@extends('admin.layout')

@section('title', 'Shipping Zones')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Shipping Zones</h1>
                <p class="text-gray-600 mt-2">Manage geographic zones and their shipping rates</p>
            </div>
            <a href="{{ route('admin.shipping.zones.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Zone
            </a>
        </div>
    </div>

    <!-- Zones Table -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">All Shipping Zones</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Default Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Express Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cities</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Methods</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($zones as $zone)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-map-marked-alt text-green-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $zone->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $zone->code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $zone->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ $zone->formatted_default_cost }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">{{ $zone->formatted_express_cost }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $zone->delivery_days }}</span>
                                @if($zone->express_days)
                                    <span class="text-xs text-gray-500"> (Express: {{ $zone->express_days }})</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($zone->cities)
                                        @php
                                            $citiesArray = is_array($zone->cities) ? $zone->cities : json_decode($zone->cities, true);
                                            $citiesCount = is_array($citiesArray) ? count($citiesArray) : 0;
                                        @endphp
                                        @if($citiesCount > 0)
                                            {{ $citiesCount }} cities
                                        @else
                                            <span class="text-gray-400">None specified</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">None specified</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($zone->activeShippingMethods as $method)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $method->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($zone->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.shipping.zones.edit', $zone) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.shipping.zones.destroy', $zone) }}" 
                                          onsubmit="return confirm('Are you sure you want to delete this shipping zone?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-map-marked-alt text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No shipping zones found</p>
                                    <a href="{{ route('admin.shipping.zones.create') }}" class="mt-4 text-blue-600 hover:text-blue-800">
                                        Create your first shipping zone
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Zone Coverage Map -->
    @if($zones->count() > 0)
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Zone Coverage Overview</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($zones as $zone)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-map-marked-alt text-green-600 text-sm"></i>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900">{{ $zone->name }}</h3>
                                </div>
                                @if($zone->is_active)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                            
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Standard:</span>
                                    <span class="font-medium">{{ $zone->formatted_default_cost }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Express:</span>
                                    <span class="font-medium">{{ $zone->formatted_express_cost }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Delivery:</span>
                                    <span class="font-medium">{{ $zone->delivery_days }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Methods:</span>
                                    <span class="font-medium">{{ $zone->activeShippingMethods->count() }}</span>
                                </div>
                            </div>
                            
                            @if($zone->cities)
                                @php
                                    $citiesArray = is_array($zone->cities) ? $zone->cities : json_decode($zone->cities, true);
                                @endphp
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <p class="text-xs text-gray-500 mb-2">Cities covered:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($citiesArray, 0, 5) as $city)
                                            <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                                {{ $city }}
                                            </span>
                                        @endforeach
                                        @if(is_array($citiesArray) && count($citiesArray) > 5)
                                            <span class="text-xs text-gray-500">+{{ count($citiesArray) - 5 }} more</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
