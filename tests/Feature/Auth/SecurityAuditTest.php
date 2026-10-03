<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Modules\Auth\Models\Verification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use RuntimeException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SecurityAuditTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    }

    public function test_sensitive_attributes_are_hidden_from_serialization(): void
    {
        $user = User::factory()->create();
        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);

        $verification = $user->sendVerification(Verification::PURPOSE_EMAIL_VERIFICATION);
        $verifArray = $verification->toArray();

        // Verification secret code must NEVER be serialized in model arrays/JSON
        $this->assertArrayNotHasKey('code', $verifArray);
    }

    public function test_api_does_not_leak_otp_in_production(): void
    {
        // Simulate production environment
        $this->app->detectEnvironment(fn () => 'production');

        $regPayload = [
            'name'                  => 'Audit Tester',
            'email'                 => 'audit.' . uniqid() . '@example.com',
            'password'              => 'Str0ngP@ss2026!',
            'password_confirmation' => 'Str0ngP@ss2026!',
            'agree'                 => true,
        ];

        $response = $this->postJson('/api/register', $regPayload);
        $response->assertStatus(201);
        $this->assertNull($response->json('data.otp'), 'OTP must never be leaked in production registration response');

        $forgotResponse = $this->postJson('/api/forget-password', [
            'email' => $regPayload['email'],
        ]);
        $forgotResponse->assertStatus(200);
        $this->assertNull($forgotResponse->json('data.otp'), 'OTP must never be leaked in production forgot-password response');
    }

    public function test_web_registration_flow_and_otp_verification(): void
    {
        $email = 'webverify.' . uniqid() . '@example.com';

        // 1. Web Register
        $response = $this->post('/register', [
            'name'                  => 'Web Verify User',
            'email'                 => $email,
            'password'              => 'Str0ngP@ss2026!',
            'password_confirmation' => 'Str0ngP@ss2026!',
        ]);

        $response->assertSessionHasNoErrors();
        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->isEmailVerified());

        // Fetch latest verification record created by sendVerification
        $verification = $user->verifications()
            ->where('purpose', Verification::PURPOSE_EMAIL_VERIFICATION)
            ->latest()
            ->first();

        $this->assertNotNull($verification);
        $this->assertEquals(6, strlen((string) $verification->code));

        // 2. Submit wrong OTP
        $wrongResponse = $this->post('/verify/otp', [
            'email' => $email,
            'otp'   => '000000',
        ]);
        $wrongResponse->assertSessionHas('error');
        $this->assertFalse($user->fresh()->isEmailVerified());

        // 3. Submit valid OTP
        $validResponse = $this->post('/verify/otp', [
            'email' => $email,
            'otp'   => (string) $verification->code,
        ]);
        $validResponse->assertRedirect(route('login'));
        $this->assertTrue($user->fresh()->isEmailVerified());
    }

    public function test_verification_service_enforces_brute_force_lockout(): void
    {
        $user = User::factory()->create([
            'email' => 'lockout.' . uniqid() . '@example.com',
        ]);

        $user->sendVerification(Verification::PURPOSE_EMAIL_VERIFICATION);

        // Consecutive wrong attempts trigger lockout exception on max attempt
        $lockedOut = false;
        try {
            for ($i = 1; $i <= 5; $i++) {
                $user->verifyOtp('999999', Verification::PURPOSE_EMAIL_VERIFICATION);
            }
        } catch (RuntimeException $e) {
            $lockedOut = str_contains($e->getMessage(), 'blocked temporarily');
        }

        $this->assertTrue($lockedOut, 'Expected lockout exception when exceeding max attempts');

        $verification = $user->verifications()
            ->where('purpose', Verification::PURPOSE_EMAIL_VERIFICATION)
            ->latest()
            ->first();

        $this->assertNotNull($verification->blocked_until);
        $this->assertTrue($verification->isBlocked());
    }
}
