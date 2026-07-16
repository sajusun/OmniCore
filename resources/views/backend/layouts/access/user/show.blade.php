@extends('backend.app')

@section('title', 'User Details')

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title">User Details</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                            <h5 class="card-title mb-0 fw-semibold">Profile Information</h5>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-edit me-1"></i> Edit
                            </a>
                        </div>
                        <div class="card-body">
                            <!-- Profile Header -->
                            <div class="d-flex align-items-center gap-4 pb-4 mb-3 border-bottom">
                                <img src="{{ $user->avatar ? asset($user->avatar) : asset('default/profile.jpg') }}"
                                    class="rounded-circle" width="80" height="80" style="object-fit: cover;"
                                    alt="Profile">
                                <div>
                                    <h4 class="mb-1 fw-semibold">{{ $user->name }}</h4>
                                    <p class="text-muted mb-2 small">{{ $user->email }}</p>
                                    <span
                                        class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }} px-3 py-3">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Details Grid -->
                            <div class="row g-0">
                                <div class="col-md-6">
                                    <div class="py-2 px-3">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-1">Full
                                            Name</label>
                                        <span class="text-dark">{{ $user->name }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="py-2 px-3">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-1">Email
                                            Address</label>
                                        <span class="text-dark">{{ $user->email }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="py-2 px-3 border-top">
                                        <label
                                            class="text-muted small text-uppercase fw-semibold d-block mb-1">Status</label>
                                        <span class="text-dark">{{ ucfirst($user->status) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="py-2 px-3 border-top">
                                        <label class="text-muted small text-uppercase fw-semibold d-block mb-1">Joined
                                            Date</label>
                                        <span class="text-dark">{{ $user->created_at->format('d M Y, h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Food Scans --}}
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Recent Food Scans</h4>
                            <a href="{{ route('admin.scan_histories.index',['user'=>$user->id]) }}"
                                class="btn btn-sm btn-outline-primary">
                                View All <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            @if($foodScans->isEmpty())
                            <p class="text-muted">No food scans found.</p>
                            @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Food Name</th>
                                        <th>Scanned At</th>
                                        <th>Ojais Score</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($foodScans as $scan)
                                    <tr>
                                        <td>{{ $scan->id }}</td>
                                        <td>{{ $scan->product_name ?? 'N/A' }}</td>
                                        <td>{{ $scan->created_at->format('d M Y, H:i') }}</td>
                                        <td>{{ $scan->ojais_score ?? 'N/A' }}</td>
                                        <td><a href="{{ route('admin.scan_histories.show', $scan->id) }}">view</>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>
                    </div>

                    {{-- Food Logs --}}
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Recent Food Logs</h4>
                            <a href="{{ route('admin.food_logs.index',['user'=>$user->id]) }}"
                                class="btn btn-sm btn-outline-primary">
                                View All <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            @if($foodLogs->isEmpty())
                            <p class="text-muted">No food logs found.</p>
                            @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Food Name</th>
                                        <th>Meal Type</th>
                                        <th>Calories</th>
                                        <th>Logged At</th>
                                        <th>Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($foodLogs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>{{ $log->product_name }}</td>
                                        <td>{{ $log->meal_type }}</td>
                                        <td>{{ $log->calories }}</td>
                                        <td>{{ $log->created_at->format('d M Y, H:i') }}</td>
                                        <td><a href="{{ route('admin.food_logs.show', $scan->id) }}">view</>
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>
                    </div>

                    {{-- Activity Logs --}}
                    {{-- Activity Logs --}}
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-history me-2"></i>Recent Activity
                            </h4>
                        </div>
                        <div class="card-body p-0">
                            @if($activityLogs->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                <p class="text-muted mb-0">All quiet here</p>
                            </div>
                            @else
                            <div class="list-group list-group-flush">
                                @foreach($activityLogs as $activity)
                                <div class="list-group-item px-4 py-3">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            @php
                                            $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                                            $randomColor = $colors[array_rand($colors)];
                                            @endphp
                                            <div class="avatar-circle bg-{{ $randomColor }} text-white">
                                                {{ strtoupper(substr($activity->title, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h6 class="mb-0">{{ $activity->title }}</h6>
                                                    <small class="text-muted">{{ $activity->description }}</small>
                                                </div>
                                                <small class="text-muted">{{ $activity->created_at->format('M d, H:i A')
                                                    }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
    }
</style>
@endpush