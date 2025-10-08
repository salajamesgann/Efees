<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Student extends Model
{
    use HasFactory;

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
    public $timestamps = false;

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
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'contact_number' => 'string',
        'level' => 'string',
        'middle_initial' => 'string',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'first_name',
        'middle_initial',
        'last_name',
        'contact_number',
        'sex',
        'level',
        'section',
        'profile_picture_url',
        'department',
    ];

    /**
     * Get the user record associated with this student.
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'roleable');
    }

    /**
     * Get the fee records for the student.
     */
    public function feeRecords()
    {
        return $this->hasMany(FeeRecord::class, 'student_id', 'student_id');
    }

    /**
     * Get the student's full name.
     */
    public function getFullNameAttribute()
    {
        $middleInitial = $this->middle_initial ? " {$this->middle_initial}. " : ' ';
        return trim("{$this->first_name}{$middleInitial}{$this->last_name}");
    }

    /**
     * Use student_id for route-model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'student_id';
    }
}
