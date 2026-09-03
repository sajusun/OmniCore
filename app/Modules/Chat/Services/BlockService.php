<?php

namespace App\Modules\Chat\Services;

use App\Modules\Chat\Models\UserBlock;
use Illuminate\Support\Collection;

class BlockService
{
    /**
     * Block a user.
     */
    public function block(int $userId, int $blockedUserId): void
    {
        if ($userId === $blockedUserId) {
            throw new \InvalidArgumentException("You cannot block yourself.");
        }

        UserBlock::updateOrCreate([
            'user_id'         => $userId,
            'blocked_user_id' => $blockedUserId,
        ]);
    }

    /**
     * Unblock a user.
     */
    public function unblock(int $userId, int $blockedUserId): void
    {
        UserBlock::where([
            'user_id'         => $userId,
            'blocked_user_id' => $blockedUserId,
        ])->delete();
    }

    /**
     * Check if a block exists between two users (either side).
     */
    public function isBlocked(int $userId, int $blockedUserId): bool
    {
        return $this->hasBlocked($userId, $blockedUserId) || $this->isBlockedBy($userId, $blockedUserId);
    }

    /**
     * Check if user A has blocked user B.
     */
    public function hasBlocked(int $userId, int $blockedUserId): bool
    {
        return UserBlock::where([
            'user_id'         => $userId,
            'blocked_user_id' => $blockedUserId,
        ])->exists();
    }

    /**
     * Check if user A is blocked by user B.
     */
    public function isBlockedBy(int $userId, int $blockerId): bool
    {
        return UserBlock::where([
            'user_id'         => $blockerId,
            'blocked_user_id' => $userId,
        ])->exists();
    }

    /**
     * Get list of users blocked by a specific user.
     */
    public function blockedUsers(int $userId): Collection
    {
        return UserBlock::where('user_id', $userId)
            ->with('blockedUser')
            ->get()
            ->map(fn($block) => $block->blockedUser);
    }
}
