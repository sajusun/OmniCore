<x-admin-layout>
    <x-slot name="title">Create Staff</x-slot>

    <div class="container-fluid py-4">
        <x-page-header
            title="Create Staff"
            subtitle="Create a new administrative staff account and assign roles."
            :breadcrumbs="['Dashboard' => route('admin.dashboard'), 'Staff' => route('admin.stuff.index'), 'Create' => null]">
            <x-slot:actions>
                <a href="{{ route('admin.stuff.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1.5 px-3 py-2" style="font-size: 0.8125rem; border-radius: 8px;">
                    <i class="fa fa-arrow-left"></i>
                    <span>Back to Staff</span>
                </a>
            </x-slot:actions>
        </x-page-header>

        <form action="{{ route('admin.stuff.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <x-card title="Staff Information" :noPadding="true" class="mb-4">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </x-slot:icon>

                {{-- Profile Photo Upload --}}
                <div class="p-4 bg-light border-bottom">
                    <x-form.file name="image" label="Profile Photo" file="{{ $user->image ?? '' }}" />
                </div>

                {{-- Inputs Grid --}}
                <div class="p-4">
                    <div class="row g-4">
                        {{-- Name --}}
                        <div class="col-12 col-md-6">
                            <x-form.text name="name" label="Full Name" placeholder="Enter full name" :value="old('name')" required autofocus />
                        </div>

                        {{-- Email --}}
                        <div class="col-12 col-md-6">
                            <x-form.email name="email" label="Email Address" placeholder="staff@example.com" :value="old('email')" required />
                        </div>

                        {{-- Role --}}
                        <div class="col-12 col-md-6">
                            <x-form.select name="role" label="Role" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" @selected(old('role') == $role->name)>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </x-form.select>
                        </div>

                        {{-- Password --}}
                        <div class="col-12 col-md-6">
                            <x-form.password name="password" label="Password" placeholder="Enter password" required>
                                <x-slot name="labelActions">
                                    <button type="button" onclick="generatePassword()" class="btn btn-link p-0 text-decoration-none small fw-semibold text-primary">
                                        <i class="fa fa-magic me-1"></i> Generate
                                    </button>
                                </x-slot>
                            </x-form.password>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="col-12 col-md-6">
                            <x-form.password name="password_confirmation" label="Confirm Password" placeholder="Repeat password" required />
                        </div>
                    </div>
                </div>

                <x-slot:footer>
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <a href="{{ route('admin.stuff.index') }}" class="btn btn-light px-4 py-2 fw-semibold" style="font-size: 0.8125rem; border-radius: 6px;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5 px-4 py-2 shadow-sm fw-semibold" style="font-size: 0.8125rem; border-radius: 6px;">
                            <i class="fa fa-check"></i>
                            <span>Save Staff</span>
                        </button>
                    </div>
                </x-slot:footer>
            </x-card>
        </form>
    </div>

    @push('scripts')
    <script>
    function generatePassword() {
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
        let password = "";
        for (let i = 0; i < 12; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        document.getElementById('password').value = password;
        document.getElementById('password_confirmation').value = password;
    }
    </script>
    @endpush
</x-admin-layout>
