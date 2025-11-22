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
        $query = User::query();

        // Filter by role if provided
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate($request->get('per_page', 15));
        
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', 'string', Rule::in(['admin', 'editor', 'hr', 'staff'])],
            'avatar' => 'nullable|string|max:255',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        
        // Remove password from response
        unset($user->password);
        
        return response()->json($user, 201);
    }

    public function show(User $user)
    {
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
            'role' => ['sometimes', 'required', 'string', Rule::in(['admin', 'editor', 'hr', 'staff'])],
            'avatar' => 'nullable|string|max:255',
        ]);

        // Only hash password if it's being updated
        if (isset($validated['password']) && $validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        
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

        // Prevent deleting the last admin
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return response()->json(['message' => 'Cannot delete the last admin user'], 403);
            }
        }

        $user->delete();
        
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function roles()
    {
        return response()->json([
            'roles' => [
                [
                    'value' => 'admin',
                    'label' => 'Administrator',
                    'description' => 'Full access to all features and settings'
                ],
                [
                    'value' => 'editor',
                    'label' => 'Editor',
                    'description' => 'Can manage content (pages, posts, services, products) but not system settings'
                ],
                [
                    'value' => 'hr',
                    'label' => 'HR User',
                    'description' => 'Can manage careers and job applications'
                ],
                [
                    'value' => 'staff',
                    'label' => 'Staff',
                    'description' => 'Limited access, typically for branch managers or content creators'
                ],
            ]
        ]);
    }
}
