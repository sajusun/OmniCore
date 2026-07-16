@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'alert alert-success role-alert text-sm py-2 px-3']) }}>
        {{ $status }}
    </div>
@endif
