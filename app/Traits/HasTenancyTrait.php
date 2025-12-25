<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Support\Facades\Auth;

trait HasTenancyTrait
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            // Automatically set school_id if not already set
            if (Auth::check() && !$model->school_id) {
                $user = Auth::user();
                if ($user->school_id && !$user->hasRole('super_admin')) {
                    $model->school_id = $user->school_id;
                }
            }
        });
    }

    /**
     * Get the school that owns the model.
     */
    public function school()
    {
        return $this->belongsTo(\App\Models\School::class);
    }
}
