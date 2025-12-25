<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasTenancyTrait;

class Subject extends Model
{
    use HasTenancyTrait;

    protected $fillable = [
        'name',
        'code',
        'school_id',
    ];
}
