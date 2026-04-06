<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $orderNumber = $request->get('order_number');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $customerName = $request->get('customer_name');

        if (!$orderNumber && !$phone) {
            return view('frontend.tracking.index');
        }

        // Track by order number (existing method)
        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)
                ->with(['items.product', 'shippingAddress', 'billingAddress', 'payment'])
                ->first();

            if (!$order) {
                return back()->with('error', 'Order not found. Please check your order number.');
            }

            // Verify customer information for security
            if (Auth::check()) {
                // If user is logged in, check if order belongs to them
                if ($order->user_id !== Auth::id()) {
                    return back()->with('error', 'You can only track your own orders.');
                }
            } else {
                // For guest users, verify email or phone
                if ($email && $order->user->email !== $email) {
                    return back()->with('error', 'Email does not match our records.');
                }
                if ($phone && $order->shippingAddress->phone !== $phone) {
                    return back()->with('error', 'Phone number does not match our records.');
                }
            }

            return view('frontend.tracking.show', compact('order'));
        }

        // Track by name and mobile number (new method)
        if ($phone && $customerName) {
            $orders = Order::whereHas('shippingAddress', function($query) use ($phone, $customerName) {
                    $query->where('phone', $phone);
                })
                ->with(['items.product', 'shippingAddress', 'billingAddress', 'payment'])
                ->get();

            if ($orders->isEmpty()) {
                return back()->with('error', 'No orders found with this mobile number.');
            }

            // Filter orders by customer name
            $filteredOrders = $orders->filter(function($order) use ($customerName) {
                $fullName = strtolower($order->shippingAddress->first_name . ' ' . $order->shippingAddress->last_name);
                $searchName = strtolower($customerName);
                return strpos($fullName, $searchName) !== false;
            });

            if ($filteredOrders->isEmpty()) {
                return back()->with('error', 'No orders found with this name and mobile number combination.');
            }

            return view('frontend.tracking.show', ['orders' => $filteredOrders, 'searchMethod' => 'name_phone']);
        }

        return back()->with('error', 'Please provide either order number or name with mobile number.');
    }

    public function trackByOrderNumber(Request $request)
    {
        $request->validate([
            'order_number' => 'required_without:customer_name,phone|string',
            'customer_name' => 'required_without:order_number|string',
            'phone' => 'required_without:order_number|string|max:20',
            'email' => 'nullable|email',
        ]);

        // Track by order number (existing method)
        if ($request->order_number) {
            $order = Order::where('order_number', $request->order_number)
                ->with(['items.product', 'shippingAddress', 'billingAddress', 'payment'])
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found. Please check your order number.'
                ]);
            }

            // Verify customer information for security
            if (Auth::check()) {
                if ($order->user_id !== Auth::id()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You can only track your own orders.'
                    ]);
                }
            } else {
                if ($request->email && $order->user->email !== $request->email) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email does not match our records.'
                    ]);
                }
                if ($request->phone && $order->shippingAddress->phone !== $request->phone) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Phone number does not match our records.'
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'order' => [
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'status_color' => $order->status_color,
                    'created_at' => $order->created_at->format('M j, Y H:i'),
                    'total_amount' => $order->formatted_total,
                    'items_count' => $order->items->count(),
                    'estimated_delivery' => $this->getEstimatedDelivery($order),
                    'tracking_history' => $this->getTrackingHistory($order),
                ]
            ]);
        }

        // Track by name and mobile number (new method)
        if ($request->phone && $request->customer_name) {
            $orders = Order::whereHas('shippingAddress', function($query) use ($request) {
                    $query->where('phone', $request->phone);
                })
                ->with(['items.product', 'shippingAddress', 'billingAddress', 'payment'])
                ->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No orders found with this mobile number.'
                ]);
            }

            // Filter orders by customer name
            $filteredOrders = $orders->filter(function($order) use ($request) {
                $fullName = strtolower($order->shippingAddress->first_name . ' ' . $order->shippingAddress->last_name);
                $searchName = strtolower($request->customer_name);
                return strpos($fullName, $searchName) !== false;
            });

            if ($filteredOrders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No orders found with this name and mobile number combination.'
                ]);
            }

            $ordersData = $filteredOrders->map(function($order) {
                return [
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'status_color' => $order->status_color,
                    'created_at' => $order->created_at->format('M j, Y H:i'),
                    'total_amount' => $order->formatted_total,
                    'items_count' => $order->items->count(),
                    'estimated_delivery' => $this->getEstimatedDelivery($order),
                    'tracking_history' => $this->getTrackingHistory($order),
                ];
            });

            return response()->json([
                'success' => true,
                'orders' => $ordersData,
                'search_method' => 'name_phone'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Please provide either order number or name with mobile number.'
        ]);
    }

    public function apiTrack($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product', 'shippingAddress', 'billingAddress', 'payment'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_color' => $order->status_color,
                'created_at' => $order->created_at->format('M j, Y H:i'),
                'total_amount' => $order->formatted_total,
                'items_count' => $order->items->count(),
                'estimated_delivery' => $this->getEstimatedDelivery($order),
                'tracking_history' => $this->getTrackingHistory($order),
                'shipping_address' => [
                    'name' => $order->shippingAddress->first_name . ' ' . $order->shippingAddress->last_name,
                    'address' => $order->shippingAddress->address_line_1,
                    'city' => $order->shippingAddress->city,
                    'division' => $order->shippingAddress->division,
                    'phone' => $order->shippingAddress->phone,
                ],
                'items' => $order->items->map(function($item) {
                    return [
                        'name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'price' => $item->formatted_price,
                        'image' => $item->product->primaryImage ? asset('storage/' . $item->product->primaryImage->image_path) : null,
                    ];
                }),
            ]
        ]);
    }

    private function getEstimatedDelivery($order)
    {
        // Calculate estimated delivery based on order status and creation date
        $createdAt = $order->created_at;
        
        switch ($order->status) {
            case 'pending':
                return 'Processing order...';
            case 'paid':
                return 'Preparing for shipment (1-2 days)';
            case 'processing':
                return 'Shipped within 24 hours';
            case 'shipped':
                $deliveryDate = $createdAt->addDays(3);
                return 'Delivered by ' . $deliveryDate->format('M j, Y');
            case 'delivered':
                return 'Delivered on ' . $order->updated_at->format('M j, Y');
            case 'cancelled':
                return 'Order cancelled';
            case 'refunded':
                return 'Order refunded';
            default:
                return 'Processing order...';
        }
    }

    private function getTrackingHistory($order)
    {
        $history = [];
        
        // Add order creation
        $history[] = [
            'date' => $order->created_at->format('M j, Y H:i'),
            'status' => 'Order Placed',
            'description' => 'Your order has been placed successfully.',
            'icon' => 'fas fa-shopping-cart',
            'color' => 'blue'
        ];

        // Add payment confirmation
        if ($order->status !== 'pending') {
            $history[] = [
                'date' => $order->created_at->addHours(1)->format('M j, Y H:i'),
                'status' => 'Payment Confirmed',
                'description' => 'Payment has been confirmed successfully.',
                'icon' => 'fas fa-credit-card',
                'color' => 'green'
            ];
        }

        // Add processing status
        if (in_array($order->status, ['processing', 'shipped', 'delivered'])) {
            $history[] = [
                'date' => $order->created_at->addHours(2)->format('M j, Y H:i'),
                'status' => 'Order Processing',
                'description' => 'Your order is being prepared for shipment.',
                'icon' => 'fas fa-box',
                'color' => 'yellow'
            ];
        }

        // Add shipped status
        if (in_array($order->status, ['shipped', 'delivered'])) {
            $history[] = [
                'date' => $order->created_at->addDays(1)->format('M j, Y H:i'),
                'status' => 'Order Shipped',
                'description' => 'Your order has been shipped and is on its way.',
                'icon' => 'fas fa-truck',
                'color' => 'purple'
            ];
        }

        // Add delivered status
        if ($order->status === 'delivered') {
            $history[] = [
                'date' => $order->updated_at->format('M j, Y H:i'),
                'status' => 'Order Delivered',
                'description' => 'Your order has been delivered successfully.',
                'icon' => 'fas fa-check-circle',
                'color' => 'green'
            ];
        }

        // Add cancelled status
        if ($order->status === 'cancelled') {
            $history[] = [
                'date' => $order->updated_at->format('M j, Y H:i'),
                'status' => 'Order Cancelled',
                'description' => 'Your order has been cancelled.',
                'icon' => 'fas fa-times-circle',
                'color' => 'red'
            ];
        }

        return $history;
    }
}
