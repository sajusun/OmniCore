<?php

declare(strict_types=1);

namespace App\Modules\Review\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Review\Enums\ReviewStatus;
use App\Modules\Review\Models\Review;
use App\Modules\Review\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReviewAdminController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}

    public function index(Request $request): View
    {
        $query = Review::with(['user', 'reviewable', 'media'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $reviews = $query->paginate(20)->withQueryString();

        $stats = [
            'total'    => Review::count(),
            'approved' => Review::where('status', ReviewStatus::APPROVED->value)->count(),
            'pending'  => Review::where('status', ReviewStatus::PENDING->value)->count(),
            'flagged'  => Review::where('status', ReviewStatus::FLAGGED->value)->count(),
            'avg_star' => round((float) Review::where('status', ReviewStatus::APPROVED->value)->avg('rating') ?: 0.0, 1),
        ];

        return view('review::backend.reviews.index', compact('reviews', 'stats'));
    }

    public function moderate(Request $request, Review $review): RedirectResponse
    {
        $request->validate([
            'status' => ['required', Rule::enum(ReviewStatus::class)],
        ]);

        $this->reviewService->moderateReview($review, ReviewStatus::from($request->status));

        return back()->with('success', 'Review status updated successfully.');
    }

    public function reply(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_reply' => 'required|string',
        ]);

        $this->reviewService->replyToReview($review, $validated['vendor_reply']);

        return back()->with('success', 'Reply posted successfully.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();
        return back()->with('success', 'Review removed successfully.');
    }
}
