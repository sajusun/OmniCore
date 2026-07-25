<x-admin-layout>
    @slot('title')
    Site Information Settings
    @endslot
    @slot('header')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
        <nav class="small fw-medium text-muted d-none d-sm-flex align-items-center bg-light px-3 py-2 rounded-pill shadow-sm border">
            <ol class="d-flex list-unstyled m-0 gap-2">
                <li class="d-flex align-items-center">
                    <a href="javascript:void(0);" class="text-decoration-none text-secondary">Settings</a>
                    <span class="mx-2 text-muted">/</span>
                </li>
                <li class="d-flex align-items-center text-dark fw-medium" aria-current="page">
                    Site Settings
                </li>
            </ol>
        </nav>
    </div>
    @endslot

    <div class="py-4">
        <div class="container-fluid">
            <!-- Enhanced card with deeper shadow & glass-morphism effect -->
            <div class="card shadow-sm border overflow-hidden">
                <div class="card-header bg-light d-flex align-items-center gap-2 py-3 px-4">
                    <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h6 class="card-title fw-bold mb-0">Configure general settings</h6>
                    <span class="badge bg-primary ms-auto">required fields *</span>
                </div>

                <div class="card-body p-4">
                    <form class="form form-horizontal" method="post"
                        action="{{ route('admin.setting.general.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <!-- Grid layout for better structure -->
                        <div class="row g-3">
                            <!-- Left column -->
                            <div class="col-md-6 d-flex flex-column gap-3">
                                <x-form.text name="name" label="Name" placeholder="Enter site name"
                                    value="{{ $setting->name ?? old('name') ?? '' }}" />

                                <x-form.text name="title" label="Title" placeholder="Site title"
                                    value="{{ $setting->title ?? old('title') ?? '' }}" />

                                <x-form.textarea name="description" label="Description"
                                    value="{{ $setting->description ?? old('description') ?? '' }}"
                                    placeholder="Write description..." rows="3" />

                                <x-form.textarea name="keywords" label="Keywords"
                                    value="{{ $setting->keywords ?? old('keywords') ?? '' }}"
                                    placeholder="SEO keywords, comma separated" rows="3" />

                                <x-form.text name="author" label="Author" placeholder="Author name"
                                    value="{{ $setting->author ?? old('author') ?? '' }}" />
                            </div>

                            <!-- Right column -->
                            <div class="col-md-6 d-flex flex-column gap-3">
                                <x-form.text name="phone" label="Phone" placeholder="Contact phone"
                                    value="{{ $setting->phone ?? old('phone') ?? '' }}" />

                                <x-form.text name="email" label="Email" placeholder="Contact email"
                                    value="{{ $setting->email ?? old('email') ?? '' }}" />

                                <x-form.text name="address" label="Address" placeholder="Physical address"
                                    value="{{ $setting->address ?? old('address') ?? '' }}" />

                                <x-form.text name="copyright" label="Copyright" placeholder="Copyright text"
                                    value="{{ $setting->copyright ?? old('copyright') ?? '' }}" />

                                <x-form.text name="map_embed_code" label="Map Embed URL (Only Iframe embed link)"
                                    placeholder="Paste your Google Maps iframe embed code"
                                    value="{{ $setting->map_embed_code ?? old('map_embed_code') ?? '' }}" />
                            </div>

                            <!-- Full width fields: map + files -->
                            <div class="col-12 mt-3">

                                <!-- File uploads in a responsive row -->
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card bg-light p-3 border">
                                            <x-form.file name="logo" label="Logo" file="{{ $setting->logo ? url($setting->logo): '' }}">
                                                <p class="text-muted small mt-1 d-flex align-items-center gap-1 mb-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Max 5MB · jpeg, jpg, png
                                                </p>
                                            </x-form.file>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light p-3 border">
                                            <x-form.file name="favicon" label="Favicon" file="{{ $setting->favicon ? url($setting->favicon): '' }}">
                                                <p class="text-muted small mt-1 d-flex align-items-center gap-1 mb-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Max 5MB · jpeg, jpg, png, ico
                                                </p>
                                            </x-form.file>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="mt-4 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div class="small text-muted">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    All changes are saved immediately
                                </span>
                            </div>
                            <button class="btn btn-primary d-inline-flex align-items-center px-4 py-2" type="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="me-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Update Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional info card with subtle shadow -->
            <div class="row g-3 mt-4">
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100">
                        <div class="p-2 bg-success bg-opacity-10 text-success rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">SEO ready</p>
                            <p class="fw-semibold mb-0">Meta tags & keywords</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100">
                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Brand identity</p>
                            <p class="fw-semibold mb-0">Thumbnail & Favicon</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100">
                        <div class="p-2 bg-warning bg-opacity-10 text-warning rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Contact & location</p>
                            <p class="fw-semibold mb-0">Map & business hours</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-modal.status />

</x-admin-layout>