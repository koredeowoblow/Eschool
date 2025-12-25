<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Users\GuardianService;

class GuardianController extends Controller
{
    public function __construct(private GuardianService $service)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:super_admin|school_admin|teacher')->only(['index', 'show']);
        $this->middleware('role:super_admin|school_admin')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $data = $this->service->list([
            'user_id' => $request->query('user_id'),
            'email' => $request->query('email'),
            'relation' => $request->query('relation'),
        ]);
        return get_success_response($data, 'Guardians fetched successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'relation' => 'required|string|max:100',
            'occupation' => 'nullable|string|max:255',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'integer|exists:students,id',
        ]);

        $model = $this->service->createAndAttach($validated, $validated['student_ids'] ?? []);
        return get_success_response($model, 'Guardian created successfully', 201);
    }

    public function show(string $id)
    {
        $model = $this->service->get($id);
        return get_success_response($model, 'Guardian fetched successfully');
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'relation' => 'sometimes|string|max:100',
            'occupation' => 'nullable|string|max:255',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'integer|exists:students,id',
        ]);

        $updated = $this->service->updateAndSync($id, $validated, $validated['student_ids'] ?? null);
        return get_success_response($updated, 'Guardian updated successfully');
    }

    public function destroy(string $id)
    {
        $this->service->delete($id);
        return get_success_response(null, 'Guardian deleted successfully');
    }
}
