@props([
'name',
'action',
'title' => 'Delete Confirmation',
'message' => 'Are you sure you want to delete this item? This action cannot be undone.',
'method' => 'DELETE',
])

{{-- Bootstrap Modal --}}
<div class="modal fade" id="modal_{{ $name }}" tabindex="-1" aria-labelledby="modal_{{ $name }}_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ $action }}" id="confirm-delete-form-{{ $name }}">
                @csrf
                @method($method)

                <div class="modal-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 text-danger"
                            style="width: 48px; height: 48px;">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-semibold mb-1" id="modal_{{ $name }}_label">{{ $title }}</h5>
                            <p class="text-muted small mb-0">{{ $message }}</p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
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
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modal_{{ $name }}'));
                        if (modal) modal.hide();

                        if (response.status || response.success) {
                            if ($('.dataTable').length > 0) {
                                $('.dataTable').DataTable().ajax.reload();
                            } else {
                                window.location.reload();
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message || 'Item deleted successfully.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Something went wrong.'
                            });
                        }
                    },
                    error: function(xhr) {
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modal_{{ $name }}'));
                        if (modal) modal.hide();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while deleting.'
                        });
                    }
                });
            });
        }
    });
</script>
