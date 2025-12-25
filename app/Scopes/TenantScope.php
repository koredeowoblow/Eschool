<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Super Admin bypasses tenancy
            if ($user->hasRole('super_admin')) {
                return;
            }

            // If user has a school_id, filter by it
            if ($user->school_id) {
                $builder->where($model->qualifyColumn('school_id'), $user->school_id);
            }
        }
    }
}
