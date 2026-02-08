<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSmsPreference extends Model
{
    use HasFactory;

    protected $table = 'student_sms_preferences';

    protected $fillable = [
        'student_id',
        'sms_due_reminder_enabled',
        'sms_payment_confirm_enabled',
        'sms_overdue_enabled',
        'updated_by',
    ];

    protected $casts = [
        'sms_due_reminder_enabled' => 'boolean',
        'sms_payment_confirm_enabled' => 'boolean',
        'sms_overdue_enabled' => 'boolean',
    ];

    /**
     * Get the student that owns the preferences.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Get the user who last updated the preferences.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }
}
