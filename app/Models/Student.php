<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasTenancyTrait;

class Student extends Model
{
    use HasFactory, SoftDeletes, HasTenancyTrait;

    protected $fillable = [
        'user_id',
        'admission_number',
        'admission_date',
        'status',
        'class_id',
        'section_id',
        'school_session_id',
        'school_id',
        'blood_group',
        'emergency_contact',
        'medical_conditions',
    ];

    protected $appends = [
        'full_name',
        'current_class',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'status' => 'boolean',
        'medical_conditions' => 'array',
    ];

    /**
     * Get the student's full name from the related user.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->user ? $this->user->name : '';
    }

    /**
     * Get the student's current class name.
     *
     * @return string
     */
    public function getCurrentClassAttribute()
    {
        return $this->classRoom ? $this->classRoom->name : 'Unassigned';
    }

    /**
     * Scope a query to only include active students.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Tenant
    // School relationship handled by HasTenancyTrait

    // Core relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }


    // Academic
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(Attendance::class);
    }

    // Finance
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Guardianship
    public function guardians()
    {
        return $this->belongsToMany(Guardian::class, 'student_guardians');
    }
}
