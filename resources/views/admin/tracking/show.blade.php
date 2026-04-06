@extends('admin.layout')

@section('title', 'Order Tracking - ' . $order->order_number)

@section('content')
<div class="container-fluid px-4">
    <!-- eBay-style Header -->
    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
        <div>
            <h1 class="h3 mb-0 text-dark">Order #{{ $order->order_number }}</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-user mr-1"></i>
                {{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}
                @if($order->user)
                    <span class="text-muted">• {{ $order->user->email }}</span>
                @endif
            </p>
        </div>
        <div class="d-flex align-items-center">
            <span class="badge badge-{{ $order->status_color }} px-3 py-2 mr-3">
                {{ ucfirst($order->status) }}
            </span>
            <a href="{{ route('admin.tracking.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Search
            </a>
            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-primary ml-2">
                <i class="fas fa-edit mr-2"></i>Manage Order
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row mt-4">
        <!-- Left Column - Order Details -->
        <div class="col-lg-8">
            <!-- Order Summary Card -->
            <div class="bg-white border rounded-lg shadow-sm mb-4">
                <div class="border-bottom p-4">
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-shopping-bag mr-2"></i>Order Summary
                    </h5>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <span class="text-muted">Order Date:</span>
                                <div class="font-weight-bold text-dark">{{ $order->created_at->format('M j, Y H:i') }}</div>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted">Payment Method:</span>
                                <div class="font-weight-bold text-dark">{{ $order->payment->method_display_name ?? 'N/A' }}</div>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted">Customer Type:</span>
                                <div class="font-weight-bold text-dark">
                                    @if($order->user)
                                        <i class="fas fa-user-check text-success mr-1"></i>Registered User
                                    @else
                                        <i class="fas fa-user text-muted mr-1"></i>Guest Customer
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <span class="text-muted">Total Amount:</span>
                                <div class="font-weight-bold text-primary h4 mb-0">৳{{ number_format($order->total_amount, 2) }}</div>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted">Items:</span>
                                <div class="font-weight-bold text-dark">{{ $order->items_count }} products</div>
                            </div>
                            <div class="mb-3">
                                <span class="text-muted">Estimated Delivery:</span>
                                <div class="font-weight-bold text-dark">{{ $order->getEstimatedDelivery() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="bg-white border rounded-lg shadow-sm mb-4">
                <div class="border-bottom p-4">
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-history mr-2"></i>Order Timeline
                    </h5>
                </div>
                <div class="p-4">
                    @php $trackingHistory = $order->getTrackingHistory(); @endphp
                    <div class="ebay-timeline">
                        @foreach($trackingHistory as $index => $event)
                            <div class="ebay-timeline-item">
                                <div class="ebay-timeline-marker bg-{{ $event['color'] }}">
                                    <i class="fas {{ $event['icon'] }} text-white"></i>
                                </div>
                                <div class="ebay-timeline-content">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="font-weight-bold text-dark mb-1">{{ $event['status'] }}</h6>
                                            <p class="text-muted mb-0">{{ $event['description'] }}</p>
                                        </div>
                                        <small class="text-muted font-weight-bold">{{ $event['date'] }}</small>
                                    </div>
                                </div>
                            </div>
                            
                            @if($index < count($trackingHistory) - 1)
                                <div class="ebay-timeline-line"></div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white border rounded-lg shadow-sm">
                <div class="border-bottom p-4">
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-box mr-2"></i>Order Items ({{ $order->items_count }})
                    </h5>
                </div>
                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="border-top-0 font-weight-bold text-dark">Product</th>
                                    <th class="border-top-0 font-weight-bold text-dark">SKU</th>
                                    <th class="border-top-0 font-weight-bold text-dark text-right">Price</th>
                                    <th class="border-top-0 font-weight-bold text-dark text-center">Qty</th>
                                    <th class="border-top-0 font-weight-bold text-dark text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr class="border-bottom">
                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->primaryImage)
                                                    <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" 
                                                         alt="{{ $item->product_name }}" 
                                                         class="img-thumbnail mr-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light border d-flex align-items-center justify-content-center mr-3" 
                                                         style="width: 60px; height: 60px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-weight-bold text-dark">{{ $item->product_name }}</div>
                                                    @if($item->product)
                                                        <small class="text-muted">{{ $item->product->category->name ?? '' }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <code class="bg-light px-2 py-1 rounded">{{ $item->product_sku }}</code>
                                        </td>
                                        <td class="py-3 text-right">
                                            <div class="font-weight-bold text-dark">৳{{ number_format($item->price, 2) }}</div>
                                        </td>
                                        <td class="py-3 text-center">
                                            <span class="badge badge-secondary">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="py-3 text-right">
                                            <div class="font-weight-bold text-primary">৳{{ number_format($item->total, 2) }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="4" class="font-weight-bold text-dark">Subtotal</td>
                                    <td class="text-right font-weight-bold text-dark">৳{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-muted">Tax (15%)</td>
                                    <td class="text-right text-muted">৳{{ number_format($order->tax_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-muted">Shipping</td>
                                    <td class="text-right text-muted">৳{{ number_format($order->shipping_amount, 2) }}</td>
                                </tr>
                                @if($order->discount_amount > 0)
                                    <tr>
                                        <td colspan="4" class="text-success">Discount</td>
                                        <td class="text-right text-success">-৳{{ number_format($order->discount_amount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="bg-primary text-white">
                                    <td colspan="4" class="font-weight-bold">Total</td>
                                    <td class="text-right font-weight-bold">৳{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Status & Customer Info -->
        <div class="col-lg-4">
            <!-- Status Update Card -->
            <div class="bg-white border rounded-lg shadow-sm mb-4">
                <div class="border-bottom p-4">
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-edit mr-2"></i>Update Status
                    </h5>
                </div>
                <div class="p-4">
                    <form method="POST" action="{{ route('admin.tracking.update-status', $order->order_number) }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">Order Status</label>
                            <select class="form-control form-control-lg" id="status" name="status" required>
                                <option value="{{ $order->status }}" selected>{{ ucfirst($order->status) }} (Current)</option>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">Tracking Number</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-truck text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control border-left-0" id="tracking_number" name="tracking_number" 
                                       placeholder="Enter tracking number" 
                                       value="{{ $order->payment->gateway_transaction_id ?? '' }}">
                            </div>
                            <small class="text-muted">For shipped orders</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Add any notes about this status update">{{ $order->notes ?? '' }}</textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-save mr-2"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Customer Information Card -->
            <div class="bg-white border rounded-lg shadow-sm mb-4">
                <div class="border-bottom p-4">
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-user mr-2"></i>Customer Information
                    </h5>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <span class="text-muted">Name:</span>
                        <div class="font-weight-bold text-dark">
                            {{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <span class="text-muted">Phone:</span>
                        <div class="font-weight-bold text-dark">
                            <i class="fas fa-phone text-muted mr-2"></i>{{ $order->shippingAddress->phone }}
                        </div>
                    </div>
                    
                    @if($order->user)
                        <div class="mb-3">
                            <span class="text-muted">Email:</span>
                            <div class="font-weight-bold text-dark">
                                <i class="fas fa-envelope text-muted mr-2"></i>{{ $order->user->email }}
                            </div>
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <span class="text-muted">Shipping Address:</span>
                        <div class="font-weight-bold text-dark bg-light p-3 rounded">
                            {{ $order->shippingAddress->full_address }}
                        </div>
                    </div>

                    @if($order->billingAddress && $order->billingAddress->id !== $order->shippingAddress->id)
                        <div class="mb-3">
                            <span class="text-muted">Billing Address:</span>
                            <div class="font-weight-bold text-dark bg-light p-3 rounded">
                                {{ $order->billingAddress->full_address }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-white border rounded-lg shadow-sm">
                <div class="border-bottom p-4">
                    <h5 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-bolt mr-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="p-4">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print mr-2"></i>Print Order Details
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="copyOrderNumber()">
                            <i class="fas fa-copy mr-2"></i>Copy Order Number
                        </button>
                        <a href="mailto:{{ $order->user ? $order->user->email : '#' }}" class="btn btn-outline-success">
                            <i class="fas fa-envelope mr-2"></i>Email Customer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.ebay-timeline {
    position: relative;
    padding-left: 30px;
}

.ebay-timeline-item {
    position: relative;
    margin-bottom: 25px;
}

.ebay-timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.ebay-timeline-line {
    position: absolute;
    left: -18px;
    top: 24px;
    width: 2px;
    height: 25px;
    background-color: #e9ecef;
}

.ebay-timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
    transition: all 0.3s ease;
}

.ebay-timeline-content:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.border-left-0 {
    border-left: none !important;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
}

.table th {
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.btn-lg {
    font-weight: 600;
}

.form-control-lg {
    font-size: 1rem;
}

.border-bottom {
    border-bottom: 1px solid #dee2e6 !important;
}

.border-top-0 {
    border-top: none !important;
}

code {
    font-size: 0.875rem;
}
</style>

<script>
function copyOrderNumber() {
    const orderNumber = '{{ $order->order_number }}';
    navigator.clipboard.writeText(orderNumber).then(function() {
        // Show success message
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
        btn.classList.remove('btn-outline-info');
        btn.classList.add('btn-success');
        
        setTimeout(function() {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-info');
        }, 2000);
    });
}
</script>
@endsection
