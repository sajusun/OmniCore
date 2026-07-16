<div class="mb-3">
    <div class="form-check">
        <input
            type="checkbox"
            class="form-check-input @error($name) is-invalid @enderror"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $value ?? '1' }}"
            {{ (isset($checked) && $checked) || old($name, $value ?? '') ? 'checked' : '' }}
            {{ $attributes }}
        />
        <label class="form-check-label fw-medium" for="{{ $name }}">{!! $label !!}</label>
        {{ $slot }}
        @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
