<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin')</title>
    @include('admin.partials.head')
</head>

<body class="{{ str_replace('.', '-', Route::currentRouteName()) }} page">
    @if (Route::currentRouteName() !== 'login')
        <header class="navbar navbar-expand-md d-print-none">
            <div class="container-xl">
                <!-- BEGIN NAVBAR TOGGLER -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
                    aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- END NAVBAR TOGGLER -->
                <!-- BEGIN NAVBAR LOGO -->
                <div class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                    <a href="." aria-label="Admin">
                        Admin
                    </a>
                </div>
                <!-- END NAVBAR LOGO -->
                <div class="navbar-nav flex-row order-md-last">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown"
                            aria-label="Open user menu">
                            <span
                                class="avatar avatar-sm avatar-initial me-2">{{ strtoupper(mb_substr(Auth::user()->name ?? 'A', 0, 1)) }}</span>
                            <div class="d-none d-xl-block ps-2">
                                <div>{{ Auth::user()->name ?? 'Admin' }}</div>
                                <div class="mt-1 small text-secondary">
                                    {{ Auth::user()->is_admin ?? false ? 'Administrator' : Auth::user()->role ?? 'User' }}
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>
                                    Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <header class="navbar-expand-md">
            <div class="collapse navbar-collapse" id="navbar-menu">
                <div class="navbar">
                    <div class="container-xl">
                        <div class="row flex-column flex-md-row flex-fill align-items-center">
                            <div class="col">
                                <!-- BEGIN NAVBAR MENU -->
                                @include('admin.partials.nav')
                                <!-- END NAVBAR MENU -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    @endif

    @yield('content')
    <!-- Global toaster -->
    <div id="tablerToasts" class="position-fixed top-0 end-0 p-3" style="z-index:1080; width: 360px;">
        <!-- toasts will be injected here -->
    </div>
    @include('admin.partials.scripts')
    @vite(['resources/js/admin.js'])
    @stack('scripts')
</body>

</html>
