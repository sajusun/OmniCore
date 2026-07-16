@props([
'name',
'label',
'options' => null,
'value' => null,
'placeholder' => null,
'width' => '',
])

@php
$selected = old($name, $value);
@endphp

<div class="mb-3">
    <label for="{{ $name }}" class="form-label fw-medium">
        {{ $label }}
    </label>

    <select id="{{ $name }}" name="{{ $name }}" {{ $attributes->merge([
        'class' => 'form-select' . ($width ? ' w-' . $width : '') . ($errors->has($name) ? ' is-invalid' : '')
        ]) }}>

        @if($placeholder)
        <option value="">{{ $placeholder }}</option>
        @endif

        @if(isset($options))

        @foreach($options as $key => $option)

        @php
        if (is_object($option)) {
        $optionValue = $option->id;
        $optionLabel = $option->name;
        } else {
        $optionValue = $key;
        $optionLabel = $option;
        }
        @endphp

        <option value="{{ $optionValue }}" @selected($selected==$optionValue)>
            {{ $optionLabel }}
        </option>

        @endforeach

        @else

        {{ $slot }}

        @endif

    </select>

    @error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
</div>
