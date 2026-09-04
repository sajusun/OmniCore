<?php

namespace App\Modules\Product\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\ProductReview;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductReview::with(['product', 'user', 'media'])->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('product', fn ($row) => $row->product ? $row->product->name : 'N/A')
                ->addColumn('user', fn ($row) => $row->user ? $row->user->name : 'N/A')
                ->addColumn('rating', function ($row) {
                    $stars = str_repeat('<i class="fa fa-star text-warning"></i>', $row->rating);
                    return '<div>' . $stars . ' <span class="small fw-bold">(' . $row->rating . '/5)</span></div>';
                })
                ->addColumn('status', function ($row) {
                    $badge = match ($row->status) {
                        'approved' => 'bg-success',
                        'pending' => 'bg-warning text-dark',
                        'rejected' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    return '<span class="badge ' . $badge . '">' . ucfirst($row->status) . '</span>';
                })
                ->rawColumns(['rating', 'status'])
                ->make(true);
        }

        return view('product::backend.reviews.index');
    }
}
