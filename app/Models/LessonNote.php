<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasTenancyTrait;

class LessonNote extends Model
{
    use HasTenancyTrait;
    protected $fillable = [
        'teacher_id',
        'subject_id',
        'class_id',
        'title',
        'content',
        'school_id',
    ];
}
