@extends('backend.app', ['title' => 'Manage Items'])

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Items for: {{ $plan->name }}</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.subscription_plans.index') }}">Plans</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Items</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <!-- Add New Item Form -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Add New Item</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.subscription_plans.items.store', $plan->id) }}" method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="form-label">Title *</label>
                                    <input type="text" class="form-control" name="title" required placeholder="e.g. Unlimited Scans">
                                </div>
                                <!-- <div class="form-group mb-3">
                                    <label class="form-label">Key</label>
                                    <input type="text" class="form-control" name="key" placeholder="e.g. unlimited_scans">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Value</label>
                                    <input type="text" class="form-control" name="value">
                                </div> -->
                                <div class="form-group mb-4">
                                    <label class="form-label">Order</label>
                                    <input type="number" class="form-control" name="order" value="0">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Add Item</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Items List -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">Items List</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Title</th>
                                            <!-- <th>Key</th> -->
                                            <th>Order</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $sn = 1; @endphp
                                        @forelse ($items as $item)
                                        <tr>
                                            <td>{{ $sn++ }}</td>
                                            <td>{{ $item->title }}</td>
                                            <!-- <td>{{ $item->key }}</td> -->
                                            <td>{{ $item->order }}</td>
                                            <td>
                                                <span class="badge {{ $item->status ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $item->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.subscription_plans.items.edit', $item->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    <a href="{{ route('admin.subscription_plans.items.status', $item->id) }}" class="btn btn-warning btn-sm" title="Toggle Status">
                                                        @if ($item->status)
                                                        <i class="fa-solid fa-lock-open"></i>
                                                        @else
                                                        <i class="fa-solid fa-lock"></i>
                                                        @endif
                                                    </a>
                                                    <form action="{{ route('admin.subscription_plans.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure?')" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" style="border-radius: 0; border-top-right-radius: 5px; border-bottom-right-radius: 5px;" title="Delete">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No Items Found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
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
