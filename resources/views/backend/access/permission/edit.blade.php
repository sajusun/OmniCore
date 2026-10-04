<x-admin-layout>
    <x-slot name="title">Edit Permission</x-slot>

    <div class="container-fluid py-4">
        <x-page-header title="Edit Permission" subtitle="Updating: {{ $permission->name }}"
            :breadcrumbs="['Access Control' => null, 'Permissions' => route('admin.permissions.index'), 'Edit' => null]">
            <x-slot:actions>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-light border d-inline-flex align-items-center gap-2 px-3">
                    <i class="fas fa-arrow-left"></i><span>Back</span>
                </a>
            </x-slot:actions>
        </x-page-header>

        <x-card title="Edit Permission Info" subtitle="Changes apply to both web & api guards.">
            <x-slot:icon><i class="fas fa-pen-to-square"></i></x-slot:icon>
            @include('backend.access.permission._form', ['permission' => $permission])
        </x-card>
    </div>
</x-admin-layout>