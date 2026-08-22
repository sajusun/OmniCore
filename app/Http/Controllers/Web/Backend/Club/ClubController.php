<?php

namespace App\Http\Controllers\Web\Backend\Club;

use App\Models\Club;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ClubController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->query('user') ?? $request->query('user_id');
        $selectedUser = $userId ? User::find($userId) : null;

        if ($request->ajax()) {
            $data = Club::with('creator')
                ->when($userId, function ($query, $userId) {
                    return $query->where('created_by', $userId);
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    return $row->name ?? 'N/A';
                })
                ->editColumn('type', function ($row) {
                    return '<span class="badge bg-info">' . ucfirst($row->type ?? 'Club') . '</span>';
                })
                ->addColumn('creator', function ($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('location', function ($row) {
                    $loc = array_filter([$row->city, $row->state, $row->country]);
                    return !empty($loc) ? implode(', ', $loc) : 'N/A';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->diffForHumans() : 'N/A';
                })
                ->editColumn('status', function ($row) {
                    $status = strtolower($row->status ?? 'pending');
                    $statusClass = match ($status) {
                        'published', 'active' => 'success',
                        'pending' => 'warning text-dark',
                        default => 'secondary',
                    };
                    return '<span class="badge bg-' . $statusClass . '">' . ucfirst($status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.clubs.show', $row->id);
                    return '<div class="d-flex align-items-center gap-2">
                                <a href="' . $showUrl . '" class="btn btn-sm btn-info" title="View Details"><i class="fa fa-eye"></i></a>
                                <button type="button" onclick="confirmDeleteClub(' . $row->id . ', \'' . addslashes($row->name) . '\')" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
                            </div>';
                })
                ->orderColumn('creator', function ($query, $orderId) {
                    $query->orderBy(
                        User::select('name')->whereColumn('users.id', 'clubs.created_by'),
                        $orderId
                    );
                })
                ->orderColumn('creator.name', function ($query, $orderId) {
                    $query->orderBy(
                        User::select('name')->whereColumn('users.id', 'clubs.created_by'),
                        $orderId
                    );
                })
                ->orderColumn('created_at', function ($query, $orderId) {
                    $query->orderBy('clubs.created_at', $orderId);
                })
                ->rawColumns(['type', 'status', 'action'])
                ->make(true);
        }

        return view('backend.clubs.index', compact('selectedUser', 'userId'));
    }

    public function show(int $id)
    {
        $club = Club::with(['creator', 'media', 'members'])->findOrFail($id);
        return view('backend.clubs.show', compact('club'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:draft,published,pending',
        ]);

        $club = Club::findOrFail($id);
        $club->update(['status' => $request->status]);

        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Club status updated successfully!']);
        }

        return redirect()->back()->with('t-success', 'Club status updated successfully!');
    }

    public function destroy(int $id)
    {
        $club = Club::findOrFail($id);
        $club->delete();

        return response()->json(['status' => true, 'message' => 'Club deleted successfully!']);
    }
}
