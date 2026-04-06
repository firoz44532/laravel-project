<?php

namespace App\Services;

use App\Models\ShippingZone;
use App\Models\ShippingMethod;
use App\Models\ShippingSetting;
use App\Models\Address;
use App\Models\Cart;

class ShippingService
{
    /**
     * Calculate shipping cost for given parameters.
     */
    public function calculateShipping($cartTotal = 0, $weight = 0, $city = null, $area = null)
    {
        // Get shipping settings
        $settings = ShippingSetting::getAllSettings();
        
        // Check for free shipping
        $freeShippingThreshold = $settings['free_shipping_threshold'] ?? config('shipping.free_shipping_threshold', 2000);
        if ($cartTotal >= $freeShippingThreshold) {
            return [
                'cost' => 0,
                'method' => 'free_shipping',
                'method_name' => 'Free Shipping',
                'estimated_days' => '2-3 business days',
                'message' => 'Free shipping on orders over ৳' . number_format($freeShippingThreshold, 2),
            ];
        }

        // Find shipping zone
        $zone = $this->findShippingZone($city, $area);
        
        if (!$zone) {
            // Use default shipping cost
            $defaultCost = $settings['default_shipping_cost'] ?? config('shipping.default_shipping_cost', 50);
            return [
                'cost' => $defaultCost,
                'method' => 'standard',
                'method_name' => 'Standard Delivery',
                'estimated_days' => '3-5 business days',
                'message' => 'Standard shipping rate',
            ];
        }

        // Get default shipping method for the zone
        $method = ShippingMethod::findByCode('standard');
        
        if (!$method) {
            return [
                'cost' => $zone->default_cost,
                'method' => 'standard',
                'method_name' => 'Standard Delivery',
                'estimated_days' => $zone->delivery_days ?? '2-3 business days',
                'message' => $zone->name . ' standard shipping',
            ];
        }

        // Get rate for zone and method
        $rate = $zone->getRateForMethod($method);
        $cost = $rate ? $rate->pivot->cost : $zone->default_cost;

        // Apply weight-based additional cost if enabled
        if ($settings['weight_based_enabled'] ?? false) {
            $cost += $this->calculateWeightBasedCost($weight);
        }

        // Apply order value-based adjustment if enabled
        if ($settings['order_value_based_enabled'] ?? false) {
            $cost += $this->calculateOrderValueBasedCost($cartTotal);
        }

        return [
            'cost' => $cost,
            'method' => $method->code,
            'method_name' => $method->name,
            'estimated_days' => $method->estimated_days ?? $zone->delivery_days,
            'zone_name' => $zone->name,
            'message' => "Shipping to {$zone->name}",
        ];
    }

    /**
     * Get available shipping methods for a location.
     */
    public function getAvailableShippingMethods($city = null, $area = null, $cartTotal = 0)
    {
        $zone = $this->findShippingZone($city, $area);
        $methods = [];
        
        if (!$zone) {
            // Return default methods
            return [
                [
                    'code' => 'standard',
                    'name' => 'Standard Delivery',
                    'cost' => ShippingSetting::getValue('default_shipping_cost', config('shipping.default_shipping_cost', 50)),
                    'estimated_days' => '3-5 business days',
                    'description' => 'Standard delivery service',
                ],
            ];
        }

        $activeMethods = $zone->activeShippingMethods()->ordered()->get();
        
        foreach ($activeMethods as $method) {
            $cost = $method->pivot->cost;
            
            // Check for free shipping
            $freeShippingThreshold = ShippingSetting::getValue('free_shipping_threshold', config('shipping.free_shipping_threshold', 2000));
            if ($cartTotal >= $freeShippingThreshold) {
                $cost = 0;
            }

            $methods[] = [
                'code' => $method->code,
                'name' => $method->name,
                'cost' => $cost,
                'estimated_days' => $method->estimated_days ?? $zone->delivery_days,
                'description' => $method->description,
                'tracking_available' => $method->tracking_available,
            ];
        }

        return $methods;
    }

    /**
     * Calculate tax for shipping.
     */
    public function calculateShippingTax($shippingCost)
    {
        $settings = ShippingSetting::getAllSettings();
        
        if (!($settings['tax_enabled'] ?? config('shipping.tax.enabled', true))) {
            return 0;
        }

        if (!($settings['shipping_taxable'] ?? config('shipping.tax.shipping_taxable', true))) {
            return 0;
        }

        $vatRate = $settings['vat_rate'] ?? config('shipping.tax.vat_rate', 15);
        
        return ($shippingCost * $vatRate) / 100;
    }

    /**
     * Calculate total tax for order.
     */
    public function calculateOrderTax($subtotal, $shippingCost = 0)
    {
        $settings = ShippingSetting::getAllSettings();
        
        if (!($settings['tax_enabled'] ?? config('shipping.tax.enabled', true))) {
            return 0;
        }

        $vatRate = $settings['vat_rate'] ?? config('shipping.tax.vat_rate', 15);
        $taxableAmount = $subtotal;

        // Add shipping cost to taxable amount if shipping is taxable
        if ($settings['shipping_taxable'] ?? config('shipping.tax.shipping_taxable', true)) {
            $taxableAmount += $shippingCost;
        }

        return ($taxableAmount * $vatRate) / 100;
    }

    /**
     * Find shipping zone by city/area.
     */
    private function findShippingZone($city, $area = null)
    {
        if (!$city) {
            return null;
        }

        return ShippingZone::findByLocation($city, $area);
    }

    /**
     * Calculate weight-based additional cost.
     */
    private function calculateWeightBasedCost($weight)
    {
        $tiers = config('shipping.weight_based.tiers', []);
        $additionalCost = 0;

        foreach ($tiers as $tier) {
            if ($weight > $tier['min_weight'] && 
                ($tier['max_weight'] === null || $weight <= $tier['max_weight'])) {
                $additionalCost = $tier['additional_cost'];
                break;
            }
        }

        return $additionalCost;
    }

    /**
     * Calculate order value-based additional cost.
     */
    private function calculateOrderValueBasedCost($orderValue)
    {
        $tiers = config('shipping.order_value_based.tiers', []);
        $additionalCost = 0;

        foreach ($tiers as $tier) {
            if ($orderValue > $tier['min_amount'] && 
                ($tier['max_amount'] === null || $orderValue <= $tier['max_amount'])) {
                $additionalCost = $tier['additional_cost'];
                break;
            }
        }

        return $additionalCost;
    }

    /**
     * Get shipping zones for admin.
     */
    public function getShippingZones()
    {
        return ShippingZone::with(['activeShippingMethods'])->ordered()->get();
    }

    /**
     * Get shipping methods for admin.
     */
    public function getShippingMethods()
    {
        return ShippingMethod::with(['activeShippingZones'])->ordered()->get();
    }

    /**
     * Get shipping settings for admin.
     */
    public function getShippingSettings()
    {
        return ShippingSetting::orderBy('group')->orderBy('key')->get();
    }

    /**
     * Update shipping setting.
     */
    public function updateShippingSetting($key, $value)
    {
        return ShippingSetting::setValue($key, $value);
    }

    /**
     * Format shipping cost for display.
     */
    public function formatShippingCost($cost)
    {
        return $cost == 0 ? 'Free' : '৳' . number_format($cost, 2);
    }

    /**
     * Get estimated delivery date.
     */
    public function getEstimatedDeliveryDate($estimatedDays, $startDate = null)
    {
        $startDate = $startDate ?: now();
        
        if (is_numeric($estimatedDays)) {
            return $startDate->addDays($estimatedDays)->format('M j, Y');
        }

        // Handle ranges like "2-3 business days"
        if (preg_match('/(\d+)-(\d+)/', $estimatedDays, $matches)) {
            $minDays = (int) $matches[1];
            $maxDays = (int) $matches[2];
            
            $minDate = $startDate->addWeekdays($minDays);
            $maxDate = $startDate->addWeekdays($maxDays - $minDays);
            
            return $minDate->format('M j') . ' - ' . $maxDate->format('M j, Y');
        }

        return $startDate->addDays(3)->format('M j, Y'); // Default fallback
    }
}
