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
                })
                ->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name ?? 'N/A';
                })
                ->addColumn('type', function ($row) {
                    return '<span class="badge bg-info">' . ucfirst($row->type ?? 'Club') . '</span>';
                })
                ->addColumn('creator', function ($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('location', function ($row) {
                    $loc = array_filter([$row->city, $row->state, $row->country]);
                    return !empty($loc) ? implode(', ', $loc) : 'N/A';
                })
                ->addColumn('status', function ($row) {
                    $statusClass = in_array(strtolower($row->status ?? ''), ['published', 'active']) ? 'success' : 'secondary';
                    return '<span class="badge bg-' . $statusClass . '">' . ucfirst($row->status ?? 'Draft') . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.clubs.show', $row->id);
                    return '<div class="d-flex align-items-center gap-2">
                                <a href="' . $showUrl . '" class="btn btn-sm btn-info" title="View Details"><i class="fa fa-eye"></i></a>
                                <button type="button" onclick="confirmDeleteClub(' . $row->id . ', \'' . addslashes($row->name) . '\')" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
                            </div>';
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

    public function destroy(int $id)
    {
        $club = Club::findOrFail($id);
        $club->delete();

        return response()->json(['status' => true, 'message' => 'Club deleted successfully!']);
    }
}
