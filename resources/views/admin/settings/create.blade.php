@extends('admin.layout')

@section('title', 'Create Setting')

@section('header', 'Create New Setting')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Create New Setting</h1>
                    <p class="text-sm text-gray-500 mt-1">Add a new configuration setting to your store</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.settings.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('admin.settings.store') }}" method="POST" class="p-6">
            @csrf
            
            <!-- Basic Information -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-orange-500"></i>
                    Basic Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="key" class="block text-sm font-medium text-gray-700">
                            Setting Key <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="key" 
                               name="key" 
                               value="{{ old('key') }}"
                               placeholder="e.g., site_name, contact_email"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('key') border-red-500 @enderror"
                               required>
                        @error('key')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500">Unique identifier for this setting (lowercase, underscores allowed)</p>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="title" class="block text-sm font-medium text-gray-700">
                            Display Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}"
                               placeholder="e.g., Site Name, Contact Email"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('title') border-red-500 @enderror"
                               required>
                        @error('title')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500">User-friendly name that will be displayed in the admin panel</p>
                    </div>
                </div>
            </div>

            <!-- Configuration -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-cog mr-2 text-orange-500"></i>
                    Configuration
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label for="group" class="block text-sm font-medium text-gray-700">
                            Group <span class="text-red-500">*</span>
                        </label>
                        <select id="group" 
                                name="group" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('group') border-red-500 @enderror"
                                required>
                            <option value="">Select Group</option>
                            @foreach($groups as $group)
                                <option value="{{ $group }}" {{ old('group') == $group ? 'selected' : '' }}>
                                    {{ ucfirst($group) }}
                                </option>
                            @endforeach
                            <option value="general" {{ old('group') == 'general' ? 'selected' : '' }}>
                                General
                            </option>
                            <option value="site" {{ old('group') == 'site' ? 'selected' : '' }}>
                                Site
                            </option>
                            <option value="payment" {{ old('group') == 'payment' ? 'selected' : '' }}>
                                Payment
                            </option>
                            <option value="shipping" {{ old('group') == 'shipping' ? 'selected' : '' }}>
                                Shipping
                            </option>
                        </select>
                        @error('group')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500">Group for organizing related settings</p>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="type" class="block text-sm font-medium text-gray-700">
                            Data Type <span class="text-red-500">*</span>
                        </label>
                        <select id="type" 
                                name="type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('type') border-red-500 @enderror"
                                required>
                            <option value="">Select Type</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500">Type of data this setting will store</p>
                    </div>

                    <div class="space-y-2">
                        <label for="sort_order" class="block text-sm font-medium text-gray-700">
                            Sort Order
                        </label>
                        <input type="number" 
                               id="sort_order" 
                               name="sort_order" 
                               value="{{ old('sort_order', 0) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('sort_order') border-red-500 @enderror"
                               min="0">
                        @error('sort_order')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500">Order in which this setting appears (0 = first)</p>
                    </div>
                </div>
            </div>

            <!-- Value & Description -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-edit mr-2 text-orange-500"></i>
                    Content
                </h2>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label for="value" class="block text-sm font-medium text-gray-700">
                            Default Value
                        </label>
                        <input type="text" 
                               id="value" 
                               name="value" 
                               value="{{ old('value') }}"
                               placeholder="Enter default value"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('value') border-red-500 @enderror">
                        @error('value')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500">Initial value for this setting (can be changed later)</p>
                    </div>

                    <div class="space-y-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  placeholder="Describe what this setting does and how it's used"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500">Help text that explains the purpose of this setting</p>
                    </div>
                </div>
            </div>

            <!-- Visibility Options -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-eye mr-2 text-orange-500"></i>
                    Visibility Options
                </h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" 
                               id="is_public" 
                               name="is_public" 
                               value="1"
                               {{ old('is_public') ? 'checked' : '' }}
                               class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <label for="is_public" class="text-sm font-medium text-gray-700">
                            Make this setting public (accessible on frontend)
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 ml-7">
                        Public settings can be accessed via API and used in frontend templates
                    </p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-2"></i>
                        Required fields are marked with <span class="text-red-500">*</span>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.settings.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition-colors">
                            <i class="fas fa-plus mr-2"></i> Create Setting
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
