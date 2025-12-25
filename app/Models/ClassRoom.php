<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenancyTrait;

class ClassRoom extends Model
{
    use HasTenancyTrait;
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'section_id',
        'session_id',
        'term_id',
        'class_teacher_id',
        'school_id',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function classTeacher()
    {
        return $this->belongsTo(TeacherProfile::class, 'class_teacher_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects', 'class_id', 'subject_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'class_id');
    }

    public function getNameAttribute()
    {
        $baseName = $this->attributes['name'] ?? 'N/A';
        $sectionName = $this->section ? $this->section->name : 'Main';
        return "{$baseName} ({$sectionName})";
    }


    protected $appends = ['name'];
}
