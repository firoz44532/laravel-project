@extends('admin.layout')

@section('title', 'Settings Management')

@section('header', 'Settings Management')

@section('content')
<div class="max-w-7xl mx-auto px-5">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Settings Management</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage your store configuration and preferences</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="exportSettings()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-download mr-2"></i> Export
                    </button>
                    <a href="{{ route('admin.settings.create') }}" class="px-4 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i> Add Setting
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

    <!-- Settings Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <i class="fas fa-cog text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Settings</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $settings->flatten()->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $settings->count() }} groups</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i class="fas fa-globe text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Public Settings</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $settings->flatten()->where('is_public', true)->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1">Visible to public</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <i class="fas fa-lock text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Private Settings</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $settings->flatten()->where('is_public', false)->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1">Admin only</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Last Updated</p>
                    <p class="text-2xl font-semibold text-gray-900">Today</p>
                    <p class="text-sm text-gray-600 mt-1">Recent changes</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Configuration -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200" style="padding-left: 43px; padding-right: 43px;">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Settings Configuration</h2>
                    <p class="text-sm text-gray-500 mt-1">Configure system settings and preferences</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ $settings->flatten()->count() }} total settings
                    </span>
                </div>
            </div>
        </div>

        <!-- Settings Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                @foreach($settings as $group => $groupSettings)
                    <button onclick="showTab('{{ $group }}')" 
                            class="tab-button py-4 px-1 border-b-2 font-medium text-sm transition-colors
                                   {{ $loop->first ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                            data-tab="{{ $group }}">
                        {{ ucfirst($group) }}
                        <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">
                            {{ $groupSettings->count() }}
                        </span>
                    </button>
                @endforeach
            </nav>
        </div>

        <!-- Tab Content -->
        <form action="{{ route('admin.settings.bulkUpdate') }}" method="POST" class="p-6">
            @csrf
            @foreach($settings as $group => $groupSettings)
                <div id="tab-{{ $group }}" 
                     class="tab-content {{ $loop->first ? '' : 'hidden' }}"
                     data-group="{{ $group }}">
                    <div class="space-y-6">
                        @foreach($groupSettings as $setting)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <label for="setting-{{ $setting->id }}" class="text-sm font-medium text-gray-900">
                                            {{ $setting->title }}
                                        </label>
                                        @if($setting->is_public)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-globe mr-1"></i> Public
                                            </span>
                                        @endif
                                    </div>
                                    @if($setting->description)
                                        <p class="text-sm text-gray-500">{{ $setting->description }}</p>
                                    @endif
                                </div>
                                
                                <div class="space-y-2">
                                    @if($setting->type == 'boolean')
                                        <div class="flex items-center space-x-3">
                                            <input type="hidden" name="settings[{{ $setting->id }}][value]" value="0">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" 
                                                       id="setting-{{ $setting->id }}" 
                                                       name="settings[{{ $setting->id }}][value]" 
                                                       value="1"
                                                       {{ $setting->getValue() ? 'checked' : '' }}
                                                       class="sr-only peer">
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                                                <span class="ml-3 text-sm font-medium text-gray-700">
                                                    {{ $setting->getValue() ? 'Enabled' : 'Disabled' }}
                                                </span>
                                            </label>
                                        </div>
                                    @elseif($setting->type == 'number')
                                        <input type="number" 
                                               id="setting-{{ $setting->id }}" 
                                               name="settings[{{ $setting->id }}][value]" 
                                               value="{{ $setting->getValue() }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                                    @elseif($setting->type == 'json')
                                        <textarea id="setting-{{ $setting->id }}" 
                                                  name="settings[{{ $setting->id }}][value]" 
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm font-mono"
                                                  rows="3">{{ $setting->value }}</textarea>
                                    @elseif($setting->type == 'image')
                                        <div class="flex items-center space-x-3">
                                            <input type="text" 
                                                   id="setting-{{ $setting->id }}" 
                                                   name="settings[{{ $setting->id }}][value]" 
                                                   value="{{ $setting->value }}"
                                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm"
                                                   placeholder="Enter image URL">
                                            @if($setting->value)
                                                <img src="{{ $setting->value }}" alt="Preview" class="h-10 w-10 object-cover rounded">
                                            @endif
                                        </div>
                                    @else
                                        <input type="text" 
                                               id="setting-{{ $setting->id }}" 
                                               name="settings[{{ $setting->id }}][value]" 
                                               value="{{ $setting->value }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                                    @endif
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">
                                            Key: <code class="bg-gray-100 px-1 py-0.5 rounded">{{ $setting->key }}</code>
                                        </span>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.settings.edit', $setting) }}" 
                                               class="text-orange-600 hover:text-orange-800 text-sm font-medium">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.settings.destroy', $setting) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        onclick="return confirm('Are you sure you want to delete this setting?')"
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                    <i class="fas fa-trash mr-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if(!$loop->last)
                                <hr class="my-6 border-gray-200">
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Action Buttons -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-2"></i>
                        Changes will be applied immediately after saving
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.settings.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition-colors">
                            <i class="fas fa-save mr-2"></i> Save All Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function showTab(groupName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active state from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-orange-500', 'text-orange-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    document.getElementById('tab-' + groupName).classList.remove('hidden');
    
    // Add active state to clicked button
    const activeButton = document.querySelector(`[data-tab="${groupName}"]`);
    activeButton.classList.remove('border-transparent', 'text-gray-500');
    activeButton.classList.add('border-orange-500', 'text-orange-600');
}

function exportSettings() {
    // Simple export functionality
    const settings = @json($settings);
    const dataStr = JSON.stringify(settings, null, 2);
    const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
    
    const exportFileDefaultName = 'settings-' + new Date().toISOString().slice(0,10) + '.json';
    
    const linkElement = document.createElement('a');
    linkElement.setAttribute('href', dataUri);
    linkElement.setAttribute('download', exportFileDefaultName);
    linkElement.click();
}
</script>
@endsection
