<div id="global-loader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white dark:bg-gray-900">
    <div class="flex flex-col items-center gap-4">
        <img src="{{ asset('default/loader.gif') }}" alt="Loading..." class="w-20 h-20 object-contain">

        <p class="text-sm text-gray-500 dark:text-gray-400 animate-pulse">
            Loading...
        </p>
    </div>
</div>
@push('scripts')
<script>
    window.addEventListener('load', function () {
        const loader = document.getElementById('global-loader');

        loader.classList.add(
            'opacity-0',
            'transition-opacity',
            'duration-300'
        );

        setTimeout(() => {
            loader.remove();
        }, 300);
    });
</script>
@endpush