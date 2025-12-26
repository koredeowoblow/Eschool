<?php

namespace App\Services\Users;

use App\Repositories\Users\GuardianRepository;
use App\Models\Guardian;
use Illuminate\Support\Facades\Auth;
use App\Models\School;

class GuardianService
{
    public function __construct(public GuardianRepository $repo) {}

    public function list(array $filters = [])
    {
        return $this->repo->list($filters);
    }

    public function get(int|string $id): Guardian
    {
        $query = Guardian::where('id', $id);

        if (!Auth::user()->hasRole('super_admin')) {
            $query->whereHas('user', function ($q) {
                $q->where('school_id', Auth::user()->school_id ?? null);
            });
        }

        return $query->with(['user', 'students'])
            ->firstOrFail();
    }

    public function createAndAttach(array $data, array $studentIds = []): Guardian
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        // Check Plan Limit
        $school = School::find($schoolId);
        if ($school) {
            $limit = $school->getLimit('guardians');
            if ($limit > 0) {
                $currentCount = Guardian::where('school_id', $schoolId)->count();
                if ($currentCount >= $limit) {
                    throw new \Exception("Guardian limit reached for this school plan ({$limit}). Upgrade your plan to add more guardians.");
                }
            }
        }

        // Security: Filter student IDs to ensure they belong to the same school
        if (!empty($studentIds) && !$user->hasRole('super_admin')) {
            $studentIds = \App\Models\Student::whereIn('id', $studentIds)
                ->where('school_id', $schoolId)
                ->pluck('id')
                ->toArray();
        }

        $guardian = $this->repo->create([
            'user_id' => $data['user_id'],
            'relation' => $data['relation'],
            'occupation' => $data['occupation'] ?? null,
            'school_id' => $schoolId,
        ]);

        if (!empty($studentIds)) {
            $guardian->students()->syncWithoutDetaching($studentIds);
        }

        return $guardian->load(['user', 'students']);
    }

    public function updateAndSync(int|string $id, array $data, ?array $studentIds = null): Guardian
    {
        $guardian = $this->get($id);
        $user = Auth::user();
        $schoolId = $user->school_id;

        $guardian->update([
            'relation' => $data['relation'] ?? $guardian->relation,
            'occupation' => $data['occupation'] ?? $guardian->occupation,
        ]);

        if (is_array($studentIds)) {
            // Security: Filter student IDs to ensure they belong to the same school
            if (!$user->hasRole('super_admin')) {
                $studentIds = \App\Models\Student::whereIn('id', $studentIds)
                    ->where('school_id', $schoolId)
                    ->pluck('id')
                    ->toArray();
            }
            $guardian->students()->sync($studentIds);
        }

        return $guardian->load(['user', 'students']);
    }

    public function delete(int|string $id): bool
    {
        $guardian = $this->get($id);
        $guardian->students()->detach();
        return (bool) $guardian->delete();
    }
}
