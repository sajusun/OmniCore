<x-admin-layout>
    @slot('title')
        Profile Settings
    @endslot

    <div class="py-4">
        <div class="container-fluid">
            <x-page-header title="Profile Settings" :breadcrumbs="['Settings' => 'javascript:void(0);', 'Profile' => null]" class="mb-4" />

            <!-- Main Profile Header Card -->
            <div class="card shadow-sm border overflow-hidden mb-4"
                 style="background-color: var(--theme-card-bg, #ffffff); border-color: var(--theme-card-border, #e2e8f0); border-radius: var(--theme-card-radius, 12px);">

                <!-- Profile Info Header -->
                <div class="card-header py-4 px-4 border-bottom-0"
                     style="background: linear-gradient(to right, rgba(99, 102, 241, 0.05), transparent); border-color: var(--theme-card-border, #e2e8f0);">
                    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-4">
                        <!-- Avatar Section -->
                        <div class="position-relative flex-shrink-0">
                            <div class="profile-img-main overflow-hidden border border-4 border-white shadow-sm rounded-circle"
                                 style="width: 110px; height: 110px; background-color: var(--theme-body-bg, #f8fafc);">
                                <img src="{{ Auth::user()->avatar ? asset(Auth::user()->avatar) : asset('default/profile.png') }}"
                                     alt="Profile Picture" class="w-100 h-100 object-cover">
                            </div>
                            <button id="uploadImageBtn"
                                    class="btn btn-primary btn-sm position-absolute bottom-0 end-0 p-2 d-flex align-items-center justify-content-center shadow rounded-circle"
                                    style="width: 34px; height: 34px;" title="Change Profile Picture">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                            <input type="file" name="profile_picture" id="profile_picture_input" style="display: none;">
                        </div>

                        <!-- User Info -->
                        <div class="flex-grow-1 min-w-0">
                            <h3 class="fs-4 fw-bold mb-1" style="color: var(--theme-heading-color, #0f172a);">
                                {{ Auth::user()->name ?? 'N/A' }}
                            </h3>
                            <p class="text-muted small d-flex align-items-center gap-2 mt-1 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>{{ Auth::user()->email ?? 'N/A' }}</span>
                            </p>
                            <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 small fw-medium">
                                    Active
                                </span>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2.5 py-1.5 small fw-medium">
                                    Member since {{ Auth::user()->created_at ? Auth::user()->created_at->format('M d, Y') : 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <!-- Quick Stats Badge Cards -->
                        <div class="d-flex flex-row flex-lg-column gap-2 flex-shrink-0">
                            <div class="px-3 py-2 border rounded-2 text-center shadow-sm"
                                 style="background-color: var(--theme-card-bg, #ffffff); border-color: var(--theme-card-border, #e2e8f0); min-width: 110px;">
                                <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size: 0.7rem;">Role</p>
                                <p class="small fw-bold mb-0 text-dark" style="color: var(--theme-heading-color, #0f172a) !important;">
                                    {{ Auth::user()->role ?? 'User' }}
                                </p>
                            </div>
                            <div class="px-3 py-2 border rounded-2 text-center shadow-sm"
                                 style="background-color: var(--theme-card-bg, #ffffff); border-color: var(--theme-card-border, #e2e8f0); min-width: 110px;">
                                <p class="text-muted small text-uppercase fw-semibold mb-0" style="font-size: 0.7rem;">Status</p>
                                <p class="small fw-bold mb-0 text-success">Verified</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Navigation (Clean Bootstrap 5 + Theme Engine) -->
                <div class="card-header border-top border-bottom-0 p-0"
                     style="background-color: var(--theme-table-header-bg, #f8fafc); border-color: var(--theme-card-border, #e2e8f0);">
                    <ul class="nav nav-tabs border-bottom-0 px-3 pt-2 gap-2 flex-nowrap" id="profileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#editProfile" id="edit-profile-tab"
                               class="nav-link active d-flex align-items-center gap-2 px-4 py-2.5 small fw-semibold border-bottom-0"
                               data-bs-toggle="tab" role="tab" aria-controls="editProfile" aria-selected="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Edit Profile</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#updatePassword" id="update-password-tab"
                               class="nav-link d-flex align-items-center gap-2 px-4 py-2.5 small fw-semibold border-bottom-0"
                               data-bs-toggle="tab" role="tab" aria-controls="updatePassword" aria-selected="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Update Password</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Edit Profile Tab -->
                <div class="tab-pane fade show active" id="editProfile" role="tabpanel" aria-labelledby="edit-profile-tab">
                    <x-card title="Personal Information" badge="required fields *" badge-class="badge bg-secondary">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </x-slot:icon>

                        <form class="form form-horizontal" method="post" action="{{ route('admin.setting.profile.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-6 form-group">
                                    <x-form.text label="Full Name" name="name" :value="old('name', $user->name)" required autofocus />
                                </div>
                                <div class="col-md-6 form-group">
                                    <x-form.text label="Email" name="email" :value="old('email', $user->email)" required readonly="true" />
                                </div>
                            </div>

                            <x-form.actions submit-text="Update Profile" />
                        </form>
                    </x-card>
                </div>

                <!-- Update Password Tab -->
                <div class="tab-pane fade" id="updatePassword" role="tabpanel" aria-labelledby="update-password-tab">
                    <x-card title="Change Password" badge="secure" badge-class="badge bg-warning text-dark">
                        <x-slot:icon>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </x-slot:icon>

                        <form class="form form-horizontal" method="post" action="{{ route('admin.setting.profile.update.password') }}">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-12 form-group">
                                    <x-form.password name="old_password" label="Current Password" value="" placeholder="Enter current password" />
                                </div>

                                <div class="col-md-6 form-group">
                                    <x-form.password name="password" label="New Password" value="" placeholder="Enter new password" />
                                </div>

                                <div class="col-md-6 form-group">
                                    <x-form.password name="password_confirmation" label="Confirm Password" value="" placeholder="Confirm new password" />
                                </div>
                            </div>

                            <!-- Password strength indicator notice -->
                            <div class="mt-4 p-3 rounded-2 border d-flex align-items-start gap-2"
                                 style="background-color: var(--theme-table-header-bg, #f8fafc); border-color: var(--theme-card-border, #e2e8f0);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="text-primary flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="small text-muted mb-0">
                                    Use at least <strong>8 characters</strong> with a mix of uppercase, lowercase, numbers, and symbols for a strong password.
                                </p>
                            </div>

                            <x-form.actions submit-text="Update Password" />
                        </form>
                    </x-card>
                </div>
            </div>

            <!-- Additional Info Cards -->
            <div class="row g-3 mt-4">
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100"
                         style="background-color: var(--theme-card-bg, #ffffff); border-color: var(--theme-card-border, #e2e8f0); border-radius: var(--theme-card-radius, 12px);">
                        <div class="p-2.5 rounded-2 bg-success bg-opacity-10 text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Profile Status</p>
                            <p class="fw-semibold mb-0" style="color: var(--theme-heading-color, #0f172a);">All information is up to date</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100"
                         style="background-color: var(--theme-card-bg, #ffffff); border-color: var(--theme-card-border, #e2e8f0); border-radius: var(--theme-card-radius, 12px);">
                        <div class="p-2.5 rounded-2 bg-primary bg-opacity-10 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Security</p>
                            <p class="fw-semibold mb-0" style="color: var(--theme-heading-color, #0f172a);">Password last changed recently</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100"
                         style="background-color: var(--theme-card-bg, #ffffff); border-color: var(--theme-card-border, #e2e8f0); border-radius: var(--theme-card-radius, 12px);">
                        <div class="p-2.5 rounded-2 text-purple" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Account</p>
                            <p class="fw-semibold mb-0" style="color: var(--theme-heading-color, #0f172a);">{{ Auth::user()->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                // If there are password validation errors, switch to password tab
                @if($errors->has('old_password') || $errors->has('password') || $errors->has('password_confirmation'))
                    var triggerEl = document.querySelector('#update-password-tab');
                    if (triggerEl) {
                        var tab = new bootstrap.Tab(triggerEl);
                        tab.show();
                    }
                @endif

                // Profile Picture Upload
                $('#uploadImageBtn').on('click', function (e) {
                    e.preventDefault();
                    $('#profile_picture_input').click();
                });

                // Preview image before upload
                $('#profile_picture_input').on('change', function () {
                    var file = this.files[0];
                    if (!file) return;

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('.profile-img-main img').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);

                    // Modern Axios upload
                    var formData = new FormData();
                    formData.append('profile_picture', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    window.axios.post("{{ route('admin.setting.profile.avatar.update') }}", formData, {
                        headers: { 'Content-Type': 'multipart/form-data' }
                    })
                    .then(function (res) {
                        var response = res.data;
                        if (response.success) {
                            $('.profile-img-main img').attr('src', response.image_url);
                            $('.profile-img-change').attr('src', response.image_url);
                            toastr.success('Profile picture updated successfully.');
                        } else {
                            toastr.error(response.message || 'Update failed');
                        }
                    })
                    .catch(function (error) {
                        var msg = error.response?.data?.message || 'An error occurred while updating the profile picture.';
                        toastr.error(msg);
                    });
                });
            });
        </script>
    @endpush
</x-admin-layout>