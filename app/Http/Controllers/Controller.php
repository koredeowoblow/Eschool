<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Convenience wrapper for standardized success responses.
     */
    protected function success($data = null, string $message = 'Operation successful', int $statusCode = 200, array $meta = [])
    {
        return get_success_response($data, $message, $statusCode, $meta);
    }

    /**
     * Convenience wrapper for standardized error responses.
     */
    protected function error(string $message = 'Error', int $statusCode = 400, $errors = null, array $meta = [])
    {
        return get_error_response($message, $statusCode, $errors, $meta);
    }
}
