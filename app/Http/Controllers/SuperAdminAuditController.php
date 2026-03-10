<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class SuperAdminAuditController extends Controller
{
    /**
     * Display a listing of audit logs with advanced filtering.
     */
    public function index(Request $request): View
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Filter by Search (User Name or Email)
        if ($request->filled('q')) {
            $searchTerm = trim($request->q);
            $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
            
            $query->where(function ($q) use ($searchTerm, $operator) {
                $q->where('action', $operator, "%{$searchTerm}%")
                  ->orWhere('ip_address', $operator, "%{$searchTerm}%")
                  ->orWhereHas('user', function ($uq) use ($searchTerm, $operator) {
                      $uq->where('email', $operator, "%{$searchTerm}%");
                  });
            });
        }

        // Filter by Action Type
        if ($request->filled('action_type') && $request->action_type !== 'all') {
            $query->where('action', $request->action_type);
        }

        // Filter by User
        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->paginate(20)->withQueryString();
        
        // Get unique action types for filter dropdown
        $actionTypes = AuditLog::select('action')->distinct()->pluck('action');
        
        // Get admins/staff for user filter
        $users = User::whereHas('role', function($q) {
            $q->whereIn('role_name', ['admin', 'super_admin', 'staff']);
        })->get();

        return view('super_admin.audit_logs', compact('logs', 'actionTypes', 'users'));
    }

    /**
     * Display the specified audit log detail.
     */
    public function show(AuditLog $log): View
    {
        return view('super_admin.audit_show', compact('log'));
    }
}
