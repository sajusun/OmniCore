<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use RuntimeException;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * AuthService
 *
 * Handles all authentication logic: registration, login, logout,
 * email verification, forgot password, OTP/token verification,
 * password reset, and role assignment.
 *
 * ● Driver-agnostic: returns plain data arrays — controllers (API or
 *   Web) decide how to format the response.
 * ● Delegates verification (OTP / link) entirely to VerificationService.
 * ● Delegates password-reset token management to UserService.
 */
class AuthService
{
    /** Fields selected when returning user data to callers. */
    private const USER_FIELDS = ['id', 'name', 'email', 'avatar', 'last_activity_at'];

    public function __construct(
        private readonly VerificationService $verificationService,
        private readonly UserService $userService,
    ) {}

    // =========================================================================
    // Registration
    // =========================================================================

    /**
     * Register a new user, optionally assign a role, and dispatch email OTP.
     *
     * @param  array $data {
     *   name: string,
     *   email: string,
     *   password: string,
     *   role?: string|null,         // optional — caller passes when needed
     *   verification_type?: string, // 'otp' (default) | 'token'
     * }
     * @return array { user: User, verification: Verification }
     *
     * @throws RuntimeException|\Exception
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            // 1. Build a unique slug from name
            $slug = $this->makeSlug($data['name']);

            // 2. Create the user
            /** @var User $user */
            $user = User::create([
                'name'             => $data['name'],
                'slug'             => $slug,
                'email'            => strtolower($data['email']),
                'password'         => Hash::make($data['password']),
                'status'           => 'active',
                'last_activity_at' => now(),
            ]);

            // 3. Assign role when provided (uses Spatie/Permission)
            if (!empty($data['role'])) {
                $this->assignRole($user, $data['role']);
            }

            // 4. Send email verification (OTP by default, or 'token')
            $verificationType = $data['verification_type'] ?? 'otp';
            $verification = $this->verificationService->send(
                user: $user,
                purpose: Verification::PURPOSE_EMAIL_VERIFICATION,
                type: $verificationType,
            );

            return compact('user', 'verification');
        });
    }

    // =========================================================================
    // Login
    // =========================================================================

    /**
     * Authenticate a user and return a JWT token.
     *
     * @param  array $credentials { email: string, password: string, guard?: string }
     * @return array { user: User, token: string, token_type: string, expires_in: int }
     *
     * @throws RuntimeException
     */
    public function login(array $credentials): array
    {
        $user = $this->userService->findByEmail($credentials['email']);

        if (!$user) {
            throw new RuntimeException('No account found with that email address.');
        }

        if ($user->status !== 'active') {
            throw new RuntimeException('Your account is not active. Please contact support.');
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            throw new RuntimeException('The password you entered is incorrect.');
        }

        if (!$user->isEmailVerified()) {
            throw new RuntimeException(
                'Email not verified. Please verify your email before logging in.',
                403
            );
        }

        // Update last activity
        $user->update(['last_activity_at' => now()]);

        $guard = $credentials['guard'] ?? 'api';
        $token = auth($guard)->login($user);

        if (!$token) {
            throw new RuntimeException('Unable to generate authentication token. Please try again.');
        }

        return [
            'user'       => $user->only(self::USER_FIELDS),
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => auth($guard)->factory()->getTTL() * 60,
        ];
    }

    /**
     * Refresh an existing JWT token.
     *
     * @param  string $guard  'api' | 'web'
     * @return array { token: string, token_type: string, expires_in: int, user: User }
     *
     * @throws RuntimeException
     */
    public function refreshToken(string $guard = 'api'): array
    {
        $token = auth($guard)->refresh();

        if (empty($token)) {
            throw new RuntimeException('Failed to refresh the token.');
        }

        return [
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => auth($guard)->factory()->getTTL() * 60,
            'user'       => auth($guard)->user(),
        ];
    }

    // =========================================================================
    // Logout
    // =========================================================================

    /**
     * Invalidate the current token and log the user out.
     *
     * @param  string $guard 'api' | 'web'
     */
    public function logout(string $guard = 'api'): void
    {
        auth($guard)->logout();
    }

    // =========================================================================
    // Email Verification
    // =========================================================================

    /**
     * Verify an email OTP submitted by the user.
     *
     * @param  string $email
     * @param  string $otp
     * @return User  The user after email is verified.
     *
     * @throws RuntimeException
     */
    public function verifyEmail(string $email, string $otp): User
    {
        $user = $this->resolveUser($email);

        if ($user->isEmailVerified()) {
            throw new RuntimeException('Email is already verified.', 409);
        }

        $verified = $this->verificationService->verifyOtp(
            user: $user,
            purpose: Verification::PURPOSE_EMAIL_VERIFICATION,
            code: $otp,
        );

        if (!$verified) {
            throw new RuntimeException('Invalid OTP code. Please try again.', 422);
        }

        return $user->refresh();
    }

    /**
     * Resend email verification OTP (or link token) to the user.
     *
     * @param  string      $email
     * @param  string|null $type  'otp' | 'token'
     * @return Verification
     *
     * @throws RuntimeException
     */
    public function resendEmailVerification(string $email, ?string $type = 'otp'): Verification
    {
        $user = $this->resolveUser($email);

        if ($user->isEmailVerified()) {
            throw new RuntimeException('Email is already verified.', 409);
        }

        return $this->verificationService->resend(
            user: $user,
            purpose: Verification::PURPOSE_EMAIL_VERIFICATION,
            type: $type,
        );
    }

    // =========================================================================
    // Forgot Password / Password Reset
    // =========================================================================

    /**
     * Send a password-reset OTP (or link) to the given email.
     *
     * @param  string      $email
     * @param  string|null $type  'otp' (default) | 'token'
     * @return Verification
     *
     * @throws RuntimeException
     */
    public function forgotPassword(string $email, ?string $type = 'otp'): Verification
    {
        $user = $this->resolveUser($email, allowUnverified: true);

        return $this->verificationService->send(
            user: $user,
            purpose: Verification::PURPOSE_PASSWORD_RESET,
            type: $type,
        );
    }

    /**
     * Verify the password-reset OTP and return a short-lived secret key
     * that the "reset password" step requires.
     *
     * @param  string $email
     * @param  string $otp
     * @return string  Secret reset key.
     *
     * @throws RuntimeException
     */
    public function verifyPasswordResetOtp(string $email, string $otp): string
    {
        $user = $this->resolveUser($email, allowUnverified: true);

        $verified = $this->verificationService->verifyOtp(
            user: $user,
            purpose: Verification::PURPOSE_PASSWORD_RESET,
            code: $otp,
        );

        if (!$verified) {
            throw new RuntimeException('Invalid OTP code.', 422);
        }

        // Issue a short-lived secret key stored in password_reset_tokens
        return $this->userService->createPasswordResetToken($user);
    }

    /**
     * Reset the user's password using the secret key from verifyPasswordResetOtp().
     *
     * @param  string $email
     * @param  string $secretKey
     * @param  string $newPassword
     *
     * @throws RuntimeException
     */
    public function resetPassword(string $email, string $secretKey, string $newPassword): void
    {
        $user = $this->resolveUser($email, allowUnverified: true);

        $tokenData = $this->userService->getResetTokenUser($secretKey, $email);
        if (!$tokenData) {
            throw new RuntimeException('Invalid or expired secret key.', 419);
        }

        if (!$this->userService->isTokenValid($tokenData)) {
            throw new RuntimeException('Secret key has expired. Please request a new OTP.', 419);
        }

        $this->userService->updatePassword($user, $newPassword);
        $this->userService->revokePasswordToken($user);
    }

    // =========================================================================
    // Role Management
    // =========================================================================

    /**
     * Assign a role to a user (uses Spatie Permission).
     * Silently does nothing if the role name is empty/null.
     *
     * @param  User   $user
     * @param  string $role
     *
     * @throws RuntimeException  If the role does not exist.
     */
    public function assignRole(User $user, string $role): void
    {
        $role = trim($role);
        if ($role === '') {
            return;
        }

        try {
            $user->assignRole($role);
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            throw new RuntimeException("Role '{$role}' does not exist.", 422);
        }
    }

    /**
     * Sync all roles for a user (replaces existing roles).
     *
     * @param  User          $user
     * @param  string|array  $roles  Single role name or array of role names.
     */
    public function syncRoles(User $user, string|array $roles): void
    {
        $user->syncRoles($roles);
    }

    /**
     * Remove a role from a user.
     *
     * @param  User   $user
     * @param  string $role
     */
    public function removeRole(User $user, string $role): void
    {
        $user->removeRole($role);
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    /**
     * Look up a user by email, throwing RuntimeException if not found.
     * By default it also checks that the user's email is verified — pass
     * $allowUnverified = true for password-reset flows.
     *
     * @param  bool $allowUnverified  When true, skips the email-verified check.
     *
     * @throws RuntimeException
     */
    private function resolveUser(string $email, bool $allowUnverified = false): User
    {
        $user = $this->userService->findByEmail($email);

        if (!$user) {
            throw new RuntimeException('No account found with that email address.', 404);
        }

        return $user;
    }

    /**
     * Generate a unique URL-safe slug from a display name.
     * Appends a short random suffix on collision.
     */
    private function makeSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 2;

        while (User::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
