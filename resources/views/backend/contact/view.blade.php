<x-admin-layout title="Contact Message Details">
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📩 Contact Message Details</h5>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Name:</strong>
                        <p class="mb-0">{{ $contactUs->name }}</p>
                    </div>

                    <div class="col-md-6">
                        <strong>Email:</strong>
                        <p class="mb-0">{{ $contactUs->email }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Phone:</strong>
                        <p class="mb-0">{{ $contactUs->phone ?? 'N/A' }}</p>
                    </div>

                    <div class="col-md-6">
                        <strong>Subject:</strong>
                        <p class="mb-0">{{ $contactUs->subject }}</p>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <strong>Message:</strong>
                    <div class="border p-3 bg-light">
                        {!! nl2br(e($contactUs->message)) !!}
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Sent At:</strong>
                        <p class="mb-0">
                            {{ $contactUs->created_at?->format('d M, Y h:i A') ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="col-md-6">
                        <strong>Read At:</strong>
                        <p class="mb-0">
                            {{ $contactUs->read_at?->format('d M, Y h:i A') ?? 'Not read yet' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="card-footer text-end">
                <a href="{{ route('contact.me') }}" class="btn btn-secondary">
                    ⬅ Back to List
                </a>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#replyModal">
                    ✉️ Reply via Email
                </button>

            </div>
        </div>
    </div>


    <!-- Reply Email Modal -->
    <div class="modal fade" id="replyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('contact-us.reply', $contactUs->id) }}" method="POST">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reply to {{ $contactUs->email }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">To</label>
                            <input type="email" class="form-control" value="{{ $contactUs->email }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" value="Re: {{ $contactUs->subject }}"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" rows="6" class="form-control" required>
Hello {{ $contactUs->name }},

                        </textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            📤 Send Email
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
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
            $(document).on('click', '.view-btn', function() {
                const id = $(this).data('id');
                const url = "{{ route('contact.me.show', ':id') }}".replace(':id', id);

                $.get(url, function(res) {
                    if (res.success) {
                        $('#modal-name').text(res.data.name);
                        $('#modal-email').text(res.data.email);
                        $('#modal-phone').text(res.data.phone);
                        $('#modal-sent_at').text(res.data.sent_at);
                        $('#modal-email').text(res.data.email);
                        $('#modal-subject').text(res.data.subject);
                        $('#modal-message').html(res.data.message);
                        $('#viewModal').modal('show');

                        // Reload table silently to update "Unread" badge without resetting pagination
                        table.ajax.reload(null, false);
                    }
                }).fail(function(xhr) {
                    console.error(xhr);
                    toastr.error('Failed to load message details');
                });
            });

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