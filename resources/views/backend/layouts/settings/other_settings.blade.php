@extends('backend.app')

@section('content')
<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">

            {{-- PAGE-HEADER --}}
            <div class="page-header">
                <div>
                    <h1 class="page-title">Other Settings <i class="fa-solid fa-triangle-exclamation text-danger" title="Warning"></i></h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Settings</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Other</li>
                    </ol>
                </div>
            </div>
            {{-- PAGE-HEADER --}}


            <div class="row">
                <div class="col-md-12">
                    <div class="card box-shadow-0 bg-blue-100">
                        <div class="card-body">

                            {{-- MAIL SEND --}}
                            <div class="row mb-4">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label for="mail" class="col-form-label fw-bold mb-0">MAIL Send</label>
                                </div>
                                <div class="col-md-9 d-flex align-items-center">
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input @error('mail') is-invalid @enderror"
                                            type="checkbox" id="mail" name="mail"
                                            {{ $settings['mail'] === 'on' ? 'checked' : '' }}
                                            data-url="{{ route('admin.setting.other.mail') }}" />
                                    </div>
                                </div>
                            </div>

                            {{-- SMS SEND --}}
                            <div class="row mb-4">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label for="sms" class="col-form-label fw-bold mb-0">SMS Send</label>
                                </div>
                                <div class="col-md-9 d-flex align-items-center">
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input @error('sms') is-invalid @enderror"
                                            type="checkbox" id="sms" name="sms"
                                            {{ $settings['sms'] === 'on' ? 'checked' : '' }}
                                            data-url="{{ route('admin.setting.other.sms') }}" />
                                    </div>
                                </div>
                            </div>

                            {{-- RECAPTCHA --}}
                            <div class="row mb-4">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label for="recaptcha" class="col-form-label fw-bold mb-0">RECAPTCHA Enable</label>
                                </div>
                                <div class="col-md-9 d-flex align-items-center">
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input @error('recaptcha') is-invalid @enderror"
                                            type="checkbox" id="recaptcha" name="recaptcha"
                                            {{ $settings['recaptcha'] === 'yes' ? 'checked' : '' }}
                                            data-url="{{ route('admin.setting.other.recaptcha') }}" />
                                    </div>
                                </div>
                            </div>

                            {{-- PAGINATION --}}
                            <div class="row mb-3">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label for="pagination" class="col-form-label fw-bold mb-0">Pagination</label>
                                </div>
                                <div class="col-md-9 d-flex align-items-center" style="padding-left: 0px;">
                                    <div class="d-flex align-items-center" style="height: 38px;">
                                        <input type="number"
                                            class="@error('pagination') is-invalid @enderror"
                                            id="pagination" name="pagination"
                                            min="1" max="100"
                                            value="{{ $settings['pagination'] }}"
                                            data-url="{{ route('admin.setting.other.pagination') }}"
                                            style="max-width: 100px; height: 100%; padding-top: 0.25rem; padding-bottom: 0.25rem;">
                                    </div>
                                </div>
                            </div>

                            {{-- SMS SEND --}}
                            <div class="row mb-4">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label for="reverb" class="col-form-label fw-bold mb-0">Reverb</label>
                                </div>
                                <div class="col-md-9 d-flex align-items-center">
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input @error('reverb') is-invalid @enderror"
                                            type="checkbox" id="reverb" name="reverb"
                                            {{ $settings['reverb'] === 'on' ? 'checked' : '' }}
                                            data-url="{{ route('admin.setting.other.reverb') }}" />
                                    </div>
                                </div>
                            </div>

                            {{-- DEBUG --}}
                            <div class="row mb-4">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label for="debug" class="col-form-label fw-bold mb-0">Debug</label>
                                </div>
                                <div class="col-md-9 d-flex align-items-center">
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input @error('debug') is-invalid @enderror"
                                            type="checkbox"
                                            value="{{ env('APP_DEBUG') }}"
                                            id="debug"
                                            name="debug"
                                            {{ env('APP_DEBUG') === true ? 'checked' : '' }}
                                            data-url="{{ route('admin.setting.other.debug') }}" />
                                    </div>
                                </div>
                            </div>

                            {{-- ACCESS --}}
                            <div class="row mb-4">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label for="access" class="col-form-label fw-bold mb-0">Access</label>
                                </div>
                                <div class="col-md-9 d-flex align-items-center">
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input @error('access') is-invalid @enderror"
                                            type="checkbox"
                                            value="{{ env('ACCESS') }}"
                                            id="access"
                                            name="access"
                                            {{ env('ACCESS') === true ? 'checked' : '' }}
                                            data-url="{{ route('admin.setting.other.access') }}" />
                                    </div>
                                </div>
                            </div>

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

        function jsonUpdateByGet(e) {
            NProgress.start();
            $.ajax({
                url: $(e).data('url'),
                type: "GET",
                success: function(response) {
                    NProgress.done();
                    if (response.status === 't-success') {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }

        function jsonUpdateWithGetData(e) {
            NProgress.start();
            $.ajax({
                url: $(e).data('url'),
                type: "GET",
                data: {
                    "value": $(e).val() ? $(e).val() : 1
                },
                success: function(response) {
                    NProgress.done();
                    if (response.status === 't-success') {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }

        $("#mail").on("change", function() {
            jsonUpdateByGet(this);
        });

        $("#sms").on("change", function() {
            jsonUpdateByGet(this);
        });

        $("#recaptcha").on("change", function() {
            jsonUpdateByGet(this);
        });

        $("#reverb").on("change", function() {
            jsonUpdateByGet(this);
        });

        $("#pagination").on("keyup", function() {
            jsonUpdateWithGetData(this);
        });

        $("#debug").on("change", function() {
            NProgress.start();
            $.ajax({
                url: $(this).data('url'),
                type: "GET",
                success: function(response) {
                    NProgress.done();
                    if (response.status === 't-success') {
                        toastr.success(response.message);
                        window.location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        });

        $("#access").on("change", function() {
            NProgress.start();
            $.ajax({
                url: $(this).data('url'),
                type: "GET",
                success: function(response) {
                    NProgress.done();
                    if (response.status === 't-success') {
                        toastr.success(response.message);
                        window.location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        });

    });
</script>
@endpush