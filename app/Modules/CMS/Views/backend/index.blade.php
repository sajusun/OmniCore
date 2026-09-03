<x-admin-layout>
    <x-slot name="title">Page > {{ str($page)->replace('-', ' ')->title() }}</x-slot>

    {{-- Breadcrumb + Action --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    Page &gt; {{ str($page)->replace('-', ' ')->title() . ' > ' . str($section)->replace('-', ' ')->title() }}
                </li>
            </ol>
        </nav>
        <a href="{{ route('admin.cms.page.edit', [$page, $section]) }}" class="btn btn-primary btn-sm" style="border-radius: 0;">
            <i class="fa-solid fa-pen-to-square me-1"></i> Edit Section
        </a>
    </div>

    {{-- Main Card --}}
    <div class="card shadow-sm border-0" style="border-radius: 0;">

        {{-- Card Header --}}
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary d-flex align-items-center justify-content-center shadow"
                    style="width: 42px; height: 42px; flex-shrink: 0; border-radius: 0;">
                    <svg class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h5 class="mb-0 fw-semibold">Update Section Content</h5>
                    <p class="text-muted small mb-0">Fill in the fields below and save your changes.</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="card-body p-4">
            <form action="{{ route('admin.cms.page.update', [$page, $section]) }}" method="POST" id="update_form"
                enctype="multipart/form-data">
                @csrf

                @if (in_array('name', $elements))
                <x-form.text name="name" label="Name" placeholder="Enter name"
                    value="{{ $data->name ?? (old('name') ?? '') }}" />
                @endif

                @if (in_array('title', $elements))
                <x-form.text name="title" label="Title" rows="2" placeholder="Enter title"
                    value="{{ $data->title ?? (old('title') ?? '') }}" />
                @endif

                @if (in_array('subtitle', $elements))
                <x-form.textarea name="subtitle" label="Subtitle" rows="2" placeholder="Enter subtitle"
                    value="{{ $data->subtitle ?? (old('subtitle') ?? '') }}" />
                @endif

                @if (in_array('mini-description', $elements))
                <x-form.textarea name="description" label="Content" rows="5"
                    value="{{ $data->description ?? old('description') }}" />
                @endif

                @if (in_array('description', $elements))
                <x-form.quilleditor name="description" label="Content (Rich Text)" placeholder="Enter Content"
                    :value="$data->description ?? old('description')" />
                @endif

                @if (in_array('short_description', $elements))
                <x-form.textarea name="short_description" label="Short Description" rows="3"
                    value="{{ $data->short_description ?? old('short_description') }}" />
                @endif

                @if (in_array('image', $elements))
                <x-form.file name="image" label="Hero Image" placeholder="Choose Image"
                    file="{{ $data->image ?? '' }}" />
                @endif

                @if (in_array('bg', $elements))
                <x-form.file name="bg" label="Background Image" placeholder="Choose Image"
                    file="{{ $data->bg ?? '' }}" />
                @endif

                @if (in_array('video', $elements))
                <x-form.text name="video" label="Video URL / Path" placeholder="Enter Video URL"
                    value="{{ $data->video ?? '' }}" />
                @endif

                @if (in_array('meta', $elements))
                <x-form.meta-fields label="Meta Data" name="meta" :data="$data" />
                @endif

                @if (in_array('images', $elements))
                <x-form.media-gallery label="Image Gallery" name="images" :model="$data" />
                @endif

                <hr class="my-4">

                <div
                    class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3">
                    <div>
                        <x-form.select name="status" label="Status" value="{{ $data?->status ?? old('status') }}">
                            <option value="active" {{ $data?->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $data?->status == 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </x-form.select>
                    </div>
                    <x-form.submit>Save Changes</x-form.submit>
                </div>

            </form>
        </div>
    </div>

    <x-modal.confirm-delete name="confirm-user-delete" action=""
        message="Are you sure you want to delete this Media? You Can not recover it later." />
    {{-- Reusable Success/Error Toast status modal --}}
    <x-modal.status />

    @push('styles')
    <style>
        .sortable-ghost {
            opacity: 0.4;
            background: #eef2ff;
            border: 2px dashed #6366f1 !important;
        }

        .sortable-drag {
            cursor: grabbing;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/js/iziToast.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/css/iziToast.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            window.previewSingleImage = function (event, targetId) {
                const file = event.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = e => { const img = document.getElementById(targetId); if (img) img.src = e.target.result; };
                reader.readAsDataURL(file);
            };

            const imagesInput = document.getElementById('images-upload');
            const previewGrid = document.getElementById('new-images-preview');
            if (imagesInput && previewGrid) {
                imagesInput.addEventListener('change', function () {
                    previewGrid.innerHTML = '';
                    if (!this.files.length) { previewGrid.classList.add('d-none'); return; }
                    previewGrid.classList.remove('d-none');
                    Array.from(this.files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = e => {
                            const div = document.createElement('div');
                            div.className = 'overflow-hidden border';
                            div.innerHTML = `<img src="${e.target.result}" class="img-fluid w-100" style="object-fit:cover; aspect-ratio:16/9;">`;
                            previewGrid.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    });
                });
            }

            document.querySelectorAll('.status-toggle').forEach(toggle => {
                toggle.addEventListener('change', async function () {
                    const id = this.getAttribute('data-id');
                    const newStatus = this.checked;
                    this.checked = !newStatus;
                    try {
                        let url = "{{ route('media.status.update', ':id') }}";
                        url = url.replace(':id', id);
                        const res = await axios.post(url, { status: newStatus ? 1 : 0 }, { headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value } });
                        this.checked = newStatus;
                        iziToast.success({ message: res.data.message, position: 'topCenter', timeout: 800, progressBar: false, theme: 'light', icon: 'fa-solid fa-circle-check', iconColor: '#4f46e5' });
                    } catch (err) {
                        iziToast.error({ message: err.response?.data?.message || 'Failed to update.', position: 'topRight', timeout: 800, progressBar: false, theme: 'light', icon: 'fa-solid fa-circle-exclamation', iconColor: '#ef4444' });
                    }
                });
            });

            document.querySelectorAll('.delete-slider').forEach(btn => {
                btn.addEventListener('click', async function () {
                    const id = this.getAttribute('data-id');
                    const form = document.getElementById('confirm-delete-form');
                    if (form) {
                        let url = "{{ route('media.delete', ':id') }}";
                        url = url.replace(':id', id);
                        form.setAttribute('action', url);
                    }
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'confirm-user-delete' }));
                });
            });

            const sortableEl = document.getElementById('sortable-sliders');
            if (sortableEl && sortableEl.children.length > 0) {
                new Sortable(sortableEl, {
                    animation: 200, ghostClass: 'sortable-ghost', dragClass: 'sortable-drag', handle: '.drag-handle',
                    onEnd: async function () {
                        const orders = [];
                        document.querySelectorAll('.sortable-item').forEach((item, i) => orders.push({ id: item.getAttribute('data-id'), position: i + 1 }));
                        try {
                            const res = await axios.post('{{ route('media.order.update') }}', { orders }, { headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value } });
                            iziToast.success({ title: 'Reordered', message: res.data.message, position: 'topCenter', timeout: 800, progressBar: false, theme: 'light', icon: 'fa-solid fa-circle-check', iconColor: '#4f46e5' });
                        } catch { iziToast.error({ title: 'Error', message: 'Failed to update order.', position: 'topRight' }); }
                    }
                });
            }
        });
    </script>
    @endpush

</x-admin-layout>
