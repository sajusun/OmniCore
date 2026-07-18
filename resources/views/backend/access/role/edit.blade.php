@extends('layouts.admin', ['title' => 'Edit Role'])

@section('content')

<div class="container-fluid py-4 px-0">
    <div class="mx-auto" style="max-width: 1280px;">

        {{-- Page Header --}}
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
            <div>
                {{-- Breadcrumb System --}}
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1" style="font-size: 0.875rem;">
                        <li class="breadcrumb-item text-muted">
                            <i class="fa-solid fa-shield-halved me-1"></i> Access Control
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}"
                                class="text-decoration-none text-muted">Roles</a></li>
                        <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Edit</li>
                    </ol>
                </nav>
                <h1 class="h4 fw-bold text-dark mb-1">Edit Role</h1>
                <p class="small text-muted mb-0">
                    Updating: <span class="fw-bold text-primary">{{ $role->name }}</span>
                </p>
            </div>

            {{-- Back Button --}}
            <a href="{{ route('admin.roles.index') }}"
                class="btn btn-sm btn-light border px-3 d-inline-flex align-items-center gap-2 rounded-0"
                style="height: 38px; transition: all 0.2s ease;">
                <i class="fa-solid fa-arrow-left small"></i>
                <span>Back</span>
            </a>
        </div>

        {{-- Main Card Content Container --}}
        <div class="card border border-light-subtle shadow-sm overflow-hidden rounded-0">

            {{-- Card Header with Dynamic Subtle Gradient (Tailwind Alternative Accent) --}}
            <div class="card-header border-bottom border-light-subtle px-4 py-3 bg-light">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-0"
                        style="width: 40px; height: 40px; font-size: 1.15rem;">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h2 class="h6 card-title mb-0 fw-bold text-dark">Role: {{ $role->name }}</h2>
                        <p class="small text-muted mb-0" style="font-size: 0.75rem;">
                            {{ $role->permissions->count() }} permission(s) currently assigned.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form Start --}}
            <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" class="card-body p-4">
                @csrf
                @method('PUT')

                {{-- Role Name Form Group --}}
                <div class="mb-4">
                    <x-form.text name="name" label="Role Name" placeholder="e.g. Manager, Editor, Moderator"
                        :value="old('name', $role->name)" :readonly=true />
                </div>

                {{-- Permissions Label & Native Bootstrap Switch Container --}}
                <div class="mb-4">
                    <div
                        class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2 border-light-subtle">
                        <label class="form-label fw-bold text-dark mb-0 d-inline-flex align-items-center gap-2">
                            <span>Permissions</span>
                            <span class="badge bg-primary text-white font-monospace rounded-0"
                                style="font-size: 0.7rem; padding: 0.35em 0.65em;">
                                {{ $permissions->count() }} total
                            </span>
                        </label>

                        {{-- Standard High Quality Bootstrap Form Switch --}}
                        <div class="d-flex align-items-center">
                            <label class="small text-muted mb-0 me-4" for="selectAll">
                                Select All
                            </label>

                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                            </div>
                        </div>
                    </div>

                    {{-- Permissions Grid grouped by suffix --}}
                    @if($permissions->count())
                    <div class="border border-light-subtle rounded-0 overflow-hidden">
                        @php
                        $grouped = $permissions->groupBy(function($p) {
                        $parts = explode('-', $p->name);
                        return count($parts) > 1 ? ucfirst(end($parts)) : 'General';
                        });
                        $assignedNames = $role->permissions->pluck('name')->toArray();
                        @endphp

                        @foreach($grouped as $group => $perms)
                        <div class="border-bottom border-light-subtle last:border-0">
                            {{-- Permission Group Category Title --}}
                            <div
                                class="px-3 py-2 bg-light border-bottom border-light-subtle d-flex align-items-center gap-2">
                                <span class="fw-bold text-secondary text-uppercase tracking-wider"
                                    style="font-size: 0.725rem;">{{ $group }}</span>
                                <span class="text-muted small" style="font-size: 0.725rem;">({{ $perms->count()
                                    }})</span>
                            </div>

                            {{-- Internal Grid Selection Box Layout --}}
                            <div class="row g-2 p-3">
                                @foreach($perms as $permission)
                                @php
                                $isChecked = in_array($permission->name, old('permissions', $assignedNames));
                                @endphp
                                <div class="col-6 col-sm-4 col-md-3">
                                    <label for="perm-{{ $permission->id }}"
                                        class="d-flex align-items-center gap-2 px-3 py-2 border border-light-subtle cursor-pointer w-100 h-100 transition-all text-break perm-label-box"
                                        style="background-color: {{ $isChecked ? 'var(--bs-light-bg-subtle)' : '#ffffff' }}; font-size: 0.8rem;">

                                        <input
                                            class="form-check-input permission-checkbox m-0 flex-shrink-0 cursor-pointer rounded-0 border-secondary-subtle"
                                            type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                            id="perm-{{ $permission->id }}" {{ $isChecked ? 'checked' : '' }}>
                                        <span class="text-dark-emphasis text-capitalize ms-4">
                                            {{ str_replace('-', ' ', $permission->name) }}
                                        </span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5 border border-dashed border-light-subtle bg-light rounded-0">
                        <p class="small text-muted mb-0">No permissions found. Create permissions first.</p>
                    </div>
                    @endif
                </div>

                {{-- Action Action Bottom Footer Panel --}}
                <div class="pt-3 border-top border-light-subtle d-flex justify-content-end align-items-center gap-2 ">
                    <x-form.submit class="btn btn-primary px-4 rounded-0">
                        <i class="fa-solid fa-check me-1.5 small"></i> Update Role
                    </x-form.submit>
                    <x-form.cancel :href="route('admin.roles.index')" class="btn btn-light border rounded-0">Cancel
                    </x-form.cancel>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.permission-checkbox');

    // Sync individual panel background styling when toggled
    function updateLabelStyle(cb) {
        const parentLabel = cb.closest('.perm-label-box');
        if (parentLabel) {
            parentLabel.style.backgroundColor = cb.checked ? 'var(--bs-light-bg-subtle)' : '#ffffff';
        }
    }

    // Checking global state matching handler
    function checkGlobalState() {
        if (!checkboxes.length) return;
        const allChecked = [...checkboxes].every(c => c.checked);
        if (selectAll) selectAll.checked = allChecked;
    }

    // Initialize layout status mapping runtime
    checkGlobalState();

    selectAll?.addEventListener('change', function () {
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
            updateLabelStyle(cb);
        });
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            updateLabelStyle(this);
            checkGlobalState();
        });
    });
})();
</script>
@endpush