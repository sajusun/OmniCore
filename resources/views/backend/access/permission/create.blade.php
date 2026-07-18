@extends('layouts.admin', ['title' => 'Create Permission'])

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                <li class="breadcrumb-item text-muted flex-row align-items-center d-inline-flex gap-1">
                    <svg class="text-secondary" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Access Control
                </li>
                <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}"
                        class="text-decoration-none text-muted">Permissions</a></li>
                <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Create</li>
            </ol>
        </nav>
        <h1 class="h3 mb-1 fw-bold text-dark">Create New Permission</h1>
        <p class="small text-muted mb-0">Define an individual permission key (e.g. role-create).</p>
    </div>
    <a href="{{ route('admin.permissions.index') }}"
        class="btn btn-light border d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm"
        style="border-radius: 0.5rem;">
        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back
    </a>
</div>

{{-- Main Card --}}
<div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 1rem;">

    {{-- Card Header with subtle gradient bg --}}
    <div class="card-header border-0 px-4 py-3" style="background: linear-gradient(to right, #f8fafc, #f1f5f9);">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center bg-primary text-white shadow-sm rounded-3"
                style="width: 2.5rem; height: 2.5rem;">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <div>
                <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1rem;">Permission Info</h5>
                <p class="card-text text-muted small mb-0" style="font-size: 0.75rem;">Fill in the permission key name.
                </p>
            </div>
        </div>
    </div>

    {{-- Form Body --}}
    <div class="card-body p-4">
        <form action="{{ route('admin.permissions.store') }}" method="POST">
            @csrf

            {{-- Permission Name --}}
            <div class="mb-4">
                <x-form.text name="name" label="Permission Name"
                    placeholder="e.g. role-create, user-delete, setting-view" :value="old('name')" />
                    <x-form.text name="display_name" label="Display Name"
                    placeholder="e.g. role create, user delete, setting view" :value="old('display_name')" />
            </div>

            {{-- Actions --}}
            <div class="pt-3 border-t d-flex align-items-center gap-2">
                <x-form.submit class="btn btn-primary d-inline-flex align-items-center">
                    <svg class="me-1" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Permission
                </x-form.submit>
                <x-form.cancel :href="route('admin.permissions.index')" class="btn btn-light border">Cancel
                </x-form.cancel>
            </div>
        </form>
    </div>
</div>

@endsection