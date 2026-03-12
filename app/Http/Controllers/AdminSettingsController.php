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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $user->load(['role', 'roleable']);
        $prefs = $user->preferences ?? [];
        return view('auth.admin_settings', [
            'user' => $user,
            'prefs' => $prefs,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        return redirect()->route('admin.settings.index')->with('success', 'Settings are now managed by the Super Admin under System Configuration.');
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

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $user->load('roleable');
        $data = $request->validate([
            'first_name' => ['required','string','max:255'],
            'middle_initial' => ['nullable','string','max:2'],
            'last_name' => ['required','string','max:255'],
            'phone' => ['nullable','string','max:20'],
            'avatar' => ['nullable','image','max:2048'],
        ]);
        DB::transaction(function () use ($user, $data, $request) {
            if ($user->roleable) {
                $user->roleable->first_name = $data['first_name'];
                $user->roleable->MI = $data['middle_initial'] ?? null;
                $user->roleable->last_name = $data['last_name'];
                $user->roleable->contact_number = $data['phone'] ?? null;
                $user->roleable->save();
            }
            $prefs = $user->preferences ?? [];
            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars','public');
                $prefs['profile']['avatar_path'] = $path;
            }
            $prefs['profile']['updated_at'] = now()->toISOString();
            $user->preferences = $prefs;
            $user->save();
        });
        return back()->with('success','Profile updated.');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $request->validate([
            'approvals' => ['nullable','in:0,1'],
            'online_confirmations' => ['nullable','in:0,1'],
        ]);
        $prefs = $user->preferences ?? [];
        $prefs['notifications']['approvals'] = $request->boolean('approvals');
        $prefs['notifications']['online_confirmations'] = $request->boolean('online_confirmations');
        $user->preferences = $prefs;
        $user->save();
        return back()->with('success','Notification preferences saved.');
    }

    public function revokeOtherSessions(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required','string'],
        ]);
        $user = Auth::user();
        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Password is incorrect.']);
        }
        Auth::logoutOtherDevices($request->input('current_password'));
        return back()->with('success','Signed out from other sessions.');
    }

    public function toggleTwoFactor(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $request->validate([
            'enabled' => ['required','in:0,1'],
        ]);
        $prefs = $user->preferences ?? [];
        $enabled = $request->input('enabled') === '1';
        $prefs['two_factor']['enabled'] = $enabled;
        if ($enabled && empty($prefs['two_factor']['recovery_codes'])) {
            $prefs['two_factor']['recovery_codes'] = $this->generateRecoveryCodes();
        }
        $prefs['two_factor']['updated_at'] = now()->toISOString();
        $user->preferences = $prefs;
        $user->save();
        return back()->with('success', $enabled ? 'Two-factor authentication enabled.' : 'Two-factor authentication disabled.');
    }

    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $prefs = $user->preferences ?? [];
        $prefs['two_factor']['recovery_codes'] = $this->generateRecoveryCodes();
        $prefs['two_factor']['updated_at'] = now()->toISOString();
        $user->preferences = $prefs;
        $user->save();
        return back()->with('success','Recovery codes regenerated.');
    }

    private function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i=0; $i<8; $i++) {
            $codes[] = Str::upper(Str::random(4)).'-'.Str::upper(Str::random(4));
        }
        return $codes;
    }
}
