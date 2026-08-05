@props([
    'name',
    'label',
    'value' => '',
    'placeholder' => '',
    'readonly' => false,
    'type' => 'text'
])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label fw-medium">{!! $label !!}</label>
    <input
        type={{ $type }}
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        id="{{ $name }}"
        value="{{ $value }}"
        @readonly($readonly)
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
    />
    {{ $slot }}
    @error($name)
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
