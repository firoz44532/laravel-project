<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'variant_type',
        'price',
        'compare_price',
        'stock_quantity',
        'track_stock',
        'is_active',
        'sort_order',
        'attributes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'track_stock' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'attributes' => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function images()
    {
        return $this->hasMany(ProductVariantImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductVariantImage::class)->where('is_primary', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('variant_type', $type);
    }

    public function getFormattedPriceAttribute()
    {
        return '৳' . number_format($this->price, 2);
    }

    public function getFormattedComparePriceAttribute()
    {
        return $this->compare_price ? '৳' . number_format($this->compare_price, 2) : null;
    }

    public function getStockStatusAttribute()
    {
        if (!$this->track_stock) {
            return 'Available';
        }

        if ($this->stock_quantity > 0) {
            return "In Stock ({$this->stock_quantity})";
        }

        return 'Out of Stock';
    }

    public function getHasDiscountAttribute()
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->has_discount) {
            return 0;
        }

        return round((($this->compare_price - $this->price) / $this->compare_price) * 100, 1);
    }

    public function getDisplayPriceAttribute()
    {
        if ($this->has_discount) {
            return [
                'current' => $this->formatted_price,
                'original' => $this->formatted_compare_price,
                'discount' => $this->discount_percentage . '%'
            ];
        }

        return [
            'current' => $this->formatted_price,
            'original' => null,
            'discount' => null
        ];
    }

    public function getVariantOptionsAttribute()
    {
        $attributes = $this->attributes ?? [];
        
        $options = [];
        
        switch ($this->variant_type) {
            case 'color':
                if (isset($attributes['color'])) {
                    $options['color'] = $attributes['color'];
                }
                if (isset($attributes['color_name'])) {
                    $options['color_name'] = $attributes['color_name'];
                }
                break;
                
            case 'size':
                if (isset($attributes['size'])) {
                    $options['size'] = $attributes['size'];
                }
                if (isset($attributes['size_name'])) {
                    $options['size_name'] = $attributes['size_name'];
                }
                break;
                
            case 'material':
                if (isset($attributes['material'])) {
                    $options['material'] = $attributes['material'];
                }
                break;
                
            case 'style':
                if (isset($attributes['style'])) {
                    $options['style'] = $attributes['style'];
                }
                break;
        }
        
        return $options;
    }

    public function getVariantNameAttribute()
    {
        $name = $this->name;
        $options = $this->variant_options;
        
        if (!empty($options)) {
            $optionTexts = [];
            
            foreach ($options as $key => $value) {
                if ($value) {
                    $optionTexts[] = $value;
                }
            }
            
            if (!empty($optionTexts)) {
                $name .= ' (' . implode(', ', $optionTexts) . ')';
            }
        }
        
        return $name;
    }

    public function isInStock()
    {
        return !$this->track_stock || $this->stock_quantity > 0;
    }

    public function isOutOfStock()
    {
        return $this->track_stock && $this->stock_quantity <= 0;
    }

    public function decreaseStock($quantity = 1)
    {
        if ($this->track_stock) {
            $this->stock_quantity = max(0, $this->stock_quantity - $quantity);
            $this->save();
        }
    }

    public function increaseStock($quantity = 1)
    {
        if ($this->track_stock) {
            $this->stock_quantity += $quantity;
            $this->save();
        }
    }

    public function setStock($quantity)
    {
        if ($this->track_stock) {
            $this->stock_quantity = max(0, $quantity);
            $this->save();
        }
    }

    public static function createVariant($product, $data)
    {
        $variant = new self([
            'product_id' => $product->id,
            'sku' => $data['sku'] ?? self::generateSKU($product),
            'name' => $data['name'],
            'variant_type' => $data['variant_type'] ?? 'simple',
            'price' => $data['price'] ?? $product->price,
            'compare_price' => $data['compare_price'] ?? null,
            'stock_quantity' => $data['stock_quantity'] ?? 0,
            'track_stock' => $data['track_stock'] ?? true,
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
            'attributes' => $data['attributes'] ?? [],
        ]);

        $variant->save();
        
        return $variant;
    }

    public static function generateSKU($product)
    {
        $baseSKU = $product->sku ?? strtoupper(substr($product->name, 0, 3));
        $random = strtoupper(Str::random(4));
        
        return $baseSKU . '-' . $random;
    }

    public function getFullSKU()
    {
        return $this->sku;
    }

    public function getURL()
    {
        return route('products.show', $this->product->slug) . '?variant=' . $this->id;
    }
}
