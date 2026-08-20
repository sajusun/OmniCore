<x-admin-layout>
    <x-slot name="title">Vehicle Details - {{ $vehicle->name ?: $vehicle->model }}</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Vehicle Details</h2>
    </x-slot>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vehicles.index') }}" class="text-decoration-none text-muted">Vehicles</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Vehicle #{{ $vehicle->id }}</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Vehicle Details</h4>
        </div>
        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left me-1"></i> Back to Vehicles
        </a>
    </div>

    {{-- Main Summary Card --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start justify-content-between border-bottom pb-4 mb-4 gap-3">
                <div>
                    @php
                        $brandName = is_object($vehicle->brand) ? ($vehicle->brand->value ?? $vehicle->brand->name ?? '') : ($vehicle->brand ?? '');
                    @endphp
                    <h3 class="fw-bold text-dark mb-1">
                        {{ $vehicle->name ?: ($vehicle->year . ' ' . $brandName . ' ' . $vehicle->model) }}
                    </h3>
                    <p class="text-muted mb-0">
                        <i class="fa fa-car me-1 text-primary"></i> 
                        {{ implode(' • ', array_filter([$brandName, $vehicle->model, $vehicle->year])) ?: 'Vehicle Info N/A' }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-info px-3 py-2 fs-6">
                        {{ ucfirst(is_object($vehicle->vehicle_type) ? ($vehicle->vehicle_type->value ?? $vehicle->vehicle_type->name ?? 'Vehicle') : ($vehicle->vehicle_type ?? 'Vehicle')) }}
                    </span>
                    <span class="badge bg-secondary px-3 py-2 fs-6">
                        {{ ucfirst($vehicle->status ?? 'Active') }}
                    </span>
                </div>
            </div>

            {{-- Owner Info Box --}}
            <div class="bg-light p-3 border mb-4" style="border-radius: 0;">
                <h6 class="fw-bold text-dark mb-2"><i class="fa fa-user me-2 text-primary"></i> Vehicle Owner</h6>
                @if($vehicle->user)
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            @php
                                $ownerAvatar = !empty($vehicle->user->avatar)
                                    ? (filter_var($vehicle->user->avatar, FILTER_VALIDATE_URL) ? $vehicle->user->avatar : asset($vehicle->user->avatar))
                                    : asset('default/profile.png');
                            @endphp
                            <img src="{{ $ownerAvatar }}" alt="{{ $vehicle->user->name }}" class="rounded-circle me-3" style="width: 42px; height: 42px; object-fit: cover; border: 1px solid #e2e8f0;" onError="this.onerror=null;this.src='{{ asset('default/profile.png') }}';">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">{{ $vehicle->user->name }}</h6>
                                <small class="text-muted">{{ $vehicle->user->email }}</small>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.show', $vehicle->user->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-user me-1"></i> View Profile
                        </a>
                    </div>
                @else
                    <span class="text-muted">Owner N/A</span>
                @endif
            </div>

            {{-- Technical Specifications Table --}}
            <h6 class="fw-bold text-dark mb-3">Vehicle Specifications</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <tbody>
                        <tr>
                            <th class="bg-light" style="width: 200px;">Brand</th>
                            <td>{{ $brandName ?: 'N/A' }}</td>
                            <th class="bg-light" style="width: 200px;">Model</th>
                            <td>{{ $vehicle->model ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Year</th>
                            <td>{{ $vehicle->year ?? 'N/A' }}</td>
                            <th class="bg-light">Transmission</th>
                            <td>{{ is_object($vehicle->transmission) ? ($vehicle->transmission->value ?? $vehicle->transmission->name) : ($vehicle->transmission ?? 'N/A') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Drive Type</th>
                            <td>{{ is_object($vehicle->drive_type) ? ($vehicle->drive_type->value ?? $vehicle->drive_type->name) : ($vehicle->drive_type ?? 'N/A') }}</td>
                            <th class="bg-light">Horsepower</th>
                            <td>{{ $vehicle->horsepower ? $vehicle->horsepower . ' HP' : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Mileage</th>
                            <td>{{ $vehicle->mileage ? number_format($vehicle->mileage) . ' miles' : 'N/A' }}</td>
                            <th class="bg-light">Engine</th>
                            <td>{{ $vehicle->engine ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Color</th>
                            <td>{{ $vehicle->color ?? 'N/A' }}</td>
                            <th class="bg-light">VIN</th>
                            <td><code>{{ $vehicle->vin ?? 'N/A' }}</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Build Story --}}
            @if($vehicle->build_story)
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2">Build Story</h6>
                    <div class="text-dark bg-light p-3 border" style="line-height: 1.7; white-space: pre-line; border-radius: 0;">
                        {!! e($vehicle->build_story) !!}
                    </div>
                </div>
            @endif

            {{-- Modifications Section --}}
            @if(!empty($vehicle->performance_mods) || !empty($vehicle->exterior_mods) || !empty($vehicle->suspension))
                <h6 class="fw-bold text-dark mb-3">Vehicle Modifications</h6>
                <div class="row g-3 mb-4">
                    @if(!empty($vehicle->performance_mods))
                        <div class="col-12 col-md-4">
                            <div class="border p-3 bg-light h-100" style="border-radius: 0;">
                                <h6 class="fw-bold text-danger mb-2"><i class="fa fa-tachometer-alt me-1"></i> Performance Mods</h6>
                                <ul class="mb-0 ps-3">
                                    @foreach((array)$vehicle->performance_mods as $mod)
                                        <li>{{ is_array($mod) ? json_encode($mod) : $mod }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if(!empty($vehicle->exterior_mods))
                        <div class="col-12 col-md-4">
                            <div class="border p-3 bg-light h-100" style="border-radius: 0;">
                                <h6 class="fw-bold text-primary mb-2"><i class="fa fa-spray-can me-1"></i> Exterior Mods</h6>
                                <ul class="mb-0 ps-3">
                                    @foreach((array)$vehicle->exterior_mods as $mod)
                                        <li>{{ is_array($mod) ? json_encode($mod) : $mod }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if(!empty($vehicle->suspension))
                        <div class="col-12 col-md-4">
                            <div class="border p-3 bg-light h-100" style="border-radius: 0;">
                                <h6 class="fw-bold text-success mb-2"><i class="fa fa-cogs me-1"></i> Suspension Mods</h6>
                                <ul class="mb-0 ps-3">
                                    @foreach((array)$vehicle->suspension as $mod)
                                        <li>{{ is_array($mod) ? json_encode($mod) : $mod }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Vehicle Media Gallery --}}
            @if($vehicle->media && $vehicle->media->count() > 0)
                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fa fa-photo-video text-primary me-2"></i> Vehicle Media Gallery ({{ $vehicle->media->count() }})
                    </h6>
                    <div class="row g-3 mb-4">
                        @foreach($vehicle->media as $media)
                            @php
                                $mediaUrl = !empty($media->full_url) ? $media->full_url : (!empty($media->url) ? $media->url : asset($media->path));
                                $ext = strtolower(pathinfo($media->path ?? $media->url ?? '', PATHINFO_EXTENSION));
                                $isImage = str_contains($media->mime_type ?? '', 'image') || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $isVideo = str_contains($media->mime_type ?? '', 'video') || in_array($ext, ['mp4', 'mov', 'avi', 'webm']);
                            @endphp
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="border p-2 bg-light text-center h-100 d-flex flex-column justify-content-center align-items-center" style="border-radius: 0;">
                                    @if($isImage)
                                        <a href="{{ $mediaUrl }}" target="_blank" class="w-100">
                                            <img src="{{ $mediaUrl }}" class="img-fluid" style="max-height: 220px; width: 100%; object-fit: cover; border-radius: 0;" alt="Vehicle Media" onError="this.onerror=null;this.src='{{ asset('default/profile.png') }}';">
                                        </a>
                                    @elseif($isVideo)
                                        <video controls style="max-height: 220px; width: 100%; border-radius: 0;">
                                            <source src="{{ $mediaUrl }}" type="{{ $media->mime_type ?? 'video/mp4' }}">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <a href="{{ $mediaUrl }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 py-3" style="border-radius: 0;">
                                            <i class="fa fa-file me-1"></i> View Attachment ({{ $media->original_name ?? 'File' }})
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="d-flex gap-4 pt-3 border-top text-muted small">
                <span><i class="fa fa-wrench text-warning me-1"></i> {{ $vehicle->parts ? $vehicle->parts->count() : 0 }} Attached Parts</span>
                <span><i class="fa fa-calendar me-1"></i> Added: {{ $vehicle->created_at ? $vehicle->created_at->format('d M, Y H:i A') : 'N/A' }}</span>
            </div>
        </div>
    </div>
</x-admin-layout>
