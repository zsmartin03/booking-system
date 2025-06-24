<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Service;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $business = Business::where('id', $request->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $services = $business->services()->with('employees')->get();

        // Get business settings for currency display
        $businessSettings = \App\Models\Setting::getBusinessSettings($business->id);

        return view('services.index', compact('business', 'services', 'businessSettings'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $business = Business::where('id', $request->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $employees = $business->employees()->where('active', true)->get();

        return view('services.create', compact('business', 'employees'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $business = Business::where('id', $request->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'active' => 'boolean',
            'employees' => 'array',
            'employees.*' => 'exists:employees,id',
        ]);

        $validated['business_id'] = $business->id;
        $service = Service::create($validated);

        if (isset($validated['employees'])) {
            // Verify all employees belong to the same business
            $businessEmployees = $business->employees()->whereIn('id', $validated['employees'])->pluck('id');
            $service->employees()->sync($businessEmployees);
        }

        return redirect()->route('services.index', ['business_id' => $business->id])
            ->with('success', 'Service created.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $service = Service::with('employees')->findOrFail($id);
        $business = Business::where('id', $service->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $employees = $business->employees()->where('active', true)->get();

        return view('services.edit', compact('service', 'business', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $service = Service::findOrFail($id);
        $business = Business::where('id', $service->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'active' => 'boolean',
            'employees' => 'array',
            'employees.*' => 'exists:employees,id',
        ]);

        $service->update($validated);

        if (isset($validated['employees'])) {
            // Verify all employees belong to the same business
            $businessEmployees = $business->employees()->whereIn('id', $validated['employees'])->pluck('id');
            $service->employees()->sync($businessEmployees);
        } else {
            $service->employees()->detach();
        }

        return redirect()->route('services.index', ['business_id' => $business->id])
            ->with('success', 'Service updated.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $service = Service::findOrFail($id);
        $business = Business::where('id', $service->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $service->delete();

        return redirect()->route('services.index', ['business_id' => $business->id])
            ->with('success', 'Service deleted.');
    }
}
