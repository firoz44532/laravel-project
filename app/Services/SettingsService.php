<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    const CACHE_KEY = 'site_settings';
    const CACHE_TTL = 3600; // 1 hour

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::getCachedSettings();
        $setting = $settings[$key] ?? null;
        return $setting ? $setting['value'] : $default;
    }

    /**
     * Get all settings grouped by group
     */
    public static function getByGroup(string $group): array
    {
        $settings = self::getCachedSettings();
        return array_filter($settings, function($setting) use ($group) {
            return $setting['group'] === $group;
        });
    }

    /**
     * Get all public settings
     */
    public static function getPublic(): array
    {
        $settings = self::getCachedSettings();
        return array_filter($settings, function($setting) {
            return $setting['is_public'];
        });
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value): void
    {
        $setting = Setting::firstOrCreate(['key' => $key]);
        $setting->setValue($value);
        $setting->save();
        
        self::clearCache();
    }

    /**
     * Set multiple settings at once
     */
    public static function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $setting = Setting::firstOrCreate(['key' => $key]);
            $setting->setValue($value);
            $setting->save();
        }
        
        self::clearCache();
    }

    /**
     * Create a new setting with full configuration
     */
    public static function create(array $data): Setting
    {
        $setting = Setting::create($data);
        self::clearCache();
        return $setting;
    }

    /**
     * Update an existing setting
     */
    public static function update(string $key, array $data): ?Setting
    {
        $setting = Setting::where('key', $key)->first();
        if ($setting) {
            $setting->update($data);
            self::clearCache();
        }
        return $setting;
    }

    /**
     * Delete a setting
     */
    public static function delete(string $key): bool
    {
        $deleted = Setting::where('key', $key)->delete();
        if ($deleted) {
            self::clearCache();
        }
        return $deleted;
    }

    /**
     * Check if a setting exists
     */
    public static function has(string $key): bool
    {
        $settings = self::getCachedSettings();
        return array_key_exists($key, $settings);
    }

    /**
     * Get boolean setting value
     */
    public static function bool(string $key, bool $default = false): bool
    {
        return (bool) self::get($key, $default);
    }

    /**
     * Get integer setting value
     */
    public static function int(string $key, int $default = 0): int
    {
        return (int) self::get($key, $default);
    }

    /**
     * Get float setting value
     */
    public static function float(string $key, float $default = 0.0): float
    {
        return (float) self::get($key, $default);
    }

    /**
     * Get string setting value
     */
    public static function string(string $key, string $default = ''): string
    {
        return (string) self::get($key, $default);
    }

    /**
     * Get array setting value (for JSON type)
     */
    public static function array(string $key, array $default = []): array
    {
        $value = self::get($key);
        return is_array($value) ? $value : $default;
    }

    /**
     * Get all cached settings
     */
    private static function getCachedSettings(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::all()->mapWithKeys(function ($setting) {
                return [$setting->key => [
                    'value' => $setting->getValue(),
                    'type' => $setting->type,
                    'group' => $setting->group,
                    'title' => $setting->title,
                    'description' => $setting->description,
                    'is_public' => $setting->is_public,
                    'sort_order' => $setting->sort_order,
                ]];
            })->toArray();
        });
    }

    /**
     * Clear the settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Seed default settings
     */
    public static function seedDefaults(): void
    {
        $defaults = [
            // Site Settings
            'site_name' => [
                'key' => 'site_name',
                'value' => 'E-Commerce Store',
                'type' => 'text',
                'group' => 'site',
                'title' => 'Site Name',
                'description' => 'The name of your e-commerce store',
                'is_public' => true,
                'sort_order' => 1,
            ],
            'site_tagline' => [
                'key' => 'site_tagline',
                'value' => 'Quality Products, Great Prices',
                'type' => 'text',
                'group' => 'site',
                'title' => 'Site Tagline',
                'description' => 'Short tagline shown under logo',
                'is_public' => true,
                'sort_order' => 2,
            ],
            'site_description' => [
                'key' => 'site_description',
                'value' => 'A modern e-commerce platform',
                'type' => 'text',
                'group' => 'site',
                'title' => 'Site Description',
                'description' => 'Meta description for your site',
                'is_public' => true,
                'sort_order' => 3,
            ],
            'site_logo' => [
                'key' => 'site_logo',
                'value' => '',
                'type' => 'image',
                'group' => 'site',
                'title' => 'Site Logo',
                'description' => 'URL to your site logo',
                'is_public' => true,
                'sort_order' => 4,
            ],
            'site_favicon' => [
                'key' => 'site_favicon',
                'value' => '',
                'type' => 'image',
                'group' => 'site',
                'title' => 'Site Favicon',
                'description' => 'URL to your site favicon',
                'is_public' => true,
                'sort_order' => 5,
            ],

            // Contact Settings
            'contact_email' => [
                'key' => 'contact_email',
                'value' => 'contact@example.com',
                'type' => 'text',
                'group' => 'contact',
                'title' => 'Contact Email',
                'description' => 'Primary contact email address',
                'is_public' => true,
                'sort_order' => 1,
            ],
            'contact_phone' => [
                'key' => 'contact_phone',
                'value' => '+1234567890',
                'type' => 'text',
                'group' => 'contact',
                'title' => 'Contact Phone',
                'description' => 'Primary contact phone number',
                'is_public' => true,
                'sort_order' => 2,
            ],
            'contact_address' => [
                'key' => 'contact_address',
                'value' => '123 Main St, City, State 12345',
                'type' => 'text',
                'group' => 'contact',
                'title' => 'Contact Address',
                'description' => 'Physical address',
                'is_public' => true,
                'sort_order' => 3,
            ],

            // Social Media Settings
            'social_facebook' => [
                'key' => 'social_facebook',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'title' => 'Facebook URL',
                'description' => 'Facebook page URL',
                'is_public' => true,
                'sort_order' => 1,
            ],
            'social_twitter' => [
                'key' => 'social_twitter',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'title' => 'Twitter URL',
                'description' => 'Twitter profile URL',
                'is_public' => true,
                'sort_order' => 2,
            ],
            'social_instagram' => [
                'key' => 'social_instagram',
                'value' => '',
                'type' => 'text',
                'group' => 'social',
                'title' => 'Instagram URL',
                'description' => 'Instagram profile URL',
                'is_public' => true,
                'sort_order' => 3,
            ],

            // General Settings
            'maintenance_mode' => [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'general',
                'title' => 'Maintenance Mode',
                'description' => 'Put the site in maintenance mode',
                'is_public' => false,
                'sort_order' => 1,
            ],
            'enable_registration' => [
                'key' => 'enable_registration',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'general',
                'title' => 'Enable User Registration',
                'description' => 'Allow new users to register',
                'is_public' => false,
                'sort_order' => 2,
            ],
            'items_per_page' => [
                'key' => 'items_per_page',
                'value' => '12',
                'type' => 'number',
                'group' => 'general',
                'title' => 'Items Per Page',
                'description' => 'Number of items to display per page',
                'is_public' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($defaults as $data) {
            Setting::firstOrCreate(['key' => $data['key']], $data);
        }

        self::clearCache();
    }
}
