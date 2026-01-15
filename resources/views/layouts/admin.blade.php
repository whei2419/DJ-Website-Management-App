<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Admin')</title>
    @vite(['resources/js/admin.js','resources/sass/admin.scss'])
    @include('components.font-awesome')
  </head>
  <body class="{{ str_replace('.', '-', Route::currentRouteName()) }}">
    @if(Route::currentRouteName() !== 'login')
      <header class="navbar navbar-expand-lg navbar-dark" style="background: #1e293b; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div class="container">
          <a class="navbar-brand d-flex align-items-center text-white" href="/admin">
            <i class="fas fa-music me-2"></i>
            <span class="fw-bold">DJ Management</span>
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
              <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('dashboard') ? 'active fw-bold' : '' }}" href="/dashboard">
                  <i class="fas fa-home me-1"></i> Dashboard
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('admin/djs') ? 'active fw-bold' : '' }}" href="/admin/djs">
                  <i class="fas fa-headphones me-1"></i> DJs
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('admin/djs/create') ? 'active fw-bold' : '' }}" href="/admin/djs/create">
                  <i class="fas fa-plus-circle me-1"></i> Create DJ
                </a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                  <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="/profile"><i class="fas fa-user me-2"></i> Profile</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                    </form>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </header>
    @endif

    @yield('content')
  </body>
</html>
