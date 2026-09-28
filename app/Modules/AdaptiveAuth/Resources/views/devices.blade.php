@extends(view()->exists('layouts.admin') ? 'layouts.admin' : (view()->exists('backend.app') ? 'backend.app' :
'layouts.app'))

@section('content')
<!-- CONTAINER -->
<div class="main-container container-fluid">

    <!-- PAGE-HEADER -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Active Devices & Security</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Recognized Devices</li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    <!-- FLASH NOTIFICATIONS -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span class="alert-inner--icon me-2"><i class="fe fe-check-circle"></i></span>
        <span class="alert-inner--text">{{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span class="alert-inner--icon me-2"><i class="fe fe-alert-triangle"></i></span>
        <span class="alert-inner--text">{{ session('error') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- ROW-1: Security Stat Cards -->
    <div class="row">
        {{-- Total Devices --}}
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h3 class="mb-2 fw-semibold">{{ $devices->where('is_trusted', true)->count() }}</h3>
                            <p class="text-muted fs-13 mb-0">Trusted Devices</p>
                            <small class="text-muted">{{ $devices->count() }} total registered</small>
                        </div>
                        <div class="col col-auto top-icn dash">
                            <div class="counter-icon bg-primary dash ms-auto box-shadow-primary">
                                <i class="fe fe-hard-drive text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Current Session Device --}}
        @php
        $currentDevice = $devices->firstWhere('device_uuid', $currentUuid) ?? $devices->first();
        @endphp
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h3 class="mb-2 fw-semibold text-truncate" style="max-width: 200px;">
                                {{ $currentDevice ? $currentDevice->device_name : 'This Browser' }}
                            </h3>
                            <p class="text-success fs-13 mb-0 fw-semibold">
                                <i class="fe fe-check-circle me-1"></i>Current Active Session
                            </p>
                            <small class="text-muted">{{ $currentDevice?->last_ip ?? request()->ip() }}</small>
                        </div>
                        <div class="col col-auto top-icn dash">
                            <div class="counter-icon bg-success dash ms-auto box-shadow-success">
                                <i class="fe fe-monitor text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Protection Status --}}
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h3 class="mb-2 fw-semibold text-primary">Smart 2FA Active</h3>
                            <p class="text-muted fs-13 mb-0">Adaptive Protection</p>
                            <small class="text-muted">Unrecognized logins require email OTP</small>
                        </div>
                        <div class="col col-auto top-icn dash">
                            <div class="counter-icon bg-info dash ms-auto box-shadow-info">
                                <i class="fe fe-shield text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ROW-1 END -->

    <!-- ROW-2: Devices Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fe fe-cpu me-2 text-primary"></i>Recognized Hardware & Browsers
                    </h3>
                    <div class="card-options">
                        @if ($devices->where('is_trusted', true)->count() > 1)
                        <form method="POST" action="{{ route('adaptive.devices.revoke_others') }}"
                            onsubmit="return confirm('Are you sure you want to revoke access for all other devices? They will be prompted for OTP on next login.');"
                            class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fe fe-log-out me-1"></i>Revoke Other Devices
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered text-nowrap border-bottom mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Device / Browser</th>
                                    <th>Platform</th>
                                    <th>IP Address</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Last Active</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($devices as $device)
                                @php
                                $isCurrent = ($currentUuid && $device->device_uuid === $currentUuid);
                                $icon = match(strtolower($device->device_type ?? 'desktop')) {
                                'mobile' => 'fe-smartphone',
                                'tablet' => 'fe-tablet',
                                default => 'fe-monitor',
                                };
                                @endphp
                                <tr class="{{ $isCurrent ? 'table-success-subtle' : '' }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md br-7 bg-primary-transparent text-primary me-3">
                                                <i class="fe {{ $icon }} fs-18"></i>
                                            </div>
                                            <div>
                                                <span class="fw-semibold text-dark">{{ $device->device_name }}</span>
                                                @if ($isCurrent)
                                                <span class="badge bg-success-transparent text-success ms-2">Current
                                                    Session</span>
                                                @endif
                                                <div class="text-muted fs-12">{{ $device->browser ?? 'Browser' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fs-13 text-dark">{{ $device->platform ?? 'Unknown OS' }}</span>
                                    </td>
                                    <td>
                                        <code class="fs-12 text-primary">{{ $device->last_ip }}</code>
                                    </td>
                                    <td>
                                        <span class="fs-13 text-secondary">
                                            <i class="fe fe-map-pin me-1 text-muted"></i>
                                            {{ trim(($device->city ?? '') . ', ' . ($device->country ?? 'Unknown'), ',
                                            ') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($device->isCurrentlyTrusted())
                                        <span class="badge bg-success-transparent text-success">
                                            <i class="fe fe-check me-1"></i>Trusted
                                        </span>
                                        @else
                                        <span class="badge bg-danger-transparent text-danger">
                                            <i class="fe fe-x me-1"></i>Revoked
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fs-13 text-muted" title="{{ $device->last_active_at }}">
                                            {{ $device->last_active_at ? $device->last_active_at->diffForHumans() :
                                            'Never' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($device->isCurrentlyTrusted() && !$isCurrent)
                                        <form method="POST" action="{{ route('adaptive.devices.revoke', $device->id) }}"
                                            onsubmit="return confirm('Revoke access for {{ $device->device_name }}?');"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fe fe-slash me-1"></i>Revoke
                                            </button>
                                        </form>
                                        @elseif ($isCurrent)
                                        <span class="badge bg-info-transparent text-info">Active Now</span>
                                        @else
                                        <span class="text-muted fs-12">Access Revoked</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fe fe-info fs-24 d-block mb-2 text-muted"></i>
                                        No devices currently registered on this account.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ROW-2 END -->

    <!-- ROW-3: Sign-In Audit Trail -->
    @if ($logs->isNotEmpty())
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fe fe-activity me-2 text-info"></i>Recent Sign-In Audit History
                    </h3>
                    <div class="card-options">
                        <form method="POST" action="{{ route('adaptive.devices.clear_logs') }}"
                            onsubmit="return confirm('Are you sure you want to clear your entire sign-in audit history?');"
                            class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fe fe-trash-2 me-1"></i>Clear Audit History
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover text-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Outcome</th>
                                    <th>Device Name</th>
                                    <th>IP Address</th>
                                    <th>Location</th>
                                    <th>Details / Notes</th>
                                </tr>
                            </thead>
                            <tbody class="fs-13">
                                @foreach ($logs as $log)
                                <tr>
                                    <td class="text-muted">{{ $log->created_at?->format('M d, Y h:i A') }}</td>
                                    <td>
                                        @if ($log->status === 'trusted_login' || $log->status === 'challenge_passed')
                                        <span class="badge bg-success-transparent text-success">
                                            <i class="fe fe-check-circle me-1"></i>Success
                                        </span>
                                        @elseif ($log->status === 'challenge_issued')
                                        <span class="badge bg-warning-transparent text-warning">
                                            <i class="fe fe-alert-circle me-1"></i>2FA Prompt
                                        </span>
                                        @else
                                        <span class="badge bg-danger-transparent text-danger">
                                            <i class="fe fe-x-circle me-1"></i>Challenge Failed
                                        </span>
                                        @endif
                                    </td>
                                    <td class="fw-medium text-dark">{{ $log->device_name ?? 'Unknown Device' }}</td>
                                    <td><code>{{ $log->ip_address }}</code></td>
                                    <td class="text-muted">{{ $log->location ?? 'Unknown' }}</td>
                                    <td class="text-muted fs-12">{{ $log->failure_reason ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- ROW-3 END -->

</div>
<!-- CONTAINER END -->
@endsection