<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Filter by role if provided (by role ID or slug)
        if ($request->has('role')) {
            $roleFilter = $request->role;
            // Check if it's a numeric ID
            if (is_numeric($roleFilter)) {
                $query->whereHas('roles', function ($q) use ($roleFilter) {
                    $q->where('roles.id', $roleFilter);
                });
            } else {
                // Otherwise treat it as slug
                $query->whereHas('roles', function ($q) use ($roleFilter) {
                    $q->where('roles.slug', $roleFilter);
                });
            }
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate($request->get('per_page', 10));
        
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
            'avatar' => 'nullable|string|max:255',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        // Extract role_ids before creating user
        $roleIds = $validated['role_ids'];
        unset($validated['role_ids']);

        $user = User::create($validated);
        
        // Assign roles
        $user->roles()->sync($roleIds);
        
        // Load roles relationship
        $user->load('roles');
        
        // Remove password from response
        unset($user->password);
        
        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        // Load roles relationship
        $user->load('roles');
        
        // Remove password from response
        unset($user->password);
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'role_ids' => 'sometimes|required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
            'avatar' => 'nullable|string|max:255',
        ]);

        // Only hash password if it's being updated
        if (isset($validated['password']) && $validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Extract role_ids if provided
        $roleIds = null;
        if (isset($validated['role_ids'])) {
            $roleIds = $validated['role_ids'];
            unset($validated['role_ids']);
        }

        $user->update($validated);
        
        // Update roles if provided
        if ($roleIds !== null) {
            $user->roles()->sync($roleIds);
        }
        
        // Load roles relationship
        $user->load('roles');
        
        // Remove password from response
        unset($user->password);
        
        return response()->json($user);
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot delete your own account'], 403);
        }

        // Prevent deleting the last admin (check both legacy role and new role system)
        $isAdmin = false;
        if ($user->role === 'admin') {
            $isAdmin = true;
        } else {
            // Check if user has administrator role in new role system
            $isAdmin = $user->roles()->where('slug', 'administrator')->exists();
        }
        
        if ($isAdmin) {
            // Count admins using both systems (legacy role and new role system)
            $legacyAdminCount = User::where('role', 'admin')->where('id', '!=', $user->id)->count();
            
            $newAdminCount = User::whereHas('roles', function ($q) {
                $q->where('slug', 'administrator');
            })->where('id', '!=', $user->id)->count();
            
            // If this user is the only admin (either legacy or new system), prevent deletion
            if ($legacyAdminCount === 0 && $newAdminCount === 0) {
                return response()->json(['message' => 'Cannot delete the last admin user'], 403);
            }
        }

        $user->delete();
        
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function roles()
    {
        // Get all active roles from the database
        $roles = \App\Models\Role::where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get()
            ->map(function ($role) {
                return [
                    'value' => $role->id,
                    'label' => $role->name,
                    'slug' => $role->slug,
                    'description' => $role->description,
                    'is_system' => $role->is_system,
                ];
            });

        return response()->json([
            'roles' => $roles
        ]);
    }
}
