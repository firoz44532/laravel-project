@extends('layouts.merchant')

@section('title', 'Earnings')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Amazon-Daraz Mixed Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-1 flex items-center">
                        <i class="fas fa-chart-line mr-3"></i>
                        Earnings Dashboard
                    </h1>
                    <p class="text-orange-100 text-sm">Track your revenue, commissions, and payouts</p>
                </div>
                <div class="flex space-x-3">
                    <button class="bg-white text-orange-500 px-4 py-2 rounded-lg font-semibold hover:bg-orange-50 transition duration-200 shadow-md">
                        <i class="fas fa-download mr-2"></i>Download Report
                    </button>
                    <button class="bg-white text-orange-500 px-4 py-2 rounded-lg font-semibold hover:bg-orange-50 transition duration-200 shadow-md">
                        <i class="fas fa-file-invoice mr-2"></i>Request Payout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Stats Overview -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Revenue -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-blue-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Revenue</p>
                            <p class="text-3xl font-bold text-gray-900">৳{{ number_format($merchant->total_revenue, 2) }}</p>
                            <div class="flex items-center mt-2">
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-medium">
                                    <i class="fas fa-arrow-up mr-1"></i>Lifetime sales
                                </span>
                            </div>
                        </div>
                        <div class="bg-blue-100 rounded-full p-4">
                            <i class="fas fa-chart-line text-blue-500 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Platform Fees -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-yellow-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Platform Fees</p>
                            <p class="text-3xl font-bold text-gray-900">৳{{ number_format($merchant->total_commission, 2) }}</p>
                            <div class="flex items-center mt-2">
                                <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full font-medium">
                                    {{ $merchant->commission_rate }}% commission
                                </span>
                            </div>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-4">
                            <i class="fas fa-percentage text-yellow-500 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Your Earnings -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-green-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Your Earnings</p>
                            <p class="text-3xl font-bold text-gray-900">৳{{ number_format($merchant->total_earnings, 2) }}</p>
                            <div class="flex items-center mt-2">
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full font-medium">
                                    <i class="fas fa-wallet mr-1"></i>Available for payout
                                </span>
                            </div>
                        </div>
                        <div class="bg-green-100 rounded-full p-4">
                            <i class="fas fa-wallet text-green-500 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Monthly Revenue Chart (Amazon-style) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center">
                                <i class="fas fa-chart-area mr-2 text-orange-500"></i>
                                Monthly Revenue Trend
                            </h2>
                            <div class="flex items-center space-x-2">
                                <button class="px-3 py-1 bg-orange-500 text-white rounded-lg text-sm">12M</button>
                                <button class="px-3 py-1 bg-gray-200 text-gray-600 rounded-lg text-sm">6M</button>
                                <button class="px-3 py-1 bg-gray-200 text-gray-600 rounded-lg text-sm">3M</button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($monthlyEarnings->count() > 0)
                            <div class="relative" style="height: 300px;">
                                <canvas id="earningsChart"></canvas>
                            </div>
                            <div class="mt-4 grid grid-cols-3 gap-4">
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <p class="text-sm text-gray-600">Average Monthly</p>
                                    <p class="text-lg font-bold text-blue-600">৳{{ number_format($monthlyEarnings->avg('revenue'), 0) }}</p>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <p class="text-sm text-gray-600">Best Month</p>
                                    <p class="text-lg font-bold text-green-600">৳{{ number_format($monthlyEarnings->max('revenue'), 0) }}</p>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <p class="text-sm text-gray-600">Total Months</p>
                                    <p class="text-lg font-bold text-purple-600">{{ $monthlyEarnings->count() }}</p>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="bg-gradient-to-br from-blue-100 to-purple-100 rounded-full w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                                    <i class="fas fa-chart-line text-blue-500 text-4xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">No earnings data yet</h3>
                                <p class="text-gray-600">Your monthly revenue will appear here once you start making sales.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Commission Structure (Daraz-style) -->
            <div class="space-y-6">
                <!-- Commission Breakdown -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500 to-red-500 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-percentage mr-2"></i>
                            Commission Structure
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Platform Commission -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Platform Commission</span>
                                    <span class="text-sm font-bold text-yellow-600">{{ $merchant->commission_rate }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-yellow-400 to-orange-400 h-3 rounded-full" style="width: {{ $merchant->commission_rate }}%"></div>
                                </div>
                            </div>
                            
                            <!-- Payment Processing -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Payment Processing</span>
                                    <span class="text-sm font-bold text-blue-600">2.5%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-blue-400 to-indigo-400 h-3 rounded-full" style="width: 2.5%"></div>
                                </div>
                            </div>
                            
                            <!-- Your Earnings -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Your Earnings</span>
                                    <span class="text-sm font-bold text-green-600">{{ 100 - $merchant->commission_rate - 2.5 }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-green-400 to-emerald-400 h-3 rounded-full" style="width: {{ 100 - $merchant->commission_rate - 2.5 }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-3">Payout Schedule</h4>
                            <div class="space-y-3">
                                <div class="flex items-center text-sm">
                                    <div class="bg-blue-100 rounded-full p-2 mr-3">
                                        <i class="fas fa-clock text-blue-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Weekly Payouts</p>
                                        <p class="text-gray-500">Every Friday</p>
                                    </div>
                                </div>
                                <div class="flex items-center text-sm">
                                    <div class="bg-green-100 rounded-full p-2 mr-3">
                                        <i class="fas fa-shield-alt text-green-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Holding Period</p>
                                        <p class="text-gray-500">7 days for refunds</p>
                                    </div>
                                </div>
                                <div class="flex items-center text-sm">
                                    <div class="bg-purple-100 rounded-full p-2 mr-3">
                                        <i class="fas fa-bank text-purple-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">Payment Method</p>
                                        <p class="text-gray-500">Direct bank transfer</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg shadow-md p-6 text-white">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <i class="fas fa-calculator mr-2"></i>
                        Quick Stats
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center bg-white bg-opacity-10 rounded-lg p-3">
                            <span class="text-sm font-medium">Commission Rate</span>
                            <span class="font-bold text-lg">{{ $merchant->commission_rate }}%</span>
                        </div>
                        <div class="flex justify-between items-center bg-white bg-opacity-10 rounded-lg p-3">
                            <span class="text-sm font-medium">Net Earnings</span>
                            <span class="font-bold text-lg">{{ round((100 - $merchant->commission_rate - 2.5), 1) }}%</span>
                        </div>
                        <div class="flex justify-between items-center bg-white bg-opacity-10 rounded-lg p-3">
                            <span class="text-sm font-medium">Next Payout</span>
                            <span class="font-bold text-lg">Friday</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Completed Orders -->
        <div class="mt-6">
            <div class="bg-white rounded-lg shadow-md border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-800 flex items-center">
                            <i class="fas fa-receipt mr-2 text-orange-500"></i>
                            Recent Completed Orders
                        </h2>
                        <button class="text-orange-500 hover:text-orange-700 font-medium text-sm">
                            View All Orders <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    @if($recentOrders->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Your Earnings</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentOrders as $order)
                                    <tr class="hover:bg-orange-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="bg-orange-100 rounded-lg p-2 mr-3">
                                                    <i class="fas fa-shopping-bag text-orange-500 text-sm"></i>
                                                </div>
                                                <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="bg-gray-200 rounded-full h-8 w-8 flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-gray-500 text-sm"></i>
                                                </div>
                                                <div class="text-sm text-gray-900">{{ $order->user->name }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">৳{{ number_format($order->total_amount, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-yellow-600">
                                                -৳{{ number_format($order->total_amount * (($merchant->commission_rate + 2.5) / 100), 2) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-green-600">
                                                ৳{{ number_format($order->total_amount * (1 - ($merchant->commission_rate + 2.5) / 100), 2) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $order->created_at->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->created_at->format('h:i A') }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-16">
                            <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-full w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                                <i class="fas fa-receipt text-gray-400 text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">No completed orders yet</h3>
                            <p class="text-gray-600">Completed orders and earnings will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($monthlyEarnings->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('earningsChart').getContext('2d');
    
    const labels = {!! json_encode($monthlyEarnings->pluck('month')->reverse()) !!};
    const revenueData = {!! json_encode($monthlyEarnings->pluck('revenue')->reverse()) !!};
    
    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(251, 146, 60, 0.3)');
    gradient.addColorStop(1, 'rgba(251, 146, 60, 0.01)');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Monthly Revenue',
                data: revenueData,
                borderColor: 'rgb(251, 146, 60)',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(251, 146, 60)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgb(251, 146, 60)',
                    borderWidth: 1,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: ৳' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6b7280'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#6b7280',
                        callback: function(value) {
                            return '৳' + value.toLocaleString();
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
});
</script>
@endif

<style>
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #ff5722, #ff9800);
}
</style>
@endsection
