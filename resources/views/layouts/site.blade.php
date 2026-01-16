<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Site')</title>
    @vite(['resources/js/site.js','resources/sass/site.scss'])
  </head>
  <body>
    @include('website.partials.navbar')
   
    <main class="container-fluid p-0 m-0">
      @yield('content')
    </main>

    {{-- scripts already included in head via Vite --}}
    @stack('scripts')
  </body>
</html>
