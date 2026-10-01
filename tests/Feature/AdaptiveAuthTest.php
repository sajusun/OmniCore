<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\AdaptiveAuth\Models\DeviceLoginChallenge;
use App\Modules\AdaptiveAuth\Models\UserDevice;
use App\Modules\AdaptiveAuth\Models\UserTotpCredential;
use App\Modules\AdaptiveAuth\Services\AdaptiveAuthService;
use App\Modules\AdaptiveAuth\Services\TotpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdaptiveAuthTest extends TestCase
{
    /**
     * Test TOTP Service generation, validation, and recovery codes.
     */
    public function test_totp_service_lifecycle_and_recovery_codes(): void
    {
        $totpService = app(TotpService::class);
        $secretKey = $totpService->generateSecretKey();

        $this->assertNotEmpty($secretKey);
        $this->assertEquals(32, strlen($secretKey));

        // Test recovery codes generation
        $recovery = $totpService->generateRecoveryCodes(8);
        $this->assertCount(8, $recovery['plain']);
        $this->assertCount(8, $recovery['hashed']);

        // Create a test user instance
        $user = new User();
        $user->id = 9999;
        $user->name = 'Test User';
        $user->email = 'test@mybergo.com';

        // Test Recovery Code Consumption via Model
        $totpCredential = new UserTotpCredential([
            'authenticatable_type' => $user->getMorphClass(),
            'authenticatable_id'   => $user->getKey(),
            'secret_key'           => $secretKey,
            'recovery_codes'       => $recovery['hashed'],
            'is_enabled'           => true,
            'confirmed_at'         => now(),
        ]);

        $firstPlainCode = $recovery['plain'][0];
        $this->assertTrue($totpCredential->useRecoveryCode($firstPlainCode));

        // Second attempt with the SAME code must fail (single-use protection)
        $this->assertFalse($totpCredential->useRecoveryCode($firstPlainCode));

        // Remaining codes count must be 7
        $this->assertCount(7, $totpCredential->recovery_codes);

        $totpCredential->save();
        $newCodes = $totpService->regenerateRecoveryCodes($user, 8);
        $this->assertCount(8, $newCodes);
        $this->assertEquals(8, $totpService->getRemainingRecoveryCodesCount($user));
        $totpCredential->delete();
    }

    /**
     * Test OTP creation, expiry, and verification in AdaptiveAuthService.
     */
    public function test_adaptive_otp_challenge_and_verification(): void
    {
        $adaptiveService = app(AdaptiveAuthService::class);

        // Find or create test user
        $user = User::first() ?? User::forceCreate([
            'name'     => 'Security Admin',
            'email'    => 'admin@test-security.com',
            'password' => Hash::make('Secret123!'),
        ]);

        $meta = [
            'device_uuid'      => (string) \Illuminate\Support\Str::uuid(),
            'device_name'      => 'Chrome on Windows 11',
            'platform'         => 'Windows 11',
            'browser'          => 'Chrome',
            'device_type'      => 'desktop',
            'fingerprint_hash' => hash('sha256', 'test-fingerprint'),
            'ip'               => '127.0.0.1',
            'city'             => 'Dhaka',
            'country'          => 'Bangladesh',
            'country_code'     => 'BD',
            'user_agent'       => 'Mozilla/5.0 Test',
        ];

        // 1. Create Challenge
        $challenge = $adaptiveService->createChallenge($user, $meta);
        $this->assertNotNull($challenge->challenge_token);
        $this->assertNotNull($challenge->otp_code_hash);

        // 2. Test Invalid OTP fails
        $failResult = $adaptiveService->verifyChallenge($challenge->challenge_token, '000000');
        $this->assertFalse($failResult['success']);
        $this->assertEquals('invalid_otp', $failResult['status']);

        // 3. Test Correct OTP succeeds (we hash a known OTP for test)
        $knownOtp = '789456';
        $challenge->update(['otp_code_hash' => Hash::make($knownOtp)]);

        $successResult = $adaptiveService->verifyChallenge($challenge->challenge_token, $knownOtp);
        $this->assertTrue($successResult['success']);
        $this->assertEquals('verified', $successResult['status']);
        $this->assertNotNull($successResult['cookie']);
        $this->assertTrue($successResult['device']->is_trusted);

        // Clean up test records
        $successResult['device']->delete();
        if ($user->email === 'admin@test-security.com') {
            $user->delete();
        }
    }

    /**
     * Test TOTP enforcement on untrusted vs trusted devices and preference toggle.
     */
    public function test_totp_enforcement_and_login_flow(): void
    {
        $adaptiveService = app(AdaptiveAuthService::class);
        $totpService = app(TotpService::class);

        $email = 'totp-admin-' . uniqid() . '@test-security.com';
        $user = User::forceCreate([
            'name'     => 'TOTP Admin',
            'email'    => $email,
            'password' => Hash::make('Secret123!'),
            'status'   => 'active',
        ]);

        $secretKey = $totpService->generateSecretKey();
        $credential = UserTotpCredential::create([
            'authenticatable_type'    => $user->getMorphClass(),
            'authenticatable_id'      => $user->getKey(),
            'secret_key'              => $secretKey,
            'recovery_codes'          => [],
            'is_enabled'              => true,
            'always_require_on_login' => false,
            'confirmed_at'            => now(),
        ]);

        $request = Request::create('/test-login', 'POST', [], [], [], [
            'REMOTE_ADDR' => '192.168.1.50',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 Test Chrome Untrusted',
        ]);

        // 1. Untrusted device with TOTP enabled -> must return totp_required
        $assessment = $adaptiveService->evaluateEnvironment($user, $request, isLoginAttempt: true);
        $this->assertEquals('totp_required', $assessment['status']);
        $this->assertTrue($assessment['totp_required']);

        // 2. Register device as trusted
        $device = $adaptiveService->registerTrustedDevice($user, $assessment['metadata']);
        $this->assertTrue($device->is_trusted);

        // Attach trusted cookie to subsequent request
        $cookieName = config('adaptive_auth.cookie_name', 'adaptive_device_token');
        $request->cookies->set($cookieName, $device->device_uuid);

        // 3. Trusted device with always_require = false -> returns trusted
        $assessment2 = $adaptiveService->evaluateEnvironment($user, $request, isLoginAttempt: true);
        $this->assertEquals('trusted', $assessment2['status']);

        // 4. Toggle always_require_on_login = true -> returns totp_required even on trusted device!
        $totpService->updateLoginPreference($user, true);
        $this->assertTrue($totpService->alwaysRequiresTotpOnLogin($user));

        $assessment3 = $adaptiveService->evaluateEnvironment($user, $request, isLoginAttempt: true);
        $this->assertEquals('totp_required', $assessment3['status']);

        // 5. Clean up
        $device->delete();
        $credential->delete();
        $user->delete();
    }

    /**
     * Test disabling TOTP requires valid current account password.
     */
    public function test_disable_totp_requires_correct_password(): void
    {
        $totpService = app(TotpService::class);
        $user = User::forceCreate([
            'name'     => 'Disable 2FA Tester',
            'email'    => 'disable-totp-' . uniqid() . '@test-security.com',
            'password' => Hash::make('MySecurePassword123!'),
            'status'   => 'active',
        ]);

        $credential = UserTotpCredential::create([
            'authenticatable_type'    => $user->getMorphClass(),
            'authenticatable_id'      => $user->getKey(),
            'secret_key'              => $totpService->generateSecretKey(),
            'recovery_codes'          => [],
            'is_enabled'              => true,
            'always_require_on_login' => false,
            'confirmed_at'            => now(),
        ]);

        $this->assertTrue($totpService->hasTotpEnabled($user));

        // 1. Attempt disable without password -> validation error
        $response1 = $this->actingAs($user)->post(route('adaptive.totp.disable'), []);
        $response1->assertSessionHasErrors(['password']);
        $this->assertTrue($totpService->hasTotpEnabled($user));

        // 2. Attempt disable with wrong password -> error and remains enabled
        $response2 = $this->actingAs($user)->post(route('adaptive.totp.disable'), [
            'password' => 'WrongPassword123!',
        ]);
        $response2->assertSessionHasErrors(['password']);
        $this->assertTrue($totpService->hasTotpEnabled($user));

        // 3. Attempt disable with correct password -> successfully disabled
        $response3 = $this->actingAs($user)->post(route('adaptive.totp.disable'), [
            'password' => 'MySecurePassword123!',
        ]);
        $response3->assertRedirect(route('adaptive.devices.index'));
        $response3->assertSessionHas('success');
        $this->assertFalse($totpService->hasTotpEnabled($user));

        // Cleanup
        $credential->delete();
        $user->delete();
    }
}

