@extends('admin.layout')

@section('title', 'Test Form')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-2xl font-semibold mb-4">Test Form</h1>
        
        <form action="{{ route('admin.products.update', 18) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Test Field</label>
                <input type="text" name="test_field" value="test" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div class="mb-4">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked>
                <label>Active Test</label>
            </div>
            
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                Submit Test Form
            </button>
        </form>
    </div>
</div>
@endsection
