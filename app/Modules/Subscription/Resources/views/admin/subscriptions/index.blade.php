<x-admin-layout>
    @slot('title')
        Active Subscriptions
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Active Member Subscriptions</h4>
                <p class="text-muted small mb-0">Monitor active customer memberships, subscription validity dates, and auto-renewal states.</p>
            </div>
            <a href="{{ route('admin.subscription.plans.index') }}" class="btn btn-outline-secondary px-3">
                <i class="bi bi-arrow-left me-1"></i> Manage Plans
            </a>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Subscriptions Table --}}
        <x-card title="Subscribers Directory">
            <x-table>
                <thead>
                    <tr>
                        <x-table.th>Subscriber</x-table.th>
                        <x-table.th>Subscribed Plan</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th>Started At</x-table.th>
                        <x-table.th>Expires At</x-table.th>
                        <x-table.th class="text-end">Auto Renew</x-table.th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $sub)
                        <tr>
                            <x-table.td>
                                <div class="fw-bold text-dark">{{ $sub->user?->name ?? 'N/A' }}</div>
                                <div class="text-muted small">{{ $sub->user?->email }}</div>
                            </x-table.td>
                            <x-table.td>
                                <div class="fw-bold text-primary">{{ $sub->plan?->name ?? 'Custom Plan' }}</div>
                                <div class="text-muted small">${{ number_format($sub->plan?->price ?? 0, 2) }}</div>
                            </x-table.td>
                            <x-table.td>
                                @if($sub->status === 'active')
                                    <x-badge color="success">Active</x-badge>
                                @else
                                    <x-badge color="danger">{{ ucfirst($sub->status) }}</x-badge>
                                @endif
                            </x-table.td>
                            <x-table.td class="text-muted small">
                                {{ $sub->starts_at?->format('M d, Y') ?? 'N/A' }}
                            </x-table.td>
                            <x-table.td class="text-muted small">
                                {{ $sub->expires_at?->format('M d, Y') ?? 'Lifetime' }}
                            </x-table.td>
                            <x-table.td class="text-end">
                                @if($sub->auto_renew)
                                    <span class="text-success small fw-semibold"><i class="bi bi-check-circle-fill me-1"></i> Enabled</span>
                                @else
                                    <span class="text-muted small"><i class="bi bi-dash-circle me-1"></i> Disabled</span>
                                @endif
                            </x-table.td>
                        </tr>
                    @empty
                        <x-empty-state colspan="6" title="No Active Subscribers Found" description="There are currently no active user subscriptions recorded." />
                    @endforelse
                </tbody>
            </x-table>

            <div class="mt-3">
                {{ $subscriptions->links() }}
            </div>
        </x-card>
    </div>
</x-admin-layout>
