@extends('admin.layout')

@section('title', 'Shipping Settings')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-lg p-6 mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">
                    <i class="fas fa-shipping-fast mr-3"></i>Shipping Settings
                </h1>
                <p class="text-blue-100">Manage shipping zones, methods, and tax configuration</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3 mr-4">
                    <i class="fas fa-map-marked-alt text-blue-600 text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Shipping Zones</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $zones->count() }}</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-3 mr-4">
                    <i class="fas fa-truck text-green-600 text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Shipping Methods</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $methods->count() }}</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-yellow-100 rounded-full p-3 mr-4">
                    <i class="fas fa-percentage text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">VAT Rate</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $settings['vat_rate'] ?? 15 }}%</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-full p-3 mr-4">
                    <i class="fas fa-gift text-purple-600 text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Free Shipping</div>
                    <div class="text-2xl font-bold text-gray-800">৳{{ number_format($settings['free_shipping_threshold'] ?? 2000, 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="{{ route('admin.shipping.zones') }}" class="py-4 px-6 border-b-2 border-blue-500 text-blue-600 font-medium">
                    <i class="fas fa-map-marked-alt mr-2"></i>Shipping Zones
                </a>
                <a href="{{ route('admin.shipping.methods') }}" class="py-4 px-6 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium">
                    <i class="fas fa-truck mr-2"></i>Shipping Methods
                </a>
                <a href="{{ route('admin.shipping.settings') }}" class="py-4 px-6 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium">
                    <i class="fas fa-cog mr-2"></i>General Settings
                </a>
            </nav>
        </div>
    </div>

    <!-- Recent Zones -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Shipping Zones</h2>
            </div>
            <div class="p-6">
                @if($zones->count() > 0)
                    <div class="space-y-4">
                        @foreach($zones->take(5) as $zone)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $zone->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $zone->description }}</p>
                                    <div class="flex items-center mt-2">
                                        <span class="text-sm text-gray-600">Default: {{ $zone->formatted_default_cost }}</span>
                                        <span class="mx-2 text-gray-400">|</span>
                                        <span class="text-sm text-gray-600">Express: {{ $zone->formatted_express_cost }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($zone->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-map-marked-alt text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No shipping zones configured</p>
                    </div>
                @endif
                
                <div class="mt-6">
                    <a href="{{ route('admin.shipping.zones') }}" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-center block">
                        Manage All Zones
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Methods -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Shipping Methods</h2>
            </div>
            <div class="p-6">
                @if($methods->count() > 0)
                    <div class="space-y-4">
                        @foreach($methods->take(5) as $method)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $method->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $method->description }}</p>
                                    <div class="flex items-center mt-2">
                                        <span class="text-sm text-gray-600">{{ $method->estimated_days }}</span>
                                        @if($method->tracking_available)
                                            <span class="ml-3 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-map-marker-alt mr-1"></i>Tracking
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($method->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-truck text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No shipping methods configured</p>
                    </div>
                @endif
                
                <div class="mt-6">
                    <a href="{{ route('admin.shipping.methods') }}" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-center block">
                        Manage All Methods
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Quick Actions</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('admin.shipping.zones.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="bg-blue-100 rounded-lg p-3 mr-4">
                        <i class="fas fa-plus text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Add Zone</h3>
                        <p class="text-sm text-gray-500">Create new shipping zone</p>
                    </div>
                </a>
                
                <a href="{{ route('admin.shipping.methods.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="bg-green-100 rounded-lg p-3 mr-4">
                        <i class="fas fa-plus text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Add Method</h3>
                        <p class="text-sm text-gray-500">Create new shipping method</p>
                    </div>
                </a>
                
                <a href="{{ route('admin.shipping.settings') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="bg-purple-100 rounded-lg p-3 mr-4">
                        <i class="fas fa-sliders-h text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Configure</h3>
                        <p class="text-sm text-gray-500">Tax and general settings</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
