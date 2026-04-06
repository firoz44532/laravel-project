@extends('frontend.layout')

@section('title', 'Add New Address')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-6">My Account</h2>
                <nav class="space-y-2">
                    <a href="{{ route('account.dashboard') }}" 
                       class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('account.profile') }}" 
                       class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-user mr-2"></i>Profile
                    </a>
                    <a href="{{ route('account.addresses') }}" 
                       class="block px-4 py-2 rounded-lg bg-primary text-white">
                        <i class="fas fa-map-marker-alt mr-2"></i>Addresses
                    </a>
                    <a href="{{ route('account.orders') }}" 
                       class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-shopping-bag mr-2"></i>Orders
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 rounded-lg hover:bg-gray-100 text-left">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h1 class="text-2xl font-bold">Add New Address</h1>
                </div>
                
                <form method="POST" action="{{ route('account.addresses.store') }}" class="p-6">
                    @csrf
                    @if(!empty($from))
                        <input type="hidden" name="from" value="{{ $from }}">
                    @endif
                    
                    <!-- Address Type -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Address Type</h3>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="type" value="shipping" checked class="mr-2">
                                <span>Shipping Address</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="type" value="billing" class="mr-2">
                                <span>Billing Address</span>
                            </label>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="first_name" name="first_name" required
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="last_name" name="last_name" required
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" id="phone" name="phone" required
                                       placeholder="01XXXXXXXXX"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address
                                </label>
                                <input type="email" id="email" name="email"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Address Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="address_line_1" class="block text-sm font-medium text-gray-700 mb-2">
                                    Address Line 1 <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="address_line_1" name="address_line_1" required
                                       placeholder="House/Flat/Apartment Number, Street Name"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                @error('address_line_1')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="address_line_2" class="block text-sm font-medium text-gray-700 mb-2">
                                    Address Line 2
                                </label>
                                <input type="text" id="address_line_2" name="address_line_2"
                                       placeholder="Area, Landmark (Optional)"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                        City <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="city" name="city" required
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                    @error('city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                        Postal Code
                                    </label>
                                    <input type="text" id="postal_code" name="postal_code"
                                           placeholder="XXXX"
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="division" class="block text-sm font-medium text-gray-700 mb-2">
                                        Division <span class="text-red-500">*</span>
                                    </label>
                                    <select id="division" name="division" required
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                        <option value="">Select Division</option>
                                        <option value="Barishal">Barishal</option>
                                        <option value="Chattogram">Chattogram</option>
                                        <option value="Dhaka">Dhaka</option>
                                        <option value="Khulna">Khulna</option>
                                        <option value="Mymensingh">Mymensingh</option>
                                        <option value="Rajshahi">Rajshahi</option>
                                        <option value="Rangpur">Rangpur</option>
                                        <option value="Sylhet">Sylhet</option>
                                    </select>
                                    @error('division')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                        Country <span class="text-red-500">*</span>
                                    </label>
                                    <select id="country" name="country" required
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                        <option value="Bangladesh">Bangladesh</option>
                                    </select>
                                    @error('country')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Default Address -->
                    <div class="mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" id="is_default" name="is_default" class="mr-2">
                            <label for="is_default" class="text-sm font-medium text-gray-700">
                                Set as default {{ old('type', 'shipping') }} address
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">This will be used as your default address for faster checkout</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('account.addresses') }}" 
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-orange-600 transition">
                            Save Address
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
