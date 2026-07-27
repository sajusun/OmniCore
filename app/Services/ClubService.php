<?php

namespace App\Services;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\User;
use App\Modules\Media\Traits\HandlesMedia;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ClubService
{
    use HandlesMedia;

    /**
     * Get a paginated list of clubs with optional filters.
     */
    public function list(array $filters = [], int $perPage = 15, ?User $user = null): LengthAwarePaginator
    {
        return Club::query()
            ->when($user, fn ($q) => $q->where('created_by', $user->id))
            ->when(isset($filters['type']), fn ($q) => $q->where('type', $filters['type']))
            ->when(isset($filters['country']), fn ($q) => $q->where('country', $filters['country']))
            ->when(isset($filters['state']), fn ($q) => $q->where('state', $filters['state']))
            ->when(isset($filters['city']), fn ($q) => $q->where('city', $filters['city']))
            ->when(isset($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['created_by']), fn ($q) => $q->where('created_by', $filters['created_by']))
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $q->where(function ($sub) use ($filters) {
                    $sub->where('name', 'like', '%'.$filters['search'].'%')
                        ->orWhere('description', 'like', '%'.$filters['search'].'%');
                });
            })
            ->with(['media', 'creator'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find a club by ID.
     */
    public function find(int $id): Club
    {
        return Club::with(['media', 'creator'])->findOrFail($id);
    }

    /**
     * Create a new club and handle media.
     */
    public function store(array $data): Club
    {
        return DB::transaction(function () use ($data) {
            $club = Club::create([
                'name' => $data['name'] ?? $data['club_name'] ?? null,
                'type' => $data['type'] ?? $data['club_type'] ?? null,
                'country' => $data['country'] ?? null,
                'state' => $data['state'] ?? null,
                'city' => $data['city'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'created_by' => $data['created_by'] ?? auth('api')->id(),
            ]);

            // Handle Thumbnail (single)
            if (! empty($data['thumbnail'])) {
                $this->uploadMedia($club, $data['thumbnail'], 'thumbnail');
            }

            // Handle Images (multiple/single)
            if (! empty($data['images'])) {
                $this->uploadMedia($club, $data['images'], 'images');
            }

            // Handle Videos (multiple/single)
            if (! empty($data['video'])) {
                $this->uploadMedia($club, $data['video'], 'video');
            }

            if (! empty($data['videos'])) {
                $this->uploadMedia($club, $data['videos'], 'video');
            }

            // Auto-add creator as admin member
            ClubMember::create([
                'club_id' => $club->id,
                'user_id' => $club->created_by,
                'role' => 'admin',
                'status' => 'approved',
                'joined_at' => now(),
                'approved_at' => now(),
                'approved_by' => $club->created_by,
            ]);

            return $club->load('media');
        });
    }

    /**
     * Update an existing club and media files.
     */
    public function update(Club $club, array $data): Club
    {
        return DB::transaction(function () use ($club, $data) {
            $club->update(array_filter([
                'name' => $data['name'] ?? $data['club_name'] ?? null,
                'type' => $data['type'] ?? $data['club_type'] ?? null,
                'country' => $data['country'] ?? null,
                'state' => $data['state'] ?? null,
                'city' => $data['city'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? null,
            ], fn ($value) => ! is_null($value)));

            // Handle Thumbnail (single)
            if (isset($data['thumbnail'])) {
                if ($data['thumbnail']) {
                    $this->updateMedia($club, $data['thumbnail'], 'thumbnail');
                } else {
                    $this->deleteCollectionMedia($club, 'thumbnail');
                }
            }

            // Handle Images (multiple/single)
            if (isset($data['images'])) {
                if ($data['images']) {
                    $this->updateMedia($club, $data['images'], 'images');
                } else {
                    $this->deleteCollectionMedia($club, 'images');
                }
            }

            // Handle Videos (multiple/single)
            if (isset($data['video'])) {
                if ($data['video']) {
                    $this->updateMedia($club, $data['video'], 'video');
                } else {
                    $this->deleteCollectionMedia($club, 'video');
                }
            }

            if (isset($data['videos'])) {
                if ($data['videos']) {
                    $this->updateMedia($club, $data['videos'], 'video');
                } else {
                    $this->deleteCollectionMedia($club, 'video');
                }
            }

            return $club->load('media');
        });
    }

    /**
     * Delete a club and its attached media.
     */
    public function delete(Club $club): bool
    {
        return DB::transaction(function () use ($club) {
            // Retrieve all media IDs associated with this club
            $mediaIds = $club->media()->pluck('id')->toArray();
            if (! empty($mediaIds)) {
                $this->deleteMedia($mediaIds);
            }

            return $club->delete();
        });
    }

    /**
     * Helper method to delete all media of a specific collection.
     */
    protected function deleteCollectionMedia(Club $club, string $collection): void
    {
        $ids = $club->media()->where('collection_name', $collection)->pluck('id')->toArray();
        if (! empty($ids)) {
            $this->deleteMedia($ids);
        }
    }

    // ── Membership Methods ─────────────────────────────────────────────────────

    /**
     * Join a club.
     *
     * Currently auto-approves.
     * Future: when club has 'approval_required = true', set status = 'pending'.
     */
    public function join(Club $club, User $user): ClubMember
    {
        // Already a member?
        $existing = $club->getMembership($user->id);
        if ($existing) {
            return $existing;
        }

        // Creator is always admin — guard against double-insert
        if ($club->isCreator($user->id)) {
            throw new \RuntimeException('You are the creator of this club.');
        }

        /**
         * Future hook: swap 'approved' → 'pending' when club enables approval mode.
         *
         *   $status = $club->approval_required ? 'pending' : 'approved';
         */
        $status = 'approved';

        return ClubMember::create([
            'club_id' => $club->id,
            'user_id' => $user->id,
            'role' => 'member',
            'status' => $status,
            'joined_at' => now(),
            'approved_at' => $status === 'approved' ? now() : null,
        ]);
    }

    /**
     * Leave a club.
     */
    public function leave(Club $club, User $user): void
    {
        if ($club->isCreator($user->id)) {
            throw new \RuntimeException('The club creator cannot leave. Transfer ownership or delete the club.');
        }

        $club->memberships()->where('user_id', $user->id)->delete();
    }

    /**
     * Approve a pending membership request.
     * Future admin-approval use.
     */
    public function approveMember(Club $club, User $targetUser, User $approver): ClubMember
    {
        if (! $club->isCreator($approver->id) && ! $club->isMemberAdmin($approver->id)) {
            throw new \RuntimeException('Only club admins can approve members.');
        }

        $membership = $club->getMembership($targetUser->id);
        if (! $membership) {
            throw new \RuntimeException('No membership request found for this user.');
        }

        $membership->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approver->id,
        ]);

        return $membership->fresh();
    }

    /**
     * Reject a pending membership request.
     * Future admin-approval use.
     */
    public function rejectMember(Club $club, User $targetUser, User $approver): ClubMember
    {
        if (! $club->isCreator($approver->id) && ! $club->isMemberAdmin($approver->id)) {
            throw new \RuntimeException('Only club admins can reject members.');
        }

        $membership = $club->getMembership($targetUser->id);
        if (! $membership) {
            throw new \RuntimeException('No membership request found for this user.');
        }

        $membership->update([
            'status' => 'rejected',
            'approved_by' => $approver->id,
        ]);

        return $membership->fresh();
    }

    /**
     * Admin forcefully removes a member from the club.
     */
    public function removeMember(Club $club, User $targetUser, User $admin): void
    {
        if (! $club->isCreator($admin->id) && ! $club->isMemberAdmin($admin->id)) {
            throw new \RuntimeException('Only club admins can remove members.');
        }

        if ($club->isCreator($targetUser->id)) {
            throw new \RuntimeException('Cannot remove the club creator.');
        }

        $club->memberships()->where('user_id', $targetUser->id)->delete();
    }

    /**
     * List approved members of a club (paginated).
     */
    public function members(Club $club, int $perPage = 15): LengthAwarePaginator
    {
        return ClubMember::where('club_id', $club->id)
            ->where('status', 'approved')
            ->with('user')
            ->latest('joined_at')
            ->paginate($perPage);
    }
}
