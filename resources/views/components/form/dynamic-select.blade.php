@props([
'id' => uniqid('dynamic-select-'),
'name',
'label' => null,
'options' => collect(),
'valueKey' => 'id',
'labelKey' => 'name',
'selected' => null,
'placeholder' => 'Select Option',
'createRoute' => null,
'allowCreate' => true,
'otherLabel' => 'Other',
'required' => false,
])

@php
$selected = old($name, $selected);
@endphp

<div class="dynamic-select-wrapper mb-4" data-id="{{ $id }}" data-create-route="{{ $createRoute }}"
    data-value-key="{{ $valueKey }}" data-label-key="{{ $labelKey }}">

    @if($label)
    <label for="{{ $id }}" class="form-label">
        {{ $label }}

        @if($required)
        <span class="text-danger">*</span>
        @endif
    </label>
    @endif

    <select id="{{ $id }}" name="{{ $name }}" class="form-select dynamic-select">
        <option value="">
            {{ $placeholder }}
        </option>

        @foreach($options as $item)

        <option value="{{ data_get($item,$valueKey) }}" @selected($selected==data_get($item,$valueKey))>
            {{ data_get($item,$labelKey) }}
        </option>

        @endforeach

        @if($allowCreate)
        <option value="__other__">
            + {{ $otherLabel }}
        </option>
        @endif

    </select>

    @if($allowCreate)

    <div class="dynamic-select-create mt-2 d-none">

        <div class="input-group">

            <input type="text" class="form-control dynamic-select-input"
                placeholder="Enter new {{ strtolower($label ?? 'item') }}">

            <button type="button" class="btn btn-primary dynamic-select-save">
                Save
            </button>

            <button type="button" class="btn btn-secondary dynamic-select-cancel">
                Cancel
            </button>

        </div>

        <small class="text-danger dynamic-select-error d-none"></small>

    </div>

    @endif

    @error($name)
    <small class="text-danger">
        {{ $message }}
    </small>
    @enderror

</div>

@pushOnce('scripts')
@vite('resources/js/components/dynamic-select.js')
@endPushOnce
