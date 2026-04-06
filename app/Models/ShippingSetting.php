<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Scope a query to only include settings from a specific group.
     */
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope a query to only include public settings.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get setting value by key.
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return match($setting->type) {
            'boolean' => (bool) $setting->value,
            'number' => is_numeric($setting->value) ? (float) $setting->value : $default,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set setting value by key.
     */
    public static function setValue($key, $value, $type = 'text', $description = null, $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'description' => $description,
                'group' => $group,
            ]
        );
    }

    /**
     * Get all settings as key-value pairs.
     */
    public static function getAllSettings()
    {
        $settings = [];
        static::all()->each(function ($setting) use (&$settings) {
            $settings[$setting->key] = match($setting->type) {
                'boolean' => (bool) $setting->value,
                'number' => is_numeric($setting->value) ? (float) $setting->value : $setting->value,
                'json' => json_decode($setting->value, true),
                default => $setting->value,
            };
        });
        
        return $settings;
    }

    /**
     * Get settings by group.
     */
    public static function getGroupSettings($group)
    {
        return static::group($group)->get()->mapWithKeys(function ($setting) {
            $value = match($setting->type) {
                'boolean' => (bool) $setting->value,
                'number' => is_numeric($setting->value) ? (float) $setting->value : $setting->value,
                'json' => json_decode($setting->value, true),
                default => $setting->value,
            };
            
            return [$setting->key => $value];
        });
    }
}
