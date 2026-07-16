<x-admin-layout>
    @slot('title')
    Site Information Settings
    @endslot
    @slot('header')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        {{-- <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-100 leading-tight tracking-tight">
            General Settings
        </h2> --}}
        <nav
            class="text-sm font-medium text-gray-500 dark:text-gray-400 hidden sm:flex items-center bg-white/50 dark:bg-gray-800/50 px-4 py-2 rounded-full shadow-sm border border-gray-200/50 dark:border-gray-700/50 backdrop-blur-sm">
            <ol class="flex list-none p-0 space-x-2">
                <li class="flex items-center">
                    <a href="javascript:void(0);" class="hover:text-indigo-600 transition-colors">Settings</a>
                    <span class="mx-2 text-gray-300 dark:text-gray-600">/</span>
                </li>
                <li class="flex items-center text-gray-700 dark:text-gray-200 font-medium" aria-current="page">
                    Site Settings
                </li>
            </ol>
        </nav>
    </div>
    @endslot

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Enhanced card with deeper shadow & glass-morphism effect -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-indigo-100/30 dark:shadow-gray-900/50 border border-gray-100/50 dark:border-gray-700/50 overflow-hidden backdrop-blur-sm transition-all duration-300 hover:shadow-indigo-200/40 dark:hover:shadow-gray-700/30">
                <div
                    class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30 flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/40 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 dark:text-indigo-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Configure general settings</h3>
                    <span
                        class="ml-auto text-xs bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 px-3 py-1 rounded-full border border-indigo-100 dark:border-indigo-800">required
                        fields *</span>
                </div>

                <div class="card-body p-6 sm:p-8">
                    <form class="form form-horizontal" method="post"
                        action="{{ route('admin.setting.general.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <!-- Grid layout for better structure -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 gap-y-8">
                            <!-- Left column -->
                            <div class="space-y-6">
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
                            <div class="space-y-6">
                                <x-form.text name="phone" label="Phone" placeholder="Contact phone"
                                    value="{{ $setting->phone ?? old('phone') ?? '' }}" />

                                <x-form.text name="email" label="Email" placeholder="Contact email"
                                    value="{{ $setting->email ?? old('email') ?? '' }}" />

                                <x-form.text name="address" label="Address" placeholder="Physical address"
                                    value="{{ $setting->address ?? old('address') ?? '' }}" />

                                <x-form.text name="copyright" label="Copyright" placeholder="Copyright text"
                                    value="{{ $setting->copyright ?? old('copyright') ?? '' }}" />

                                {{--
                                <x-form.text name="business_time" label="Business Hour"
                                    placeholder="e.g. Mon-Fri 9am-6pm"
                                    value="{{ $setting->business_time ?? old('business_time') ?? '' }}" /> --}}
                                <x-form.text name="map_embed_code" label="Map Embed URL (Only Iframe embed link)"
                                    placeholder="Paste your Google Maps iframe embed code"
                                    value="{{ $setting->map_embed_code ?? old('map_embed_code') ?? '' }}" />
                            </div>

                            <!-- Full width fields: map + files -->
                            <div class="col-span-1 lg:col-span-2 space-y-6">

                                <!-- File uploads in a responsive row -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                                    <div
                                        class="bg-gray-50/80 dark:bg-gray-800/50 p-5 rounded-xl border border-gray-200/60 dark:border-gray-700/60 shadow-sm">
                                        <x-form.file name="logo" label="Logo" file="{{ $setting->logo ?? '' }}">
                                            <p
                                                class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Max 5MB · jpeg, jpg, png
                                            </p>
                                        </x-form.file>
                                    </div>
                                    <div
                                        class="bg-gray-50/80 dark:bg-gray-800/50 p-5 rounded-xl border border-gray-200/60 dark:border-gray-700/60 shadow-sm">
                                        <x-form.file name="favicon" label="Favicon"
                                            file="{{ $setting->favicon ?? '' }}">
                                            <p
                                                class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Max 5MB · jpeg, jpg, png, ico
                                            </p>
                                        </x-form.file>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div
                            class="mt-8 pt-6 border-t border-gray-200/60 dark:border-gray-700/60 flex flex-wrap items-center justify-between gap-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    All changes are saved immediately
                                </span>
                            </div>
                            <button
                                class="submit btn btn-primary inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-medium rounded-xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/40 transition-all duration-200 hover:shadow-indigo-300/50 dark:hover:shadow-indigo-800/30 hover:-translate-y-0.5 focus:ring-4 focus:ring-indigo-300/50"
                                type="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
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
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg shadow-gray-100/40 dark:shadow-gray-900/30 p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600 dark:text-emerald-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">SEO ready</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Meta tags & keywords</p>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg shadow-gray-100/40 dark:shadow-gray-900/30 p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Brand identity</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Thumbnail & Favicon</p>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg shadow-gray-100/40 dark:shadow-gray-900/30 p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-3">
                    <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 dark:text-amber-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Contact & location</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Map & business hours</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-modal.status />

</x-admin-layout>