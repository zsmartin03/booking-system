<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StatisticsController extends Controller
{
    /**
     * Display business statistics page
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['provider', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        // Get available businesses based on user role
        if ($user->role === 'admin') {
            $businesses = Business::with(['user'])->orderBy('name')->get();
        } else {
            $businesses = Business::where('user_id', $user->id)->orderBy('name')->get();
        }

        if ($businesses->isEmpty()) {
            return redirect()->route('businesses.create')->with('error', 'Please create a business first to view statistics.');
        }

        // Get selected business or default to first one
        $selectedBusinessId = $request->get('business_id', $businesses->first()->id);
        $business = $businesses->where('id', $selectedBusinessId)->first();
        
        if (!$business) {
            // If selected business not found or not accessible, use first available
            $business = $businesses->first();
            $selectedBusinessId = $business->id;
        }

        $period = $request->get('period', 'month'); // month, week, day

        // Get statistics data
        $totalBookings = $business->total_bookings;
        $totalRevenue = $business->total_revenue;
        $totalCustomers = $business->total_customers;
        $mostBookedServices = $business->most_booked_services;

        // Get chart data
        $bookingsData = $business->getBookingsPerPeriod($period);
        $revenueData = $business->getRevenuePerPeriod($period);

        // Format data for charts
        $chartData = [
            'bookings' => [
                'labels' => $bookingsData->pluck('period')->reverse()->values(),
                'data' => $bookingsData->pluck('count')->reverse()->values()
            ],
            'revenue' => [
                'labels' => $revenueData->pluck('period')->reverse()->values(),
                'data' => $revenueData->pluck('revenue')->reverse()->values()
            ]
        ];

        return view('statistics.index', compact(
            'business',
            'businesses',
            'selectedBusinessId',
            'totalBookings',
            'totalRevenue',
            'totalCustomers',
            'mostBookedServices',
            'chartData',
            'period'
        ));
    }

    /**
     * Get statistics data for AJAX requests
     */
    public function getData(Request $request)
    {
        try {
            $user = Auth::user();

            if (!in_array($user->role, ['provider', 'admin'])) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Get business based on user role and selection
            $businessId = $request->get('business_id');
            
            if (!$businessId) {
                return response()->json(['error' => 'Business ID is required'], 400);
            }
            
            if ($user->role === 'admin') {
                $business = Business::findOrFail($businessId);
            } else {
                $business = Business::where('user_id', $user->id)->where('id', $businessId)->firstOrFail();
            }

            $period = $request->get('period', 'month');

            $bookingsData = $business->getBookingsPerPeriod($period);
            $revenueData = $business->getRevenuePerPeriod($period);

            $response = [
                'bookings' => [
                    'labels' => $bookingsData->pluck('period')->reverse()->values(),
                    'data' => $bookingsData->pluck('count')->reverse()->values()
                ],
                'revenue' => [
                    'labels' => $revenueData->pluck('period')->reverse()->values(),
                    'data' => $revenueData->pluck('revenue')->reverse()->values()
                ]
            ];
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('Statistics getData error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }
}
