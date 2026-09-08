@extends('layouts.admin')

@section('content')
    <!-- CONTAINER -->
    <div class="main-container container-fluid">
        <!-- PAGE-HEADER -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Dashboard</h1>
            </div>
            <div class="ms-auto pageheader-btn">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </div>
        </div>
        <!-- PAGE-HEADER END -->

        <!-- ROW-1: 4 Primary Stat Cards -->
        <div class="row">

            {{-- Total Users --}}
            <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ number_format($totalUsers ?? 0) }}</h3>
                                <p class="text-muted fs-13 mb-0">Total Registered Users</p>
                                <small class="text-success fw-semibold">
                                    <i class="fe fe-trending-up me-1"></i>{{ $newUsers ?? 0 }} this month
                                </small>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-primary dash ms-auto box-shadow-primary">
                                    <i class="fe fe-users text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Subscribed Users --}}
            <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ number_format($totalSubscribedUsers ?? 0) }}</h3>
                                <p class="text-muted fs-13 mb-0">Subscribed Users</p>
                                <small class="text-muted">
                                    {{ $verifiedUsers ?? 0 }} email verified
                                </small>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-success dash ms-auto box-shadow-success">
                                    <i class="fe fe-bell text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Orders --}}
            <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ number_format($totalOrders ?? $totalEvents ?? 0) }}</h3>
                                <p class="text-muted fs-13 mb-0">Total Orders</p>
                                <small class="text-warning fw-semibold">
                                    <i class="fe fe-clock me-1"></i>{{ $pendingOrders ?? $upcomingEvents ?? 0 }} pending
                                </small>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-warning dash ms-auto box-shadow-warning">
                                    <i class="fe fe-shopping-bag text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Posts --}}
            <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ number_format($totalPosts ?? 0) }}</h3>
                                <p class="text-muted fs-13 mb-0">Total Posts</p>
                                <small class="text-info fw-semibold">
                                    <i class="fe fe-file-text me-1"></i>{{ $newPostsMonth ?? 0 }} this month
                                </small>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-info dash ms-auto box-shadow-info">
                                    <i class="fe fe-file-text text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- ROW-1 END -->

        <!-- ROW-2: Secondary Stat Cards (Vendors, Products, Published Posts, Active Users) -->
        <div class="row">

            {{-- Total Vendors --}}
            <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ number_format($totalVendors ?? $totalClubs ?? 0) }}</h3>
                                <p class="text-muted fs-13 mb-0">Total Vendors</p>
                                <small class="text-muted">{{ number_format($totalReviews ?? $totalClubMembers ?? 0) }} reviews</small>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-purple dash ms-auto" style="background: #7c3aed!important;">
                                    <i class="fe fe-briefcase text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Products --}}
            <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ number_format($totalProducts ?? $totalVehicles ?? 0) }}</h3>
                                <p class="text-muted fs-13 mb-0">Total Products</p>
                                <small class="text-muted">In Catalog</small>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon dash ms-auto" style="background: #f59e0b!important;">
                                    <i class="fe fe-box text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Published Posts --}}
            <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ number_format($publishedPosts ?? 0) }}</h3>
                                <p class="text-muted fs-13 mb-0">Published Posts</p>
                                <small class="text-muted">of {{ number_format($totalPosts ?? 0) }} total</small>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-success dash ms-auto box-shadow-success">
                                    <i class="fe fe-edit text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Online / Active Users --}}
            <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3 class="mb-2 fw-semibold">{{ number_format($activeCount ?? 0) }}</h3>
                                <p class="text-muted fs-13 mb-0">Online Users <small>(last 5 min)</small></p>
                                <small class="text-muted">{{ number_format($inactiveCount ?? 0) }} offline</small>
                            </div>
                            <div class="col col-auto top-icn dash">
                                <div class="counter-icon bg-primary dash ms-auto box-shadow-primary">
                                    <i class="fe fe-activity text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- ROW-2 END -->

        <!-- CHARTS ROW 1: Monthly Growth & Subscription Ratio -->
        <div class="row">
            <!-- Monthly Growth & Subscriptions Overview (Area Chart) -->
            <div class="col-lg-12 col-xl-8">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header border-bottom bg-transparent py-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">
                            <i class="fe fe-trending-up me-2" style="color: #8fbd56;"></i> Monthly Activity & Subscription Growth
                        </h5>
                    </div>
                    <div class="card-body">
                        <x-chart 
                            type="area" 
                            :height="320"
                            chartId="monthly-growth-chart"
                            :categories="$signupCategories"
                            :series="[
                                ['name' => 'New Users', 'data' => $signupData],
                                ['name' => 'Paid Subscriptions', 'data' => $subscriptionChartData],
                                ['name' => 'Events Created', 'data' => $eventChartData],
                                ['name' => 'Posts Published', 'data' => $postChartData]
                            ]" 
                        />
                    </div>
                </div>
            </div>

            <!-- Subscription Ratio (Donut Chart) -->
            <div class="col-lg-12 col-xl-4">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header border-bottom bg-transparent py-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">
                            <i class="fe fe-pie-chart me-2" style="color: #10b981;"></i> Subscription Plan Ratio
                        </h5>
                    </div>
                    <div class="card-body">
                        <x-chart 
                            type="donut" 
                            :height="320"
                            chartId="subscription-ratio-chart"
                            :categories="['Subscribed Users', 'Free Plan Users']"
                            :series="[(int)$totalSubscribedUsers, (int)$freeUsersCount]" 
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- CHARTS ROW 2: Resource Breakdown & Active Status -->
        <div class="row">
            <!-- System Resources Breakdown (Bar Chart) -->
            <div class="col-lg-12 col-xl-6">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header border-bottom bg-transparent py-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">
                            <i class="fe fe-bar-chart-2 me-2" style="color: #4f46e5;"></i> System Resources Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <x-chart 
                            type="bar" 
                            :height="300"
                            chartId="system-resources-chart"
                            :categories="['Users', 'Posts', 'Orders', 'Vendors', 'Products']"
                            :series="[
                                ['name' => 'Total Count', 'data' => [(int)$totalUsers, (int)$totalPosts, (int)($totalOrders ?? $totalEvents ?? 0), (int)($totalVendors ?? $totalClubs ?? 0), (int)($totalProducts ?? $totalVehicles ?? 0)]]
                            ]" 
                        />
                    </div>
                </div>
            </div>

            <!-- User Verification Status Overview (Donut Chart) -->
            <div class="col-lg-12 col-xl-6">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header border-bottom bg-transparent py-3">
                        <h5 class="card-title fw-bold mb-0 text-dark">
                            <i class="fe fe-check-circle me-2" style="color: #10b981;"></i> User Account Verification Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <x-chart 
                            type="donut" 
                            :height="300"
                            chartId="user-verification-chart"
                            :categories="['Email Verified Users', 'Unverified Users']"
                            :series="[(int)$verifiedUsers, (int)$unverifiedUsersCount]" 
                        />
                    </div>
                </div>
            </div>
        </div>
        <!-- CHARTS ROW END -->

        <!-- ROW-3: Recent Users + Recent Events -->
        <div class="row">

            {{-- Recent Registered Users --}}
            <div class="col-sm-12 col-md-12 col-xl-6">
                <div class="card overflow-hidden border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                        <h4 class="card-title fw-semibold mb-0">Recent Registered Users</h4>
                        <span class="badge bg-primary-transparent text-primary fs-12">Last 5</span>
                    </div>
                    <div class="card-body p-0 customers mt-1">
                        <div class="list-group py-1">
                            @forelse ($latestpostUsers as $postUser)
                                <a href="{{ route('admin.users.show', $postUser->id) }}" class="border-0">
                                    <div class="list-group-item border-0">
                                        <div class="media mt-0 align-items-center">
                                            <div class="transaction-icon bg-primary-transparent text-primary brround me-3">
                                                <i class="fe fe-user"></i>
                                            </div>
                                            <div class="media-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="mt-0">
                                                        <h5 class="mb-1 fs-13 fw-semibold text-dark">{{ $postUser->name }}</h5>
                                                        <p class="mb-0 fs-12 text-muted">{{ $postUser->email }}</p>
                                                    </div>
                                                    <span class="ms-auto fs-12 text-muted">
                                                        {{ $postUser->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <p class="text-muted fs-13 text-center py-3">No Users Found</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Events --}}
            <div class="col-sm-12 col-md-12 col-xl-6">
                <div class="card overflow-hidden border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                        <h4 class="card-title fw-semibold mb-0">Recent Events</h4>
                        <span class="badge bg-warning-transparent text-warning fs-12">Last 5</span>
                    </div>
                    <div class="card-body p-0 mt-1">
                        <div class="list-group py-1">
                            @forelse ($recentEvents as $event)
                                <div class="list-group-item border-0">
                                    <div class="media mt-0 align-items-center">
                                        <div class="transaction-icon bg-warning-transparent text-warning brround me-3">
                                            <i class="fe fe-calendar"></i>
                                        </div>
                                        <div class="media-body">
                                            <div class="d-flex align-items-center">
                                                <div class="mt-0">
                                                    <h5 class="mb-1 fs-13 fw-semibold text-dark">{{ $event->title }}</h5>
                                                    <p class="mb-0 fs-12 text-muted">
                                                        {{ $event->location }}
                                                        &bull;
                                                        <span class="text-primary">
                                                            {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
                                                        </span>
                                                    </p>
                                                </div>
                                                <span class="ms-auto">
                                                    @if($event->status === 'published')
                                                        <span class="badge bg-success-transparent text-success fs-11">Published</span>
                                                    @elseif($event->status === 'cancelled')
                                                        <span class="badge bg-danger-transparent text-danger fs-11">Cancelled</span>
                                                    @else
                                                        <span class="badge bg-secondary-transparent text-secondary fs-11">{{ ucfirst($event->status) }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted fs-13 text-center py-3">No Events Found</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- ROW-3 END -->

        <!-- ROW-4: Recent Posts -->
        <div class="row">
            {{-- Recent Posts --}}
            <div class="col-sm-12 col-md-12 col-xl-12">
                <div class="card overflow-hidden border-0 shadow-sm mb-4" style="border-radius: 0;">
                    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                        <h4 class="card-title fw-semibold mb-0">Recent Posts</h4>
                        <span class="badge bg-info-transparent text-info fs-12">Last 5</span>
                    </div>
                    <div class="card-body p-0 mt-1">
                        <div class="list-group py-1">
                            @forelse ($recentPosts as $post)
                                <div class="list-group-item border-0">
                                    <div class="media mt-0 align-items-center">
                                        <div class="transaction-icon bg-info-transparent text-info brround me-3">
                                            <i class="fe fe-file-text"></i>
                                        </div>
                                        <div class="media-body">
                                            <div class="d-flex align-items-center">
                                                <div class="mt-0">
                                                    <h5 class="mb-1 fs-13 fw-semibold text-dark">
                                                        {{ Str::limit($post->title ?? 'Untitled', 45) }}
                                                    </h5>
                                                    <p class="mb-0 fs-12 text-muted">
                                                        by {{ $post->user?->name ?? 'Unknown' }}
                                                        &bull; {{ $post->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                                <span class="ms-auto d-flex align-items-center gap-2">
                                                    <a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-sm btn-outline-info" title="View Details"><i class="fa fa-eye"></i></a>
                                                    @if($post->status === 'published')
                                                        <span class="badge bg-success-transparent text-success fs-11">Published</span>
                                                    @else
                                                        <span class="badge bg-secondary-transparent text-secondary fs-11">{{ ucfirst($post->status ?? 'Draft') }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted fs-13 text-center py-3">No Posts Found</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ROW-4 END -->

    </div>
    <!-- CONTAINER CLOSED -->
@endsection

@push('scripts')
@endpush