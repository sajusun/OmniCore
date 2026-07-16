@extends('backend.app', ['title' => 'Food Log Details'])

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Food Log Details</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.food_logs.index') }}">Food Logs</a></li>
                        <li class="breadcrumb-item active" aria-current="page">#{{ $foodLog->id }}</li>
                    </ol>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            @php
                                $image = $foodLog->image ?: ($foodLog->scan?->image_url ?? null);
                            @endphp
                            @if (!empty($image))
                                <img src="{{ asset($image) }}" alt="Food log image" class="img-fluid rounded shadow-sm" style="max-height: 420px; object-fit: cover;">
                            @else
                                <div class="border rounded p-5 text-muted">No image available</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Food Log Summary </h4>
                                <span class="badge bg-success"> {{ number_format($foodLog->food_score ?? 0, 0) }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="border p-3 h-100">
                                        <small class="text-muted">User</small>
                                        <h6 class="mb-0 mt-1">{{ $foodLog->user?->name ?? 'Unknown User' }}</h6>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border p-3 h-100">
                                        <small class="text-muted">Product</small>
                                        <h6 class="mb-0 mt-1">{{ $foodLog->product_name ?? 'N/A' }}</h6>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border p-3 h-100">
                                        <small class="text-muted">Meal Type</small>
                                        <h6 class="mb-0 mt-1">{{ ucfirst($foodLog->meal_type ?? 'N/A') }}</h6>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border p-3 h-100">
                                        <small class="text-muted">Serving Size</small>
                                        <h6 class="mb-0 mt-1">{{ $foodLog->serving_size ?? 'N/A' }}</h6>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border p-3 h-100">
                                        <small class="text-muted">Calories</small>
                                        <h6 class="mb-0 mt-1">{{ $foodLog->calories ?? 'N/A' }}</h6>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border p-3 h-100">
                                        <small class="text-muted">Logged</small>
                                        <h6 class="mb-0 mt-1">{{ ($foodLog->logged_date ?? $foodLog->created_at)?->format('d M Y') }} {{ ($foodLog->logged_time ?? $foodLog->created_at)?->format('H:i') }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-1">
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">Nutrition Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="border p-2">
                                        <small class="text-muted">Protein</small>
                                        <div class="fw-semibold">{{ $foodLog->protein_g ?? 0 }} g</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border p-2">
                                        <small class="text-muted">Carbs</small>
                                        <div class="fw-semibold">{{ $foodLog->carbs_g ?? 0 }} g</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border p-2">
                                        <small class="text-muted">Fat</small>
                                        <div class="fw-semibold">{{ $foodLog->fat_g ?? 0 }} g</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border p-2">
                                        <small class="text-muted">Food Score</small>
                                        <div class="fw-semibold">{{ $foodLog->food_score ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">Scan Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="border p-2">
                                        <small class="text-muted">Scan ID</small>
                                        <div class="fw-semibold">{{ $foodLog->food_scan_id ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border p-2">
                                        <small class="text-muted">Verdict</small>
                                        <div class="fw-semibold">{{ $foodLog->scan?->verdict_label ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="border p-2">
                                        <small class="text-muted">Insight</small>
                                        <div class="fw-semibold">{{ $foodLog->scan?->insight ?? 'No insight available.' }}</div>
                                    </div>
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
