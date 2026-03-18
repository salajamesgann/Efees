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

        if ($user->hasRole($role)) {
            return $next($request);
        }

        abort(403);
    }
}
