@extends('admin.layout')

@section('title', 'Merchant Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ $merchant->store_name }}</h1>
                <div class="btn-group">
                    <a href="{{ route('merchants.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Merchants
                    </a>
                    
                    @if($merchant->status === 'pending')
                        <form method="POST" action="{{ route('merchants.approve', $merchant) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success" onclick="return confirm('Approve this merchant?')">
                                <i class="fas fa-check me-2"></i>Approve
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times me-2"></i>Reject
                        </button>
                    @elseif($merchant->status === 'approved')
                        <form method="POST" action="{{ route('merchants.suspend', $merchant) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Suspend this merchant?')">
                                <i class="fas fa-pause me-2"></i>Suspend
                            </button>
                        </form>
                    @elseif($merchant->status === 'suspended')
                        <form method="POST" action="{{ route('merchants.approve', $merchant) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success" onclick="return confirm('Reactivate this merchant?')">
                                <i class="fas fa-play me-2"></i>Reactivate
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Store Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Store Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4"><strong>Store Name:</strong></div>
                        <div class="col-sm-8">{{ $merchant->store_name }}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4"><strong>Store Slug:</strong></div>
                        <div class="col-sm-8">{{ $merchant->store_slug }}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4"><strong>Status:</strong></div>
                        <div class="col-sm-8">{!! $merchant->status_badge !!}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4"><strong>Commission:</strong></div>
                        <div class="col-sm-8">{{ $merchant->commission_rate }}%</div>
                    </div>
                    @if($merchant->approved_at)
                        <hr>
                        <div class="row">
                            <div class="col-sm-4"><strong>Approved:</strong></div>
                            <div class="col-sm-8">{{ $merchant->approved_at->format('M d, Y h:i A') }}</div>
                        </div>
                    @endif
                    @if($merchant->rejection_reason)
                        <hr>
                        <div class="row">
                            <div class="col-sm-4"><strong>Rejection Reason:</strong></div>
                            <div class="col-sm-8 text-danger">{{ $merchant->rejection_reason }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Owner Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4"><strong>Name:</strong></div>
                        <div class="col-sm-8">{{ $merchant->user->name }}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4"><strong>Email:</strong></div>
                        <div class="col-sm-8">{{ $merchant->user->email }}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4"><strong>Phone:</strong></div>
                        <div class="col-sm-8">{{ $merchant->user->phone ?? 'N/A' }}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4"><strong>Member Since:</strong></div>
                        <div class="col-sm-8">{{ $merchant->user->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Store Description -->
    @if($merchant->store_description)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Store Description</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $merchant->store_description }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $stats['total_products'] }}</h4>
                    <p class="card-text">Total Products</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $stats['active_products'] }}</h4>
                    <p class="card-text">Active Products</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $stats['total_orders'] }}</h4>
                    <p class="card-text">Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4 class="card-title">৳{{ number_format($stats['total_revenue'], 2) }}</h4>
                    <p class="card-text">Total Revenue</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Orders</h5>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Products</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td><strong>{{ $order->order_number }}</strong></td>
                                            <td>{{ $order->user->name }}</td>
                                            <td>
                                                <div class="small">
                                                    @foreach($order->items->take(2) as $item)
                                                        <div>{{ $item->product_name }} x {{ $item->quantity }}</div>
                                                    @endforeach
                                                    @if($order->items->count() > 2)
                                                        <div class="text-muted">+{{ $order->items->count() - 2 }} more</div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>৳{{ number_format($order->total_amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $order->status === 'completed' ? 'success' : 
                                                    ($order->status === 'pending' ? 'warning' : 'info') 
                                                }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300 mb-3"></i>
                            <h6>No orders yet</h6>
                            <p class="text-muted">This merchant hasn't received any orders.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
@if($merchant->status === 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Merchant Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('merchants.reject', $merchant) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason *</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
                        <small class="form-text text-muted">This reason will be sent to the merchant.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Application</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
