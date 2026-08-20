<x-admin-layout>
    <x-slot name="title">Edit Event</x-slot>
    <x-slot name="header">Edit Event</x-slot>

    <div class="container-fluid py-4 px-0">
        <div class="mx-auto" style="max-width: 1280px;">

            {{-- Header Area with Bootstrap Flex & Breadcrumb --}}
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
                <div>
                    <h2 class="h4 fw-bold text-dark mb-1">Edit Event</h2>
                    <p class="small text-muted mb-0">Update event details, location, schedule, and permissions.</p>
                </div>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}"
                                class="text-decoration-none text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.events.index') }}"
                                class="text-decoration-none text-muted">Events</a>
                        </li>
                        <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>

            {{-- Form Start --}}
            <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Main Form Card Container with Force Flat Edges --}}
                <div class="card border border-light-subtle shadow-sm overflow-hidden"
                    style="border-radius: 0 !important;">

                    {{-- Card Header --}}
                    <div class="card-header border-bottom border-light-subtle bg-transparent px-4 py-3">
                        <h3 class="card-title h6 mb-0 fw-bold text-dark">
                            Event: {{ $event->title }}
                        </h3>
                    </div>

                    {{-- Full-width Banner / Thumbnail Upload Section --}}
                    <div class="w-full bg-light border-bottom border-light-subtle p-4">
                        <x-form.file name="thumbnail" label="Event Banner / Thumbnail"
                            :file="$event->thumbnail_url ? $event->thumbnail_url : ''" />
                    </div>

                    {{-- Event Media Gallery Section --}}
                    <div class="w-full bg-white border-bottom border-light-subtle p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h4 class="h6 fw-bold text-dark mb-0">
                                <i class="fa fa-images me-2 text-indigo"></i> Event Media Gallery
                                <span class="badge bg-secondary ms-1">{{ $event->media ? $event->media->count() : 0 }} Files</span>
                            </h4>
                        </div>

                        @if($event->media && $event->media->isNotEmpty())
                            <div class="row g-3 mb-4">
                                @foreach($event->media as $media)
                                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                        <div class="card h-100 border shadow-sm overflow-hidden text-center position-relative">
                                            <span class="position-absolute top-0 start-0 m-1 badge bg-dark opacity-75" style="font-size: 0.65rem; z-index: 2;">
                                                {{ ucfirst($media->collection_name ?? 'media') }}
                                            </span>
                                            
                                            <div class="ratio ratio-1x1 bg-light border-bottom d-flex align-items-center justify-content-center overflow-hidden">
                                                @if(str_contains($media->mime_type ?? '', 'image') || in_array(strtolower($media->extension ?? ''), ['jpg','jpeg','png','gif','webp','svg']))
                                                    <img src="{{ $media->url }}" alt="{{ $media->original_name ?? 'Media' }}" class="img-fluid object-fit-cover w-100 h-100">
                                                @elseif(str_contains($media->mime_type ?? '', 'video') || in_array(strtolower($media->extension ?? ''), ['mp4','webm','ogg']))
                                                    <video src="{{ $media->url }}" class="w-100 h-100 object-fit-cover" muted></video>
                                                @else
                                                    <div class="d-flex align-items-center justify-content-center h-100 w-100 bg-light text-secondary">
                                                        <i class="fa fa-file text-2xl"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="card-body p-2 d-flex flex-column justify-content-between">
                                                <p class="card-text small text-truncate mb-1 fw-medium" title="{{ $media->original_name ?? $media->file_name }}">
                                                    {{ $media->original_name ?? $media->file_name }}
                                                </p>
                                                <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 0.75rem;">
                                                    <span>{{ $media->size ? number_format($media->size / 1024, 1) . ' KB' : '' }}</span>
                                                    <a href="{{ $media->url }}" target="_blank" class="text-indigo text-decoration-none" title="View full media">
                                                        <i class="fa fa-external-link-alt ms-1"></i> View
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-light border py-2 px-3 mb-3 text-muted small" role="alert">
                                <i class="fa fa-info-circle me-1"></i> No additional media files are attached to this event.
                            </div>
                        @endif

                        <div class="mt-3">
                            <label class="form-label fw-medium text-dark small">Add / Replace Gallery Images</label>
                            <input type="file" name="images[]" multiple class="form-control" accept="image/*">
                            <small class="text-muted" style="font-size: 0.75rem;">Select one or multiple image files to update the event's photo gallery.</small>
                        </div>
                    </div>

                    {{-- Card Body Inputs Grid --}}
                    <div class="card-body p-4">
                        <div class="row g-4">

                            {{-- Title --}}
                            <div class="col-12 col-md-8">
                                <x-form.text name="title" label="Event Title" placeholder="Enter event title"
                                    :value="old('title', $event->title)" required autofocus />
                            </div>

                            {{-- Event Type --}}
                            <div class="col-12 col-md-4">
                                <x-form.select name="event_type" label="Event Type" required>
                                    <option value="">Select Event Type</option>
                                    @foreach($eventTypes as $type)
                                    <option value="{{ $type->label() }}" @selected(old('event_type', $event->event_type)
                                        == $type->label())>
                                        {{ $type->label() }}
                                    </option>
                                    @endforeach
                                </x-form.select>
                            </div>

                            {{-- Organizer / User --}}
                            <div class="col-12 col-md-6">
                                <x-form.select name="user_id" label="Organizer (User)" required>
                                    <option value="">Select Organizer</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('user_id', $event->user_id) ==
                                        $user->id)>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                    @endforeach
                                </x-form.select>
                            </div>

                            {{-- Associated Club (Optional) --}}
                            <div class="col-12 col-md-6">
                                <x-form.select name="club_id" label="Club (Optional)">
                                    <option value="">No Club Associated</option>
                                    @foreach($clubs as $club)
                                    <option value="{{ $club->id }}" @selected(old('club_id', $event->club_id) ==
                                        $club->id)>
                                        {{ $club->name }}
                                    </option>
                                    @endforeach
                                </x-form.select>
                            </div>

                            {{-- Event Date --}}
                            <div class="col-12 col-md-4">
                                <x-form.date name="event_date" label="Event Date"
                                    :value="old('event_date', $event->event_date?->format('Y-m-d'))" required />
                            </div>

                            {{-- Event Time --}}
                            <div class="col-12 col-md-4">
                                <x-form.text type="time" name="event_time" label="Event Time"
                                    :value="old('event_time', $event->event_time)" required />
                            </div>

                            {{-- Max Participants --}}
                            <div class="col-12 col-md-4">
                                <x-form.text type="'number" name="max_participants" label="Max Participants"
                                    placeholder="e.g. 50" :value="old('max_participants', $event->max_participants)" />
                            </div>

                            {{-- Location Text --}}
                            <div class="col-12">
                                <x-form.text name="location" label="Location Address"
                                    placeholder="Enter full venue address" :value="old('location', $event->location)"
                                    required />
                            </div>

                            {{-- Latitude --}}
                            <div class="col-12 col-md-6">
                                <x-form.text name="latitude" label="Latitude" placeholder="e.g. 23.8103"
                                    :value="old('latitude', $event->latitude)" />
                            </div>

                            {{-- Longitude --}}
                            <div class="col-12 col-md-6">
                                <x-form.text name="longitude" label="Longitude" placeholder="e.g. 90.4125"
                                    :value="old('longitude', $event->longitude)" />
                            </div>

                            {{-- Required Vehicles (Array Field / Multi-select) --}}
                            <div class="col-12">
                                <x-form.select name="vehicles_required[]" label="Vehicles Required" multiple>
                                    @php
                                    $selectedVehicles = old('vehicles_required', $event->vehicles_required ?? []);
                                    @endphp
                                    @foreach($vehicleOptions as $vehicle)
                                    <option value="{{ $vehicle->label() }}" @selected(in_array($vehicle->label(),
                                        $selectedVehicles))>
                                        {{ $vehicle->label() }}
                                    </option>
                                    @endforeach
                                </x-form.select>
                            </div>

                            {{-- Description --}}
                            <div class="col-12">
                                <x-form.textarea name="description" label="Event Description" rows="4"
                                    placeholder="Provide detailed information about the event..."
                                    :value="old('description', $event->description)" />
                            </div>

                            {{-- Status & Visibility Switches / Selects --}}
                            <div class="col-12 col-md-6">
                                <x-form.select name="status" label="Status" required>
                                    <option value="draft" @selected(old('status', $event->status) == 'draft')>Draft
                                    </option>
                                    <option value="published" @selected(old('status', $event->status) ==
                                        'published')>Published</option>
                                    <option value="cancelled" @selected(old('status', $event->status) ==
                                        'cancelled')>Cancelled</option>
                                </x-form.select>
                            </div>

                            <div class="col-12 col-md-6">
                                <x-form.select name="is_public" label="Visibility" required>
                                    <option value="1" @selected(old('is_public', $event->is_public) == 1)>Public
                                    </option>
                                    <option value="0" @selected(old('is_public', $event->is_public) == 0)>Private
                                    </option>
                                </x-form.select>
                            </div>

                        </div>
                    </div>

                    {{-- Card Action Footer with Absolute Flat Corners --}}
                    <div
                        class="card-footer d-flex justify-content-end gap-2 border-top border-light-subtle bg-light px-4 py-3">
                        <x-form.cancel href="{{ route('admin.events.index') }}" class="btn btn-light border"
                            style="border-radius: 0 !important;">
                            Cancel
                        </x-form.cancel>

                        <x-form.submit class="btn btn-primary px-4" style="border-radius: 0 !important;">
                            Update Event
                        </x-form.submit>
                    </div>

                </div>
            </form>

        </div>
    </div>
</x-admin-layout>