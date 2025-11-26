<?php

namespace App\Http\Controllers\Api\content;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Support\MediaPath;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by published status
        if ($request->has('published')) {
            $query->where('published', $request->published);
        }

        // Filter by page type
        if ($request->has('page_type')) {
            $query->where('page_type', $request->page_type);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'order');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSortFields = ['id', 'title', 'slug', 'page_type', 'published', 'order', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'order';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }
        
        $query->orderBy($sortBy, $sortDirection);
        
        if ($sortBy !== 'title' && $sortBy !== 'order') {
            $query->orderBy('title', 'asc');
        }

        // Paginate results
        $perPage = $request->get('per_page', 10);
        $pages = $query->paginate($perPage);

        $pages->getCollection()->transform(function ($page) {
            return $this->transformPageWithImages($page);
        });
        
        return response()->json($pages);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages',
            'content' => 'nullable|string',
            'page_type' => 'nullable|string|max:255',
            'featured_image' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'og_image' => 'nullable|string',
            'published' => 'boolean',
            'order' => 'integer',
        ]);

        // Normalize image paths
        if (!empty($validated['featured_image'])) {
            $validated['featured_image'] = MediaPath::normalize($validated['featured_image']);
        }
        if (!empty($validated['og_image'])) {
            $validated['og_image'] = MediaPath::normalize($validated['og_image']);
        }

        $page = Page::create($validated);
        return response()->json($this->transformPageWithImages($page), 201);
    }

    public function show($id)
    {
        // Support both id and slug for route model binding
        $page = Page::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        return response()->json($this->transformPageWithImages($page));
    }

    public function update(Request $request, $id)
    {
        // Support both id and slug for route model binding
        $page = Page::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'nullable|string',
            'page_type' => 'nullable|string|max:255',
            'featured_image' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'og_image' => 'nullable|string',
            'published' => 'boolean',
            'order' => 'integer',
        ]);

        // Normalize image paths
        if (array_key_exists('featured_image', $validated) && !empty($validated['featured_image'])) {
            $validated['featured_image'] = MediaPath::normalize($validated['featured_image']);
        }
        if (array_key_exists('og_image', $validated) && !empty($validated['og_image'])) {
            $validated['og_image'] = MediaPath::normalize($validated['og_image']);
        }

        $page->update($validated);
        return response()->json($this->transformPageWithImages($page));
    }

    private function transformPageWithImages(Page $page): Page
    {
        if (!empty($page->featured_image)) {
            $page->featured_image = MediaPath::url($page->featured_image);
        }

        if (!empty($page->og_image)) {
            $page->og_image = MediaPath::url($page->og_image);
        }

        return $page;
    }

    public function destroy($id)
    {
        // Support both id and slug for route model binding
        $page = Page::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        $page->delete();
        return response()->json(['message' => 'Page deleted successfully']);
    }
}

