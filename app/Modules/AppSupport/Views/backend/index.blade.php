<x-admin-layout>
    <x-slot name="title">App Support & Feedback</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">App Support & Feedback</h2>
    </x-slot>

    <div class="container-fluid py-2 px-0">
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

        {{-- Status Notification Modal --}}
        <x-modal.status />

        <!-- Status Counters -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-sm-4 col-lg-2">
                <a href="{{ route('admin.app-supports.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm text-center py-3 {{ empty($filters['status']) ? 'bg-primary text-white' : 'bg-white text-dark' }}">
                        <small class="text-uppercase fw-semibold opacity-75" style="font-size: 11px;">All Reports</small>
                        <h4 class="fw-bold mb-0 mt-1">{{ $counts['all'] ?? 0 }}</h4>
                    </div>
                </a>
            </div>
            <div class="col-6 col-sm-4 col-lg-2">
                <a href="{{ route('admin.app-supports.index', ['status' => 'pending']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm text-center py-3 {{ ($filters['status'] ?? '') === 'pending' ? 'bg-warning text-dark' : 'bg-white text-dark' }}">
                        <small class="text-uppercase fw-semibold opacity-75" style="font-size: 11px;">Pending</small>
                        <h4 class="fw-bold mb-0 mt-1">{{ $counts['pending'] ?? 0 }}</h4>
                    </div>
                </a>
            </div>
            <div class="col-6 col-sm-4 col-lg-2">
                <a href="{{ route('admin.app-supports.index', ['status' => 'in_progress']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm text-center py-3 {{ ($filters['status'] ?? '') === 'in_progress' ? 'bg-info text-white' : 'bg-white text-dark' }}">
                        <small class="text-uppercase fw-semibold opacity-75" style="font-size: 11px;">In Progress</small>
                        <h4 class="fw-bold mb-0 mt-1">{{ $counts['in_progress'] ?? 0 }}</h4>
                    </div>
                </a>
            </div>
            <div class="col-6 col-sm-4 col-lg-2">
                <a href="{{ route('admin.app-supports.index', ['status' => 'resolved']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm text-center py-3 {{ ($filters['status'] ?? '') === 'resolved' ? 'bg-success text-white' : 'bg-white text-dark' }}">
                        <small class="text-uppercase fw-semibold opacity-75" style="font-size: 11px;">Resolved</small>
                        <h4 class="fw-bold mb-0 mt-1">{{ $counts['resolved'] ?? 0 }}</h4>
                    </div>
                </a>
            </div>
            <div class="col-6 col-sm-4 col-lg-2">
                <a href="{{ route('admin.app-supports.index', ['status' => 'closed']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm text-center py-3 {{ ($filters['status'] ?? '') === 'closed' ? 'bg-secondary text-white' : 'bg-white text-dark' }}">
                        <small class="text-uppercase fw-semibold opacity-75" style="font-size: 11px;">Closed</small>
                        <h4 class="fw-bold mb-0 mt-1">{{ $counts['closed'] ?? 0 }}</h4>
                    </div>
                </a>
            </div>
            <div class="col-6 col-sm-4 col-lg-2">
                <a href="{{ route('admin.app-supports.index', ['status' => 'rejected']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm text-center py-3 {{ ($filters['status'] ?? '') === 'rejected' ? 'bg-danger text-white' : 'bg-white text-dark' }}">
                        <small class="text-uppercase fw-semibold opacity-75" style="font-size: 11px;">Rejected</small>
                        <h4 class="fw-bold mb-0 mt-1">{{ $counts['rejected'] ?? 0 }}</h4>
                    </div>
                </a>
            </div>
        </div>

        <!-- Filter Card -->
        <x-card title="Filter Reports" class="mb-4">
            <form method="GET" action="{{ route('admin.app-supports.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Subject, name, email..." value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}" @selected(($filters['type'] ?? '') === $t)>{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold">Priority</label>
                    <select name="priority" class="form-select form-select-sm">
                        <option value="">All Priorities</option>
                        @foreach($priorities as $p)
                            <option value="{{ $p }}" @selected(($filters['priority'] ?? '') === $p)>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                    <a href="{{ route('admin.app-supports.index') }}" class="btn btn-sm btn-light border px-3">Reset</a>
                </div>
            </form>
        </x-card>

        <!-- Reports List Card -->
        <x-card title="App Support Submissions">
            <x-table>
                <thead>
                    <tr>
                        <x-table.th>ID</x-table.th>
                        <x-table.th>User</x-table.th>
                        <x-table.th>Type</x-table.th>
                        <x-table.th>Subject</x-table.th>
                        <x-table.th>Priority</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th>Date</x-table.th>
                        <x-table.th class="text-end">Action</x-table.th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supports as $support)
                        <tr>
                            <x-table.td class="fw-bold text-dark">#{{ $support->id }}</x-table.td>
                            <x-table.td>
                                <div class="fw-bold text-dark">{{ $support->name ?? $support->user?->name ?? 'Guest' }}</div>
                                <div class="text-muted small">{{ $support->email ?? $support->user?->email }}</div>
                            </x-table.td>
                            <x-table.td>
                                <span class="badge bg-light text-dark border">{{ ucfirst(str_replace('_', ' ', $support->type)) }}</span>
                            </x-table.td>
                            <x-table.td>
                                <div class="text-truncate" style="max-width: 250px;" title="{{ $support->subject }}">{{ $support->subject }}</div>
                            </x-table.td>
                            <x-table.td>
                                @php
                                    $prColor = match($support->priority) {
                                        'critical', 'high' => 'danger',
                                        'medium' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <x-badge :color="$prColor">{{ ucfirst($support->priority ?? 'Normal') }}</x-badge>
                            </x-table.td>
                            <x-table.td>
                                @php
                                    $stColor = match($support->status) {
                                        'resolved' => 'success',
                                        'pending' => 'warning',
                                        'in_progress' => 'info',
                                        'rejected' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <x-badge :color="$stColor">{{ ucfirst(str_replace('_', ' ', $support->status)) }}</x-badge>
                            </x-table.td>
                            <x-table.td class="text-muted small">{{ $support->created_at->format('M d, Y') }}</x-table.td>
                            <x-table.td class="text-end">
                                <a href="{{ route('admin.app-supports.show', $support) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> View
                                </a>
                            </x-table.td>
                        </tr>
                    @empty
                        <x-empty-state colspan="8" title="No Support Reports" description="No customer feedback or support reports match your filter." />
                    @endforelse
                </tbody>
            </x-table>

            <div class="mt-3">
                {{ $supports->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
