<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an action to the audit trail.
     *
     * @param  string  $action  The action name (e.g., 'Payment Added')
     * @param  mixed  $model  The model affected (optional)
     * @param  string|null  $details  Short description or remarks
     * @param  array|null  $oldValues  Old values before update
     * @param  array|null  $newValues  New values after update
     * @return AuditLog
     */
    public static function log($action, $model = null, $details = null, $oldValues = null, $newValues = null)
    {
        $user = Auth::user();

        $modelType = null;
        $modelId = null;

        if ($model) {
            $modelType = get_class($model);
            $modelId = $model->getKey();
        }

        $userRole = 'system';
        if ($user) {
            if ($user->relationLoaded('role') && $user->role) {
                $userRole = $user->role->role_name;
            } elseif ($user->role_id && method_exists($user, 'role')) {
                // Try to load if not loaded
                $userRole = $user->role->role_name ?? 'unknown';
            } elseif (isset($user->role)) {
                // Legacy string column
                $userRole = $user->role;
            }
        }

        return AuditLog::create([
            'user_id' => $user ? $user->user_id : null,
            'user_role' => $userRole,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'details' => $details,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
