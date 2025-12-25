<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenancyTrait;

class Session extends Model
{
    use HasTenancyTrait;
    protected $table = 'school_sessions';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'school_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];
}
