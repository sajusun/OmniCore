<x-admin-layout>
    @slot('title')
        Loyalty Leaderboard
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">User Streaks & Points Leaderboard</h4>
                <p class="text-muted small mb-0">Live community rankings by accumulated reward points and consecutive daily check-ins.</p>
            </div>
            <a href="{{ route('admin.reward.tiers.index') }}" class="btn btn-outline-secondary px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Tiers
            </a>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        <div class="row g-4">
            {{-- Points Leaderboard --}}
            <div class="col-md-6">
                <x-card title="Top Points Earners">
                    <x-table>
                        <thead>
                            <tr>
                                <x-table.th>Rank</x-table.th>
                                <x-table.th>User</x-table.th>
                                <x-table.th class="text-end">Points</x-table.th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topUsers as $userReward)
                                <tr>
                                    <x-table.td>
                                        @if($loop->iteration === 1)
                                            <span class="badge bg-warning text-dark"><i class="bi bi-trophy-fill"></i> #1</span>
                                        @elseif($loop->iteration === 2)
                                            <span class="badge bg-secondary text-white">#2</span>
                                        @elseif($loop->iteration === 3)
                                            <span class="badge bg-danger text-white">#3</span>
                                        @else
                                            <span class="text-muted small">#{{ $loop->iteration }}</span>
                                        @endif
                                    </x-table.td>
                                    <x-table.td class="fw-bold text-dark">{{ $userReward->user?->name ?? 'N/A' }}</x-table.td>
                                    <x-table.td class="text-end fw-bold text-primary">{{ number_format($userReward->current_points) }} pts</x-table.td>
                                </tr>
                            @empty
                                <x-empty-state colspan="3" title="No Points Recorded" description="No users have earned reward points yet." />
                            @endforelse
                        </tbody>
                    </x-table>
                </x-card>
            </div>

            {{-- Streak Leaderboard --}}
            <div class="col-md-6">
                <x-card title="Top Daily Check-in Streaks">
                    <x-table>
                        <thead>
                            <tr>
                                <x-table.th>Rank</x-table.th>
                                <x-table.th>User</x-table.th>
                                <x-table.th class="text-end">Streak</x-table.th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topStreaks as $userReward)
                                <tr>
                                    <x-table.td>
                                        @if($loop->iteration === 1)
                                            <span class="badge bg-warning text-dark"><i class="bi bi-fire"></i> #1</span>
                                        @else
                                            <span class="text-muted small">#{{ $loop->iteration }}</span>
                                        @endif
                                    </x-table.td>
                                    <x-table.td class="fw-bold text-dark">{{ $userReward->user?->name ?? 'N/A' }}</x-table.td>
                                    <x-table.td class="text-end fw-bold text-warning">🔥 {{ $userReward->streak_days }} Days</x-table.td>
                                </tr>
                            @empty
                                <x-empty-state colspan="3" title="No Active Streaks" description="No users have started daily check-in streaks." />
                            @endforelse
                        </tbody>
                    </x-table>
                </x-card>
            </div>
        </div>
    </div>
</x-admin-layout>
