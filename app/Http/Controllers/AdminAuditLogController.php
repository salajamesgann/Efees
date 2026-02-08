<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('details', 'like', "%{$search}%")
                    ->orWhere('user_role', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('action_type')) {
            $query->where('action', $request->action_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();

        // Get unique actions for filter
        $actions = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('auth.admin_audit_logs', compact('logs', 'actions'));
    }

    public function show(AuditLog $auditLog)
    {
        return response()->json($auditLog->load('user'));
    }

    public function export(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('details', 'like', "%{$search}%")
                    ->orWhere('user_role', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('action_type')) {
            $query->where('action', $request->action_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=audit_logs_'.date('Y-m-d_H-i-s').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Date', 'User', 'Role', 'Action', 'Details', 'IP Address']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->toDateTimeString(),
                    $log->user ? $log->user->email : 'System/Unknown',
                    $log->user_role,
                    $log->action,
                    $log->details,
                    $log->ip_address,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
