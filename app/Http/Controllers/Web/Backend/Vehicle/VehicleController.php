<?php

namespace App\Http\Controllers\Web\Backend\Vehicle;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->query('user') ?? $request->query('user_id');
        $selectedUser = $userId ? User::find($userId) : null;

        if ($request->ajax()) {
            $data = Vehicle::with('user')
                ->when($userId, function ($query, $userId) {
                    return $query->where('user_id', $userId);
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    return $row->name ?: ($row->model ? ($row->year . ' ' . $row->model) : 'N/A');
                })
                ->editColumn('brand', function ($row) {
                    $brandVal = is_object($row->brand) ? ($row->brand->value ?? $row->brand->name ?? '') : ($row->brand ?? '');
                    return !empty($brandVal) ? ucfirst((string) $brandVal) : 'N/A';
                })
                ->editColumn('model', function ($row) {
                    return $row->model ?? 'N/A';
                })
                ->editColumn('year', function ($row) {
                    return $row->year ?? 'N/A';
                })
                ->addColumn('user', function ($row) {
                    return $row->user->name ?? 'N/A';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->diffForHumans() : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.vehicles.show', $row->id);
                    return '<div class="d-flex align-items-center gap-2">
                                <a href="' . $showUrl . '" class="btn btn-sm btn-info" title="View Details"><i class="fa fa-eye"></i></a>
                                <button type="button" onclick="confirmDeleteVehicle(' . $row->id . ', \'' . addslashes($row->name ?? 'Vehicle') . '\')" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
                            </div>';
                })
                ->orderColumn('user', function ($query, $orderId) {
                    $query->orderBy(
                        User::select('name')->whereColumn('users.id', 'vehicles.user_id'),
                        $orderId
                    );
                })
                ->orderColumn('user.name', function ($query, $orderId) {
                    $query->orderBy(
                        User::select('name')->whereColumn('users.id', 'vehicles.user_id'),
                        $orderId
                    );
                })
                ->orderColumn('created_at', function ($query, $orderId) {
                    $query->orderBy('vehicles.created_at', $orderId);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.vehicles.index', compact('selectedUser', 'userId'));
    }

    public function show(int $id)
    {
        $vehicle = Vehicle::with(['user', 'garage', 'parts', 'media'])->findOrFail($id);
        return view('backend.vehicles.show', compact('vehicle'));
    }

    public function destroy(int $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();

        return response()->json(['status' => true, 'message' => 'Vehicle deleted successfully!']);
    }
}
