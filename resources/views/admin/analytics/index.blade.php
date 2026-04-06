@extends('admin.layout')

@section('title', 'Analytics Dashboard')
@section('header', 'Analytics Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Period Selector -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Analytics Dashboard</h1>
            <div class="flex space-x-2">
                <select id="period-selector" class="px-4 py-2 border rounded-lg focus:outline-none focus:border-primary">
                    <option value="7days" {{ $period === '7days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30days" {{ $period === '30days' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="90days" {{ $period === '90days' ? 'selected' : '' }}>Last 90 Days</option>
                    <option value="1year" {{ $period === '1year' ? 'selected' : '' }}>Last Year</option>
                </select>
                <button onclick="exportReport('sales')" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">
                    <i class="fas fa-download mr-2"></i>Export Sales
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-600">Total Orders</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-600">Total Revenue</h3>
                    <p class="text-2xl font-bold text-gray-900">৳{{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-600">New Customers</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_customers'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg">
                    <i class="fas fa-box text-orange-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-600">Active Products</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Sales Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Sales Overview</h2>
            <div class="relative h-64">
                <canvas id="sales-chart"></canvas>
            </div>
        </div>

        <!-- Order Status Distribution -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Order Status Distribution</h2>
            <div class="relative h-64">
                <canvas id="status-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Products -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold">Top Products</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($topProducts as $product)
                        <div class="flex items-center space-x-4">
                            @if($product->primaryImage)
                                <img src="{{ $product->primaryImage->image_url }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-12 h-12 object-cover rounded">
                            @else
                                <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $product->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $product->category->name ?? 'No Category' }}</p>
                                <div class="flex items-center space-x-4 mt-1">
                                    <span class="text-sm text-gray-600">{{ $product->order_items_count }} sold</span>
                                    <span class="text-sm font-medium text-primary">৳{{ number_format($product->price, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold">Recent Orders</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($recentOrders as $order)
                        <div class="border-b pb-4 last:border-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $order->order_number }}</h4>
                                    <p class="text-sm text-gray-600">{{ $order->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $order->created_at->format('M j, Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    <p class="text-lg font-bold text-primary mt-1">৳{{ number_format($order->total_amount, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Growth Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">Customer Growth</h2>
        <div class="relative h-64">
            <canvas id="customer-growth-chart"></canvas>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Payment Methods</h2>
        <div class="relative h-64">
            <canvas id="payment-chart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Period selector
document.getElementById('period-selector').addEventListener('change', function() {
    const period = this.value;
    window.location.href = `{{ route('admin.analytics.index') }}?period=${period}`;
});

// Sales Chart
const salesCtx = document.getElementById('sales-chart').getContext('2d');
const salesData = @json($salesData);
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: salesData.map(item => item.date),
        datasets: [{
            label: 'Revenue',
            data: salesData.map(item => item.revenue),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }, {
            label: 'Orders',
            data: salesData.map(item => item.orders),
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('status-chart').getContext('2d');
const statusData = @json($orderStatusData);
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusData.map(item => item.status),
        datasets: [{
            data: statusData.map(item => item.count),
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 206, 86)',
                'rgb(75, 192, 192)',
                'rgb(153, 102, 255)',
                'rgb(255, 159, 64)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
            }
        }
    }
});

// Customer Growth Chart
const customerCtx = document.getElementById('customer-growth-chart').getContext('2d');
const customerData = @json($customerGrowth);
new Chart(customerCtx, {
    type: 'line',
    data: {
        labels: customerData.map(item => item.date),
        datasets: [{
            label: 'New Customers',
            data: customerData.map(item => item.count),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        }
    }
});

// Payment Methods Chart
const paymentCtx = document.getElementById('payment-chart').getContext('2d');
const paymentData = @json($paymentMethodData);
new Chart(paymentCtx, {
    type: 'bar',
    data: {
        labels: paymentData.map(item => item.method),
        datasets: [{
            label: 'Orders',
            data: paymentData.map(item => item.count),
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

function exportReport(type) {
    const period = document.getElementById('period-selector').value;
    window.location.href = `{{ route('admin.analytics.export') }}?type=${type}&period=${period}`;
}
</script>
@endsection
