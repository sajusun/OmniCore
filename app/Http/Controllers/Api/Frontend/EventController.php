<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Enums\EventTypeEnum;
use App\Enums\VehicleCarTypeEnum;
use App\Enums\VehicleMotorcycleTypeEnum;
use App\Enums\VehicleRequiredEnum;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Http\Resources\VehicleResource;
use App\Models\Event;
use App\Models\User;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    private User $user;

    private EventService $eventService;

    public function __construct(EventService $eventService)
    {
        parent::__construct();
        $this->user = auth('api')->user();
        $this->eventService = $eventService;
    }

    /**
     * Display a listing of events.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'event_type',
            'club_id',
            'status',
            'is_public',
            'location',
            'user_id',
            'upcoming',
            'search',
            'vehicles_required',
        ]);
        $perPage = (int) $request->query('per_page', 15);

        $events = $this->eventService->list($filters, $perPage, for_public: true);
        $events->each(function ($event) {
            $event->distance = $this->eventService->getDistance($event);
        });

        return Helper::jsonResponse(true, 'Events retrieved successfully', 200, EventResource::collection($events), true, $events);
    }

    public function myEvent(Request $request): JsonResponse
    {
        $filters = $request->only([
            'event_type',
            'club_id',
            'status',
            'is_public',
            'location',
            'user_id',
            'upcoming',
            'search',
        ]);
        $perPage = (int) $request->query('per_page', 15);

        $events = $this->eventService->list($filters, $perPage, auth('api')->user());
        // $events->each(function ($event) {
        //     $event->distance = $this->eventService->getDistance($event);
        // });

        return Helper::jsonResponse(true, 'Events retrieved successfully', 200, EventResource::collection($events), true, $events);
    }

    /**
     * Store a newly created event.
     */
    public function store(StoreEventRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail');
        }
        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }

        $event = $this->eventService->store($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Event created successfully!',
            'data' => new EventResource($event),
        ], 201);
    }

    /**
     * Display the specified event.
     */
    public function show(int $id): JsonResponse
    {
        $event = $this->eventService->find($id);
        $event->distance = $this->eventService->getDistance($event);

        return $this->success(new EventResource($event), 'Event retrieved successfully', 200);
    }

    /**
     * Update the specified event.
     */
    public function update(UpdateEventRequest $request, Event $event): JsonResponse
    {
        if ($event->user_id !== auth('api')->id()) {
            return $this->error('You do not have permission to update this event.', null, 403);
        }

        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail');
        }
        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }

        $updatedEvent = $this->eventService->update($event, $data);

        return $this->success(new EventResource($updatedEvent), 'Event updated successfully!', 200);
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event): JsonResponse
    {
        if ($event->user_id !== auth('api')->id()) {
            return $this->error('You do not have permission to delete this event.', null, 403);
        }

        $result = $this->eventService->delete($event);

        if ($result) {
            return $this->success([], 'Event successfully deleted!', 200);
        }

        return $this->error('Failed to delete event.', null, 500);
    }

    /**
     * RSVP to an event (going / interested).
     */
    public function rsvp(Request $request, Event $event): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:going,interested',
            'vehicle_id' => 'required|integer|exists:vehicles,id',
        ]);

        try {
            $rsvp = $this->eventService->rsvp($event, $this->user, $request->input('status'), $request->input('vehicle_id'));

            return $this->success([
                'status' => $rsvp->status,
                'message' => "You marked yourself as '{$rsvp->status}' for this event.",
            ], 'RSVP updated successfully', 200);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), null, 422);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 400);
        }
    }

    /**
     * Cancel/Remove RSVP from an event.
     */
    public function cancelRsvp(Event $event): JsonResponse
    {
        $this->eventService->cancelRsvp($event, auth('api')->user());

        return $this->success([], 'RSVP cancelled successfully', 200);
    }

    /**
     * List users who RSVPed to an event.
     */
    public function rsvps(Event $event, Request $request): JsonResponse
    {
        $status = $request->query('status'); // 'going' or 'interested' or null
        $perPage = (int) $request->query('per_page', 15);

        $rsvps = $this->eventService->rsvps($event, $status, $perPage);

        return $this->response(
            status: true,
            message: 'RSVPs retrieved successfully',
            code: 200,
            data: $rsvps->map(fn ($item) => [
                'user_id' => $item->user_id,
                'name' => $item->user?->name,
                'avatar' => $item->user?->avatar ?? null,
                'status' => $item->status,
                'vehicle' => new VehicleResource($item->vehicle),
                'created_at' => $item->created_at?->toIso8601String(),
            ]),
            paginate: true,
            paginateData: $rsvps
        );
    }

    /**
     * Get event metadata (Event Types & Vehicle Requirements list).
     */
    public function meta(): JsonResponse
    {
        return $this->success([
            'event_types' => EventTypeEnum::toArray(),
            // 'vehicle_requirements' => VehicleRequiredEnum::toArray(),
            'car_types' => VehicleCarTypeEnum::toArray(),
            'motorcycle_types' => VehicleMotorcycleTypeEnum::toArray(),
        ], 'Metadata retrieved successfully', 200);
    }

    public function matchingParts(Event $event)
    {
        return $this->eventService->matchingParts($event, $this->user);
    }

    public function allParts(Event $event)
    {
        return $this->eventService->allParts($event, $this->user);
    }

    /**
     * Bookmark or remove bookmark from an event.
     */
    public function toggleBookmark(Event $event): JsonResponse
    {
        $result = $this->eventService->toggleBookmark($event, auth('api')->user());

        return $this->success($result, $result['message'], 200);
    }

    /**
     * Get paginated bookmarked events for the authenticated user.
     */
    public function bookmarkedEvents(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $events = $this->eventService->getBookmarkedEvents(auth('api')->user(), $perPage);

        $events->each(function ($event) {
            $event->distance = $this->eventService->getDistance($event);
        });

        return Helper::jsonResponse(true, 'Bookmarked events retrieved successfully', 200, EventResource::collection($events), true, $events);
    }

    public function filterTypes()
    {
        return Helper::jsonResponse(true, 'fetch filters types', 200, [
            'car_types' => VehicleCarTypeEnum::toArray(),
            'motorcycle_types' => VehicleMotorcycleTypeEnum::toArray(),
        ]);
    }
}
