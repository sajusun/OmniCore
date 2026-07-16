@extends('backend.app')

@section('content')
<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

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

            <!-- ROW-1: User Stats by Type -->
            <div class="row">
                <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h3 class="mb-2 fw-semibold">{{ $totalUsers ?? 0 }}</h3>
                                    <p class="text-muted fs-13 mb-0">Total Registered Users</p>
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
                <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h3 class="mb-2 fw-semibold">{{ $totalSubscribedUsers ?? 0 }}</h3>
                                    <p class="text-muted fs-13 mb-0">Subscribed Users</p>
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

                <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h3 class="mb-2 fw-semibold">{{ $totalFoodScans ?? 0 }}</h3>
                                    <p class="text-muted fs-13 mb-0">Total Food Scans</p>
                                </div>
                                <div class="col col-auto top-icn dash">
                                    <div class="counter-icon bg-warning dash ms-auto box-shadow-warning">
                                        <i class="fe fe-bar-chart-2 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-12 col-md-6 col-xl-3">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h3 class="mb-2 fw-semibold">{{ $totalFoodLogs ?? 0 }}</h3>
                                    <p class="text-muted fs-13 mb-0">Total Food Logs</p>
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


            <!-- ROW-3: Recent Data Sections -->
            <div class="row">
                <div class="col-sm-12 col-md-12 col-xl-6">
                    <div class="card overflow-hidden">
                        <div class="card-header border-bottom">
                            <h4 class="card-title fw-semibold">Recent Registered Users</h4>
                        </div>
                        <div class="card-body p-0 customers mt-1">
                            <div class="list-group py-1">
                                @forelse ($latestpostUsers as $postUser)
                                <a href="javascript:void(0);" class="border-0">
                                    <div class="list-group-item border-0">
                                        <div class="media mt-0 align-items-center">
                                            <div
                                                class="transaction-icon bg-primary-transparent text-primary brround me-3">
                                                <i class="fe fe-user"></i>
                                            </div>
                                            <div class="media-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="mt-0">
                                                        <h5 class="mb-1 fs-13 fw-semibold text-dark">
                                                            {{ $postUser->name }}
                                                        </h5>
                                                        <p class="mb-0 fs-12 text-muted">
                                                            {{ $postUser->email }}
                                                        </p>
                                                    </div>
                                                    <span class="ms-auto fs-13">
                                                        <span class="float-end text-dark">{{
                                                            $postUser->created_at->diffForHumans() }}</span>
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
                <div class="col-sm-12 col-md-12 col-xl-6">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title fw-semibold mb-0">System Activity</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                @if($activityLogs->count())
                                    <ul class="task-list">
                                        @foreach($activityLogs as $log)
                                            <li class="d-flex align-items-start mb-3">
                                                <i class="task-icon bg-success"></i>
                                                <div class="flex-grow-1">
                                                    <p class="fw-semibold mb-1 fs-13">{{ $log->title }}</p>
                                                    <p class="text-muted fs-12 mb-0">{{ $log->description }}</p>
                                                    <p class="text-muted fs-12 mb-0"><small>{{ $log->created_at->format('Y-m-d H:i') }}</small></p>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">No recent system activity.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ROW-3 END -->

        </div>
    </div>
</div>
<!-- CONTAINER CLOSED -->
@endsection

@push('scripts')
@endpush