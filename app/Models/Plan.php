<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'no_of_students',
        'no_of_teachers',
        'no_of_guardians',
        'no_of_staff',
    ];

    public function schools()
    {
        // This might be indirect now through SchoolPlan, or we can keep it if schools still have a direct link? 
        // Migration dropped plan_id from schools. So this relationship is broken directly.
        // Schools have school_plan_id. SchoolPlan has plan_id.
        return $this->hasManyThrough(School::class, SchoolPlan::class, 'plan_id', 'school_plan_id');
    }

    public function schoolPlans()
    {
        return $this->hasMany(SchoolPlan::class);
    }
}
