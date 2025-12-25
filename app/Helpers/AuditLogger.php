<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Log a create operation
     */
    public static function logCreate(string $entity, $model, array $additionalData = []): void
    {
        Log::info("Created {$entity}", array_merge([
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
        ], $additionalData));
    }

    /**
     * Log an update operation
     */
    public static function logUpdate(string $entity, $model, array $changes = []): void
    {
        Log::info("Updated {$entity}", [
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
        ]);
    }

    /**
     * Log a delete operation
     */
    public static function logDelete(string $entity, $id, array $additionalData = []): void
    {
        Log::warning("Deleted {$entity}", array_merge([
            'action' => 'delete',
            'entity' => $entity,
            'entity_id' => $id,
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'user_role' => Auth::user()?->getRoleNames()->first(),
            'school_id' => Auth::user()?->school_id,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toDateTimeString(),
        ], $additionalData));
    }

    /**
     * Log a failed authorization attempt
     */
    public static function logUnauthorized(string $action, string $entity, $id = null): void
    {
        Log::warning("Unauthorized {$action} attempt on {$entity}", [
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
        ]);
    }
}
