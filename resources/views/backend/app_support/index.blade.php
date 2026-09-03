<x-admin-layout>
    <x-slot name="title">App Support & Feedback</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">App Support & Feedback</h2>
    </x-slot>

    <!-- Page Header / Breadcrumb -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">App Support</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">App Support & Issue Reports</h4>
        </div>
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

    <!-- Status Tabs / Counters -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-sm-4 col-lg-2">
            <a href="{{ route('admin.app-supports.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center py-3 {{ empty($filters['status']) ? 'bg-primary text-white' : 'bg-white text-dark' }}" style="border-radius: 0;">
                    <small class="text-uppercase fw-semibold opacity-75" style="font-size: 11px;">All Reports</small>
                    <h3 class="fw-bold mb-0 mt-1">{{ $counts['all'] ?? 0 }}</h3>
                </div>
            </a>
        </div>
        <div class="col-6 col-sm-4 col-lg-2">
            <a href="{{ route('admin.app-supports.index', ['status' => 'pending']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center py-3 {{ ($filters['status'] ?? '') === 'pending' ? 'bg-warning text-dark' : 'bg-white text-dark' }}" style="border-radius: 0;">
                    <small class="text-uppercase fw-semibold opacity-75" style="font-size: 11px;">Pending</small>
                    <h3 class="fw-bold mb-0 mt-1">{{ $counts['pending'] ?? 0 }}</h3>
                </div>
            </a>
        </div>
        <div class="col-6 col-sm-4 col-lg-2">
            <a href="{{ route('admin.app-supports.index', ['status' => 'in_progress']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center py-3 {{ ($filters['status'] ?? '') === 'in_progress' ? 'bg-info text-white' : 'bg-white text-dark' }}" style="border-radius: 0;">
                    <small class="text-uppercase fw-semibold opacity-75" style="font-size: 11px;">In Progress</small>
                    <h3 class="fw-bold mb-0 mt-1">{{ $counts['in_progress'] ?? 0 }}</h3>
                </div>
            </a>
        </div>
        <div class="col-6 col-sm-4 col-lg-2">
            <a href="{{ route('admin.app-supports.index', ['status' => 'replied']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center py-3 {{ ($filters['status'] ?? '') === 'replied' ? 'bg-primary text-white' : 'bg-white text-dark' }}" style="border-radius: 0;">
                    <small class="text-uppercase fw-semibold opacity-75" style="font-size: 11px;">Replied</small>
                    <h3 class="fw-bold mb-0 mt-1">{{ $counts['replied'] ?? 0 }}</h3>
                </div>
            </a>
        </div>
        <div class="col-6 col-sm-4 col-lg-2">
            <a href="{{ route('admin.app-supports.index', ['status' => 'resolved']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center py-3 {{ ($filters['status'] ?? '') === 'resolved' ? 'bg-success text-white' : 'bg-white text-dark' }}" style="border-radius: 0;">
                    <small class="text-uppercase fw-semibold opacity-75" style="font-size: 11px;">Resolved</small>
                    <h3 class="fw-bold mb-0 mt-1">{{ $counts['resolved'] ?? 0 }}</h3>
                </div>
            </a>
        </div>
        <div class="col-6 col-sm-4 col-lg-2">
            <a href="{{ route('admin.app-supports.index', ['status' => 'closed']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm text-center py-3 {{ ($filters['status'] ?? '') === 'closed' ? 'bg-secondary text-white' : 'bg-white text-dark' }}" style="border-radius: 0;">
                    <small class="text-uppercase fw-semibold opacity-75" style="font-size: 11px;">Closed</small>
                    <h3 class="fw-bold mb-0 mt-1">{{ $counts['closed'] ?? 0 }}</h3>
                </div>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.app-supports.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small mb-1">Search Keyword / Ticket</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" 
                           placeholder="Ticket #, subject, or user name..." 
                           class="form-control" style="border-radius: 0;">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted fw-semibold small mb-1">Category</label>
                    <select name="category" class="form-select" style="border-radius: 0;">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->value }}" {{ ($filters['category'] ?? '') === $category->value ? 'selected' : '' }}>
                                {{ $category->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted fw-semibold small mb-1">Status</label>
                    <select name="status" class="form-select" style="border-radius: 0;">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ ($filters['status'] ?? '') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill" style="border-radius: 0;">
                        <i class="fa fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.app-supports.index') }}" class="btn btn-outline-secondary" style="border-radius: 0;">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Reports Table Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-header bg-transparent border-0 pt-3 pb-0 px-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">
                {{ !empty($filters['status']) ? ucfirst(str_replace('_', ' ', $filters['status'])) . ' Reports' : 'All Support Reports' }}
            </h5>
            <span class="badge bg-light text-dark" style="border-radius: 0;">{{ $reports->total() }} Total</span>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap border-bottom mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 130px;">Ticket #</th>
                            <th>User</th>
                            <th>Category & Subject</th>
                            <th>Device Info</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end" style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td>
                                    <span class="fw-bold text-primary font-monospace">
                                        {{ $report->ticket_no }}
                                    </span>
                                    @if($report->media->count() > 0)
                                        <span class="badge bg-secondary ms-1" style="border-radius: 0;" title="{{ $report->media->count() }} attachment(s)">
                                            <i class="fa fa-paperclip"></i> {{ $report->media->count() }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="fw-semibold text-dark">
                                        {{ $report->user->name ?? 'Deleted User' }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $report->user->email ?? 'N/A' }}
                                    </small>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border mb-1" style="border-radius: 0;">
                                        {{ $report->category?->label() ?? ucfirst($report->category) }}
                                    </span>
                                    <div class="fw-medium text-dark text-truncate" style="max-width: 280px;">
                                        {{ $report->subject }}
                                    </div>
                                </td>

                                <td>
                                    @if($report->device_os || $report->app_version)
                                        <div class="small fw-semibold text-dark">{{ $report->device_os ?? 'OS N/A' }} ({{ $report->app_version ?? 'v?' }})</div>
                                        <small class="text-muted">{{ $report->device_model ?? '' }}</small>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>

                                <td>
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
                                </td>

                                <td>
                                    <div class="small text-dark">{{ $report->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $report->created_at->format('h:i A') }}</small>
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('admin.app-supports.show', $report->id) }}" 
                                       class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1" style="border-radius: 0;">
                                        <span>Review & Reply</span>
                                        <i class="fa fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa fa-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    No support reports found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reports->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $reports->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
