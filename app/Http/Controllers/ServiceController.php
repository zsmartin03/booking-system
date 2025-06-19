<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Service;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        $business = Business::where('id', $request->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|integer|min:0',
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
            'price' => 'required|integer|min:0',
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
}
