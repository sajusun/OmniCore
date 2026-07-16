@extends('backend.app')

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
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
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                    <h1 class="mb-0">Products</h1>

                    <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                        Add New Product
                    </a>
                </div>

                <table class="table table-hover" id="products-table">
                    <thead>
                        <tr>
                            <th>UPC</th>
                            <th>Name</th>
                            <th>Verdict</th>
                            <th>Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
    $(function () {
    $('#products-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.products.data') }}",
        columns: [
            { data: 'upc',   name: 'upc' },
            { data: 'name',  name: 'name' },
            { data: 'verdict', name: 'verdict' },
            { data: 'score', name: 'score' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush