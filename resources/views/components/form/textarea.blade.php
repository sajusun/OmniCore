@props([
'name',
'label',
'value' => '',
'placeholder' => '',
'rows' => 3,
'required' => false,
])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label fw-medium">
        {{ $label }}

        @if($required)
        <span class="text-danger">*</span>
        @endif
    </label>

    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}"
        @required($required) {{ $attributes->merge([
            'class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')
        ]) }}
    >{{ old($name, $value) }}</textarea>

    {{ $slot }}

    @error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>
