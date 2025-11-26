<?php

namespace App\Http\Controllers\Api\career;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CareerController extends Controller
{
    public function index(Request $request)
    {
        $query = Career::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by published status
        if ($request->has('published')) {
            $query->where('published', $request->published);
        }

        // Filter by department
        if ($request->has('department')) {
            $query->where('department', $request->department);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->where('location', $request->location);
        }

        // Filter by employment type
        if ($request->has('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'order');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSortFields = ['id', 'title', 'slug', 'department', 'location', 'employment_type', 'published', 'order', 'deadline', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'order';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }
        
        $query->orderBy($sortBy, $sortDirection);
        
        if ($sortBy !== 'title' && $sortBy !== 'order') {
            $query->orderBy('order', 'asc');
        }

        // Paginate results
        $perPage = $request->get('per_page', 10);
        $careers = $query->paginate($perPage);

        // Include application count
        $careers->getCollection()->transform(function ($career) {
            $career->applications_count = $career->applications()->count();
            return $career;
        });
        
        return response()->json($careers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:careers',
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'deadline' => 'nullable|date',
            'published' => 'boolean',
            'order' => 'integer',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug']) && !empty($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Career::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $career = Career::create($validated);
        $career->applications_count = 0;
        
        return response()->json($career, 201);
    }

    public function show($id)
    {
        // Support both id and slug for route model binding
        $career = Career::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        $career->applications_count = $career->applications()->count();
        return response()->json($career);
    }

    public function update(Request $request, $id)
    {
        // Support both id and slug for route model binding
        $career = Career::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255|unique:careers,slug,' . $career->id,
            'department' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'deadline' => 'nullable|date',
            'published' => 'boolean',
            'order' => 'integer',
        ]);

        // Auto-generate slug if title changed and slug not provided
        if (isset($validated['title']) && $career->title !== $validated['title'] && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Career::where('slug', $validated['slug'])->where('id', '!=', $career->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $career->update($validated);
        $career->applications_count = $career->applications()->count();
        
        return response()->json($career);
    }

    public function destroy($id)
    {
        // Support both id and slug for route model binding
        $career = Career::where('id', $id)->orWhere('slug', $id)->firstOrFail();
        $career->delete();
        return response()->json(['message' => 'Career deleted successfully']);
    }
}

