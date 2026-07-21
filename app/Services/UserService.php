<?php

namespace App\Services;

use stdClass;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::with('profile')->latest()->paginate($perPage);
    }

    public function all(): Collection
    {
        return User::with('profile')->get();
    }

    public function find(int $id): ?User
    {
        return User::with('profile')->find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->refresh();
    }

    public function delete(User $user): bool
    {
        return (bool) $user->delete();
    }

    public function changeStatus(User $user, bool $status): User
    {
        $user->update([
            'status' => $status ? 'active' : 'inactive',
        ]);

        return $user->refresh();
    }

    public function updatePassword(User $user, string $password): User
    {
        $user->update([
            'password' => bcrypt($password),
        ]);

        return $user->refresh();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }

    public function createPasswordResetToken(User $user): string
    {

        $token = Str::random(config('verification.token_length'));

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token'      => hash('sha256', $token),
                'created_at' => now(),
            ]
        );
        return $token;
    }

    public function getResetTokenUser(string $token, ?string $email = null)
    {
        $tokenData = DB::table('password_reset_tokens')->where('token', hash('sha256', $token))->first();
        if (!$tokenData) {
            return false;
        }
        if ($email && $tokenData->email !== $email) {
            return false;
        }

        return $tokenData;
    }

    public function isTokenValid(stdClass $tokenData): bool
    {

        if (now()->diffInMinutes($tokenData->created_at) > config('verification.token_expiry_minutes')) {
            return false;
        }
        return true;
    }

    public function revokePasswordToken(User $user): void
    {
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
    }
}
