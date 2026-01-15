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
      <header class="navbar navbar-expand navbar-dark bg-dark">
        <div class="container-fluid">
          <a class="navbar-brand" href="/admin">Admin</a>
          <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="/admin">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="#">DJs</a></li>
          </ul>
        </div>
      </header>
    @endif

    <div class="container-fluid py-4">
      @yield('content')
    </div>
  </body>
</html>
