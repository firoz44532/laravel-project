@extends('frontend.layout')

@section('title', 'My Profile')

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
                       class="block px-4 py-2 rounded-lg bg-primary text-white">
                        <i class="fas fa-user mr-2"></i>Profile
                    </a>
                    <a href="{{ route('account.addresses') }}" 
                       class="block px-4 py-2 rounded-lg hover:bg-gray-100">
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
                    <h1 class="text-2xl font-bold">Profile Information</h1>
                </div>
                
                <form method="POST" action="{{ route('account.profile.update') }}" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Personal Information</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        First Name
                                    </label>
                                    <input type="text" id="first_name" name="first_name" 
                                           value="{{ $user->first_name ?? '' }}"
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                </div>
                                
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Last Name
                                    </label>
                                    <input type="text" id="last_name" name="last_name" 
                                           value="{{ $user->last_name ?? '' }}"
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Address
                                    </label>
                                    <input type="email" id="email" name="email" 
                                           value="{{ $user->email }}"
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary"
                                           readonly>
                                    <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                                </div>
                                
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone Number
                                    </label>
                                    <input type="tel" id="phone" name="phone" 
                                           value="{{ $user->phone ?? '' }}"
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Account Settings -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Account Settings</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Display Name
                                    </label>
                                    <input type="text" id="name" name="name" 
                                           value="{{ $user->name }}"
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" id="is_active" name="is_active" 
                                           {{ $user->is_active ? 'checked' : '' }}
                                           class="mr-2">
                                    <label for="is_active" class="text-sm font-medium text-gray-700">
                                        Account Active
                                    </label>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" id="email_verified_at" name="email_verified_at" 
                                           {{ $user->email_verified_at ? 'checked' : '' }}
                                           class="mr-2" disabled>
                                    <label for="email_verified_at" class="text-sm font-medium text-gray-700">
                                        Email Verified
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">Email verification status</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password Change Section -->
                    <div class="border-t pt-6 mt-6">
                        <h3 class="text-lg font-semibold mb-4">Change Password</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Current Password
                                </label>
                                <input type="password" id="current_password" name="current_password" 
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-700 mb-2">
                                    New Password
                                </label>
                                <input type="password" id="password" name="password" 
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirm New Password
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation" 
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-4 mt-8">
                            <a href="{{ route('account.dashboard') }}" 
                               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-orange-600 transition">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
