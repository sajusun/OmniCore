<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\FoodLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class FoodLogController extends Controller
{
    public function index(Request $request)
    {
        return view('backend.layouts.food_logs.index', ['userId' => $request->user]);
    }

    public function ajax(Request $request)
    {
        if (! $request->ajax()) {
            abort(403);
        }

        $query = FoodLog::with('user')->select('food_logs.*')->latest();

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('user', function ($row) {
                return $row->user?->name ?? 'Unknown User';
            })
            ->addColumn('meal_type', function ($row) {
                return '<span class="badge bg-info">' . ($row->meal_type ?? 'N/A') . '</span>';
            })
            ->addColumn('food_score', function ($row) {
                return '<span class="badge bg-success">' . ($row->food_score ?? 'N/A') . '</span>';
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
                    <a href="' . route('admin.food_logs.show', $row->id) . '" class="btn btn-sm btn-info" title="View"><i class="mdi mdi-eye"></i></a>
                    <button type="button" class="btn btn-sm btn-danger delete-scan-history" data-id="' . $row->id . '" title="Delete"><i class="mdi mdi-delete"></i></button>
                </div>';
            })
            ->rawColumns(['meal_type', 'food_score', 'action'])
            ->make(true);
    }

    public function show(FoodLog $foodLog)
    {
        return view('backend.layouts.food_logs.show', compact('foodLog'));
    }

    public function delete(FoodLog $foodLog)
    {
        $foodLog->delete();

        return response()->json([
            'status' => true,
            'message' => 'Food log deleted successfully.'
        ]);
    }
}
