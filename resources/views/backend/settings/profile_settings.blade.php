<x-admin-layout>
    @slot('title')
    Profile Settings
    @endslot

    @slot('header')
    <div class="d-flex flex-row justify-content-between align-items-center gap-3 w-100">
        <span class="fs-5 fw-bold">Profile Settings</span>
        <nav
            class="small fw-medium text-muted d-none d-sm-flex align-items-center bg-light px-3 py-2 rounded-pill shadow-sm border">
            <ol class="d-flex list-unstyled m-0 gap-2">
                <li class="d-flex align-items-center">
                    <a href="javascript:void(0);" class="text-decoration-none text-secondary">Settings</a>
                    <span class="mx-2 text-muted">/</span>
                </li>
                <li class="d-flex align-items-center text-dark fw-medium" aria-current="page">
                    Profile
                </li>
            </ol>
        </nav>
    </div>
    @endslot

    <div class="py-4">
        <div class="container-fluid">
            <!-- Main Profile Card -->
            <div class="card shadow-sm border overflow-hidden">

                <!-- Profile Header -->
                <div class="card-header bg-light bg-gradient py-4 px-4 border-bottom-0">
                    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-4">
                        <!-- Avatar Section -->
                        <div class="position-relative flex-shrink-0">
                            <div class="profile-img-main rounded-circle overflow-hidden border border-4 border-white shadow-sm"
                                style="width: 120px; height: 120px;">
                                <img src="{{ Auth::user()->avatar ? asset(Auth::user()->avatar) : asset('default/profile.png') }}"
                                    alt="Profile Picture" class="w-full h-full object-cover">
                            </div>
                            <button id="uploadImageBtn"
                                class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 p-2 d-flex align-items-center justify-content-center shadow"
                                style="width: 32px; height: 32px;">
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
                            <h3 class="fs-4 fw-bold mb-1">{{ Auth::user()->name ?? 'N/A' }}</h3>
                            <p class="text-muted small d-flex align-items-center gap-2 mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ Auth::user()->email ?? 'N/A' }}
                            </p>
                            <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                                <span
                                    class="badge bg-info text-white border border-success border-opacity-25 px-3 py-2 small">
                                    Active
                                </span>
                                <span
                                    class="badge bg-secondary text-white border border-secondary border-opacity-25 px-3 py-2 small">
                                    
                                    Member since {{ Auth::user()->created_at ? Auth::user()->created_at->format('M d,
                                    Y') : 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="d-flex flex-row flex-lg-column gap-2 flex-shrink-0">
                            <div class="bg-white px-3 py-2 rounded border text-center min-w-[100px] shadow-sm">
                                <p class="text-muted small text-uppercase fw-semibold mb-0">Role</p>
                                <p class="small fw-bold mb-0 text-dark">{{ Auth::user()->role ?? 'User' }}</p>
                            </div>
                            <div class="bg-white px-3 py-2 rounded border text-center min-w-[100px] shadow-sm">
                                <p class="text-muted small text-uppercase fw-semibold mb-0">Status</p>
                                <p class="small fw-bold mb-0 text-success">Verified</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="card-header bg-light border-top border-bottom-0 p-0">
                    <ul class="nav nav-tabs border-bottom-0 px-3 pt-2 gap-1 flex-nowrap" id="profileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#editProfile" id="edit-profile-tab"
                                class="nav-link active d-flex align-items-center gap-2 px-4 py-2 small fw-medium rounded-top border-bottom-0"
                                data-bs-toggle="tab" role="tab" aria-controls="editProfile" aria-selected="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Edit Profile
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#updatePassword" id="update-password-tab"
                                class="nav-link d-flex align-items-center gap-2 px-4 py-2 small fw-medium rounded-top border-bottom-0"
                                data-bs-toggle="tab" role="tab" aria-controls="updatePassword" aria-selected="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Update Password
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content mt-4">
                <!-- Edit Profile Tab -->
                <div class="tab-pane fade show active" id="editProfile" role="tabpanel"
                    aria-labelledby="edit-profile-tab">
                    <div class="card shadow-sm border overflow-hidden">
                        <div class="card-header bg-light d-flex align-items-center gap-2 py-3 px-4">
                            <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <h6 class="card-title fw-bold mb-0">Personal Information</h6>
                            <span class="badge bg-secondary ms-auto">required fields *</span>
                        </div>
                        <div class="card-body p-4">
                            <form class="form form-horizontal" method="post"
                                action="{{ route('admin.setting.profile.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">

                                    <div class="col-md-6 form-group">
                                        <x-form.text label="Full Name" name="name" :value="old('name', $user->name)"
                                            required autofocus />
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <x-form.text label="Email" name="email" :value="old('name', $user->email)"
                                            required autofocus readonly="true" />
                                    </div>
                                </div>
                                <div
                                    class="mt-4 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <p class="small text-muted d-flex align-items-center gap-1 mb-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        All changes are saved immediately
                                    </p>
                                    <button class="btn btn-primary d-inline-flex align-items-center px-4 py-2"
                                        type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="me-2"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                        </svg>
                                        Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Update Password Tab -->
                <div class="tab-pane fade" id="updatePassword" role="tabpanel" aria-labelledby="update-password-tab">
                    <div class="card shadow-sm border overflow-hidden">
                        <div class="card-header bg-light d-flex align-items-center gap-2 py-3 px-4">
                            <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h6 class="card-title fw-bold mb-0">Change Password</h6>
                            <span class="badge bg-warning text-dark ms-auto">secure</span>
                        </div>
                        <div class="card-body p-4">
                            <form class="form form-horizontal" method="post"
                                action="{{ route('admin.setting.profile.update.password') }}">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">
                                    <div class="col-12 form-group">
                                        <x-form.password name="old_password" label="Current Password" value=""
                                            placeholder="Enter current password" />
                                    </div>

                                    <div class="col-md-6 form-group">

                                        <x-form.password name="password" label="New Password" value=""
                                            placeholder="Enter new password" />
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <x-form.password name="password_confirmation" label="Confirm Password" value=""
                                            placeholder="Confirm new password" />
                                    </div>
                                </div>

                                <!-- Password strength indicator -->
                                <div class="mt-4 p-3 bg-light border rounded-3 d-flex align-items-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        class="text-primary flex-shrink-0 mt-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="small text-muted mb-0">
                                        Use at least <strong>8 characters</strong> with a mix of uppercase, lowercase,
                                        numbers, and symbols for a strong password.
                                    </p>
                                </div>

                                <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                                    <x-form.submit>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="me-2"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                        </svg>
                                        Update Password
                                    </x-form.submit>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info Cards -->
            <div class="row g-3 mt-4">
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100">
                        <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Profile Status</p>
                            <p class="fw-semibold mb-0">All information is up to date</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100">
                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Security</p>
                            <p class="fw-semibold mb-0">Password last changed recently</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100">
                        <div class="p-2 bg-purple bg-opacity-10 text-purple rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Account</p>
                            <p class="fw-semibold mb-0">{{ Auth::user()->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    @push('scripts')
        <script>
            $(document).ready(function () {
                // ── If there are password validation errors, switch to password tab ──
                @if($errors->has('old_password') || $errors->has('password') || $errors->has('password_confirmation'))
                    var triggerEl = document.querySelector('#update-password-tab');
                    if (triggerEl) {
                        var tab = new bootstrap.Tab(triggerEl);
                        tab.show();
                    }
                @endif

                // ── Profile Picture Upload ────────────────────────────────
                $('#uploadImageBtn').on('click', function (e) {
                    e.preventDefault();
                    $('#profile_picture_input').click();
                });

                // Preview image before upload
                $('#profile_picture_input').on('change', function () {
                    var file = this.files[0];
                    if (!file) return;

                    // Instant preview
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('.profile-img-main img').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);

                    // AJAX upload
                    var formData = new FormData();
                    formData.append('profile_picture', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    $.ajax({
                        url: "{{ route('admin.setting.profile.avatar.update') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.success) {
                                $('.profile-img-main img').attr('src', response.image_url);
                                $('.profile-img-change').attr('src', response.image_url);
                                toastr.success('Profile picture updated successfully.');
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function () {
                            toastr.error('An error occurred while updating the profile picture.');
                        }
                    });
                });
            });
        </script>
    @endpush
</x-admin-layout>