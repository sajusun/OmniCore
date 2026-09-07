@extends('backend.app', ['title' => 'Subscription Plans'])

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Subscription Plans</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active" aria-current="page">Subscription Plans</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-sm-12">
                    <div class="card product-sales-main">
                        <div class="card-header border-bottom">
                            <h3 class="card-title mb-0">Plans List</h3>
                            <div class="card-options ms-auto">
                                <a href="{{ route('admin.subscription_plans.create') }}" class="btn btn-primary btn-sm">Add New Plan</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom" id="datatable">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Logo</th>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Billing Cycle</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $sn = 1; @endphp
                                        @forelse ($plans as $plan)
                                        <tr>
                                            <td>{{ $sn++ }}</td>
                                            <td>
                                                @if($plan->logo)
                                                    <img src="{{ asset($plan->logo) }}" alt="Logo" width="40" height="40" class="">
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $plan->name }}</td>
                                            <td>${{ number_format($plan->price, 2) }}</td>
                                            <td>{{ ucfirst($plan->billing_cycle) }}</td>
                                            <td>
                                                <span class="badge {{ $plan->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.subscription_plans.items.index', $plan->id) }}" class="btn btn-info btn-sm" title="Manage Items">
                                                        <i class="fa-solid fa-list"></i> Items
                                                    </a>
                                                    <a href="{{ route('admin.subscription_plans.edit', $plan->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    <a href="{{ route('admin.subscription_plans.status', $plan->id) }}" class="btn btn-warning btn-sm" title="Toggle Status">
                                                        @if ($plan->is_active)
                                                        <i class="fa-solid fa-lock-open"></i>
                                                        @else
                                                        <i class="fa-solid fa-lock"></i>
                                                        @endif
                                                    </a>
                                                    <form action="{{ route('admin.subscription_plans.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Are you sure?')" style="margin: 0; padding: 0; display: inline;">
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
                                            <td colspan="7" class="text-center">No Data Found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $plans->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
