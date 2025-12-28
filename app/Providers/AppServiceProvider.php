<?php

namespace App\Providers;

use App\Models\User;
use Database\Seeders\AppOwnerSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        // Only run when serving web requests, not Artisan or tests
        if (app()->runningInConsole() || app()->runningUnitTests()) {
            return;
        }

        // Schedule seeder check after app boots fully
        app()->booted(function () {
            $this->seedIfMissing();
        });
    }

    protected function seedIfMissing()
    {
        // Ensure this logic only runs once per install to avoid per-request DB overhead
        if (cache()->has('app_seed_if_missing_done')) {
            return;
        }

        if (! Schema::hasTable('roles') || ! Schema::hasTable('users')) {
            return;
        }

        if (DB::table('roles')->count() === 0) {
            (new RolesSeeder)->run();
        }

        if (User::whereHas('roles', fn($q) => $q->where('name', 'super_admin'))->count() === 0) {
            (new AppOwnerSeeder)->run();
        }

        cache()->forever('app_seed_if_missing_done', true);
    }
}
