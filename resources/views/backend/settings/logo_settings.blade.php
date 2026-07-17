<x-admin-layout>
    <x-slot name="title">Logo Settings</x-slot>
    <div class="d-flex flex-column gap-4">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="fs-4 fw-bold mb-0">
                    Logo Settings
                </h1>
                <p class="text-muted small mt-1 mb-0">
                    Update your application logo and dimensions.
                </p>
            </div>

            <nav class="small text-muted">
                Settings /
                <span class="text-primary fw-medium">Logo Settings</span>
            </nav>
        </div>

        <div class="row g-3">

            {{-- Form --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border">

                    <div class="card-body p-4">

                        <form action="{{ route('admin.setting.general.logo.update') }}" method="POST"
                            enctype="multipart/form-data" class="d-flex flex-column gap-3">

                            @csrf
                            @method('PATCH')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-form.text name="logo_width" label="Logo Width" type="number"
                                        :value="old('logo_width', $setting->logo_width ?? 240)" />
                                </div>
                                <div class="col-md-6">
                                    <x-form.text name="logo_height" label="Logo Height" type="number"
                                        :value="old('logo_height', $setting->logo_height ?? 240)" />
                                </div>
                            </div>
                            <div>
                                <x-form.file label="Logo" name="logo" :file="$setting->logo ?? ''" />
                                <p class="text-muted small mt-1 mb-0"> Maximum size 5MB. Supported formats: JPG, JPEG, PNG, SVG. </p>
                            </div>
                            <div>
                                <x-form.submit class="btn btn-primary px-4"> Save Change </x-form.submit>
                            </div>

                        </form>

                    </div>

                </div>
            </div>

            {{-- Preview --}}
            <div class="col-lg-4">

                <div class="card shadow-sm border">

                    <div class="card-body p-4">

                        <h2 class="card-title fw-bold mb-3">
                            Preview
                        </h2>

                        <div class="d-flex justify-content-center bg-light p-3 rounded border">

                            <img id="logo-preview" src="{{ !empty($setting->logo) && file_exists(public_path($setting->logo))
                                    ? asset($setting->logo)
                                    : asset('default/logo.svg') }}" width="{{ $setting->logo_width ?? 240 }}"
                                height="{{ $setting->logo_height ?? 240 }}" class="object-contain" style="max-width: 100%;">

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