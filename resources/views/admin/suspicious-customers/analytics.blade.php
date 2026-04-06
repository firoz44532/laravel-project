@extends('admin.layout')

@section('title', 'Suspicious Customers Analytics')
@section('header', 'Suspicious Customers Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Breadcrumb / Page bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('admin.suspicious-customers.index') }}" class="hover:text-orange-600 transition-colors">Suspicious Customers</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900 font-medium">Analytics</span>
        </div>
        <a href="{{ route('admin.suspicious-customers.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-orange-500 hover:text-orange-600 transition-colors shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    {{-- KPI Cards - Daraz style with icons --}}
    @php
        $totalFlagged = $riskDistribution['high'] + $riskDistribution['medium'] + $riskDistribution['low'];
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-user-slash text-orange-600 text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Flagged</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalFlagged }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">High Risk</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $riskDistribution['high'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-amber-600 text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Medium Risk</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $riskDistribution['medium'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center">
                    <i class="fas fa-shield-alt text-sky-600 text-lg"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Low Risk</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $riskDistribution['low'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-orange-600"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-900">Registration Trends (Last 30 Days)</h3>
            </div>
            <div class="p-6">
                <div class="h-64">
                    <canvas id="registrationTrendsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center">
                    <i class="fas fa-pie-chart text-red-600"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-900">Risk Distribution</h3>
            </div>
            <div class="p-6">
                <div class="h-64">
                    <canvas id="riskDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Risk Factors & Recent High Risk --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-list-ul text-amber-600"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-900">Top Risk Factors</h3>
            </div>
            <div class="p-6">
                @if(count($topFactors) > 0)
                    <div class="overflow-x-auto -mx-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Risk Factor</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Count</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">%</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @php $totalFactors = array_sum($topFactors); @endphp
                                @foreach($topFactors as $factor => $count)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-3 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $factor)) }}</td>
                                        <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $count }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-600">{{ round(($count / $totalFactors) * 100, 1) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-chart-bar text-4xl text-gray-200 mb-2"></i>
                        <p class="text-sm">No risk factors data available.</p>
                    </div>
                @endif
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center">
                    <i class="fas fa-user-times text-red-600"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-900">Recent High Risk (Last 7 Days)</h3>
            </div>
            <div class="p-6">
                @if(count($recentHighRisk) > 0)
                    <div class="overflow-x-auto -mx-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Risk</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($recentHighRisk as $customer)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-3">
                                            <a href="{{ route('admin.suspicious-customers.show', $customer) }}" class="text-sm font-medium text-orange-600 hover:text-orange-700 hover:underline">
                                                {{ $customer->email }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ $customer->risk_score }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-600">{{ $customer->created_at->format('M j, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-user-check text-4xl text-gray-200 mb-2"></i>
                        <p class="text-sm">No high risk customers in the last 7 days.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Fake Order Distribution --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center">
                <i class="fas fa-shopping-cart text-purple-600"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-900">Fake Order Distribution</h3>
        </div>
        <div class="p-6">
            @if(count($fakeOrderTrends) > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="h-64">
                            <canvas id="fakeOrderTrendsChart"></canvas>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Fake Orders</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Customers</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($fakeOrderTrends->take(10) as $trend)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $trend->fake_order_count }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-600">{{ $trend->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-box-open text-4xl text-gray-200 mb-2"></i>
                    <p class="text-sm">No fake order data available.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    const orange = 'rgb(234, 88, 12)';
    const orangeLight = 'rgba(234, 88, 12, 0.15)';

    // Registration Trends
    const registrationCtx = document.getElementById('registrationTrendsChart');
    if (registrationCtx) {
        new Chart(registrationCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($registrationTrends->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('M j'))),
                datasets: [{
                    label: 'New Suspicious Customers',
                    data: @json($registrationTrends->pluck('count')),
                    borderColor: orange,
                    backgroundColor: orangeLight,
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2,
                    pointBackgroundColor: orange,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.06)' },
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { maxRotation: 45 }
                    }
                }
            }
        });
    }

    // Risk Distribution
    const riskCtx = document.getElementById('riskDistributionChart');
    if (riskCtx) {
        new Chart(riskCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['High Risk', 'Medium Risk', 'Low Risk'],
                datasets: [{
                    data: [
                        {{ $riskDistribution['high'] }},
                        {{ $riskDistribution['medium'] }},
                        {{ $riskDistribution['low'] }}
                    ],
                    backgroundColor: [
                        'rgb(220, 38, 38)',
                        'rgb(245, 158, 11)',
                        'rgb(14, 165, 233)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Fake Order Trends (only if we have data)
    const fakeOrderCtx = document.getElementById('fakeOrderTrendsChart');
    if (fakeOrderCtx) {
        new Chart(fakeOrderCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($fakeOrderTrends->pluck('fake_order_count')),
                datasets: [{
                    label: 'Number of Customers',
                    data: @json($fakeOrderTrends->pluck('count')),
                    backgroundColor: 'rgba(234, 88, 12, 0.8)',
                    borderColor: orange,
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.06)' },
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        title: { display: true, text: 'Number of Fake Orders' }
                    }
                }
            }
        });
    }
})();
</script>
@endpush
