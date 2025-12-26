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
                });
        }
    )
    ->withBroadcasting(
        __DIR__ . '/../routes/channels.php',
        ['middleware' => ['web', 'auth']],
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->trustProxies(at: '*');

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
            return get_error_response("Invalid request method for this endpoint", 405);
        });

        // Handle routes not found
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return get_error_response("Route not found", 404);
            }
        });

        // Handle validation errors
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return get_error_response("Validation failed", 422, $e->errors());
            }
        });

        // Catch-all for unexpected errors
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
