@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label font-weight-medium small']) }}>
    {{ $value ?? $slot }}
</label>
