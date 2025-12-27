<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdmin\SystemSettingsService;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Requests\SuperAdmin\SystemSettingsRequest;

class SystemSettingsController extends Controller
{
    protected $settingsService;

    public function __construct(SystemSettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function index()
    {
        if (request()->wantsJson()) {
            return ResponseHelper::success(
                $this->settingsService->getSettings(),
                'Settings fetched successfully'
            );
        }
        return view('super_admin.settings.index');
    }

    public function update(SystemSettingsRequest $request)
    {
        // Update logic
        $this->settingsService->updateSettings($request->validated());
        return ResponseHelper::success(null, 'Settings updated successfully');
    }
}
