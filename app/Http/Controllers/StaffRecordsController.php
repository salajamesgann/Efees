<?php

namespace App\Http\Controllers;

use App\Models\FeeRecord;
use App\Models\FeeUpdateAudit;
use App\Models\SystemSetting;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StaffRecordsController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $this->checkPermission();

        $data = $request->validate([
            'student_id' => ['required', 'string'],
            'record_type' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'balance' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['pending', 'paid', 'overdue', 'cancelled'])],
            'notes' => ['nullable', 'string'],
            'payment_date' => ['nullable', 'date'],
        ]);

        return DB::transaction(function () use ($data) {
            $amount = (float) $data['amount'];
            $balance = array_key_exists('balance', $data) && $data['balance'] !== null ? (float) $data['balance'] : $amount;
            if ($balance > $amount) {
                $balance = $amount;
            }
            $status = $data['status'];
            if ($balance <= 0) {
                $status = 'paid';
                $balance = 0;
            }
            $record = FeeRecord::create([
                'student_id' => $data['student_id'],
                'record_type' => $data['record_type'],
                'amount' => $amount,
                'balance' => $balance,
                'status' => $status,
                'notes' => $data['notes'] ?? null,
                'payment_date' => $data['payment_date'] ?? null,
            ]);
            $this->recalculateStudentBalances($record->student_id);

            // Audit Log
            $this->logAudit('create', "Created FeeRecord #{$record->id} for student {$record->student_id}. Amount: {$amount}, Status: {$status}");

            // New Audit Log
            try {
                AuditService::log(
                    'Fee Record Created',
                    $record,
                    "Created fee record for student {$record->student_id} ({$record->record_type})",
                    null,
                    $record->toArray()
                );
            } catch (\Throwable $e) {
            }

            return back()->with('success', 'Fee record added');
        });
    }

    public function update(Request $request, FeeRecord $record): RedirectResponse
    {
        $this->checkPermission();
        if ($record->status === 'paid') {
            return back()->with('error', 'Editing paid records is not allowed.');
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'balance' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['pending', 'paid', 'overdue', 'cancelled'])],
            'notes' => ['nullable', 'string'],
            'payment_date' => ['nullable', 'date'],
        ]);

        return DB::transaction(function () use ($record, $data) {
            $oldValues = $record->only(['amount', 'balance', 'status', 'notes', 'payment_date']);

            $amount = (float) $data['amount'];
            $balance = (float) $data['balance'];
            if ($balance > $amount) {
                $balance = $amount;
            }
            $status = $data['status'];
            if ($balance <= 0) {
                $status = 'paid';
                $balance = 0;
            }
            $record->update([
                'amount' => $amount,
                'balance' => $balance,
                'status' => $status,
                'notes' => $data['notes'] ?? null,
                'payment_date' => $data['payment_date'] ?? null,
            ]);
            $this->recalculateStudentBalances($record->student_id);

            // Audit Log
            $changes = [];
            foreach ($data as $key => $val) {
                if (($oldValues[$key] ?? null) != $val) {
                    $changes[] = "$key: ".($oldValues[$key] ?? 'null')." -> $val";
                }
            }
            $changeStr = implode(', ', $changes);
            $this->logAudit('update', "Updated FeeRecord #{$record->id} for student {$record->student_id}. Changes: {$changeStr}");

            // New Audit Log
            try {
                AuditService::log(
                    'Fee Record Updated',
                    $record,
                    "Updated fee record #{$record->id} for student {$record->student_id}",
                    $oldValues,
                    $record->toArray()
                );
            } catch (\Throwable $e) {
            }

            return back()->with('success', 'Fee record updated');
        });
    }

    private function recalculateStudentBalances(string $studentId): void
    {
        $records = FeeRecord::where('student_id', $studentId)->get();
        foreach ($records as $rec) {
            $status = $rec->status;
            if ($rec->balance <= 0) {
                $status = 'paid';
                if ($rec->balance !== 0) {
                    $rec->balance = 0;
                }
            } elseif ($status === 'paid' && $rec->balance > 0) {
                $status = 'pending';
            }
            if ($status !== $rec->status || $rec->isDirty('balance')) {
                $rec->status = $status;
                $rec->save();
            }
        }
    }

    private function checkPermission(): void
    {
        abort(403, 'Staff accounts are view-only (Monitoring Mode).');
    }

    private function logAudit(string $type, string $message): void
    {
        FeeUpdateAudit::create([
            'performed_by_user_id' => Auth::id(),
            'event_type' => 'staff_fee_'.$type,
            'message' => $message,
            'affected_students_count' => 1,
            'school_year' => SystemSetting::where('key', 'school_year')->value('value'),
            'semester' => SystemSetting::where('key', 'semester')->value('value'),
        ]);
    }
}
