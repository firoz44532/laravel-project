<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MerchantController extends Controller
{
    public function dashboard()
    {
        $merchant = Auth::user()->merchant;
        
        if (!$merchant || !$merchant->isApproved()) {
            return redirect()->route('merchant.profile')->with('error', 'Your merchant account is not approved yet.');
        }

        // Enhanced stats
        $stats = [
            'total_products' => $merchant->products()->count(),
            'active_products' => $merchant->activeProducts()->count(),
            'total_orders' => $merchant->orders()->count(),
            'pending_orders' => $merchant->orders()->whereIn('status', ['pending', 'processing'])->count(),
            'completed_orders' => $merchant->orders()->where('status', 'completed')->count(),
            'total_revenue' => $merchant->total_revenue,
            'total_earnings' => $merchant->total_earnings,
            'pending_approval_products' => $merchant->products()->where('is_active', false)->count(),
            'low_stock_products' => $merchant->products()->where('stock_quantity', '<=', 5)->where('track_stock', true)->count(),
        ];

        // Recent orders with more details
        $recentOrders = $merchant->orders()
            ->with(['items.product', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Recent products
        $recentProducts = $merchant->products()
            ->latest()
            ->take(5)
            ->get();

        // Top selling products
        $topProducts = Product::where('merchant_id', $merchant->id)
            ->withCount(['orderItems' => function($query) {
                $query->whereHas('order', function($q) {
                    $q->where('status', 'completed');
                });
            }])
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();

        // Monthly revenue for the last 6 months
        $monthlyRevenue = $merchant->orders()
            ->where('status', 'completed')
            ->selectRaw('strftime("%Y-%m", created_at) as month, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Order status breakdown
        $orderStatusBreakdown = $merchant->orders()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return view('merchant.dashboard', compact(
            'merchant', 
            'stats', 
            'recentOrders', 
            'recentProducts', 
            'topProducts',
            'monthlyRevenue',
            'orderStatusBreakdown'
        ));
    }

    public function register()
    {
        $merchant = Auth::user()->merchant;
        
        if ($merchant) {
            return redirect()->route('merchant.profile')->with('info', 'You already have a merchant account.');
        }

        return view('merchant.register');
    }

    public function profile()
    {
        $merchant = Auth::user()->merchant;
        
        if (!$merchant) {
            return redirect()->route('merchant.register');
        }

        return view('merchant.profile', compact('merchant'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string',
            'store_email' => 'nullable|email|max:255',
            'store_phone' => 'nullable|string|max:20',
            'store_address' => 'nullable|string|max:500',
            'store_city' => 'nullable|string|max:100',
            'store_country' => 'nullable|string|max:100',
            'payment_method' => 'nullable|string',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'routing_number' => 'nullable|string|max:50',
        ]);

        // Create payment details JSON
        $paymentDetails = null;
        if ($request->filled('payment_method')) {
            $paymentDetails = [
                'method' => $request->payment_method,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'bank_name' => $request->bank_name,
                'branch_name' => $request->branch_name,
                'routing_number' => $request->routing_number,
            ];
        }

        $merchant = Merchant::create([
            'user_id' => Auth::id(),
            'store_name' => $validated['store_name'],
            'store_slug' => Str::slug($validated['store_name']) . '-' . time(),
            'store_description' => $validated['store_description'],
            'store_email' => $validated['store_email'],
            'store_phone' => $validated['store_phone'],
            'store_address' => $validated['store_address'],
            'store_city' => $validated['store_city'],
            'store_country' => $validated['store_country'],
            'payment_details' => $paymentDetails,
            'status' => 'pending',
        ]);

        // Update user role
        Auth::user()->update(['role' => 'merchant']);

        return redirect()->route('merchant.profile')->with('success', 'Your merchant application has been submitted successfully! We will review it within 24-48 hours.');
    }

    public function update(Request $request)
    {
        $merchant = Auth::user()->merchant;
        
        if (!$merchant) {
            return redirect()->route('merchant.profile')->with('error', 'Merchant profile not found.');
        }

        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string',
            'store_email' => 'nullable|email|max:255',
            'store_phone' => 'nullable|string|max:20',
            'store_address' => 'nullable|string|max:500',
            'store_city' => 'nullable|string|max:100',
            'store_country' => 'nullable|string|max:100',
            'payment_method' => 'nullable|string',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'routing_number' => 'nullable|string|max:50',
        ]);

        // Create payment details JSON
        $paymentDetails = null;
        if ($request->filled('payment_method')) {
            $paymentDetails = [
                'method' => $request->payment_method,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'bank_name' => $request->bank_name,
                'branch_name' => $request->branch_name,
                'routing_number' => $request->routing_number,
            ];
        }

        $merchant->update([
            'store_name' => $validated['store_name'],
            'store_description' => $validated['store_description'],
            'store_email' => $validated['store_email'],
            'store_phone' => $validated['store_phone'],
            'store_address' => $validated['store_address'],
            'store_city' => $validated['store_city'],
            'store_country' => $validated['store_country'],
            'payment_details' => $paymentDetails,
        ]);

        return redirect()->route('merchant.profile')->with('success', 'Your merchant profile has been updated successfully!');
    }

    public function products()
    {
        $merchant = Auth::user()->merchant;
        $products = $merchant->products()->with(['category', 'primaryImage'])->latest()->paginate(10);
        
        return view('merchant.products.index', compact('merchant', 'products'));
    }

    public function createProduct()
    {
        $merchant = Auth::user()->merchant;
        if (!$merchant->isApproved()) {
            return back()->with('error', 'Your merchant account must be approved to add products.');
        }

        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        $brands = \App\Models\Brand::where('is_active', true)->orderBy('name')->get();
        
        return view('merchant.products.create', compact('categories', 'brands'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'required|string|max:100|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'track_stock' => 'boolean',
            'weight' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'is_active' => 'boolean',
        ]);

        $merchant = Auth::user()->merchant;
        
        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . uniqid(),
            'description' => $request->description,
            'short_description' => $request->short_description,
            'sku' => $request->sku,
            'price' => $request->price,
            'compare_price' => $request->compare_price,
            'cost_price' => $request->cost_price,
            'stock_quantity' => $request->stock_quantity,
            'track_stock' => $request->track_stock ?? false,
            'weight' => $request->weight,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'merchant_id' => $merchant->id,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('merchant.products.index')->with('success', 'Product created successfully.');
    }

    public function orders()
    {
        $merchant = Auth::user()->merchant;
        $orders = $merchant->orders()
            ->with(['user', 'items.product'])
            ->latest()
            ->paginate(10);
        
        return view('merchant.orders.index', compact('merchant', 'orders'));
    }

    public function earnings()
    {
        $merchant = Auth::user()->merchant;
        
        $monthlyEarnings = $merchant->earnings()
            ->selectRaw('strftime("%Y-%m", created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        $recentOrders = $merchant->earnings()
            ->with(['user'])
            ->latest()
            ->take(10)
            ->get();

        return view('merchant.earnings', compact('merchant', 'monthlyEarnings', 'recentOrders'));
    }
}
