<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\Frontend\FacebookController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Facebook Conversions API
Route::post('/facebook/conversions', [FacebookController::class, 'conversions']);
Route::get('/facebook/pixel-config', [FacebookController::class, 'pixelConfig']);

// Products API for inventory bulk update
Route::get('/products', function (Request $request) {
    try {
        $products = \App\Models\Product::select('id', 'name', 'stock_quantity', 'price', 'sku')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return response()->json($products);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to load products'], 500);
    }
})->middleware('auth');
