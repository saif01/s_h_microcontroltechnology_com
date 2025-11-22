<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Check if user has administrator role or access-dashboard permission
        $hasAccess = false;
        
        // Check legacy admin role
        if ($user->role === 'admin') {
            $hasAccess = true;
        }
        
        // Check new role system - administrator role or access-dashboard permission
        if ($user->hasRole('administrator') || $user->hasPermission('access-dashboard')) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);
        }
        
        return $next($request);
    }
}
