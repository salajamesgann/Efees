<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsSchedule extends Model
{
    use HasFactory;

    protected $table = 'sms_schedules';

    protected $fillable = [
        'student_id',
        'schedule_time',
        'message',
        'status',
    ];

    protected $casts = [
        'schedule_time' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}
