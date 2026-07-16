<div>
    <div class="mb-3">
        <label for="{{ $name }}" class="form-label fw-medium">{{ $label }}:</label>
        <input type="color" class="form-control form-control-color @error($name) is-invalid @enderror"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            id="{{ $name }}"
            value="{{ $value }}" />
        {{ $slot }}
        @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
