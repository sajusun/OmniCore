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
            if (app()->isLocal() && file_exists(base_path('routes/cmd.php'))) {
                require base_path('routes/cmd.php');
            }
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
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e instanceof ValidationException) {
                    return \App\Helpers\ApiResponse::validationError($e->errors(), $e->getMessage());
                }

                if ($e instanceof ModelNotFoundException) {
                    return \App\Helpers\ApiResponse::notFound($e->getMessage() ?: 'Resource not found.');
                }

                if ($e instanceof AuthenticationException) {
                    return \App\Helpers\ApiResponse::unauthorized($e->getMessage() ?: 'Unauthenticated.');
                }

                if ($e instanceof AuthorizationException) {
                    return \App\Helpers\ApiResponse::forbidden($e->getMessage() ?: 'This action is unauthorized.');
                }

                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                return \App\Helpers\ApiResponse::error($e->getMessage() ?: 'Server Error', $statusCode);
            }

            return null;
        });
    })->create();
