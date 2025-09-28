<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     */
    protected $table = 'users';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'role_id',
        'roleable_type',
        'roleable_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role that this user belongs to.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    /**
     * Get the owning roleable model (Student, Admin, or Staff).
     */
    public function roleable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user's full name from their role-specific record.
     */
    public function getFullNameAttribute(): string
    {
        return $this->roleable ? $this->roleable->full_name : 'Unknown User';
    }

    /**
     * Get the user's initials from their role-specific record.
     */
    public function getInitialsAttribute(): string
    {
        return $this->roleable ? $this->roleable->initials : 'UU';
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->role_name === $roleName;
    }

    /**
     * Get the student relationship for backward compatibility.
     */
    public function student()
    {
        // Only maps roleable_id to students; do not filter on roleable_type here
        // because roleable_type lives on the users table, not students.
        return $this->belongsTo(Student::class, 'roleable_id', 'student_id');
    }
}
