<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Category;
use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        $categories = Category::orderBy('slug')->get();

        return view('businesses.index', compact('businesses', 'categories'));
    }

    /**
     * Display a public listing of all businesses (for clients/guests).
     */
    public function publicIndex(Request $request)
    {
        $query = Business::with('categories')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category)
                    ->orWhere('description', 'like', '%' . $request->category . '%');
            });
        }

        if ($request->filled('min_rating')) {
            $minRating = floatval($request->min_rating);
            $query->having('reviews_avg_rating', '>=', $minRating)
                ->where('reviews_count', '>', 0);
        }

        $sortBy = $request->get('sort', 'name');
        switch ($sortBy) {
            case 'rating_high':
                $query->orderBy('reviews_avg_rating', 'desc')
                    ->orderBy('name', 'asc');
                break;
            case 'rating_low':
                $query->orderBy('reviews_avg_rating', 'asc')
                    ->orderBy('name', 'asc');
                break;
            case 'reviews_count':
                $query->orderBy('reviews_count', 'desc')
                    ->orderBy('name', 'asc');
                break;
            case 'name':
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $businesses = $query->paginate(12)->appends($request->query());

        $businesses->getCollection()->transform(function ($business) {
            $business->average_rating = $business->reviews_avg_rating ?? 0;
            $business->reviews_count = $business->reviews_count ?? 0;
            return $business;
        });

        $categories = Category::orderBy('slug')->get();

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

        $categories = Category::orderBy('slug')->get();
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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        $validated['user_id'] = $user->id;

        if ($request->hasFile('logo')) {
            try {
                $logoPath = $request->file('logo')->store('business-logos', 'public');
                $validated['logo'] = $logoPath;
            } catch (\Exception $e) {
                return back()->withErrors(['logo' => 'Failed to upload logo. Please try again.'])->withInput();
            }
        }

        // Handle geocoding
        $geocodingService = app(GeocodingService::class);

        if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
            // If lat/lng provided, use them and reverse geocode for formatted address
            $result = $geocodingService->reverseGeocode($validated['latitude'], $validated['longitude']);
            if ($result && $result['formatted_address']) {
                $validated['address'] = $result['formatted_address'];
            }
        } else {
            // If no coordinates provided, geocode the address
            $result = $geocodingService->geocode($validated['address']);
            if ($result) {
                $validated['latitude'] = $result['latitude'];
                $validated['longitude'] = $result['longitude'];
                $validated['address'] = $result['formatted_address'];
            } else {
                // If geocoding fails, don't allow business creation
                return back()->withErrors(['address' => 'Could not find a valid location for this address. Please try a different address.'])->withInput();
            }
        }

        $business = Business::create($validated);

        if (isset($validated['categories'])) {
            $business->categories()->attach($validated['categories']);
        }

        return redirect()->route('businesses.index')->with('success', 'Business created successfully.');
    }

    /**
     * Display the specified business (public view).
     */
    public function show(string $id, Request $request)
    {
        $business = Business::with([
            'categories',
            'services',
        ])->findOrFail($id);

        $sortBy = $request->get('sort', 'helpful'); // default to most helpful
        $filterRating = $request->get('rating');
        $filterBooking = $request->get('booking');
        $page = $request->get('page', 1);

        $userReview = null;

        if (Auth::check()) {
            $userReview = $business->reviews()
                ->where('user_id', Auth::id())
                ->with(['user', 'response.user', 'votes'])
                ->first();
        }

        $reviewsQuery = $business->reviews()
            ->with(['user', 'response.user', 'votes'])
            ->when(Auth::check(), function ($query) {
                $query->where('reviews.user_id', '!=', Auth::id());
            });

        if ($filterRating) {
            $reviewsQuery->where('rating', $filterRating);
        }

        if ($filterBooking === 'verified') {
            $reviewsQuery->where('has_booking', true);
        }

        switch ($sortBy) {
            case 'rating_high':
                $reviewsQuery->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'rating_low':
                $reviewsQuery->orderBy('rating', 'asc')->orderBy('created_at', 'desc');
                break;
            case 'date_new':
                $reviewsQuery->orderBy('created_at', 'desc');
                break;
            case 'date_old':
                $reviewsQuery->orderBy('created_at', 'asc');
                break;
            case 'helpful':
            default:
                // Sort by best like/dislike ratio using raw SQL for efficiency
                $reviewsQuery->leftJoin('review_votes', 'reviews.id', '=', 'review_votes.review_id')
                    ->selectRaw('reviews.*,
                        COALESCE(SUM(CASE WHEN review_votes.is_upvote = 1 THEN 1 ELSE 0 END), 0) as upvotes_count,
                        COALESCE(SUM(CASE WHEN review_votes.is_upvote = 0 THEN 1 ELSE 0 END), 0) as downvotes_count,
                        (COALESCE(SUM(CASE WHEN review_votes.is_upvote = 1 THEN 1 ELSE 0 END), 0) -
                         COALESCE(SUM(CASE WHEN review_votes.is_upvote = 0 THEN 1 ELSE 0 END), 0)) as net_votes')
                    ->groupBy('reviews.id')
                    ->orderBy('net_votes', 'desc')
                    ->orderBy('reviews.created_at', 'desc');
                break;
        }

        $otherReviews = $reviewsQuery->paginate(10, ['*'], 'page', $page)->appends($request->query());

        if ($request->ajax()) {
            return response()->json([
                'html' => view('businesses.partials.reviews-list', compact('otherReviews', 'business'))->render(),
                'pagination' => [
                    'current_page' => $otherReviews->currentPage(),
                    'last_page' => $otherReviews->lastPage(),
                    'per_page' => $otherReviews->perPage(),
                    'total' => $otherReviews->total(),
                    'from' => $otherReviews->firstItem(),
                    'to' => $otherReviews->lastItem(),
                ]
            ]);
        }

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

        return view('businesses.show', compact('business', 'relatedBusinesses', 'userReview', 'otherReviews'));
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

        $categories = Category::orderBy('slug')->get();
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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            try {
                // Delete old logo if it exists
                if ($business->logo) {
                    Storage::disk('public')->delete($business->logo);
                }

                $logoPath = $request->file('logo')->store('business-logos', 'public');
                $validated['logo'] = $logoPath;
            } catch (\Exception $e) {
                return back()->withErrors(['logo' => 'Failed to upload logo. Please try again.'])->withInput();
            }
        }

        // Handle geocoding
        $geocodingService = app(GeocodingService::class);

        if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
            // If lat/lng provided, use them and reverse geocode for formatted address
            $result = $geocodingService->reverseGeocode($validated['latitude'], $validated['longitude']);
            if ($result && $result['formatted_address']) {
                $validated['address'] = $result['formatted_address'];
            }
        } else {
            // If no coordinates provided, geocode the address
            $result = $geocodingService->geocode($validated['address']);
            if ($result) {
                $validated['latitude'] = $result['latitude'];
                $validated['longitude'] = $result['longitude'];
                $validated['address'] = $result['formatted_address'];
            } else {
                // If geocoding fails, don't allow business update
                return back()->withErrors(['address' => 'Could not find a valid location for this address. Please try a different address.'])->withInput();
            }
        }

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

        // Delete logo file if it exists
        if ($business->logo) {
            try {
                Storage::disk('public')->delete($business->logo);
            } catch (\Exception $e) {
                // Log the error but don't fail the deletion
                Log::warning('Failed to delete business logo: ' . $e->getMessage());
            }
        }

        $business->delete();

        return redirect()->route('businesses.index')->with('success', 'Business deleted successfully.');
    }

    /**
     * Remove the logo from the specified business.
     */
    public function removeLogo(string $id)
    {
        $user = Auth::user();
        $business = Business::findOrFail($id);

        if ($user->role !== 'admin' && $business->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($business->logo) {
            try {
                Storage::disk('public')->delete($business->logo);
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to remove logo file.');
            }

            $business->update(['logo' => null]);

            return redirect()->route('businesses.edit', $business->id)
                ->with('success', 'Logo removed successfully!');
        }

        return redirect()->route('businesses.edit', $business->id)
            ->with('info', 'No logo to remove.');
    }
}
