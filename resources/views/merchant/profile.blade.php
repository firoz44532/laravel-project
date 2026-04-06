@extends('layouts.merchant')

@section('title', 'Merchant Profile')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            @if(isset($merchant))
                Store Profile
            @else
                Become a Seller
            @endif
        </h1>
        <p class="mt-2 text-gray-600">
            @if(isset($merchant))
                Manage your store information and payment details
            @else
                Start selling on our platform and reach thousands of customers
            @endif
        </p>
    </div>

    <!-- Status Alert -->
    @if(isset($merchant))
        @if($merchant->status === 'pending')
            <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-start">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-yellow-800">Application Pending Review</h3>
                    <p class="text-sm text-yellow-700 mt-1">Your merchant application is pending approval. We'll review it within 24-48 hours.</p>
                </div>
            </div>
        @elseif($merchant->status === 'rejected')
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 flex items-start">
                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-red-800">Application Rejected</h3>
                    <p class="text-sm text-red-700 mt-1">Your application was rejected: {{ $merchant->rejection_reason }}</p>
                    <p class="text-sm text-red-700 mt-1">You can update your information below and reapply.</p>
                </div>
            </div>
        @elseif($merchant->status === 'approved')
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-start">
                <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-green-800">Account Approved</h3>
                    <p class="text-sm text-green-700 mt-1">Your merchant account is approved! You can start adding products.</p>
                </div>
            </div>
        @endif
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm rounded-lg border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        @if(isset($merchant))
                            Edit Store Information
                        @else
                            Store Registration
                        @endif
                    </h2>
                </div>
                <div class="p-6">
                    <form action="{{ isset($merchant) ? route('merchant.profile.update') : route('merchant.profile.store') }}" 
                          method="{{ isset($merchant) ? 'PUT' : 'POST' }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Store Information -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-base font-medium text-gray-900 mb-4">Store Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="store_name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Store Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="store_name" name="store_name" 
                                               value="{{ old('store_name', $merchant->store_name ?? '') }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                               required>
                                        @error('store_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="store_email" class="block text-sm font-medium text-gray-700 mb-2">
                                            Store Email
                                        </label>
                                        <input type="email" id="store_email" name="store_email" 
                                               value="{{ old('store_email', $merchant->store_email ?? '') }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('store_email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <label for="store_description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Store Description <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="store_description" name="store_description" rows="4" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                              required>{{ old('store_description', $merchant->store_description ?? '') }}</textarea>
                                    @error('store_description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                    <div>
                                        <label for="store_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                            Store Phone
                                        </label>
                                        <input type="tel" id="store_phone" name="store_phone" 
                                               value="{{ old('store_phone', $merchant->store_phone ?? '') }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('store_phone')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="store_city" class="block text-sm font-medium text-gray-700 mb-2">
                                            City
                                        </label>
                                        <input type="text" id="store_city" name="store_city" 
                                               value="{{ old('store_city', $merchant->store_city ?? '') }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('store_city')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <label for="store_address" class="block text-sm font-medium text-gray-700 mb-2">
                                        Store Address
                                    </label>
                                    <textarea id="store_address" name="store_address" rows="2" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('store_address', $merchant->store_address ?? '') }}</textarea>
                                    @error('store_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="mt-6">
                                    <label for="store_country" class="block text-sm font-medium text-gray-700 mb-2">
                                        Country
                                    </label>
                                    <input type="text" id="store_country" name="store_country" 
                                           value="{{ old('store_country', $merchant->store_country ?? 'Bangladesh') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('store_country')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            
                            <!-- Payment Details Section -->
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-base font-medium text-gray-900 mb-4">
                                    <svg class="w-5 h-5 inline mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    Payment Details
                                </h3>
                                <p class="text-sm text-gray-600 mb-4">Add your payment information to receive earnings from sales</p>
                                
                                @if(isset($merchant) && $merchant->payment_details)
                                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                        <h4 class="text-sm font-medium text-gray-900 mb-3">Current Payment Information</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <p class="text-gray-600">Payment Method:</p>
                                                <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $merchant->payment_details['method'])) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600">Account Name:</p>
                                                <p class="font-medium">{{ $merchant->payment_details['account_name'] }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-600">Account Number:</p>
                                                <p class="font-medium">{{ $merchant->payment_details['account_number'] }}</p>
                                            </div>
                                            @if($merchant->payment_details['bank_name'])
                                            <div>
                                                <p class="text-gray-600">Bank Name:</p>
                                                <p class="font-medium">{{ $merchant->payment_details['bank_name'] }}</p>
                                            </div>
                                            @endif
                                            @if($merchant->payment_details['branch_name'])
                                            <div>
                                                <p class="text-gray-600">Branch Name:</p>
                                                <p class="font-medium">{{ $merchant->payment_details['branch_name'] }}</p>
                                            </div>
                                            @endif
                                            @if($merchant->payment_details['routing_number'])
                                            <div>
                                                <p class="text-gray-600">Routing Number:</p>
                                                <p class="font-medium">{{ $merchant->payment_details['routing_number'] }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                                            Payment Method
                                        </label>
                                        <select id="payment_method" name="payment_method" 
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Select Payment Method</option>
                                            <option value="bank_transfer" {{ (old('payment_method', $merchant->payment_details['method'] ?? '') == 'bank_transfer') ? 'selected' : '' }}>
                                                Bank Transfer
                                            </option>
                                            <option value="mobile_banking" {{ (old('payment_method', $merchant->payment_details['method'] ?? '') == 'mobile_banking') ? 'selected' : '' }}>
                                                Mobile Banking
                                            </option>
                                            <option value="bkash" {{ (old('payment_method', $merchant->payment_details['method'] ?? '') == 'bkash') ? 'selected' : '' }}>
                                                bKash
                                            </option>
                                            <option value="nagad" {{ (old('payment_method', $merchant->payment_details['method'] ?? '') == 'nagad') ? 'selected' : '' }}>
                                                Nagad
                                            </option>
                                            <option value="rocket" {{ (old('payment_method', $merchant->payment_details['method'] ?? '') == 'rocket') ? 'selected' : '' }}>
                                                Rocket
                                            </option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="account_name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Account Holder Name
                                        </label>
                                        <input type="text" id="account_name" name="account_name" 
                                               value="{{ old('account_name', $merchant->payment_details['account_name'] ?? '') }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    
                                    <div>
                                        <label for="account_number" class="block text-sm font-medium text-gray-700 mb-2">
                                            Account Number / Mobile Number
                                        </label>
                                        <input type="text" id="account_number" name="account_number" 
                                               value="{{ old('account_number', $merchant->payment_details['account_number'] ?? '') }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    
                                    <div>
                                        <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Bank Name (if applicable)
                                        </label>
                                        <input type="text" id="bank_name" name="bank_name" 
                                               value="{{ old('bank_name', $merchant->payment_details['bank_name'] ?? '') }}" 
                                               placeholder="e.g., Dutch-Bangla Bank"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    
                                    <div>
                                        <label for="branch_name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Branch Name (if applicable)
                                        </label>
                                        <input type="text" id="branch_name" name="branch_name" 
                                               value="{{ old('branch_name', $merchant->payment_details['branch_name'] ?? '') }}" 
                                               placeholder="e.g., Dhanmondi Branch"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    
                                    <div>
                                        <label for="routing_number" class="block text-sm font-medium text-gray-700 mb-2">
                                            Routing Number (if applicable)
                                        </label>
                                        <input type="text" id="routing_number" name="routing_number" 
                                               value="{{ old('routing_number', $merchant->payment_details['routing_number'] ?? '') }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                            <a href="{{ route('merchant.dashboard') }}" 
                               class="px-4 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @if(isset($merchant))
                                    Update Profile
                                @else
                                    Submit Application
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            @if(isset($merchant))
                <!-- Store Status Card -->
                <div class="bg-white shadow-sm rounded-lg border mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Store Status</h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center mb-4">
                            {!! $merchant->status_badge !!}
                        </div>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Store Name:</span>
                                <span class="font-medium text-gray-900">{{ $merchant->store_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Store Slug:</span>
                                <span class="font-medium text-gray-900 text-xs">{{ $merchant->store_slug }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Commission Rate:</span>
                                <span class="font-medium text-gray-900">{{ $merchant->commission_rate }}%</span>
                            </div>
                            @if($merchant->approved_at)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Approved:</span>
                                <span class="font-medium text-gray-900">{{ $merchant->approved_at->format('M d, Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-white shadow-sm rounded-lg border">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @if($merchant->isApproved())
                                <a href="{{ route('merchant.products.create') }}" 
                                   class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    Add New Product
                                </a>
                                <a href="{{ route('merchant.products.index') }}" 
                                   class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    View Products
                                </a>
                                <a href="{{ route('merchant.orders.index') }}" 
                                   class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    View Orders
                                </a>
                            @else
                                <div class="text-center text-gray-500 text-sm">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p>Complete profile setup and wait for approval to start selling</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Why Sell With Us Card -->
                <div class="bg-white shadow-sm rounded-lg border">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Why Sell With Us?</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-gray-700">Reach thousands of customers</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-gray-700">Easy product management</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-gray-700">Secure payment processing</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-gray-700">Real-time sales analytics</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-gray-700">Marketing support</span>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
