@extends('layouts.admin')

@section('title', 'Admin Login')

@section('hide-navbar', true)

@section('content')
<div class="row justify-content-center align-items-center vh-100">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white text-center">
                <h4>{{ __('Admin Login') }}</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input id="password" type="password" class="form-control" name="password" required>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                        </div>

                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Password?') }}
                            </a>
                        @endif
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100">{{ __('Login') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
