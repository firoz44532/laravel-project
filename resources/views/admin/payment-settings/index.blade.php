@extends('admin.layout')

@section('title', 'Payment Settings')

@section('header', 'Payment Settings Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Payment Settings</h1>
                    <p class="text-sm text-gray-500 mt-1">Configure payment methods and gateway settings</p>
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

    <!-- Payment Methods List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Payment Methods</h2>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-2"></i>
                    Drag to reorder payment methods
                </div>
            </div>
        </div>

        <div class="p-6">
            <div id="payment-methods-list" class="space-y-4">
                @foreach($paymentSettings as $paymentSetting)
                    <div class="payment-method-item border rounded-lg p-4 hover:bg-gray-50 transition-colors" 
                         data-gateway="{{ $paymentSetting->gateway }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="cursor-move">
                                    <i class="fas fa-grip-vertical text-gray-400"></i>
                                </div>
                                
                                <div class="flex items-center space-x-3">
                                    @switch($paymentSetting->gateway)
                                        @case('bkash')
                                            <div class="w-10 h-10 bg-pink-500 rounded flex items-center justify-center">
                                                <span class="text-white font-bold">b</span>
                                            </div>
                                            @break
                                        @case('nagad')
                                            <div class="w-10 h-10 bg-orange-500 rounded flex items-center justify-center">
                                                <span class="text-white font-bold">N</span>
                                            </div>
                                            @break
                                        @case('rocket')
                                            <div class="w-10 h-10 bg-purple-500 rounded flex items-center justify-center">
                                                <span class="text-white font-bold">R</span>
                                            </div>
                                            @break
                                        @case('bank_transfer')
                                            <div class="w-10 h-10 bg-blue-600 rounded flex items-center justify-center">
                                                <i class="fas fa-university text-white"></i>
                                            </div>
                                            @break
                                        @case('cash_on_delivery')
                                            <div class="w-10 h-10 bg-green-500 rounded flex items-center justify-center">
                                                <i class="fas fa-money-bill-wave text-white"></i>
                                            </div>
                                            @break
                                        @default
                                            <div class="w-10 h-10 bg-gray-500 rounded flex items-center justify-center">
                                                <i class="fas fa-credit-card text-white"></i>
                                            </div>
                                    @endswitch

                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $paymentSetting->display_name }}</h3>
                                        <p class="text-sm text-gray-500">{{ ucfirst($paymentSetting->gateway) }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    @if($paymentSetting->is_active)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <button onclick="togglePaymentStatus('{{ $paymentSetting->gateway }}')" 
                                        class="toggle-status-btn px-3 py-1 rounded text-sm font-medium transition-colors
                                               {{ $paymentSetting->is_active 
                                                   ? 'bg-red-100 text-red-700 hover:bg-red-200' 
                                                   : 'bg-green-100 text-green-700 hover:bg-green-200' }}"
                                        data-gateway="{{ $paymentSetting->gateway }}">
                                    {{ $paymentSetting->is_active ? 'Disable' : 'Enable' }}
                                </button>

                                <a href="{{ route('admin.payment-settings.edit', $paymentSetting->gateway) }}" 
                                   class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm font-medium hover:bg-blue-200 transition-colors">
                                    <i class="fas fa-cog mr-1"></i> Configure
                                </a>
                            </div>
                        </div>

                        @if($paymentSetting->instructions)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-sm text-gray-600">
                                    <strong>Instructions:</strong> {{ Str::limit($paymentSetting->instructions, 100) }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sortable
    new Sortable(document.getElementById('payment-methods-list'), {
        handle: '.cursor-move',
        animation: 150,
        onEnd: function(evt) {
            updatePaymentOrder();
        }
    });
});

function togglePaymentStatus(gateway) {
    fetch(`/admin/payment-settings/${gateway}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating payment status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating payment status');
    });
}

function updatePaymentOrder() {
    const items = document.querySelectorAll('.payment-method-item');
    const gateways = Array.from(items).map(item => item.dataset.gateway);

    fetch('/admin/payment-settings/update-order', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            gateways: gateways
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Error updating order');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endsection
