@props(['edit' => null, 'delete' => null, 'show' => null, 'deleteId' => null])

<div class="d-flex align-items-center gap-2">
    @if($show)
        <a href="{{ $show }}" class="btn btn-link btn-sm p-1 text-info" title="View">
            <i class="fas fa-eye fa-lg"></i>
        </a>
    @endif
    
    @if($edit)
        <a href="{{ $edit }}" class="btn btn-link btn-sm p-1 text-primary" title="Edit">
            <i class="fas fa-edit fa-lg"></i>
        </a>
    @endif
    
    @if($delete && $deleteId)
        <button type="button" onclick="deleteResource('{{ $delete }}', '{{ $deleteId }}')" class="btn btn-link btn-sm p-1 text-danger" title="Delete">
            <i class="fas fa-trash fa-lg"></i>
        </button>
    @endif
</div>

@once
    @push('scripts')
    <script>
        function deleteResource(url, id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status || response.success) {
                                Swal.fire('Deleted!', response.message || 'Successfully deleted.', 'success');
                                // Reload any initialized datatables on the page
                                if ($.fn.DataTable && $.fn.DataTable.isDataTable('table')) {
                                    $('table').DataTable().ajax.reload(null, false);
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                Swal.fire('Error!', response.message || 'Something went wrong.', 'error');
                            }
                        }
                    });
                }
            })
        }
    </script>
    @endpush
@endonce
