<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['business_id', 'key', 'value'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get setting value with default fallback
     */
    public static function getValue(int $businessId, string $key, mixed $default = null): mixed
    {
        $cacheKey = "setting_{$businessId}_{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($businessId, $key, $default) {
            $setting = self::where('business_id', $businessId)
                ->where('key', $key)
                ->first();

            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set setting value
     */
    public static function setValue(int $businessId, string $key, mixed $value): void
    {
        self::updateOrCreate(
            ['business_id' => $businessId, 'key' => $key],
            ['value' => $value]
        );

        // Clear cache
        Cache::forget("setting_{$businessId}_{$key}");
    }

    /**
     * Get all settings for a business
     */
    public static function getBusinessSettings(int $businessId): array
    {
        $cacheKey = "business_settings_{$businessId}";

        return Cache::remember($cacheKey, 3600, function () use ($businessId) {
            $settings = self::where('business_id', $businessId)->get();
            $result = [];

            foreach ($settings as $setting) {
                $result[$setting->key] = $setting->value;
            }

            return array_merge(self::getDefaultSettings(), $result);
        });
    }

    /**
     * Default settings for all businesses
     */
    public static function getDefaultSettings(): array
    {
        return [
            'booking_advance_hours' => 2,        // Minimum hours in advance to book
            'booking_advance_days' => 30,        // Maximum days in advance to book
            'currency' => 'HUF',                 // Currency symbol
            'holiday_mode' => false,             // Temporarily disable bookings
            'maintenance_mode' => false,         // Show maintenance message
            'booking_confirmation_required' => true, // Require provider confirmation
            'allow_cancellation_hours' => 24,   // Hours before appointment cancellation allowed
            'business_timezone' => 'Europe/Budapest', // Business timezone
            'notification_email' => true,       // Send email notifications
            'notification_sms' => false,        // Send SMS notifications
            'booking_buffer_minutes' => 0,      // Buffer time between bookings
        ];
    }

    /**
     * Clear all settings cache for a business
     */
    public static function clearBusinessCache(int $businessId): void
    {
        Cache::forget("business_settings_{$businessId}");

        // Clear individual setting caches
        $defaultSettings = self::getDefaultSettings();
        foreach (array_keys($defaultSettings) as $key) {
            Cache::forget("setting_{$businessId}_{$key}");
        }
    }
}
