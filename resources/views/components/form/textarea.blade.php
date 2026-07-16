<div>
    <div class="form-group">
        <label for="{{ $name }}" class="form-label">{{ $label }}:</label>
        <textarea class="summernote form-control @error($name) is-invalid @enderror" name="{{ $name }}" id="{{ $name }}" placeholder="{{ $placeholder }}" rows="3">{{ $value }}</textarea>
        {{ $slot }}
        @error($name)
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>