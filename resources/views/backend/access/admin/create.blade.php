<x-admin-layout>
    <x-slot name="title">Create User</x-slot>
    <x-slot name="header">Create User</x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        Create User
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Create a new admin stuff account and assign a role.
                    </p>
                </div>

                <nav class="flex items-center gap-2 text-sm text-gray-500">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('admin.stuff.index') }}" class="hover:text-indigo-600">Users</a>
                    <span>/</span>
                    <span class="font-medium text-gray-800">Create</span>
                </nav>
            </div>

            <form action="{{ route('admin.stuff.store') }}" method="POST">
                @csrf

                <div class="overflow-hidden border border-gray-200 bg-white shadow-sm">

                    {{-- Card Header --}}
                    <div class="border-b border-gray-200 px-6 py-5">
                        <h3 class="text-lg font-semibold">
                            User Information
                        </h3>
                    </div>

                    {{-- Card Body --}}
                    <div class="w-full">
                        <div class="bg-transparent dark:bg-gray-900/90 p-5">
                            <x-form.file name="image" label="Profile Photo" file="{{ $user->image ?? '' }}">
                            </x-form.file>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                        {{-- Name --}}
                        <x-form.text name="name" label="Name" placeholder="Enter full name" :value="old('name')"
                            required autofocus />

                        {{-- Email --}}
                        <x-form.email name="email" label="Email Address" placeholder="user@example.com"
                            :value="old('email')" required />

                        {{-- Role --}}
                        <x-form.select name="role" label="Role" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" @selected(old('role')==$role->name)>
                                {{ ucfirst($role->name) }}
                            </option>
                            @endforeach
                        </x-form.select>

                        {{-- Password --}}
                        <x-form.password name="password" label="Password" placeholder="Enter password" required>
                            <x-slot name="labelActions">
                                <button type="button" onclick="generatePassword()"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Generate</button>
                            </x-slot>
                        </x-form.password>

                        {{-- Confirm Password --}}
                        <x-form.password name="password_confirmation" label="Confirm Password"
                            placeholder="Repeat password" required />

                    </div>

                    {{-- Footer --}}
                    <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-5">
                        <x-form.cancel href="{{ route('admin.users.index') }}">Cancel</x-form.cancel>
                        <x-form.submit>Save User</x-form.submit>
                    </div>

                </div>

            </form>

        </div>
    </div>

    <script>
    function generatePassword() {
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
        let password = "";

        for (let i = 0; i < 12; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        console.log(password);

        document.getElementById('password').value = password;
        document.getElementById('password_confirmation').value = password;
    }
    </script>

</x-admin-layout>
