<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

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
}
