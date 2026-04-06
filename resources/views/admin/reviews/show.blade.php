@extends('admin.layout')

@section('title', 'Review Details')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Review Details</h1>
            <div class="flex space-x-2">
                <a href="{{ route('admin.reviews.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Reviews
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Review Info -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Review Information</h3>
                        <dl class="grid grid-cols-1 gap-2">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Rating:</dt>
                                <dd class="text-sm text-gray-900">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                    ({{ $review->rating }}/5)
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Status:</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($review->is_approved)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Verified Purchase:</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($review->is_verified_purchase)
                                        <span class="text-green-600">Yes</span>
                                    @else
                                        <span class="text-gray-400">No</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Date:</dt>
                                <dd class="text-sm text-gray-900">{{ $review->created_at->format('M j, Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Actions -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Actions</h3>
                        <div class="flex space-x-2">
                            @if(!$review->is_approved)
                                <button onclick="approveReview({{ $review->id }})" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                    <i class="fas fa-check mr-2"></i>
                                    Approve
                                </button>
                            @else
                                <button onclick="rejectReview({{ $review->id }})" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700">
                                    <i class="fas fa-undo mr-2"></i>
                                    Unapprove
                                </button>
                            @endif
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this review?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                                    <i class="fas fa-trash mr-2"></i>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Customer & Product Info -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Customer Information</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-500"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $review->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $review->user->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Product Information</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    @if($review->product->primaryImage)
                                        <img src="{{ asset('storage/' . $review->product->primaryImage->image) }}" 
                                             alt="{{ $review->product->name }}" 
                                             class="w-10 h-10 rounded object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded bg-gray-300 flex items-center justify-center">
                                            <i class="fas fa-box text-gray-500"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $review->product->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $review->product->sku }}</p>
                                    <p class="text-sm text-gray-900 font-medium">BDT {{ number_format($review->product->price, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Content -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Review Content</h3>
                
                @if($review->title)
                    <div class="mb-4">
                        <h4 class="text-base font-medium text-gray-900">{{ $review->title }}</h4>
                    </div>
                @endif
                
                <div class="prose max-w-none">
                    <p class="text-gray-700">{{ $review->comment }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for actions -->
<script>
function approveReview(reviewId) {
    fetch(`/admin/reviews/${reviewId}/approve`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Review approved successfully!');
            location.reload();
        } else {
            alert('Error approving review: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error approving review');
    });
}

function rejectReview(reviewId) {
    const reason = prompt('Please provide a reason for rejecting this review:');
    if (reason) {
        fetch(`/admin/reviews/${reviewId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ reason: reason }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Review rejected successfully!');
                window.location.href = '/admin/reviews';
            } else {
                alert('Error rejecting review: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error rejecting review');
        });
    }
}
</script>
@endsection
