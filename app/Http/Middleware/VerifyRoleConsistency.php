<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyRoleConsistency
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $sessionRole = $request->session()->get('active_role');
            $sessionUserId = $request->session()->get('active_user_id');
            $currentRole = $user->getRoleName();
            $currentUserId = $user->user_id;

            // Strict check: Both role and User ID must match the initial login session
            if (($sessionRole && $sessionRole !== $currentRole) || ($sessionUserId && (string)$sessionUserId !== (string)$currentUserId)) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/')->with('error', 'Session conflict detected. You have been logged out for security reasons.');
            }

            // Lock the role and user ID in the session if not already set
            if (!$sessionRole) {
                $request->session()->put('active_role', $currentRole);
            }
            if (!$sessionUserId) {
                $request->session()->put('active_user_id', $currentUserId);
            }
        }

        return $next($request);
    }
}
