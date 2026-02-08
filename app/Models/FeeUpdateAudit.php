<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeUpdateAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'performed_by_user_id',
        'event_type',
        'school_year',
        'semester',
        'affected_students_count',
        'affected_staff_count',
        'message',
    ];

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_user_id', 'user_id');
    }
}
