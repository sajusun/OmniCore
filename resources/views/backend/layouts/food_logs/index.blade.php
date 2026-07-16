@extends('backend.app', ['title' => 'Food Logs'])

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Food Logs</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Food Logs</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div
                            class="card-header border-bottom-0 bg-white d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title mb-0">All Food Logs Records</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom"
                                    id="scan-history-datatable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>User</th>
                                            <th>Product</th>
                                            <th>Meal Type</th>
                                            <th>Food Score</th>
                                            <th>Date</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        let table = $('#scan-history-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.food_logs.ajax') }}",
                data: {
                    user: "{{ request('user') }}"
                    }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'user', name: 'user' },
                { data: 'product_name', name: 'product_name' },
                { data: 'meal_type', name: 'meal_type' },
                { data: 'food_score', name: 'food_score' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            order: [[5, 'desc']],
            language: {
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            }
        });

        $(document).on('click', '.delete-scan-history', function () {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This scan history will be deleted permanently.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.food_logs.delete', '') }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.status) {
                                Swal.fire('Deleted!', response.message, 'success');
                                table.ajax.reload();
                            } else {
                                Swal.fire('Error', response.message || 'Something went wrong.', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'Unable to delete this record.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush