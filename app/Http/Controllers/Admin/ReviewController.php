<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Product;
use App\Http\Requests\Admin\ReviewRejectRequest;
use App\Http\Requests\Admin\ReviewBulkActionRequest;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product']);

        // Filter by approval status
        if ($request->has('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        // Filter by rating
        if ($request->has('rating') && $request->rating !== '') {
            $query->where('rating', $request->rating);
        }

        // Search by product name or customer name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('user', function($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%");
                })->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        $reviews = $query->latest()->paginate(20);
        
        return view('admin.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        $review->load(['user', 'product']);
        
        return view('admin.reviews.show', compact('review'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'Review approved successfully!'
        ]);
    }

    public function reject(ReviewRejectRequest $request, Review $review)
    {
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review rejected and removed successfully!'
        ]);
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully!');
    }

    public function bulkApprove(ReviewBulkActionRequest $request)
    {
        $updated = Review::whereIn('id', $request->review_ids)
            ->where('is_approved', false)
            ->update(['is_approved' => true]);

        return response()->json([
            'success' => true,
            'message' => "Approved {$updated} reviews successfully",
            'updated_count' => $updated
        ]);
    }

    public function bulkReject(ReviewBulkActionRequest $request)
    {
        $deleted = Review::whereIn('id', $request->review_ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "Rejected and removed {$deleted} reviews successfully",
            'deleted_count' => $deleted
        ]);
    }

    public function stats()
    {
        $stats = [
            'total_reviews' => Review::count(),
            'approved_reviews' => Review::where('is_approved', true)->count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
            'average_rating' => Review::where('is_approved', true)->avg('rating') ?? 0,
            'five_star' => Review::where('is_approved', true)->where('rating', 5)->count(),
            'four_star' => Review::where('is_approved', true)->where('rating', 4)->count(),
            'three_star' => Review::where('is_approved', true)->where('rating', 3)->count(),
            'two_star' => Review::where('is_approved', true)->where('rating', 2)->count(),
            'one_star' => Review::where('is_approved', true)->where('rating', 1)->count(),
        ];

        // Recent reviews
        $recentReviews = Review::with(['user', 'product'])
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent_reviews' => $recentReviews
        ]);
    }
}
