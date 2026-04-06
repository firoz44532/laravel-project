<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('message', 'Please login to view your wishlist');
        }

        $wishlistItems = Wishlist::forUser(Auth::id())
            ->withProduct()
            ->latest()
            ->get();

        return view('frontend.wishlist.index', compact('wishlistItems'));
    }

    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add items to wishlist'
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not available'
            ], 400);
        }

        $wishlistItem = Wishlist::addToWishlist($product->id);

        if ($wishlistItem) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist successfully!',
                'wishlist_count' => Wishlist::getWishlistCount(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product is already in your wishlist!',
        ]);
    }

    public function remove(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to manage your wishlist'
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        Wishlist::removeFromWishlist($request->product_id);

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist!',
            'wishlist_count' => Wishlist::getWishlistCount(),
        ]);
    }

    public function clear()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to manage your wishlist'
            ], 401);
        }

        Wishlist::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist cleared successfully!',
            'wishlist_count' => 0
        ]);
    }

    public function getWishlistCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        return response()->json([
            'count' => Wishlist::getWishlistCount()
        ]);
    }

    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to manage wishlist'
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->product_id;
        $userId = Auth::id();

        if (Wishlist::isInWishlist($productId, $userId)) {
            Wishlist::removeFromWishlist($productId, $userId);
            $message = 'Product removed from wishlist';
            $isInWishlist = false;
        } else {
            Wishlist::addToWishlist($productId, $userId);
            $message = 'Product added to wishlist';
            $isInWishlist = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_in_wishlist' => $isInWishlist,
            'wishlist_count' => Wishlist::getWishlistCount(),
        ]);
    }
}
