<x-admin-layout>
    <x-slot name="title">Page > {{ str($page)->replace('-', ' ')->title() }}</x-slot>

    <div class="container-fluid py-4">
        {{-- Page Header --}}
        <x-page-header
            :title="str($page)->replace('-', ' ')->title() . ' — ' . str($section)->replace('-', ' ')->title()"
            subtitle="Update section content and configuration."
            :breadcrumbs="[
                'Dashboard' => route('admin.dashboard'),
                'CMS' => null,
                str($page)->replace('-', ' ')->title() => null,
                str($section)->replace('-', ' ')->title() => null,
            ]">
            <x-slot:actions>
                <a href="{{ route('admin.cms.page.edit', [$page, $section]) }}" class="btn btn-light border d-inline-flex align-items-center gap-2 px-3">
                    <i class="fas fa-rotate-right"></i><span>Reset Form</span>
                </a>
            </x-slot:actions>
        </x-page-header>

        {{-- Main Card --}}
        <x-card title="Update Section Content" subtitle="Fill in the fields below and save your changes.">
            <x-slot:icon><i class="fas fa-edit"></i></x-slot:icon>

            <form action="{{ route('admin.cms.page.update', [$page, $section]) }}" method="POST" id="update_form" enctype="multipart/form-data">
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

                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3">
                    <div style="min-width: 200px;">
                        <x-form.select name="status" label="Status" value="{{ $data?->status ?? old('status') }}">
                            <option value="active" {{ ($data?->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ ($data?->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </x-form.select>
                    </div>
                    <button type="submit" class="btn btn-primary px-4 d-inline-flex align-items-center gap-2">
                        <i class="fas fa-check"></i><span>Save Changes</span>
                    </button>
                </div>
            </form>
        </x-card>
    </div>

    <x-modal.confirm-delete name="confirm-media-delete" action=""
        title="Delete Media"
        message="Are you sure you want to delete this media item? This action cannot be undone." />

    <x-modal.status />
</x-admin-layout>