<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories (admin only).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $query = Category::withCount('businesses');

        if ($request->filled('search')) {
            $query->where('slug', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderBy('slug')->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:categories,slug|regex:/^[a-z0-9\-]+$/',
            'name_en' => 'required|string|max:255',
            'name_hu' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_hu' => 'required|string',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        // Create the category
        $category = Category::create([
            'slug' => $validated['slug'],
            'color' => $validated['color'],
        ]);

        // Update translation files
        $this->updateTranslationFiles($validated['slug'], [
            'en' => [
                'name' => $validated['name_en'],
                'description' => $validated['description_en'],
            ],
            'hu' => [
                'name' => $validated['name_hu'],
                'description' => $validated['description_hu'],
            ]
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category.
     */
    public function show(string $id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $category = Category::with('businesses')->findOrFail($id);

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(string $id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $category = Category::findOrFail($id);

        // Get current translations
        $translations = [
            'en' => [
                'name' => __("categories.{$category->slug}.name", [], 'en'),
                'description' => __("categories.{$category->slug}.description", [], 'en'),
            ],
            'hu' => [
                'name' => __("categories.{$category->slug}.name", [], 'hu'),
                'description' => __("categories.{$category->slug}.description", [], 'hu'),
            ]
        ];

        return view('categories.edit', compact('category', 'translations'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:categories,slug,' . $id . '|regex:/^[a-z0-9\-]+$/',
            'name_en' => 'required|string|max:255',
            'name_hu' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_hu' => 'required|string',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $category->update([
            'slug' => $validated['slug'],
            'color' => $validated['color'],
        ]);

        // Update translation files
        $this->updateTranslationFiles($validated['slug'], [
            'en' => [
                'name' => $validated['name_en'],
                'description' => $validated['description_en'],
            ],
            'hu' => [
                'name' => $validated['name_hu'],
                'description' => $validated['description_hu'],
            ]
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Update translation files with new category data
     */
    private function updateTranslationFiles($slug, $translations)
    {
        foreach ($translations as $locale => $data) {
            $filePath = resource_path("lang/{$locale}/categories.php");

            if (file_exists($filePath)) {
                $existing = include $filePath;
            } else {
                $existing = [];
            }

            $existing[$slug] = $data;

            $content = "<?php\n\nreturn " . var_export($existing, true) . ";\n";
            file_put_contents($filePath, $content);
        }
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $category = Category::findOrFail($id);

        if ($category->businesses()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Cannot delete category that has businesses assigned to it.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
