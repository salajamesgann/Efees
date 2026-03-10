<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Services\AuditService;
use App\Services\SchoolYearUpdateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class SuperAdminSettingsController extends Controller
{
    /**
     * Display the global system settings.
     */
    public function index(): View
    {
        $settings = [
            'school_year' => SystemSetting::getValue('school_year', date('Y').'-'.(date('Y') + 1)),
            'institution_name' => SystemSetting::getValue('institution_name', 'Efees Educational Institution'),
            'maintenance_mode' => SystemSetting::getValue('maintenance_mode', 'off'), // off, read-only, maintenance
            'school_logo' => SystemSetting::getValue('school_logo'),
            'system_notice' => SystemSetting::getValue('system_notice'),
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
            'institution_name' => 'required|string|max:255',
            'maintenance_mode' => 'required|in:off,read-only,maintenance',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'system_notice' => 'nullable|string|max:500',
        ]);

        $oldSettings = [];
        $newSettings = $request->only(['school_year', 'institution_name', 'maintenance_mode', 'system_notice']);

        // Check for school year change to trigger service
        $currentSY = SystemSetting::getValue('school_year');
        if ($newSettings['school_year'] !== $currentSY) {
            SchoolYearUpdateService::handleSchoolYearChange($newSettings['school_year']);
        }

        foreach ($newSettings as $key => $value) {
            $oldSettings[$key] = SystemSetting::getValue($key);
            SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Handle Logo Upload
        if ($request->hasFile('school_logo')) {
            $oldLogo = SystemSetting::getValue('school_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            
            $path = $request->file('school_logo')->store('branding', 'public');
            SystemSetting::updateOrCreate(['key' => 'school_logo'], ['value' => $path]);
            $newSettings['school_logo'] = $path;
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
