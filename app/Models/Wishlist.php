<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWithProduct($query)
    {
        return $query->with(['product.primaryImage', 'product.category']);
    }

    public static function isInWishlist($productId, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        return static::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }

    public static function addToWishlist($productId, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        if (!static::isInWishlist($productId, $userId)) {
            return static::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
        }
        
        return null;
    }

    public static function removeFromWishlist($productId, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        return static::where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();
    }

    public static function getWishlistCount($userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        return static::where('user_id', $userId)->count();
    }
}
