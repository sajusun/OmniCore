@props([
'name' => 'status-modal',
'type' => null,
'title' => null,
'message' => null,
'show' => false
])

@php
$statusType = $type ?? (session('success') ? 'success' : (session('error') ? 'error' : null));
$statusMessage = $message ?? (session('success') ?? session('error'));
$statusTitle = $title ?? ($statusType === 'success' ? 'Success!' : 'Error Occurred');
$shouldShow = $show || !empty($statusMessage);
@endphp

@if($statusType && $statusMessage)
<div class="modal fade" id="modal_{{ $name }}" tabindex="-1" aria-labelledby="modal_{{ $name }}_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body p-4 text-center">

                @if($statusType === 'success')
                <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success mb-3"
                    style="width: 56px; height: 56px;">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>
                @else
                <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 text-danger mb-3"
                    style="width: 56px; height: 56px;">
                    <i class="bi bi-x-circle-fill fs-4"></i>
                </div>
                @endif

                <h5 class="fw-semibold mb-2" id="modal_{{ $name }}_label">{{ $statusTitle }}</h5>
                <p class="text-muted mb-4">{{ $statusMessage }}</p>

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Dismiss</button>
            </div>
        </div>
    </div>
</div>

@if($shouldShow)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('modal_{{ $name }}');
        if (el) {
            var modal = new bootstrap.Modal(el);
            modal.show();
        }
    });
</script>
@endif
@endif
