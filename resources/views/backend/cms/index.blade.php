<x-admin-layout>
    <x-slot name="title">Page > {{ str($page)->replace('-', ' ')->title() }}</x-slot>


    <div class="mb-6 flex justify-between items-center">
        <div class="text-sm text-gray-500">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900 transition-colors">Dashboard</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium">Page > {{ str($page)->replace('-', ' ')->title() . ' > ' .
                str($section)->replace('-', ' ')->title() }}</span>
        </div>
        <a href="{{ route('admin.cms.page.edit', [$page, $section]) }}"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
            Edit Section
        </a>
    </div>

    {{-- Main Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-md border border-gray-100 dark:border-gray-700 overflow-hidden">

        {{-- Card Header --}}
        <div
            class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-md shadow-indigo-200 dark:shadow-indigo-900/40">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Update Section Content</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Fill in the fields below and save your changes.
                    </p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.cms.page.update', [$page, $section]) }}" method="POST" id="update_form"
            enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            @if (in_array('name', $elements))
            <x-form.text name="name" label="Name" placeholder="Enter name"
                value="{{ $data->name ?? (old('name') ?? '') }}" />
            @endif

            @if (in_array('title', $elements))
            <x-form.textarea name="title" label="Title" rows="2" placeholder="Enter title"
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
                value="{{ $data->description ?? old('description') }}" />
            @endif

            @if (in_array('short_description', $elements))
            <x-form.textarea name="short_description" label="Short Description" rows="3"
                value="{{ $data->short_description ?? old('short_description') }}" />
            @endif

            @if (in_array('image', $elements))
            <x-form.file name="image" label="Hero Image" placeholder="Choose Image" file="{{ $data->image ?? '' }}" />
            @endif

            @if (in_array('bg', $elements))
            <x-form.file name="bg" label="Background Image" placeholder="Choose Image" file="{{ $data->bg ?? '' }}" />
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

            <hr class="border-gray-100 dark:border-gray-700">

            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <x-form.select name="status" label="Status" value="{{ $data?->status ?? old('status') }}"
                        width="w-64">
                        <option value="active" {{ $data?->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $data?->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </x-form.select>
                </div>
                <x-form.submit>Save Changes</x-form.submit>

            </div>

        </form>
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
                        if (!this.files.length) { previewGrid.classList.add('hidden'); return; }
                        previewGrid.classList.remove('hidden');
                        Array.from(this.files).forEach(file => {
                            const reader = new FileReader();
                            reader.onload = e => {
                                const div = document.createElement('div');
                                div.className = 'relative rounded-lg overflow-hidden aspect-video border border-gray-100';
                                div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
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
                        const label = newStatus ? 'activate' : 'deactivate';
                        this.checked = !newStatus;
                        try {
                            let url = "{{ route('media.status.update', ':id') }}";
                            url = url.replace(':id', id);
                            const res = await axios.post(url, { status: newStatus ? 1 : 0 }, { headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value } });
                            this.checked = newStatus;
                            const bg = this.parentElement.querySelector('.toggle-bg');
                            const dot = this.parentElement.querySelector('.toggle-dot');
                            if (bg) { bg.classList.toggle('bg-indigo-600', newStatus); bg.classList.toggle('bg-gray-200', !newStatus); }
                            if (dot) dot.classList.toggle('translate-x-4', newStatus);
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