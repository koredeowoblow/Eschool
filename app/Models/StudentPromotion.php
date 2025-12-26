<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenancyTrait;

class StudentPromotion extends Model
{
    use HasTenancyTrait;

    protected $fillable = [
        'school_id',
        'student_id',
        'from_class_id',
        'to_class_id',
        'from_session_id',
        'to_session_id',
        'type',
        'promoted_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function fromClass()
    {
        return $this->belongsTo(ClassRoom::class, 'from_class_id');
    }

    public function toClass()
    {
        return $this->belongsTo(ClassRoom::class, 'to_class_id');
    }

    public function fromSession()
    {
        return $this->belongsTo(Session::class, 'from_session_id');
    }

    public function toSession()
    {
        return $this->belongsTo(Session::class, 'to_session_id');
    }

    public function promotedBy()
    {
        return $this->belongsTo(User::class, 'promoted_by');
    }
}
