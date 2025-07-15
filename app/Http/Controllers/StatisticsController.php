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
    public function index(Request $request, Business $business)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['provider', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        // Check if user has access to this business
        if ($user->role === 'provider' && $business->user_id !== $user->id) {
            abort(403, 'You do not have access to this business.');
        }

        // Get available businesses for the business selector (if needed)
        if ($user->role === 'admin') {
            $businesses = Business::with(['user'])->orderBy('name')->get();
        } else {
            $businesses = Business::where('user_id', $user->id)->orderBy('name')->get();
        }

        if ($businesses->isEmpty()) {
            return redirect()->route('businesses.create')->with('error', 'Please create a business first to view statistics.');
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
    public function getData(Request $request, Business $business)
    {
        try {
            $user = Auth::user();

            if (!in_array($user->role, ['provider', 'admin'])) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Check if user has access to this business
            if ($user->role === 'provider' && $business->user_id !== $user->id) {
                return response()->json(['error' => 'You do not have access to this business.'], 403);
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

    /**
     * Redirect to first available business statistics
     */
    public function redirect()
    {
        $user = Auth::user();

        if (!in_array($user->role, ['provider', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        // Get available businesses based on user role
        if ($user->role === 'admin') {
            $businesses = Business::orderBy('name')->get();
        } else {
            $businesses = Business::where('user_id', $user->id)->orderBy('name')->get();
        }

        if ($businesses->isEmpty()) {
            return redirect()->route('businesses.create')->with('error', 'Please create a business first to view statistics.');
        }

        // Redirect to first available business statistics
        return redirect()->route('statistics.index', ['business' => $businesses->first()]);
    }
}
