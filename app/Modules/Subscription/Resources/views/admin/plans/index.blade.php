<x-admin-layout>
    @slot('title')
        Subscription Plans & Quotas
    @endslot

    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Subscription Plans & Feature Quotas</h4>
                <p class="text-muted small mb-0">Define SaaS subscription tiers, periodic pricing, quotas, and feature entitlements.</p>
            </div>
            <a href="{{ route('admin.subscription.subscriptions.index') }}" class="btn btn-primary px-3 shadow-sm">
                <i class="bi bi-people-fill me-1"></i> Active Subscribers
            </a>
        </div>

        {{-- Status Notification Modal --}}
        <x-modal.status />

        {{-- Top Metrics --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <x-stat-card 
                    title="Active Subscribers" 
                    value="{{ number_format($totalSubscribers) }}" 
                    color="primary" 
                    icon="<i class='bi bi-person-check-fill fs-3'></i>" 
                />
            </div>
            <div class="col-md-6">
                <x-stat-card 
                    title="Estimated Monthly Recurring Revenue (MRR)" 
                    value="${{ number_format($monthlyRevenue, 2) }}" 
                    color="emerald" 
                    icon="<i class='bi bi-cash-stack fs-3'></i>" 
                />
            </div>
        </div>

        {{-- Create Plan Card --}}
        <x-card title="Create New Subscription Plan" class="mb-4">
            <form action="{{ route('admin.subscription.plans.store') }}" method="POST">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Plan Name</label>
                        <input type="text" name="name" required placeholder="e.g. Starter, Pro Tier, Enterprise" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Price (USD)</label>
                        <input type="number" step="0.01" name="price" required placeholder="29.99 (0 for Free)" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Duration (Days)</label>
                        <input type="number" name="duration_days" value="30" required class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Description / Value Proposition</label>
                    <textarea name="description" rows="2" placeholder="Brief summary of plan highlights..." class="form-control"></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured">
                        <label class="form-check-label fw-semibold" for="is_featured">Mark as Featured / Most Popular</label>
                    </div>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Save Plan
                    </button>
                </div>
            </form>
        </x-card>

        {{-- Existing Plans Grid --}}
        <div class="row g-4">
            @forelse($plans as $plan)
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-light {{ $plan->is_featured ? 'border border-primary border-2' : '' }}">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="fw-bold mb-0 text-dark">{{ $plan->name }}</h5>
                                    @if($plan->is_featured)
                                        <x-badge color="primary">Featured</x-badge>
                                    @endif
                                </div>
                                <p class="text-muted small mb-3">{{ $plan->description ?? 'Standard access plan.' }}</p>

                                <div class="d-flex align-items-baseline mb-4">
                                    <h3 class="fw-bold mb-0 text-dark">${{ number_format($plan->price, 2) }}</h3>
                                    <span class="text-muted small ms-1">/ {{ $plan->duration_days }} days</span>
                                </div>

                                <div class="border-top pt-3">
                                    <h6 class="text-uppercase text-muted fw-bold small mb-2">Quotas & Features</h6>
                                    <ul class="list-unstyled mb-0">
                                        @forelse($plan->features as $feat)
                                            <li class="d-flex justify-content-between align-items-center py-1 small">
                                                <span class="text-dark"><i class="bi bi-check-circle-fill text-success me-1"></i> {{ str_replace('_', ' ', ucfirst($feat->feature_key)) }}</span>
                                                <span class="badge bg-light text-primary border">{{ $feat->quota_limit ?? 'Unlimited' }}</span>
                                            </li>
                                        @empty
                                            <li class="text-muted small">Standard basic features</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>

                            <div class="border-top pt-3 mt-4 d-flex justify-content-between align-items-center">
                                <span class="text-muted small">{{ $plan->subscriptions->count() }} active subscribers</span>
                                <form action="{{ route('admin.subscription.plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <x-card>
                        <x-empty-state title="No Subscription Plans" description="Get started by creating your first subscription tier above." />
                    </x-card>
                </div>
            @endforelse
        </div>
    </div>
</x-admin-layout>
