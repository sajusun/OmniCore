<x-admin-layout>
    <x-slot name="title">Support #{{ $report->ticket_no }} — {{ $report->subject }}</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Support Report Details</h2>
    </x-slot>

    <!-- Page Header / Breadcrumb -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.app-supports.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius: 0;">
                <i class="fa fa-arrow-left me-1"></i> Back
            </a>
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.app-supports.index') }}" class="text-decoration-none text-muted">App Support</a></li>
                        <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">#{{ $report->ticket_no }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="fw-bold text-dark mb-0">{{ $report->subject }}</h4>
                    @php
                        $badgeBg = match($report->status?->value ?? $report->status) {
                            'pending'     => 'bg-warning text-dark',
                            'in_progress' => 'bg-info text-white',
                            'replied'     => 'bg-primary text-white',
                            'resolved'    => 'bg-success text-white',
                            'closed'      => 'bg-secondary text-white',
                            default       => 'bg-light text-dark',
                        };
                    @endphp
                    <span class="badge {{ $badgeBg }} px-2 py-1" style="border-radius: 0;">
                        {{ $report->status?->label() ?? ucfirst($report->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Status Change Form -->
        <form action="{{ route('admin.app-supports.status', $report->id) }}" method="POST" class="d-flex align-items-center gap-2">
            @csrf
            @method('PATCH')
            <select name="status" class="form-select form-select-sm" style="width: auto; border-radius: 0;">
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}" {{ ($report->status?->value ?? $report->status) === $status->value ? 'selected' : '' }}>
                        Mark as: {{ $status->label() }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-dark text-nowrap" style="border-radius: 0;">
                Update Status
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 0;">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 0;">
            <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">

        <!-- Left Column: Details, Timeline, Reply Composer -->
        <div class="col-lg-8">

            <!-- Original User Issue Card -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                <div class="card-header bg-transparent border-bottom pt-3 pb-3 px-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                            {{ strtoupper(substr($report->user->name ?? 'U', 0, 2)) }}
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">{{ $report->user->name ?? 'User' }}</h6>
                            <small class="text-muted">Reported {{ $report->created_at->format('M d, Y \a\t h:i A') }} ({{ $report->created_at->diffForHumans() }})</small>
                        </div>
                    </div>
                    <span class="badge bg-light text-dark border" style="border-radius: 0;">
                        <i class="fa fa-tag me-1"></i> {{ $report->category?->label() ?? ucfirst($report->category) }}
                    </span>
                </div>

                <div class="card-body p-4">
                    <h6 class="fw-semibold text-muted text-uppercase small mb-2">Issue Description:</h6>
                    <div class="p-3 bg-light text-dark" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;">
                        {{ $report->message }}
                    </div>

                    <!-- Attached Screenshots / Media -->
                    @if($report->media && $report->media->count() > 0)
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="fw-bold text-dark small text-uppercase mb-3">
                                <i class="fa fa-paperclip me-1"></i> Attached Screenshots / Files ({{ $report->media->count() }})
                            </h6>
                            <div class="row g-3">
                                @foreach($report->media as $media)
                                    @php
                                        $isImage = in_array(strtolower($media->extension ?? ''), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                    @endphp
                                    <div class="col-6 col-sm-4 col-md-3">
                                        <a href="{{ $media->full_url }}" target="_blank" class="card border h-100 text-decoration-none shadow-none hover-shadow" style="border-radius: 0;">
                                            @if($isImage)
                                                <div style="height: 110px; overflow: hidden; background: #f8f9fa;" class="d-flex align-items-center justify-content-center">
                                                    <img src="{{ $media->full_url }}" alt="{{ $media->original_name }}" class="w-100 h-100" style="object-fit: cover;">
                                                </div>
                                            @else
                                                <div style="height: 110px; background: #f8f9fa;" class="d-flex align-items-center justify-content-center text-muted fs-1">
                                                    <i class="fa fa-file-alt"></i>
                                                </div>
                                            @endif
                                            <div class="card-body p-2 text-center">
                                                <div class="text-truncate small fw-medium text-dark" title="{{ $media->original_name }}">
                                                    {{ $media->original_name }}
                                                </div>
                                                <small class="text-muted" style="font-size: 10px;">
                                                    {{ number_format(($media->size ?? 0) / 1024, 1) }} KB &bull; View <i class="fa fa-external-link-alt"></i>
                                                </small>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Responses Timeline -->
            @if($report->replies->count() > 0)
                <div class="mb-4">
                    <h6 class="fw-bold text-dark text-uppercase small mb-3">
                        <i class="fa fa-history me-1"></i> Responses History ({{ $report->replies->count() }})
                    </h6>

                    <div class="d-flex flex-column gap-3">
                        @foreach($report->replies as $reply)
                            @if($reply->sender_type === 'system')
                                <!-- System Message -->
                                <div class="alert alert-light border border-dashed mb-0 d-flex align-items-start gap-2" style="border-radius: 0;">
                                    <i class="fa fa-robot fs-5 text-muted mt-1"></i>
                                    <div>
                                        <strong class="text-dark">System Automated Confirmation</strong>
                                        <small class="text-muted ms-2">{{ $reply->created_at->format('M d, h:i A') }}</small>
                                        <p class="mb-0 text-muted small mt-1">{{ $reply->message }}</p>
                                    </div>
                                </div>
                            @elseif($reply->sender_type === 'admin')
                                <!-- Admin Reply -->
                                <div class="card border-0 shadow-sm border-start border-4 border-primary" style="border-radius: 0;">
                                    <div class="card-header bg-transparent border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-primary text-uppercase" style="border-radius: 0;">Admin Reply</span>
                                            <strong class="text-dark">{{ $reply->author->name ?? 'Administrator' }}</strong>
                                        </div>
                                        <small class="text-muted">{{ $reply->created_at->format('M d, Y \a\t h:i A') }}</small>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="text-dark" style="white-space: pre-wrap; line-height: 1.6;">
                                            {{ $reply->message }}
                                        </div>

                                        @if($reply->media && $reply->media->count() > 0)
                                            <div class="mt-2 pt-2 border-top">
                                                @foreach($reply->media as $rMedia)
                                                    <a href="{{ $rMedia->full_url }}" target="_blank" class="small text-primary me-3 text-decoration-none">
                                                        <i class="fa fa-paperclip"></i> {{ $rMedia->original_name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <!-- User Reply (Future Ready) -->
                                <div class="card border-0 shadow-sm" style="border-radius: 0;">
                                    <div class="card-header bg-transparent border-bottom py-2 px-3 d-flex justify-content-between align-items-center">
                                        <strong class="text-dark">{{ $reply->author->name ?? 'User' }}</strong>
                                        <small class="text-muted">{{ $reply->created_at->format('M d, Y \a\t h:i A') }}</small>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="text-dark" style="white-space: pre-wrap;">
                                            {{ $reply->message }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Reply Composer Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 0;">
                <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="fa fa-reply me-1 text-primary"></i> Send Response to User
                    </h5>
                    <small class="text-primary fw-semibold">
                        <i class="fa fa-bell me-1"></i> Push Notification + <i class="fa fa-envelope me-1"></i> Email
                    </small>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.app-supports.reply', $report->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">
                                Reply Message <span class="text-danger">*</span>
                            </label>
                            <textarea name="message" rows="5" required 
                                      placeholder="Type your resolution or response here. It will immediately trigger an email and in-app push notification to the user..."
                                      class="form-control" style="border-radius: 0;"></textarea>
                            @error('message')
                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row g-3 align-items-end mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Update Report Status to:</label>
                                <select name="status" class="form-select" style="border-radius: 0;">
                                    <option value="replied" selected>Replied (Keep Open)</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Attach Files (Optional):</label>
                                <input type="file" name="attachments[]" multiple class="form-control form-control-sm" style="border-radius: 0;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end pt-2 border-top">
                            <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 0;">
                                <i class="fa fa-paper-plane me-1"></i> Send Reply & Notify User
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <!-- Right Column: User Info & Device Info -->
        <div class="col-lg-4">

            <!-- User Profile Card -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                <div class="card-header bg-transparent border-bottom py-3 px-4">
                    <h6 class="card-title mb-0 fw-bold text-dark text-uppercase small">
                        <i class="fa fa-user me-1"></i> User Information
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                        <div class="avatar avatar-lg bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 52px; height: 52px;">
                            {{ strtoupper(substr($report->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">{{ $report->user->name ?? 'Deleted User' }}</h6>
                            <small class="text-muted">User ID: #{{ $report->user->id ?? 'N/A' }}</small>
                        </div>
                    </div>

                    <ul class="list-unstyled mb-0 small">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Email:</span>
                            <span class="fw-semibold text-dark">{{ $report->user->email ?? 'N/A' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Phone:</span>
                            <span class="fw-semibold text-dark">{{ $report->user->phone ?? 'N/A' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">Joined Date:</span>
                            <span class="fw-semibold text-dark">{{ $report->user->created_at ? $report->user->created_at->format('M d, Y') : 'N/A' }}</span>
                        </li>
                    </ul>

                    @if(!empty($report->user))
                        <div class="mt-3 pt-2 text-center">
                            <a href="{{ route('admin.users.show', $report->user->id) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100" style="border-radius: 0;">
                                View Full User Profile <i class="fa fa-external-link-alt ms-1"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Device Diagnostics Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 0;">
                <div class="card-header bg-transparent border-bottom py-3 px-4">
                    <h6 class="card-title mb-0 fw-bold text-dark text-uppercase small">
                        <i class="fa fa-mobile-alt me-1"></i> Device Diagnostics
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-2 small">
                        <div class="p-2 bg-light d-flex justify-content-between align-items-center">
                            <span class="text-muted">Operating System:</span>
                            <strong class="text-dark">{{ $report->device_os ?: 'Not provided' }}</strong>
                        </div>
                        <div class="p-2 bg-light d-flex justify-content-between align-items-center">
                            <span class="text-muted">Device Model:</span>
                            <strong class="text-dark">{{ $report->device_model ?: 'Not provided' }}</strong>
                        </div>
                        <div class="p-2 bg-light d-flex justify-content-between align-items-center">
                            <span class="text-muted">App Version:</span>
                            <strong class="text-primary font-monospace">{{ $report->app_version ?: 'N/A' }}</strong>
                        </div>
                        <div class="p-2 bg-light d-flex justify-content-between align-items-center">
                            <span class="text-muted">Submitted At:</span>
                            <strong class="text-dark">{{ $report->created_at->format('M d, Y h:i A') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-admin-layout>
