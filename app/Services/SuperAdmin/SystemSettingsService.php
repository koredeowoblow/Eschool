<?php

namespace App\Services\SuperAdmin;

class SystemSettingsService
{
    public function getSettings()
    {
        // Mocking settings for now. In reality, fetch from a settings table or config file.
        return [
            'site_name' => config('app.name'),
            'maintenance_mode' => false,
            'allow_registration' => true,
        ];
    }

    public function updateSettings(array $data)
    {
        // Logic to update settings
        return true;
    }
}
