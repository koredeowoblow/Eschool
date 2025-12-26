<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasUuids;
    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone',
        'email',
        'state',
        'area',
        'city',
        'website',
        'contact_person',
        'school_plan_id',
        'contact_person_phone',
        'status',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['status_label'];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically sync status field with is_active
        static::saving(function ($school) {
            if (isset($school->is_active)) {
                $school->status = $school->is_active ? 'active' : 'inactive';
            }
        });
    }

    public function getStatusAttribute($value)
    {
        // Source of Truth is is_active
        return $this->is_active ? 'active' : 'inactive';
    }

    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }


    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function schoolPlan()
    {
        return $this->belongsTo(SchoolPlan::class);
    }

    // Helper to get limit
    public function getLimit($type)
    {
        return $this->schoolPlan ? $this->schoolPlan->{'no_of_' . $type} : 0;
    }
}
