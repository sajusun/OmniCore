<?php

namespace App\Services;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRsvp;
use Illuminate\Support\Facades\DB;
use App\Modules\Media\Traits\HandlesMedia;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EventService
{
    use HandlesMedia;

    /**
     * Get paginated events with optional filters.
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Event::query()
            ->when(isset($filters['event_type']), fn($q) => $q->where('event_type', $filters['event_type']))
            ->when(isset($filters['club_id']), fn($q) => $q->where('club_id', $filters['club_id']))
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['is_public']), fn($q) => $q->where('is_public', filter_var($filters['is_public'], FILTER_VALIDATE_BOOLEAN)))
            ->when(isset($filters['location']), fn($q) => $q->where('location', 'like', '%' . $filters['location'] . '%'))
            ->when(isset($filters['user_id']), fn($q) => $q->where('user_id', $filters['user_id']))
            ->when(!empty($filters['upcoming']), fn($q) => $q->where('event_date', '>=', now()->toDateString()))
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $q->where(function ($sub) use ($filters) {
                    $sub->where('title', 'like', '%' . $filters['search'] . '%')
                       ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                       ->orWhere('location', 'like', '%' . $filters['search'] . '%');
                });
            })
            ->with(['media', 'user', 'club'])
            ->latest('event_date')
            ->paginate($perPage);
    }

    /**
     * Find an event by ID.
     */
    public function find(int $id): Event
    {
        return Event::with(['media', 'user', 'club', 'rsvps.user'])->findOrFail($id);
    }

    /**
     * Store a new event and process media.
     */
    public function store(array $data): Event
    {
        return DB::transaction(function () use ($data) {
            $event = Event::create([
                'user_id'           => $data['user_id'] ?? auth('api')->id(),
                'club_id'           => $data['club_id'] ?? null,
                'title'             => $data['title'],
                'description'       => $data['description'] ?? null,
                'event_type'        => $data['event_type'],
                'location'          => $data['location'],
                'latitude'          => $data['latitude'] ?? null,
                'longitude'         => $data['longitude'] ?? null,
                'event_date'        => $data['event_date'],
                'event_time'        => $data['event_time'],
                'max_participants'  => $data['max_participants'] ?? null,
                'vehicles_required' => $data['vehicles_required'] ?? [],
                'is_public'         => $data['is_public'] ?? true,
                'status'            => $data['status'] ?? 'published',
            ]);

            // Single thumbnail
            if (!empty($data['thumbnail'])) {
                $this->uploadMedia($event, $data['thumbnail'], 'thumbnail');
            }

            // Multiple images
            if (!empty($data['images'])) {
                $this->uploadMedia($event, $data['images'], 'images');
            }

            return $event->load(['media', 'user', 'club']);
        });
    }

    /**
     * Update an event and its media.
     */
    public function update(Event $event, array $data): Event
    {
        return DB::transaction(function () use ($event, $data) {
            $event->update(array_filter([
                'club_id'           => $data['club_id'] ?? null,
                'title'             => $data['title'] ?? null,
                'description'       => $data['description'] ?? null,
                'event_type'        => $data['event_type'] ?? null,
                'location'          => $data['location'] ?? null,
                'latitude'          => $data['latitude'] ?? null,
                'longitude'         => $data['longitude'] ?? null,
                'event_date'        => $data['event_date'] ?? null,
                'event_time'        => $data['event_time'] ?? null,
                'max_participants'  => $data['max_participants'] ?? null,
                'vehicles_required' => $data['vehicles_required'] ?? null,
                'is_public'         => isset($data['is_public']) ? filter_var($data['is_public'], FILTER_VALIDATE_BOOLEAN) : null,
                'status'            => $data['status'] ?? null,
            ], fn($val) => !is_null($val)));

            if (isset($data['thumbnail'])) {
                if ($data['thumbnail']) {
                    $this->updateMedia($event, $data['thumbnail'], 'thumbnail');
                } else {
                    $this->deleteCollectionMedia($event, 'thumbnail');
                }
            }

            if (isset($data['images'])) {
                if ($data['images']) {
                    $this->updateMedia($event, $data['images'], 'images');
                } else {
                    $this->deleteCollectionMedia($event, 'images');
                }
            }

            return $event->load(['media', 'user', 'club']);
        });
    }

    /**
     * Delete an event and purge its media.
     */
    public function delete(Event $event): bool
    {
        return DB::transaction(function () use ($event) {
            $mediaIds = $event->media()->pluck('id')->toArray();
            if (!empty($mediaIds)) {
                $this->deleteMedia($mediaIds);
            }

            return $event->delete();
        });
    }

    /**
     * RSVP to an event (going / interested).
     */
    public function rsvp(Event $event, User $user, string $status): EventRsvp
    {
        if (!in_array($status, ['going', 'interested'])) {
            throw new \InvalidArgumentException("Invalid RSVP status. Must be 'going' or 'interested'.");
        }

        if ($status === 'going' && $event->max_participants) {
            $currentGoingCount = $event->rsvps()
                ->where('status', 'going')
                ->where('user_id', '!=', $user->id)
                ->count();

            if ($currentGoingCount >= $event->max_participants) {
                throw new \RuntimeException("Event maximum capacity limit reached.");
            }
        }

        return EventRsvp::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $user->id],
            ['status' => $status]
        );
    }

    /**
     * Cancel/Remove RSVP from an event.
     */
    public function cancelRsvp(Event $event, User $user): bool
    {
        return EventRsvp::where([
            'event_id' => $event->id,
            'user_id'  => $user->id,
        ])->delete() > 0;
    }

    /**
     * Get paginated RSVP list of an event.
     */
    public function rsvps(Event $event, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        return EventRsvp::where('event_id', $event->id)
            ->when($status, fn($q) => $q->where('status', $status))
            ->with(['user'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Helper to delete collection media.
     */
    protected function deleteCollectionMedia(Event $event, string $collection): void
    {
        $ids = $event->media()->where('collection_name', $collection)->pluck('id')->toArray();
        if (!empty($ids)) {
            $this->deleteMedia($ids);
        }
    }
}
