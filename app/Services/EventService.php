<?php

namespace App\Services;

use App\Models\User;
use App\Models\Event;
use App\Models\Vehicle;
use App\Models\EventRsvp;
use App\Models\EventBookmark;
use Illuminate\Support\Facades\DB;
use App\Modules\Media\Traits\HandlesMedia;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EventService
{
    use HandlesMedia;

    /**
     * Get paginated events with optional filters.
     */
    public function list(array $filters = [], int $perPage = 15, ?User $user = null, $paginate = true, mixed $for_public=null): LengthAwarePaginator|array
    {
        $query = Event::query()
            ->when($user, fn($q) => $q->where('user_id', $user->id))
            ->when(isset($filters['event_type']), fn($q) => $q->where('event_type', $filters['event_type']))
            ->when(isset($filters['club_id']), fn($q) => $q->where('club_id', $filters['club_id']))
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['is_public']), fn($q) => $q->where('is_public', filter_var($filters['is_public'], FILTER_VALIDATE_BOOLEAN)))
            ->when(isset($filters['location']), fn($q) => $q->where('location', 'like', '%' . $filters['location'] . '%'))
            ->when(isset($filters['user_id']), fn($q) => $q->where('user_id', $filters['user_id']))
            ->when(! empty($filters['upcoming']), fn($q) => $q->where('event_date', '>=', now()->toDateString()))
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $q->where(function ($sub) use ($filters) {
                    $sub->where('title', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('location', 'like', '%' . $filters['search'] . '%');
                });
            })->when(isset($for_public), fn($q) => $q->where('is_public', 1)->where('status', 'published'))
            ->with(['media', 'user', 'club'])->latest('event_date');

        return $paginate ? $query->paginate($perPage) : $query;
    }



    public function getDistance(Event $event)
    {
        $user = auth('api')->user();
        if (!$user->profile->latitude && !$user->profile->longitude) {
            return 0;
        }
        return app(LocationService::class)->getDistance(
            $user->profile->latitude,
            $user->profile->longitude,
            $event->latitude,
            $event->longitude
        );
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
                'user_id' => $data['user_id'] ?? auth('api')->id(),
                'club_id' => $data['club_id'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'event_type' => $data['event_type'],
                'location' => $data['location'],
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'event_date' => $data['event_date'],
                'event_time' => $data['event_time'],
                'max_participants' => $data['max_participants'] ?? null,
                'vehicles_required' => $data['vehicles_required'] ?? [],
                'is_public' => $data['is_public'] ?? true,
                'status' => $data['status'] ?? 'draft',
            ]);

            // Single thumbnail
            if (! empty($data['thumbnail'])) {
                $this->uploadMedia($event, $data['thumbnail'], 'thumbnail');
            }

            // Multiple images
            if (! empty($data['images'])) {
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
                'club_id' => $data['club_id'] ?? null,
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'event_type' => $data['event_type'] ?? null,
                'location' => $data['location'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'event_date' => $data['event_date'] ?? null,
                'event_time' => $data['event_time'] ?? null,
                'max_participants' => $data['max_participants'] ?? null,
                'vehicles_required' => $data['vehicles_required'] ?? null,
                'is_public' => isset($data['is_public']) ? filter_var($data['is_public'], FILTER_VALIDATE_BOOLEAN) : null,
                'status' => $data['status'] ?? null,
            ], fn($val) => ! is_null($val)));

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
            if (! empty($mediaIds)) {
                $this->deleteMedia($mediaIds);
            }

            return $event->delete();
        });
    }

    /**
     * RSVP to an event (going / interested).
     */
    public function rsvp(Event $event, User $user, string $status, int $vehicle_id): EventRsvp
    {
        if (! in_array($status, ['going', 'interested'])) {
            throw new \InvalidArgumentException("Invalid RSVP status. Must be 'going' or 'interested'.");
        }
        if (! Vehicle::where('id', $vehicle_id)->where('user_id', $user->id)->exists()) {
            throw new \InvalidArgumentException("Invalid RSVP Vehicle. Must be 'your own garage vehile'.");
        }

        if ($status === 'going' && $event->max_participants) {
            $currentGoingCount = $event->rsvps()
                ->where('status', 'going')
                ->where('user_id', '!=', $user->id)
                ->count();

            if ($currentGoingCount >= $event->max_participants) {
                throw new \RuntimeException('Event maximum capacity limit reached.');
            }
        }

        return EventRsvp::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $user->id],
            [
                'status' => $status,
                'vehicle_id' => $vehicle_id,
            ],

        );
    }

    /**
     * Cancel/Remove RSVP from an event.
     */
    public function cancelRsvp(Event $event, User $user): bool
    {
        return EventRsvp::where([
            'event_id' => $event->id,
            'user_id' => $user->id,
        ])->delete() > 0;
    }

    /**
     * Get paginated RSVP list of an event.
     */
    public function rsvps(Event $event, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        return EventRsvp::where('event_id', $event->id)
            ->when($status, fn($q) => $q->where('status', $status))
            ->with(['user', 'vehicle.parts.media'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Helper to delete collection media.
     */
    protected function deleteCollectionMedia(Event $event, string $collection): void
    {
        $ids = $event->media()->where('collection_name', $collection)->pluck('id')->toArray();
        if (! empty($ids)) {
            $this->deleteMedia($ids);
        }
    }


    public function matchingParts(Event $event, User $user)
    {
        // Get current user's RSVP with vehicle
        $myRsvp = EventRsvp::where('event_id', $event->id)
            ->where('user_id', $user->id)
            // ->where('status', 'going')
            ->with('vehicle')
            ->first();

        if (! $myRsvp || ! $myRsvp->vehicle) {
            return []; // User hasn't selected a vehicle or doesn't have one
        }

        $myVehicle = $myRsvp->vehicle;
        $myBrand = $myVehicle->brand; // This is a string
        $myModel = $myVehicle->model; // This is a string
        $myParts = $myVehicle->parts->pluck('name')->toArray();


        // Get all going persons with their vehicles
        $goingPersons = EventRsvp::where('event_id', $event->id)
            // ->where('status', 'going')
            ->where('user_id', '!=', $user->id) // Exclude current user
            ->with(['user', 'vehicle.parts'])
            ->get();

        // return $goingPersons;

        $matchedPersons = [];

        foreach ($goingPersons as $goingPerson) {
            $personVehicle = $goingPerson->vehicle;

            // Skip if no vehicle
            if (! $personVehicle) {
                continue;
            }

            // Check if brand AND model match (string comparison)
            $brandMatches = strtolower($personVehicle->brand->value) === strtolower($myBrand->value);
            $modelMatches = strtolower($personVehicle->model) === strtolower($myModel);

            // Only proceed if both brand and model match
            if ($brandMatches && $modelMatches) {
                $matchedParts = [];

                // Check matching parts
                foreach ($personVehicle->parts as $part) {

                    if (in_array($part->name, $myParts)) {
                        $matchedParts[] = [
                            'id' => $part->id,
                            'name' => $part->name,
                            'image' => $part->media->first()->url,
                        ];
                    }
                }

                // Only add if there are matching parts
                if (! empty($matchedParts)) {
                    $matchedPersons[] = [
                        'user' => [
                            'id' => $goingPerson->user->id,
                            'name' => $goingPerson->user->name,
                            'avatar' => $goingPerson->user->avatar,
                        ],
                        'vehicle' => [
                            'id' => $personVehicle->id,
                            'name' => $personVehicle->name,
                        ],
                        'matched_parts' => $matchedParts,
                        'total_parts_matched' => count($matchedParts),
                        'status' => $goingPerson->status,
                    ];
                }
            }
        }

        // Sort by most matching parts first
        usort($matchedPersons, function ($a, $b) {
            return $b['total_parts_matched'] - $a['total_parts_matched'];
        });

        return $matchedPersons;
    }

    public function allParts(Event $event, User $user)
    {
        // Get current user's RSVP with vehicle
        $myRsvp = EventRsvp::where('event_id', $event->id)
            ->where('user_id', $user->id)
            // ->where('status', 'going')
            ->with('vehicle')
            ->first();

        if (! $myRsvp || ! $myRsvp->vehicle) {
            return []; // User hasn't selected a vehicle or doesn't have one
        }

        $myVehicle = $myRsvp->vehicle;
        $myBrand = $myVehicle->brand; // This is a string
        $myModel = $myVehicle->model; // This is a string
        $myParts = $myVehicle->parts->pluck('name')->toArray();


        // Get all going persons with their vehicles
        $goingPersons = EventRsvp::where('event_id', $event->id)
            // ->where('status', 'going')
            ->where('user_id', '!=', $user->id) // Exclude current user
            ->with(['user', 'vehicle.parts'])
            ->get();

        // return $goingPersons;

        $matchedPersons = [];

        foreach ($goingPersons as $goingPerson) {
            $personVehicle = $goingPerson->vehicle;

            // Skip if no vehicle
            if (! $personVehicle) {
                continue;
            }


            // Check matching parts
            foreach ($personVehicle->parts as $part) {

                $matchedParts[] = [
                    'id' => $part->id,
                    'name' => $part->name,
                    'image' => $part->media->first()->url,
                ];
            }

            if (! empty($matchedParts)) {
                $matchedPersons[] = [
                    'user' => [
                        'id' => $goingPerson->user->id,
                        'name' => $goingPerson->user->name,
                        'avatar' => $goingPerson->user->avatar,
                    ],
                    'vehicle' => [
                        'id' => $personVehicle->id,
                        'name' => $personVehicle->name,
                    ],
                    'parts' => $matchedParts,
                    'total_parts' => count($matchedParts),
                    'status' => $goingPerson->status,
                ];
            }
        }

        return $matchedPersons;
    }

    protected function notify($goingPerson, $matchPerson) {}

    /**
     * Toggle bookmark for an event.
     */
    public function toggleBookmark(Event $event, User $user): array
    {
        $bookmark = EventBookmark::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if ($bookmark) {
            $bookmark->delete();

            return [
                'is_bookmarked' => false,
                'message' => 'Event removed from bookmarks successfully',
            ];
        }

        EventBookmark::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);

        return [
            'is_bookmarked' => true,
            'message' => 'Event bookmarked successfully',
        ];
    }

    /**
     * Get paginated bookmarked events for a user.
     */
    public function getBookmarkedEvents(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Event::query()
            ->whereHas('bookmarks', fn($q) => $q->where('user_id', $user->id))
            ->with(['media', 'user', 'club'])
            ->latest('event_date')
            ->paginate($perPage);
    }
}
