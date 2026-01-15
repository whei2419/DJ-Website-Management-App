<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Admin')</title>
    @vite(['resources/js/admin.js','resources/sass/admin.scss'])
  </head>
  <body>
    <header class="navbar navbar-expand navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="/admin">Admin</a>
      </div>
    </header>

    <div class="container-fluid">
      <div class="row">
        <aside class="col-md-2 py-4">
          <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link" href="/admin">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="#">DJs</a></li>
          </ul>
        </aside>
        <section class="col-md-10 py-4">
          @yield('content')
        </section>
      </div>
    </div>
  </body>
</html>
