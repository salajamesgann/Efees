<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TuitionFee extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tuition_fees';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'grade_level',
        'amount',
        'school_year',
        'semester',
        'is_active',
        'payment_schedule',
        'subject_fees',
        'default_discount_ids',
        'default_charge_ids',
        'notes',
        'track',
        'strand',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
        'payment_schedule' => 'array',
        'subject_fees' => 'array',
        'default_discount_ids' => 'array',
        'default_charge_ids' => 'array',
    ];

    /**
     * Get the fee assignments for this tuition fee.
     */
    public function feeAssignments(): HasMany
    {
        return $this->hasMany(FeeAssignment::class);
    }

    /**
     * Get applicable additional charges for this tuition fee.
     */
    public function applicableCharges()
    {
        return AdditionalCharge::active()->applicableToGrade($this->grade_level);
    }

    /**
     * Get applicable discounts for this tuition fee.
     */
    public function applicableDiscounts()
    {
        return Discount::active()->applicableToGrade($this->grade_level);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(TuitionFeeCharge::class);
    }

    /**
     * Scope to get active tuition fees.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by grade level.
     */
    public function scopeForGrade($query, $gradeLevel)
    {
        return $query->where('grade_level', $gradeLevel);
    }

    /**
     * Scope to filter by school year.
     */
    public function scopeForSchoolYear($query, $schoolYear)
    {
        return $query->where('school_year', $schoolYear);
    }

    /**
     * Scope to filter by semester.
     */
    public function scopeForSemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Get the formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₱'.number_format($this->amount, 2);
    }

    /**
     * Get the full fee description.
     */
    public function getFullDescriptionAttribute(): string
    {
        return "{$this->grade_level} - {$this->school_year} - {$this->semester}";
    }
}
