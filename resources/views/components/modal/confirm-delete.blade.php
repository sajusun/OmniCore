@props([
'name',
'action',
'title' => 'Delete Confirmation',
'message' => 'Are you sure you want to delete this item? This action cannot be undone.',
'method' => 'DELETE',
])

{{-- Bootstrap Modal Component without any rounded properties --}}
<div class="modal fade" id="modal_{{ $name }}" tabindex="-1" aria-labelledby="modal_{{ $name }}_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 0;">
            <form method="POST" action="{{ $action }}" id="confirm-delete-form-{{ $name }}">
                @csrf
                @method($method)

                <div class="modal-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        {{-- Icon wrapper with absolute solid layout --}}
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-danger text-white shadow-sm"
                            style="width: 48px; height: 48px; border-radius: 0;">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-1" id="modal_{{ $name }}_label">{{ $title }}</h5>
                            <p class="text-muted small mb-0">{{ $message }}</p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" style="border-radius: 0;">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4" style="border-radius: 0;">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('confirm-delete-form-{{ $name }}');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const url = form.getAttribute('action');
                const token = form.querySelector('input[name="_token"]').value;
                const method = form.querySelector('input[name="_method"]').value;

                $.ajax({
                    url: url,
                    type: method,
                    data: { _token: token },
                    success: function(response) {
                        var modalElement = document.getElementById('modal_{{ $name }}');
                        var modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) modal.hide();

                        if (response.status || response.success) {
                            if ($('.dataTable').length > 0) {
                                $('.dataTable').DataTable().ajax.reload();
                            } else {
                                window.location.reload();
                            }

                            // SweetAlert with custom clean classes to strip out rounded layouts
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message || 'Item deleted successfully.',
                                timer: 2000,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'border-0 rounded-0 shadow'
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Something went wrong.',
                                customClass: {
                                    popup: 'border-0 rounded-0 shadow',
                                    confirmButton: 'btn btn-danger rounded-0 px-4'
                                },
                                buttonsStyling: false
                            });
                        }
                    },
                    error: function(xhr) {
                        var modalElement = document.getElementById('modal_{{ $name }}');
                        var modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) modal.hide();
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while deleting.',
                            customClass: {
                                popup: 'border-0 rounded-0 shadow',
                                confirmButton: 'btn btn-danger rounded-0 px-4'
                            },
                            buttonsStyling: false
                        });
                    }
                });
            });
        }
    });
</script>