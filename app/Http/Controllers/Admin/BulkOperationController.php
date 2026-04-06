<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Review;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Coupon;
use App\Models\Banner;
use App\Http\Requests\Admin\BulkProductActionRequest;
use App\Http\Requests\Admin\BulkOrderActionRequest;
use App\Http\Requests\Admin\BulkReviewActionRequest;
use App\Http\Requests\Admin\BulkCategoryActionRequest;
use App\Http\Requests\Admin\BulkStatsRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BulkOperationController extends Controller
{
    // Product Bulk Operations
    public function bulkProductAction(BulkProductActionRequest $request)
    {
        $productIds = $request->product_ids;
        $action = $request->action;

        switch ($action) {
            case 'activate':
                Product::whereIn('id', $productIds)->update(['is_active' => true]);
                return response()->json(['success' => true, 'message' => 'Products activated successfully']);

            case 'deactivate':
                Product::whereIn('id', $productIds)->update(['is_active' => false]);
                return response()->json(['success' => true, 'message' => 'Products deactivated successfully']);

            case 'delete':
                Product::whereIn('id', $productIds)->delete();
                return response()->json(['success' => true, 'message' => 'Products deleted successfully']);

            case 'update_price':
                $products = Product::whereIn('id', $productIds);
                if ($request->price_type === 'fixed') {
                    $products->update(['price' => $request->price_value]);
                } else {
                    $products->get()->each(function($product) use ($request) {
                        $product->price = $product->price * (1 + ($request->price_value / 100));
                        $product->save();
                    });
                }
                return response()->json(['success' => true, 'message' => 'Product prices updated successfully']);

            case 'update_stock':
                $products = Product::whereIn('id', $productIds);
                if ($request->stock_action === 'set') {
                    $products->update(['stock_quantity' => $request->stock_value]);
                } elseif ($request->stock_action === 'add') {
                    $products->get()->each(function($product) use ($request) {
                        $product->stock_quantity += $request->stock_value;
                        $product->save();
                    });
                } else {
                    $products->get()->each(function($product) use ($request) {
                        $product->stock_quantity = max(0, $product->stock_quantity - $request->stock_value);
                        $product->save();
                    });
                }
                return response()->json(['success' => true, 'message' => 'Product stock updated successfully']);

            case 'update_category':
                Product::whereIn('id', $productIds)->update(['category_id' => $request->category_id]);
                return response()->json(['success' => true, 'message' => 'Product categories updated successfully']);

            case 'update_brand':
                Product::whereIn('id', $productIds)->update(['brand_id' => $request->brand_id]);
                return response()->json(['success' => true, 'message' => 'Product brands updated successfully']);
        }
    }

    // Order Bulk Operations
    public function bulkOrderAction(BulkOrderActionRequest $request)
    {
        $orderIds = $request->order_ids;
        $action = $request->action;

        switch ($action) {
            case 'update_status':
                Order::whereIn('id', $orderIds)->update(['status' => $request->status]);
                return response()->json(['success' => true, 'message' => 'Order statuses updated successfully']);

            case 'mark_paid':
                Order::whereIn('id', $orderIds)->update(['status' => 'paid']);
                return response()->json(['success' => true, 'message' => 'Orders marked as paid']);

            case 'mark_shipped':
                Order::whereIn('id', $orderIds)->update(['status' => 'shipped']);
                return response()->json(['success' => true, 'message' => 'Orders marked as shipped']);

            case 'mark_delivered':
                Order::whereIn('id', $orderIds)->update(['status' => 'delivered']);
                return response()->json(['success' => true, 'message' => 'Orders marked as delivered']);

            case 'mark_cancelled':
                Order::whereIn('id', $orderIds)->update(['status' => 'cancelled']);
                return response()->json(['success' => true, 'message' => 'Orders marked as cancelled']);

            case 'mark_refunded':
                Order::whereIn('id', $orderIds)->update(['status' => 'refunded']);
                return response()->json(['success' => true, 'message' => 'Orders marked as refunded']);

            case 'delete':
                Order::whereIn('id', $orderIds)->delete();
                return response()->json(['success' => true, 'message' => 'Orders deleted successfully']);

            case 'export':
                return $this->exportOrders($orderIds);
        }
    }

    // Review Bulk Operations
    public function bulkReviewAction(BulkReviewActionRequest $request)
    {
        $reviewIds = $request->review_ids;
        $action = $request->action;

        switch ($action) {
            case 'approve':
                Review::whereIn('id', $reviewIds)->update(['is_approved' => true]);
                return response()->json(['success' => true, 'message' => 'Reviews approved successfully']);

            case 'reject':
                Review::whereIn('id', $reviewIds)->update(['is_approved' => false]);
                return response()->json(['success' => true, 'message' => 'Reviews rejected successfully']);

            case 'delete':
                Review::whereIn('id', $reviewIds)->delete();
                return response()->json(['success' => true, 'message' => 'Reviews deleted successfully']);
        }
    }

    // Category Bulk Operations
    public function bulkCategoryAction(BulkCategoryActionRequest $request)
    {
        $categoryIds = $request->category_ids;
        $action = $request->action;

        switch ($action) {
            case 'activate':
                Category::whereIn('id', $categoryIds)->update(['is_active' => true]);
                return response()->json(['success' => true, 'message' => 'Categories activated successfully']);

            case 'deactivate':
                Category::whereIn('id', $categoryIds)->update(['is_active' => false]);
                return response()->json(['success' => true, 'message' => 'Categories deactivated successfully']);

            case 'delete':
                Category::whereIn('id', $categoryIds)->delete();
                return response()->json(['success' => true, 'message' => 'Categories deleted successfully']);

            case 'update_parent':
                Category::whereIn('id', $categoryIds)->update(['parent_id' => $request->parent_id]);
                return response()->json(['success' => true, 'message' => 'Category parents updated successfully']);
        }
    }

    // Brand Bulk Operations
    public function bulkBrandAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'brand_ids' => 'required|array',
            'brand_ids.*' => 'exists:brands,id',
        ]);

        $brandIds = $request->brand_ids;
        $action = $request->action;

        switch ($action) {
            case 'activate':
                Brand::whereIn('id', $brandIds)->update(['is_active' => true]);
                return response()->json(['success' => true, 'message' => 'Brands activated successfully']);

            case 'deactivate':
                Brand::whereIn('id', $brandIds)->update(['is_active' => false]);
                return response()->json(['success' => true, 'message' => 'Brands deactivated successfully']);

            case 'delete':
                Brand::whereIn('id', $brandIds)->delete();
                return response()->json(['success' => true, 'message' => 'Brands deleted successfully']);
        }
    }

    // Coupon Bulk Operations
    public function bulkCouponAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'coupon_ids' => 'required|array',
            'coupon_ids.*' => 'exists:coupons,id',
        ]);

        $couponIds = $request->coupon_ids;
        $action = $request->action;

        switch ($action) {
            case 'activate':
                Coupon::whereIn('id', $couponIds)->update(['is_active' => true]);
                return response()->json(['success' => true, 'message' => 'Coupons activated successfully']);

            case 'deactivate':
                Coupon::whereIn('id', $couponIds)->update(['is_active' => false]);
                return response()->json(['success' => true, 'message' => 'Coupons deactivated successfully']);

            case 'delete':
                Coupon::whereIn('id', $couponIds)->delete();
                return response()->json(['success' => true, 'message' => 'Coupons deleted successfully']);
        }
    }

    // Banner Bulk Operations
    public function bulkBannerAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'banner_ids' => 'required|array',
            'banner_ids.*' => 'exists:banners,id',
        ]);

        $bannerIds = $request->banner_ids;
        $action = $request->action;

        switch ($action) {
            case 'activate':
                Banner::whereIn('id', $bannerIds)->update(['is_active' => true]);
                return response()->json(['success' => true, 'message' => 'Banners activated successfully']);

            case 'deactivate':
                Banner::whereIn('id', $bannerIds)->update(['is_active' => false]);
                return response()->json(['success' => true, 'message' => 'Banners deactivated successfully']);

            case 'delete':
                Banner::whereIn('id', $bannerIds)->delete();
                return response()->json(['success' => true, 'message' => 'Banners deleted successfully']);
        }
    }

    // Export Orders
    private function exportOrders($orderIds)
    {
        $orders = Order::whereIn('id', $orderIds)
            ->with(['user', 'items.product', 'shippingAddress', 'billingAddress'])
            ->get();

        $csvContent = "Order Number,Customer Name,Email,Status,Total Amount,Created At\n";
        
        foreach ($orders as $order) {
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
            ->header('Content-Disposition', 'attachment; filename="orders.csv"');
    }

    // Get statistics for bulk operations
    public function getBulkStats(BulkStatsRequest $request)
    {
        $type = $request->type;
        $stats = [];

        switch ($type) {
            case 'products':
                $stats = [
                    'total' => Product::count(),
                    'active' => Product::where('is_active', true)->count(),
                    'inactive' => Product::where('is_active', false)->count(),
                    'out_of_stock' => Product::where('stock_quantity', 0)->count(),
                ];
                break;

            case 'orders':
                $stats = [
                    'total' => Order::count(),
                    'pending' => Order::where('status', 'pending')->count(),
                    'paid' => Order::where('status', 'paid')->count(),
                    'processing' => Order::where('status', 'processing')->count(),
                    'shipped' => Order::where('status', 'shipped')->count(),
                    'delivered' => Order::where('status', 'delivered')->count(),
                    'cancelled' => Order::where('status', 'cancelled')->count(),
                    'refunded' => Order::where('status', 'refunded')->count(),
                ];
                break;

            case 'reviews':
                $stats = [
                    'total' => Review::count(),
                    'approved' => Review::where('is_approved', true)->count(),
                    'pending' => Review::where('is_approved', false)->count(),
                    '5_star' => Review::where('rating', 5)->count(),
                    '4_star' => Review::where('rating', 4)->count(),
                    '3_star' => Review::where('rating', 3)->count(),
                    '2_star' => Review::where('rating', 2)->count(),
                    '1_star' => Review::where('rating', 1)->count(),
                ];
                break;

            case 'categories':
                $stats = [
                    'total' => Category::count(),
                    'active' => Category::where('is_active', true)->count(),
                    'inactive' => Category::where('is_active', false)->count(),
                    'parent' => Category::whereNull('parent_id')->count(),
                    'child' => Category::whereNotNull('parent_id')->count(),
                ];
                break;

            case 'brands':
                $stats = [
                    'total' => Brand::count(),
                    'active' => Brand::where('is_active', true)->count(),
                    'inactive' => Brand::where('is_active', false)->count(),
                ];
                break;

            case 'coupons':
                $stats = [
                    'total' => Coupon::count(),
                    'active' => Coupon::where('is_active', true)->count(),
                    'inactive' => Coupon::where('is_active', false)->count(),
                    'expired' => Coupon::where('expires_at', '<', now())->count(),
                ];
                break;

            case 'banners':
                $stats = [
                    'total' => Banner::count(),
                    'active' => Banner::where('is_active', true)->count(),
                    'inactive' => Banner::where('is_active', false)->count(),
                ];
                break;
        }

        return response()->json($stats);
    }
}
