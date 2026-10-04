<x-admin-layout>
    <x-slot name="title">Edit Role</x-slot>

    <div class="container-fluid py-4">
        <x-page-header title="Edit Role" subtitle="Updating: {{ $role->name }}"
            :breadcrumbs="['Access Control' => null, 'Roles' => route('admin.roles.index'), 'Edit' => null]">
            <x-slot:actions>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-light border d-inline-flex align-items-center gap-2 px-3">
                    <i class="fas fa-arrow-left"></i><span>Back</span>
                </a>
            </x-slot:actions>
        </x-page-header>

        <x-card title="Role: {{ $role->name }}" subtitle="{{ $role->permissions->count() }} permission(s) currently assigned.">
            <x-slot:icon><i class="fas fa-pen-to-square"></i></x-slot:icon>
            @include('backend.access.role._form', ['role' => $role])
        </x-card>
    </div>
</x-admin-layout>