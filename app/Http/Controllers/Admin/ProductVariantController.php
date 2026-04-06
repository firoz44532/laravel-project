<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Http\Requests\Admin\ProductVariantStoreRequest;
use App\Http\Requests\Admin\ProductVariantUpdateRequest;
use App\Http\Requests\Admin\ProductVariantBulkStoreRequest;
use App\Http\Requests\Admin\ProductVariantBulkUpdateRequest;
use App\Http\Requests\Admin\ProductVariantStockUpdateRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductVariantController extends Controller
{
    public function index(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $variants = $product->variants()->with(['primaryImage'])->get();
        
        return view('admin.products.variants.index', compact('product', 'variants'));
    }

    public function create($productId)
    {
        $product = Product::findOrFail($productId);
        return view('admin.products.variants.create', compact('product'));
    }

    public function store(ProductVariantStoreRequest $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
        $variant = $product->variants()->create([
            'name' => $request->name,
            'variant_type' => $request->variant_type,
            'sku' => $request->sku,
            'price' => $request->price,
            'compare_price' => $request->compare_price,
            'stock_quantity' => $request->stock_quantity,
            'track_stock' => $request->boolean('track_stock', true),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
            'attributes' => $request->attributes ?? [],
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('product_variants', $imageName, 'public');
            
            $variant->images()->create([
                'image_path' => 'product_variants/' . $imageName,
                'is_primary' => true,
            ]);
        }

        return redirect()
            ->route('admin.products.variants.index', $productId)
            ->with('success', 'Product variant created successfully!');
    }

    public function edit($productId, $variantId)
    {
        $product = Product::findOrFail($productId);
        $variant = $product->variants()->findOrFail($variantId);
        
        return view('admin.products.variants.edit', compact('product', 'variant'));
    }

    public function update(ProductVariantUpdateRequest $request, $productId, $variantId)
    {
        $product = Product::findOrFail($productId);
        $variant = $product->variants()->findOrFail($variantId);
        
        $variant->update([
            'name' => $request->name,
            'variant_type' => $request->variant_type,
            'sku' => $request->sku,
            'price' => $request->price,
            'compare_price' => $request->compare_price,
            'stock_quantity' => $request->stock_quantity,
            'track_stock' => $request->boolean('track_stock', true),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
            'attributes' => $request->attributes ?? [],
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($variant->primaryImage) {
                Storage::disk('public')->delete($variant->primaryImage->image_path);
                $variant->primaryImage->delete();
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('product_variants', $imageName, 'public');
            
            $variant->images()->create([
                'image_path' => 'product_variants/' . $imageName,
                'is_primary' => true,
            ]);
        }

        return redirect()
            ->route('admin.products.variants.index', $productId)
            ->with('success', 'Product variant updated successfully!');
    }

    public function destroy($productId, $variantId)
    {
        $product = Product::findOrFail($productId);
        $variant = $product->variants()->findOrFail($variantId);
        
        // Delete variant images
        foreach ($variant->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
        
        $variant->delete();

        return redirect()
            ->route('admin.products.variants.index', $productId)
            ->with('success', 'Product variant deleted successfully!');
    }

    public function bulkCreate($productId)
    {
        $product = Product::findOrFail($productId);
        return view('admin.products.variants.bulk-create', compact('product'));
    }

    public function bulkStore(ProductVariantBulkStoreRequest $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $createdCount = 0;
        
        foreach ($request->variants as $variantData) {
            try {
                $variant = $product->variants()->create([
                    'name' => $variantData['name'],
                    'variant_type' => $variantData['variant_type'],
                    'sku' => $variantData['sku'],
                    'price' => $variantData['price'],
                    'compare_price' => $variantData['compare_price'],
                    'stock_quantity' => $variantData['stock_quantity'],
                    'track_stock' => $variantData['track_stock'] ?? true,
                    'is_active' => $variantData['is_active'] ?? true,
                    'sort_order' => $variantData['sort_order'] ?? 0,
                    'attributes' => $variantData['attributes'] ?? [],
                ]);
                
                $createdCount++;
            } catch (\Exception $e) {
                // Log error but continue with other variants
                continue;
            }
        }

        return redirect()
            ->route('admin.products.variants.index', $productId)
            ->with('success', "Successfully created {$createdCount} product variants!");
    }

    public function bulkUpdate(ProductVariantBulkUpdateRequest $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $variants = $product->variants()->whereIn('id', $request->variant_ids);
        $updatedCount = 0;

        switch ($request->action) {
            case 'activate':
                $variants->update(['is_active' => true]);
                $updatedCount = $variants->count();
                $message = "Successfully activated {$updatedCount} variants!";
                break;
                
            case 'deactivate':
                $variants->update(['is_active' => false]);
                $updatedCount = $variants->count();
                $message = "Successfully deactivated {$updatedCount} variants!";
                break;
                
            case 'delete':
                foreach ($variants as $variant) {
                    foreach ($variant->images as $image) {
                        Storage::disk('public')->delete($image->image_path);
                        $image->delete();
                    }
                    $variant->delete();
                }
                $updatedCount = $variants->count();
                $message = "Successfully deleted {$updatedCount} variants!";
                break;
                
            case 'update_stock':
                $variants->update(['stock_quantity' => $request->stock_quantity]);
                $updatedCount = $variants->count();
                $message = "Successfully updated stock for {$updatedCount} variants!";
                break;
                
            case 'update_price':
                $variants->update(['price' => $request->price]);
                $updatedCount = $variants->count();
                $message = "Successfully updated price for {$updatedCount} variants!";
                break;
        }

        return redirect()
            ->route('admin.products.variants.index', $productId)
            ->with('success', $message);
    }

    public function duplicate($productId, $variantId)
    {
        $product = Product::findOrFail($productId);
        $originalVariant = $product->variants()->findOrFail($variantId);
        
        $newVariant = $originalVariant->replicate([
            'sku' => $originalVariant->sku . '-copy-' . Str::random(4),
        ]);
        
        $newVariant->save();
        
        // Duplicate images
        foreach ($originalVariant->images as $image) {
            $newImage = $image->replicate();
            $newImage->save();
        }
        
        return redirect()
            ->route('admin.products.variants.index', $productId)
            ->with('success', 'Product variant duplicated successfully!');
    }

    public function setPrimaryImage($productId, $variantId, $imageId)
    {
        $product = Product::findOrFail($productId);
        $variant = $product->variants()->findOrFail($variantId);
        
        // Remove primary status from all images
        $variant->images()->update(['is_primary' => false]);
        
        // Set new primary image
        $variant->images()->where('id', $imageId)->update(['is_primary' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'Primary image updated successfully!'
        ]);
    }

    public function deleteImage($productId, $variantId, $imageId)
    {
        $product = Product::findOrFail($productId);
        $variant = $product->variants()->findOrFail($variantId);
        $image = $variant->images()->findOrFail($imageId);
        
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully!'
        ]);
    }

    public function getVariantData($productId, $variantId)
    {
        $product = Product::findOrFail($productId);
        $variant = $product->variants()->with(['images'])->findOrFail($variantId);
        
        return response()->json([
            'variant' => $variant,
            'product' => $product
        ]);
    }

    public function getVariantOptions($productId)
    {
        $product = Product::findOrFail($productId);
        $variants = $product->variants()->where('is_active', true)->get();
        
        $options = [];
        foreach ($variants as $variant) {
            $options[$variant->variant_type][] = [
                'id' => $variant->id,
                'name' => $variant->name,
                'sku' => $variant->sku,
                'price' => $variant->price,
                'compare_price' => $variant->compare_price,
                'stock_quantity' => $variant->stock_quantity,
                'attributes' => $variant->attributes,
                'image' => $variant->primaryImage ? asset('storage/' . $variant->primaryImage->image_path) : null,
            ];
        }
        
        return response()->json($options);
    }

    public function updateVariantStock(ProductVariantStockUpdateRequest $request, $productId, $variantId)
    {
        $product = Product::findOrFail($productId);
        $variant = $product->variants()->findOrFail($variantId);
        
        $variant->update([
            'stock_quantity' => $request->stock_quantity
        ]);
        
        return response()->json([
            'success' => true,
            'stock_quantity' => $variant->stock_quantity,
            'stock_status' => $variant->stock_status,
            'message' => 'Stock updated successfully!'
        ]);
    }
}
