<?php

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ApiAdminMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use App\Http\Middleware\WebAuthCheckMiddleware;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Middleware\ApiOtpVerifiedMiddleware;
use App\Http\Middleware\RolePermissionMiddleware;
use App\Http\Middleware\WebOtpVerifiedMiddleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'admin'])->prefix('admin')->name('admin.')->group(base_path('routes/admin.php'));
            Route::middleware(['web', 'admin'])->group(base_path('routes/backend.php'));
            require base_path('routes/cmd.php');
        }
    )
    ->withBroadcasting(
        __DIR__ . '/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['auth:api']],
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'api-admin' => ApiAdminMiddleware::class,
            'web-otp' => WebOtpVerifiedMiddleware::class,
            'api-otp' => ApiOtpVerifiedMiddleware::class,
            'check' => WebAuthCheckMiddleware::class,
            'permission' => RolePermissionMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'payment/stripe/webhook',
            'graphql',
        ]);
        $middleware->api([
            StartSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e instanceof ValidationException) {
                    return Helper::jsonErrorResponse($e->getMessage(), 422, $e->errors());
                }

                if ($e instanceof ModelNotFoundException) {
                    return Helper::jsonErrorResponse($e->getMessage(), 404);
                }

                if ($e instanceof AuthenticationException) {
                    return Helper::jsonErrorResponse($e->getMessage(), 401);
                }
                if ($e instanceof AuthorizationException) {
                    return Helper::jsonErrorResponse($e->getMessage(), 403);
                }
                // Dynamically determine the status code if available
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                return Helper::jsonErrorResponse($e->getMessage(), $statusCode);
            } else {
                return null;
            }
        });
    })->create();
