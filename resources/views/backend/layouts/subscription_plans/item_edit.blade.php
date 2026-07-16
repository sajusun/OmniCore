@extends('backend.app', ['title' => 'Edit Item'])

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Edit Item</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.subscription_plans.items.index', $item->subscription_plan_id) }}">Items</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Edit Item Details</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.subscription_plans.items.update', $item->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group mb-3">
                                    <label class="form-label">Title *</label>
                                    <input type="text" class="form-control" name="title" value="{{ old('title', $item->title) }}" required>
                                </div>
                                <!-- <div class="form-group mb-3">
                                    <label class="form-label">Key</label>
                                    <input type="text" class="form-control" name="key" value="{{ old('key', $item->key) }}">
                                </div> -->
                                <!-- <div class="form-group mb-3">
                                    <label class="form-label">Value</label>
                                    <input type="text" class="form-control" name="value" value="{{ old('value', $item->value) }}">
                                </div> -->
                                <div class="form-group mb-4">
                                    <label class="form-label">Order</label>
                                    <input type="number" class="form-control" name="order" value="{{ old('order', $item->order) }}">
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('admin.subscription_plans.items.index', $item->subscription_plan_id) }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Item</button>
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
