<?php

namespace App\Http\Controllers;

use App\Models\FeeAssignment;
use App\Models\FeeRecord;
use App\Models\ParentContact;
use App\Models\Payment;
use App\Models\SmsLog;
use App\Models\SmsSchedule;
use App\Models\Student;
use App\Models\StudentFeeAdjustment;
use App\Models\StudentSmsPreference;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AuditService;
use App\Services\SchoolYearUpdateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function index(): View
    {
        $settings = SystemSetting::whereIn('key', [
            'school_year',
            'semester',
            'student_id_format',
            'allow_staff_edit_fees',
            'auto_generate_fees_on_enrollment',
            'notifications_enabled',
            'maintenance_mode',
            'max_login_attempts',
            'lockout_minutes',
            'password_expiry_days',
        ])
            ->get()
            ->keyBy('key');

        return view('auth.admin_settings', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_year' => ['nullable', 'string', 'max:50'],
            'semester' => ['nullable', 'string', 'max:50'],
            'student_id_format' => ['nullable', 'string', 'max:100'],
            'auto_generate_fees_on_enrollment' => ['nullable', 'in:0,1'],
            'notifications_enabled' => ['nullable', 'in:0,1'],
            'maintenance_mode' => ['nullable', 'in:0,1'],
            'allow_staff_edit_fees' => ['nullable', 'in:0,1'],
            'max_login_attempts' => ['nullable', 'integer', 'min:3', 'max:20'],
            'lockout_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'password_expiry_days' => ['nullable', 'integer', 'min:7', 'max:365'],
        ]);

        if (! $request->has('auto_generate_fees_on_enrollment')) {
            $data['auto_generate_fees_on_enrollment'] = '0';
        }

        if (! $request->has('notifications_enabled')) {
            $data['notifications_enabled'] = '0';
        }

        if (! $request->has('maintenance_mode')) {
            $data['maintenance_mode'] = '0';
        }

        if (! $request->has('allow_staff_edit_fees')) {
            $data['allow_staff_edit_fees'] = '0';
        }

        if (! $request->has('max_login_attempts')) {
            $data['max_login_attempts'] = (string) (int) ($request->input('max_login_attempts') ?? 5);
        }
        if (! $request->has('lockout_minutes')) {
            $data['lockout_minutes'] = (string) (int) ($request->input('lockout_minutes') ?? 15);
        }
        if (! $request->has('password_expiry_days')) {
            $data['password_expiry_days'] = (string) (int) ($request->input('password_expiry_days') ?? 90);
        }

        $oldSettings = SystemSetting::whereIn('key', array_keys($data))->pluck('value', 'key')->toArray();

        // Check if school year is being updated
        $schoolYearUpdateResults = [];
        if (isset($data['school_year']) && $data['school_year'] !== ($oldSettings['school_year'] ?? null)) {
            $schoolYearUpdateResults = SchoolYearUpdateService::handleSchoolYearChange($data['school_year']);
        }

        foreach ($data as $key => $value) {
            SystemSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        cache()->forget('system_settings');

        // Audit Log
        try {
            AuditService::log(
                'System Settings Updated',
                null,
                'Updated system settings',
                $oldSettings,
                $data
            );
        } catch (\Throwable $e) {
        }

        // Prepare success message
        $successMessage = 'Settings updated';
        if (!empty($schoolYearUpdateResults['staff_updated'])) {
            $successMessage .= sprintf(
                '. School year updated: %d staff records updated to %s. %d student records preserved in their original enrollment years.',
                $schoolYearUpdateResults['staff_updated'],
                $data['school_year'] ?? 'new school year',
                $schoolYearUpdateResults['students_preserved'] ?? 0
            );
        }

        if (!empty($schoolYearUpdateResults['errors'])) {
            // Add errors to session if any occurred during school year update
            return redirect()->route('admin.settings.index')
                ->with('success', $successMessage)
                ->with('warning', 'Some issues occurred during school year update: ' . implode(', ', $schoolYearUpdateResults['errors']));
        }

        return redirect()->route('admin.settings.index')->with('success', $successMessage);
    }

    public function resetDemoData(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm' => ['required', 'in:RESET'],
        ]);

        DB::transaction(function () {
            FeeRecord::query()->delete();
            Payment::query()->delete();
            StudentFeeAdjustment::query()->delete();
            SmsLog::query()->delete();
            SmsSchedule::query()->delete();
            StudentSmsPreference::query()->delete();
            FeeAssignment::query()->delete();

            $parentIds = ParentContact::pluck('id')->all();

            if (! empty($parentIds)) {
                DB::table('parent_student')->whereIn('parent_id', $parentIds)->delete();
            }

            User::where('roleable_type', ParentContact::class)->delete();

            Student::query()->delete();
            ParentContact::query()->delete();
        });

        try {
            AuditService::log(
                'Demo Data Reset',
                null,
                'All students and parent accounts were removed via admin settings resetDemoData.',
                null,
                []
            );
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.settings.index')->with('success', 'All students and parent accounts have been removed.');
    }

    /**
     * Clear application cache.
     */
    public function clearCache(): RedirectResponse
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');

            try {
                AuditService::log('Cache Cleared', null, 'Admin cleared all application caches.', null, []);
            } catch (\Throwable $e) {
            }

            return redirect()->route('admin.settings.index')->with('success', 'All caches cleared successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings.index')->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Reset all data in the database (dangerous operation).
     */
    public function resetDatabase(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm' => ['required', 'in:RESET'],
        ]);

        try {
            DB::transaction(function () {
                // Delete in order to respect foreign key constraints
                DB::table('fee_records')->delete();
                DB::table('payments')->delete();
                DB::table('student_fee_adjustments')->delete();
                DB::table('sms_logs')->delete();
                DB::table('sms_schedules')->delete();
                DB::table('student_sms_preferences')->delete();

                // Delete fee assignments pivot data
                if (\Schema::hasTable('fee_assignment_additional_charge')) {
                    DB::table('fee_assignment_additional_charge')->delete();
                }
                if (\Schema::hasTable('fee_assignment_discount')) {
                    DB::table('fee_assignment_discount')->delete();
                }
                DB::table('fee_assignments')->delete();

                DB::table('parent_student')->delete();

                // Remove student and parent user accounts
                User::where('roleable_type', \App\Models\ParentContact::class)->delete();
                User::where('roleable_type', \App\Models\Student::class)->delete();

                Student::query()->delete();
                ParentContact::query()->delete();
            });

            try {
                AuditService::log('Database Reset', null, 'Admin performed full database reset.', null, []);
            } catch (\Throwable $e) {
            }

            return redirect()->route('admin.settings.index')->with('success', 'Database has been reset successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings.index')->with('error', 'Failed to reset database: ' . $e->getMessage());
        }
    }

    /**
     * Export the database as a downloadable SQL dump or CSV archive.
     */
    public function exportDatabase(): \Symfony\Component\HttpFoundation\StreamedResponse|RedirectResponse
    {
        try {
            $filename = 'efees_export_' . now()->format('Ymd_His') . '.csv';

            try {
                AuditService::log('Database Exported', null, 'Admin exported database.', null, []);
            } catch (\Throwable $e) {
            }

            return response()->streamDownload(function () {
                $fp = fopen('php://output', 'w');

                // Export students
                fputcsv($fp, ['--- STUDENTS ---']);
                fputcsv($fp, ['Student ID', 'First Name', 'Last Name', 'Level', 'Section', 'School Year', 'Status']);
                Student::chunk(500, function ($students) use ($fp) {
                    foreach ($students as $s) {
                        fputcsv($fp, [$s->student_id, $s->first_name, $s->last_name, $s->level, $s->section, $s->school_year, $s->enrollment_status]);
                    }
                });

                // Export payments
                fputcsv($fp, []);
                fputcsv($fp, ['--- PAYMENTS ---']);
                fputcsv($fp, ['Payment ID', 'Student ID', 'Amount', 'Status', 'Method', 'Date']);
                \App\Models\Payment::chunk(500, function ($payments) use ($fp) {
                    foreach ($payments as $p) {
                        fputcsv($fp, [$p->payment_id, $p->student_id, $p->amount_paid, $p->status, $p->payment_method, $p->payment_date]);
                    }
                });

                // Export fee assignments
                fputcsv($fp, []);
                fputcsv($fp, ['--- FEE ASSIGNMENTS ---']);
                fputcsv($fp, ['ID', 'Student ID', 'Tuition Fee ID', 'Total Amount', 'Created At']);
                FeeAssignment::chunk(500, function ($fas) use ($fp) {
                    foreach ($fas as $fa) {
                        fputcsv($fp, [$fa->id, $fa->student_id, $fa->tuition_fee_id, $fa->total_amount, $fa->created_at]);
                    }
                });

                fclose($fp);
            }, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings.index')->with('error', 'Failed to export database: ' . $e->getMessage());
        }
    }
}
