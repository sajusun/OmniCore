{{--
    Shared Permission form (used by create & edit)
    @param \Spatie\Permission\Models\Permission|null $permission
--}}
@php $permission = $permission ?? null; @endphp

<form action="{{ $permission ? route('admin.permissions.update', $permission->id) : route('admin.permissions.store') }}" method="POST">
    @csrf
    @if($permission) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-md-6">
            <x-form.text name="name" label="Permission Key <span class='text-danger'>*</span>"
                placeholder="e.g. user.create, role.delete" :value="old('name', $permission->name ?? '')">
                <div class="form-text">Lowercase, dot separated: <code>module.action</code></div>
            </x-form.text>
        </div>
        <div class="col-md-6">
            <x-form.text name="display_name" label="Display Name <span class='text-danger'>*</span>"
                placeholder="e.g. Create User, Delete Role" :value="old('display_name', $permission->display_name ?? '')">
                <div class="form-text">Human readable label shown in role assignment.</div>
            </x-form.text>
        </div>
    </div>

    <div class="d-flex justify-content-end align-items-center gap-2 pt-3 mt-2 border-top">
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-light border px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-2">
            <i class="fas fa-check"></i>
            <span>{{ $permission ? 'Update Permission' : 'Save Permission' }}</span>
        </button>
    </div>
</form>
