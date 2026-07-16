@extends('auth.app')

@section('content')
<!-- CONTAINER OPEN -->
<div class="col col-login mx-auto text-center">
    <a href="index.html" class="text-center">
        <img src="{{ asset($settings->logo ?? 'default/logo.png') }}" class="header-brand-img" alt="">
    </a>
</div>
<div class="container-login100">
    <div class="wrap-login100 p-0">
        <div class="card-body">
            <form class="login100-form validate-form" method="POST" action="{{ route('verify.otp.resend') }}">
                @csrf
                <div class="login100-form-title">
                    <h2>Resend OTP</h2>
                </div>

                <div class="wrap-input100">
                    <input class="input100" type="text" name="email" placeholder="{{ session('email') ?? 'Email' }}" value="{{ session('email') ?? '' }}">
                    <span class="symbol-input100">
                        <i class="zmdi zmdi-email" aria-hidden="true"></i>
                    </span>
                </div>
                @error('email')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <div class="container-login100-form-btn">
                    <button type="submit" class="login100-form-btn btn-primary">
                        Resend
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<!-- CONTAINER CLOSED -->
@endsection

