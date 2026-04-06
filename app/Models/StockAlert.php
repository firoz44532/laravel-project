<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'threshold_quantity',
        'is_active',
        'is_sent',
        'last_sent_at',
        'alert_type',
    ];

    protected $casts = [
        'threshold_quantity' => 'integer',
        'is_active' => 'boolean',
        'is_sent' => 'boolean',
        'last_sent_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotSent($query)
    {
        return $query->where('is_sent', false);
    }

    public function scopeLowStock($query)
    {
        return $query->whereHas('product', function ($q) {
            $q->whereColumn('products.stock_quantity', '<=', 'stock_alerts.threshold_quantity');
        });
    }

    public function markAsSent()
    {
        $this->update([
            'is_sent' => true,
            'last_sent_at' => now(),
        ]);
    }

    public function resetAlert()
    {
        $this->update([
            'is_sent' => false,
            'last_sent_at' => null,
        ]);
    }
}
