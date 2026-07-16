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
                    <div class="card">
                        <div class="card-header border-bottom d-flex justify-content-between">
                            <h3 class="card-title">Profile Information</h3>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary"><i class="fa fa-edit"></i> Edit User</a>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-2 text-center">
                                    <img src="{{ $user->avatar ? asset($user->avatar) : asset('default/profile.jpg') }}" class="avatar-xxl brround cover-image" alt="user profile">
                                </div>
                                <div class="col-md-10">
                                    <h4>{{ $user->name }}</h4>
                                    <p class="text-muted">{{ $user->email }}</p>
                                    <span class="badge bg-primary">{{ $user->getRoleNames()->first() ?? 'No Role' }}</span>
                                    <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($user->status) }}</span>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="200">Full Name</th>
                                        <td>{{ $user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email Address</th>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Role</th>
                                        <td>{{ $user->getRoleNames()->first() ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{{ ucfirst($user->status) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Joined Date</th>
                                        <td>{{ $user->created_at->format('d M Y, h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
