<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $subtotal = $cartItems->sum(function($item) {
            return $item->quantity * $item->price;
        });
        
        return view('frontend.cart.index', compact('cartItems', 'subtotal'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Product is not available'
            ], 400);
        }

        if ($product->track_stock && $product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available'
            ], 400);
        }

        if (Auth::check()) {
            // Logged in user - save to database
            $cartItem = Cart::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                ],
                [
                    'quantity' => $request->quantity,
                    'price' => $product->price,
                ]
            );

            // If item exists, update quantity
            if (!$cartItem->wasRecentlyCreated) {
                $cartItem->increment('quantity', $request->quantity);
            }
        } else {
            // Guest user - save to session
            $cart = Session::get('cart', []);
            $productId = $product->id;

            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] += $request->quantity;
            } else {
                $cart[$productId] = [
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'price' => $product->price,
                ];
            }

            Session::put('cart', $cart);
        }

        $cartCount = $this->getCartItems()->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart_count' => $cartCount,
            'product' => [
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->primaryImage ? $product->primaryImage->image_url : null,
                'quantity' => (int) $request->quantity,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->track_stock && $product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available'
            ], 400);
        }

        if (Auth::check()) {
            $cartItem = Cart::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                $cartItem->update(['quantity' => $request->quantity]);
            }
        } else {
            $cart = Session::get('cart', []);
            $productId = $product->id;

            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] = $request->quantity;
                Session::put('cart', $cart);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully!',
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (Auth::check()) {
            Cart::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->delete();
        } else {
            $cart = Session::get('cart', []);
            unset($cart[$product->id]);
            Session::put('cart', $cart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart!',
        ]);
    }

    public function clear()
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->delete();
        } else {
            Session::forget('cart');
        }

        return redirect()->route('cart.index')->with('success', 'Cart cleared successfully!');
    }

    public function getCartCount()
    {
        $count = $this->getCartItems()->sum('quantity');
        return response()->json(['count' => $count]);
    }

    public function getCartItems()
    {
        if (Auth::check()) {
            return Cart::with(['product.primaryImage', 'product.category'])
                ->where('user_id', Auth::id())
                ->get()
                ->map(function($cartItem) {
                    $product = $cartItem->product;
                    $product->quantity = $cartItem->quantity;
                    $product->price = $cartItem->price;
                    $product->cart_id = $cartItem->id;
                    $product->sku = $product->sku ?? 'N/A';
                    $product->track_stock = $product->track_stock ?? false;
                    $product->stock_quantity = $product->stock_quantity ?? 0;
                    return $product;
                });
        } else {
            $cart = Session::get('cart', []);
            $productIds = array_keys($cart);
            
            if (empty($productIds)) {
                return collect([]);
            }

            $products = Product::with(['primaryImage', 'category'])
                ->whereIn('id', $productIds)
                ->get();

            return $products->map(function($product) use ($cart) {
                $cartItem = $cart[$product->id];
                $product->quantity = $cartItem['quantity'];
                $product->price = $cartItem['price'];
                $product->cart_id = $product->id; // For consistency
                $product->sku = $product->sku ?? 'N/A';
                $product->track_stock = $product->track_stock ?? false;
                $product->stock_quantity = $product->stock_quantity ?? 0;
                return $product;
            });
        }
    }

    public function getCartTotal()
    {
        $cartItems = $this->getCartItems();
        return $cartItems->sum(function($item) {
            return $item->quantity * $item->price;
        });
    }

    // Merge guest cart with user cart when user logs in
    public static function mergeGuestCart()
    {
        if (!Auth::check()) {
            return;
        }

        $guestCart = Session::get('cart', []);
        
        if (empty($guestCart)) {
            return;
        }

        foreach ($guestCart as $productId => $item) {
            $product = Product::find($productId);
            
            if (!$product || !$product->is_active) {
                continue;
            }

            Cart::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'product_id' => $productId,
                ],
                [
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]
            );
        }

        Session::forget('cart');
    }
}
