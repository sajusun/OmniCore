<x-admin-layout>
    <x-slot name="title">Create User</x-slot>
    <x-slot name="header">Create User</x-slot>

    <div class="container-fluid py-4 px-0">
        <div class="mx-auto" style="max-width: 1280px;">

            {{-- Header with Bootstrap Flex & Breadcrumb Layout --}}
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
                <div>
                    <h2 class="h4 fw-bold text-dark mb-1">Create User</h2>
                    <p class="small text-muted mb-0">Create a new user account and assign a role.</p>
                </div>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted">Users</a></li>
                        <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Create</li>
                    </ol>
                </nav>
            </div>

            {{-- Form Start --}}
            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Main Form Card Container with Forced Flat Corners --}}
                <div class="card border border-light-subtle shadow-sm overflow-hidden" style="border-radius: 0 !important;">

                    {{-- Card Header --}}
                    <div class="card-header border-bottom border-light-subtle bg-transparent px-4 py-3">
                        <h3 class="card-title h6 mb-0 fw-bold text-dark">
                            User Information
                        </h3>
                    </div>

                    {{-- Profile Photo Upload Container --}}
                    <div class="w-full bg-light border-bottom border-light-subtle p-4">
                        <x-form.file name="image" label="Profile Photo" file="{{ $user->image ?? '' }}">
                        </x-form.file>
                    </div>

                    {{-- Card Body Inputs Grid --}}
                    <div class="card-body p-4">
                        <div class="row g-4">

                            {{-- Name --}}
                            <div class="col-12 col-md-6">
                                <x-form.text name="name" label="Name" placeholder="Enter full name" 
                                    :value="old('name')" required autofocus />
                            </div>

                            {{-- Email --}}
                            <div class="col-12 col-md-6">
                                <x-form.email name="email" label="Email Address" placeholder="user@example.com"
                                    :value="old('email')" required />
                            </div>

                            {{-- Role Selector --}}
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

                            {{-- Password with Custom Label Actions Slot --}}
                            <div class="col-12 col-md-6">
                                <x-form.password name="password" id="password" label="Password" placeholder="Enter password" required>
                                    <x-slot name="labelActions">
                                        <button type="button" onclick="generatePassword()"
                                            class="btn btn-link p-0 text-decoration-none fw-medium text-primary m-0 border-0" 
                                            style="font-size: 0.875rem; vertical-align: baseline;">
                                            Generate
                                        </button>
                                    </x-slot>
                                </x-form.password>
                            </div>

                            {{-- Confirm Password --}}
                            <div class="col-12 col-md-6">
                                <x-form.password name="password_confirmation" id="password_confirmation" label="Confirm Password"
                                    placeholder="Repeat password" required />
                            </div>

                        </div>
                    </div>

                    {{-- Card Action Footer with Absolute Flat Corners --}}
                    <div class="card-footer d-flex justify-content-end gap-2 border-top border-light-subtle bg-light px-4 py-3">
                        <x-form.cancel href="{{ route('admin.users.index') }}" class="btn btn-light border" style="border-radius: 0 !important;">
                            Cancel
                        </x-form.cancel>

                        <x-form.submit class="btn btn-primary px-4" style="border-radius: 0 !important;">
                            Save User
                        </x-form.submit>
                    </div>

                </div>
            </form>

        </div>
    </div>

    {{-- Native Password Generator JavaScript Logic --}}
    <script>
    function generatePassword() {
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
        let password = "";

        for (let i = 0; i < 12; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }

        // Target field matching with ID parameters inside components
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');

        if (passwordInput && confirmInput) {
            passwordInput.value = password;
            confirmInput.value = password;
        } else {
            // Fallback in case your dynamic component sets name as default ID wrapper
            const namePass = document.getElementsByName('password')[0];
            const nameConfirm = document.getElementsByName('password_confirmation')[0];
            if (namePass && nameConfirm) {
                namePass.value = password;
                nameConfirm.value = password;
            }
        }
    }
    </script>
</x-admin-layout>