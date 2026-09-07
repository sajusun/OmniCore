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
            $query = ProductReview::with(['product', 'user'])->latest();

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('product_info', function ($row) {
                    if (! $row->product) {
                        return '<span class="text-muted">Deleted Product</span>';
                    }

                    return '<a href="'.route('admin.products.show', $row->product->id).'" class="fw-bold text-dark text-decoration-none">'.e($row->product->name).'</a>';
                })
                ->addColumn('customer', fn ($row) => $row->user ? '<div><span class="fw-medium">'.e($row->user->name).'</span><br><small class="text-muted">'.e($row->user->email).'</small></div>' : '<span class="text-muted">Guest</span>')
                ->addColumn('rating_stars', function ($row) {
                    $stars = '';
                    for ($i = 1; $i <= 5; $i++) {
                        $stars .= $i <= $row->rating ? '<i class="fa fa-star text-warning"></i>' : '<i class="fa-regular fa-star text-muted"></i>';
                    }

                    return '<div>'.$stars.' <span class="small fw-bold ms-1">('.$row->rating.'/5)</span></div>';
                })
                ->addColumn('review_text', function ($row) {
                    $verified = $row->is_verified_purchase ? ' <span class="badge bg-success-subtle text-success border border-success-subtle small"><i class="fa fa-check-circle"></i> Verified</span>' : '';

                    return '<div><strong>'.e($row->title ?? 'Review').'</strong>'.$verified.'<p class="small text-muted mb-0 mt-1">'.e($row->comment).'</p></div>';
                })
                ->addColumn('status_badge', function ($row) {
                    $badge = match ($row->status) {
                        'approved' => 'bg-success',
                        'pending' => 'bg-warning text-dark',
                        'rejected' => 'bg-danger',
                        default => 'bg-secondary'
                    };

                    return '<span class="badge '.$badge.' text-capitalize">'.e($row->status).'</span>';
                })
                ->addColumn('action', function ($row) {
                    $approveBtn = $row->status !== 'approved' ? '<button type="button" class="btn btn-sm btn-success" onclick="toggleReviewStatus('.$row->id.', \'approved\')" title="Approve"><i class="fa fa-check"></i></button>' : '';
                    $rejectBtn = $row->status !== 'rejected' ? '<button type="button" class="btn btn-sm btn-warning text-dark" onclick="toggleReviewStatus('.$row->id.', \'rejected\')" title="Reject"><i class="fa fa-ban"></i></button>' : '';

                    return '<div class="d-flex align-items-center gap-1">
                                '.$approveBtn.'
                                '.$rejectBtn.'
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteReview('.$row->id.')" title="Delete"><i class="fa fa-trash"></i></button>
                            </div>';
                })
                ->rawColumns(['product_info', 'customer', 'rating_stars', 'review_text', 'status_badge', 'action'])
                ->make(true);
        }

        return view('product::backend.reviews.index');
    }

    public function toggleStatus(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:approved,pending,rejected']);

        $review = ProductReview::findOrFail($id);
        $review->status = $request->status;
        $review->save();

        if ($review->product) {
            $review->product->updateRatingStats();
        }

        return response()->json([
            'success' => true,
            'message' => 'Review status updated to '.ucfirst($review->status),
        ]);
    }

    public function destroy(int $id)
    {
        $review = ProductReview::findOrFail($id);
        $product = $review->product;
        $review->delete();

        if ($product) {
            $product->updateRatingStats();
        }

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully.',
        ]);
    }
}
