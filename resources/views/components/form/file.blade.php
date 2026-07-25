@props([
    'name',
    'label' => '',
    'file' => null,
    'multiple' => false
])

@php
    $existingFile = is_string($file) ? $file : null;
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label fw-medium">{!! $label !!}</label>
    @endif

    <div class="border border-2 border-dashed p-4 text-center position-relative" id="drop_zone_{{ $name }}"
        style="cursor: pointer; min-height: 120px;" onclick="document.getElementById('{{ $name }}').click()"
        ondragover="event.preventDefault(); this.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');"
        ondragleave="this.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');" ondrop="
            event.preventDefault();
            this.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
            var dt = event.dataTransfer;
            document.getElementById('{{ $name }}').files = dt.files;
            document.getElementById('{{ $name }}').dispatchEvent(new Event('change'));
        ">

        <div id="preview_wrapper_{{ $name }}" class="mb-2" style="{{ $existingFile ? 'display:block;' : 'display:none;' }}">
            <img id="preview_img_{{ $name }}" src="{{ $existingFile ?? '' }}" class="img-fluid"
                style="max-height: 160px; object-fit: contain;" />
        </div>

        <div id="upload_hint_{{ $name }}" style="{{ $existingFile ? 'display:none;' : 'display:block;' }}">
            <i class="bi bi-cloud-arrow-up fs-2 text-secondary"></i>
            <p class="mb-1 text-secondary small">
                <span class="text-primary fw-semibold">Upload a file</span> or drag and drop
            </p>
            <p class="text-muted" style="font-size: 0.75rem;">PNG, JPG, GIF up to 5MB</p>
        </div>
    </div>

    <input type="file" class="d-none" name="{{ $name }}" id="{{ $name }}" {{ $multiple ? 'multiple' : '' }} onchange="
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview_img_{{ $name }}').src = e.target.result;
                    document.getElementById('preview_wrapper_{{ $name }}').style.display = 'block';
                    document.getElementById('upload_hint_{{ $name }}').style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        " />

    {{ $slot }}
    @error($name)
    <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>