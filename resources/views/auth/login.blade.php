@extends('layouts.guest')

@section('guest_data')

<div class="container d-flex align-items-center justify-content-center" style="height: 70%;">
    <div class="shadow-card p-4" style="max-width: 420px; width: 100%;">
            <h3 class="text-center mb-4">{{ __('Login') }}</h3>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success mb-3">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="text" name="login_type" value="plain" hidden>
                <div class="floating-bordered-input position-relative">
                    <span class="floating-label">{{ __('Email address') }}</span>
                    <span class="input-icon-left"><i class="bi bi-envelope-fill"></i></span>
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus placeholder=" ">
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="floating-bordered-input position-relative">
                    <span class="floating-label">{{ __('Password') }}</span>
                    <span class="input-icon-left"><i class="bi bi-lock-fill"></i></span>
                    <input id="password" type="password" class="form-control" name="password" required placeholder=" ">
                    @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                    <label class="form-check-label" for="remember_me">{{ __('Remember me') }}</label>
                </div>

                <div class="text-left mb-1">
                    @if (Route::has('password.request'))
                        <a class="text-decoration-none small" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                    @endif
                    <button type="submit" class="btn btn-primary btn-uniform w-100 mt-2">{{ __('Log in') }}</button>
                </div>
            </form>
    </div>
</div>
@endsection
