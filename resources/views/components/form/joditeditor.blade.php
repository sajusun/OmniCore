@props([
'name',
'label',
'value' => '',
'placeholder' => 'Start writing here...',
'height' => 400,
'required' => false,
])

@once
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jodit@3.24.6/build/jodit.min.css" />
<style>
    .jodit-wysiwyg ul,
    .jodit-container .jodit-wysiwyg ul {
        list-style-type: disc !important;
        padding-left: 2em !important;
        margin: 0.5em 0 !important;
    }

    .jodit-wysiwyg ol,
    .jodit-container .jodit-wysiwyg ol {
        list-style-type: decimal !important;
        padding-left: 2em !important;
        margin: 0.5em 0 !important;
    }

    .jodit-wysiwyg ul ul {
        list-style-type: circle !important;
    }

    .jodit-wysiwyg ul ul ul {
        list-style-type: square !important;
    }

    .jodit-wysiwyg li {
        display: list-item !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jodit@3.24.6/build/jodit.min.js"></script>
@endpush
@endonce

<div class="mb-3">
    <label for="jodit_{{ $name }}" class="form-label fw-medium">
        {!! $label !!}
        @if($required)
        <span class="text-danger">*</span>
        @endif
    </label>

    <textarea id="jodit_{{ $name }}" name="{{ $name }}" placeholder="{{ $placeholder }}" @required($required)
        class="form-control @error($name) is-invalid @enderror">{{ old($name, $value) }}</textarea>

    {{ $slot }}

    @error($name)
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@push('scripts')
<script>
    (function () {
    var id = 'jodit_{{ $name }}';
    var el = document.getElementById(id);

    if (!el || el.dataset.joditDone) return;
    el.dataset.joditDone = '1';

    Jodit.make('#' + id, {
        height       : {{ $height }},
        language     : 'en',
        placeholder  : '{{ addslashes($placeholder) }}',
        buttons: [
            'source', '|',
            'bold', 'italic', 'underline', 'strikethrough', '|',
            'superscript', 'subscript', '|',
            'eraser', '|',
            'ul', 'ol', '|',
            'outdent', 'indent', '|',
            'font', 'fontsize', 'brush', 'paragraph', '|',
            'image', 'table', 'link', '|',
            'align', '|',
            'undo', 'redo', '|',
            'hr', '|',
            'fullsize', 'preview', 'print',
        ],
        style: {
            'ul': 'list-style-type: disc; padding-left: 2em; margin: 0.5em 0;',
            'ol': 'list-style-type: decimal; padding-left: 2em; margin: 0.5em 0;',
            'li': 'display: list-item;',
        },
        uploader             : { insertImageAsBase64URI: true },
        showCharsCounter     : true,
        showWordsCounter     : true,
        showXPathInStatusbar : false,
        toolbarAdaptive      : true,
        toolbarSticky        : false,
        allowResizeX         : false,
        allowResizeY         : true,
    });
})();
</script>
@endpush
