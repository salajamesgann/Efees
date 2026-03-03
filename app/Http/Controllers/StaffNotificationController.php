<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StaffNotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated staff member.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        if (! $user || ! $user->hasRole('staff')) {
            abort(403);
        }

        $query = DB::table('notifications')
            ->where('user_id', $user->user_id);

        // Filter by read status
        $status = $request->input('status');
        if ($status === 'unread') {
            $query->whereNull('read_at');
        } elseif ($status === 'read') {
            $query->whereNotNull('read_at');
        }

        // Filter by type (keyword in title)
        if ($type = $request->input('type')) {
            $query->where('title', 'like', "%{$type}%");
        }

        // Search in title and body
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $notifications = $query->orderByDesc('created_at')
            ->paginate(20)
            ->appends($request->query());

        $unreadCount = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->whereNull('read_at')
            ->count();

        $totalCount = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->count();

        return view('auth.staff_notifications', compact('notifications', 'unreadCount', 'totalCount'));
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $user = Auth::user();

        if (! $user || ! $user->hasRole('staff')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $updated = DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', $user->user_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($updated) {
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Notification not found or already read'], 404);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (! $user || ! $user->hasRole('staff')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notification count (AJAX).
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['count' => 0]);
        }

        $count = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }
}
