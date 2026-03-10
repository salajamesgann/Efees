<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maintenanceMode = SystemSetting::getValue('maintenance_mode', 'off');

        if ($maintenanceMode === 'off') {
            return $next($request);
        }

        $user = auth()->user();
        
        // Super Admins are immune to maintenance mode
        if ($user && $user->hasRole('super_admin')) {
            return $next($request);
        }

        // Full Maintenance Mode
        if ($maintenanceMode === 'maintenance') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'System is under maintenance.'], 503);
            }
            abort(503, 'The system is currently undergoing maintenance. Please check back later.');
        }

        // Read-Only Mode
        if ($maintenanceMode === 'read-only') {
            // Allow GET requests, block others for non-super admins
            if (!$request->isMethod('GET')) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'System is in read-only mode.'], 403);
                }
                return redirect()->back()->with('error', 'The system is currently in read-only mode for year-end processing.');
            }
        }

        return $next($request);
    }
}
