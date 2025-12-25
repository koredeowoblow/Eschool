<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    protected $fillable = [
        'school_id',
        'user_id',
        'employee_number',
        'hire_date',
        'qualification',
        'department',
        'bio',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
