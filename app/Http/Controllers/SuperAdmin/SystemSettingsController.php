<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdmin\SystemSettingsService;
use Illuminate\Http\Request;

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
            return response()->json([
                'status' => 'success',
                'data' => $this->settingsService->getSettings()
            ]);
        }
        return view('super_admin.settings.index');
    }

    public function update(Request $request)
    {
        // Update logic
        $this->settingsService->updateSettings($request->all());
        return response()->json(['status' => 'success', 'message' => 'Settings updated']);
    }
}
