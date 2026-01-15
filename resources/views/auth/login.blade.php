@extends('layouts.admin')

@section('title', 'Admin Login')

@section('content')
<div class="page page-center">
    <div class="container-tight py-4">
        <div class="text-center mb-5">
            <a href="/" class="navbar-brand navbar-brand-autodark">
                <img src="{{ URL::asset('assets/images/logo.webp') }}" height="48" alt="Your Logo">
            </a>
        </div>
        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 text-center mb-4">{{ __('Sign in to your account') }}</h2>
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">{{ __('Email address') }}</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label">
                            {{ __('Password') }}
                            @if (Route::has('password.request'))
                                <span class="form-label-description">
                                    <a href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                                </span>
                            @endif
                        </label>
                        <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" name="remember">
                            <span class="form-check-label">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">{{ __('Sign in') }}</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-center text-muted mt-3">
            {{ __('Don\'t have an account?') }} <a href="{{ route('register') }}" tabindex="-1">{{ __('Sign up') }}</a>
        </div>
    </div>
</div>
@endsection
