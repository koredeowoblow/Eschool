<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasTenancyTrait;

class Assignment extends Model
{
    use HasTenancyTrait;

    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'title',
        'description',
        'due_date',
        'school_id',
    ];
}
