<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    /**
     * Display a listing of the businesses for the current provider/admin.
     */
    public function index()
    {
        $user = Auth::user();

        // Only providers and admins can view their businesses
        if (!in_array($user->role, ['provider', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        // Admins see all businesses, providers see their own
        $businesses = $user->role === 'admin'
            ? Business::all()
            : Business::where('user_id', $user->id)->get();

        return view('businesses.index', compact('businesses'));
    }

    /**
     * Display a public listing of all businesses (for clients/guests).
     */
    public function publicIndex()
    {
        $businesses = Business::all();
        return view('businesses.public_index', compact('businesses'));
    }

    /**
     * Show the form for creating a new business.
     */
    public function create()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['provider', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }
        return view('businesses.create');
    }

    /**
     * Store a newly created business in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['provider', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'email' => 'required|email',
            'website' => 'nullable|url',
            'logo' => 'nullable|string',
        ]);

        $validated['user_id'] = $user->id;

        $business = Business::create($validated);

        return redirect()->route('businesses.index')->with('success', 'Business created successfully.');
    }

    /**
     * Display the specified business (public view).
     */
    public function show(string $id)
    {
        $business = Business::findOrFail($id);
        return view('businesses.show', compact('business'));
    }

    /**
     * Show the form for editing the specified business.
     */
    public function edit(string $id)
    {
        $user = Auth::user();
        $business = Business::findOrFail($id);

        if ($user->role !== 'admin' && $business->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('businesses.edit', compact('business'));
    }

    /**
     * Update the specified business in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $business = Business::findOrFail($id);

        if ($user->role !== 'admin' && $business->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'email' => 'required|email',
            'website' => 'nullable|url',
            'logo' => 'nullable|string',
        ]);

        $business->update($validated);

        return redirect()->route('businesses.index')->with('success', 'Business updated successfully.');
    }

    /**
     * Remove the specified business from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $business = Business::findOrFail($id);

        if ($user->role !== 'admin' && $business->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $business->delete();

        return redirect()->route('businesses.index')->with('success', 'Business deleted successfully.');
    }
}
