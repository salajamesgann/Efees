<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'students';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'student_id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'lrn',
        'first_name',
        'middle_name',
        'middle_initial',
        'last_name',
        'suffix',
        'date_of_birth',
        'sex',
        'level',
        'section',
        'school_year',
        'enrollment_status',
        'address',
        'nationality',
        'profile_picture_url',
        'strand',
        'is_shs_voucher',
    ];

    /**
     * Get the user record associated with this student.
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'roleable');
    }

    /**
     * Get the parents associated with this student.
     */
    public function parents()
    {
        return $this->belongsToMany(\App\Models\ParentContact::class, 'parent_student', 'student_id', 'parent_id')
            ->withPivot(['relationship', 'is_primary'])
            ->withTimestamps();
    }

    /**
     * Get the SMS preferences for the student.
     */
    public function smsPreference(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StudentSmsPreference::class, 'student_id', 'student_id');
    }

    /**
     * Get the fee records associated with the student.
     */
    public function feeRecords(): HasMany
    {
        return $this->hasMany(FeeRecord::class, 'student_id', 'student_id');
    }

    /**
     * Get the fee assignments for the student.
     */
    public function feeAssignments()
    {
        return $this->hasMany(FeeAssignment::class, 'student_id', 'student_id');
    }

    /**
     * Get the payments associated with the student.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'student_id', 'student_id');
    }

    /**
     * Get the current fee assignment for a specific school year and semester.
     */
    public function getCurrentFeeAssignment($schoolYear = null, $semester = null)
    {
        $query = $this->feeAssignments();

        if ($schoolYear) {
            $query->where('school_year', $schoolYear);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        return $query->first();
    }

    /**
     * Get the current total balance for the student.
     */
    public function getCurrentBalanceAttribute()
    {
        $svc = app(\App\Services\FeeManagementService::class);
        $totals = $svc->computeTotalsForStudent($this);

        return (float) ($totals['remainingBalance'] ?? 0.0);
    }

    /**
     * Get the total amount paid by the student.
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()
            ->where(function ($q) {
                $q->whereIn('status', ['approved', 'paid'])
                    ->orWhereNull('status');
            })
            ->sum('amount_paid');
    }

    /**
     * Get the student's full name.
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name.' '.($this->middle_name ?? '').' '.$this->last_name);
    }

    /**
    }

    /**
     * Use student_id for route-model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'student_id';
    }
}
