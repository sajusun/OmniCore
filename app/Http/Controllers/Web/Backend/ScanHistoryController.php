<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\FoodScan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ScanHistoryController extends Controller
{
    public function index(Request $request)
    {
        return view('backend.layouts.scan_histories.index', ['userId' => $request->user]);
    }

    public function ajax(Request $request)
    {
        if (! $request->ajax()) {
            abort(403);
        }

        $query = FoodScan::with('user')->select('food_scans.*')->latest();

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('user', function ($row) {
                return $row->user?->name ?? 'Unknown User';
            })
            ->addColumn('score', function ($row) {
                $score = (int) ($row->ojais_score ?? 0);
                $badge = $score >= 70 ? 'bg-success' : ($score >= 50 ? 'bg-warning text-dark' : 'bg-danger');

                return '<span class="badge ' . $badge . '">' . $score . '</span>';
            })
            ->addColumn('verdict', function ($row) {
                return '<span class="badge bg-info-subtle text-info-emphasis">' . ($row->verdict_label ?? 'N/A') . '</span>';
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at?->format('d M Y, H:i') ?? 'N/A';
            })
            ->filterColumn('user', function ($query, $keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('verdict', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('verdict_label', 'like', "%{$keyword}%")
                        ->orWhere('verdict_key', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('action', function ($row) {
                return '<div class="btn-group" role="group">
                    <a href="' . route('admin.scan_histories.show', $row->id) . '" class="btn btn-sm btn-info" title="View"><i class="mdi mdi-eye"></i></a>
                    <button type="button" class="btn btn-sm btn-danger delete-scan-history" data-id="' . $row->id . '" title="Delete"><i class="mdi mdi-delete"></i></button>
                </div>';
            })
            ->rawColumns(['score', 'verdict', 'action'])
            ->make(true);
    }

    public function show(FoodScan $scanHistory)
    {
        return view('backend.layouts.scan_histories.show', compact('scanHistory'));
    }

    public function delete(FoodScan $scanHistory)
    {
        $scanHistory->delete();

        return response()->json([
            'status' => true,
            'message' => 'Scan history deleted successfully.'
        ]);
    }
}
