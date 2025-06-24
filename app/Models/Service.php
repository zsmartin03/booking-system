<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['business_id', 'name', 'description', 'price', 'duration', 'active'];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'service_employee');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all available currencies for the dropdown
     */
    public static function getAvailableCurrencies()
    {
        return [
            'USD' => 'US Dollar ($)',
            'EUR' => 'Euro (€)',
            'GBP' => 'British Pound (£)',
            'JPY' => 'Japanese Yen (¥)',
            'CNY' => 'Chinese Yuan (¥)',
            'KRW' => 'South Korean Won (₩)',
            'INR' => 'Indian Rupee (₹)',
            'RUB' => 'Russian Ruble (₽)',
            'HUF' => 'Hungarian Forint (Ft)',
            'CZK' => 'Czech Koruna (Kč)',
            'PLN' => 'Polish Złoty (zł)',
            'SEK' => 'Swedish Krona (kr)',
            'NOK' => 'Norwegian Krone (kr)',
            'DKK' => 'Danish Krone (kr)',
            'CHF' => 'Swiss Franc (CHF)',
            'CAD' => 'Canadian Dollar (CAD)',
            'AUD' => 'Australian Dollar (AUD)',
            'NZD' => 'New Zealand Dollar (NZD)',
            'SGD' => 'Singapore Dollar (SGD)',
            'HKD' => 'Hong Kong Dollar (HKD)',
        ];
    }

    /**
     * Format price with currency based on currency type
     */
    public static function formatPrice($price, $currency)
    {
        // Define currencies that use symbols and should be placed before the price
        $symbolCurrencies = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CNY' => '¥',
            'KRW' => '₩',
            'INR' => '₹',
            'RUB' => '₽',
        ];

        // Define currencies that use text and should be placed after the price
        $textCurrencies = [
            'HUF' => 'Ft',
            'CZK' => 'Kč',
            'PLN' => 'zł',
            'SEK' => 'kr',
            'NOK' => 'kr',
            'DKK' => 'kr',
        ];

        $formattedPrice = number_format($price, 2);

        if (isset($symbolCurrencies[$currency])) {
            return $symbolCurrencies[$currency] . $formattedPrice;
        } elseif (isset($textCurrencies[$currency])) {
            return $formattedPrice . ' ' . $textCurrencies[$currency];
        } else {
            // Default: use currency code after price
            return $formattedPrice . ' ' . $currency;
        }
    }
}
