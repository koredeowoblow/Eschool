<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasTenancyTrait;

class Term extends Model
{
    use HasTenancyTrait;

    protected $fillable = [
        "session_id",
        "name",
        "start_date",
        "end_date",
        "status",
        'school_id'
    ];
}
