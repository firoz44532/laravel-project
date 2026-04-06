@extends('admin.layout')

@section('title', 'Edit Coupon')
@section('header', 'Edit Coupon')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h1 class="text-xl font-semibold">Edit Coupon</h1>
        </div>
        
        <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Coupon Information -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Coupon Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Coupon Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="code" name="code" required
                               value="{{ $coupon->code }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary uppercase"
                               placeholder="SAVE20">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Coupon Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required
                               value="{{ $coupon->name }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Coupon Description
                        </label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">{{ $coupon->description }}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Coupon Type -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Coupon Type</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Discount Type <span class="text-red-500">*</span>
                        </label>
                        <select id="type" name="type" required
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                            <option value="fixed" {{ $coupon->type === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            <option value="percentage" {{ $coupon->type === 'percentage' ? 'selected' : '' }}>Percentage</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                            Discount Value <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="value" name="value" required
                                   value="{{ $coupon->value }}"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary"
                                   placeholder="0.00">
                            <span id="value-display" class="absolute right-3 top-2 text-sm text-gray-500">৳</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            @if($coupon->type === 'percentage')
                                {{ $coupon->value }}% discount
                            @else
                                Fixed amount discount
                            @endif
                        </p>
                        @error('value')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Usage Limits -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Usage Limits</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-2">
                            Usage Limit
                        </label>
                        <input type="number" id="usage_limit" name="usage_limit" min="1"
                               value="{{ $coupon->usage_limit }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        <p class="text-xs text-gray-500 mt-1">Maximum number of times coupon can be used</p>
                        @error('usage_limit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="usage_limit_per_user" class="block text-sm font-medium text-gray-700 mb-2">
                            Per User Limit
                        </label>
                        <input type="number" id="usage_limit_per_user" name="usage_limit_per_user" min="1"
                               value="{{ $coupon->usage_limit_per_user }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        <p class="text-xs text-gray-500 mt-1">Maximum times a single user can use this coupon</p>
                        @error('usage_limit_per_user')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Validity Period -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Validity Period</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date
                        </label>
                        <input type="date" id="starts_at" name="starts_at"
                               value="{{ $coupon->starts_at ? $coupon->starts_at->format('Y-m-d') : '' }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        @error('starts_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">
                            Expiry Date
                        </label>
                        <input type="date" id="expires_at" name="expires_at"
                               value="{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '' }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        @error('expires_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Options -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Options</h2>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               {{ $coupon->is_active ? 'checked' : '' }}
                               class="mr-2">
                        <label for="is_active" class="text-sm font-medium text-gray-700">
                            Active (Coupon will be available for use)
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               {{ $coupon->is_active ? 'checked' : '' }}
                               class="mr-2">
                        <label for="is_active" class="text-sm font-medium text-gray-700">
                            Active (Coupon will be available for use)
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.coupons.index') }}" 
                   class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                    Update Coupon
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('type').addEventListener('change', function() {
    const valueDisplay = document.getElementById('value-display');
    const valueInput = document.getElementById('value');
    
    if (this.value === 'percentage') {
        valueDisplay.textContent = valueInput.value + '%';
    } else {
        valueDisplay.textContent = '৳' + valueInput.value;
    }
});

document.getElementById('value').addEventListener('input', function() {
    const valueDisplay = document.getElementById('value-display');
    const valueInput = document.getElementById('value');
    
    if (this.value === 'percentage') {
        valueDisplay.textContent = this.value + '%';
    } else {
        valueDisplay.textContent = '৳' + this.value;
    }
});
</script>
@endsection
