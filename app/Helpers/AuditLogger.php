<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class AuditLogger
{
    /**
     * Log a create operation
     */
    public static function logCreate(string $entity, $model, array $additionalData = []): void
    {
        $logData = array_merge([
            'action' => 'create',
            'entity' => $entity,
            'entity_id' => $model->id ?? null,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'user_role' => Auth::user()?->getRoleNames()->first(),
            'school_id' => Auth::user()?->school_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ], $additionalData);

        // Log to file
        Log::info("Created {$entity}", $logData);

        // Log to database
        self::storeInDatabase($logData);
    }

    /**
     * Log an update operation
     */
    public static function logUpdate(string $entity, $model, array $changes = []): void
    {
        $logData = [
            'action' => 'update',
            'entity' => $entity,
            'entity_id' => $model->id ?? null,
            'changes' => $changes,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'user_role' => Auth::user()?->getRoleNames()->first(),
            'school_id' => Auth::user()?->school_id,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ];

        // Log to file
        Log::info("Updated {$entity}", $logData);

        // Log to database
        self::storeInDatabase($logData);
    }

    /**
     * Log a delete operation
     */
    public static function logDelete(string $entity, $id, array $additionalData = []): void
    {
        $logData = array_merge([
            'action' => 'delete',
            'entity' => $entity,
            'entity_id' => $id,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'user_role' => Auth::user()?->getRoleNames()->first(),
            'school_id' => Auth::user()?->school_id,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ], $additionalData);

        // Log to file
        Log::warning("Deleted {$entity}", $logData);

        // Log to database
        self::storeInDatabase($logData);
    }

    /**
     * Log a failed authorization attempt
     */
    public static function logUnauthorized(string $action, string $entity, $id = null): void
    {
        $logData = [
            'action' => 'unauthorized',
            'attempted_action' => $action,
            'entity' => $entity,
            'entity_id' => $id,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'user_role' => Auth::user()?->getRoleNames()->first(),
            'school_id' => Auth::user()?->school_id,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ];

        // Log to file
        Log::warning("Unauthorized {$action} attempt on {$entity}", $logData);

        // Log to database
        self::storeInDatabase($logData);
    }

    /**
     * Log a state change with before/after snapshots
     */
    public static function logStateChange(string $entity, $model, array $beforeState, array $afterState, ?string $reason = null): void
    {
        $logData = [
            'action' => 'state_change',
            'entity' => $entity,
            'entity_id' => $model->id ?? null,
            'before_state' => $beforeState,
            'after_state' => $afterState,
            'reason' => $reason,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'user_role' => Auth::user()?->getRoleNames()->first(),
            'school_id' => Auth::user()?->school_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ];

        Log::info("State changed for {$entity}", $logData);
        self::storeInDatabase($logData);
    }

    /**
     * Log role assignment/removal
     */
    public static function logRoleChange(string $userId, string $action, string $roleName, ?string $schoolId = null): void
    {
        $logData = [
            'action' => 'role_change',
            'entity' => 'user_role',
            'entity_id' => $userId,
            'role_action' => $action, // 'assigned' or 'removed'
            'role_name' => $roleName,
            'target_school_id' => $schoolId,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'user_role' => Auth::user()?->getRoleNames()->first(),
            'school_id' => Auth::user()?->school_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ];

        Log::warning("Role {$action}: {$roleName} for user {$userId}", $logData);
        self::storeInDatabase($logData);
    }

    /**
     * Log student promotion
     */
    public static function logPromotion(string $studentId, array $fromClass, array $toClass): void
    {
        $logData = [
            'action' => 'promotion',
            'entity' => 'student',
            'entity_id' => $studentId,
            'from_class' => $fromClass,
            'to_class' => $toClass,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'user_role' => Auth::user()?->getRoleNames()->first(),
            'school_id' => Auth::user()?->school_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ];

        Log::info("Student promoted: {$studentId}", $logData);
        self::storeInDatabase($logData);
    }

    /**
     * Store audit log in database
     */
    private static function storeInDatabase(array $logData): void
    {
        try {
            // Extract metadata (everything except the main fields)
            $metadata = array_diff_key($logData, array_flip([
                'action',
                'entity',
                'entity_id',
                'user_id',
                'user_email',
                'user_role',
                'school_id',
                'ip_address',
                'user_agent',
                'timestamp'
            ]));

            AuditLog::create([
                'action' => $logData['action'],
                'entity' => $logData['entity'],
                'entity_id' => $logData['entity_id'] ?? null,
                'user_id' => $logData['user_id'] ?? null,
                'user_email' => $logData['user_email'] ?? null,
                'user_role' => $logData['user_role'] ?? null,
                'school_id' => $logData['school_id'] ?? null,
                'ip_address' => $logData['ip_address'] ?? null,
                'user_agent' => $logData['user_agent'] ?? null,
                'metadata' => !empty($metadata) ? $metadata : null,
            ]);
        } catch (\Exception $e) {
            // If database logging fails, log the error but don't break the application
            Log::error("Failed to store audit log in database: " . $e->getMessage());
        }
    }
}
