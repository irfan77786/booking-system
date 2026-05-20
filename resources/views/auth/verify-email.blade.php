@extends('layouts.guest')

@section('guest_data')

<div class="container d-flex align-items-center justify-content-center" style="height: 70%;">
    <div class="card shadow-sm" style="max-width: 480px; width: 100%;">
        <div class="card-body p-4">

            <h3 class="text-center mb-3">{{ __('Verify Your Email') }}</h3>

            <p class="text-muted small mb-4">
                {{ __('Thanks for signing up! Before getting started, please verify your email address by clicking the link we just sent you. If you didn\'t receive the email, you can request another one below.') }}
            </p>

            <!-- Status -->
            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success mb-3">
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mt-4">

                <!-- Resend Email -->
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        {{ __('Resend Verification Email') }}
                    </button>
                </form>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-decoration-none small text-danger">
                        {{ __('Log Out') }}
                    </button>
                </form>

            </div>

        </div>
    </div>
</div>

@endsection
