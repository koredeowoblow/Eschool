<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenancyTrait;

class SubjectResult extends Model
{
    use HasTenancyTrait;

    protected $fillable = [
        'student_id',
        'class_id',
        'subject_id',
        'session_id',
        'term_id',
        'ca_score',
        'exam_score',
        'total_score',
        'grade',
        'remark',
        'status',
        'is_collated',
        'locked_at',
        'school_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }
}
