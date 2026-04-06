@extends('admin.layout')

@section('title', 'Reviews')
@section('header', 'Review Management')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Customer Reviews</h1>
    <div class="flex space-x-4">
        <button onclick="showBulkActions()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-tasks mr-2"></i>Bulk Actions
        </button>
        <button onclick="refreshStats()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-sync mr-2"></i>Refresh Stats
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Reviews</p>
                <p class="text-2xl font-bold text-gray-900" id="total-reviews">0</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-star text-blue-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Approved</p>
                <p class="text-2xl font-bold text-green-600" id="approved-reviews">0</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-green-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Pending</p>
                <p class="text-2xl font-bold text-yellow-600" id="pending-reviews">0</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Avg Rating</p>
                <p class="text-2xl font-bold text-purple-600" id="avg-rating">0.0</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-chart-line text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" placeholder="Search reviews..." 
                   value="{{ request('search') }}"
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
        </div>
        <select name="status" class="px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
            <option value="">All Status</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
        </select>
        <select name="rating" class="px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
            <option value="">All Ratings</option>
            <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
            <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            <i class="fas fa-search mr-2"></i>Filter
        </button>
        <a href="{{ route('admin.reviews.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
            <i class="fas fa-times mr-2"></i>Clear
        </a>
    </form>
</div>

<!-- Bulk Actions (Hidden by default) -->
<div id="bulk-actions" class="bg-white rounded-lg shadow mb-6 p-4 hidden">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <input type="checkbox" id="select-all" class="mr-2">
            <label for="select-all" class="text-sm font-medium">Select All</label>
            <span id="selected-count" class="text-sm text-gray-600">0 selected</span>
        </div>
        <div class="flex space-x-2">
            <button onclick="bulkApprove()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                Approve Selected
            </button>
            <button onclick="bulkReject()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm">
                Reject Selected
            </button>
        </div>
    </div>
</div>

<!-- Reviews Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" id="bulk-select" class="mr-2">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Review
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Customer
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Product
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Rating
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reviews as $review)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="review-checkbox" value="{{ $review->id }}">
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs">
                                <div class="text-sm font-medium text-gray-900">{{ $review->title }}</div>
                                <div class="text-sm text-gray-500 truncate">{{ $review->comment }}</div>
                                @if($review->is_verified_purchase)
                                    <div class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded mt-1">
                                        Verified Purchase
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $review->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $review->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $review->product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $review->product->sku }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="fas fa-star text-sm"></i>
                                    @else
                                        <i class="far fa-star text-sm"></i>
                                    @endif
                                @endfor
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($review->is_approved)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Approved
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $review->created_at->format('M j, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.reviews.show', $review) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$review->is_approved)
                                    <button onclick="approveReview({{ $review->id }})" 
                                            class="text-green-600 hover:text-green-900" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                <button onclick="rejectReview({{ $review->id }})" 
                                        class="text-red-600 hover:text-red-900" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this review?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-600 hover:text-gray-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No reviews found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $reviews->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
// Load statistics on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStats();
});

function loadStats() {
    fetch('/admin/reviews/stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-reviews').textContent = data.stats.total_reviews;
            document.getElementById('approved-reviews').textContent = data.stats.approved_reviews;
            document.getElementById('pending-reviews').textContent = data.stats.pending_reviews;
            document.getElementById('avg-rating').textContent = data.stats.average_rating.toFixed(1);
        })
        .catch(error => {
            console.error('Error loading stats:', error);
        });
}

function refreshStats() {
    loadStats();
    location.reload();
}

function showBulkActions() {
    document.getElementById('bulk-actions').classList.remove('hidden');
}

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

// Bulk actions
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.review-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateSelectedCount();
});

document.querySelectorAll('.review-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const checked = document.querySelectorAll('.review-checkbox:checked');
    document.getElementById('selected-count').textContent = checked.length + ' selected';
}

function bulkApprove() {
    const checked = document.querySelectorAll('.review-checkbox:checked');
    const reviewIds = Array.from(checked).map(cb => cb.value);
    
    if (reviewIds.length === 0) {
        alert('Please select reviews to approve');
        return;
    }
    
    fetch('/admin/reviews/bulk-approve', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            review_ids: reviewIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error approving reviews');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error approving reviews');
    });
}

function bulkReject() {
    const checked = document.querySelectorAll('.review-checkbox:checked');
    const reviewIds = Array.from(checked).map(cb => cb.value);
    
    if (reviewIds.length === 0) {
        alert('Please select reviews to reject');
        return;
    }
    
    if (confirm('Are you sure you want to reject and delete ' + reviewIds.length + ' reviews?')) {
        fetch('/admin/reviews/bulk-reject', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                review_ids: reviewIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error rejecting reviews');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error rejecting reviews');
        });
    }
}
</script>
@endpush
