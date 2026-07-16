@extends('layouts.admin', ['title' => 'Create Permission'])

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
            <a href="{{ route('admin.permissions.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Permissions</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-indigo-600 dark:text-indigo-400 font-medium">Create</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Permission</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Define an individual permission key (e.g. role-create).</p>
    </div>
    <a href="{{ route('admin.permissions.index') }}"
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Permission Info</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">Fill in the permission key name.</p>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.permissions.store') }}" method="POST" class="p-6 space-y-6">
        @csrf

        {{-- Permission Name --}}
        <x-form.text
            name="name"
            label="Permission Name"
            placeholder="e.g. role-create, user-delete, setting-view"
            :value="old('name')"
        />

        {{-- Actions --}}
        <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center gap-3">
            <x-form.submit>
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Permission
            </x-form.submit>
            <x-form.cancel :href="route('admin.permissions.index')">Cancel</x-form.cancel>
        </div>
    </form>
</div>

@endsection
