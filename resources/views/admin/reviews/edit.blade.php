@extends('admin.layout')

@section('title', 'Edit Review')
@section('header', 'Edit Review - ' . $review->product->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h1 class="text-xl font-semibold">Edit Review</h1>
            <div class="flex items-center space-x-4 text-sm">
                <span class="px-3 py-1 rounded-full bg-{{ $review->is_approved ? 'green' : 'yellow' }}-100 text-{{ $review->is_approved ? 'green' : 'yellow' }}-800">
                    {{ $review->is_approved ? 'Approved' : 'Pending' }}
                </span>
                <span class="text-gray-600">
                    by {{ $review->user->name }} on {{ $review->created_at->format('M j, Y') }}
                </span>
            </div>
        </div>
        
        <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Review Details -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Review Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Review Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" required
                               value="{{ $review->title }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">
                            Rating <span class="text-red-500">*</span>
                        </label>
                        <select id="rating" name="rating" required
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                            <option value="1" {{ $review->rating === 1 ? 'selected' : '' }}>1 Star</option>
                            <option value="2" {{ $review->rating === 2 ? 'selected' : '' }}>2 Stars</option>
                            <option value="3" {{ $review->rating === 3 ? 'selected' : '' }}>3 Stars</option>
                            <option value="4" {{ $review->rating === 4 ? 'selected' : '' }}>4 Stars</option>
                            <option value="5" {{ $review->rating === 5 ? 'selected' : '' }}>5 Stars</option>
                        </select>
                        @error('rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Review Content -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Review Content</h2>
                <div class="mb-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                        Review <span class="text-red-500">*</span>
                    </label>
                    <textarea id="comment" name="comment" rows="6" required
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">{{ old('comment') }}</textarea>
                    @error('comment')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Admin Actions -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Admin Actions</h2>
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <button onclick="approveReview({{ $review->id }})" 
                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-check mr-2"></i>Approve
                        </button>
                        <button onclick="rejectReview({{ $review->id }})" 
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-times mr-2"></i>Reject
                        </button>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <button onclick="deleteReview({{ $review->id }})" 
                                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.reviews.index') }}" 
                   class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Reviews
                </a>
                <button type="submit" 
                        class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                    <i class="fas fa-save mr-2"></i>Update Review
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function approveReview(reviewId) {
    fetch(`/admin/reviews/${reviewId}/approve`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error approving review');
    });
}

function rejectReview(reviewId) {
    if (confirm('Are you sure you want to reject this review? This will delete it permanently.')) {
        fetch(`/admin/reviews/${reviewId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error rejecting review');
        });
    }
}

function deleteReview(reviewId) {
    if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
        fetch(`/admin/reviews/${reviewId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting review');
        });
    }
}
</script>
@endsection
