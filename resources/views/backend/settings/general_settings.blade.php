<x-admin-layout>
    @slot('title')
        Site Information Settings
    @endslot

    <div class="py-4">
        <div class="container-fluid">
            <x-page-header title="Site Information Settings" :breadcrumbs="['Settings' => 'javascript:void(0);', 'Site Settings' => null]" class="mb-4" />

            <!-- Enhanced card using unified modern component -->
            <x-card title="Configure general settings" badge="required fields *" badge-class="badge bg-primary">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </x-slot:icon>

                <form class="form form-horizontal" method="post" action="{{ route('admin.setting.general.update') }}" enctype="multipart/form-data">
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
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-2"
                                         style="background-color: var(--theme-table-header-bg, #f8fafc); border-color: var(--theme-card-border, #e2e8f0);">
                                        <x-form.file name="logo" label="Logo" :file="$setting?->logo ? url($setting->logo) : ''" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-2"
                                         style="background-color: var(--theme-table-header-bg, #f8fafc); border-color: var(--theme-card-border, #e2e8f0);">
                                        <x-form.file name="favicon" label="Favicon" :file="$setting?->favicon ? url($setting->favicon) : ''" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <x-form.actions submit-text="Update Settings" />
                </form>
            </x-card>

            <!-- Additional info cards with theme tokens -->
            <div class="row g-3 mt-4">
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100"
                         style="background-color: var(--theme-card-bg, #ffffff); border-color: var(--theme-card-border, #e2e8f0); border-radius: var(--theme-card-radius, 12px);">
                        <div class="p-2.5 rounded-2 bg-success bg-opacity-10 text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">SEO ready</p>
                            <p class="fw-semibold mb-0" style="color: var(--theme-heading-color, #0f172a);">Meta tags & keywords</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100"
                         style="background-color: var(--theme-card-bg, #ffffff); border-color: var(--theme-card-border, #e2e8f0); border-radius: var(--theme-card-radius, 12px);">
                        <div class="p-2.5 rounded-2 bg-primary bg-opacity-10 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Brand identity</p>
                            <p class="fw-semibold mb-0" style="color: var(--theme-heading-color, #0f172a);">Thumbnail & Favicon</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-3 d-flex flex-row align-items-center gap-3 border shadow-sm h-100"
                         style="background-color: var(--theme-card-bg, #ffffff); border-color: var(--theme-card-border, #e2e8f0); border-radius: var(--theme-card-radius, 12px);">
                        <div class="p-2.5 rounded-2 bg-warning bg-opacity-10 text-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Contact & location</p>
                            <p class="fw-semibold mb-0" style="color: var(--theme-heading-color, #0f172a);">Map & business hours</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-modal.status />
</x-admin-layout>