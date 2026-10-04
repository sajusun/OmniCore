<x-admin-layout>
    <x-slot name="title">Create Permission</x-slot>

    <div class="container-fluid py-4">
        <x-page-header title="Create New Permission" subtitle="Define an individual permission key that can be assigned to roles."
            :breadcrumbs="['Access Control' => null, 'Permissions' => route('admin.permissions.index'), 'Create' => null]">
            <x-slot:actions>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-light border d-inline-flex align-items-center gap-2 px-3">
                    <i class="fas fa-arrow-left"></i><span>Back</span>
                </a>
            </x-slot:actions>
        </x-page-header>

        <x-card title="Permission Info" subtitle="Fill in the permission key and display name.">
            <x-slot:icon><i class="fas fa-key"></i></x-slot:icon>
            @include('backend.access.permission._form', ['permission' => null])
        </x-card>
    </div>
</x-admin-layout>