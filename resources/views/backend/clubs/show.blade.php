<x-admin-layout>
    <x-slot name="title">Club Details - {{ $club->name }}</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Club Details</h2>
    </x-slot>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.clubs.index') }}" class="text-decoration-none text-muted">Clubs</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">{{ $club->name }}</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Club Details</h4>
        </div>
        <a href="{{ route('admin.clubs.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left me-1"></i> Back to Clubs
        </a>
    </div>

    {{-- Club Summary Card --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start justify-content-between border-bottom pb-4 mb-4 gap-3">
                <div class="d-flex align-items-center gap-3">
                    @php
                        $thumbnailUrl = $club->thumbnail_url ?: asset('default/profile.png');
                    @endphp
                    <img src="{{ $thumbnailUrl }}" alt="{{ $club->name }}" class="border p-1" style="width: 80px; height: 80px; object-fit: cover; border-radius: 0;" onError="this.onerror=null;this.src='{{ asset('default/profile.png') }}';">
                    <div>
                        <h3 class="fw-bold text-dark mb-1">{{ $club->name }}</h3>
                        <p class="text-muted mb-0">
                            <i class="fa fa-map-marker-alt text-danger me-1"></i> 
                            {{ implode(', ', array_filter([$club->city, $club->state, $club->country])) ?: 'Location N/A' }}
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info px-3 py-2 fs-6">{{ ucfirst($club->type ?? 'Club') }}</span>
                    @php
                        $curStatus = strtolower($club->status ?? 'pending');
                        $statusBadge = match($curStatus) {
                            'published', 'active' => 'success',
                            'pending' => 'warning text-dark',
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-{{ $statusBadge }} px-3 py-2 fs-6">
                        {{ ucfirst($curStatus) }}
                    </span>
                </div>
            </div>

            {{-- Admin Status Management Section --}}
            <div class="card border mb-4 shadow-none bg-light" style="border-radius: 0;">
                <div class="card-body p-3">
                    <div class="row align-items-center g-3">
                        <div class="col-12 col-md-6">
                            <h6 class="fw-bold text-dark mb-1">
                                <i class="fa fa-sliders-h me-2 text-primary"></i> Club Status Management
                            </h6>
                            <small class="text-muted">Review club details and update approval status.</small>
                        </div>
                        <div class="col-12 col-md-6">
                            <form action="{{ route('admin.clubs.status.update', $club->id) }}" method="POST" class="d-flex flex-wrap align-items-center justify-content-md-end gap-2 m-0">
                                @csrf
                                @method('PUT')
                                <select name="status" class="form-select form-select-sm fw-semibold" style="width: auto; max-width: 200px;">
                                    <option value="pending" {{ $curStatus === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                                    <option value="published" {{ in_array($curStatus, ['published', 'active']) ? 'selected' : '' }}>Approve & Publish</option>
                                    <option value="draft" {{ $curStatus === 'draft' ? 'selected' : '' }}>Draft / Reject</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary text-nowrap fw-semibold px-3">
                                    <i class="fa fa-check-circle me-1"></i> Update Status
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Creator Info Section --}}
            <div class="bg-light p-3 border mb-4" style="border-radius: 0;">
                <h6 class="fw-bold text-dark mb-2"><i class="fa fa-user-shield me-2 text-primary"></i> Club Creator</h6>
                @if($club->creator)
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            @php
                                $creatorAvatar = !empty($club->creator->avatar)
                                    ? (filter_var($club->creator->avatar, FILTER_VALIDATE_URL) ? $club->creator->avatar : asset($club->creator->avatar))
                                    : asset('default/profile.png');
                            @endphp
                            <img src="{{ $creatorAvatar }}" alt="{{ $club->creator->name }}" class="rounded-circle me-3" style="width: 42px; height: 42px; object-fit: cover; border: 1px solid #e2e8f0;" onError="this.onerror=null;this.src='{{ asset('default/profile.png') }}';">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">{{ $club->creator->name }}</h6>
                                <small class="text-muted">{{ $club->creator->email }}</small>
                            </div>
                        </div>
                        <a href="{{ route('admin.users.show', $club->creator->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-user me-1"></i> View Profile
                        </a>
                    </div>
                @else
                    <span class="text-muted">Creator Unknown</span>
                @endif
            </div>

            {{-- Description --}}
            @if($club->description)
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2">Description</h6>
                    <div class="text-dark" style="line-height: 1.7; white-space: pre-line;">
                        {!! e($club->description) !!}
                    </div>
                </div>
            @endif

            {{-- Media Gallery --}}
            @if($club->media && $club->media->count() > 0)
                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fa fa-photo-video text-primary me-2"></i> Club Media Gallery ({{ $club->media->count() }})
                    </h6>
                    <div class="row g-3 mb-4">
                        @foreach($club->media as $media)
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
                                            <img src="{{ $mediaUrl }}" class="img-fluid" style="max-height: 220px; width: 100%; object-fit: cover; border-radius: 0;" alt="Club Media" onError="this.onerror=null;this.src='{{ asset('default/profile.png') }}';">
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
                <span><i class="fa fa-users text-primary me-1"></i> {{ $club->members ? $club->members->count() : 0 }} Approved Members</span>
                <span><i class="fa fa-calendar me-1"></i> Created: {{ $club->created_at ? $club->created_at->format('d M, Y H:i A') : 'N/A' }}</span>
            </div>
        </div>
    </div>
</x-admin-layout>
