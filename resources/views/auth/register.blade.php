@extends('layouts.guest')

@section('guest_data')

<div class="container d-flex align-items-center justify-content-center" style="height: 70%;">
    <div class="card shadow-sm" style="max-width: 460px; width: 100%;">
        <div class="card-body p-4">
            <h3 class="text-center mb-4">{{ __('Create Account') }}</h3>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- First Name -->
                <div class="mb-3">
                    <label for="first_name" class="form-label">{{ __('First Name') }}</label>
                    <input id="first_name" type="text"
                           class="form-control @error('first_name') is-invalid @enderror"
                           name="first_name" value="{{ old('first_name') }}" required autofocus
                           autocomplete="given-name"
                           placeholder="Enter first name">
                    @error('first_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Last Name -->
                <div class="mb-3">
                    <label for="last_name" class="form-label">{{ __('Last Name') }}</label>
                    <input id="last_name" type="text"
                           class="form-control @error('last_name') is-invalid @enderror"
                           name="last_name" value="{{ old('last_name') }}" required
                           autocomplete="family-name"
                           placeholder="Enter last name">
                    @error('last_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <input id="email" type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required
                           autocomplete="username"
                           placeholder="Enter your email">
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input id="password" type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           name="password" required autocomplete="new-password"
                           placeholder="Enter password">
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                    <input id="password_confirmation" type="password"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           name="password_confirmation" required autocomplete="new-password"
                           placeholder="Confirm your password">
                    @error('password_confirmation')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-primary w-100">
                    {{ __('Register') }}
                </button>

                <!-- Login Link -->
                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="text-decoration-none small">
                        {{ __('Already registered?') }}
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
