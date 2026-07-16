<x-admin-layout>
    @slot('title')
    Profile Settings
    @endslot

    @slot('header')
    Profile Settings
    @endslot

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Profile Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-indigo-100/30 dark:shadow-gray-900/50 border border-gray-100/50 dark:border-gray-700/50 overflow-hidden transition-all duration-300 hover:shadow-indigo-200/40 dark:hover:shadow-gray-700/30">

                <!-- Profile Header -->
                <div
                    class="px-6 py-6 sm:p-8 bg-gradient-to-r from-blue-50/50 via-indigo-50/30 to-purple-50/50 dark:from-gray-800/80 dark:via-gray-800/60 dark:to-gray-900/80">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center gap-6">
                        <!-- Avatar Section -->
                        <div class="relative flex-shrink-0">
                            <div
                                class="profile-img-main w-28 h-28 sm:w-32 sm:h-32 rounded-full overflow-hidden ring-4 ring-white/80 dark:ring-gray-700/80 shadow-xl shadow-indigo-200/50 dark:shadow-gray-900/50">
                                <img src="{{ Auth::user()->avatar ? asset(Auth::user()->avatar) : asset('default/profile.png') }}"
                                    alt="Profile Picture" class="w-full h-full object-cover">
                            </div>
                            <button id="uploadImageBtn"
                                class="absolute bottom-1 right-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full p-2.5 shadow-lg shadow-indigo-300/50 dark:shadow-indigo-900/50 transition-all duration-200 hover:scale-110 hover:shadow-indigo-400/60 focus:ring-4 focus:ring-indigo-300/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                            <input type="file" name="profile_picture" id="profile_picture_input" style="display: none;">
                        </div>

                        <!-- User Info -->
                        <div class="flex-1 min-w-0">
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ Auth::user()->name ??
                                'N/A' }}</h3>
                            <p class="text-gray-600 dark:text-gray-300 flex items-center gap-2 mt-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ Auth::user()->email ?? 'N/A' }}
                            </p>
                            <div class="flex flex-wrap items-center gap-3 mt-3">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-100/80 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 text-sm rounded-full border border-emerald-200 dark:border-emerald-800/60">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                                    Active
                                </span>
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100/80 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 text-sm rounded-full border border-gray-200 dark:border-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Member since {{ Auth::user()->created_at ? Auth::user()->created_at->format('M d,
                                    Y') : 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="flex flex-row lg:flex-col gap-3 lg:gap-2 flex-shrink-0">
                            <div
                                class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm px-4 py-2.5 rounded-xl shadow-sm border border-gray-200/50 dark:border-gray-700/50 text-center min-w-[100px]">
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider">
                                    Role</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ Auth::user()->role
                                    ?? 'User' }}</p>
                            </div>
                            <div
                                class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm px-4 py-2.5 rounded-xl shadow-sm border border-gray-200/50 dark:border-gray-700/50 text-center min-w-[100px]">
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider">
                                    Status</p>
                                <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">Verified</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="border-t border-gray-200/70 dark:border-gray-700/70 bg-gray-50/50 dark:bg-gray-900/30">
                    <div class="px-4 sm:px-6">
                        <div class="tabs-menu1">
                            <ul class="nav flex flex-wrap gap-1 py-2" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a href="#editProfile"
                                        class="tab-link active-tab flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 bg-indigo-600 text-white shadow-md shadow-indigo-200/50 dark:shadow-indigo-900/40"
                                        data-tab="editProfile" role="tab">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Edit Profile
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#updatePassword"
                                        class="tab-link flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 text-gray-600 dark:text-gray-300 hover:bg-gray-200/50 dark:hover:bg-gray-700/50"
                                        data-tab="updatePassword" role="tab">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
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
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content mt-6">
                <!-- Edit Profile Tab -->
                <div class="tab-pane" id="editProfile" role="tabpanel">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-gray-100/40 dark:shadow-gray-900/30 border border-gray-100/50 dark:border-gray-700/50 overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30 flex items-center gap-3">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/40 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <h5 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Personal Information</h5>
                            <span
                                class="ml-auto text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-600">required
                                fields *</span>
                        </div>
                        <div class="p-6 sm:p-8">
                            <form class="form form-horizontal" method="post"
                                action="{{ route('admin.setting.profile.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div class="form-group space-y-1.5">
                                        <label for="username"
                                            class="form-label text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Full Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control w-full rounded-xl border-gray-200 dark:border-gray-600 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 dark:focus:ring-indigo-800/50 transition-all duration-200 @error('name') is-invalid @enderror"
                                            name="name" placeholder="Enter your full name" id="username"
                                            value="{{ Auth::user()->name ?? 'N/A' }}">
                                        @error('name')
                                        <span class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group space-y-1.5">
                                        <label for="firstname"
                                            class="form-label text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Email Address <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email"
                                            class="form-control w-full rounded-xl border-gray-200 dark:border-gray-600 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 dark:focus:ring-indigo-800/50 transition-all duration-200 @error('email') is-invalid @enderror"
                                            name="email" id="firstname" placeholder="Enter your email"
                                            value="{{ Auth::user()->email ?? 'N/A' }}">
                                        @error('email')
                                        <span class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div
                                    class="mt-6 pt-4 border-t border-gray-200/60 dark:border-gray-700/60 flex flex-wrap items-center justify-end gap-3">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        All changes are saved immediately
                                    </p>
                                    <button
                                        class="submit btn btn-primary inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-medium rounded-xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/40 transition-all duration-200 hover:shadow-indigo-300/50 dark:hover:shadow-indigo-800/30 hover:-translate-y-0.5 focus:ring-4 focus:ring-indigo-300/50"
                                        type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
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
                <div class="tab-pane hidden" id="updatePassword" role="tabpanel">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-gray-100/40 dark:shadow-gray-900/30 border border-gray-100/50 dark:border-gray-700/50 overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30 flex items-center gap-3">
                            <div class="p-2 bg-amber-100 dark:bg-amber-900/40 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h5 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Change Password</h5>
                            <span
                                class="ml-auto text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-600">secure</span>
                        </div>
                        <div class="p-6 sm:p-8">
                            <form class="form form-horizontal" method="post"
                                action="{{ route('admin.setting.profile.update.password') }}">
                                @csrf
                                @method('PUT')
                                <div class="space-y-5">
                                    <div class="form-group space-y-1.5">
                                        <label for="old_password"
                                            class="form-label text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Current Password <span class="text-red-500">*</span>
                                        </label>
                                        <input type="password"
                                            class="form-control w-full rounded-xl border-gray-200 dark:border-gray-600 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 dark:focus:ring-indigo-800/50 transition-all duration-200 @error('old_password') is-invalid @enderror"
                                            name="old_password" placeholder="Enter current password" id="old_password">
                                        @error('old_password')
                                        <span class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-group space-y-1.5">
                                        <label for="password"
                                            class="form-label text-sm font-medium text-gray-700 dark:text-gray-300">
                                            New Password <span class="text-red-500">*</span>
                                        </label>
                                        <input type="password"
                                            class="form-control w-full rounded-xl border-gray-200 dark:border-gray-600 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 dark:focus:ring-indigo-800/50 transition-all duration-200 @error('password') is-invalid @enderror"
                                            name="password" id="password" placeholder="Enter new password">
                                        @error('password')
                                        <span class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-group space-y-1.5">
                                        <label for="password_confirmation"
                                            class="form-label text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Confirm Password <span class="text-red-500">*</span>
                                        </label>
                                        <input type="password"
                                            class="form-control w-full rounded-xl border-gray-200 dark:border-gray-600 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 dark:focus:ring-indigo-800/50 transition-all duration-200 @error('password_confirmation') is-invalid @enderror"
                                            name="password_confirmation" id="password_confirmation"
                                            placeholder="Confirm new password">
                                        @error('password_confirmation')
                                        <span class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Password strength indicator -->
                                <div
                                    class="mt-4 p-4 bg-gray-50/80 dark:bg-gray-800/60 rounded-xl border border-gray-200/60 dark:border-gray-700/60 flex items-start gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-blue-500 dark:text-blue-400 flex-shrink-0 mt-0.5"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        Use at least <strong>8 characters</strong> with a mix of uppercase, lowercase,
                                        numbers, and symbols for a strong password.
                                    </p>
                                </div>

                                <div
                                    class="mt-6 pt-4 border-t border-gray-200/60 dark:border-gray-700/60 flex flex-wrap items-center justify-end gap-3">
                                    <button
                                        class="submit btn btn-primary inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-medium rounded-xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/40 transition-all duration-200 hover:shadow-indigo-300/50 dark:hover:shadow-indigo-800/30 hover:-translate-y-0.5 focus:ring-4 focus:ring-indigo-300/50"
                                        type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                        </svg>
                                        Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info Cards -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg shadow-gray-100/40 dark:shadow-gray-900/30 p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600 dark:text-emerald-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Profile Status</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">All information is up to date
                        </p>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg shadow-gray-100/40 dark:shadow-gray-900/30 p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Security</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Password last changed recently
                        </p>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg shadow-gray-100/40 dark:shadow-gray-900/30 p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 dark:text-purple-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Account</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ Auth::user()->email ?? 'N/A'
                            }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .tab-pane {
            display: block;
        }
        .tab-pane.hidden {
            display: none !important;
        }
        .tab-link.active-tab {
            background-color: #4f46e5 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 6px -1px rgba(99,102,241,0.3);
        }
        .tab-link:not(.active-tab) {
            background-color: transparent;
            color: #4b5563;
        }
        .dark .tab-link:not(.active-tab) {
            color: #d1d5db;
        }
        .tab-link:not(.active-tab):hover {
            background-color: rgba(156,163,175,0.2);
        }
        .dark .tab-link:not(.active-tab):hover {
            background-color: rgba(55,65,81,0.5);
        }
        .form-control.is-invalid {
            border-color: #ef4444;
        }
        .form-control:focus {
            outline: none;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        $(document).ready(function() {
            // ── Tab Switching ────────────────────────────────────────
            function switchTab(targetId) {
                // Hide all panes
                $('.tab-pane').addClass('hidden');
                // Show the target pane
                $('#' + targetId).removeClass('hidden');

                // Reset all tab links to inactive style
                $('.tab-link').removeClass('active-tab bg-indigo-600 text-white shadow-md shadow-indigo-200/50 dark:shadow-indigo-900/40');
                $('.tab-link').addClass('text-gray-600 dark:text-gray-300');

                // Set clicked tab link to active style
                $('.tab-link[data-tab="' + targetId + '"]')
                    .addClass('active-tab bg-indigo-600 text-white shadow-md shadow-indigo-200/50 dark:shadow-indigo-900/40')
                    .removeClass('text-gray-600 dark:text-gray-300');
            }

            // Show Edit Profile tab by default
            switchTab('editProfile');

            // Tab link click handler
            $('.tab-link').on('click', function(e) {
                e.preventDefault();
                var targetId = $(this).data('tab');
                switchTab(targetId);
            });

            // ── If there are password validation errors, switch to password tab ──
            @if($errors->has('old_password') || $errors->has('password') || $errors->has('password_confirmation'))
                switchTab('updatePassword');
            @endif

            // ── Profile Picture Upload ────────────────────────────────
            $('#uploadImageBtn').on('click', function(e) {
                e.preventDefault();
                $('#profile_picture_input').click();
            });

            // Preview image before upload
            $('#profile_picture_input').on('change', function() {
                var file = this.files[0];
                if (!file) return;

                // Instant preview
                var reader = new FileReader();
                reader.onload = function(e) {
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
                    success: function(response) {
                        if (response.success) {
                            $('.profile-img-main img').attr('src', response.image_url);
                            $('.profile-img-change').attr('src', response.image_url);
                            toastr.success('Profile picture updated successfully.');
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('An error occurred while updating the profile picture.');
                    }
                });
            });
        });
    </script>
    @endpush
</x-admin-layout>