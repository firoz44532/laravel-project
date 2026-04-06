<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Coupon;
use App\Notifications\SendOrderNotification;
use App\Services\ShippingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    public function index()
    {
        $cartController = new CartController();
        $cartItems = $cartController->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $subtotal = $cartController->getCartTotal();
        
        // Calculate cart weight for shipping
        $cartWeight = $cartItems->sum(function($item) {
            return ($item->product->weight ?? 1) * $item->quantity;
        });

        // Get default address for shipping calculation (if user logged in)
        $defaultAddress = null;
        $city = null;
        $area = null;
        if (Auth::check()) {
            $defaultAddress = Auth::user()->getDefaultAddress();
            $city = $defaultAddress->city ?? null;
            $area = $defaultAddress->area ?? null;
        }

        // Calculate shipping using ShippingService
        $shippingData = $this->shippingService->calculateShipping($subtotal, $cartWeight, $city, $area);
        $shipping = $shippingData['cost'];

        // Calculate tax using ShippingService
        $tax = $this->shippingService->calculateOrderTax($subtotal, $shipping);
        $total = $subtotal + $shipping + $tax;

        // Get available shipping methods
        $availableShippingMethods = $this->shippingService->getAvailableShippingMethods($city, $area, $subtotal);

        $addresses = Auth::check() ? Auth::user()->addresses : collect();

        return view('frontend.checkout.index', compact(
            'cartItems', 
            'subtotal', 
            'shipping', 
            'tax', 
            'total',
            'addresses',
            'defaultAddress',
            'availableShippingMethods',
            'shippingData'
        ));
    }

    public function validateCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $request->coupon_code)
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid coupon code'
            ]);
        }

        // Check if coupon is expired
        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return response()->json([
                'valid' => false,
                'message' => 'Coupon has expired'
            ]);
        }

        // Check if coupon has started
        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return response()->json([
                'valid' => false,
                'message' => 'Coupon is not yet active'
            ]);
        }

        // Check minimum amount
        if ($coupon->minimum_amount && $request->total_amount < $coupon->minimum_amount) {
            return response()->json([
                'valid' => false,
                'message' => "Minimum order amount is ৳{$coupon->minimum_amount}"
            ]);
        }

        // Check usage limit
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json([
                'valid' => false,
                'message' => 'Coupon usage limit reached'
            ]);
        }

        // Calculate discount
        $discount = 0;
        if ($coupon->type === 'fixed') {
            $discount = min($coupon->value, $request->total_amount);
        } else {
            $discount = ($request->total_amount * $coupon->value) / 100;
        }

        return response()->json([
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'message' => 'Coupon applied successfully!'
        ]);
    }

    public function store(Request $request)
    {
        // Support guest checkout: accept either an existing address id or inline address fields
        $rules = [
            'payment_method' => 'required|in:bkash,nagad,rocket,upay,card,bank_transfer,cash_on_delivery',
            'payment_mobile' => 'required_if:payment_method,bkash,nagad,rocket,upay|string|max:20',
            'account_number' => 'nullable|string|max:50',
            'card_last_four' => 'required_if:payment_method,card|string|max:4',
            'coupon_code' => 'nullable|string|max:50',
            // Bank transfer specific
            'account_holder_name' => 'required_if:payment_method,bank_transfer|string|max:255',
            'bank_name' => 'required_if:payment_method,bank_transfer|string|max:255',
            'bank_account_number' => 'required_if:payment_method,bank_transfer|string|max:50',
            'branch_name' => 'required_if:payment_method,bank_transfer|string|max:255',
        ];

        $isAuth = Auth::check();

        // Shipping: authenticated users may select an existing address; guests must provide inline fields
        if ($isAuth && $request->filled('shipping_address_id')) {
            $rules['shipping_address_id'] = 'exists:addresses,id';
        } else {
            $rules = array_merge($rules, [
                'shipping_first_name' => 'required|string|max:255',
                'shipping_last_name' => 'required|string|max:255',
                'shipping_phone' => 'required|string|max:20',
                'shipping_email' => 'nullable|email|max:255',
                'shipping_address_line_1' => 'required|string|max:255',
                'shipping_address_line_2' => 'nullable|string|max:255',
                'shipping_city' => 'required|string|max:100',
                'shipping_postal_code' => 'nullable|string|max:20',
                'shipping_division' => 'required|string|max:100',
                'shipping_country' => 'required|string|max:100',
            ]);
        }

        // Billing: optional, otherwise billing = shipping
        if ($isAuth && $request->filled('billing_address_id')) {
            $rules['billing_address_id'] = 'exists:addresses,id';
        } else {
            if ($request->filled('billing_first_name') || $request->filled('billing_address_line_1')) {
                $rules = array_merge($rules, [
                    'billing_first_name' => 'required|string|max:255',
                    'billing_last_name' => 'required|string|max:255',
                    'billing_phone' => 'required|string|max:20',
                    'billing_email' => 'nullable|email|max:255',
                    'billing_address_line_1' => 'required|string|max:255',
                    'billing_address_line_2' => 'nullable|string|max:255',
                    'billing_city' => 'required|string|max:100',
                    'billing_postal_code' => 'nullable|string|max:20',
                    'billing_division' => 'required|string|max:100',
                    'billing_country' => 'required|string|max:100',
                ]);
            }
        }

        $request->validate($rules);

        $cartController = new CartController();
        $cartItems = $cartController->getCartItems();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty'
            ], 400);
        }

        // Log checkout attempt (mask sensitive fields)
        try {
            $maskedBank = $request->input('bank_account_number') ? str_repeat('*', max(0, strlen($request->input('bank_account_number')) - 4)) . substr($request->input('bank_account_number'), -4) : null;
            $maskedAccount = $request->input('account_number') ? str_repeat('*', max(0, strlen($request->input('account_number')) - 4)) . substr($request->input('account_number'), -4) : null;

            Log::info('Checkout attempt', [
                'user_id' => Auth::id(),
                'payment_method' => $request->input('payment_method'),
                'payment_mobile_present' => $request->filled('payment_mobile'),
                'account_number_masked' => $maskedAccount,
                'bank_account_masked' => $maskedBank,
                'shipping_address_id' => $request->input('shipping_address_id'),
                'billing_address_id' => $request->input('billing_address_id'),
                'ip' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            // Don't block checkout for logging failures
            Log::warning('Failed to log checkout attempt: ' . $e->getMessage());
        }

        try {
            DB::beginTransaction();

            // Check stock availability
            foreach ($cartItems as $item) {
                $product = Product::find($item->id);
                if ($product->track_stock && $product->stock_quantity < $item->quantity) {
                    throw new \Exception("Not enough stock for {$product->name}");
                }
            }

            // Create order
            $subtotal = $cartController->getCartTotal();
            
            // Calculate cart weight for shipping
            $cartWeight = $cartItems->sum(function($item) {
                return ($item->product->weight ?? 1) * $item->quantity;
            });

            // Determine shipping address (existing or create from inline fields)
            if ($isAuth && $request->filled('shipping_address_id')) {
                $shippingAddress = Address::find($request->shipping_address_id);
                // Ownership check
                if (!$shippingAddress || $shippingAddress->user_id !== Auth::id()) {
                    throw new \Exception('Invalid shipping address');
                }
            } else {
                $shippingAddress = Address::create([
                    'type' => 'shipping',
                    'first_name' => $request->input('shipping_first_name'),
                    'last_name' => $request->input('shipping_last_name'),
                    'phone' => $request->input('shipping_phone'),
                    'email' => $request->input('shipping_email'),
                    'address_line_1' => $request->input('shipping_address_line_1'),
                    'address_line_2' => $request->input('shipping_address_line_2'),
                    'city' => $request->input('shipping_city'),
                    'postal_code' => $request->input('shipping_postal_code'),
                    'division' => $request->input('shipping_division'),
                    'country' => $request->input('shipping_country'),
                    'user_id' => Auth::check() ? Auth::id() : null,
                ]);
            }
            // If user requested to save to account
            if ($request->boolean('save_to_account')) {
                if (Auth::check()) {
                    $shippingAddress->user_id = Auth::id();
                    $shippingAddress->save();
                } else {
                    // remember to attach after login
                    session(['pending_save_address_id' => $shippingAddress->id]);
                }
            }
            $city = $shippingAddress->city ?? null;
            $area = $shippingAddress->area ?? null;

            // Calculate shipping using ShippingService
            $shippingData = $this->shippingService->calculateShipping($subtotal, $cartWeight, $city, $area);
            $shipping = $shippingData['cost'];

            // Calculate tax using ShippingService
            $tax = $this->shippingService->calculateOrderTax($subtotal, $shipping);
            $total = $subtotal + $shipping + $tax;
            
            // Apply coupon if provided
            $discount = 0;
            $coupon = null;
            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)
                    ->where('is_active', true)
                    ->first();
                
                if ($coupon) {
                    // Validate coupon
                    if ($coupon->expires_at && $coupon->expires_at->isPast()) {
                        throw new \Exception('Coupon has expired');
                    }
                    
                    if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
                        throw new \Exception('Coupon is not yet active');
                    }
                    
                    if ($coupon->minimum_amount && $subtotal < $coupon->minimum_amount) {
                        throw new \Exception("Minimum order amount is ৳{$coupon->minimum_amount}");
                    }
                    
                    if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                        throw new \Exception('Coupon usage limit reached');
                    }
                    
                    // Calculate discount
                    if ($coupon->type === 'fixed') {
                        $discount = min($coupon->value, $total);
                    } else {
                        $discount = ($total * $coupon->value) / 100;
                    }
                    
                    // Update coupon usage
                    $coupon->increment('used_count');
                    
                    // Update total
                    $total -= $discount;
                } else {
                    throw new \Exception('Invalid coupon code');
                }
            }

            // Determine billing address (existing, inline, or same as shipping)
            if ($isAuth && $request->filled('billing_address_id')) {
                $billingAddress = Address::find($request->billing_address_id);
                if (!$billingAddress || $billingAddress->user_id !== Auth::id()) {
                    throw new \Exception('Invalid billing address');
                }
            } else {
                if ($request->filled('billing_first_name')) {
                    $billingAddress = Address::create([
                        'type' => 'billing',
                        'first_name' => $request->input('billing_first_name'),
                        'last_name' => $request->input('billing_last_name'),
                        'phone' => $request->input('billing_phone'),
                        'email' => $request->input('billing_email'),
                        'address_line_1' => $request->input('billing_address_line_1'),
                        'address_line_2' => $request->input('billing_address_line_2'),
                        'city' => $request->input('billing_city'),
                        'postal_code' => $request->input('billing_postal_code'),
                        'division' => $request->input('billing_division'),
                        'country' => $request->input('billing_country'),
                        'user_id' => Auth::check() ? Auth::id() : null,
                    ]);
                } else {
                    $billingAddress = $shippingAddress;
                }
            }

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'user_id' => Auth::id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'shipping_amount' => $shipping,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'currency' => 'BDT',
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $billingAddress->id,
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->id,
                    'product_name' => $item->name,
                    'product_sku' => $item->sku,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->price * $item->quantity,
                ]);

                // Update stock
                $product = Product::find($item->id);
                if ($product->track_stock) {
                    $product->decrement('stock_quantity', $item->quantity);
                }
            }

            // Create payment record
            $paymentData = [
                'transaction_id' => 'PAY-' . strtoupper(Str::random(10)),
                'order_id' => $order->id,
                'method' => $request->payment_method,
                'status' => $request->payment_method === 'cash_on_delivery' ? 'pending' : 'processing',
                'amount' => $total,
                'currency' => 'BDT',
            ];

            // Add payment details based on payment method
            if ($request->payment_method === 'bkash' || $request->payment_method === 'nagad' || $request->payment_method === 'rocket') {
                $paymentData['phone_number'] = $request->payment_mobile;
                $paymentData['account_number'] = $request->account_number;
                $paymentData['transaction_id'] = $request->transaction_id ?: $paymentData['transaction_id'];
            } elseif ($request->payment_method === 'bank_transfer') {
                $paymentData['account_holder_name'] = $request->account_holder_name;
                $paymentData['bank_name'] = $request->bank_name;
                $paymentData['account_number'] = $request->bank_account_number;
                $paymentData['branch_name'] = $request->branch_name;
            }

            $payment = Payment::create($paymentData);

            // Clear cart
            if (Auth::check()) {
                Cart::where('user_id', Auth::id())->delete();
            }

            // Send order confirmation email notification
            try {
                $order->user->notify(new SendOrderNotification($order));
            } catch (\Exception $e) {
                \Log::error('Failed to send order confirmation email: ' . $e->getMessage());
                // Continue with order process even if email fails
            }

            DB::commit();

            // Redirect based on payment method
            if ($request->payment_method === 'cash_on_delivery') {
                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'redirect_url' => route('checkout.success', ['order_id' => $order->id])
                ]);
            } else {
                // Redirect to payment gateway
                return response()->json([
                    'success' => true,
                    'redirect_url' => $this->getPaymentGatewayUrl($request->payment_method, $payment)
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'ip' => request()->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function success(Request $request)
    {
        $orderId = $request->get('order_id');
        
        if (!$orderId) {
            return redirect()->route('home')->with('error', 'Invalid order');
        }

        $query = Order::with(['items.product', 'shippingAddress', 'billingAddress', 'payment'])
            ->where('id', $orderId);

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->whereNull('user_id');
        }

        $order = $query->firstOrFail();

        return view('frontend.checkout.success', compact('order'));
    }

    public function failed(Request $request)
    {
        $orderId = $request->get('order_id');
        
        if (!$orderId) {
            return redirect()->route('home')->with('error', 'Invalid order');
        }

        $query = Order::where('id', $orderId);
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->whereNull('user_id');
        }

        $order = $query->firstOrFail();

        return view('frontend.checkout.failed', compact('order'));
    }

    private function getPaymentGatewayUrl($method, $payment)
    {
        // In non-production, route all gateways through the local sandbox page
        if (!app()->environment('production')) {
            return route('test-payment.show', $payment->transaction_id);
        }

        // Production payment gateway URLs
        $gatewayUrls = [
            'bkash' => "https://checkout.bkash.com/payment/{$payment->transaction_id}",
            'nagad' => "https://checkout.nagad.com/payment/{$payment->transaction_id}",
            'rocket' => "https://checkout.rocket.com/payment/{$payment->transaction_id}",
            'upay' => "https://checkout.upay.com/payment/{$payment->transaction_id}",
            'card' => "https://secure.payment-gateway.com/pay/{$payment->transaction_id}",
        ];

        return $gatewayUrls[$method] ?? route('checkout.failed', ['order_id' => $payment->order_id]);
    }

    // Payment callback handlers (for real payment gateway integration)
    public function paymentCallback(Request $request, $gateway)
    {
        $transactionId = $request->get('transaction_id');
        $status = $request->get('status'); // success, failed, cancelled
        
        $payment = Payment::where('transaction_id', $transactionId)->first();
        
        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        $order = $payment->order;

        if ($status === 'success') {
            $payment->update(['status' => 'completed']);
            $order->update(['status' => 'paid']);
            
            // Send payment success email notification
            try {
                if ($order->user) {
                    $order->user->notify(new SendOrderNotification($order));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send payment success email: ' . $e->getMessage());
                // Continue with order process even if email fails
            }
            
            return redirect()->route('checkout.success', ['order_id' => $order->id]);
        } else {
            $payment->update(['status' => 'failed']);
            $order->update(['status' => 'cancelled']);
            
            // Send payment failure email notification
            try {
                if ($order->user) {
                    $order->user->notify(new SendOrderNotification($order));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send payment failure email: ' . $e->getMessage());
                // Continue with order process even if email fails
            }
            
            return redirect()->route('checkout.failed', ['order_id' => $order->id]);
        }
    }

    /**
     * Calculate shipping cost based on address
     */
    public function calculateShipping(Request $request)
    {
        $request->validate([
            'shipping_address_id' => 'required|exists:addresses,id',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $cartController = new CartController();
        $cartItems = $cartController->getCartItems();
        
        // Calculate cart weight
        $cartWeight = $cartItems->sum(function($item) {
            return ($item->product->weight ?? 1) * $item->quantity;
        });

        // Get shipping address
        $shippingAddress = Address::find($request->shipping_address_id);
        $city = $shippingAddress->city ?? null;
        $area = $shippingAddress->area ?? null;

        // Calculate shipping
        $shippingData = $this->shippingService->calculateShipping($request->subtotal, $cartWeight, $city, $area);
        $tax = $this->shippingService->calculateOrderTax($request->subtotal, $shippingData['cost']);

        // Get available shipping methods
        $availableMethods = $this->shippingService->getAvailableShippingMethods($city, $area, $request->subtotal);

        return response()->json([
            'success' => true,
            'shipping' => $shippingData,
            'tax' => $tax,
            'total' => $request->subtotal + $shippingData['cost'] + $tax,
            'available_methods' => $availableMethods,
        ]);
    }
}
