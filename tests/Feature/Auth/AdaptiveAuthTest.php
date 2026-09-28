<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Modules\AdaptiveAuth\Mail\AdaptiveOtpMail;
use App\Modules\AdaptiveAuth\Mail\NewDeviceAlertMail;
use App\Modules\AdaptiveAuth\Models\DeviceLoginChallenge;
use App\Modules\AdaptiveAuth\Models\UserDevice;
use App\Modules\AdaptiveAuth\Services\AdaptiveAuthService;
use App\Modules\AdaptiveAuth\Services\DeviceDetectorService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdaptiveAuthTest extends TestCase
{
    use DatabaseTransactions;

    protected AdaptiveAuthService $adaptiveService;
    protected DeviceDetectorService $detectorService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->detectorService = app(DeviceDetectorService::class);
        $this->adaptiveService = app(AdaptiveAuthService::class);
    }

    protected function createTestUser(): User
    {
        return User::create([
            'name'     => 'Adaptive Test User',
            'username' => 'adaptivetest_' . uniqid(),
            'email'    => 'adaptivetest_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'status'   => 'active',
        ]);
    }

    public function test_device_detector_inspects_user_agent_and_ip(): void
    {
        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
            'REMOTE_ADDR'     => '127.0.0.1',
        ]);

        $meta = $this->detectorService->inspect($request);

        $this->assertEquals('iOS', $meta['platform']);
        $this->assertEquals('Safari', $meta['browser']);
        $this->assertEquals('mobile', $meta['device_type']);
        $this->assertEquals('127.0.0.1', $meta['ip']);
        $this->assertNotEmpty($meta['device_uuid']);
        $this->assertNotEmpty($meta['fingerprint_hash']);
    }

    public function test_first_login_auto_trusts_device_when_configured(): void
    {
        $user = $this->createTestUser();

        $request = Request::create('/login', 'POST', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/125.0.0.0 Safari/537.36',
            'REMOTE_ADDR'     => '127.0.0.1',
        ]);

        $result = $this->adaptiveService->evaluateEnvironment($user, $request);

        $this->assertEquals('trusted', $result['status']);
        $this->assertNotNull($result['device']);
        $this->assertTrue($result['device']->is_trusted);

        // Verify record in database
        $this->assertDatabaseHas('user_devices', [
            'authenticatable_id'   => $user->id,
            'authenticatable_type' => $user->getMorphClass(),
            'is_trusted'           => true,
        ]);
    }

    public function test_same_trusted_device_with_cookie_is_granted_access(): void
    {
        $user = $this->createTestUser();

        $request1 = Request::create('/login', 'POST', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/125.0.0.0 Safari/537.36',
            'REMOTE_ADDR'     => '127.0.0.1',
        ]);

        $result1 = $this->adaptiveService->evaluateEnvironment($user, $request1);
        $deviceUuid = $result1['device']->device_uuid;

        // Second login with the device cookie set
        $cookieName = config('adaptive_auth.cookie_name', 'adaptive_device_token');
        $request2 = Request::create('/login', 'POST', [], [$cookieName => $deviceUuid], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/125.0.0.0 Safari/537.36',
            'REMOTE_ADDR'     => '127.0.0.1',
        ]);

        $result2 = $this->adaptiveService->evaluateEnvironment($user, $request2);

        $this->assertEquals('trusted', $result2['status']);
        $this->assertEquals($deviceUuid, $result2['device']->device_uuid);
    }

    public function test_new_unrecognized_device_triggers_challenge(): void
    {
        $user = $this->createTestUser();

        // 1. Establish first trusted device
        $request1 = Request::create('/login', 'POST', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/125.0.0.0 Safari/537.36',
            'REMOTE_ADDR'     => '127.0.0.1',
        ]);
        $this->adaptiveService->evaluateEnvironment($user, $request1);

        // 2. Second login attempt from a DIFFERENT device/browser (e.g. Firefox on Mac) without cookie
        $request2 = Request::create('/login', 'POST', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:126.0) Gecko/20100101 Firefox/126.0',
            'REMOTE_ADDR'     => '127.0.0.1',
        ]);

        $result2 = $this->adaptiveService->evaluateEnvironment($user, $request2);

        $this->assertEquals('challenge_required', $result2['status']);
        $this->assertNotEmpty($result2['metadata']);
    }

    public function test_create_challenge_dispatches_otp_mail(): void
    {
        Mail::fake();

        $user = $this->createTestUser();
        $meta = [
            'device_uuid' => 'test-device-uuid-12345',
            'device_name' => 'Safari on iPhone',
            'ip'          => '127.0.0.1',
            'city'        => 'Dhaka',
            'country'     => 'Bangladesh',
        ];

        $challenge = $this->adaptiveService->createChallenge($user, $meta);

        $this->assertNotNull($challenge);
        $this->assertEquals(36, strlen($challenge->challenge_token));
        $this->assertFalse($challenge->isExpired());
        $this->assertFalse($challenge->isVerified());

        Mail::assertSent(AdaptiveOtpMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && strlen($mail->otpCode) === 6;
        });
    }

    public function test_verify_challenge_with_invalid_otp_fails(): void
    {
        Mail::fake();

        $user = $this->createTestUser();
        $meta = [
            'device_uuid' => 'test-device-uuid-99999',
            'device_name' => 'Chrome on Windows',
            'ip'          => '127.0.0.1',
        ];

        $challenge = $this->adaptiveService->createChallenge($user, $meta);

        $result = $this->adaptiveService->verifyChallenge($challenge->challenge_token, '000000');

        $this->assertFalse($result['success']);
        $this->assertEquals('INVALID_OTP', $result['code']);
        $this->assertEquals(2, $result['attempts_remaining']);
    }

    public function test_verify_challenge_with_correct_otp_trusts_device_and_sends_security_alert(): void
    {
        Mail::fake();

        $user = $this->createTestUser();
        $meta = [
            'device_uuid'      => 'trusted-test-uuid-55555',
            'device_name'      => 'Chrome on Windows 11',
            'platform'         => 'Windows 11',
            'browser'          => 'Chrome',
            'device_type'      => 'desktop',
            'fingerprint_hash' => hash('sha256', 'entropy'),
            'ip'               => '127.0.0.1',
            'city'             => 'Dhaka',
            'country'          => 'Bangladesh',
        ];

        $challenge = $this->adaptiveService->createChallenge($user, $meta);

        // Extract the plain OTP that was mailed
        $sentMail = null;
        Mail::assertSent(AdaptiveOtpMail::class, function ($mail) use (&$sentMail) {
            $sentMail = $mail;
            return true;
        });

        $this->assertNotNull($sentMail);
        $correctOtp = $sentMail->otpCode;

        // Verify using the correct OTP
        $result = $this->adaptiveService->verifyChallenge($challenge->challenge_token, $correctOtp);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['device']);
        $this->assertTrue($result['device']->is_trusted);

        // Security alert mail sent
        Mail::assertSent(NewDeviceAlertMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_user_can_revoke_device(): void
    {
        $user = $this->createTestUser();

        $request = Request::create('/login', 'POST', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/125.0.0.0 Safari/537.36',
            'REMOTE_ADDR'     => '127.0.0.1',
        ]);

        $result = $this->adaptiveService->evaluateEnvironment($user, $request);
        $device = $result['device'];

        $this->assertTrue($device->isCurrentlyTrusted());

        // Revoke device
        $revoked = $user->revokeDevice($device->id);
        $this->assertTrue($revoked);

        $device->refresh();
        $this->assertFalse($device->isCurrentlyTrusted());
        $this->assertNotNull($device->revoked_at);
    }

    public function test_resend_otp_enforces_cooldown(): void
    {
        Mail::fake();

        $user = $this->createTestUser();
        $meta = [
            'device_uuid' => 'cooldown-test-device',
            'ip'          => '127.0.0.1',
        ];

        $challenge = $this->adaptiveService->createChallenge($user, $meta);

        // Attempt immediate resend without waiting
        $result = $this->adaptiveService->resendOtp($challenge->challenge_token);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Please wait', $result['message']);
    }

    public function test_api_verify_endpoint_returns_json(): void
    {
        Mail::fake();

        $user = $this->createTestUser();
        $meta = [
            'device_uuid' => 'api-test-device',
            'ip'          => '127.0.0.1',
        ];

        $challenge = $this->adaptiveService->createChallenge($user, $meta);

        // Test invalid OTP via API
        $response = $this->postJson('/api/adaptive-auth/verify', [
            'challenge_token' => $challenge->challenge_token,
            'otp'             => '999999',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => false,
        ]);
    }

    public function test_web_authenticated_user_can_view_devices_page(): void
    {
        $user = $this->createTestUser();

        // Establish a trusted device
        $this->adaptiveService->registerTrustedDevice($user, [
            'device_uuid' => 'test-device-uuid-web',
            'device_name' => 'Chrome on Windows 11',
            'ip'          => '127.0.0.1',
        ]);

        $response = $this->actingAs($user)->get(route('adaptive.devices.index'));

        $response->assertStatus(200);
        $response->assertSee('Recognized Devices');
        $response->assertSee('Chrome on Windows 11');
    }

    public function test_web_authenticated_user_can_revoke_device(): void
    {
        $user = $this->createTestUser();

        $device = $this->adaptiveService->registerTrustedDevice($user, [
            'device_uuid' => 'device-to-revoke',
            'device_name' => 'Firefox on Linux',
            'ip'          => '127.0.0.1',
        ]);

        $this->assertTrue($device->isCurrentlyTrusted());

        $response = $this->actingAs($user)->post(route('adaptive.devices.revoke', $device->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $device->refresh();
        $this->assertFalse($device->isCurrentlyTrusted());
    }

    public function test_web_authenticated_user_can_revoke_other_devices(): void
    {
        $user = $this->createTestUser();

        $device1 = $this->adaptiveService->registerTrustedDevice($user, [
            'device_uuid' => 'current-device-uuid',
            'device_name' => 'Current Chrome Device',
            'ip'          => '127.0.0.1',
        ]);

        $device2 = $this->adaptiveService->registerTrustedDevice($user, [
            'device_uuid' => 'other-device-uuid',
            'device_name' => 'Old Laptop',
            'ip'          => '127.0.0.1',
        ]);

        $cookieName = config('adaptive_auth.cookie_name', 'adaptive_device_token');

        $response = $this->actingAs($user)
            ->withCookie($cookieName, 'current-device-uuid')
            ->post(route('adaptive.devices.revoke_others'));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $device1->refresh();
        $device2->refresh();

        $this->assertTrue($device1->isCurrentlyTrusted());
        $this->assertFalse($device2->isCurrentlyTrusted());
    }

    public function test_web_verify_challenge_submission_logs_in_user_and_redirects(): void
    {
        Mail::fake();

        $user = $this->createTestUser();
        $meta = [
            'device_uuid' => 'web-verify-device-uuid',
            'device_name' => 'Firefox on Windows 11',
            'ip'          => '127.0.0.1',
        ];

        $challenge = $this->adaptiveService->createChallenge($user, $meta);

        $sentMail = null;
        Mail::assertSent(AdaptiveOtpMail::class, function ($mail) use (&$sentMail) {
            $sentMail = $mail;
            return true;
        });

        $this->assertNotNull($sentMail);
        $otp = $sentMail->otpCode;

        // Submit to Web verify route
        $response = $this->post(route('adaptive.challenge.verify'), [
            'challenge_token' => $challenge->challenge_token,
            'otp'             => $otp,
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
        $cookieName = config('adaptive_auth.cookie_name', 'adaptive_device_token');
        $response->assertCookie($cookieName);
    }

    public function test_web_authenticated_user_can_clear_audit_logs(): void
    {
        $user = $this->createTestUser();

        // Create some sample logs for this user
        $user->loginLogs()->create([
            'ip_address'     => '127.0.0.1',
            'status'         => 'trusted_login',
            'created_at'     => now(),
        ]);
        $user->loginLogs()->create([
            'ip_address'     => '127.0.0.1',
            'status'         => 'challenge_passed',
            'created_at'     => now(),
        ]);

        $this->assertEquals(2, $user->loginLogs()->count());

        $response = $this->actingAs($user)->post(route('adaptive.devices.clear_logs'));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals(0, $user->loginLogs()->count());
    }

    public function test_api_authenticated_user_can_clear_audit_logs(): void
    {
        $user = $this->createTestUser();

        $user->loginLogs()->create([
            'ip_address'     => '127.0.0.1',
            'status'         => 'trusted_login',
            'created_at'     => now(),
        ]);

        $this->assertEquals(1, $user->loginLogs()->count());

        $response = $this->actingAs($user, 'api')->deleteJson('/api/adaptive-auth/audit-logs');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
        ]);
        $this->assertEquals(0, $user->loginLogs()->count());
    }

    public function test_revoked_device_is_immediately_logged_out_on_next_request(): void
    {
        $user = $this->createTestUser();

        // 1. Create trusted device record for Browser B
        $device = $this->adaptiveService->registerTrustedDevice($user, [
            'device_uuid' => 'browser-b-uuid',
            'device_name' => 'Browser B on Windows',
            'ip'          => '127.0.0.1',
        ]);

        $this->assertTrue($device->isCurrentlyTrusted());

        // 2. User revokes Device B (from Browser A)
        $user->revokeDevice($device->id);
        $device->refresh();
        $this->assertFalse($device->isCurrentlyTrusted());

        // 3. Browser B makes a request (e.g. reload or visit any page)
        $cookieName = config('adaptive_auth.cookie_name', 'adaptive_device_token');

        $response = $this->actingAs($user)
            ->withSession(['adaptive_device_uuid' => 'browser-b-uuid'])
            ->get(route('adaptive.devices.index'));

        // 4. Assert Browser B is immediately kicked out, logged out, and redirected to login with error
        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
    }

    public function test_revoked_device_receives_401_json_when_requesting_api(): void
    {
        $user = $this->createTestUser();

        $device = $this->adaptiveService->registerTrustedDevice($user, [
            'device_uuid' => 'browser-c-uuid',
            'device_name' => 'Browser C',
            'ip'          => '127.0.0.1',
        ]);

        $user->revokeDevice($device->id);

        $response = $this->actingAs($user)
            ->withSession(['adaptive_device_uuid' => 'browser-c-uuid'])
            ->getJson(route('adaptive.devices.index'));

        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'REVOKED',
        ]);
        $this->assertGuest();
    }
}




