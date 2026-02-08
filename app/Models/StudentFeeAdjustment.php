<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentFeeAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_assignment_id',
        'student_id',
        'type',
        'name',
        'amount',
        'remarks',
        'created_by',
    ];

    public function feeAssignment(): BelongsTo
    {
        return $this->belongsTo(FeeAssignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
