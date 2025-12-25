<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasTenancyTrait;

class Result extends Model
{
    use HasTenancyTrait;

    protected $fillable = [
        'assessment_id',
        'student_id',
        'marks_obtained',
        'grade',
        'remark',
        'school_id',
    ];
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}
