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
        $businessId = $request->query('business_id');
        $business = Business::where('id', $businessId)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $services = $business->services()->with('employees')->get();

        return view('services.index', compact('business', 'services'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $businessId = $request->query('business_id');
        $business = Business::where('id', $businessId)
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
            'price' => 'required|integer|min:0',
            'duration' => 'required|integer|min:1',
            'active' => 'boolean',
            'employees' => 'array',
            'employees.*' => 'exists:employees,id',
        ]);

        $validated['business_id'] = $business->id;
        $service = Service::create($validated);

        if (isset($validated['employees'])) {
            $service->employees()->sync($validated['employees']);
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

        return view('services.edit', compact('business', 'service', 'employees'));
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
            $service->employees()->sync($validated['employees']);
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

        $service->employees()->detach();
        $service->delete();

        return redirect()->route('services.index', ['business_id' => $business->id])
            ->with('success', 'Service deleted.');
    }
}
