<x-admin-layout>
    <x-slot name="title">Discount Coupons</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Discount Coupons</h2>
    </x-slot>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}" class="text-decoration-none text-muted">Orders</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium">Coupons</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Discount Coupons Management</h4>
        </div>
        <div>
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" onclick="openCreateCouponModal()">
                <i class="fa fa-plus"></i> Create New Coupon
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-header bg-transparent border-0 pt-3 pb-0 px-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="fa fa-ticket me-2 text-primary"></i> Coupons List ({{ \App\Modules\Coupon\Models\Coupon::count() }})
            </h5>
        </div>
        <div class="card-body p-3">
            <x-datatable id="coupons-datatable" :url="route('admin.coupons.index')" :order="[[0, 'desc']]" :columns="[
                ['data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'SL', 'orderable' => false, 'searchable' => false],
                ['data' => 'code', 'name' => 'code', 'title' => 'Promo Code'],
                ['data' => 'discount_display', 'name' => 'value', 'title' => 'Discount Rate'],
                ['data' => 'min_spend', 'name' => 'min_order_amount', 'title' => 'Min Order'],
                ['data' => 'usage', 'name' => 'usage_count', 'title' => 'Used / Limit', 'searchable' => false],
                ['data' => 'validity', 'name' => 'expires_at', 'title' => 'Expiration'],
                ['data' => 'status', 'name' => 'is_active', 'title' => 'Status'],
                ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false]
            ]" />
        </div>
    </div>

    {{-- Coupon Modal --}}
    <div class="modal fade" id="couponModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="couponForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="couponFormMethod" value="POST">
                    <input type="hidden" id="couponId">

                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold" id="couponModalTitle">Create Discount Coupon</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Coupon Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="couponCode" class="form-control font-monospace text-uppercase" required placeholder="e.g. FLASH30, SAVE20">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Discount Type <span class="text-danger">*</span></label>
                                <select name="type" id="couponType" class="form-select" required>
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="fixed">Fixed Amount ($)</option>
                                    <option value="free_shipping">Free Shipping</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Discount Value <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="value" id="couponValue" class="form-control" required placeholder="e.g. 15">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Minimum Spend ($)</label>
                                <input type="number" step="0.01" min="0" name="min_order_amount" id="couponMinOrder" class="form-control" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Max Cap Discount ($)</label>
                                <input type="number" step="0.01" min="0" name="max_discount_amount" id="couponMaxDiscount" class="form-control" placeholder="Optional cap">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Total Usage Limit</label>
                                <input type="number" min="1" name="usage_limit" id="couponUsageLimit" class="form-control" placeholder="Unlimited if empty">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Limit Per Customer</label>
                                <input type="number" min="1" name="usage_limit_per_user" id="couponPerUserLimit" class="form-control" placeholder="e.g. 1">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Start Date</label>
                                <input type="date" name="starts_at" id="couponStartsAt" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Expiry Date</label>
                                <input type="date" name="expires_at" id="couponExpiresAt" class="form-control">
                            </div>
                        </div>

                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="is_active" id="couponIsActive" value="1" checked>
                            <label class="form-check-label fw-bold" for="couponIsActive">Coupon is Active</label>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveCouponBtn">Save Coupon</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const couponModal = new bootstrap.Modal(document.getElementById('couponModal'));

        function openCreateCouponModal() {
            $('#couponForm')[0].reset();
            $('#couponFormMethod').val('POST');
            $('#couponId').val('');
            $('#couponModalTitle').text('Create Discount Coupon');
            $('#couponForm').attr('action', "{{ route('admin.coupons.store') }}");
            $('#couponIsActive').prop('checked', true);
            couponModal.show();
        }

        function openEditCouponModal(data) {
            $('#couponForm')[0].reset();
            $('#couponFormMethod').val('PUT');
            $('#couponId').val(data.id);
            $('#couponModalTitle').text('Edit Coupon: ' + data.code);
            $('#couponForm').attr('action', "{{ url('admin/coupons') }}/" + data.id);
            $('#couponCode').val(data.code);
            $('#couponType').val(data.type);
            $('#couponValue').val(data.value);
            $('#couponMinOrder').val(data.min_order_amount || '');
            $('#couponMaxDiscount').val(data.max_discount_amount || '');
            $('#couponUsageLimit').val(data.usage_limit || '');
            $('#couponPerUserLimit').val(data.usage_limit_per_user || '');
            $('#couponStartsAt').val(data.starts_at || '');
            $('#couponExpiresAt').val(data.expires_at || '');
            $('#couponIsActive').prop('checked', !!data.is_active);
            couponModal.show();
        }

        $('#couponForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    couponModal.hide();
                    Swal.fire('Success!', response.message || 'Saved successfully.', 'success');
                    $('#coupons-datatable').DataTable().ajax.reload(null, false);
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Validation failed.';
                    Swal.fire('Error!', msg, 'error');
                }
            });
        });

        function deleteCoupon(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this discount coupon?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/coupons') }}/" + id,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            Swal.fire('Deleted!', res.message, 'success');
                            $('#coupons-datatable').DataTable().ajax.reload(null, false);
                        },
                        error: function() {
                            Swal.fire('Error!', 'Could not delete coupon.', 'error');
                        }
                    });
                }
            });
        }
    </script>
    @endpush
</x-admin-layout>
