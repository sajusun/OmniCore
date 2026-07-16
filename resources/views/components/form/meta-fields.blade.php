@props([
'label' => 'Meta Data',
'name' => 'meta',
'data' => null,
])

@php
$value = old($name, data_get($data, $name, []));

if (is_string($value)) {
$value = json_decode($value, true) ?? [];
}

$value = is_array($value) ? $value : [];
@endphp

<div class="json-kv-component mb-3" data-name="{{ $name }}">
    <label class="form-label fw-medium">
        {{ $label }}
        <span class="text-muted small fw-normal ms-1">(JSON Key-Value Pairs)</span>
    </label>

    <div class="json-kv-wrapper d-flex flex-column gap-2">

        @forelse($value as $key => $val)

        <div class="json-kv-row d-flex align-items-center gap-2">

            <input type="text" name="{{ $name }}_keys[]" value="{{ $key }}" placeholder="Key"
                class="json-key form-control form-control-sm">

            <input type="text" name="{{ $name }}_values[]" value="{{ $val }}" placeholder="Value"
                class="json-value form-control form-control-sm">

            <button type="button"
                class="remove-json-row btn btn-outline-danger btn-sm flex-shrink-0"
                style="width: 36px; height: 36px; padding: 0;">
                <i class="bi bi-trash"></i>
            </button>

        </div>

        @empty

        <div class="json-kv-row d-flex align-items-center gap-2">

            <input type="text" name="{{ $name }}_keys[]" placeholder="Key"
                class="json-key form-control form-control-sm">

            <input type="text" name="{{ $name }}_values[]" placeholder="Value"
                class="json-value form-control form-control-sm">

            <button type="button"
                class="remove-json-row btn btn-outline-danger btn-sm flex-shrink-0"
                style="width: 36px; height: 36px; padding: 0;">
                <i class="bi bi-trash"></i>
            </button>

        </div>

        @endforelse

    </div>

    <div class="mt-2 d-flex align-items-center gap-3">

        <button type="button"
            class="add-json-row btn btn-outline-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Row
        </button>

        <span class="text-muted small">
            Saved into <code class="text-primary">{{ $name }}</code>
        </span>

    </div>
</div>

@once
@push('scripts')

<script>
    document.querySelectorAll('.json-kv-component').forEach(component=>{

    const wrapper=component.querySelector('.json-kv-wrapper');
    const addBtn=component.querySelector('.add-json-row');
    const fieldName=component.dataset.name;

    const inputClass='form-control form-control-sm';

    function bindRemove(btn){

        btn.addEventListener('click',function(){

            const row=this.closest('.json-kv-row');

            if(wrapper.querySelectorAll('.json-kv-row').length>1){

                row.remove();

            }else{

                row.querySelectorAll('input').forEach(i=>i.value='');

            }

        });

    }

    wrapper.querySelectorAll('.remove-json-row').forEach(bindRemove);

    addBtn.addEventListener('click',()=>{

        const row=document.createElement('div');

        row.className='json-kv-row d-flex align-items-center gap-2';

        row.innerHTML=`
            <input
                type="text"
                name="${fieldName}_keys[]"
                placeholder="Key"
                class="json-key ${inputClass}">

            <input
                type="text"
                name="${fieldName}_values[]"
                placeholder="Value"
                class="json-value ${inputClass}">

            <button
                type="button"
                class="remove-json-row btn btn-outline-danger btn-sm flex-shrink-0"
                style="width: 36px; height: 36px; padding: 0;">
                <i class="bi bi-trash"></i>
            </button>
        `;

        bindRemove(row.querySelector('.remove-json-row'));

        wrapper.appendChild(row);

    });

    component.closest('form').addEventListener('submit',function(){

        const obj={};

        wrapper.querySelectorAll('.json-kv-row').forEach(row=>{

            const key=row.querySelector('.json-key').value.trim();

            const value=row.querySelector('.json-value').value.trim();

            if(key){

                obj[key]=value;

            }

        });

        let hidden=this.querySelector(`input[name="${fieldName}"]`);

        if(!hidden){

            hidden=document.createElement('input');

            hidden.type='hidden';

            hidden.name=fieldName;

            this.appendChild(hidden);

        }

        hidden.value=JSON.stringify(obj);

        wrapper.querySelectorAll('input').forEach(i=>i.disabled=true);

    });

});

</script>

@endpush
@endonce
