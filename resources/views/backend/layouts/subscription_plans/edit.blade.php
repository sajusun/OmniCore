@extends('backend.app', ['title' => 'Edit Subscription Plan'])

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Edit Subscription Plan</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.subscription_plans.index') }}">Plans</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Plan Details</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.subscription_plans.update', $subscriptionPlan->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row mb-4">
                                    <div class="col-md-6 form-group">
                                        <label for="name" class="form-label">Name *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $subscriptionPlan->name) }}" required>
                                        @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="price" class="form-label">Price *</label>
                                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $subscriptionPlan->price) }}" required>
                                        @error('price')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>
                                    <!-- <div class="col-md-6 form-group">
                                        <label for="billing_cycle" class="form-label">Billing Cycle *</label>
                                        <select class="form-control @error('billing_cycle') is-invalid @enderror" id="billing_cycle" name="billing_cycle" required>
                                            <option value="monthly" {{ old('billing_cycle', $subscriptionPlan->billing_cycle) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                            <option value="yearly" {{ old('billing_cycle', $subscriptionPlan->billing_cycle) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                            <option value="lifetime" {{ old('billing_cycle', $subscriptionPlan->billing_cycle) == 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                                        </select>
                                        @error('billing_cycle')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div> -->
                                    <div class="col-md-6 form-group">
                                        <label for="title" class="form-label">Title / Description</label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $subscriptionPlan->title) }}">
                                        @error('title')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="logo" class="form-label">Logo</label>
                                        @if($subscriptionPlan->logo)
                                            <div class="mb-2">
                                                <img src="{{ asset($subscriptionPlan->logo) }}" alt="Logo" width="50" height="50" class="rounded">
                                            </div>
                                        @endif
                                        <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                                        @error('logo')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div>
                                    <!-- <div class="col-md-6 form-group">
                                        <label for="bg" class="form-label">Background Image (bg)</label>
                                        @if($subscriptionPlan->bg)
                                            <div class="mb-2">
                                                <img src="{{ asset($subscriptionPlan->bg) }}" alt="BG" width="50" height="50" class="rounded">
                                            </div>
                                        @endif
                                        <input type="file" class="form-control @error('bg') is-invalid @enderror" id="bg" name="bg" accept="image/*">
                                        @error('bg')<span class="text-danger">{{ $message }}</span>@enderror
                                    </div> -->
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('admin.subscription_plans.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Plan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
