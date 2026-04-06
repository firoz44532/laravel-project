@extends('frontend.layout')

@section('title', 'Product Comparison')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h1 class="text-2xl font-bold flex items-center">
                <i class="fas fa-balance-scale text-primary mr-3"></i>
                Product Comparison
            </h1>
            <p class="text-gray-600 mt-2">Compare up to 4 products side by side</p>
        </div>
        
        <div class="p-6">
            <div class="text-center py-12">
                <i class="fas fa-balance-scale text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Products Selected</h3>
                <p class="text-gray-500 mb-6">Add products to comparison to see detailed comparison</p>
                
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('products.index') }}" 
                       class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                        <i class="fas fa-shopping-bag mr-2"></i>Browse Products
                    </a>
                    <button onclick="clearComparison()" 
                            class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                        <i class="fas fa-trash mr-2"></i>Clear Comparison
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearComparison() {
    fetch('/comparison/clear', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endsection
