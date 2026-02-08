<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Strand extends Model
{
    protected $fillable = ['name'];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}
