<?php

namespace App\Http\Controllers\Api\products;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by published status
        if ($request->has('published')) {
            $query->where('published', $request->published);
        }

        // Filter by parent_id
        if ($request->has('parent_id')) {
            if ($request->parent_id === 'null' || $request->parent_id === null) {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'order');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSortFields = ['id', 'name', 'slug', 'type', 'published', 'order', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'order';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }
        
        $query->orderBy($sortBy, $sortDirection);
        
        if ($sortBy !== 'name' && $sortBy !== 'order') {
            $query->orderBy('name', 'asc');
        }

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $categories = $query->with(['parent', 'children'])->paginate($perPage);

        $categories->getCollection()->transform(function ($category) {
            return $this->transformCategoryWithImage($category);
        });
        
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer',
            'published' => 'boolean',
        ]);

        if (!empty($validated['image'])) {
            $validated['image'] = $this->normalizeImagePath($validated['image']);
        }

        $category = Category::create($validated);

        return response()->json(
            $this->transformCategoryWithImage($category->load(['parent', 'children'])),
            201
        );
    }

    public function show(Category $category)
    {
        return response()->json($this->transformCategoryWithImage($category->load(['parent', 'children'])));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:categories,slug,' . $category->id,
            'type' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer',
            'published' => 'boolean',
        ]);

        if (array_key_exists('image', $validated)) {
            $validated['image'] = $this->normalizeImagePath($validated['image']);
        }

        // Prevent category from being its own parent
        if (isset($validated['parent_id']) && $validated['parent_id'] == $category->id) {
            return response()->json(['error' => 'A category cannot be its own parent'], 422);
        }

        $category->update($validated);
        
        return response()->json($this->transformCategoryWithImage($category->load(['parent', 'children'])));
    }

    public function destroy(Category $category)
    {
        // Check if category has children
        if ($category->children()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete category with child categories. Please delete or reassign child categories first.'
            ], 422);
        }

        // Check if category has products/services
        $productCount = $category->products()->count();
        $serviceCount = $category->services()->count();
        
        if ($productCount > 0 || $serviceCount > 0) {
            return response()->json([
                'error' => "Cannot delete category. It is associated with {$productCount} product(s) and {$serviceCount} service(s)."
            ], 422);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }

    private function transformCategoryWithImage(Category $category): Category
    {
        $category->image = $this->buildImageUrl($category->image);

        if ($category->relationLoaded('parent') && $category->parent) {
            $category->parent->image = $this->buildImageUrl($category->parent->image);
        }

        if ($category->relationLoaded('children')) {
            $category->children->transform(function ($child) {
                return $this->transformCategoryWithImage($child);
            });
        }

        return $category;
    }

    private function normalizeImagePath(?string $image): ?string
    {
        if (!$image) {
            return null;
        }

        $image = trim($image);
        if ($image === '') {
            return null;
        }

        $relativePattern = '/^(uploads\/|storage\/uploads\/|storage\/)/i';
        $trimmed = ltrim($image, '/');

        if (preg_match($relativePattern, $trimmed)) {
            return $trimmed;
        }

        if (preg_match('#^https?://#i', $image)) {
            $parsed = parse_url($image);
            $path = isset($parsed['path']) ? ltrim($parsed['path'], '/') : '';

            if ($path && preg_match($relativePattern, $path)) {
                return $path;
            }

            return $image;
        }

        return $trimmed;
    }

    private function buildImageUrl(?string $image): ?string
    {
        if (!$image) {
            return null;
        }

        $image = trim($image);
        if ($image === '') {
            return null;
        }

        if (Str::startsWith($image, ['http://', 'https://', '//'])) {
            $parsed = parse_url($image);
            $path = isset($parsed['path']) ? ltrim($parsed['path'], '/') : '';

            if ($path && preg_match('/^(uploads\/|storage\/uploads\/|storage\/)/i', $path)) {
                return asset($path);
            }

            return $image;
        }

        return asset(ltrim($image, '/'));
    }
}
