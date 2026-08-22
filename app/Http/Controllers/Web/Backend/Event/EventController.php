<?php

namespace App\Http\Controllers\Web\Backend\Event;

use App\Models\Club;
use App\Models\User;
use App\Models\Event;
use App\Enums\EventTypeEnum;
use Illuminate\Http\Request;
use App\Services\EventService;
use App\Enums\VehicleRequiredEnum;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class EventController extends Controller
{
    public function __construct(private EventService $eventService) {}

    public function index(Request $request)
    {
        $userId = $request->query('user') ?? $request->query('user_id');
        $selectedUser = $userId ? User::find($userId) : null;

        if ($request->ajax()) {
            $data = Event::with(['media', 'user', 'club'])
                ->when($userId, function ($query, $userId) {
                    return $query->where('user_id', $userId);
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('title', function ($row) {
                    return $row->title ?? 'N/A';
                })
                ->editColumn('event_type', function ($row) {
                    return $row->event_type_label ?? $row->event_type ?? 'N/A';
                })
                ->addColumn('user', function ($row) {
                    return $row->user->name ?? 'N/A';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->diffForHumans() : 'N/A';
                })
                ->editColumn('status', function ($row) {
                    $status = $row->status == 'Published' ? 'success' : 'danger';

                    return '<span class="badge bg-' . $status . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="flex items-center gap-1.5">
                                <a href="' . route('admin.events.edit', $row->id) . '" class="inline-flex items-center justify-center p-2 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors duration-150" title="Edit"><i class="fa fa-edit text-sm leading-none"></i></a>
                                <button type="button" onclick="confirmDeleteUser(' . $row->id . ', \'' . addslashes($row->name ?? $row->title) . '\')" class="inline-flex items-center justify-center p-2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors duration-150" title="Delete"><i class="fa fa-trash text-sm leading-none"></i></button>
                            </div>';
                })
                ->orderColumn('user', function ($query, $orderId) {
                    $query->orderBy(
                        User::select('name')->whereColumn('users.id', 'events.user_id'),
                        $orderId
                    );
                })
                ->orderColumn('user.name', function ($query, $orderId) {
                    $query->orderBy(
                        User::select('name')->whereColumn('users.id', 'events.user_id'),
                        $orderId
                    );
                })
                ->orderColumn('created_at', function ($query, $orderId) {
                    $query->orderBy('events.created_at', $orderId);
                })
                ->rawColumns(['status', 'user', 'action'])
                ->make(true);
        }

        return view('backend.events.index', compact('selectedUser', 'userId'));
    }


    public function edit(Event $event)
    {
        $event->load('media');
        $users = User::select('id', 'name', 'email')->get();
        $clubs = Club::select('id', 'name')->get();
        $eventTypes = EventTypeEnum::cases();
        $vehicleOptions = VehicleRequiredEnum::cases();

        return view('backend.events.edit', compact('event', 'users', 'clubs', 'eventTypes', 'vehicleOptions'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $event_id)
    {
        $event = Event::findOrFail($event_id);
        $event = $this->eventService->update($event, $request->all());

        return redirect()->route('admin.events.index')->with('t-success', 'updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $event = Event::findOrFail($id);
        $result = $this->eventService->delete($event);
        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Operation Failed!']);
        }

        return response()->json(['status' => true, 'message' => 'Operation Successful!']);
    }


}
