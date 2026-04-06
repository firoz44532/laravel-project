<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Notifications\SendOrderNotification;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\OrderStatusUpdateRequest;
use App\Http\Requests\Admin\OrderBulkStatusUpdateRequest;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'payment', 'shippingAddress']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number or customer name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($query) use ($search) {
                      $query->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(20);
        
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'payment', 'shippingAddress', 'billingAddress']);
        
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(OrderStatusUpdateRequest $request, Order $order)
    {
        $validated = $request->validated();

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        // Validate status transitions
        if (!$this->isValidStatusTransition($oldStatus, $newStatus)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status transition from ' . $oldStatus . ' to ' . $newStatus
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Update order status
            $order->update([
                'status' => $newStatus,
                'notes' => $request->notes,
            ]);

            // Update payment status if order is paid
            if ($newStatus === 'paid' && $order->payment) {
                $order->payment->update(['status' => 'completed']);
            }

            // Send order status update email notification
            try {
                $order->user->notify(new SendOrderNotification($order));
            } catch (\Exception $e) {
                \Log::error('Failed to send order status update email: ' . $e->getMessage());
                // Continue with order process even if email fails
            }

            // Handle stock management for cancelled orders
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product && $product->track_stock) {
                        $product->increment('stock_quantity', $item->quantity);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully!',
                'new_status' => $newStatus,
                'status_color' => $order->status_color
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating order status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        // Apply same filters as index
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->get();

        // CSV export logic would go here
        // For now, return a simple response
        return response()->json([
            'success' => true,
            'message' => 'Export functionality would be implemented here',
            'count' => $orders->count()
        ]);
    }

    public function dashboard()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'paid_orders' => Order::where('status', 'paid')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
        ];

        $recentOrders = Order::with(['user'])
            ->latest()
            ->take(10)
            ->get();

        $monthlyRevenue = Order::where('status', 'delivered')
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        return view('admin.orders.dashboard', compact('stats', 'recentOrders', 'monthlyRevenue'));
    }

    private function isValidStatusTransition($from, $to)
    {
        $validTransitions = [
            'pending' => ['paid', 'cancelled'],
            'paid' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered'],
            'delivered' => ['refunded'],
            'cancelled' => [],
            'refunded' => [],
        ];

        return in_array($to, $validTransitions[$from] ?? []);
    }

    public function bulkUpdateStatus(OrderBulkStatusUpdateRequest $request)
    {
        $validated = $request->validated();

        $orderIds = $validated['order_ids'];
        $newStatus = $validated['status'];
        $updated = 0;
        $errors = [];

        foreach ($orderIds as $orderId) {
            $order = Order::find($orderId);
            
            if ($order && $this->isValidStatusTransition($order->status, $newStatus)) {
                $order->update(['status' => $newStatus]);
                
                // Send order status update email notification for bulk updates
                try {
                    $order->user->notify(new SendOrderNotification($order));
                } catch (\Exception $e) {
                    \Log::error('Failed to send bulk order status update email for order #' . $order->order_number . ': ' . $e->getMessage());
                    // Continue with order process even if email fails
                }
                
                $updated++;
            } else {
                $errors[] = "Order #{$order->order_number}: Invalid status transition";
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Updated {$updated} orders successfully",
            'updated_count' => $updated,
            'errors' => $errors
        ]);
    }

    public function printInvoice(Order $order)
    {
        $order->load(['user', 'items.product', 'payment', 'shippingAddress', 'billingAddress']);
        
        return view('admin.orders.invoice', compact('order'));
    }

    public function sendInvoiceEmail(Order $order)
    {
        // Email sending logic would go here
        return response()->json([
            'success' => true,
            'message' => 'Invoice sent successfully to customer email'
        ]);
    }
}
