@props([
    'name',
    'label',
    'value' => '',
    'placeholder' => ''
])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label fw-medium">{!! $label !!}</label>
    <input
        type="text"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        id="{{ $name }}"
        value="{{ $value }}"
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
    />
    {{ $slot }}
    @error($name)
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
