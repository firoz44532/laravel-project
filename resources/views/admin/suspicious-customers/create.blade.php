@extends('admin.layout')

@section('title', 'Add Suspicious Customer')
@section('header', 'Add Suspicious Customer')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Add New Suspicious Customer</h3>
                <a href="{{ route('admin.suspicious-customers.index') }}" 
                   class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.suspicious-customers.store') }}" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <input type="email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="customer@example.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" name="name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="John Doe">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" name="phone"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="+8801234567890">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Flagging *</label>
                    <textarea name="reason" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Describe why this customer is being flagged as suspicious..."></textarea>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Initial Risk Score (0-100)</label>
                    <input type="number" name="risk_score" min="0" max="100" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="50">
                    <p class="mt-1 text-xs text-gray-500">0-30: Low risk, 31-69: Medium risk, 70-100: High risk</p>
                    @error('risk_score')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Detection Method</label>
                    <select name="detection_method"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="manual">Manual</option>
                        <option value="auto">Automatic</option>
                        <option value="report">Customer Report</option>
                        <option value="investigation">Manual Investigation</option>
                    </select>
                    @error('detection_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Risk Factors</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="risk_factors[]" value="temporary_email" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Temporary/Disposable Email</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="risk_factors[]" value="fake_order" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Fake Order History</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="risk_factors[]" value="high_cancellation_rate" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">High Cancellation Rate</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="risk_factors[]" value="suspicious_phone" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Suspicious Phone Number</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="risk_factors[]" value="multiple_accounts" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Multiple Accounts</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="risk_factors[]" value="rapid_ordering" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Rapid Order Pattern</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="risk_factors[]" value="suspicious_name" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Suspicious Name Pattern</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="risk_factors[]" value="payment_fraud" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Payment Fraud Indicators</span>
                        </label>
                    </div>
                    @error('risk_factors')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.suspicious-customers.index') }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Suspicious Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
