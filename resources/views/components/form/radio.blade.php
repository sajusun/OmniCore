<div class="mb-3">
    <div class="form-check">
        <input
            type="radio"
            class="form-check-input @error($name) is-invalid @enderror"
            name="{{ $name }}"
            id="{{ $id ?? $name . '_' . $value }}"
            value="{{ $value }}"
            {{ (isset($checked) && $checked) || old($name) == $value ? 'checked' : '' }}
            {{ $attributes }}
        />
        <label class="form-check-label fw-medium" for="{{ $id ?? $name . '_' . $value }}">{!! $label !!}</label>
        @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
