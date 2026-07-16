@extends('backend.app', ['title' => 'Scan Details'])

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Scan Details</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.scan_histories.index') }}">Scan History</a></li>
                        <li class="breadcrumb-item active" aria-current="page">#{{ $scanHistory->id }}</li>
                    </ol>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            @if (!empty($scanHistory->image_url))
                                <img src="{{ asset($scanHistory->image_url) }}" alt="Food scan" class="img-fluid rounded shadow-sm" style="max-height: 420px; object-fit: cover;">
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
                                <h4 class="mb-0"> Scan Summary </h4>
                                <span class="badge bg-success mx-2"> {{ $scanHistory->ojais_score ?? 0 }}/100</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="border p-3 h-100">
                                        <small class="text-muted">User</small>
                                        <h6 class="mb-0 mt-1">{{ $scanHistory->user?->name ?? 'Unknown User' }}</h6>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border  p-3 h-100">
                                        <small class="text-muted">Product</small>
                                        <h6 class="mb-0 mt-1">{{ $scanHistory->product_name ?? 'N/A' }}</h6>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border p-3 h-100">
                                        <small class="text-muted">Verdict</small>
                                        <h6 class="mb-0 mt-1">{{ $scanHistory->verdict_label ?? 'N/A' }}</h6>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border p-3 h-100">
                                        <small class="text-muted">Portion</small>
                                        <h6 class="mb-0 mt-1">{{ $scanHistory->portion_estimation ?? 'N/A' }}</h6>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border p-3 h-100">
                                        <small class="text-muted">Created</small>
                                        <h6 class="mb-0 mt-1">{{ $scanHistory->created_at?->format('d M Y, H:i') }}</h6>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border p-3 h-100">
                                        <small class="text-muted">Updated</small>
                                        <h6 class="mb-0 mt-1">{{ $scanHistory->updated_at?->format('d M Y, H:i') }}</h6>
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
                            <h5 class="mb-0">Identified Foods</h5>
                        </div>
                        <div class="card-body">
                            @if (!empty($scanHistory->identified_foods) && is_array($scanHistory->identified_foods))
                                <ul class="list-group list-group-flush">
                                    @foreach ($scanHistory->identified_foods as $food)
                                        <li class="list-group-item px-0">{{ is_array($food) ? ($food['name'] ?? json_encode($food)) : $food }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted mb-0">No identified foods available.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">Nutrition</h5>
                        </div>
                        <div class="card-body">
                            @if (!empty($scanHistory->nutrition) && is_array($scanHistory->nutrition))
                                <div class="row g-2">
                                    @foreach ($scanHistory->nutrition as $key => $value)
                                        <div class="col-6">
                                            <div class="border p-2">
                                                <small class="text-muted text-capitalize">{{ str_replace('_', ' ', $key) }}</small>
                                                <div class="fw-semibold">{{ is_numeric($value) ? number_format($value, 2) : $value }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">No nutrition details available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">Insights</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-muted">Insight</h6>
                                <p class="mb-0">{{ $scanHistory->insight ?? 'No insight provided.' }}</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-muted">Team Says</h6>
                                <p class="mb-0">{{ $scanHistory->team_says_text ?? 'No additional notes.' }}</p>
                            </div>
                            <div>
                                <h6 class="text-muted">One Improvement</h6>
                                <p class="mb-0">{{ $scanHistory->one_improvement ?? 'No recommendation available.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
