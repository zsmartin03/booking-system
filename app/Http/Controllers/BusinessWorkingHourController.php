<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessWorkingHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessWorkingHourController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $businessId = $request->query('business_id');
        $business = Business::where('id', $businessId)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $workingHours = $business->workingHours()->orderBy('day_of_week')->get();

        return view('business-working-hours.index', compact('business', 'workingHours'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $businessId = $request->query('business_id');
        $business = Business::where('id', $businessId)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        return view('business-working-hours.create', compact('business'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $business = Business::where('id', $request->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $validated = $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $validated['business_id'] = $business->id;

        BusinessWorkingHour::create($validated);

        return redirect()->route('business-working-hours.index', ['business_id' => $business->id])
            ->with('success', 'Working hour added.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $workingHour = BusinessWorkingHour::findOrFail($id);
        $business = Business::where('id', $workingHour->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        return view('business-working-hours.edit', compact('business', 'workingHour'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $workingHour = BusinessWorkingHour::findOrFail($id);
        $business = Business::where('id', $workingHour->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $validated = $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $workingHour->update($validated);

        return redirect()->route('business-working-hours.index', ['business_id' => $business->id])
            ->with('success', 'Working hour updated.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $workingHour = BusinessWorkingHour::findOrFail($id);
        $business = Business::where('id', $workingHour->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $workingHour->delete();

        return redirect()->route('business-working-hours.index', ['business_id' => $business->id])
            ->with('success', 'Working hour deleted.');
    }
}
