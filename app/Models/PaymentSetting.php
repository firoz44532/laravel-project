<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway',
        'display_name',
        'is_active',
        'settings',
        'instructions',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Get a specific setting value
     */
    public function getSetting($key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Set a specific setting value
     */
    public function setSetting($key, $value)
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
    }

    /**
     * Get active payment methods
     */
    public static function getActiveMethods()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->gateway => $setting];
            });
    }

    /**
     * Get payment method by gateway
     */
    public static function getByGateway($gateway)
    {
        return self::where('gateway', $gateway)->first();
    }

    /**
     * Check if gateway is available
     */
    public static function isGatewayActive($gateway)
    {
        return self::where('gateway', $gateway)
            ->where('is_active', true)
            ->exists();
    }
}
