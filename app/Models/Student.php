<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

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
        $query = $this->feeAssignments()->with('tuitionFee')->orderByDesc('created_at');

        $sy = $schoolYear ?: ($this->school_year ?? null);
        if ($sy) {
            $query->where('school_year', $sy);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        // Prefer assignments whose tuition fee matches student's current grade
        $query->where(function ($q) {
            $q->whereHas('tuitionFee', function ($tq) {
                $tq->where('grade_level', $this->level);
            })->orWhereNull('tuition_fee_id');
        });

        $assignment = $query->first();

        // If no tuition fee exists for this grade/year, ignore any stale assignment
        $hasTuition = \App\Models\TuitionFee::active()->forGrade($this->level)->where('school_year', $sy)->exists();
        if (! $hasTuition) {
            return null;
        }

        return $assignment;
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
        $query = $this->payments();

        if (Schema::hasColumn('payments', 'status')) {
            $query->where(function ($paymentQuery) {
                $paymentQuery->whereIn('status', ['approved', 'paid'])
                    ->orWhereNull('status');
            });
        }

        return $query->sum('amount_paid');
    }

    /**
     * Get the student's full name.
     */
    public function getFullNameAttribute()
    {
        return preg_replace('/\s+/', ' ', trim($this->first_name.' '.($this->middle_name ?? '').' '.$this->last_name));
    }

    /**
     * Get all siblings of this student (other students sharing a parent).
     * Returns a collection of Student models (deduplicated).
     */
    public function getSiblingsAttribute()
    {
        return $this->getSiblings();
    }

    /**
     * Get siblings of this student via shared parent contacts.
     *
     * @param  string|null  $schoolYear  Optionally filter to a specific school year.
     * @return \Illuminate\Support\Collection<Student>
     */
    public function getSiblings(?string $schoolYear = null): \Illuminate\Support\Collection
    {
        $this->loadMissing('parents');

        $parentIds = $this->parents->pluck('id');

        if ($parentIds->isEmpty()) {
            return collect();
        }

        $query = static::whereHas('parents', function ($q) use ($parentIds) {
            $q->whereIn('parents.id', $parentIds);
        })->where('student_id', '!=', $this->student_id);

        if ($schoolYear) {
            $query->where('school_year', $schoolYear);
        }

        return $query->get()->unique('student_id');
    }

    /**
     * Count active siblings (Active/Irregular) in the same school year.
     */
    public function getActiveSiblingCountAttribute(): int
    {
        $this->loadMissing('parents');

        $parentIds = $this->parents->pluck('id');

        if ($parentIds->isEmpty()) {
            return 0;
        }

        return static::whereHas('parents', function ($q) use ($parentIds) {
            $q->whereIn('parents.id', $parentIds);
        })
            ->where('student_id', '!=', $this->student_id)
            ->where('school_year', $this->school_year)
            ->whereIn('enrollment_status', ['Active', 'Irregular'])
            ->count();
    }

    /**
     * Use student_id for route-model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'student_id';
    }
}
