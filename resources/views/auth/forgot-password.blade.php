@extends('layouts.guest')

@section('guest_data')

<div class="container d-flex align-items-center justify-content-center" style="height: 70%;">
    <div class="shadow-card p-4" style="max-width: 420px; width: 100%;">
            <h3 class="text-center mb-4">{{ __('Forgot Password') }}</h3>

            <p class="text-muted small mb-4">
                {{ __('Forgot your password? No problem. Just enter your email and we will send you a password reset link.') }}
            </p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success mb-3">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="floating-bordered-input position-relative">
                    <span class="floating-label">{{ __('Email address') }}</span>
                    <span class="input-icon-left"><i class="bi bi-envelope-fill"></i></span>
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus placeholder=" ">
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    {{ __('Email Password Reset Link') }}
                </button>

                <!-- Back to login -->
                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="text-decoration-none small">
                        ← {{ __('Back to Login') }}
                    </a>
                </div>

            </form>
    </div>
</div>

@endsection
