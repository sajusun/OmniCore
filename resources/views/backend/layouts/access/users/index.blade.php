@extends('backend.app', ['title' => 'Users'])

@push('styles')
<link href="{{ asset('default/datatable.css') }}" rel="stylesheet" />
@endpush


@section('content')
<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">


            <!-- PAGE-HEADER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Users</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Access</li>
                        <li class="breadcrumb-item">Users</li>
                    </ol>
                </div>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- ROW-4 -->
            <div class="row">
                <div class="col-12 col-sm-12">
                    <div class="card product-sales-main">
                        <div class="card-header border-bottom">
                            <h3 class="card-title mb-0">List</h3>
                            <div class="card-options ms-auto">
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">Add</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="">
                                <table class="table table-bordered text-nowrap border-bottom" id="datatable">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Name</th>
                                            <th>Slug</th>
                                            <th>Roles</th>
                                            <th>Guard</th>
                                            <th>Created</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php $sn = 1; @endphp
                                        @forelse ($users as $user)
                                        <tr>
                                            <td>{{ $sn++ }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->slug }}</td>
                                            <td>
                                                @forelse ($user->roles as $role)
                                                <span class="badge rounded-pill bg-primary">{{ $role->name }}</span>
                                                @empty
                                                <span class="badge rounded-pill bg-primary">N/A</span>
                                                @endforelse
                                            </td>
                                            <td>
                                                @forelse ($user->roles as $role)
                                                <span class="badge rounded-pill bg-primary">{{ $role->guard_name }}</span>
                                                @empty
                                                <span class="badge rounded-pill bg-primary">N/A</span>
                                                @endforelse
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y') }}</td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group" aria-label="Basic example">

                                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>

                                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    
                                                    <a href="{{ route('admin.users.card', $user->slug) }}" class="btn btn-success">
                                                        <i class="mdi mdi-printer"></i>
                                                    </a>

                                                    @can('web_update', $user)
                                                    <a href="{{ route('admin.users.status', $user->id) }}" class="btn btn-warning">
                                                        @if ($user->status == 'active')
                                                        <i class="fa-solid fa-lock-open"></i>
                                                        @else
                                                        <i class="fa-solid fa-lock"></i>
                                                        @endif
                                                    </a>
                                                    @endcan

                                                    @can('web_delete', $user)
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure?')" style="margin: 0; padding: 0;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" style="border-radius: 0; border-top-right-radius: 5px; border-bottom-right-radius: 5px;">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>

                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="100" class="text-center">No Data</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $users->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div><!-- COL END -->
            </div>
            <!-- ROW-4 END -->

        </div>
    </div>
</div>
<!-- CONTAINER CLOSED -->
@endsection

@push('scripts')

@endpush