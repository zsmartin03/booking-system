<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    private $apiKey;
    private $baseUrl = 'https://api.geoapify.com/v1/geocode';

    public function __construct()
    {
        $this->apiKey = config('services.geoapify.key');
    }

    public function geocode(string $address): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('Geoapify API key not configured');
            return null;
        }

        try {
            $response = Http::get($this->baseUrl . '/search', [
                'text' => $address,
                'apiKey' => $this->apiKey,
                'limit' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['features']) && count($data['features']) > 0) {
                    $feature = $data['features'][0];
                    $properties = $feature['properties'];
                    $coordinates = $feature['geometry']['coordinates'];

                    return [
                        'latitude' => $coordinates[1],
                        'longitude' => $coordinates[0],
                        'formatted_address' => $properties['formatted'] ?? $address,
                        'valid' => true
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Geocoding error: ' . $e->getMessage());
        }

        return null;
    }

    public function reverseGeocode(float $latitude, float $longitude): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('Geoapify API key not configured');
            return null;
        }

        try {
            $response = Http::get($this->baseUrl . '/reverse', [
                'lat' => $latitude,
                'lon' => $longitude,
                'apiKey' => $this->apiKey,
                'limit' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['features']) && count($data['features']) > 0) {
                    $feature = $data['features'][0];
                    $properties = $feature['properties'];

                    return [
                        'formatted_address' => $properties['formatted'] ?? null,
                        'valid' => true
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Reverse geocoding error: ' . $e->getMessage());
        }

        return null;
    }
}
