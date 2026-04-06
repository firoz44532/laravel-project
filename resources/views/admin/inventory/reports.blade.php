@extends('admin.layout')

@section('title', 'Inventory Reports')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Inventory Reports</h1>
                    <p class="text-sm text-gray-500 mt-1">Comprehensive inventory analytics</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="exportReports()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fas fa-download mr-2"></i>Export Reports
                    </button>
                    <a href="{{ route('admin.inventory.dashboard') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-chart-line mr-2"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($reports['inventory_valuation']['total_products']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-cubes text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Quantity</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($reports['inventory_valuation']['total_quantity']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Value</p>
                    <p class="text-2xl font-bold text-gray-900">৳{{ number_format($reports['inventory_valuation']['total_value'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-chart-bar text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg Value</p>
                    <p class="text-2xl font-bold text-gray-900">৳{{ number_format($reports['inventory_valuation']['average_value_per_product'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Stock Movement Report -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Stock Movement (Last 30 Days)</h2>
                <p class="text-sm text-gray-500 mt-1">Summary of all stock movements</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($reports['stock_movements'] as $movement)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                @switch($movement->action)
                                    @case('stock_in')
                                        <div class="p-2 bg-green-100 rounded-full mr-3">
                                            <i class="fas fa-arrow-down text-green-600 text-sm"></i>
                                        </div>
                                        <span class="font-medium text-green-700">Stock In</span>
                                        @break
                                    @case('stock_out')
                                        <div class="p-2 bg-red-100 rounded-full mr-3">
                                            <i class="fas fa-arrow-up text-red-600 text-sm"></i>
                                        </div>
                                        <span class="font-medium text-red-700">Stock Out</span>
                                        @break
                                    @default
                                        <div class="p-2 bg-blue-100 rounded-full mr-3">
                                            <i class="fas fa-exchange-alt text-blue-600 text-sm"></i>
                                        </div>
                                        <span class="font-medium text-blue-700">{{ ucfirst($movement->action) }}</span>
                                @endswitch
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ number_format($movement->count) }} transactions</p>
                                <p class="text-xs text-gray-500">{{ number_format($movement->total_quantity) }} units</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Low Stock Analysis -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Low Stock Analysis</h2>
                <p class="text-sm text-gray-500 mt-1">Products requiring attention</p>
            </div>
            <div class="p-6">
                @if($reports['low_stock_analysis']->count() > 0)
                    <div class="space-y-4">
                        @foreach($reports['low_stock_analysis']->take(5) as $product)
                            <div class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $product->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $product->category->name ?? 'No Category' }}</p>
                                    <div class="flex items-center mt-2 space-x-4">
                                        <span class="text-sm font-medium text-gray-900">
                                            Stock: {{ $product->stock_quantity }}
                                        </span>
                                        <span class="text-sm text-yellow-600">
                                            Threshold: {{ $product->stockAlert->threshold_quantity }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition">
                                        Update
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($reports['low_stock_analysis']->count() > 5)
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-500">... and {{ $reports['low_stock_analysis']->count() - 5 }} more products</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>
                        <p class="text-gray-500">No low stock products</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Detailed Reports -->
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Inventory Valuation Details</h2>
                <p class="text-sm text-gray-500 mt-1">Complete inventory breakdown</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ number_format($reports['inventory_valuation']['total_products']) }}</div>
                        <div class="text-sm text-gray-500 mt-1">Total Products</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">{{ number_format($reports['inventory_valuation']['total_quantity']) }}</div>
                        <div class="text-sm text-gray-500 mt-1">Total Units</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600">৳{{ number_format($reports['inventory_valuation']['total_value'], 2) }}</div>
                        <div class="text-sm text-gray-500 mt-1">Total Value</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-600">৳{{ number_format($reports['inventory_valuation']['average_value_per_product'], 2) }}</div>
                        <div class="text-sm text-gray-500 mt-1">Avg Value/Product</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportReports() {
    // Create CSV content
    let csvContent = "data:text/csv;charset=utf-8,";
    
    // Add header
    csvContent += "Inventory Report Summary\n\n";
    csvContent += "Metric,Value\n";
    csvContent += `Total Products,{{ $reports['inventory_valuation']['total_products'] }}\n`;
    csvContent += `Total Quantity,{{ $reports['inventory_valuation']['total_quantity'] }}\n`;
    csvContent += `Total Value,{{ $reports['inventory_valuation']['total_value'] }}\n`;
    csvContent += `Average Value per Product,{{ $reports['inventory_valuation']['average_value_per_product'] }}\n\n`;
    
    // Add stock movements
    csvContent += "Stock Movements (Last 30 Days)\n";
    csvContent += "Action,Transactions,Total Units\n";
    @foreach($reports['stock_movements'] as $movement)
        csvContent += "{{ ucfirst($movement->action) }},{{ $movement->count }},{{ $movement->total_quantity }}\n";
    @endforeach
    
    // Create download link
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `inventory_report_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection
