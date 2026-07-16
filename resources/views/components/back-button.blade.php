<a href="{{ $href ?? (url()->previous() == url()->current() ? route('admin.activity-logs.index') : url()->previous()) }}"
   {{ $attributes->merge(['class' => 'btn btn-outline-secondary btn-sm d-inline-flex align-items-center']) }}>
    <i class="fas fa-arrow-left me-2"></i> {{ $slot ?? 'Back' }}
</a>
