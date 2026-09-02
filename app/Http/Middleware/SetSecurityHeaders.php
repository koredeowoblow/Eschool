<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetSecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Allow for responses that don't have a headers property
        if (method_exists($response, 'header')) {
            // HSTS
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            
            // CSP (Allowing local vite dev server and reverb)
            $csp = "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; " .
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; " .
                "img-src 'self' data: https:; " .
                "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; " .
                "connect-src 'self' https://api.eschool.com ws://localhost:8080 wss://localhost:8080 http://localhost:8080 ws://localhost:5173 http://localhost:5173 https://cdn.jsdelivr.net wss://*.pusher.com ws://*.pusher.com; " .
                "frame-ancestors 'self';";
            
            $response->header('Content-Security-Policy', $csp);
            
            // Other headers
            $response->header('X-Frame-Options', 'SAMEORIGIN');
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('X-XSS-Protection', '1; mode=block');
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
        }

        return $response;
    }
}
