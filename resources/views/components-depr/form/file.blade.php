<div>
    <div class="form-group">
        <label for="{{ $name }}" class="form-label">{{ $label }}:</label>
        <input
            multiple="{{ isset($multiple) && $multiple ? 'multiple' : '' }}"
            type="file"
            class="dropify @error($name) is-invalid @enderror"
            name="{{ $name }}" id="{{ $name }}"
            data-default-file="{{ $file && file_exists(public_path($file)) ? asset($file) : asset('default/logo.png') }}" />
        {{ $slot }}
        @error($name)
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>
