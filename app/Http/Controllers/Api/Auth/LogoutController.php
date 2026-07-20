<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Exception;

class LogoutController extends Controller
{
    public function __construct(private readonly FirebaseService $firebaseService)
    {
        parent::__construct();
    }
    public function logout()
    {

        try {
            $guard = auth('api');
            if (!$guard->check()) {
                return $this->error(message: 'User not authenticated', status: 401);
            }

            $this->firebaseService->deleteTokens($guard->user());
            $guard->logout();

            return $this->success(message: 'Logged out successfully. Token revoked.', status: 200);
        } catch (Exception $e) {
            return $this->error(message: $e->getMessage(), status: 500);
        }
    }
}
