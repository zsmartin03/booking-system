<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    /**
     * Display a listing of the businesses for the current provider/admin.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['provider', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $query = Business::with('categories');

        if ($user->role === 'admin') {
        } else {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category)
                    ->orWhere('description', 'like', '%' . $request->category . '%');
            });
        }

        $businesses = $query->get();
        $categories = Category::orderBy('name')->get();

        return view('businesses.index', compact('businesses', 'categories'));
    }

    /**
     * Display a public listing of all businesses (for clients/guests).
     */
    public function publicIndex(Request $request)
    {
        $query = Business::with('categories');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category)
                    ->orWhere('description', 'like', '%' . $request->category . '%');
            });
        }

        $businesses = $query->get();
        $categories = Category::orderBy('name')->get();

        return view('businesses.public_index', compact('businesses', 'categories'));
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

        $categories = Category::orderBy('name')->get();
        return view('businesses.create', compact('categories'));
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
            'categories' => 'array',
            'categories.*' => 'exists:categories,id'
        ]);

        $validated['user_id'] = $user->id;

        $business = Business::create($validated);

        if (isset($validated['categories'])) {
            $business->categories()->attach($validated['categories']);
        }

        return redirect()->route('businesses.index')->with('success', 'Business created successfully.');
    }

    /**
     * Display the specified business (public view).
     */
    public function show(string $id)
    {
        $business = Business::with(['categories', 'services'])->findOrFail($id);

        $relatedBusinesses = collect();
        if ($business->categories->count() > 0) {
            $categoryIds = $business->categories->pluck('id');
            $relatedBusinesses = Business::with('categories')
                ->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('category_id', $categoryIds);
                })
                ->where('id', '!=', $business->id)
                ->limit(6)
                ->get();
        }

        return view('businesses.show', compact('business', 'relatedBusinesses'));
    }

    /**
     * Show the form for editing the specified business.
     */
    public function edit(string $id)
    {
        $user = Auth::user();
        $business = Business::with('categories')->findOrFail($id);

        if ($user->role !== 'admin' && $business->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::orderBy('name')->get();
        return view('businesses.edit', compact('business', 'categories'));
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
            'categories' => 'array',
            'categories.*' => 'exists:categories,id'
        ]);

        $business->update($validated);

        // Sync categories
        if (isset($validated['categories'])) {
            $business->categories()->sync($validated['categories']);
        } else {
            $business->categories()->detach();
        }

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
