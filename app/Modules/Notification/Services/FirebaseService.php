<?php

namespace App\Modules\Notification\Services;

use App\Models\User;
use App\Modules\Notification\Models\FirebaseToken;
use App\Modules\Notification\Models\Notification;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class FirebaseService
{
    protected $messaging = null;

    public function __construct()
    {
        // Messaging is initialized lazily via getMessaging() to prevent constructor failures
    }

    public function getMessaging()
    {
        if ($this->messaging !== null) {
            return $this->messaging;
        }

        if (! config('notifications.channels.firebase')) {
            return null;
        }

        $credentialsPath = config('firebase.credentials_path');
        if (empty($credentialsPath)) {
            return null;
        }

        $fullPath = storage_path($credentialsPath);
        if (! file_exists($fullPath) || is_dir($fullPath)) {
            Log::warning("Firebase credentials file not found or is a directory at: {$fullPath}");
            return null;
        }

        try {
            $this->messaging = (new Factory)->withServiceAccount($fullPath)->createMessaging();
        } catch (Exception $e) {
            Log::error('Firebase initialization error: ' . $e->getMessage());
            $this->messaging = null;
        }

        return $this->messaging;
    }

    public function send(Notification $notification): void
    {
        $messaging = $this->getMessaging();
        if (! $messaging) {
            return;
        }

        $tokens = FirebaseToken::where('user_id', $notification->user_id)->pluck('token');

        if ($tokens->isEmpty()) {
            return;
        }

        foreach ($tokens as $token) {
            try {
                $firebaseNotification = FirebaseNotification::create($notification->title, Str::limit($notification->body, 100));

                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification($firebaseNotification)
                    ->withData([
                        'id' => (string) $notification->id,
                        'type' => $notification->type,
                        'reference_type' => (string) $notification->reference_type,
                        'reference_id' => (string) $notification->reference_id,
                        'action' => (string) $notification->action,
                        'link' => (string) $notification->link,
                    ]);

                $messaging->send($message);
            } catch (Exception $e) {
                Log::error('Firebase push send error: ' . $e->getMessage());
            }
        }
    }

    public function deleteTokens(?User $user = null, string|array|null $tokens = null): void
    {
        $query = FirebaseToken::query();

        if ($user) {
            $query->where('user_id', $user->id);
        }

        if ($tokens !== null) {
            $query->whereIn('token', (array) $tokens);
        }

        $query->delete();
    }

    public function registerDevice(?User $user, array $data): FirebaseToken
    {
        $jwtToken = $data['jwt_token'] ?? request()->bearerToken();
        $jwtHash = ! empty($jwtToken) ? hash('sha256', $jwtToken) : null;

        return FirebaseToken::updateOrCreate(
            [
                'device_id' => $data['device_id'],
            ],
            [
                'user_id' => $user?->id ?? auth('api')->id(),
                'token' => $data['token'],
                'device_name' => $data['device_name'] ?? null,
                'platform' => $data['platform'] ?? null,
                'jwt_hash' => $jwtHash,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'last_activity_at' => now(),
                'status' => 'active',
            ]
        );
    }

    public function updateToken(string $deviceId, string $token): bool
    {
        return FirebaseToken::where('device_id', $deviceId)
            ->update([
                'token' => $token,
                'last_activity_at' => now(),
            ]) > 0;
    }

    public function getDevices(User $user, ?string $currentJwt = null): Collection
    {
        $currentJwtHash = ! empty($currentJwt) ? hash('sha256', $currentJwt) : null;

        return FirebaseToken::where('user_id', $user->id)
            ->select([
                'id',
                'device_id',
                'device_name',
                'platform',
                'ip_address',
                'user_agent',
                'jwt_hash',
                'last_activity_at',
                'status',
                'created_at',
            ])
            ->latest('last_activity_at')
            ->get()
            ->map(function ($device) use ($currentJwtHash) {
                $device->is_current_device = ($currentJwtHash !== null && $device->jwt_hash === $currentJwtHash);
                unset($device->jwt_hash);
                return $device;
            });
    }

    public function deleteById(User $user, int $id): bool
    {
        return FirebaseToken::where('user_id', $user->id)
            ->where('id', $id)
            ->delete() > 0;
    }

    public function getCurrentDevice(string $jwtToken): ?FirebaseToken
    {
        return FirebaseToken::where('jwt_hash', hash('sha256', $jwtToken))->first();
    }

    public function updateLastActivity(?string $jwtToken): bool
    {
        if (empty($jwtToken)) {
            return false;
        }

        return FirebaseToken::where('jwt_hash', hash('sha256', $jwtToken))
            ->update([
                'last_activity_at' => now(),
            ]) > 0;
    }

    public function logoutCurrentDevice(User $user, string $jwtToken): bool
    {
        return FirebaseToken::where('user_id', $user->id)
            ->where('jwt_hash', hash('sha256', $jwtToken))
            ->delete() > 0;
    }

    public function logoutOtherDevices(User $user, string $currentJwt): int
    {
        return FirebaseToken::where('user_id', $user->id)
            ->where('jwt_hash', '!=', hash('sha256', $currentJwt))
            ->delete();
    }

    public function logoutAllDevices(User $user): int
    {
        return FirebaseToken::where('user_id', $user->id)
            ->delete();
    }

    public function deleteByDeviceId(User $user, string $deviceId): bool
    {
        return FirebaseToken::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->delete() > 0;
    }

    public function activateDevice(string $deviceId): bool
    {
        return FirebaseToken::where('device_id', $deviceId)
            ->update([
                'status' => 'active',
            ]) > 0;
    }

    public function deactivateDevice(string $deviceId): bool
    {
        return FirebaseToken::where('device_id', $deviceId)
            ->update([
                'status' => 'inactive',
            ]) > 0;
    }

    public function findByDeviceId(string $deviceId): ?FirebaseToken
    {
        return FirebaseToken::where('device_id', $deviceId)->first();
    }

    public function findByJwt(string $jwt): ?FirebaseToken
    {
        return FirebaseToken::where('jwt_hash', hash('sha256', $jwt))->first();
    }

    public function deviceExists(string $deviceId): bool
    {
        return FirebaseToken::where('device_id', $deviceId)->exists();
    }

    public function cleanupInactiveDevices(int $days = 30): int
    {
        return FirebaseToken::where('last_activity_at', '<', now()->subDays($days))
            ->delete();
    }

    public function updateDeviceInformation(string $deviceId, array $data): bool
    {
        return FirebaseToken::where('device_id', $deviceId)
            ->update([
                'device_name' => $data['device_name'] ?? null,
                'platform' => $data['platform'] ?? null,
                'ip_address' => $data['ip_address'] ?? request()->ip(),
                'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            ]) > 0;
    }

    public function isCurrentDevice(string $deviceId, string $jwt): bool
    {
        return FirebaseToken::where('device_id', $deviceId)->where('jwt_hash', hash('sha256', $jwt))->exists();
    }
}
