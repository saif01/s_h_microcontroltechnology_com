<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $query = Tag::query();

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
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
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSortFields = ['id', 'name', 'slug', 'type', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'name';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }
        
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $tags = $query->paginate($perPage);
        
        return response()->json($tags);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tags',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        }

        $tag = Tag::create($validated);
        
        return response()->json($tag, 201);
    }

    public function show(Tag $tag)
    {
        return response()->json($tag);
    }

    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tags,slug,' . $tag->id,
            'type' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Auto-generate slug if not provided and name changed
        if (isset($validated['name']) && empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        }

        $tag->update($validated);
        
        return response()->json($tag);
    }

    public function destroy(Tag $tag)
    {
        // Check if tag has products
        $productCount = $tag->products()->count();
        $serviceCount = $tag->services()->count();
        
        // Check posts if BlogPost model exists
        $postCount = 0;
        if (class_exists(\App\Models\BlogPost::class)) {
            $postCount = $tag->posts()->count();
        }
        
        if ($productCount > 0 || $serviceCount > 0 || $postCount > 0) {
            $message = "Cannot delete tag. It is associated with ";
            $parts = [];
            if ($productCount > 0) $parts[] = "{$productCount} product(s)";
            if ($serviceCount > 0) $parts[] = "{$serviceCount} service(s)";
            if ($postCount > 0) $parts[] = "{$postCount} post(s)";
            $message .= implode(', ', $parts) . ".";
            
            return response()->json(['error' => $message], 422);
        }

        $tag->delete();
        return response()->json(['message' => 'Tag deleted successfully']);
    }
}

