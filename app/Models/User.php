<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
<<<<<<< HEAD
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
=======
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
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'must_change_password' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'password',
        'must_change_password',
        'role_id',
        'roleable_type',
        'roleable_id',
    ];

    /**
     * Get the roleable entity that the user belongs to.
     */
    public function roleable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the role associated with the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return optional($this->role)->role_name === $role;
    }

    /**
     * Get the student's full name if user is a student.
     */
    public function getFullNameAttribute()
    {
        if ($this->roleable_type === 'App\\Models\\Student' && $this->roleable) {
            return $this->roleable->full_name;
        }

        return $this->email;
    }

    /**
     * Get the student's initials if user is a student.
     */
    public function getInitialsAttribute()
    {
        if ($this->roleable_type === 'App\\Models\\Student' && $this->roleable) {
            $firstName = substr($this->roleable->first_name, 0, 1);
            $lastName = substr($this->roleable->last_name, 0, 1);

            return strtoupper($firstName.$lastName);
        }

        return strtoupper(substr($this->email, 0, 2));
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
