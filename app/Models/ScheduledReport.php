<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_type',
        'parameters',
        'frequency',
        'next_run_at',
        'created_by',
        'status',
    ];

    protected $casts = [
        'parameters' => 'array',
        'next_run_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
