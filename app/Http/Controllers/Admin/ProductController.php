<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use App\Models\Merchant;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;

class ProductController extends Controller
{
    public function index()
    {
        $query = Product::with(['category', 'brand', 'primaryImage', 'merchant']);
        
        // Filter by merchant if specified
        if (request('merchant')) {
            if (request('merchant') == '0') {
                $query->whereNull('merchant_id');
            } else {
                $query->where('merchant_id', request('merchant'));
            }
        }
        
        // Filter by category if specified
        if (request('category')) {
            $query->where('category_id', request('category'));
        }
        
        // Filter by status if specified
        if (request('status') !== null) {
            $query->where('is_active', request('status'));
        }
        
        // Search by name or SKU
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }
        
        $products = $query->latest()->paginate(20);
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $merchants = Merchant::where('status', 'approved')->orderBy('store_name')->get();
        
        return view('admin.products.index', compact('products', 'categories', 'merchants'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(ProductStoreRequest $request)
    {
        // Debug: Log request data
        \Log::info('Product creation request', [
            'has_files' => $request->hasFile('images'),
            'all_files' => $request->allFiles(),
            'images_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
            'request_data' => $request->all(),
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'is_ajax' => $request->ajax(),
            'expects_json' => $request->expectsJson()
        ]);

        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['name']);
        
        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Product::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        // Generate SKU if not provided
        if (empty($validated['sku'])) {
            $validated['sku'] = $this->generateSKU($validated['name']);
        }
        
        $validated['track_stock'] = $request->input('track_stock', false);
        $validated['is_active'] = $request->input('is_active', false);
        $validated['is_featured'] = $request->input('is_featured', false);
        
        // Set automatic sort_order for new featured products
        if ($validated['is_featured']) {
            $minSortOrder = Product::where('is_featured', true)->min('sort_order') ?? 0;
            $validated['sort_order'] = $minSortOrder - 1;
        } else {
            $validated['sort_order'] = 0;
        }

        $product = Product::create($validated);

        // Debug: Log after product creation
        \Log::info('Product created', ['product_id' => $product->id]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            \Log::info('Processing images', ['count' => count($request->file('images'))]);
            
            foreach ($request->file('images') as $index => $image) {
                try {
                    $path = $image->store('products', 'public');
                    
                    \Log::info('Image stored', ['path' => $path]);
                    
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'alt_text' => $product->name,
                        'sort_order' => $index + 1,
                        'is_primary' => $index === 0, // First image is primary
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to store image', [
                        'error' => $e->getMessage(),
                        'file' => $image->getClientOriginalName()
                    ]);
                }
            }
        } else {
            \Log::info('No images found in request');
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Generate SKU from product name
     */
    private function generateSKU($productName)
    {
        // Take first 3 letters of each word and make uppercase
        $words = explode(' ', $productName);
        $sku = '';
        
        foreach ($words as $word) {
            $sku .= strtoupper(substr($word, 0, 3));
        }
        
        // Get the count of existing products with similar SKU pattern
        $count = Product::where('sku', 'like', $sku . '%')->count();
        
        // Add count + 1 to make unique
        return $sku . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'images', 'reviews']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['images']);
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        \Log::info('Product update method called for product ID: ' . $product->id);
        
        // Debug: Log the incoming request data
        \Log::info('Product update request data:', $request->all());
        
        $validated = $request->validated();

        \Log::info('Validated data:', $validated);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Ensure unique slug (excluding current product)
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Product::where('slug', $validated['slug'])->where('id', '!=', $product->id)->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        // Handle boolean fields - they're now validated as boolean
        \Log::info('Boolean field values before processing:', [
            'is_active_raw' => $request->input('is_active'),
            'is_featured_raw' => $request->input('is_featured'),
            'track_stock_raw' => $request->input('track_stock'),
            'has_is_active' => $request->has('is_active'),
            'has_is_featured' => $request->has('is_featured'),
            'has_track_stock' => $request->has('track_stock')
        ]);
        
        $validated['is_active'] = $request->input('is_active', false);
        $validated['is_featured'] = $request->input('is_featured', false);
        
        // Set automatic sort_order for new featured products
        if ($validated['is_featured']) {
            $minSortOrder = Product::where('is_featured', true)->min('sort_order') ?? 0;
            $validated['sort_order'] = $minSortOrder - 1;
        } else {
            $validated['sort_order'] = 0;
        }
        $validated['track_stock'] = $request->input('track_stock', false);
        
        \Log::info('Boolean field values after processing:', [
            'is_active_final' => $validated['is_active'],
            'is_featured_final' => $validated['is_featured'],
            'track_stock_final' => $validated['track_stock']
        ]);
        
        // Set automatic sort_order for newly featured products
        if ($request->input('is_featured', false) && !$product->is_featured) {
            // Product is being marked as featured for the first time
            $minSortOrder = Product::where('is_featured', true)->min('sort_order') ?? 0;
            $validated['sort_order'] = $minSortOrder - 1;
        } elseif (!$request->input('is_featured', false) && $product->is_featured) {
            // Product is being un-featured, reset sort_order
            $validated['sort_order'] = 0;
        } else {
            // Keep existing sort_order or use provided value
            $validated['sort_order'] = $validated['sort_order'] ?? $product->sort_order ?? 0;
        }

        // Remove image data from validated array before database update
        $updateData = collect($validated)->except(['images'])->toArray();

        \Log::info('Final data before update:', $updateData);

        try {
            $product->update($updateData);
            \Log::info('Product updated successfully');

            // Handle new image uploads
            if ($request->hasFile('images')) {
                \Log::info('Processing new images for product update', ['count' => count($request->file('images'))]);
                
                $maxSortOrder = $product->images()->max('sort_order') ?? 0;
                
                foreach ($request->file('images') as $index => $image) {
                    try {
                        $path = $image->store('products', 'public');
                        
                        \Log::info('New image stored for product update', ['path' => $path]);
                        
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                            'alt_text' => $product->name,
                            'sort_order' => $maxSortOrder + $index + 1,
                            'is_primary' => false,
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to store new image during product update', [
                            'error' => $e->getMessage(),
                            'file' => $image->getClientOriginalName(),
                            'product_id' => $product->id
                        ]);
                    }
                }
            }

            // Return JSON response for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product updated successfully!'
                ]);
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product updated successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Product update failed', [
                'error' => $e->getMessage(),
                'product_id' => $product->id,
                'update_data' => $updateData
            ]);
            
            // Return JSON response for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update product. Please try again.'
                ], 500);
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update product. Please try again.');
        }
    }

    public function destroy(Product $product)
    {
        // Delete product images
        $product->images()->delete();
        
        // Delete the product
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    public function uploadImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $request->file('image')->store('products', 'public');
        
        $image = ProductImage::create([
            'product_id' => $product->id,
            'image_path' => $path,
            'alt_text' => $product->name,
            'sort_order' => $product->images()->count() + 1,
            'is_primary' => false,
        ]);

        return response()->json([
            'success' => true,
            'image' => $image,
            'url' => asset('storage/' . $path)
        ]);
    }

    public function deleteImage(ProductImage $image)
    {
        try {
            // Delete the actual file from storage
            if ($image->image_path && \Storage::disk('public')->exists($image->image_path)) {
                \Storage::disk('public')->delete($image->image_path);
            }
            
            // Delete the database record
            $image->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to delete image', [
                'error' => $e->getMessage(),
                'image_id' => $image->id,
                'product_id' => $image->product_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image. Please try again.'
            ], 500);
        }
    }

    public function setPrimaryImage(ProductImage $image)
    {
        try {
            // Debug: Log the image being processed
            \Log::info('Setting primary image for image ID: ' . $image->id);
            
            // Remove primary status from all images of this product
            $image->product->images()->update(['is_primary' => false]);
            
            // Set this image as primary
            $image->update(['is_primary' => true]);
            
            \Log::info('Primary image set successfully for image ID: ' . $image->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Primary image updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to set primary image', [
                'error' => $e->getMessage(),
                'image_id' => $image->id,
                'product_id' => $image->product_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to set primary image. Please try again.'
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        // Build query with same filters as index
        $query = Product::with(['category', 'brand', 'primaryImage', 'merchant']);
        
        // Apply same filters
        if (request('merchant')) {
            if (request('merchant') == '0') {
                $query->whereNull('merchant_id');
            } else {
                $query->where('merchant_id', request('merchant'));
            }
        }
        
        if (request('category')) {
            $query->where('category_id', request('category'));
        }
        
        if (request('status') !== null) {
            $query->where('is_active', request('status'));
        }
        
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }
        
        $products = $query->latest()->get();
        $filename = 'products_export_' . date('Y-m-d');
        
        if ($format === 'pdf') {
            $html = '
            <html>
            <head>
                <style>
                    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
                    h1 { text-align: center; color: #1a202c; margin-bottom: 5px; font-size: 18px; }
                    .meta { text-align: center; color: #718096; margin-bottom: 15px; font-size: 10px; }
                    table { width: 100%; border-collapse: collapse; }
                    th { background-color: #2d3748; color: #fff; padding: 8px 6px; text-align: left; font-size: 10px; }
                    td { padding: 6px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
                    tr:nth-child(even) td { background-color: #f7fafc; }
                    .active { color: #38a169; font-weight: bold; }
                    .inactive { color: #e53e3e; font-weight: bold; }
                    .text-right { text-align: right; }
                </style>
            </head>
            <body>
                <h1>Products Export Report</h1>
                <p class="meta">Generated: ' . date('F j, Y h:i A') . ' | Total Products: ' . $products->count() . '</p>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Stock</th>
                            <th>Status</th>
                            <th>Category</th>
                            <th>Brand</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            foreach ($products as $index => $product) {
                $statusClass = $product->is_active ? 'active' : 'inactive';
                $statusText = $product->is_active ? 'Active' : 'Inactive';
                $html .= '<tr>
                    <td>' . ($index + 1) . '</td>
                    <td>' . e($product->name) . '</td>
                    <td>' . e($product->sku) . '</td>
                    <td class="text-right">৳' . number_format($product->price, 2) . '</td>
                    <td class="text-right">' . $product->stock_quantity . '</td>
                    <td class="' . $statusClass . '">' . $statusText . '</td>
                    <td>' . e($product->category ? $product->category->name : 'N/A') . '</td>
                    <td>' . e($product->brand ? $product->brand->name : 'N/A') . '</td>
                </tr>';
            }
            
            $html .= '</tbody></table></body></html>';
            
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }
        
        if ($format === 'excel') {
            // Tab-separated values with .xls extension — opens natively in Excel
            $tsvData = "Name\tSKU\tPrice\tStock\tStatus\tCategory\tBrand\n";
            
            foreach ($products as $product) {
                $tsvData .= str_replace("\t", " ", $product->name) . "\t";
                $tsvData .= str_replace("\t", " ", $product->sku ?? '') . "\t";
                $tsvData .= $product->price . "\t";
                $tsvData .= $product->stock_quantity . "\t";
                $tsvData .= ($product->is_active ? 'Active' : 'Inactive') . "\t";
                $tsvData .= str_replace("\t", " ", $product->category ? $product->category->name : 'N/A') . "\t";
                $tsvData .= str_replace("\t", " ", $product->brand ? $product->brand->name : 'N/A') . "\n";
            }
            
            return response($tsvData)
                ->header('Content-Type', 'application/vnd.ms-excel')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '.xls"');
        }
        
        // CSV format
        $handle = fopen('php://temp', 'r+');
        // BOM for UTF-8 Excel compatibility
        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, ['Name', 'SKU', 'Price', 'Stock', 'Status', 'Category', 'Brand']);
        
        foreach ($products as $product) {
            fputcsv($handle, [
                $product->name,
                $product->sku,
                $product->price,
                $product->stock_quantity,
                $product->is_active ? 'Active' : 'Inactive',
                $product->category ? $product->category->name : 'N/A',
                $product->brand ? $product->brand->name : 'N/A',
            ]);
        }
        
        rewind($handle);
        $csvData = stream_get_contents($handle);
        fclose($handle);
        
        return response($csvData)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');
    }
}
