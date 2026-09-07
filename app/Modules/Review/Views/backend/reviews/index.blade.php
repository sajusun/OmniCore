<x-admin-layout>
    @slot('title')
        Reviews & Ratings
    @endslot

    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row align-items-center mb-4">
            <div class="col">
                <h4 class="fw-bold mb-1">
                    <i class="fa fa-star text-warning me-2"></i> Reviews & Ratings Management
                </h4>
                <p class="text-muted small mb-0">Moderate user reviews, ratings, verified buyer feedback, and seller replies.</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning-subtle text-warning p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fa fa-star fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Average Rating</div>
                            <div class="fs-4 fw-bold text-dark">{{ $stats['avg_star'] }} / 5.0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary-subtle text-primary p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fa fa-comments fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Total Reviews</div>
                            <div class="fs-4 fw-bold text-primary">{{ number_format($stats['total']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="bg-success-subtle text-success p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fa fa-check-circle fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Approved</div>
                            <div class="fs-4 fw-bold text-success">{{ number_format($stats['approved']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger-subtle text-danger p-3 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fa fa-flag fa-lg"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">Flagged / Pending</div>
                            <div class="fs-4 fw-bold text-danger">{{ number_format($stats['flagged'] + $stats['pending']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Search reviewer, item, or comment content..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            @foreach(\App\Modules\Review\Enums\ReviewStatus::cases() as $st)
                                <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="rating" class="form-select">
                            <option value="">All Star Ratings</option>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Stars ({{ str_repeat('★', $i) }})</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-filter me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'status', 'rating']))
                            <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Reviews Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Item & Reviewer</th>
                                <th>Rating</th>
                                <th>Review Content</th>
                                <th>Media</th>
                                <th>Status</th>
                                <th>Vendor Reply</th>
                                <th class="pe-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $review)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">
                                            {{ class_basename($review->reviewable_type) }}: 
                                            <span class="text-primary">{{ $review->reviewable->name ?? $review->reviewable->title ?? ('#' . $review->reviewable_id) }}</span>
                                        </div>
                                        <div class="text-muted small">
                                            By <span class="fw-semibold text-dark">{{ $review->user->name ?? 'Deleted User' }}</span>
                                            @if($review->is_verified_buyer)
                                                <span class="badge bg-success-subtle text-success ms-1"><i class="fa fa-check-circle"></i> Verified Buyer</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-warning fw-bold fs-6">
                                            {{ str_repeat('★', $review->rating) }}<span class="text-muted opacity-25">{{ str_repeat('★', 5 - $review->rating) }}</span>
                                        </div>
                                        <div class="text-muted small">{{ $review->rating }}.0 / 5.0</div>
                                    </td>
                                    <td>
                                        @if($review->title)
                                            <div class="fw-bold text-dark small mb-1">{{ $review->title }}</div>
                                        @endif
                                        <div class="text-muted small" style="max-width: 320px;">
                                            {{ Str::limit($review->comment, 120) }}
                                        </div>
                                        <div class="text-muted small mt-1">
                                            <i class="fa fa-thumbs-up me-1 text-success"></i>{{ $review->helpful_count }}
                                            <i class="fa fa-thumbs-down ms-2 me-1 text-danger"></i>{{ $review->unhelpful_count }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($review->media->isNotEmpty())
                                            <div class="d-flex gap-1">
                                                @foreach($review->media->take(3) as $m)
                                                    <a href="{{ $m->url }}" target="_blank">
                                                        <img src="{{ $m->url }}" class="border" style="width: 40px; height: 40px; object-fit: cover;">
                                                    </a>
                                                @endforeach
                                                @if($review->media->count() > 3)
                                                    <span class="badge bg-secondary-subtle text-secondary align-self-center">+{{ $review->media->count() - 3 }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $review->status->badgeClass() }}">
                                            {{ $review->status->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($review->vendor_reply)
                                            <div class="text-success small fw-semibold">
                                                <i class="fa fa-reply me-1"></i> Replied
                                            </div>
                                            <div class="text-muted small" style="max-width: 200px;">
                                                {{ Str::limit($review->vendor_reply, 40) }}
                                            </div>
                                        @else
                                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}">
                                                <i class="fa fa-reply me-1"></i> Reply
                                            </button>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="dropdown d-inline">
                                            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                <li>
                                                    <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="dropdown-item text-success"><i class="fa fa-check me-2"></i> Approve</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="dropdown-item text-warning"><i class="fa fa-ban me-2"></i> Reject</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="flagged">
                                                        <button type="submit" class="dropdown-item text-danger"><i class="fa fa-flag me-2"></i> Flag</button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review permanently?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger"><i class="fa fa-trash me-2"></i> Delete</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Reply Modal -->
                                <div class="modal fade" id="replyModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Reply to Review</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.reviews.reply', $review) }}">
                                                @csrf
                                                <div class="modal-body">
                                                    <p class="text-muted small mb-2"><strong>Customer Review:</strong> "{{ $review->comment }}"</p>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Official Reply Message</label>
                                                        <textarea name="vendor_reply" class="form-control" rows="4" required placeholder="Type official response from store or team..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Post Reply</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa fa-star fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                        No reviews found matching your search.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($reviews->hasPages())
                    <div class="p-3 border-top">
                        {{ $reviews->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
