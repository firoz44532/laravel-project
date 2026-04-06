<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'estimated_days',
        'base_cost',
        'is_active',
        'tracking_available',
        'sort_order',
        'settings',
    ];

    protected $casts = [
        'base_cost' => 'decimal:2',
        'is_active' => 'boolean',
        'tracking_available' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Get the shipping zones for this method.
     */
    public function shippingZones()
    {
        return $this->belongsToMany(ShippingZone::class, 'shipping_zone_rates')
            ->withPivot(['cost', 'additional_cost_per_kg', 'free_shipping_threshold', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Get the active shipping zones for this method.
     */
    public function activeShippingZones()
    {
        return $this->shippingZones()
            ->where('shipping_zones.is_active', true)
            ->where('shipping_zone_rates.is_active', true);
    }

    /**
     * Get the rate for a specific shipping zone.
     */
    public function getRateForZone(ShippingZone $zone)
    {
        return $this->shippingZones()
            ->where('shipping_zone_id', $zone->id)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Scope a query to only include active methods.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Find method by code.
     */
    public static function findByCode($code)
    {
        return static::active()->where('code', $code)->first();
    }

    /**
     * Get formatted base cost.
     */
    public function getFormattedBaseCostAttribute()
    {
        return '৳' . number_format($this->base_cost, 2);
    }

    /**
     * Get setting value.
     */
    public function getSetting($key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Set setting value.
     */
    public function setSetting($key, $value)
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->settings = $settings;
    }
}
