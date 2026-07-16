@props([
'label' => 'Image Gallery',
'name' => 'images',
'model' => null,
'multiple' => true,
])

@php
$instance = 'media_' . \Illuminate\Support\Str::random(8);
$medias = $model->media ?? collect();
@endphp

<div class="media-gallery card shadow-sm border-light overflow-hidden"
    data-instance="{{ $instance }}" data-name="{{ $name }}">
    <div class="card-header bg-light d-flex align-items-center justify-content-between px-4 py-3 border-bottom border-light">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-images text-primary fa-lg"></i>
            <h6 class="card-title font-weight-bold mb-0">
                {{ $label }}
                <span class="badge bg-primary bg-opacity-10 text-primary ms-2 rounded-pill">
                    {{ $medias->count() }}
                </span>
            </h6>
        </div>
        <p class="text-muted small mb-0">
            <i class="fas fa-arrows-alt me-1"></i> Drag to reorder
        </p>
    </div>

    <div class="card-body p-4">
        @if($medias->isEmpty())
        <div class="text-center py-5">
            <div class="p-3 bg-light text-muted rounded-3 d-inline-flex align-items-center justify-content-center mb-3">
                <i class="fas fa-images fa-2x text-muted"></i>
            </div>
            <p class="text-muted small mb-0">No images yet</p>
        </div>
        @else
        <div class="media-sortable row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3 mb-4">
            @foreach($medias as $media)
            <div class="col media-item" data-id="{{ $media->id }}">
                <div class="card h-100 border shadow-sm position-relative overflow-hidden group">
                    <div class="ratio ratio-16x9">
                        <img src="{{ asset($media->path) }}" class="object-fit-cover w-100 h-100" alt="">
                    </div>
                    
                    {{-- Drag Handle --}}
                    <div class="drag-handle position-absolute top-0 start-0 m-2 btn btn-light btn-sm d-flex align-items-center justify-content-center cursor-move py-1 px-2 border" style="opacity: 0.85;">
                        <i class="fas fa-grip-vertical text-muted"></i>
                    </div>

                    <div class="card-body p-2 d-flex align-items-center justify-content-between">
                        {{-- Status Switch --}}
                        <div class="form-check form-switch mb-0">
                            <input type="checkbox" class="form-check-input media-status" role="switch" data-id="{{ $media->id }}" {{ $media->status ? 'checked' : '' }}>
                        </div>

                        {{-- Delete Button --}}
                        <button type="button" class="btn btn-outline-danger btn-sm media-delete py-1 px-2" data-id="{{ $media->id }}">
                            <i class="fas fa-trash-alt small"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div>
            <label class="form-label font-weight-bold mb-2">Add New Images</label>

            <label for="{{ $instance }}_upload"
                class="media-upload border border-dashed border-2 border-primary border-opacity-25 rounded-3 p-4 text-center cursor-pointer d-flex flex-column align-items-center justify-content-center gap-2 w-100"
                style="background-color: rgba(13, 110, 253, 0.02); transition: border-color 0.2s;"
                onmouseover="this.style.borderColor='#0d6efd'"
                onmouseout="this.style.borderColor='rgba(13, 110, 253, 0.25)'">
                <i class="fas fa-cloud-upload-alt fa-2x text-primary"></i>
                <span class="small font-weight-medium text-primary">Browse Images</span>
                <input id="{{ $instance }}_upload" type="file" name="{{ $name }}[]" class="d-none media-input"
                    accept="image/*" {{ $multiple ? 'multiple' : '' }}>
            </label>

            <div class="media-preview d-none mt-3 row row-cols-2 row-cols-md-4 g-3">
                <!-- Javascript Previews go here -->
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.media-gallery').forEach(initMediaGallery);

        function initMediaGallery(component) {
            if (component.dataset.initialized) {
                return;
            }
            component.dataset.initialized = true;

            const input = component.querySelector('.media-input');
            const preview = component.querySelector('.media-preview');
            const sortable = component.querySelector('.media-sortable');

            // Image Preview Handler
            input?.addEventListener('change', function () {
                preview.innerHTML = '';
                if (!this.files.length) {
                    preview.classList.add('d-none');
                    return;
                }
                preview.classList.remove('d-none');

                [...this.files].forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const col = document.createElement('div');
                        col.className = 'col position-relative';
                        col.innerHTML = `
                            <div class="card h-100 overflow-hidden border shadow-sm">
                                <div class="ratio ratio-16x9">
                                    <img src="${e.target.result}" class="object-fit-cover w-100 h-100">
                                </div>
                                <button type="button" class="remove-preview btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; padding: 0;">
                                    <i class="fas fa-times small"></i>
                                </button>
                            </div>
                        `;
                        col.querySelector('.remove-preview').addEventListener('click', function () {
                            col.remove();
                        });
                        preview.appendChild(col);
                    }
                    reader.readAsDataURL(file);
                });
            });

            // Delete Media
            component.querySelectorAll('.media-delete').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const form = document.getElementById('confirm-delete-form');
                    if (form) {
                        form.action = "{{ route('media.delete', ':id') }}".replace(':id', id);
                        window.dispatchEvent(new CustomEvent('open-modal', {
                            detail: 'confirm-user-delete'
                        }));
                    }
                });
            });

            // Status Toggle
            component.querySelectorAll('.media-status').forEach(toggle => {
                toggle.addEventListener('change', async function () {
                    const id = this.dataset.id;
                    const checked = this.checked;
                    this.checked = !checked;
                    try {
                        const res = await axios.post("{{ route('media.status.update', ':id') }}".replace(':id', id), {
                            status: checked ? 1 : 0
                        });
                        this.checked = checked;
                        if (typeof iziToast !== 'undefined') {
                            iziToast.success({message: res.data.message, position: 'topCenter', timeout: 800, progressBar: false});
                        }
                    } catch (e) {
                        if (typeof iziToast !== 'undefined') {
                            iziToast.error({message: e.response?.data?.message ?? 'Update failed.'});
                        }
                    }
                });
            });

            // Sortable List Initialization
            if (sortable && typeof Sortable !== 'undefined') {
                new Sortable(sortable, {
                    animation: 200,
                    handle: '.drag-handle',
                    onEnd: async () => {
                        const orders = [];
                        sortable.querySelectorAll('.media-item').forEach((item, index) => {
                            orders.push({
                                id: item.dataset.id,
                                position: index + 1
                            });
                        });

                        try {
                            const res = await axios.post('{{ route("media.order.update") }}', { orders });
                            if (typeof iziToast !== 'undefined') {
                                iziToast.success({message: res.data.message, position: 'topCenter', timeout: 800, progressBar: false});
                            }
                        } catch (e) {
                            if (typeof iziToast !== 'undefined') {
                                iziToast.error({message: e.response?.data?.message ?? 'Reorder failed.'});
                            }
                        }
                    }
                });
            }
        }
    });
</script>
@endpush
@endonce
