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
            $currentRole = $user->getRoleName();

            if ($sessionRole && $sessionRole !== $currentRole) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/')->with('error', 'Session conflict detected. You have been logged out for security reasons.');
            }

            // Set the role in the session if it's not there
            if (!$sessionRole) {
                $request->session()->put('active_role', $currentRole);
            }
        }

        return $next($request);
    }
}
