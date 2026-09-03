<x-admin-layout>
    <x-slot name="title">
        Activity Log Information
    </x-slot>

    <x-slot name="header">
        Activity Log Information
    </x-slot>

    <div class="row g-4 mb-4">

        <!-- General Info Card -->
        <div class="col-12 col-lg-8">
            <x-card title="General Information" style="border-radius: 0;">
                <div class="row g-3">
                    <div class="col-6">
                        <span class="d-block text-uppercase small fw-semibold text-muted mb-1">Activity ID</span>
                        <span class="fw-semibold">#{{ $activityLog->id }}</span>
                    </div>
                    <div class="col-6">
                        <span class="d-block text-uppercase small fw-semibold text-muted mb-1">Event</span>
                        @php
                        $badgeClass = match($activityLog->event) {
                            'created'                  => 'bg-success',
                            'updated'                  => 'bg-primary',
                            'deleted', 'force_deleted' => 'bg-danger',
                            'restored'                 => 'bg-warning text-dark',
                            'login'                    => 'bg-info text-dark',
                            'logout'                   => 'bg-secondary',
                            default                    => 'bg-light text-dark border',
                        };
                        @endphp
                        <span class="badge {{ $badgeClass }}" style="border-radius: 0;">
                            {{ ucfirst($activityLog->event) }}
                        </span>
                    </div>
                    <div class="col-6">
                        <span class="d-block text-uppercase small fw-semibold text-muted mb-1">Module</span>
                        <span class="fw-medium">{{ $activityLog->module }}</span>
                    </div>
                    <div class="col-6">
                        <span class="d-block text-uppercase small fw-semibold text-muted mb-1">Created At</span>
                        <span>
                            {{ $activityLog->created_at->format('Y-m-d H:i:s') }}
                            <span class="text-muted small">({{ $activityLog->created_at->diffForHumans() }})</span>
                        </span>
                    </div>
                    <div class="col-12">
                        <span class="d-block text-uppercase small fw-semibold text-muted mb-1">Description</span>
                        <div class="p-3 bg-light border fw-medium small" style="border-radius: 0;">
                            {{ $activityLog->description }}
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- User Information Card -->
        <div class="col-12 col-lg-4">
            <x-card title="User (Actor)" style="border-radius: 0;">
                @if($activityLog->user)
                <div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold"
                             style="width: 42px; height: 42px; font-size: 0.85rem; flex-shrink: 0; border-radius: 0;">
                            {{ strtoupper(substr($activityLog->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">{{ $activityLog->user->name }}</h6>
                            <p class="mb-0 text-muted small">{{ $activityLog->user->email }}</p>
                        </div>
                    </div>
                    <hr />
                    <div class="mb-2">
                        <span class="d-block text-uppercase small fw-semibold text-muted mb-1">User ID</span>
                        <span class="font-monospace">#{{ $activityLog->user_id }}</span>
                    </div>
                    <div>
                        <a href="{{ route('admin.users.activity-logs', $activityLog->user_id) }}"
                            class="small text-primary text-decoration-none">
                            View User Activity History <i class="fa fa-arrow-right ms-1" style="font-size: 0.7rem;"></i>
                        </a>
                    </div>
                </div>
                @else
                <div class="d-flex flex-column align-items-center justify-content-center py-4 text-muted">
                    <i class="fa fa-robot fs-2 mb-2 opacity-50"></i>
                    <span class="fw-semibold">System / Guest</span>
                    <span class="small">No authenticated user associated.</span>
                </div>
                @endif
            </x-card>
        </div>

    </div>

    <div class="row g-4 mb-4">

        <!-- Subject Info Card -->
        <div class="col-12 col-lg-4">
            <x-card title="Subject (Target Object)" style="border-radius: 0;">
                <div class="d-flex flex-column gap-3">
                    <div>
                        <span class="d-block text-uppercase small fw-semibold text-muted mb-1">Subject Type</span>
                        <span class="font-monospace small">{{ $activityLog->subject_type ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="d-block text-uppercase small fw-semibold text-muted mb-1">Subject ID</span>
                        <span class="font-monospace small">{{ $activityLog->subject_id ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="d-block text-uppercase small fw-semibold text-muted mb-1">Subject Status</span>
                        <div class="mt-1">
                            @if($activityLog->subject)
                            <span class="d-inline-flex align-items-center gap-1 fw-semibold text-success small">
                                <span class="bg-success d-inline-block" style="width: 8px; height: 8px;"></span>
                                Record Active
                            </span>
                            @php
                            $subjectRoute = null;
                            try {
                                if ($activityLog->subject_type === 'App\Models\User') {
                                    $subjectRoute = route('admin.users.edit', $activityLog->subject_id);
                                }
                            } catch (\Exception $e) {}
                            @endphp
                            @if($subjectRoute)
                            <div class="mt-2">
                                <a href="{{ $subjectRoute }}"
                                    class="small fw-semibold text-primary text-decoration-none">
                                    View Related Record <i class="fa fa-external-link-alt ms-1" style="font-size: 0.65rem;"></i>
                                </a>
                            </div>
                            @endif
                            @else
                            <span class="d-inline-flex align-items-center gap-1 fw-semibold text-danger small">
                                <span class="bg-danger d-inline-block" style="width: 8px; height: 8px;"></span>
                                Record No Longer Exists
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Request Details -->
        <div class="col-12 col-lg-8">
            <x-card title="Request Context" style="border-radius: 0;">
                <div class="row g-3">
                    <div class="col-6">
                        <span class="d-block text-uppercase small fw-semibold text-muted mb-1">IP Address</span>
                        <span class="font-monospace small">{{ $activityLog->ip_address ?? 'N/A' }}</span>
                    </div>
                    <div class="col-6">
                        <span class="d-block text-uppercase small fw-semibold text-muted mb-1">HTTP Method</span>
                        <span class="badge bg-primary text-uppercase fw-bold mt-1" style="border-radius: 0;">
                            {{ $activityLog->method ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="col-12">
                        <span class="d-block text-uppercase small fw-semibold text-muted mb-1">Request URL</span>
                        <span class="font-monospace small text-break user-select-all">{{ $activityLog->url ?? 'N/A' }}</span>
                    </div>
                    <div class="col-12">
                        <span class="d-block text-uppercase small fw-semibold text-muted mb-1">User Agent</span>
                        <div class="p-2 bg-light border font-monospace small text-break user-select-all mt-1" style="line-height: 1.6; border-radius: 0;">
                            {{ $activityLog->user_agent ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

    </div>

    <!-- Trace Context -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <x-card title="Tracing & Batch Audit" style="border-radius: 0;">
                <span class="d-block text-uppercase small fw-semibold text-muted mb-1">Batch UUID</span>
                <div class="p-2 bg-light border font-monospace fw-bold small user-select-all text-break mt-1 d-inline-block" style="border-radius: 0;">
                    {{ $activityLog->batch_uuid ?? 'N/A' }}
                </div>
            </x-card>
        </div>
    </div>

    <!-- Data Delta Changes -->
    <div class="row g-4 mb-4">

        <!-- Old Values -->
        <div class="col-12 col-lg-6">
            <x-card title="Old Values (Before Change)" style="border-radius: 0;">
                @if(!empty($activityLog->old_values))
                <pre class="text-xs font-monospace bg-dark text-success p-3 overflow-auto user-select-all" style="max-height: 24rem; font-size: 0.78rem; border-radius: 0;"><code>{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                @else
                <div class="text-center py-5 text-muted fst-italic small">
                    No old values recorded.
                </div>
                @endif
            </x-card>
        </div>

        <!-- New Values -->
        <div class="col-12 col-lg-6">
            <x-card title="New Values (After Change)" style="border-radius: 0;">
                @if(!empty($activityLog->new_values))
                <pre class="font-monospace bg-dark text-info p-3 overflow-auto user-select-all" style="max-height: 24rem; font-size: 0.78rem; border-radius: 0;"><code>{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                @else
                <div class="text-center py-5 text-muted fst-italic small">
                    No new values recorded.
                </div>
                @endif
            </x-card>
        </div>

    </div>

    <!-- Properties -->
    <div class="row g-4">
        <div class="col-12">
            <x-card title="Extended Properties / Meta" style="border-radius: 0;">
                @if(!empty($activityLog->properties))
                <pre class="font-monospace bg-dark text-warning p-3 overflow-auto user-select-all" style="max-height: 24rem; font-size: 0.78rem; border-radius: 0;"><code>{{ json_encode($activityLog->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                @else
                <div class="text-center py-5 text-muted fst-italic small">
                    No properties/meta recorded.
                </div>
                @endif
            </x-card>
        </div>
    </div>

</x-admin-layout>
