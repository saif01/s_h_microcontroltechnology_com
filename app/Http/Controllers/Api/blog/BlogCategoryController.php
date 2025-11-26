<?php

namespace App\Http\Controllers\Api\blog;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Support\MediaPath;

class BlogCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query()->where('type', 'post'); // Only blog categories

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
        
        $allowedSortFields = ['id', 'name', 'slug', 'published', 'order', 'created_at', 'updated_at'];
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
        $categories = $query->select('id', 'name', 'slug', 'type', 'description', 'image', 'parent_id', 'order', 'published', 'created_at', 'updated_at')
            ->with([
                'parent' => function ($query) {
                    $query->select('id', 'name', 'slug', 'type', 'image')->where('type', 'post');
                },
                'children' => function ($query) {
                    $query->select('id', 'name', 'slug', 'type', 'image', 'parent_id', 'order')->where('type', 'post');
                }
            ])
            ->paginate($perPage);

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
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer',
            'published' => 'boolean',
        ]);

        // Force type to 'post' for blog categories
        $validated['type'] = 'post';

        // Validate parent_id is also a 'post' type category
        if (!empty($validated['parent_id'])) {
            $parent = Category::find($validated['parent_id']);
            if (!$parent || $parent->type !== 'post') {
                return response()->json([
                    'error' => 'Parent category must be a blog category (type: post)'
                ], 422);
            }
        }

        if (!empty($validated['image'])) {
            $validated['image'] = MediaPath::normalize($validated['image']);
        }

        $category = Category::create($validated);

        return response()->json(
            $this->transformCategoryWithImage($category->load(['parent', 'children'])),
            201
        );
    }

    public function show(Request $request, $id)
    {
        // Support both id and slug for route model binding
        $category = Category::where('id', $id)
            ->orWhere('slug', $id)
            ->where('type', 'post')
            ->firstOrFail();

        return response()->json($this->transformCategoryWithImage($category->load(['parent', 'children'])));
    }

    public function update(Request $request, $id)
    {
        // Support both id and slug for route model binding
        $category = Category::where('id', $id)
            ->orWhere('slug', $id)
            ->where('type', 'post')
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:categories,slug,' . $category->id . ',id',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer',
            'published' => 'boolean',
        ]);

        // Ensure type remains 'post'
        $validated['type'] = 'post';

        // Validate parent_id is also a 'post' type category
        if (isset($validated['parent_id']) && !empty($validated['parent_id'])) {
            $parent = Category::find($validated['parent_id']);
            if (!$parent || $parent->type !== 'post') {
                return response()->json([
                    'error' => 'Parent category must be a blog category (type: post)'
                ], 422);
            }
        }

        if (array_key_exists('image', $validated)) {
            $validated['image'] = MediaPath::normalize($validated['image']);
        }

        // Prevent category from being its own parent
        if (isset($validated['parent_id']) && $validated['parent_id'] == $category->id) {
            return response()->json(['error' => 'A category cannot be its own parent'], 422);
        }

        $category->update($validated);
        
        return response()->json($this->transformCategoryWithImage($category->load(['parent', 'children'])));
    }

    public function destroy($id)
    {
        // Support both id and slug for route model binding
        $category = Category::where('id', $id)
            ->orWhere('slug', $id)
            ->where('type', 'post')
            ->firstOrFail();

        // Check if category has children
        if ($category->children()->where('type', 'post')->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete category with child categories. Please delete or reassign child categories first.'
            ], 422);
        }

        // Check if category has blog posts
        $postCount = $category->posts()->count();
        
        if ($postCount > 0) {
            return response()->json([
                'error' => "Cannot delete category. It is associated with {$postCount} blog post(s)."
            ], 422);
        }

        $category->delete();
        return response()->json(['message' => 'Blog category deleted successfully']);
    }

    private function transformCategoryWithImage(Category $category): Category
    {
        $category->image = MediaPath::url($category->image);

        if ($category->relationLoaded('parent') && $category->parent) {
            $category->parent->image = MediaPath::url($category->parent->image);
        }

        if ($category->relationLoaded('children')) {
            $category->children->transform(function ($child) {
                return $this->transformCategoryWithImage($child);
            });
        }

        return $category;
    }
}

