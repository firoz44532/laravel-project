<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'image_path',
        'alt_text',
        'sort_order',
        'is_primary',
        'product_id',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function getImageUrlAttribute()
    {
        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }
        
        // If it starts with 'images/', it's in public directory, don't add storage prefix
        if (str_starts_with($this->image_path, 'images/')) {
            return 'http://localhost:8000/' . $this->image_path;
        }
        
        return 'http://localhost:8000/storage/' . $this->image_path;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
