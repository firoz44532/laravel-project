@extends('admin.layout')

@section('title', 'Logo & Branding')

@section('header', 'Logo & Branding Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Logo & Branding</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage your store logo and branding settings</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.settings.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Settings
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

    <!-- Logo Upload Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-image mr-2 text-orange-500"></i>
                Store Logo
            </h2>
            
            <form action="{{ route('admin.logo.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Current Logo Preview -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Logo</label>
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            @if($settings['site_logo'])
                                <div class="flex items-center space-x-4">
                                    <img src="{{ $settings['site_logo'] }}" alt="Store Logo" 
                                         class="h-16 w-auto max-w-xs object-contain border border-gray-300 rounded p-2 bg-white">
                                    <div>
                                        <p class="text-sm text-gray-600">Current logo is active</p>
                                        <p class="text-xs text-gray-500">{{ $settings['site_logo'] }}</p>
                                        <button type="submit" name="remove_logo" value="1" 
                                                onclick="return confirm('Are you sure you want to remove the logo?')"
                                                class="mt-2 text-red-600 hover:text-red-800 text-sm font-medium">
                                            <i class="fas fa-trash mr-1"></i> Remove Logo
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-image text-4xl text-gray-300 mb-2"></i>
                                    <p class="text-gray-500">No logo uploaded yet</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Upload New Logo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload New Logo</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                            <div class="text-center">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                                <p class="text-sm text-gray-500">PNG, JPG, GIF up to 2MB (Recommended: 200x60px)</p>
                                <input type="file" name="logo" id="logo" accept="image/*"
                                       class="hidden">
                                <button type="button" onclick="document.getElementById('logo').click()"
                                        class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors text-sm">
                                    <i class="fas fa-upload mr-2"></i> Choose Logo File
                                </button>
                            </div>
                            <div id="logo-preview" class="mt-4 text-center"></div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition-colors">
                        <i class="fas fa-save mr-2"></i> Update Logo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Site Text Settings -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-font mr-2 text-orange-500"></i>
                Site Text & Branding
            </h2>
            
            <form action="{{ route('admin.branding.update') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Site Name -->
                    <div class="space-y-2">
                        <label for="site_name" class="block text-sm font-medium text-gray-700">
                            Site Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="site_name" 
                               name="site_name" 
                               value="{{ $settings['site_name'] }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                               required>
                        <p class="text-xs text-gray-500">Your store name (shown in title and header)</p>
                    </div>
                    
                    <!-- Site Tagline -->
                    <div class="space-y-2">
                        <label for="site_tagline" class="block text-sm font-medium text-gray-700">
                            Site Tagline
                        </label>
                        <input type="text" 
                               id="site_tagline" 
                               name="site_tagline" 
                               value="{{ $settings['site_tagline'] }}"
                               placeholder="e.g., Quality Products, Great Prices"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                        <p class="text-xs text-gray-500">Short tagline shown under logo</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="site_description" class="block text-sm font-medium text-gray-700">
                        Site Description
                    </label>
                    <textarea id="site_description" 
                              name="site_description" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">{{ $settings['site_description'] }}</textarea>
                    <p class="text-xs text-gray-500">Meta description for SEO (shown in search results)</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Contact Email -->
                    <div class="space-y-2">
                        <label for="contact_email" class="block text-sm font-medium text-gray-700">
                            Contact Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="contact_email" 
                               name="contact_email" 
                               value="{{ $settings['contact_email'] }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                               required>
                        <p class="text-xs text-gray-500">Customer support email</p>
                    </div>
                    
                    <!-- Contact Phone -->
                    <div class="space-y-2">
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700">
                            Contact Phone
                        </label>
                        <input type="tel" 
                               id="contact_phone" 
                               name="contact_phone" 
                               value="{{ $settings['contact_phone'] }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                        <p class="text-xs text-gray-500">Customer support phone number</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-2"></i>
                            These settings are used throughout your store and in communications
                        </div>
                        <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition-colors">
                            <i class="fas fa-save mr-2"></i> Update Branding
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('logo').addEventListener('change', function(e) {
    const preview = document.getElementById('logo-preview');
    preview.innerHTML = '';
    
    if (e.target.files && e.target.files[0]) {
        const file = e.target.files[0];
        
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                    <div class="inline-block">
                        <img src="${e.target.result}" alt="Logo Preview" class="h-16 w-auto max-w-xs object-contain border border-gray-300 rounded p-2 bg-white">
                        <p class="text-sm text-gray-600 mt-2">Preview: ${file.name}</p>
                        <p class="text-xs text-gray-500">Size: ${(file.size / 1024).toFixed(2)} KB</p>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '<p class="text-red-600 text-sm">Please select a valid image file</p>';
        }
    }
});
</script>
@endsection
