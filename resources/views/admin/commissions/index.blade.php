@extends('admin.layout')

@section('title', 'Commission Settings')

@section('header', 'Commission Settings')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Commission Settings</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage commission rates and payout configurations</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.commissions.reports') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chart-line mr-2"></i> Reports
                    </a>
                    <a href="{{ route('admin.commissions.payouts') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-money-bill-wave mr-2"></i> Payouts
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Commission Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                    <i class="fas fa-dollar-sign text-orange-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Commission Earned</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($stats['total_commission_earned'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending Payouts</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($stats['pending_payouts'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Paid</p>
                    <p class="text-2xl font-semibold text-gray-900">${{ number_format($stats['total_paid'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Partners</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['merchant_count'] + $stats['affiliate_count'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Settings Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Commission Configuration</h2>
            <p class="text-sm text-gray-500 mt-1">Configure commission rates and payment settings</p>
        </div>

        <form action="{{ route('admin.commissions.updateSettings') }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-6">
                @foreach($commissionSettings as $setting)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <label for="setting-{{ $setting->id }}" class="text-sm font-medium text-gray-900">
                                    {{ $setting->title }}
                                </label>
                                @if($setting->is_public)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-globe mr-1"></i> Public
                                    </span>
                                @endif
                            </div>
                            @if($setting->description)
                                <p class="text-sm text-gray-500">{{ $setting->description }}</p>
                            @endif
                        </div>
                        
                        <div class="space-y-2">
                            @if($setting->type == 'boolean')
                                <div class="flex items-center space-x-3">
                                    <input type="hidden" name="settings[{{ $setting->id }}][value]" value="0">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               id="setting-{{ $setting->id }}" 
                                               name="settings[{{ $setting->id }}][value]" 
                                               value="1"
                                               {{ $setting->getValue() ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-700">
                                            {{ $setting->getValue() ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </label>
                                </div>
                            @elseif($setting->type == 'number')
                                <div class="flex items-center space-x-2">
                                    <input type="number" 
                                           id="setting-{{ $setting->id }}" 
                                           name="settings[{{ $setting->id }}][value]" 
                                           value="{{ $setting->getValue() }}"
                                           step="0.01"
                                           min="0"
                                           max="100"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                                    @if(str_contains($setting->key, 'percentage'))
                                        <span class="text-sm text-gray-500">%</span>
                                    @else
                                        <span class="text-sm text-gray-500">$</span>
                                    @endif
                                </div>
                            @else
                                <input type="text" 
                                       id="setting-{{ $setting->id }}" 
                                       name="settings[{{ $setting->id }}][value]" 
                                       value="{{ $setting->value }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                            @endif
                            
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">
                                    Key: <code class="bg-gray-100 px-1 py-0.5 rounded">{{ $setting->key }}</code>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    @if(!$loop->last)
                        <hr class="my-6 border-gray-200">
                    @endif
                @endforeach
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-2"></i>
                        Changes will be applied immediately after saving
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.commissions.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition-colors">
                            <i class="fas fa-save mr-2"></i> Save Commission Settings
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
