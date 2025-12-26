<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'no_of_students',
    ];

    public function schools()
    {
        return $this->hasMany(School::class);
    }
}
