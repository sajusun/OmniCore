<x-admin-layout>
    <x-slot name="title">Post Details</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Post Details</h2>
    </x-slot>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.posts.index') }}" class="text-decoration-none text-muted">Posts</a></li>
                    <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Post #{{ $post->id }}</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-0">Post Details</h4>
        </div>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left me-1"></i> Back to Posts
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 0;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                <div class="d-flex align-items-center">
                    @php
                        $userAvatar = !empty($post->user?->avatar)
                            ? (filter_var($post->user->avatar, FILTER_VALIDATE_URL) ? $post->user->avatar : asset($post->user->avatar))
                            : asset('default/profile.png');
                    @endphp
                    <img src="{{ $userAvatar }}" alt="{{ $post->user->name ?? 'User' }}" class="me-3" style="width: 48px; height: 48px; object-fit: cover; border: 1px solid #e2e8f0;" onError="this.onerror=null;this.src='{{ asset('default/profile.png') }}';">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">{{ $post->user->name ?? 'N/A' }}</h6>
                        <small class="text-muted">{{ $post->user->email ?? 'N/A' }}</small>
                    </div>
                </div>
                <div>
                    <span class="badge bg-info me-2">{{ ucfirst(is_object($post->type) ? ($post->type->value ?? $post->type->name) : ($post->type ?? 'post')) }}</span>
                    <span class="badge bg-secondary">{{ ucfirst($post->visibility ?? 'public') }}</span>
                </div>
            </div>

            {{-- Post Thumbnail (If direct thumbnail exists) --}}
            @if(!empty($post->thumbnail))
                <div class="mb-4 text-center bg-light p-2 border position-relative" style="border-radius: 0;">
                    <a href="javascript:void(0);" onclick="openPhotoModal('{{ asset($post->thumbnail) }}', 'Post Thumbnail')" class="d-inline-block text-decoration-none" title="Click to enlarge">
                        <img src="{{ asset($post->thumbnail) }}" class="img-fluid" style="max-height: 350px; object-fit: contain; cursor: zoom-in; border-radius: 0;" alt="Post Thumbnail">
                    </a>
                </div>
            @endif

            @if($post->title)
                <h4 class="fw-bold text-dark mb-3">{{ $post->title }}</h4>
            @endif

            @if($post->content)
                <div class="post-content text-dark mb-4" style="line-height: 1.7; font-size: 1rem; white-space: pre-line;">
                    {!! e($post->content) !!}
                </div>
            @endif

            {{-- Post Polymorphic Media Gallery --}}
            @if($post->media && $post->media->count() > 0)
                <div class="mt-4 pt-3 border-top">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fa fa-photo-video text-primary me-2"></i> Attached Media Gallery ({{ $post->media->count() }})
                    </h6>
                    <div class="row g-3 mb-4">
                        @foreach($post->media as $index => $media)
                            @php
                                $mediaUrl = !empty($media->full_url) ? $media->full_url : (!empty($media->url) ? $media->url : asset($media->path));
                                $ext = strtolower(pathinfo($media->path ?? $media->url ?? '', PATHINFO_EXTENSION));
                                $isImage = str_contains($media->mime_type ?? '', 'image') || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $isVideo = str_contains($media->mime_type ?? '', 'video') || in_array($ext, ['mp4', 'mov', 'avi', 'webm']);
                            @endphp
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="border p-2 bg-light text-center h-100 d-flex flex-column justify-content-center align-items-center" style="border-radius: 0;">
                                    @if($isImage)
                                        <a href="javascript:void(0);" onclick="openPhotoModal('{{ $mediaUrl }}', 'Attachment #{{ $index + 1 }}', {{ $index }})" class="w-100 d-block text-decoration-none" title="Click to enlarge">
                                            <img src="{{ $mediaUrl }}" class="img-fluid post-gallery-img" data-src="{{ $mediaUrl }}" style="max-height: 220px; width: 100%; object-fit: cover; border-radius: 0; cursor: zoom-in;" alt="Post Media" onError="this.onerror=null;this.src='{{ asset('default/profile.png') }}';">
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
                <span><i class="fa fa-heart text-danger me-1"></i> {{ $post->likes_count ?? 0 }} Likes</span>
                <span><i class="fa fa-comment text-primary me-1"></i> {{ $post->comments_count ?? 0 }} Comments</span>
                <span><i class="fa fa-share text-success me-1"></i> {{ $post->shares_count ?? 0 }} Shares</span>
                <span><i class="fa fa-eye text-info me-1"></i> {{ $post->views_count ?? 0 }} Views</span>
                <span><i class="fa fa-calendar me-1"></i> {{ $post->created_at ? $post->created_at->format('d M, Y H:i A') : 'N/A' }}</span>
            </div>
        </div>
    </div>

    {{-- Interactive Photo Lightbox Modal with Back Icon --}}
    <div class="modal fade" id="photoViewerModal" tabindex="-1" aria-labelledby="photoViewerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-white text-dark border-0 shadow-lg" style="border-radius: 0;">
                <div class="modal-header border-bottom py-2 px-3 bg-light d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-dark text-white fw-bold btn-sm d-inline-flex align-items-center px-3 py-1.5 shadow-sm" style="border-radius: 0;" data-bs-dismiss="modal" aria-label="Back">
                        <i class="fa fa-arrow-left me-2"></i> Back
                    </button>
                    <span id="photoViewerTitle" class="modal-title small fw-bold text-dark text-truncate mx-2">Photo View</span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 text-center position-relative d-flex align-items-center justify-content-center bg-dark" style="min-height: 400px; max-height: 80vh; overflow: hidden;">
                    <button type="button" id="prevPhotoBtn" class="btn btn-light position-absolute start-0 ms-3 shadow opacity-75" style="z-index: 10; display: none; width: 44px; height: 44px; border-radius: 0;">
                        <i class="fa fa-chevron-left"></i>
                    </button>

                    <img id="photoViewerImage" src="" class="img-fluid" style="max-height: 75vh; max-width: 100%; object-fit: contain; border-radius: 0; transition: transform 0.2s ease-in-out;" alt="Enlarged Photo">

                    <button type="button" id="nextPhotoBtn" class="btn btn-light position-absolute end-0 me-3 shadow opacity-75" style="z-index: 10; display: none; width: 44px; height: 44px; border-radius: 0;">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let galleryImages = [];
        let currentPhotoIndex = 0;

        document.addEventListener('DOMContentLoaded', function () {
            const imgEls = document.querySelectorAll('.post-gallery-img');
            imgEls.forEach((img, idx) => {
                galleryImages.push({
                    src: img.getAttribute('data-src') || img.src,
                    title: `Media Attachment #${idx + 1}`
                });
            });
        });

        function openPhotoModal(src, title = 'Enlarged Photo', index = -1) {
            const modalEl = document.getElementById('photoViewerModal');
            const imgEl = document.getElementById('photoViewerImage');
            const titleEl = document.getElementById('photoViewerTitle');
            const prevBtn = document.getElementById('prevPhotoBtn');
            const nextBtn = document.getElementById('nextPhotoBtn');

            if (!modalEl || !imgEl) return;

            imgEl.src = src;
            if (titleEl) titleEl.innerText = title;

            if (index >= 0 && galleryImages.length > 1) {
                currentPhotoIndex = index;
                updateNavButtons();
            } else {
                if (prevBtn) prevBtn.style.display = 'none';
                if (nextBtn) nextBtn.style.display = 'none';
            }

            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }

        function updateNavButtons() {
            const prevBtn = document.getElementById('prevPhotoBtn');
            const nextBtn = document.getElementById('nextPhotoBtn');
            if (prevBtn) prevBtn.style.display = currentPhotoIndex > 0 ? 'block' : 'none';
            if (nextBtn) nextBtn.style.display = currentPhotoIndex < galleryImages.length - 1 ? 'block' : 'none';
        }

        document.getElementById('prevPhotoBtn')?.addEventListener('click', function () {
            if (currentPhotoIndex > 0) {
                currentPhotoIndex--;
                const img = galleryImages[currentPhotoIndex];
                document.getElementById('photoViewerImage').src = img.src;
                document.getElementById('photoViewerTitle').innerText = img.title;
                updateNavButtons();
            }
        });

        document.getElementById('nextPhotoBtn')?.addEventListener('click', function () {
            if (currentPhotoIndex < galleryImages.length - 1) {
                currentPhotoIndex++;
                const img = galleryImages[currentPhotoIndex];
                document.getElementById('photoViewerImage').src = img.src;
                document.getElementById('photoViewerTitle').innerText = img.title;
                updateNavButtons();
            }
        });
    </script>
    @endpush
</x-admin-layout>
