<x-admin-layout>
    <x-slot name="title">Logo Settings</x-slot>
    <div class="space-y-6">

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Logo Settings
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Update your application logo and dimensions.
                </p>
            </div>

            <nav class="text-sm text-gray-500 dark:text-gray-400">
                Settings /
                <span class="text-primary-600 font-medium">Logo Settings</span>
            </nav>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Form --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">

                    <div class="p-6">

                        <form action="{{ route('admin.setting.general.logo.update') }}" method="POST"
                            enctype="multipart/form-data" class="space-y-6">

                            @csrf
                            @method('PATCH')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <x-form.text name="logo_width" label="Logo Width" type="number"
                                    :value="old('logo_width', $setting->logo_width ?? 240)" />
                                <x-form.text name="logo_height" label="Logo Height" type="number"
                                    :value="old('logo_height', $setting->logo_height ?? 240)" />
                            </div>
                            <div>
                                <x-form.file label="Logo" name="logo" :file="$setting->logo ?? ''" />
                                <p class="mt-2 text-xs text-gray-500"> Maximum size 5MB. Supported formats: JPG, JPEG,
                                    PNG, SVG. </p>
                            </div>
                            <div>
                                <x-form.submit> Save Change </x-form.submit>
                            </div>

                        </form>

                    </div>

                </div>
            </div>

            {{-- Preview --}}
            <div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">

                    <div class="p-6">

                        <h2 class="text-lg font-semibold mb-6">
                            Preview
                        </h2>

                        <div class="flex justify-center">

                            <img id="logo-preview" src="{{ !empty($setting->logo) && file_exists(public_path($setting->logo))
                                    ? asset($setting->logo)
                                    : asset('default/logo.svg') }}" width="{{ $setting->logo_width ?? 240 }}"
                                height="{{ $setting->logo_height ?? 240 }}" class="object-contain">

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
    <x-modal.status />

    @push('scripts')
    <script>
        const widthInput = document.getElementById('logo_width');
            const heightInput = document.getElementById('logo_height');
            const preview = document.getElementById('logo-preview');

            widthInput.addEventListener('input', function () {
                preview.width = this.value;
            });

            heightInput.addEventListener('input', function () {
                preview.height = this.value;
            });

            document.getElementById('logo').addEventListener('change', function (e) {
                if (!e.target.files.length) return;

                preview.src = URL.createObjectURL(e.target.files[0]);
            });
    </script>
    @endpush

</x-admin-layout>