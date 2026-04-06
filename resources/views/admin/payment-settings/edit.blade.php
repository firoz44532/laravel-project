@extends('admin.layout')

@section('title', 'Edit Payment Settings - ' . $paymentSetting->display_name)

@section('header', 'Edit Payment Settings - ' . $paymentSetting->display_name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
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
                        <h1 class="text-xl font-semibold text-gray-900">{{ $paymentSetting->display_name }}</h1>
                        <p class="text-sm text-gray-500">{{ ucfirst($paymentSetting->gateway) }} Payment Method</p>
                    </div>
                </div>

                <a href="{{ route('admin.payment-settings.index') }}" 
                   class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>

        <form action="{{ route('admin.payment-settings.update', $paymentSetting->gateway) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <!-- Basic Settings -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Display Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="display_name" name="display_name" required
                               value="{{ $paymentSetting->display_name }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="flex items-center space-x-3 mt-3">
                            <input type="hidden" name="is_active" value="0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ $paymentSetting->is_active ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                                <span class="ml-3 text-sm font-medium text-gray-700">
                                    {{ $paymentSetting->is_active ? 'Enabled' : 'Disabled' }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                            Sort Order
                        </label>
                        <input type="number" id="sort_order" name="sort_order"
                               value="{{ $paymentSetting->sort_order }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                </div>
            </div>

            <!-- Gateway Specific Settings -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Gateway Settings</h2>
                
                @switch($paymentSetting->gateway)
                    @case('bkash')
                    @case('nagad')
                    @case('rocket')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="merchant_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Merchant Number <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" id="merchant_number" name="merchant_number"
                                       value="{{ $paymentSetting->getSetting('merchant_number') }}"
                                       placeholder="01xxxxxxxxx"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label for="account_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Name
                                </label>
                                <input type="text" id="account_name" name="account_name"
                                       value="{{ $paymentSetting->getSetting('account_name') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label for="transaction_fee" class="block text-sm font-medium text-gray-700 mb-2">
                                    Transaction Fee (৳)
                                </label>
                                <input type="number" id="transaction_fee" name="transaction_fee" step="0.01"
                                       value="{{ $paymentSetting->getSetting('transaction_fee', 0) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label for="min_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Minimum Amount (৳)
                                </label>
                                <input type="number" id="min_amount" name="min_amount"
                                       value="{{ $paymentSetting->getSetting('min_amount', 0) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label for="max_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Maximum Amount (৳)
                                </label>
                                <input type="number" id="max_amount" name="max_amount"
                                       value="{{ $paymentSetting->getSetting('max_amount', 50000) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>
                        @break

                    @case('bank_transfer')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bank Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="bank_name" name="bank_name"
                                       value="{{ $paymentSetting->getSetting('bank_name') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label for="account_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="account_name" name="account_name"
                                       value="{{ $paymentSetting->getSetting('account_name') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label for="account_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="account_number" name="account_number"
                                       value="{{ $paymentSetting->getSetting('account_number') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label for="branch_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Branch Name
                                </label>
                                <input type="text" id="branch_name" name="branch_name"
                                       value="{{ $paymentSetting->getSetting('branch_name') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label for="routing_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Routing Number
                                </label>
                                <input type="text" id="routing_number" name="routing_number"
                                       value="{{ $paymentSetting->getSetting('routing_number') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label for="swift_code" class="block text-sm font-medium text-gray-700 mb-2">
                                    SWIFT Code
                                </label>
                                <input type="text" id="swift_code" name="swift_code"
                                       value="{{ $paymentSetting->getSetting('swift_code') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label for="transaction_fee" class="block text-sm font-medium text-gray-700 mb-2">
                                    Transaction Fee (৳)
                                </label>
                                <input type="number" id="transaction_fee" name="transaction_fee" step="0.01"
                                       value="{{ $paymentSetting->getSetting('transaction_fee', 0) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label for="min_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Minimum Amount (৳)
                                </label>
                                <input type="number" id="min_amount" name="min_amount"
                                       value="{{ $paymentSetting->getSetting('min_amount', 0) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label for="max_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Maximum Amount (৳)
                                </label>
                                <input type="number" id="max_amount" name="max_amount"
                                       value="{{ $paymentSetting->getSetting('max_amount', 100000) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>
                        @break

                    @case('cash_on_delivery')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="delivery_fee" class="block text-sm font-medium text-gray-700 mb-2">
                                    Delivery Fee (৳)
                                </label>
                                <input type="number" id="delivery_fee" name="delivery_fee" step="0.01"
                                       value="{{ $paymentSetting->getSetting('delivery_fee', 50) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label for="min_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Minimum Amount (৳)
                                </label>
                                <input type="number" id="min_amount" name="min_amount"
                                       value="{{ $paymentSetting->getSetting('min_amount', 0) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>

                            <div>
                                <label for="max_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Maximum Amount (৳)
                                </label>
                                <input type="number" id="max_amount" name="max_amount"
                                       value="{{ $paymentSetting->getSetting('max_amount', 20000) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>
                        @break
                @endswitch
            </div>

            <!-- Payment Instructions -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Payment Instructions</h2>
                <div>
                    <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">
                        Customer Instructions
                    </label>
                    <textarea id="instructions" name="instructions" rows="6"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                              placeholder="Enter step-by-step instructions for customers...">{{ $paymentSetting->instructions }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">
                        These instructions will be shown to customers during checkout
                    </p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.payment-settings.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    <i class="fas fa-save mr-2"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
