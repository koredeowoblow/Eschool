<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use App\Http\Middleware\EnsureEmailIsVerified;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api/v1')
                ->group(function () {
                    require base_path('routes/api.php');
                    require base_path('routes/api/assignment.php');
                    require base_path('routes/api/attendance.php');
                    require base_path('routes/api/chat.php');
                    require base_path('routes/api/class.php');
                    require base_path('routes/api/library.php');

                    require base_path('routes/api/payment.php');
                    require base_path('routes/api/fee.php');
                    require base_path('routes/api/report.php');
                    require base_path('routes/api/result.php');
                    require base_path('routes/api/student.php');
                    require base_path('routes/api/user.php');
                    require base_path('routes/api/settings.php');
                    require base_path('routes/api/plan.php');
                    require base_path('routes/api/school.php');
                });
        }
    )
    ->withBroadcasting(
        __DIR__ . '/../routes/channels.php',
        ['middleware' => ['api', 'auth:sanctum'], 'prefix' => 'api/v1'],
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'api/v1/*',
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'check.session' => \App\Http\Middleware\CheckSessionStatus::class,
        ]);
    })->withSchedule(function (Schedule $schedule) {
        // $schedule->command('budget:send-reminders')->dailyAt('08:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return get_error_response("Invalid request method for this endpoint", 405);
            }
        });

        // Handle models not found (404)
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return get_error_response("Resource not found", 404);
            }
        });

        // Handle routes not found (404)
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return get_error_response("Route not found", 404);
            }
        });

        // Handle validation errors (422)
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return get_error_response("Validation failed", 422, $e->errors());
            }
        });

        // Handle authentication errors (401)
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return get_error_response("Unauthenticated", 401);
            }
        });

        // Handle authorization/permission errors (403)
        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return get_error_response("Access forbidden", 403);
            }
        });

        // Catch-all for unexpected errors (500)
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return get_error_response(
                    app()->hasDebugModeEnabled() ? $e->getMessage() : "Something went wrong",
                    500
                );
            }
        });
    })
    ->create();
