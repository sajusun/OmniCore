<div id="global-loader" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white"
    style="z-index: 9999;">
    <div class="d-flex flex-column align-items-center gap-3">
        <img src="{{ asset('default/loader.gif') }}" alt="Loading..." style="width: 80px; height: 80px; object-fit: contain;">
        <p class="text-muted small mb-0">Loading...</p>
    </div>
</div>

@push('scripts')
<script>
    window.addEventListener('load', function () {
        const loader = document.getElementById('global-loader');
        loader.style.transition = 'opacity 0.3s ease';
        loader.style.opacity = '0';
        setTimeout(() => {
            loader.remove();
        }, 300);
    });
</script>
@endpush