<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\User;

class ProfileService
{
    public function getByUser(User $user): ?Profile
    {
        return $user->profile;
    }

    public function create(User $user, array $data): Profile
    {
        $profile = $user->profile;

        if ($profile) {
            $profile->update($data);

            return $profile->refresh();
        }

        return $user->profile()->create($data);
    }

    public function update(User $user, array $data): Profile
    {
        return $this->create($user, $data);
    }

    public function delete(User $user): bool
    {
        if (! $user->profile) {
            return false;
        }

        return (bool) $user->profile()->delete();
    }

    public function updateOrCreate(User $user, array $data): Profile
    {
        return Profile::updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            $data
        );
    }

    public function exists(User $user): bool
    {
        return $user->profile()->exists();
    }

    public function byId(int $id): ?Profile
    {
        return Profile::find($id);
    }
}
