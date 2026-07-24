@props([
    'name',
    'label',
    'value' => '',
    'placeholder' => '',
    'labelActions' => null
])

<div class="mb-3">
    <div class="d-flex align-items-center justify-content-between mb-1">
        <label for="{{ $name }}" class="form-label fw-medium mb-0">{!! $label !!}</label>
        @if(isset($labelActions))
            {!! $labelActions !!}
        @endif
    </div>
    <div class="input-group">
        <input
            type="password"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            id="{{ $name }}"
            value="{{ $value }}"
            {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
        />
        <button class="btn btn-outline-primary" type="button" id="toggle_{{ $name }}"
            onclick="
                var inp = document.getElementById('{{ $name }}');
                var icon = this.querySelector('i');
                if (inp.type === 'password') {
                    inp.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    inp.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            ">
            <i class="fa fa-eye"></i>
        </button>
        @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    {{ $slot }}
</div>
