<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasTenancyTrait;

class Attendance extends Model
{
    use HasTenancyTrait;
    protected $table = 'attendance';

    protected $fillable = [
        'student_id',
        'class_id',
        'date',
        'status',
        'school_id',
    ];
}
