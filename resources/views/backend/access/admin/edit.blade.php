<x-admin-layout>
    <x-slot name="title">Edit User</x-slot>
    <x-slot name="header">Edit User</x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        Edit User
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Update user details and access control roles.
                    </p>
                </div>

                <nav class="flex items-center gap-2 text-sm text-gray-500">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('admin.stuff.index') }}" class="hover:text-indigo-600">Users</a>
                    <span>/</span>
                    <span class="font-medium text-gray-800">Edit</span>
                </nav>
            </div>

            <form action="{{ route('admin.stuff.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                    {{-- Card Header --}}
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="text-lg font-semibold">
                            User: {{ $user->name }}
                        </h3>
                    </div>

                    {{-- Card Body --}}
                    <div class="w-full">
                        <div class="bg-transparent dark:bg-gray-900/90 p-5 ">
                            <x-form.file name="image" label="Profile Photo" file="{{ $user->image ?? '' }}">
                            </x-form.file>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                        {{-- Name --}}
                        <x-form.text name="name" label="Name" placeholder="Enter full name"
                            :value="old('name', $user->name)" required autofocus />

                        {{-- Email --}}
                        <x-form.email name="email" label="Email Address" placeholder="user@example.com"
                            :value="old('email', $user->email)" required />

                        {{-- Role --}}
                        <x-form.select name="role" label="Role" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" @selected(old('role', $user->roles->first()?->name) ==
                                $role->name)>
                                {{ ucfirst($role->name) }}
                            </option>
                            @endforeach
                        </x-form.select>

                        {{-- Password --}}
                        <x-form.password name="password"
                            label="Password <small class='text-xs text-gray-400 font-normal'>(Leave blank to keep current password)</small>"
                            placeholder="New password (optional)" />

                        {{-- Confirm Password --}}
                        <x-form.password name="password_confirmation" label="Confirm Password"
                            placeholder="Repeat new password" />

                    </div>

                    {{-- Footer --}}
                    <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-5">
                        <x-form.cancel href="{{ route('admin.users.index') }}">
                            Cancel
                        </x-form.cancel>

                        <x-form.submit>
                            Update User
                        </x-form.submit>
                    </div>

                </div>

            </form>

        </div>
    </div>
</x-admin-layout>