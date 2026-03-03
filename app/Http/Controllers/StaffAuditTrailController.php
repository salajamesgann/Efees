<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StaffAuditTrailController extends Controller
{
    /**
     * Display the staff member's own activity log.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        if (! $user || ! $user->hasRole('staff')) {
            abort(403);
        }

        $query = AuditLog::where('user_id', $user->user_id);

        // Filter by action type
        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        // Filter by date range
        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        // Search in details
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('details', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('model_id', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderByDesc('created_at')->paginate(20)->appends($request->query());

        // Get distinct actions for the filter dropdown
        $actions = AuditLog::where('user_id', $user->user_id)
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('auth.staff_audit_trail', compact('logs', 'actions'));
    }
}
