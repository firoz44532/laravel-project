<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Review;
use App\Models\Cart;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', '7days');
        $dateRange = $this->getDateRange($period);
        
        // Dashboard Statistics
        $stats = [
            'total_orders' => Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count(),
            'total_revenue' => Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->sum('total_amount'),
            'total_customers' => User::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count(),
            'total_products' => Product::where('is_active', true)->count(),
            'average_order_value' => Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->avg('total_amount'),
            'conversion_rate' => $this->calculateConversionRate($dateRange),
        ];

        // Sales Chart Data
        $salesData = $this->getSalesData($dateRange);
        
        // Top Products
        $topProducts = Product::with(['primaryImage', 'category'])
            ->withCount(['orderItems' => function($query) use ($dateRange) {
                $query->whereHas('order', function($q) use ($dateRange) {
                    $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                });
            }])
            ->orderBy('order_items_count', 'desc')
            ->limit(10)
            ->get();

        // Top Categories
        $topCategories = Category::withCount(['products' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('products_count', 'desc')
            ->limit(10)
            ->get();

        // Recent Orders
        $recentOrders = Order::with(['user', 'items.product'])
            ->latest()
            ->limit(10)
            ->get();

        // Order Status Distribution
        $orderStatusData = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Payment Method Distribution
        $paymentMethodData = Order::join('payments', 'orders.id', '=', 'payments.order_id')
            ->select('payments.method', DB::raw('count(*) as count'))
            ->groupBy('payments.method')
            ->get();

        // Customer Growth
        $customerGrowth = $this->getCustomerGrowth($dateRange);

        return view('admin.analytics.index', compact(
            'stats',
            'salesData',
            'topProducts',
            'topCategories',
            'recentOrders',
            'orderStatusData',
            'paymentMethodData',
            'customerGrowth',
            'period'
        ));
    }

    public function salesReport(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);
        
        $salesData = Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('AVG(total_amount) as avg_order_value')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($salesData);
    }

    public function productReport(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);
        
        $productData = Product::with(['category', 'brand'])
            ->withCount(['orderItems' => function($query) use ($dateRange) {
                $query->whereHas('order', function($q) use ($dateRange) {
                    $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                });
            }])
            ->withSum(['orderItems' => function($query) use ($dateRange) {
                $query->whereHas('order', function($q) use ($dateRange) {
                    $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                });
            }], 'quantity')
            ->orderBy('order_items_count', 'desc')
            ->get();

        return response()->json($productData);
    }

    public function customerReport(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);
        
        $customerData = User::withCount(['orders' => function($query) use ($dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }])
            ->withSum(['orders' => function($query) use ($dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }], 'total_amount')
            ->orderBy('orders_count', 'desc')
            ->get();

        return response()->json($customerData);
    }

    public function exportReport(Request $request)
    {
        $type = $request->get('type', 'sales');
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);

        switch ($type) {
            case 'sales':
                return $this->exportSalesReport($dateRange);
            case 'products':
                return $this->exportProductsReport($dateRange);
            case 'customers':
                return $this->exportCustomersReport($dateRange);
            case 'orders':
                return $this->exportOrdersReport($dateRange);
            default:
                return response()->json(['error' => 'Invalid report type'], 400);
        }
    }

    private function getDateRange($period)
    {
        $now = Carbon::now();
        
        switch ($period) {
            case '7days':
                return [
                    'start' => $now->copy()->subDays(7),
                    'end' => $now
                ];
            case '30days':
                return [
                    'start' => $now->copy()->subDays(30),
                    'end' => $now
                ];
            case '90days':
                return [
                    'start' => $now->copy()->subDays(90),
                    'end' => $now
                ];
            case '1year':
                return [
                    'start' => $now->copy()->subYear(),
                    'end' => $now
                ];
            default:
                return [
                    'start' => $now->copy()->subDays(7),
                    'end' => $now
                ];
        }
    }

    private function getSalesData($dateRange)
    {
        return Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function calculateConversionRate($dateRange)
    {
        $totalVisits = 1000; // This would come from analytics tracking
        $totalOrders = Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        
        return $totalVisits > 0 ? ($totalOrders / $totalVisits) * 100 : 0;
    }

    private function getCustomerGrowth($dateRange)
    {
        return User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function exportSalesReport($dateRange)
    {
        $sales = Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->with(['user', 'items.product'])
            ->get();

        $csvContent = "Order Number,Customer Name,Email,Status,Total Amount,Created At\n";
        
        foreach ($sales as $order) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $order->order_number,
                $order->user->name,
                $order->user->email,
                $order->status,
                $order->total_amount,
                $order->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="sales_report.csv"');
    }

    private function exportProductsReport($dateRange)
    {
        $products = Product::with(['category', 'brand'])
            ->withCount(['orderItems' => function($query) use ($dateRange) {
                $query->whereHas('order', function($q) use ($dateRange) {
                    $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                });
            }])
            ->withSum(['orderItems' => function($query) use ($dateRange) {
                $query->whereHas('order', function($q) use ($dateRange) {
                    $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
                });
            }], 'quantity')
            ->get();

        $csvContent = "Product Name,Category,Brand,Price,Units Sold,Revenue\n";
        
        foreach ($products as $product) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $product->name,
                $product->category->name ?? 'N/A',
                $product->brand->name ?? 'N/A',
                $product->price,
                $product->order_items_count,
                $product->order_items_sum_quantity * $product->price
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="products_report.csv"');
    }

    private function exportCustomersReport($dateRange)
    {
        $customers = User::withCount(['orders' => function($query) use ($dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }])
            ->withSum(['orders' => function($query) use ($dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }], 'total_amount')
            ->get();

        $csvContent = "Customer Name,Email,Phone,Orders Count,Total Spent,Joined At\n";
        
        foreach ($customers as $customer) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $customer->name,
                $customer->email,
                $customer->phone ?? 'N/A',
                $customer->orders_count,
                $customer->orders_sum_total_amount ?? 0,
                $customer->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="customers_report.csv"');
    }

    private function exportOrdersReport($dateRange)
    {
        $orders = Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->with(['user', 'items.product', 'shippingAddress'])
            ->get();

        $csvContent = "Order Number,Customer,Email,Phone,Status,Total Amount,Shipping Address,Created At\n";
        
        foreach ($orders as $order) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s\n",
                $order->order_number,
                $order->user->name,
                $order->user->email,
                $order->shippingAddress->phone ?? 'N/A',
                $order->status,
                $order->total_amount,
                $order->shippingAddress->address_line_1 ?? 'N/A',
                $order->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="orders_report.csv"');
    }
}
