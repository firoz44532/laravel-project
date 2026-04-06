<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\TrackingIndexRequest;
use App\Http\Requests\Admin\TrackingStatusUpdateRequest;
use App\Http\Requests\Admin\TrackingBulkUpdateRequest;
use App\Models\Order;
use App\Models\Address;

class TrackingController extends Controller
{
    public function index(TrackingIndexRequest $request)
    {
        $validated = $request->validated();

        $searchMethod = $validated['search_method'] ?? $request->get('search_method', 'order_number');
        $orderNumber = $validated['order_number'] ?? null;
        $customerName = $validated['customer_name'] ?? null;
        $phone = $validated['phone'] ?? null;
        $email = $validated['email'] ?? null;
        
        $orders = collect();
        $searchPerformed = false;

        if ($orderNumber) {
            // Search by order number
            $order = Order::where('order_number', $orderNumber)
                ->with(['items.product', 'shippingAddress', 'billingAddress', 'payment', 'user'])
                ->first();
            
            if ($order) {
                $orders = collect([$order]);
            }
            $searchPerformed = true;
        } elseif ($customerName || $phone || $email) {
            // Search by customer details
            $query = Order::with(['items.product', 'shippingAddress', 'billingAddress', 'payment', 'user']);
            
            if ($phone) {
                $query->whereHas('shippingAddress', function($q) use ($phone) {
                    $q->where('phone', 'like', '%' . $phone . '%');
                });
            }
            
            if ($email) {
                $query->whereHas('user', function($q) use ($email) {
                    $q->where('email', 'like', '%' . $email . '%');
                });
            }
            
            $orders = $query->get();
            
            // Filter by customer name if provided
            if ($customerName) {
                $orders = $orders->filter(function($order) use ($customerName) {
                    $fullName = strtolower($order->shippingAddress->first_name . ' ' . $order->shippingAddress->last_name);
                    $searchName = strtolower($customerName);
                    return strpos($fullName, $searchName) !== false;
                });
            }
            
            $searchPerformed = true;
        }

        return view('admin.tracking.index', compact('orders', 'searchPerformed', 'searchMethod'));
    }

    public function show($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product', 'shippingAddress', 'billingAddress', 'payment', 'user'])
            ->firstOrFail();

        return view('admin.tracking.show', compact('order'));
    }

    public function updateStatus(TrackingStatusUpdateRequest $request, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        
        $order->update([
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        // Update tracking number if provided
        if ($request->tracking_number && $order->payment) {
            $order->payment->update([
                'gateway_transaction_id' => $request->tracking_number
            ]);
        }

        return redirect()->route('admin.tracking.show', $orderNumber)
            ->with('success', 'Order status updated successfully.');
    }

    public function bulkUpdateStatus(TrackingBulkUpdateRequest $request)
    {
        $validated = $request->validated();

        $updatedCount = Order::whereIn('id', $validated['order_ids'])
            ->update([
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null
            ]);

        return redirect()->back()
            ->with('success', "Updated status for {$updatedCount} orders successfully.");
    }

    public function searchOrders(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return response()->json(['orders' => []]);
        }

        $orders = Order::where('order_number', 'like', '%' . $query . '%')
            ->orWhereHas('shippingAddress', function($q) use ($query) {
                $q->where('first_name', 'like', '%' . $query . '%')
                  ->orWhere('last_name', 'like', '%' . $query . '%')
                  ->orWhere('phone', 'like', '%' . $query . '%');
            })
            ->with(['shippingAddress', 'user'])
            ->limit(10)
            ->get();

        return response()->json([
            'orders' => $orders->map(function($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->shippingAddress->first_name . ' ' . $order->shippingAddress->last_name,
                    'phone' => $order->shippingAddress->phone,
                    'email' => $order->user ? $order->user->email : 'N/A',
                    'status' => $order->status,
                    'total_amount' => $order->formatted_total,
                    'created_at' => $order->created_at->format('M j, Y H:i')
                ];
            })
        ]);
    }
}
