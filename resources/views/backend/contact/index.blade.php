<x-admin-layout title="Contact Us Messages">

    @push('styles')
    <link href="{{ asset('default/datatable.css') }}" rel="stylesheet" />
    <style>
        .user-agent-col {
            max-width: 220px;
            word-break: break-all;
            font-size: 11px;
            line-height: 1.3;
        }

        code {
            background: #f1f3f5;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
        }
    </style>
    @endpush

    @section('content')
    <div class="app-content main-content mt-0">
        <div class="side-app">
            <div class="main-container container-fluid">

                <!-- PAGE HEADER -->
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Contact Us Messages</h1>
                        <small class="text-muted">Total: {{ \App\Models\ContactUs::count() }} messages
                            ({{ \App\Models\ContactUs::where('is_read', false)->count() }} unread)</small>
                    </div>
                    <div class="ms-auto pageheader-btn">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Contact</a></li>
                            <li class="breadcrumb-item active">Messages</li>
                        </ol>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h3 class="card-title mb-0">All Messages</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table text-nowrap mb-0 table-bordered" id="datatable">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="18%">Subject</th>
                                                <th width="20%">Email</th>
                                                <th width="15%">Phone</th>
                                                <th width="12%">Status</th>
                                                <th width="15%">Sent At</th>
                                                <th width="15%">Action</th>
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

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Message Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Name:</strong> <span id="modal-name"></span></p>
                    <p><strong>Email:</strong> <span id="modal-email"></span></p>

                    <p><strong>Phone:</strong> <span id="modal-phone"></span></p>
                    <p><strong>Sent At:</strong> <span id="modal-sent_at"></span></p>
                    <p><strong>Subject:</strong> <span id="modal-subject"></span></p>
                    <hr>
                    <p><strong>Message:</strong></p>
                    <div id="modal-message" class="border p-3 
                <div class=" modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
            $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('contact.me') }}",
                order: [
                    [5, 'desc']
                ], // latest first
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'subject',
                        name: 'subject'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        render: data => data ?? 'N/A'
                    },
                    {
                        data: 'is_read',
                        name: 'is_read'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    processing: '<div class="spinner-border text-primary"></div>'
                }
            });

            // View Button
            // $(document).on('click', '.view-btn', function() {
            //     const id = $(this).data('id');
            //     const url = "{{ route('contact.me.show', ':id') }}".replace(':id', id);

            //     $.get(url, function(res) {
            //         if (res.success) {
            //             $('#modal-name').text(res.data.name);
            //             $('#modal-email').text(res.data.email);
            //             $('#modal-phone').text(res.data.phone);
            //             $('#modal-sent_at').text(res.data.sent_at);
            //             $('#modal-email').text(res.data.email);
            //             $('#modal-subject').text(res.data.subject);
            //             $('#modal-message').html(res.data.message);
            //             $('#viewModal').modal('show');

            //             // Reload table silently to update "Unread" badge without resetting pagination
            //             table.ajax.reload(null, false);
            //         }
            //     }).fail(function(xhr) {
            //         console.error(xhr);
            //         toastr.error('Failed to load message details');
            //     });
            // });

            // Delete Button
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                const deleteUrl = "{{ route('contact.me.delete', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: 'Delete this message?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete!',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: res.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });

                                    // Reload DataTable without page reset
                                    table.ajax.reload(null, false);
                                } else {
                                    toastr.error(res.message || 'Failed to delete');
                                }
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to delete message');
                            }
                        });
                    }
                });
            });
        });
        </script>
        @endpush
</x-admin-layout>