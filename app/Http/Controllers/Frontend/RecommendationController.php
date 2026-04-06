<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Wishlist;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class RecommendationController extends Controller
{
    public function getRecommendations(Request $request)
    {
        $type = $request->get('type', 'related');
        $productId = $request->get('product_id');
        $limit = $request->get('limit', 8);

        switch ($type) {
            case 'related':
                return $this->getRelatedProducts($productId, $limit);
            case 'trending':
                return $this->getTrendingProducts($limit);
            case 'new':
                return $this->getNewProducts($limit);
            case 'bestselling':
                return $this->getBestSellingProducts($limit);
            case 'personalized':
                return $this->getPersonalizedRecommendations($limit);
            case 'category':
                $categoryId = $request->get('category_id');
                return $this->getCategoryRecommendations($categoryId, $limit);
            case 'brand':
                $brandId = $request->get('brand_id');
                return $this->getBrandRecommendations($brandId, $limit);
            default:
                return response()->json(['products' => []]);
        }
    }

    private function getRelatedProducts($productId, $limit)
    {
        if (!$productId) {
            return response()->json(['products' => []]);
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['products' => []]);
        }

        // Get products from same category
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $productId)
            ->where('is_active', true)
            ->with(['primaryImage', 'category', 'brand'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        // If not enough products from same category, get from same brand
        if ($relatedProducts->count() < $limit) {
            $additionalProducts = Product::where('brand_id', $product->brand_id)
                ->where('id', '!=', $productId)
                ->where('is_active', true)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->with(['primaryImage', 'category', 'brand'])
                ->inRandomOrder()
                ->limit($limit - $relatedProducts->count())
                ->get();

            $relatedProducts = $relatedProducts->concat($additionalProducts);
        }

        return response()->json([
            'products' => $relatedProducts->map(function($product) {
                return $this->formatProduct($product);
            })
        ]);
    }

    private function getTrendingProducts($limit)
    {
        $cacheKey = 'trending_products_' . $limit;
        
        $products = Cache::remember($cacheKey, 3600, function() use ($limit) {
            return Product::with(['primaryImage', 'category', 'brand'])
                ->where('is_active', true)
                ->where('is_featured', true)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });

        return response()->json([
            'products' => $products->map(function($product) {
                return $this->formatProduct($product);
            })
        ]);
    }

    private function getNewProducts($limit)
    {
        $cacheKey = 'new_products_' . $limit;
        
        $products = Cache::remember($cacheKey, 1800, function() use ($limit) {
            return Product::with(['primaryImage', 'category', 'brand'])
                ->where('is_active', true)
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });

        return response()->json([
            'products' => $products->map(function($product) {
                return $this->formatProduct($product);
            })
        ]);
    }

    private function getBestSellingProducts($limit)
    {
        $cacheKey = 'best_selling_products_' . $limit;
        
        $products = Cache::remember($cacheKey, 3600, function() use ($limit) {
            return Product::with(['primaryImage', 'category', 'brand'])
                ->withCount(['orderItems' => function($query) {
                    $query->whereHas('order', function($q) {
                        $q->where('status', '!=', 'cancelled');
                    });
                }])
                ->where('is_active', true)
                ->orderBy('order_items_count', 'desc')
                ->limit($limit)
                ->get();
        });

        return response()->json([
            'products' => $products->map(function($product) {
                return $this->formatProduct($product);
            })
        ]);
    }

    private function getPersonalizedRecommendations($limit)
    {
        if (!Auth::check()) {
            return $this->getTrendingProducts($limit);
        }

        $user = Auth::user();
        $cacheKey = 'personalized_recommendations_' . $user->id . '_' . $limit;
        
        $products = Cache::remember($cacheKey, 1800, function() use ($user, $limit) {
            // Get user's order history
            $orderedProductIds = OrderItem::whereHas('order', function($query) use ($user) {
                    $query->where('user_id', $user->id)->where('status', '!=', 'cancelled');
                })
                ->pluck('product_id')
                ->unique()
                ->toArray();

            // Get user's wishlist
            $wishlistProductIds = Wishlist::where('user_id', $user->id)
                ->pluck('product_id')
                ->unique()
                ->toArray();

            // Get user's cart
            $cartProductIds = Cart::where('user_id', $user->id)
                ->pluck('product_id')
                ->unique()
                ->toArray();

            // Get categories user has purchased from
            $purchasedCategories = Product::whereIn('id', $orderedProductIds)
                ->pluck('category_id')
                ->unique()
                ->toArray();

            // Get brands user has purchased from
            $purchasedBrands = Product::whereIn('id', $orderedProductIds)
                ->pluck('brand_id')
                ->unique()
                ->toArray();

            // Build recommendation query
            $query = Product::with(['primaryImage', 'category', 'brand'])
                ->where('is_active', true)
                ->whereNotIn('id', array_merge($orderedProductIds, $wishlistProductIds, $cartProductIds));

            // Prioritize products from purchased categories and brands
            if (!empty($purchasedCategories)) {
                $query->whereIn('category_id', $purchasedCategories);
            } elseif (!empty($purchasedBrands)) {
                $query->whereIn('brand_id', $purchasedBrands);
            }

            return $query->inRandomOrder()
                ->limit($limit)
                ->get();
        });

        return response()->json([
            'products' => $products->map(function($product) {
                return $this->formatProduct($product);
            })
        ]);
    }

    private function getCategoryRecommendations($categoryId, $limit)
    {
        if (!$categoryId) {
            return response()->json(['products' => []]);
        }

        $cacheKey = 'category_recommendations_' . $categoryId . '_' . $limit;
        
        $products = Cache::remember($cacheKey, 1800, function() use ($categoryId, $limit) {
            return Product::with(['primaryImage', 'category', 'brand'])
                ->where('category_id', $categoryId)
                ->where('is_active', true)
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        });

        return response()->json([
            'products' => $products->map(function($product) {
                return $this->formatProduct($product);
            })
        ]);
    }

    private function getBrandRecommendations($brandId, $limit)
    {
        if (!$brandId) {
            return response()->json(['products' => []]);
        }

        $cacheKey = 'brand_recommendations_' . $brandId . '_' . $limit;
        
        $products = Cache::remember($cacheKey, 1800, function() use ($brandId, $limit) {
            return Product::with(['primaryImage', 'category', 'brand'])
                ->where('brand_id', $brandId)
                ->where('is_active', true)
                ->inRandomOrder()
                ->limit($limit)
                ->get();
        });

        return response()->json([
            'products' => $products->map(function($product) {
                return $this->formatProduct($product);
            })
        ]);
    }

    private function formatProduct($product)
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->formatted_price,
            'compare_price' => $product->formatted_compare_price,
            'has_discount' => $product->has_discount,
            'discount_percentage' => $product->discount_percentage,
            'image' => $product->primaryImage ? asset('storage/' . $product->primaryImage->image_path) : null,
            'category' => $product->category ? $product->category->name : null,
            'brand' => $product->brand ? $product->brand->name : null,
            'rating' => $product->average_rating,
            'reviews_count' => $product->approved_reviews_count,
            'stock_status' => $product->stock_status,
            'is_in_stock' => $product->is_in_stock,
            'url' => route('products.show', $product->slug),
        ];
    }

    public function getHomePageRecommendations()
    {
        $recommendations = [
            'trending' => $this->getTrendingProducts(8)->getData(true),
            'new' => $this->getNewProducts(8)->getData(true),
            'bestselling' => $this->getBestSellingProducts(8)->getData(true),
        ];

        if (Auth::check()) {
            $recommendations['personalized'] = $this->getPersonalizedRecommendations(8)->getData(true);
        }

        return response()->json($recommendations);
    }

    public function trackProductView(Request $request)
    {
        $productId = $request->get('product_id');
        
        if (!$productId) {
            return response()->json(['success' => false]);
        }

        // Track product view for analytics
        $cacheKey = 'product_views_' . date('Y-m-d');
        $views = Cache::get($cacheKey, []);
        
        if (!isset($views[$productId])) {
            $views[$productId] = 0;
        }
        $views[$productId]++;
        
        Cache::put($cacheKey, $views, 86400); // Cache for 24 hours

        return response()->json(['success' => true]);
    }

    public function getSearchRecommendations(Request $request)
    {
        $query = $request->get('query', '');
        $limit = $request->get('limit', 5);

        if (empty($query)) {
            return response()->json(['products' => []]);
        }

        $products = Product::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', '%' . $query . '%')
                  ->orWhere('description', 'LIKE', '%' . $query . '%')
                  ->orWhere('sku', 'LIKE', '%' . $query . '%');
            })
            ->with(['primaryImage', 'category', 'brand'])
            ->limit($limit)
            ->get();

        return response()->json([
            'products' => $products->map(function($product) {
                return $this->formatProduct($product);
            })
        ]);
    }

    public function getCrossSellRecommendations(Request $request)
    {
        $productId = $request->get('product_id');
        $limit = $request->get('limit', 4);

        if (!$productId) {
            return response()->json(['products' => []]);
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['products' => []]);
        }

        // Get products frequently bought together
        $frequentlyBoughtTogether = OrderItem::select('product_id')
            ->where('product_id', '!=', $productId)
            ->whereIn('order_id', function($query) use ($productId) {
                $query->select('order_id')
                    ->from('order_items')
                    ->where('product_id', $productId);
            })
            ->withCount(['order_items' => function($query) {
                $query->where('product_id', $productId);
            }])
            ->groupBy('product_id')
            ->orderBy('order_items_count', 'desc')
            ->limit($limit)
            ->pluck('product_id');

        $products = Product::whereIn('id', $frequentlyBoughtTogether)
            ->where('is_active', true)
            ->with(['primaryImage', 'category', 'brand'])
            ->limit($limit)
            ->get();

        return response()->json([
            'products' => $products->map(function($product) {
                return $this->formatProduct($product);
            })
        ]);
    }

    public function getUpsellRecommendations(Request $request)
    {
        $productId = $request->get('product_id');
        $limit = $request->get('limit', 4);

        if (!$productId) {
            return response()->json(['products' => []]);
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['products' => []]);
        }

        // Get products from same category with higher price
        $upsellProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $productId)
            ->where('is_active', true)
            ->where('price', '>', $product->price)
            ->with(['primaryImage', 'category', 'brand'])
            ->orderBy('price', 'asc')
            ->limit($limit)
            ->get();

        return response()->json([
            'products' => $upsellProducts->map(function($product) {
                return $this->formatProduct($product);
            })
        ]);
    }
}
