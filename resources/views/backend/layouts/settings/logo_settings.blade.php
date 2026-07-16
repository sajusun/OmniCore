@extends('backend.app', ['title' => 'Logo Settings'])

@section('content')
<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">

            {{-- PAGE-HEADER --}}
            <div class="page-header">
                <div>
                    <h1 class="page-title">Logo Settings</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Settings</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Logo Settings</li>
                    </ol>
                </div>
            </div>
            {{-- PAGE-HEADER --}}

            <div class="row">
                <div class="col-lg-7">
                    <div class="card box-shadow-0">
                        <div class="card-body">
                            <form class="form form-horizontal" method="post" action="{{ route('admin.setting.logo.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                <div class="row mb-4">

                                    <div class="form-group">
                                        <label for="logo_width" class="form-label">Logo Width:</label>
                                        <input type="number" class="form-control @error('logo_width') is-invalid @enderror"
                                            name="logo_width" placeholder="logo width" id="logo_width"
                                            value="{{ $setting->logo_width ?? old('logo_width') ?? 240 }}">
                                        @error('logo_width')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="logo_height" class="form-label">Logo Height:</label>
                                        <input type="number" class="form-control @error('logo_height') is-invalid @enderror"
                                            name="logo_height" placeholder="logo width" id="logo_height"
                                            value="{{ $setting->logo_height ?? old('logo_height') ?? 240 }}">
                                        @error('logo_height')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="logo" class="form-label">Logo:</label>
                                                <input type="file" class="dropify form-control @error('logo') is-invalid @enderror"
                                                    data-default-file="{{ !empty($setting->logo) && file_exists(public_path($setting->logo)) ? asset($setting->logo) : asset('default/logo.png') }}"
                                                    name="logo" id="logo">
                                                    <p class="textTransform">Image Size Less than 5MB and Image Type must be jpeg,jpg,png.</p>
                                                @error('logo')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button class="submit btn btn-primary" type="submit">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card box-shadow-0">
                        <div class="card-body text-center">
                            <img id="logo-preview" src="{{ !empty($setting->logo) && file_exists(public_path($setting->logo)) ? asset($setting->logo) : asset('default/logo.svg') }}" alt="Logo" width="{{ $setting->logo_width ?? 240 }}" height="{{ $setting->logo_height ?? 240 }}"/>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<!-- CONTAINER CLOSED -->
@endsection
@push('scripts')
<script>
$(document).ready(function() {
    $('#logo_height').change(function() {
        $('#header-brand-logo').attr('height', $(this).val());
        $('#logo-preview').attr('height', $(this).val());
    })
    $('#logo_width').change(function() {
        $('#header-brand-logo').attr('width', $(this).val());
        $('#logo-preview').attr('width', $(this).val());
    })
})
</script>
@endpush