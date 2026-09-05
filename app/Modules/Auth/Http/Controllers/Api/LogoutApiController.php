<?php

declare(strict_types=1);

namespace App\Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;

class LogoutApiController extends Controller
{
    public function logout(): JsonResponse
    {
        try {
            $guard = auth('api');
            if (!$guard->check()) {
                return $this->error(message: 'User not authenticated', status: 401);
            }

            $guard->logout();

            return $this->success(message: 'Logged out successfully. Token revoked.', status: 200);
        } catch (Exception $e) {
            return $this->error(message: $e->getMessage(), status: 500);
        }
    }
}
