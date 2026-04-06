@extends('frontend.layout')

@section('title', 'Create Support Ticket')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Create Support Ticket</h1>
            <p class="text-gray-600 mt-2">We're here to help. Describe your issue and we'll get back to you soon.</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form method="POST" action="{{ route('support.store') }}" class="p-6">
                @csrf
                
                <!-- Subject -->
                <div class="mb-6">
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="subject" 
                           name="subject" 
                           required
                           value="{{ old('subject') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Brief description of your issue">
                    @error('subject')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div class="mb-6">
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="category" 
                            name="category" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select a category</option>
                        <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General Inquiry</option>
                        <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Technical Issue</option>
                        <option value="billing" {{ old('category') == 'billing' ? 'selected' : '' }}>Billing & Payment</option>
                        <option value="shipping" {{ old('category') == 'shipping' ? 'selected' : '' }}>Shipping & Delivery</option>
                        <option value="product" {{ old('category') == 'product' ? 'selected' : '' }}>Product Related</option>
                        <option value="account" {{ old('category') == 'account' ? 'selected' : '' }}>Account Issue</option>
                    </select>
                    @error('category')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div class="mb-6">
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        Priority <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <label class="relative">
                            <input type="radio" name="priority" value="low" class="peer sr-only" {{ old('priority') == 'low' ? 'checked' : '' }}>
                            <div class="px-4 py-3 border-2 border-gray-200 rounded-lg cursor-pointer text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 transition duration-200">
                                <i class="fas fa-arrow-down text-green-500 mr-2"></i>Low
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="priority" value="medium" class="peer sr-only" {{ old('priority') == 'medium' ? 'checked' : '' }}>
                            <div class="px-4 py-3 border-2 border-gray-200 rounded-lg cursor-pointer text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 transition duration-200">
                                <i class="fas fa-minus text-blue-500 mr-2"></i>Medium
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="priority" value="high" class="peer sr-only" {{ old('priority') == 'high' ? 'checked' : '' }}>
                            <div class="px-4 py-3 border-2 border-gray-200 rounded-lg cursor-pointer text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 transition duration-200">
                                <i class="fas fa-arrow-up text-orange-500 mr-2"></i>High
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="priority" value="urgent" class="peer sr-only" {{ old('priority') == 'urgent' ? 'checked' : '' }}>
                            <div class="px-4 py-3 border-2 border-gray-200 rounded-lg cursor-pointer text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 transition duration-200">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Urgent
                            </div>
                        </label>
                    </div>
                    @error('priority')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" 
                              name="description" 
                              required
                              rows="6"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Please provide as much detail as possible about your issue...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('support.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Tickets
                    </a>
                    <div class="space-x-3">
                        <button type="reset" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition duration-200">
                            Clear
                        </button>
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Ticket
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Help Info -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Need immediate help?</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>For urgent issues, you can also reach us at:</p>
                        <ul class="mt-1 list-disc list-inside">
                            <li>Email: support@marketplace.com</li>
                            <li>Phone: +1 (555) 123-4567</li>
                            <li>Live Chat: Available 24/7</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
