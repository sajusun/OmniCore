@extends('backend.app')
@push('styles')
<style>
    #map {
        height: 400px;
        width: 100%;
    }
</style>
@endpush
@section('content')
<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">

            {{-- PAGE-HEADER --}}
            <div class="page-header">
                <div>
                    <h1 class="page-title">Google Map Settings</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Settings</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Google Map</li>
                    </ol>
                </div>
            </div>
            {{-- PAGE-HEADER --}}


            <div class="row">
                <div class="col-lg-7">
                    <div class="card box-shadow-0">
                        <div class="card-body">
                            <form class="form form-horizontal" method="post" action="{{ route('admin.setting.signature.update') }}" enctype="multipart/form-data" id="signatureForm">
                                @csrf
                                @method('PATCH')
                                <div class="row mb-4">
                                    <div class="col-md-9">
                                        <div class="input-group">
                                            <img id="signatureImage" src="" />
                                            <input type="hidden" name="signature" id="signature" value="" required />
                                            <div id="signatureImageSubmitDiv">
                                                <canvas id="signature-pad" width="400" height="200" style="border: 1px solid black;"></canvas>
                                                <br />
                                                <button id="clear" style="background-color: #4CAF50; color: white; padding: 14px 20px; margin: 8px 0; border: none; cursor: pointer;">Clear</button>
                                                <button id="save" style="background-color: #4CAF50; color: white; padding: 14px 20px; margin: 8px 0; border: none; cursor: pointer;">Save</button>
                                            </div>
                                            @error('signature')
                                            <p class="text-danger">{{$message}}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row justify-content-end">
                                        <div class="col-sm-12">
                                            <div>
                                                <button class="submit btn btn-primary" type="submit">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card box-shadow-0">
                        <div class="card-body">
                            <img src="{{ $signature ? 'data:image/png;base64,' . $signature : asset('default/logo.png') }}" alt="Signature" width="400" height="200"/>
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
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<!-- <script src="{{ asset('default/signature_pad.umd.min.js') }}"></script> -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const canvas = document.getElementById('signature-pad');
        const signatureImage = document.getElementById('signatureImage');
        const signatureImageSubmitDiv = document.getElementById('signatureImageSubmitDiv');
        const signature = document.getElementById('signature');
        const signaturePad = new SignaturePad(canvas);

        document.getElementById('clear').addEventListener('click', (e) => {
            e.preventDefault();
            signaturePad.clear();
        });

        document.getElementById('save').addEventListener('click', (e) => {
            e.preventDefault();
            if (signaturePad.isEmpty()) {
                alert("Please provide a signature first.");
            } else {
                const dataURL = signaturePad.toDataURL();
                const img = new Image();
                signatureImageSubmitDiv.style.display = 'none';
                signatureImage.src = dataURL;
                signature.value = dataURL;
            }
        });
    });
</script>
@endpush