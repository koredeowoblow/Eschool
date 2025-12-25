<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Session;

class CheckSessionStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check strict session_id from Input (e.g. Creating Result, Assignment)
        if ($sessionId = $request->input('session_id')) {
            $session = Session::find($sessionId);
            if ($session && ($session->status === 'closed' || $session->status === 'locked')) {
                return response()->json(['message' => 'Action denied: The academic session is closed.'], 403);
            }
        }

        // 2. Check strict active session for modifications if no ID passed (Optional but safer)
        // If we are PUT/POST/DELETE and not SuperAdmin?
        // Implementing strict lock based on input is the safest first step.

        return $next($request);
    }
}
