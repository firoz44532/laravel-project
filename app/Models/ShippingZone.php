<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'default_cost',
        'express_cost',
        'delivery_days',
        'express_days',
        'is_active',
        'sort_order',
        'cities',
        'areas',
    ];

    protected $casts = [
        'default_cost' => 'decimal:2',
        'express_cost' => 'decimal:2',
        'is_active' => 'boolean',
        'cities' => 'array',
        'areas' => 'array',
    ];

    /**
     * Get the shipping methods for this zone.
     */
    public function shippingMethods()
    {
        return $this->belongsToMany(ShippingMethod::class, 'shipping_zone_rates')
            ->withPivot(['cost', 'additional_cost_per_kg', 'free_shipping_threshold', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Get the active shipping methods for this zone.
     */
    public function activeShippingMethods()
    {
        return $this->shippingMethods()
            ->where('shipping_methods.is_active', true)
            ->where('shipping_zone_rates.is_active', true);
    }

    /**
     * Get the rate for a specific shipping method.
     */
    public function getRateForMethod(ShippingMethod $method)
    {
        return $this->shippingMethods()
            ->where('shipping_method_id', $method->id)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Check if a city is in this zone.
     */
    public function hasCity($city)
    {
        if (empty($this->cities)) {
            return false;
        }

        return in_array(strtolower($city), array_map('strtolower', $this->cities));
    }

    /**
     * Check if an area is in this zone.
     */
    public function hasArea($area)
    {
        if (empty($this->areas)) {
            return false;
        }

        return in_array(strtolower($area), array_map('strtolower', $this->areas));
    }

    /**
     * Scope a query to only include active zones.
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
     * Find zone by city or area.
     */
    public static function findByLocation($city, $area = null)
    {
        $query = static::active()->ordered();

        // First try to find by exact city match
        $zone = $query->whereJsonContains('cities', $city)->first();

        if (!$zone && $area) {
            // Try to find by area match
            $zone = $query->whereJsonContains('areas', $area)->first();
        }

        return $zone;
    }

    /**
     * Get formatted cost.
     */
    public function getFormattedDefaultCostAttribute()
    {
        return '৳' . number_format($this->default_cost, 2);
    }

    /**
     * Get formatted express cost.
     */
    public function getFormattedExpressCostAttribute()
    {
        return '৳' . number_format($this->express_cost, 2);
    }
}
