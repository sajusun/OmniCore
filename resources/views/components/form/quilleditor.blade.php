<div class="mb-3">
    <label for="{{ $name }}" class="form-label fw-medium">{!! $label !!}</label>
    <div class="border rounded @error($name) border-danger @enderror" style="background: #fff;">
        <div id="quill_editor_{{ $name }}" style="min-height: 200px;"></div>
    </div>
    <input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value ?? '') }}">

    {{ $slot }}
    @error($name)
    <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

@once
@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
@endpush
@endonce

@push('scripts')
<script>
(function () {
    var quill = new Quill('#quill_editor_{{ $name }}', {
        theme: 'snow',
        placeholder: '{{ $placeholder ?? "" }}',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'color': [] }, { 'background': [] }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    var existingContent = document.getElementById('{{ $name }}').value;
    if (existingContent) {
        quill.root.innerHTML = existingContent;
    }

    quill.on('text-change', function () {
        document.getElementById('{{ $name }}').value = quill.root.innerHTML;
    });
})();
</script>
@endpush
