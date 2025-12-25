<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasTenancyTrait;

class Assessment extends Model
{
    use HasTenancyTrait;

    protected $fillable = [
        'class_id',
        'term_id',
        'subject_id',
        'teacher_id',
        'type',
        'total_marks',
        'is_approved',
        'date',
        'school_id',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
    public function teacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
    public function term()
    {
        return $this->belongsTo(Term::class);
    }
}
