<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Users\GuardianService;
use App\Helpers\ResponseHelper;
use App\Http\Requests\User\StoreGuardianRequest;
use App\Http\Requests\User\UpdateGuardianRequest;

class GuardianController extends Controller
{
    public function __construct(private GuardianService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|School Admin|Teacher')->only(['index', 'show']);
        $this->middleware('role:super_admin|School Admin')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list([
            'user_id' => $request->query('user_id'),
            'email' => $request->query('email'),
            'relation' => $request->query('relation'),
        ]);
        return ResponseHelper::success($data, 'Guardians fetched successfully');
    }

    public function store(StoreGuardianRequest $request)
    {
        $validated = $request->validated();

        $model = $this->service->createAndAttach($validated, $validated['student_ids'] ?? []);
        return ResponseHelper::success($model, 'Guardian created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return ResponseHelper::success($model, 'Guardian fetched successfully');
    }

    public function update(UpdateGuardianRequest $request, string $id)
    {
        $validated = $request->validated();

        $updated = $this->service->updateAndSync($id, $validated, $validated['student_ids'] ?? null);
        return ResponseHelper::success($updated, 'Guardian updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return ResponseHelper::success(null, 'Guardian deleted successfully');
    }
}
