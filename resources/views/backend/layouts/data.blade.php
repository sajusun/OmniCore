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
            <!-- PAGE-HEADER END -->

            <!-- Data Cards Grid -->
            <div class="row g-4">
                <!-- Product -->
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                    <a href="{{ route('admin.products.index') }}" class="text-decoration-none">
                        <div class="card shadow-sm h-100 border-0 hover-card">
                            <div class="card-body d-flex align-items-center p-4">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                        <i class="fas fa-box text-success fa-2x"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-1 text-dark">Product</h6>
                                    <span class="text-muted small">Manage product catalog</span>
                                </div>
                                <div class="ms-auto">
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Scan -->
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                    <a href="{{ route('admin.scan_histories.index') }}" class="text-decoration-none">
                        <div class="card shadow-sm h-100 border-0 hover-card">
                            <div class="card-body d-flex align-items-center p-4">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                        <i class="fas fa-qrcode text-info fa-2x"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-1 text-dark">Scan</h6>
                                    <span class="text-muted small">View scan history</span>
                                </div>
                                <div class="ms-auto">
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- FoodLog -->
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                    <a href="{{ route('admin.food_logs.index') }}" class="text-decoration-none">
                        <div class="card shadow-sm h-100 border-0 hover-card">
                            <div class="card-body d-flex align-items-center p-4">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                        <i class="fas fa-utensils text-warning fa-2x"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-1 text-dark">FoodLog</h6>
                                    <span class="text-muted small">Track food entries</span>
                                </div>
                                <div class="ms-auto">
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Lab -->
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                    <a href="#" class="text-decoration-none">
                        <div class="card shadow-sm h-100 border-0 hover-card">
                            <div class="card-body d-flex align-items-center p-4">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                                        <i class="fas fa-flask text-danger fa-2x"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-1 text-dark">Lab</h6>
                                    <span class="text-muted small">Manage labs</span>
                                </div>
                                <div class="ms-auto">
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- LabResult -->
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                    <a href="#" class="text-decoration-none">
                        <div class="card shadow-sm h-100 border-0 hover-card">
                            <div class="card-body d-flex align-items-center p-4">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle bg-purple bg-opacity-10 p-3">
                                        <i class="fas fa-file-medical-alt text-purple fa-2x"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-1 text-dark">LabResult</h6>
                                    <span class="text-muted small">View lab results</span>
                                </div>
                                <div class="ms-auto">
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>


                <!-- User -->
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                    <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                        <div class="card shadow-sm h-100 border-0 hover-card">
                            <div class="card-body d-flex align-items-center p-4">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle bg-dark bg-opacity-10 p-3">
                                        <i class="fas fa-users text-dark fa-2x"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-1 text-dark">User</h6>
                                    <span class="text-muted small">Manage users</span>
                                </div>
                                <div class="ms-auto">
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- CONTAINER CLOSED -->
<!-- Custom Styles -->
<style>
    .hover-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .hover-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .hover-card:hover .fas.fa-chevron-right {
        transform: translateX(4px);
        transition: transform 0.3s ease;
    }

    .bg-purple {
        background-color: #6f42c1 !important;
    }

    .text-purple {
        color: #6f42c1 !important;
    }

    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }
</style>
@endsection