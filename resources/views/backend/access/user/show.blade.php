<x-admin-layout>
    <x-slot name="title">User Details - {{ $user->name }}</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">User Details</h2>
    </x-slot>

    {{-- Breadcrumb & Navigation --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted">Users</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">{{ $user->name }}</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">User Profile Hub</h4>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-info">
                <i class="fa fa-edit me-1"></i> Edit Profile
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-1"></i> Back to Users List
            </a>
        </div>
    </div>

    {{-- Profile Banner Card --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4">
                @php
                    $avatarUrl = !empty($user->avatar)
                        ? (filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : asset($user->avatar))
                        : asset('default/profile.png');
                @endphp
                <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="rounded-circle border p-1" style="width: 100px; height: 100px; object-fit: cover;" onError="this.onerror=null;this.src='{{ asset('default/profile.png') }}';">
                
                <div class="flex-grow-1 text-center text-md-start">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-2">
                        <div>
                            <h3 class="fw-bold text-dark mb-1">{{ $user->name }}</h3>
                            <p class="text-muted mb-0"><i class="fa fa-envelope me-1"></i> {{ $user->email }}</p>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <span class="badge bg-primary px-3 py-2 fs-6 me-2">{{ $user->getRoleNames()->first() ?? 'User' }}</span>
                            <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }} px-3 py-2 fs-6">{{ ucfirst($user->status) }}</span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-4 pt-3 border-top text-muted small">
                        <span><i class="fa fa-calendar me-1"></i> Registered: {{ $user->created_at ? $user->created_at->format('d M, Y (h:i A)') : 'N/A' }}</span>
                        <span><i class="fa fa-clock me-1"></i> Last Activity: {{ $user->last_activity_at ? $user->last_activity_at->diffForHumans() : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Resource Action Cards (Posts, Events, Clubs, Vehicles) --}}
    <h5 class="fw-bold text-dark mb-3"><i class="fa fa-th-large me-2" style="color: #8fbd56;"></i> Related User Content & Activity</h5>
    
    <div class="row g-4 mb-4">
        {{-- Posts Card --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 0; border-top: 4px solid #4f46e5 !important;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">User Posts</span>
                            <h2 class="fw-bold text-dark mb-0 mt-1">{{ $postsCount }}</h2>
                        </div>
                        <div class="p-3 bg-indigo-light text-indigo rounded-circle">
                            <i class="fa fa-newspaper fs-3" style="color: #4f46e5;"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.posts.index', ['user' => $user->id]) }}" class="btn btn-primary w-100 mt-2 fw-semibold">
                        <i class="fa fa-eye me-1"></i> View User Posts
                    </a>
                </div>
            </div>
        </div>

        {{-- Events Card --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 0; border-top: 4px solid #10b981 !important;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">User Events</span>
                            <h2 class="fw-bold text-dark mb-0 mt-1">{{ $user->events_count ?? 0 }}</h2>
                        </div>
                        <div class="p-3 bg-success-light text-success rounded-circle">
                            <i class="fa fa-calendar-days fs-3" style="color: #10b981;"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.events.index', ['user' => $user->id]) }}" class="btn btn-success w-100 mt-2 fw-semibold">
                        <i class="fa fa-eye me-1"></i> View User Events
                    </a>
                </div>
            </div>
        </div>

        {{-- Clubs Card --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 0; border-top: 4px solid #06b6d4 !important;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">User Clubs</span>
                            <h2 class="fw-bold text-dark mb-0 mt-1">{{ $user->clubs_count ?? 0 }}</h2>
                        </div>
                        <div class="p-3 bg-info-light text-info rounded-circle">
                            <i class="fa fa-shield-halved fs-3" style="color: #06b6d4;"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.clubs.index', ['user' => $user->id]) }}" class="btn btn-info w-100 mt-2 fw-semibold">
                        <i class="fa fa-eye me-1"></i> View User Clubs
                    </a>
                </div>
            </div>
        </div>

        {{-- Vehicles Card --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 0; border-top: 4px solid #f59e0b !important;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">User Vehicles</span>
                            <h2 class="fw-bold text-dark mb-0 mt-1">{{ $user->vehicles_count ?? 0 }}</h2>
                        </div>
                        <div class="p-3 bg-warning-light text-warning rounded-circle">
                            <i class="fa fa-car fs-3" style="color: #f59e0b;"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.vehicles.index', ['user' => $user->id]) }}" class="btn btn-warning w-100 mt-2 fw-semibold">
                        <i class="fa fa-eye me-1"></i> View User Vehicles
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Details Summary Table --}}
    <div class="card border-0 shadow-sm" style="border-radius: 0;">
        <div class="card-header bg-transparent border-bottom py-3">
            <h6 class="card-title mb-0 fw-bold text-dark">Detailed Account Overview</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <tbody>
                        <tr>
                            <th style="width: 220px;" class="bg-light ps-4">User ID</th>
                            <td>#{{ $user->id }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light ps-4">Full Name</th>
                            <td class="fw-semibold text-dark">{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light ps-4">Email Address</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light ps-4">Slug</th>
                            <td><code>{{ $user->slug ?? 'N/A' }}</code></td>
                        </tr>
                        <tr>
                            <th class="bg-light ps-4">Assigned Role</th>
                            <td><span class="badge bg-primary">{{ $user->getRoleNames()->first() ?? 'User' }}</span></td>
                        </tr>
                        <tr>
                            <th class="bg-light ps-4">Account Status</th>
                            <td><span class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($user->status) }}</span></td>
                        </tr>
                        <tr>
                            <th class="bg-light ps-4">Email Verification</th>
                            <td>
                                @if($user->isEmailVerified())
                                    <span class="badge bg-success"><i class="fa fa-check me-1"></i> Verified</span>
                                @else
                                    <span class="badge bg-warning"><i class="fa fa-clock me-1"></i> Unverified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light ps-4">Subscription Status</th>
                            <td>
                                @if($user->is_subscribed)
                                    <span class="badge bg-success"><i class="fa fa-star me-1"></i> Subscribed</span>
                                    <small class="text-muted ms-2">(Ends: {{ $user->subscription_ends_at ? $user->subscription_ends_at->format('d M, Y') : 'N/A' }})</small>
                                @else
                                    <span class="badge bg-secondary">Free Plan</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light ps-4">Registered Date</th>
                            <td>{{ $user->created_at ? $user->created_at->format('d M, Y (h:i A)') : 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
