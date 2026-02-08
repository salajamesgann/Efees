<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentContact extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = [
        'full_name',
        'phone',
        'phone_secondary',
        'email',
        'address_street',
        'address_barangay',
        'address_city',
        'address_province',
        'address_zip',
        'account_status',
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id')
            ->withPivot(['relationship', 'is_primary'])
            ->withTimestamps();
    }

    public function scopeActive($q)
    {
        return $q->where('account_status', 'Active')->whereNull('archived_at');
    }

    public function scopeArchived($q)
    {
        return $q->where('account_status', 'Archived')->whereNotNull('archived_at');
    }
}
