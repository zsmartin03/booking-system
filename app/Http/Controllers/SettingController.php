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
            // If no business specified, redirect to businesses list
            return redirect()->route('businesses.index')
                ->with('error', 'Please select a business to manage settings.');
        }

        $business = Business::findOrFail($businessId);

        // Check if user owns this business (for providers) or is admin
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

        // Check if user owns this business (for providers) or is admin
        if (Auth::user()->role === 'provider' && $business->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to business settings.');
        }

        // Debug: Log the request data
        Log::info('Settings update request:', [
            'all_data' => $request->all(),
            'business_id' => $businessId,
            'currency' => $request->input('currency')
        ]);

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
            'notification_email',
            'notification_sms'
        ];

        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field);
        }

        Log::info('Final validated data:', $validated);

        // Save each setting
        foreach ($validated as $key => $value) {
            if ($key !== 'business_id') {
                Log::info("Attempting to save setting: {$key} = " . var_export($value, true));
                Setting::setValue($businessId, $key, $value);
                
                // Verify it was saved
                $savedValue = Setting::getValue($businessId, $key, 'NOT_FOUND');
                Log::info("Verified setting saved: {$key} = " . var_export($savedValue, true));
            }
        }

        // Clear all business settings cache to ensure changes take effect immediately
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

        // Check if user owns this business (for providers) or is admin
        if (Auth::user()->role === 'provider' && $business->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to business settings.');
        }

        // Delete all settings for this business (will fall back to defaults)
        Setting::where('business_id', $businessId)->delete();
        Setting::clearBusinessCache($businessId);

        return redirect()->route('settings.index', ['business_id' => $businessId])
            ->with('success', 'Settings reset to defaults successfully!');
    }
}
