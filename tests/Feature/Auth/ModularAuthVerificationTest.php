<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Modules\Auth\Models\Verification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ModularAuthVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    }

    public function test_has_verification_trait_sends_otp_polymorphically(): void
    {
        $user = User::factory()->create([
            'email' => 'john.doe@example.com',
        ]);

        $verification = $user->sendVerification(
            purpose: Verification::PURPOSE_EMAIL_VERIFICATION,
            type: 'otp'
        );

        $this->assertNotNull($verification);
        $this->assertEquals(Verification::STATUS_PENDING, $verification->status);
        $this->assertEquals(Verification::TYPE_OTP, $verification->verification_type);
        $this->assertEquals($user->getMorphClass(), $verification->verifiable_type);
        $this->assertEquals($user->id, $verification->verifiable_id);

        $this->assertFalse($user->isEmailVerified());

        // Verify OTP
        $verified = $user->verifyOtp($verification->code, Verification::PURPOSE_EMAIL_VERIFICATION);
        $this->assertTrue($verified);
        $this->assertTrue($user->isEmailVerified());
    }

    public function test_api_registration_sends_otp_via_trait(): void
    {
        $payload = [
            'name'                  => 'Jane Developer',
            'email'                 => 'jane.developer@example.com',
            'password'              => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'agree'                 => true,
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('status', true)
            ->assertJsonStructure([
                'status',
                'message',
                'code',
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'otp',
                ],
            ]);

        $user = User::where('email', 'jane.developer@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->isEmailVerified());

        // Verify Email API
        $otp = $response->json('data.otp');
        $verifyResponse = $this->postJson('/api/register/verify', [
            'email' => 'jane.developer@example.com',
            'otp'   => (string) $otp,
        ]);

        $verifyResponse->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonStructure([
                'data' => ['token', 'token_type', 'id', 'email'],
            ]);

        $this->assertTrue($user->fresh()->isEmailVerified());
    }

    public function test_login_blocked_if_email_unverified(): void
    {
        $user = User::factory()->create([
            'email'    => 'unverified@example.com',
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole('user');

        $response = $this->postJson('/api/login', [
            'email'    => 'unverified@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('status', false);

        // Verify email
        $verification = $user->sendVerification(Verification::PURPOSE_EMAIL_VERIFICATION);
        $user->verifyOtp($verification->code, Verification::PURPOSE_EMAIL_VERIFICATION);

        // Try login again
        $successResponse = $this->postJson('/api/login', [
            'email'    => 'unverified@example.com',
            'password' => 'password123',
        ]);

        $successResponse->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonStructure([
                'data' => ['token', 'user'],
            ]);
    }

    public function test_forgot_password_and_reset_flow(): void
    {
        $user = User::factory()->create([
            'email'    => 'resetme@example.com',
            'password' => Hash::make('oldpassword'),
        ]);
        $user->assignRole('user');

        // 1. Forgot password request
        $forgotResponse = $this->postJson('/api/forget-password', [
            'email' => 'resetme@example.com',
        ]);

        $forgotResponse->assertStatus(200)
            ->assertJsonPath('status', true);

        $otp = $forgotResponse->json('data.otp');

        // 2. Verify OTP and get secret key
        $tokenResponse = $this->postJson('/api/forget-password/token', [
            'email' => 'resetme@example.com',
            'otp'   => (string) $otp,
        ]);

        $tokenResponse->assertStatus(200)
            ->assertJsonPath('status', true);

        $secretKey = $tokenResponse->json('data.secret_key');
        $this->assertNotEmpty($secretKey);

        // 3. Reset password
        $resetResponse = $this->postJson('/api/reset-password', [
            'email'                 => 'resetme@example.com',
            'secret_key'            => $secretKey,
            'password'              => 'NewSecurePass123!',
            'password_confirmation' => 'NewSecurePass123!',
        ]);

        $resetResponse->assertStatus(200)
            ->assertJsonPath('status', true);

        $this->assertTrue(Hash::check('NewSecurePass123!', $user->fresh()->password));
    }
}
