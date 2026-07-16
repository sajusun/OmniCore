<?php

namespace App\Traits;

use App\Models\Favorite;

trait Favoritable
{
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function isFavoritedBy(int $userId): bool
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }
    public function toggleFavorite(int $userId): bool
    {
        $favorite = $this->favorites()
            ->where('user_id', $userId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return false;
        }

        $this->favorites()->create([
            'user_id' => $userId,
        ]);

        return true;
    }
}
