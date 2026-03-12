<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Services\AuditService;
use App\Services\SchoolYearUpdateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperAdminSettingsController extends Controller
{
    /**
     * Display the global system settings.
     */
    public function index(): View
    {
        $settings = [
            'school_year' => SystemSetting::getValue('school_year', date('Y').'-'.(date('Y') + 1)),
            'maintenance_mode' => SystemSetting::getValue('maintenance_mode', 'off'), // off, read-only, maintenance
            'system_notice' => SystemSetting::getValue('system_notice'),
            'semester' => SystemSetting::getValue('semester', 'Full Year'),
            'student_id_format' => SystemSetting::getValue('student_id_format', 'STU-{SY}-{####}'),
            'auto_generate_fees_on_enrollment' => SystemSetting::getValue('auto_generate_fees_on_enrollment', '1'),
            'notifications_enabled' => SystemSetting::getValue('notifications_enabled', '1'),
            'allow_staff_edit_fees' => SystemSetting::getValue('allow_staff_edit_fees', '0'),
            'max_login_attempts' => SystemSetting::getValue('max_login_attempts', '5'),
            'lockout_minutes' => SystemSetting::getValue('lockout_minutes', '15'),
            'password_expiry_days' => SystemSetting::getValue('password_expiry_days', '90'),
        ];

        return view('super_admin.settings', compact('settings'));
    }

    /**
     * Update global system settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'school_year' => 'required|string|max:9',
            'maintenance_mode' => 'required|in:off,read-only,maintenance',
            'system_notice' => 'nullable|string|max:500',
            'semester' => 'nullable|string|max:50',
            'student_id_format' => 'nullable|string|max:100',
            'auto_generate_fees_on_enrollment' => 'nullable|in:0,1',
            'notifications_enabled' => 'nullable|in:0,1',
            'allow_staff_edit_fees' => 'nullable|in:0,1',
            'max_login_attempts' => 'nullable|integer|min:3|max:20',
            'lockout_minutes' => 'nullable|integer|min:1|max:1440',
            'password_expiry_days' => 'nullable|integer|min:7|max:365',
        ]);

        $oldSettings = [];
        $newSettings = $request->only([
            'school_year',
            'maintenance_mode',
            'system_notice',
            'semester',
            'student_id_format',
            'auto_generate_fees_on_enrollment',
            'notifications_enabled',
            'allow_staff_edit_fees',
            'max_login_attempts',
            'lockout_minutes',
            'password_expiry_days',
        ]);

        // Coerce checkbox-like to defaults if absent
        if (! $request->has('auto_generate_fees_on_enrollment')) {
            $newSettings['auto_generate_fees_on_enrollment'] = '0';
        }
        if (! $request->has('notifications_enabled')) {
            $newSettings['notifications_enabled'] = '0';
        }
        if (! $request->has('allow_staff_edit_fees')) {
            $newSettings['allow_staff_edit_fees'] = '0';
        }
        if (! isset($newSettings['max_login_attempts'])) {
            $newSettings['max_login_attempts'] = (string) (int) ($request->input('max_login_attempts') ?? 5);
        }
        if (! isset($newSettings['lockout_minutes'])) {
            $newSettings['lockout_minutes'] = (string) (int) ($request->input('lockout_minutes') ?? 15);
        }
        if (! isset($newSettings['password_expiry_days'])) {
            $newSettings['password_expiry_days'] = (string) (int) ($request->input('password_expiry_days') ?? 90);
        }

        // Check for school year change to trigger service
        $currentSY = SystemSetting::getValue('school_year');
        if ($newSettings['school_year'] !== $currentSY) {
            SchoolYearUpdateService::handleSchoolYearChange($newSettings['school_year']);
        }

        foreach ($newSettings as $key => $value) {
            $oldSettings[$key] = SystemSetting::getValue($key);
            SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Audit Log
        AuditService::log(
            'System Settings Updated',
            null,
            'Global system configuration was modified by Super Admin',
            $oldSettings,
            $newSettings
        );

        return redirect()->back()->with('success', 'System settings updated successfully.');
    }
}
