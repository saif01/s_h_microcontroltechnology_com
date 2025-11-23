<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Get all permissions, optionally grouped by group
     */
    public function index(Request $request)
    {
        $query = Permission::query();

        // Filter by group if provided
        if ($request->has('group')) {
            $query->where('group', $request->group);
        }

        // Search by name or slug
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting (only for flat view)
        $sortBy = $request->get('sort_by', 'group');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSortFields = ['id', 'name', 'slug', 'group', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'group';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Get grouped or flat list
        if ($request->has('grouped') && $request->grouped) {
            // For grouped view, we still return all permissions grouped (no pagination)
            $permissions = $query->withCount('roles')->orderBy('group')->orderBy('name')->get();
            $grouped = $permissions->groupBy('group');
            return response()->json($grouped);
        }

        // Apply sorting for flat view
        $query->orderBy($sortBy, $sortDirection);
        
        if ($sortBy !== 'name' && $sortBy !== 'group') {
            $query->orderBy('group', 'asc')->orderBy('name', 'asc');
        } elseif ($sortBy === 'group') {
            $query->orderBy('name', 'asc');
        }

        // Paginate results for flat view
        $perPage = $request->get('per_page', 10);
        $permissions = $query->withCount('roles')->paginate($perPage);
        
        return response()->json($permissions);
    }

    /**
     * Create a new permission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:permissions',
            'group' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Generate slug from name if not provided
        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            
            // Ensure slug is unique by appending number if needed
            $counter = 1;
            while (Permission::where('slug', $slug)->exists() && $counter < 100) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        $permission = Permission::create($validated);
        
        return response()->json($permission, 201);
    }

    /**
     * Get a specific permission
     */
    public function show($id)
    {
        $permission = Permission::withCount('roles')->findOrFail($id);
        return response()->json($permission);
    }

    /**
     * Update a permission
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('permissions')->ignore($permission->id)],
            'group' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Handle slug: if provided and empty, generate from name; if provided and not empty, ensure unique
        if (isset($validated['slug']) && empty($validated['slug'])) {
            // Slug was provided but empty - generate from name
            if (isset($validated['name'])) {
                $baseSlug = Str::slug($validated['name']);
            } else {
                $baseSlug = Str::slug($permission->name);
            }
            $slug = $baseSlug;
            
            // Ensure slug is unique by appending number if needed
            $counter = 1;
            while (Permission::where('slug', $slug)->where('id', '!=', $permission->id)->exists() && $counter < 100) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        } elseif (isset($validated['slug']) && !empty($validated['slug'])) {
            // If slug is provided and not empty, ensure it's unique
            $slug = $validated['slug'];
            $originalSlug = $slug;
            $counter = 1;
            while (Permission::where('slug', $slug)->where('id', '!=', $permission->id)->exists() && $counter < 100) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        $permission->update($validated);
        
        return response()->json($permission);
    }

    /**
     * Delete a permission
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete permission that is assigned to roles. Please remove it from all roles first.'
            ], 403);
        }

        $permission->delete();
        
        return response()->json(['message' => 'Permission deleted successfully']);
    }

    /**
     * Get all available permission groups
     */
    public function groups()
    {
        $groups = Permission::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');
        
        return response()->json($groups);
    }
}
