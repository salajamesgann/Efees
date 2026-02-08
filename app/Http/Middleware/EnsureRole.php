<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ?string $role = null)
    {
        if ($role === null) {
            return $next($request);
        }
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        // Check if role matches directly (support multiple roles separated by |)
        $roles = explode('|', $role);
        if (in_array(optional($user->role)->role_name, $roles)) {
            return $next($request);
        }

        // Check roleable_type as fallback
        $roleableMap = [
            'parent' => \App\Models\ParentContact::class,
            'student' => \App\Models\Student::class,
            'staff' => \App\Models\Staff::class,
        ];

        if (isset($roleableMap[$role]) && ($user->roleable_type ?? '') === $roleableMap[$role]) {
            return $next($request);
        }

        abort(403);
    }
}
