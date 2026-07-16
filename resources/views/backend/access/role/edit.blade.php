@extends('layouts.admin', ['title' => 'Edit Role'])

@section('content')

{{-- Page Header --}}
<div class="mb-8 flex items-center justify-between">
    <div>
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span>Access Control</span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="{{ route('admin.roles.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Roles</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-indigo-600 dark:text-indigo-400 font-medium">Edit</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Role</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Updating: <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $role->name }}</span>
        </p>
    </div>
    <a href="{{ route('admin.roles.index') }}"
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 group">
        <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back
    </a>
</div>

{{-- Main Card --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

    {{-- Card Header --}}
    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-md shadow-indigo-200 dark:shadow-indigo-900/40">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Role: {{ $role->name }}</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $role->permissions->count() }} permission(s) currently assigned.
                </p>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" class="p-6 space-y-6">
        @csrf
        @method('PUT')

        {{-- Role Name --}}
        <x-form.text
            name="name"
            label="Role Name"
            placeholder="e.g. Manager, Editor, Moderator"
            :value="old('name', $role->name)"
        />

        {{-- Permissions --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Permissions
                    <span class="ml-2 px-2 py-0.5 text-xs bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-full">
                        {{ $permissions->count() }} total
                    </span>
                </label>
                {{-- Select All Toggle --}}
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Select All</span>
                    <div class="relative">
                        <input type="checkbox" id="selectAll" class="sr-only">
                        <div class="toggle-track w-10 h-5 rounded-full bg-gray-200 dark:bg-gray-700 transition-colors duration-200"></div>
                        <div class="toggle-knob absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200"></div>
                    </div>
                </label>
            </div>

            {{-- Permissions Grid grouped by suffix --}}
            @if($permissions->count())
            <div class="border border-gray-100 dark:border-gray-700 rounded-xl overflow-hidden">
                @php
                    $grouped = $permissions->groupBy(function($p) {
                        $parts = explode('-', $p->name);
                        return count($parts) > 1 ? ucfirst(end($parts)) : 'General';
                    });
                    $assignedNames = $role->permissions->pluck('name')->toArray();
                @endphp
                @foreach($grouped as $group => $perms)
                <div class="border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div class="px-4 py-2 bg-gray-50 dark:bg-gray-900/40 flex items-center gap-2">
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $group }}</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">({{ $perms->count() }})</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-1 p-3">
                        @foreach($perms as $permission)
                        @php
                            $isChecked = in_array($permission->name, old('permissions', $assignedNames));
                        @endphp
                        <label for="perm-{{ $permission->id }}"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-lg cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors group {{ $isChecked ? 'bg-indigo-50/50 dark:bg-indigo-900/10' : '' }}">
                            <input
                                class="permission-checkbox w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700 cursor-pointer"
                                type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->name }}"
                                id="perm-{{ $permission->id }}"
                                {{ $isChecked ? 'checked' : '' }}
                            >
                            <span class="text-xs text-gray-600 dark:text-gray-300 group-hover:text-indigo-700 dark:group-hover:text-indigo-400 transition-colors">
                                {{ $permission->name }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                <p class="text-sm text-gray-500 dark:text-gray-400">No permissions found. Create permissions first.</p>
            </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center gap-3">
            <x-form.submit>
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Update Role
            </x-form.submit>
            <x-form.cancel :href="route('admin.roles.index')">Cancel</x-form.cancel>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    const track = selectAll?.closest('label')?.querySelector('.toggle-track');
    const knob  = selectAll?.closest('label')?.querySelector('.toggle-knob');

    function syncToggle(checked) {
        if (track) { track.classList.toggle('bg-indigo-600', checked); track.classList.toggle('bg-gray-200', !checked); }
        if (knob)  knob.style.transform = checked ? 'translateX(1.25rem)' : '';
    }

    // Init toggle state from current checkboxes
    const allCheckedInitially = [...checkboxes].every(c => c.checked);
    if (selectAll) selectAll.checked = allCheckedInitially;
    syncToggle(allCheckedInitially);

    selectAll?.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = this.checked);
        syncToggle(this.checked);
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            const allChecked = [...checkboxes].every(c => c.checked);
            if (selectAll) selectAll.checked = allChecked;
            syncToggle(allChecked);
        });
    });
})();
</script>
@endpush
