@extends('admin.layout')

@section('title', 'View Setting')
@section('header', 'Setting: ' . $setting->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.settings.index') }}" class="text-gray-600 hover:text-gray-900 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Settings
        </a>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">{{ $setting->title }}</h2>
            <a href="{{ route('admin.settings.edit', $setting) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase">Key</label>
                <p class="text-gray-900 font-mono">{{ $setting->key }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase">Group</label>
                <p class="text-gray-900">{{ $setting->group }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase">Type</label>
                <p class="text-gray-900">{{ $setting->type }}</p>
            </div>
            @if($setting->description)
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase">Description</label>
                <p class="text-gray-600">{{ $setting->description }}</p>
            </div>
            @endif
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase">Value</label>
                <p class="text-gray-900 mt-1">
                    @if($setting->type === 'boolean')
                        {{ $setting->getValue() ? 'Yes' : 'No' }}
                    @else
                        {{ is_array($setting->getValue()) ? json_encode($setting->getValue()) : $setting->value }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
