<?php

namespace App\Http\Controllers;

use App\Models\ParentContact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ParentNotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated parent.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $roleName = optional($user->role)->role_name;

        if (! $user || ($roleName !== 'parent' && $user->roleable_type !== ParentContact::class)) {
            abort(403);
        }

        $parent = $user->roleable instanceof ParentContact ? $user->roleable : null;
        $myChildren = $parent ? $parent->students()->get() : collect();

        $notifications = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->orderByDesc('created_at')
            ->paginate(20);

        $unreadCount = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->whereNull('read_at')
            ->count();

        return view('auth.parent_notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'isParent' => true,
            'myChildren' => $myChildren,
            'selectedChild' => null,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        $roleName = optional($user->role)->role_name;

        if (! $user || ($roleName !== 'parent' && $user->roleable_type !== ParentContact::class)) {
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
        $roleName = optional($user->role)->role_name;

        if (! $user || ($roleName !== 'parent' && $user->roleable_type !== ParentContact::class)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notification count (for AJAX badge updates).
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
