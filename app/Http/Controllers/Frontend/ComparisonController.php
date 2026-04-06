<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Session;

class ComparisonController extends Controller
{
    public function index()
    {
        $comparisonIds = Session::get('comparison', []);
        
        if (empty($comparisonIds)) {
            return view('frontend.comparison.empty');
        }

        $products = Product::whereIn('id', $comparisonIds)
            ->with(['primaryImage', 'category', 'brand', 'approvedReviews'])
            ->get();

        if ($products->isEmpty()) {
            Session::forget('comparison');
            return view('frontend.comparison.empty');
        }

        // Get comparison attributes
        $attributes = $this->getComparisonAttributes($products);

        return view('frontend.comparison.index', compact('products', 'attributes'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->product_id;
        $comparison = Session::get('comparison', []);

        // Limit to 4 products for comparison
        if (count($comparison) >= 4) {
            return response()->json([
                'success' => false,
                'message' => 'You can compare maximum 4 products at a time'
            ]);
        }

        // Add product to comparison if not already there
        if (!in_array($productId, $comparison)) {
            $comparison[] = $productId;
            Session::put('comparison', $comparison);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to comparison',
            'comparison_count' => count($comparison)
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->product_id;
        $comparison = Session::get('comparison', []);

        // Remove product from comparison
        if (($key = array_search($productId, $comparison)) !== false) {
            unset($comparison[$key]);
            Session::put('comparison', array_values($comparison));
        }

        return response()->json([
            'success' => true,
            'message' => 'Product removed from comparison',
            'comparison_count' => count($comparison)
        ]);
    }

    public function clear()
    {
        Session::forget('comparison');

        return response()->json([
            'success' => true,
            'message' => 'Comparison cleared'
        ]);
    }

    public function getComparisonCount()
    {
        $comparison = Session::get('comparison', []);

        return response()->json([
            'count' => count($comparison)
        ]);
    }

    public function getComparisonProducts()
    {
        $comparisonIds = Session::get('comparison', []);

        if (empty($comparisonIds)) {
            return response()->json([
                'products' => []
            ]);
        }

        $products = Product::whereIn('id', $comparisonIds)
            ->with(['primaryImage', 'category', 'brand'])
            ->get();

        return response()->json([
            'products' => $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->formatted_price,
                    'image' => $product->primaryImage ? asset('storage/' . $product->primaryImage->image_path) : null,
                    'category' => $product->category ? $product->category->name : null,
                    'brand' => $product->brand ? $product->brand->name : null,
                ];
            })
        ]);
    }

    private function getComparisonAttributes($products)
    {
        $attributes = [];

        // Basic attributes that all products have
        $attributes['basic'] = [
            'name' => 'Product Name',
            'price' => 'Price',
            'category' => 'Category',
            'brand' => 'Brand',
            'stock_quantity' => 'Stock Status',
        ];

        // Get common attributes from all products
        $commonAttributes = [];
        foreach ($products as $product) {
            if ($product->attributes) {
                $productAttributes = json_decode($product->attributes, true) ?? [];
                foreach ($productAttributes as $key => $value) {
                    if (!isset($commonAttributes[$key])) {
                        $commonAttributes[$key] = 0;
                    }
                    $commonAttributes[$key]++;
                }
            }
        }

        // Add attributes that appear in at least 2 products
        foreach ($commonAttributes as $key => $count) {
            if ($count >= 2) {
                $attributes['custom'][$key] = $this->formatAttributeName($key);
            }
        }

        return $attributes;
    }

    private function formatAttributeName($key)
    {
        // Convert snake_case to Title Case
        return ucwords(str_replace('_', ' ', $key));
    }

    private function getAttributeValue($product, $attribute)
    {
        switch ($attribute) {
            case 'name':
                return $product->name;
            case 'price':
                return $product->formatted_price;
            case 'category':
                return $product->category ? $product->category->name : 'N/A';
            case 'brand':
                return $product->brand ? $product->brand->name : 'N/A';
            case 'stock_quantity':
                return $product->stock_quantity > 0 ? 'In Stock (' . $product->stock_quantity . ')' : 'Out of Stock';
            default:
                $attributes = json_decode($product->attributes, true) ?? [];
                return $attributes[$attribute] ?? 'N/A';
        }
    }

    public function compareProducts(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:2|max:4',
            'products.*' => 'required|exists:products,id',
        ]);

        $productIds = $request->products;
        $products = Product::whereIn('id', $productIds)
            ->with(['primaryImage', 'category', 'brand', 'approvedReviews'])
            ->get();

        if ($products->count() < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least 2 products to compare'
            ]);
        }

        $attributes = $this->getComparisonAttributes($products);

        return response()->json([
            'success' => true,
            'products' => $products,
            'attributes' => $attributes
        ]);
    }
}
