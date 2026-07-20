<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class AuthHelper
{
    public static function user()
    {
        return Auth::user();
    }

    public static function id(): ?int
    {
        return Auth::id();
    }

    public static function check(): bool
    {
        return Auth::check();
    }

    public static function guard(string $guard = 'web')
    {
        return Auth::guard($guard);
    }

    public static function userRole(): ?string
    {
        return Auth::user()?->role ?? null;
    }
}