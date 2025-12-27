<?php

namespace App\Services\Results;

use App\Models\Result;
use App\Models\ResultVersion;
use App\Helpers\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Exception;
use Illuminate\Support\Facades\DB;

class ResultWorkflowService
{
    /**
     * Submit results for review (Teacher action)
     */
    public function submitForReview(array $resultIds): array
    {
        $user = Auth::user();

        if (!$user->can('results.submit.review')) {
            AuditLogger::logUnauthorized('submit_results', 'result', implode(',', $resultIds));
            throw new AuthorizationException("You do not have permission to submit results for review.");
        }

        return DB::transaction(function () use ($resultIds, $user) {
            $results = Result::whereIn('id', $resultIds)
                ->where('school_id', $user->school_id)
                ->where('status', Result::STATUS_DRAFT)
                ->get();

            foreach ($results as $result) {
                $result->update([
                    'status' => Result::STATUS_SUBMITTED,
                    'submitted_at' => now()
                ]);

                // Create version snapshot
                $this->createVersion($result, 'submitted', 'Submitted for review');

                AuditLogger::logUpdate('result', $result, ['status' => 'submitted']);
            }

            return ['count' => $results->count()];
        });
    }

    /**
     * Review results (Exams Officer action)
     */
    public function reviewResults(array $resultIds, bool $approved, ?string $comment = null): array
    {
        $user = Auth::user();

        if (!$user->can('results.review')) {
            AuditLogger::logUnauthorized('review_results', 'result', implode(',', $resultIds));
            throw new AuthorizationException("You do not have permission to review results.");
        }

        return DB::transaction(function () use ($resultIds, $approved, $comment, $user) {
            $results = Result::whereIn('id', $resultIds)
                ->where('school_id', $user->school_id)
                ->where('status', Result::STATUS_SUBMITTED)
                ->get();

            $newStatus = $approved ? Result::STATUS_REVIEWED : Result::STATUS_DRAFT;

            foreach ($results as $result) {
                $result->update([
                    'status' => $newStatus,
                    'reviewer_id' => $user->id,
                    'reviewed_at' => now()
                ]);

                $action = $approved ? 'approved' : 'rejected';
                $this->createVersion($result, $action, $comment ?? ucfirst($action) . ' by exams officer');

                AuditLogger::logUpdate('result', $result, [
                    'status' => $newStatus,
                    'action' => $action,
                    'comment' => $comment
                ]);
            }

            return ['count' => $results->count(), 'action' => $approved ? 'approved' : 'rejected'];
        });
    }

    /**
     * Publish results (Admin action)
     */
    public function publishResults(array $resultIds): array
    {
        $user = Auth::user();

        if (!$user->can('results.publish')) {
            AuditLogger::logUnauthorized('publish_results', 'result', implode(',', $resultIds));
            throw new AuthorizationException("You do not have permission to publish results.");
        }

        return DB::transaction(function () use ($resultIds, $user) {
            $results = Result::whereIn('id', $resultIds)
                ->where('school_id', $user->school_id)
                ->where('status', Result::STATUS_REVIEWED)
                ->get();

            foreach ($results as $result) {
                $result->update([
                    'status' => Result::STATUS_PUBLISHED,
                    'published_at' => now()
                ]);

                $this->createVersion($result, 'published', 'Published to students/guardians');

                AuditLogger::logUpdate('result', $result, ['status' => 'published']);
            }

            return ['count' => $results->count()];
        });
    }

    /**
     * Lock results (Admin action - makes them immutable)
     */
    public function lockResults(array $resultIds): array
    {
        $user = Auth::user();

        if (!$user->can('results.lock')) {
            AuditLogger::logUnauthorized('lock_results', 'result', implode(',', $resultIds));
            throw new AuthorizationException("You do not have permission to lock results.");
        }

        return DB::transaction(function () use ($resultIds, $user) {
            $results = Result::whereIn('id', $resultIds)
                ->where('school_id', $user->school_id)
                ->where('status', Result::STATUS_PUBLISHED)
                ->get();

            foreach ($results as $result) {
                $result->update(['status' => Result::STATUS_LOCKED]);

                $this->createVersion($result, 'locked', 'Result locked - no further edits allowed');

                AuditLogger::logUpdate('result', $result, ['status' => 'locked']);
            }

            return ['count' => $results->count()];
        });
    }

    /**
     * Update result with versioning
     */
    public function updateResult(int $id, array $data, ?string $reason = null): Result
    {
        $user = Auth::user();

        if (!$user->can('results.enter')) {
            AuditLogger::logUnauthorized('update_result', 'result', $id);
            throw new AuthorizationException("You do not have permission to update results.");
        }

        return DB::transaction(function () use ($id, $data, $reason, $user) {
            $result = Result::where('id', $id)
                ->where('school_id', $user->school_id)
                ->firstOrFail();

            // Check if editable
            if (!$result->isEditable()) {
                throw new Exception("This result cannot be edited. Status: {$result->status}");
            }

            // Create version before update
            $this->createVersion($result, 'updated', $reason ?? 'Result updated');

            $result->update($data);

            AuditLogger::logUpdate('result', $result, ['fields' => array_keys($data)]);

            return $result->fresh();
        });
    }

    /**
     * Create a version snapshot
     */
    protected function createVersion(Result $result, string $action, ?string $reason = null): ResultVersion
    {
        return ResultVersion::create([
            'result_id' => $result->id,
            'school_id' => $result->school_id,
            'data' => $result->toArray(),
            'changed_by' => Auth::id(),
            'action' => $action,
            'reason' => $reason,
            'created_at' => now()
        ]);
    }
}
