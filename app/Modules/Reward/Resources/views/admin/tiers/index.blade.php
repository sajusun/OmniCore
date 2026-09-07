<x-admin-layout>
    @slot('title')
        Reward Tiers & Points
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Loyalty Reward Tiers & Multipliers</h4>
                <p class="text-muted small mb-0">Configure gamification tier progressions, points multipliers, and cashback percentages.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.reward.badges.index') }}" class="btn btn-outline-primary px-3">
                    <i class="bi bi-award me-1"></i> Badges
                </a>
                <a href="{{ route('admin.reward.leaderboard.index') }}" class="btn btn-primary px-3 shadow-sm">
                    <i class="bi bi-trophy me-1"></i> Leaderboard
                </a>
            </div>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Top Metrics --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <x-stat-card 
                    title="Total Loyalty Points Awarded" 
                    value="{{ number_format($totalPointsIssued) }} pts" 
                    color="primary" 
                    icon="<i class='bi bi-stars fs-3'></i>" 
                />
            </div>
            <div class="col-md-6">
                <x-stat-card 
                    title="Total Points Redeemed to Cash" 
                    value="{{ number_format(abs($totalRedeemedPoints)) }} pts" 
                    color="emerald" 
                    icon="<i class='bi bi-currency-dollar fs-3'></i>" 
                />
            </div>
        </div>

        {{-- Tiers Grid --}}
        <div class="row g-4">
            @foreach($tiers as $tier)
                <div class="col-md-3">
                    <x-card title="Tier {{ $loop->iteration }}: {{ $tier->name }}" class="h-100">
                        <form action="{{ route('admin.reward.tiers.update', $tier->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Tier Name</label>
                                <input type="text" name="name" value="{{ $tier->name }}" class="form-control form-control-sm">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Min Lifetime Points</label>
                                <input type="number" name="min_points" value="{{ $tier->min_points }}" class="form-control form-control-sm">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Point Multiplier (e.g. 1.25x)</label>
                                <input type="number" step="0.05" name="point_multiplier" value="{{ $tier->point_multiplier }}" class="form-control form-control-sm">
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-semibold">Cashback %</label>
                                <input type="number" step="0.5" name="cashback_percentage" value="{{ $tier->cashback_percentage }}" class="form-control form-control-sm">
                            </div>

                            <button type="submit" class="btn btn-sm btn-primary w-100 shadow-sm">
                                <i class="bi bi-check2 me-1"></i> Update Tier
                            </button>
                        </form>
                    </x-card>
                </div>
            @endforeach
        </div>
    </div>
</x-admin-layout>
