<?php

namespace App\Services;

use App\Models\Club;
use Illuminate\Support\Facades\DB;
use App\Modules\Media\Traits\HandlesMedia;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ClubService
{
    use HandlesMedia;

    /**
     * Get a paginated list of clubs with optional filters.
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Club::query()
            ->when(isset($filters['type']), fn($q) => $q->where('type', $filters['type']))
            ->when(isset($filters['country']), fn($q) => $q->where('country', $filters['country']))
            ->when(isset($filters['state']), fn($q) => $q->where('state', $filters['state']))
            ->when(isset($filters['city']), fn($q) => $q->where('city', $filters['city']))
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['created_by']), fn($q) => $q->where('created_by', $filters['created_by']))
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $q->where(function ($sub) use ($filters) {
                    $sub->where('name', 'like', '%' . $filters['search'] . '%')
                       ->orWhere('description', 'like', '%' . $filters['search'] . '%');
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
                'name'        => $data['name'] ?? $data['club_name'] ?? null,
                'type'        => $data['type'] ?? $data['club_type'] ?? null,
                'country'     => $data['country'] ?? null,
                'state'       => $data['state'] ?? null,
                'city'        => $data['city'] ?? null,
                'description' => $data['description'] ?? null,
                'status'      => $data['status'] ?? 'draft',
                'created_by'  => $data['created_by'] ?? auth('api')->id(),
            ]);

            // Handle Thumbnail (single)
            if (!empty($data['thumbnail'])) {
                $this->uploadMedia($club, $data['thumbnail'], 'thumbnail');
            }

            // Handle Images (multiple/single)
            if (!empty($data['images'])) {
                $this->uploadMedia($club, $data['images'], 'images');
            }

            // Handle Videos (multiple/single)
            if (!empty($data['video'])) {
                $this->uploadMedia($club, $data['video'], 'video');
            }

            if (!empty($data['videos'])) {
                $this->uploadMedia($club, $data['videos'], 'video');
            }

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
                'name'        => $data['name'] ?? $data['club_name'] ?? null,
                'type'        => $data['type'] ?? $data['club_type'] ?? null,
                'country'     => $data['country'] ?? null,
                'state'       => $data['state'] ?? null,
                'city'        => $data['city'] ?? null,
                'description' => $data['description'] ?? null,
                'status'      => $data['status'] ?? null,
            ], fn($value) => !is_null($value)));

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
            if (!empty($mediaIds)) {
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
        if (!empty($ids)) {
            $this->deleteMedia($ids);
        }
    }
}
