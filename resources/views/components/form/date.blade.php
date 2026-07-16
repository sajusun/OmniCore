<div class="mb-3">
    <label for="{{ $name }}" class="form-label fw-medium">{!! $label !!}</label>
    <input
        type="date"
        class="form-control @error($name) is-invalid @enderror"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value ?? '' }}"
        {{ $attributes }}
    />
    {{ $slot }}
    @error($name)
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
