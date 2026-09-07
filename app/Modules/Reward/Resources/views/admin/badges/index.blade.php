<x-admin-layout>
    @slot('title')
        Achievement Badges
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Gamification Achievement Badges</h4>
                <p class="text-muted small mb-0">Create unlockable badges, milestone criteria, and track user achievements.</p>
            </div>
            <a href="{{ route('admin.reward.tiers.index') }}" class="btn btn-outline-secondary px-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Tiers
            </a>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Add Badge Form --}}
        <x-card title="Add New Achievement Badge" class="mb-4">
            <form action="{{ route('admin.reward.badges.store') }}" method="POST">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Badge Name</label>
                        <input type="text" name="name" required placeholder="e.g. 7-Day Streaker, Top Spender" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Slug Identifier</label>
                        <input type="text" name="slug" required placeholder="e.g. 7-day-streaker" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Criteria Type</label>
                        <select name="criteria_type" class="form-select">
                            <option value="streak">Daily Check-in Streak</option>
                            <option value="points">Total Points Earned</option>
                            <option value="orders">Completed Orders</option>
                            <option value="reviews">Verified Reviews</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Target Threshold Value</label>
                        <input type="number" name="criteria_value" value="7" required class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <input type="text" name="description" placeholder="Awarded for logging in 7 consecutive days." class="form-control">
                </div>

                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Save Badge
                </button>
            </form>
        </x-card>

        {{-- Badges Directory --}}
        <x-card title="Badges Directory">
            <div class="row g-3">
                @forelse($badges as $badge)
                    <div class="col-md-4">
                        <div class="p-3 bg-light border d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">{{ $badge->name }}</h6>
                                <p class="text-muted small mb-2">{{ $badge->description }}</p>
                                <div>
                                    <x-badge color="info">Target: {{ $badge->criteria_value }} {{ ucfirst($badge->criteria_type) }}</x-badge>
                                    <span class="text-muted small ms-2">{{ $badge->users_count ?? 0 }} Unlocked</span>
                                </div>
                            </div>
                            <form action="{{ route('admin.reward.badges.destroy', $badge->id) }}" method="POST" onsubmit="return confirm('Delete badge?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <x-empty-state title="No Badges Configured" description="Create your first gamification badge above." />
                    </div>
                @endforelse
            </div>
        </x-card>
    </div>
</x-admin-layout>
