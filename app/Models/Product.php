<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'price',
        'compare_price',
        'cost_price',
        'stock_quantity',
        'track_stock',
        'is_active',
        'is_featured',
        'sort_order',
        'weight',
        'attributes',
        'category_id',
        'brand_id',
        'merchant_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'track_stock' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'attributes' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->reviews()->where('is_approved', true);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    public function isInStock()
    {
        return !$this->track_stock || $this->stock_quantity > 0;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round((($this->compare_price - $this->price) / $this->compare_price) * 100);
        }
        return 0;
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function stockAlert()
    {
        return $this->hasOne(StockAlert::class);
    }

    public function createStockAlert($threshold = 10)
    {
        return $this->stockAlert()->create([
            'threshold_quantity' => $threshold,
            'is_active' => true,
            'alert_type' => 'email',
        ]);
    }

    public function updateStock($newQuantity, $action = 'adjustment', $reason = 'Manual update', $notes = null, $userId = null)
    {
        $oldQuantity = $this->stock_quantity;
        $quantityChange = $newQuantity - $oldQuantity;

        DB::beginTransaction();
        try {
            // Update product stock
            $this->update(['stock_quantity' => $newQuantity]);

            // Create inventory log
            InventoryLog::create([
                'product_id' => $this->id,
                'user_id' => $userId ?? auth()->id(),
                'action' => $action,
                'quantity_before' => $oldQuantity,
                'quantity_after' => $newQuantity,
                'quantity_change' => $quantityChange,
                'reason' => $reason,
                'notes' => $notes,
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function checkLowStockAlert()
    {
        if ($this->stockAlert && $this->stockAlert->is_active) {
            if ($this->stock_quantity <= $this->stockAlert->threshold_quantity) {
                return true;
            }
        }
        return false;
    }
}
