<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;

class ResponseHelper
{
    /**
     * Standard success response
     */
    public static function success($data = null, string $message = 'Operation successful', int $statusCode = 200, array $meta = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'meta'    => $meta,
        ], $statusCode);
    }

    /**
     * Standard error response
     */
    public static function error(string $message = 'Error encountered', int $statusCode = 400, $errors = null, array $meta = []): JsonResponse
    {
        // Convert errors to MessageBag for consistency if needed
        if ($errors && !$errors instanceof MessageBag) {
            if (is_array($errors)) {
                $errors = new MessageBag($errors);
            } else {
                $errors = new MessageBag(['general' => [$errors]]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
            'meta'    => $meta,
        ], $statusCode);
    }

    /**
     * Unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized access'): JsonResponse
    {
        return self::error($message, 401);
    }

    /**
     * Forbidden response
     */
    public static function forbidden(string $message = 'Access forbidden'): JsonResponse
    {
        return self::error($message, 403);
    }

    /**
     * Not found response
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return self::error($message, 404);
    }
}
