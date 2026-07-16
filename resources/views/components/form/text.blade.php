<div>
    <div class="form-group">
        <label for="{{ $name }}" class="form-label">{{ $label }}:</label>
        <input type="text" class="form-control @error($name) is-invalid @enderror" name="{{ $name }}" placeholder="{{ $placeholder }}" id="{{ $name }}" value="{{ $value }}" />
        {{ $slot }}
        @error($name)
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>