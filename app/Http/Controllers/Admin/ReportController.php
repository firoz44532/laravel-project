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

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function salesReport(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);
        
        $salesData = Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('status', '!=', 'cancelled')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('AVG(total_amount) as avg_order_value'),
                DB::raw('SUM(shipping_amount) as shipping'),
                DB::raw('SUM(tax_amount) as tax')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $summary = Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('status', '!=', 'cancelled')
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('AVG(total_amount) as avg_order_value'),
                DB::raw('SUM(shipping_amount) as total_shipping'),
                DB::raw('SUM(tax_amount) as total_tax')
            )
            ->first();

        return response()->json([
            'sales_data' => $salesData,
            'summary' => $summary,
            'period' => $period
        ]);
    }

    public function productReport(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);
        
        $productData = Product::with(['category', 'brand'])
            ->withCount(['orderItems' => function($query) use ($dateRange) {
                $query->whereHas('order', function($q) use ($dateRange) {
                    $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                      ->where('status', '!=', 'cancelled');
                });
            }])
            ->withSum(['orderItems' => function($query) use ($dateRange) {
                $query->whereHas('order', function($q) use ($dateRange) {
                    $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                      ->where('status', '!=', 'cancelled');
                });
            }], 'quantity')
            ->where('is_active', true)
            ->orderBy('order_items_count', 'desc')
            ->limit(50)
            ->get();

        $categoryData = Category::withCount(['products' => function($query) use ($dateRange) {
                $query->where('is_active', true)
                  ->whereHas('orderItems', function($q) use ($dateRange) {
                      $q->whereHas('order', function($o) use ($dateRange) {
                          $o->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                            ->where('status', '!=', 'cancelled');
                      });
                  });
            }])
            ->withSum(['products' => function($query) use ($dateRange) {
                $query->where('is_active', true)
                  ->whereHas('orderItems', function($q) use ($dateRange) {
                      $q->whereHas('order', function($o) use ($dateRange) {
                          $o->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                            ->where('status', '!=', 'cancelled');
                      });
                  });
            }], 'order_items_count')
            ->orderBy('products_sum_order_items_count', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'products' => $productData,
            'categories' => $categoryData,
            'period' => $period
        ]);
    }

    public function customerReport(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);
        
        $customerData = User::withCount(['orders' => function($query) use ($dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                  ->where('status', '!=', 'cancelled');
            }])
            ->withSum(['orders' => function($query) use ($dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                  ->where('status', '!=', 'cancelled');
            }], 'total_amount')
            ->orderBy('orders_count', 'desc')
            ->limit(50)
            ->get();

        $newCustomers = User::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $returningCustomers = User::whereHas('orders', function($query) use ($dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                  ->where('status', '!=', 'cancelled');
            })
            ->count();

        $customerGrowth = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'customers' => $customerData,
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers,
            'customer_growth' => $customerGrowth,
            'period' => $period
        ]);
    }

    public function inventoryReport(Request $request)
    {
        $products = Product::with(['category', 'brand'])
            ->where('is_active', true)
            ->get();

        $lowStock = $products->filter(function($product) {
            return $product->stock_quantity <= 10 && $product->stock_quantity > 0;
        });

        $outOfStock = $products->filter(function($product) {
            return $product->stock_quantity <= 0;
        });

        $overStock = $products->filter(function($product) {
            return $product->stock_quantity > 100;
        });

        $categoryStock = Category::with(['products' => function($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->map(function($category) {
                return [
                    'name' => $category->name,
                    'total_stock' => $category->products->sum('stock_quantity'),
                    'products_count' => $category->products->count(),
                    'low_stock_count' => $category->products->filter(function($p) {
                        return $p->stock_quantity <= 10 && $p->stock_quantity > 0;
                    })->count(),
                    'out_of_stock_count' => $category->products->filter(function($p) {
                        return $p->stock_quantity <= 0;
                    })->count()
                ];
            });

        return response()->json([
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'over_stock' => $overStock,
            'category_stock' => $categoryStock,
            'total_products' => $products->count(),
            'total_stock' => $products->sum('stock_quantity')
        ]);
    }

    public function reviewReport(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);
        
        $reviews = Review::with(['user', 'product'])
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->orderBy('created_at', 'desc')
            ->get();

        $ratingDistribution = Review::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->select('rating', DB::raw('COUNT(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();

        $averageRating = Review::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->avg('rating');

        $pendingReviews = Review::where('is_approved', false)
            ->count();

        $topRatedProducts = Product::with(['primaryImage', 'category'])
            ->withCount(['approvedReviews'])
            ->withAvg(['approvedReviews'], 'rating')
            ->whereHas('approvedReviews')
            ->orderBy('approved_reviews_avg_rating', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'reviews' => $reviews,
            'rating_distribution' => $ratingDistribution,
            'average_rating' => $averageRating,
            'pending_reviews' => $pendingReviews,
            'top_rated_products' => $topRatedProducts,
            'period' => $period
        ]);
    }

    public function financialReport(Request $request)
    {
        $period = $request->get('period', '30days');
        $dateRange = $this->getDateRange($period);
        
        $orders = Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('status', '!=', 'cancelled')
            ->get();

        $revenueByPaymentMethod = Order::join('payments', 'orders.id', '=', 'payments.order_id')
            ->whereBetween('orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->where('orders.status', '!=', 'cancelled')
            ->select('payments.method', DB::raw('COUNT(*) as count'), DB::raw('SUM(orders.total_amount) as total'))
            ->groupBy('payments.method')
            ->get();

        $revenueByStatus = Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('status')
            ->get();

        $monthlyRevenue = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return response()->json([
            'total_revenue' => $orders->sum('total_amount'),
            'total_orders' => $orders->count(),
            'avg_order_value' => $orders->avg('total_amount'),
            'total_shipping' => $orders->sum('shipping_amount'),
            'total_tax' => $orders->sum('tax_amount'),
            'revenue_by_payment_method' => $revenueByPaymentMethod,
            'revenue_by_status' => $revenueByStatus,
            'monthly_revenue' => $monthlyRevenue,
            'period' => $period
        ]);
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
            case 'inventory':
                return $this->exportInventoryReport();
            case 'reviews':
                return $this->exportReviewsReport($dateRange);
            case 'financial':
                return $this->exportFinancialReport($dateRange);
            default:
                return response()->json(['error' => 'Invalid report type'], 400);
        }
    }

    private function exportSalesReport($dateRange)
    {
        $orders = Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('status', '!=', 'cancelled')
            ->with(['user', 'items.product', 'payment'])
            ->get();

        $csvContent = "Order Number,Customer Name,Email,Status,Payment Method,Total Amount,Shipping,Tax,Created At\n";
        
        foreach ($orders as $order) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $order->order_number,
                $order->user->name,
                $order->user->email,
                $order->status,
                $order->payment->method ?? 'N/A',
                $order->total_amount,
                $order->shipping_amount,
                $order->tax_amount,
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
                    $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                      ->where('status', '!=', 'cancelled');
                });
            }])
            ->withSum(['orderItems' => function($query) use ($dateRange) {
                $query->whereHas('order', function($q) use ($dateRange) {
                    $q->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                      ->where('status', '!=', 'cancelled');
                });
            }], 'quantity')
            ->where('is_active', true)
            ->get();

        $csvContent = "Product Name,Category,Brand,Price,Units Sold,Revenue,Stock Quantity\n";
        
        foreach ($products as $product) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $product->name,
                $product->category->name ?? 'N/A',
                $product->brand->name ?? 'N/A',
                $product->price,
                $product->order_items_count,
                $product->order_items_sum_quantity * $product->price,
                $product->stock_quantity
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="products_report.csv"');
    }

    private function exportCustomersReport($dateRange)
    {
        $customers = User::withCount(['orders' => function($query) use ($dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                  ->where('status', '!=', 'cancelled');
            }])
            ->withSum(['orders' => function($query) use ($dateRange) {
                $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                  ->where('status', '!=', 'cancelled');
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

    private function exportInventoryReport()
    {
        $products = Product::with(['category', 'brand'])
            ->where('is_active', true)
            ->get();

        $csvContent = "Product Name,Category,Brand,Price,Stock Quantity,Status,SKU\n";
        
        foreach ($products as $product) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $product->name,
                $product->category->name ?? 'N/A',
                $product->brand->name ?? 'N/A',
                $product->price,
                $product->stock_quantity,
                $product->stock_status,
                $product->sku
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="inventory_report.csv"');
    }

    private function exportReviewsReport($dateRange)
    {
        $reviews = Review::with(['user', 'product'])
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get();

        $csvContent = "Product Name,Customer Name,Rating,Comment,Status,Created At\n";
        
        foreach ($reviews as $review) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $review->product->name,
                $review->user->name,
                $review->rating,
                $review->comment,
                $review->is_approved ? 'Approved' : 'Pending',
                $review->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="reviews_report.csv"');
    }

    private function exportFinancialReport($dateRange)
    {
        $orders = Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->where('status', '!=', 'cancelled')
            ->with(['user', 'payment'])
            ->get();

        $csvContent = "Order Number,Customer,Payment Method,Status,Total Amount,Shipping,Tax,Net Revenue,Created At\n";
        
        foreach ($orders as $order) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $order->order_number,
                $order->user->name,
                $order->payment->method ?? 'N/A',
                $order->status,
                $order->total_amount,
                $order->shipping_amount,
                $order->tax_amount,
                $order->total_amount - $order->shipping_amount - $order->tax_amount,
                $order->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="financial_report.csv"');
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
                    'start' => $now->copy()->subDays(30),
                    'end' => $now
                ];
        }
    }
}
