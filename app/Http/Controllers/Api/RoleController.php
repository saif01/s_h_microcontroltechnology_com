<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::query();

        if ($request->has('active')) {
            $query->where('is_active', $request->active);
        }

        $roles = $query->with('permissions')->orderBy('order')->orderBy('name')->get();
        
        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'slug' => 'nullable|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'integer',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Generate slug from name if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $role = Role::create($validated);

        // Attach permissions if provided
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        $role->load('permissions');
        
        return response()->json($role, 201);
    }

    public function show(Role $role)
    {
        $role->load('permissions');
        return response()->json($role);
    }

    public function update(Request $request, Role $role)
    {
        // Prevent editing system roles
        if ($role->is_system) {
            return response()->json(['message' => 'System roles cannot be modified'], 403);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'integer',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Generate slug from name if not provided and name changed
        if (isset($validated['name']) && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $role->update($validated);

        // Update permissions if provided
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        $role->load('permissions');
        
        return response()->json($role);
    }

    public function destroy(Role $role)
    {
        // Prevent deleting system roles
        if ($role->is_system) {
            return response()->json(['message' => 'System roles cannot be deleted'], 403);
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return response()->json(['message' => 'Cannot delete role with assigned users'], 403);
        }

        $role->delete();
        
        return response()->json(['message' => 'Role deleted successfully']);
    }

    // Get all permissions grouped
    public function permissions()
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        $grouped = $permissions->groupBy('group');
        
        return response()->json($grouped);
    }

    // Sync role permissions
    public function syncPermissions(Request $request, Role $role)
    {
        if ($role->is_system) {
            return response()->json(['message' => 'System roles cannot be modified'], 403);
        }

        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($validated['permissions']);
        $role->load('permissions');

        return response()->json($role);
    }
}
