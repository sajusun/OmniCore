<x-admin-layout>
    <x-slot name="title">Create Role</x-slot>

    <div class="container-fluid py-4">
        <x-page-header title="Create New Role" subtitle="Define a role and assign permissions to it."
            :breadcrumbs="['Access Control' => null, 'Roles' => route('admin.roles.index'), 'Create' => null]">
            <x-slot:actions>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-light border d-inline-flex align-items-center gap-2 px-3">
                    <i class="fas fa-arrow-left"></i><span>Back</span>
                </a>
            </x-slot:actions>
        </x-page-header>

        <x-card title="Role & Permissions" subtitle="Fill in the role name and select the permissions to assign.">
            <x-slot:icon><i class="fas fa-user-shield"></i></x-slot:icon>
            @include('backend.access.role._form', ['role' => null])
        </x-card>
    </div>
</x-admin-layout>