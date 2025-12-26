<?php

use Illuminate\Support\Str;
use App\Helpers\ResponseHelper;

if (!function_exists('get_success_response')) {
    function get_success_response($data = null, string $message = 'Operation successful', int $statusCode = 200, array $meta = [])
    {
        return ResponseHelper::success($data, $message, $statusCode, $meta);
    }
}

if (!function_exists("pageCount")) {
    function pageCount()
    {
        return request()->get("per_page", config('services.utils.paginate_per_page', 100));
    }
}

if (!function_exists('get_error_response')) {
    function get_error_response($message = 'Error', int $statusCode = 400, $errors = null, array $meta = [])
    {
        return ResponseHelper::error($message, $statusCode, $errors, $meta);
    }
}

if (! function_exists('generate_uuid')) {
    function generate_uuid()
    {
        return Str::uuid()->toString();
    }
}
