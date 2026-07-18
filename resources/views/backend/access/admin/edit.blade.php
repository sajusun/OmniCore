<x-admin-layout>
    <x-slot name="title">Edit User</x-slot>
    <x-slot name="header">Edit User</x-slot>

    <div class="container-fluid py-4 px-0">
        <div class="mx-auto" style="max-width: 1280px;">

            {{-- Header Area with Bootstrap Flex & Breadcrumb --}}
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
                <div>
                    <h2 class="h4 fw-bold text-dark mb-1">Edit User</h2>
                    <p class="small text-muted mb-0">Update user details and access control roles.</p>
                </div>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted">Users</a></li>
                        <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>

            {{-- Form Start --}}
            <form action="{{ route('admin.stuff.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Main Form Card Container with Force Flat Edges --}}
                <div class="card border border-light-subtle shadow-sm overflow-hidden" style="border-radius: 0 !important;">

                    {{-- Card Header --}}
                    <div class="card-header border-bottom border-light-subtle bg-transparent px-4 py-3">
                        <h3 class="card-title h6 mb-0 fw-bold text-dark">
                            User: {{ $user->name }}
                        </h3>
                    </div>

                    {{-- Full-width Profile Photo Upload Section --}}
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
                                    :value="old('name', $user->name)" required autofocus />
                            </div>

                            {{-- Email --}}
                            <div class="col-12 col-md-6">
                                <x-form.email name="email" label="Email Address" placeholder="user@example.com"
                                    :value="old('email', $user->email)" required />
                            </div>

                            {{-- Role Selector --}}
                            <div class="col-12 col-md-6">
                                <x-form.select name="role" label="Role" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->name }}" @selected(old('role', $user->roles->first()?->name) == $role->name)>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                    @endforeach
                                </x-form.select>
                            </div>

                            {{-- Password Info --}}
                            <div class="col-12 col-md-6">
                                <x-form.password name="password"
                                    label="Password <span class='small text-muted fw-normal' style='font-size: 0.75rem;'>(Leave blank to keep current password)</span>"
                                    placeholder="New password (optional)" />
                            </div>

                            {{-- Confirm Password --}}
                            <div class="col-12 col-md-6">
                                <x-form.password name="password_confirmation" label="Confirm Password"
                                    placeholder="Repeat new password" />
                            </div>

                        </div>
                    </div>

                    {{-- Card Action Footer with Absolute Flat Corners --}}
                    <div class="card-footer d-flex justify-content-end gap-2 border-top border-light-subtle bg-light px-4 py-3">
                        <x-form.cancel href="{{ route('admin.stuff.index') }}" class="btn btn-light border" style="border-radius: 0 !important;">
                            Cancel
                        </x-form.cancel>

                        <x-form.submit class="btn btn-primary px-4" style="border-radius: 0 !important;">
                            Update User
                        </x-form.submit>
                    </div>

                </div>
            </form>

        </div>
    </div>
</x-admin-layout>