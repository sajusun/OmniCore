<x-admin-layout>
    <x-slot name="title">Reviews Moderation</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reviews Moderation</h2>
    </x-slot>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none text-muted">Products</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium">Reviews</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Customer Reviews & Ratings</h4>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-header bg-transparent border-0 pt-3 pb-0 px-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="fa fa-star-half-stroke me-2 text-warning"></i> Customer Feedback Queue ({{ \App\Modules\Product\Models\ProductReview::count() }})
            </h5>
        </div>
        <div class="card-body p-3">
            <x-datatable id="reviews-datatable" :url="route('admin.reviews.index')" :order="[[0, 'desc']]" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                ['data' => 'product_info', 'name' => 'product.name', 'title' => 'Product'],
                ['data' => 'customer', 'name' => 'user.name', 'title' => 'Customer'],
                ['data' => 'rating_stars', 'name' => 'rating', 'title' => 'Rating'],
                ['data' => 'review_text', 'name' => 'comment', 'title' => 'Feedback Review'],
                ['data' => 'status_badge', 'name' => 'status', 'title' => 'Status'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
            ]" />
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleReviewStatus(reviewId, newStatus) {
            $.ajax({
                url: "{{ url('admin/reviews') }}/" + reviewId + "/toggle-status",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },
                success: function(response) {
                    Swal.fire('Updated!', response.message, 'success');
                    $('#reviews-datatable').DataTable().ajax.reload(null, false);
                },
                error: function() {
                    Swal.fire('Error!', 'Failed to update review status.', 'error');
                }
            });
        }

        function deleteReview(reviewId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This customer review will be deleted permanently.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/reviews') }}/" + reviewId,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            Swal.fire('Deleted!', res.message, 'success');
                            $('#reviews-datatable').DataTable().ajax.reload(null, false);
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-admin-layout>
