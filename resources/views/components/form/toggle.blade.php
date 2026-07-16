<div class="mb-3">
    <div class="d-flex align-items-center justify-content-between">
        <label for="{{ $name }}" class="form-label fw-medium mb-0">{!! $label !!}</label>
        <div class="form-check form-switch mb-0">
            <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                id="{{ $name }}_toggle"
                {{ (isset($checked) && $checked) || old($name, $value ?? '') ? 'checked' : '' }}
                onchange="document.getElementById('{{ $name }}').value = this.checked ? '1' : '0'"
                style="width: 2.5rem; height: 1.25rem; cursor: pointer;"
            />
        </div>
    </div>
    <input type="hidden" name="{{ $name }}" id="{{ $name }}"
        value="{{ ((isset($checked) && $checked) || old($name, $value ?? '')) ? '1' : '0' }}" />
    {{ $slot }}
    @error($name)
    <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>
