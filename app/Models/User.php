<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
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
        'lockout_until' => 'datetime',
        'password_expires_at' => 'datetime',
        'preferences' => 'array',
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
        'failed_login_attempts',
        'lockout_until',
        'password_expires_at',
        'preferences',
    ];

    /**
     * Get the role name for the user.
     */
    public function getRoleName(): ?string
    {
        $roleName = optional($this->role)->role_name;

        // Fallback: Check roleable_type if role_name is missing
        if (! $roleName) {
            if ($this->roleable_type === 'App\\Models\\Admin') {
                $roleName = 'admin';
            } elseif ($this->roleable_type === 'App\\Models\\Staff') {
                $roleName = 'staff';
            } elseif ($this->roleable_type === 'App\\Models\\ParentContact') {
                $roleName = 'parent';
            } elseif ($this->roleable_type === 'App\\Models\\Student') {
                $roleName = 'student';
            }
        }

        return $roleName;
    }

    /**
     * Check if user has a specific role.
     * Support multiple roles separated by |
     */
    public function hasRole(string $role): bool
    {
        $roles = explode('|', $role);
        return in_array($this->getRoleName(), $roles);
    }

    /**
     * Get the name from the roleable entity.
     */
    public function getNameAttribute()
    {
        if ($this->roleable) {
            if ($this->roleable_type === 'App\\Models\\Student' || $this->roleable_type === 'App\\Models\\Staff' || $this->roleable_type === 'App\\Models\\ParentContact') {
                return $this->roleable->full_name;
            }
        }

        return $this->email;
    }

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
     * Get the student's full name if user is a student.
     */
    public function getFullNameAttribute()
    {
        if ($this->roleable_type === 'App\\Models\\Student' && $this->roleable) {
            return $this->roleable->full_name;
        }

        return $this->email;
    }

    public function getIsActiveAttribute()
    {
        if ($this->roleable) {
            if ($this->roleable_type === 'App\\Models\\Staff' || $this->roleable_type === 'App\\Models\\Admin') {
                return (bool) ($this->roleable->is_active ?? true);
            }
            if ($this->roleable_type === 'App\\Models\\ParentContact') {
                return ($this->roleable->account_status ?? 'Active') === 'Active';
            }
            if ($this->roleable_type === 'App\\Models\\Student') {
                if (property_exists($this->roleable, 'deleted_at') && $this->roleable->deleted_at) {
                    return false;
                }
                $status = $this->roleable->enrollment_status ?? 'Enrolled';

                return strtolower($status) === 'enrolled';
            }
        }

        return true;
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
     * Get the student relationship for backward compatibility.
     */
    public function student()
    {
        // Only maps roleable_id to students; do not filter on roleable_type here
        // because roleable_type lives on the users table, not students.
        return $this->belongsTo(Student::class, 'roleable_id', 'student_id')->withTrashed();
    }
}
