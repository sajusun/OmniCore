<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {!! $label !!}
    </label>

    <div id="quill_editor_{{ $name }}"></div>

    <input type="hidden" id="{{ $name }}" name="{{ $name }}" value="{{ old($name, $value ?? '') }}">

    {{ $slot }}

    @error($name)
    <div class="text-danger small mt-1">
        {{ $message }}
    </div>
    @enderror
</div>

@once

@push('styles')

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<style>
    .ql-toolbar.ql-snow {
        border: 1px solid #ced4da;
        border-radius: .375rem .375rem 0 0;
        background: #fff;
    }

    .ql-container.ql-snow {
        border: 1px solid #ced4da;
        border-top: 0;
        border-radius: 0 0 .375rem .375rem;
        min-height: 220px;
    }

    .ql-editor {
        min-height: 200px;
        font-size: 15px;
        line-height: 1.7;
    }

    .ql-toolbar .ql-formats {
        margin-right: 8px;
    }

    .ql-toolbar button {
        width: 28px !important;
        height: 28px !important;
        padding: 3px !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
    }

    .ql-toolbar button svg {
        width: 18px;
        height: 18px;
    }

    .ql-picker {
        height: 28px !important;
    }

    .ql-picker-label {
        display: flex !important;
        align-items: center;
        height: 28px !important;
    }

    .ql-picker-options {
        z-index: 99999;
    }

    .ql-container * {
        box-sizing: border-box;
    }

    .ql-editor img {
        max-width: 100%;
        height: auto;
    }
</style>

@endpush

@push('scripts')

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', function () {

    const editor = document.querySelector('#quill_editor_{{ $name }}');

    if (!editor) return;

    const hidden = document.getElementById('{{ $name }}');

    const quill = new Quill(editor, {

        theme: 'snow',

        placeholder: "{{ $placeholder ?? 'Enter Content' }}",

        modules: {

            toolbar: [

                [{ header: [1,2,3,false] }],

                ['bold','italic','underline','strike'],

                ['blockquote','code-block'],

                [{ list:'ordered' },{ list:'bullet' }],

                [{ color:[] },{ background:[] }],

                ['link','image'],

                ['clean']

            ]

        }

    });

  if (hidden.value) {
    quill.clipboard.dangerouslyPasteHTML(0, hidden.value);
}

    hidden.value = quill.root.innerHTML;

    quill.on('text-change', function () {

        hidden.value = quill.root.innerHTML;

    });

    document.addEventListener('shown.bs.modal', function () {

        quill.resize;
        quill.update();

    });

});

</script>

@endpush
@endonce