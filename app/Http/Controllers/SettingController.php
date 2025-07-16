<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Setting;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Display settings for a business
     */
    public function index(Request $request)
    {
        $businessId = $request->business_id;

        if (!$businessId) {
            return redirect()->route('businesses.index')
                ->with('error', 'Please select a business to manage settings.');
        }

        $business = Business::findOrFail($businessId);

        if (Auth::user()->role === 'provider' && $business->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to business settings.');
        }

        $settings = Setting::getBusinessSettings($businessId);
        $availableCurrencies = Service::getAvailableCurrencies();

        return view('settings.index', compact('business', 'settings', 'availableCurrencies'));
    }

    /**
     * Update business settings
     */
    public function update(Request $request)
    {
        $businessId = $request->input('business_id');
        $business = Business::findOrFail($businessId);

        if (Auth::user()->role === 'provider' && $business->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to business settings.');
        }

        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'booking_advance_hours' => 'required|integer|min:0|max:168', // Max 1 week
            'booking_advance_days' => 'required|integer|min:1|max:365',  // Max 1 year
            'currency' => 'required|string|in:' . implode(',', array_keys(Service::getAvailableCurrencies())),
            'allow_cancellation_hours' => 'required|integer|min:0|max:168',
            'business_timezone' => 'required|string|max:50',
            'booking_buffer_minutes' => 'required|integer|min:0|max:120',
        ]);

        // Handle boolean fields separately (checkboxes)
        $booleanFields = [
            'holiday_mode',
            'maintenance_mode',
            'booking_confirmation_required',
        ];

        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field);
        }

        foreach ($validated as $key => $value) {
            if ($key !== 'business_id') {
                Setting::setValue($businessId, $key, $value);

                $savedValue = Setting::getValue($businessId, $key, 'NOT_FOUND');
            }
        }

        Setting::clearBusinessCache($businessId);

        return redirect()->route('settings.index', ['business_id' => $businessId])
            ->with('success', 'Settings updated successfully!');
    }

    /**
     * Reset settings to defaults
     */
    public function reset(Request $request)
    {
        $businessId = $request->input('business_id');
        $business = Business::findOrFail($businessId);

        if (Auth::user()->role === 'provider' && $business->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to business settings.');
        }

        Setting::where('business_id', $businessId)->delete();
        Setting::clearBusinessCache($businessId);

        return redirect()->route('settings.index', ['business_id' => $businessId])
            ->with('success', 'Settings reset to defaults successfully!');
    }
}
