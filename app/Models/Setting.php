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
            'booking_advance_hours' => 2,
            'booking_advance_days' => 30,
            'currency' => 'HUF',
            'holiday_mode' => false,
            'maintenance_mode' => false,
            'booking_confirmation_required' => true,
            'allow_cancellation_hours' => 24,
            'business_timezone' => 'Europe/Budapest',
            'notification_email' => true,
            'notification_sms' => false,
            'booking_buffer_minutes' => 0,
        ];
    }

    /**
     * Clear all settings cache for a business
     */
    public static function clearBusinessCache(int $businessId): void
    {
        Cache::forget("business_settings_{$businessId}");

        $defaultSettings = self::getDefaultSettings();
        foreach (array_keys($defaultSettings) as $key) {
            Cache::forget("setting_{$businessId}_{$key}");
        }
    }
}
