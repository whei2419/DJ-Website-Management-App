<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Admin')</title>
    @vite(['resources/js/admin.js','resources/sass/admin.scss'])
    @include('components.font-awesome')
    <link rel="stylesheet" href="//cdn.datatables.net/2.3.6/css/dataTables.dataTables.min.css">
    <style>
      /* Small UI polish for admin toasts and icon buttons */
      .btn-icon { width:34px; height:34px; padding:6px; display:inline-flex; align-items:center; justify-content:center; border-radius:6px; }
      #tablerToasts .admin-toast { background:#fff; border-radius:8px; box-shadow:0 6px 18px rgba(15,23,42,0.08); display:flex; align-items:flex-start; padding:12px; gap:12px; border-left:6px solid transparent; }
      #tablerToasts .admin-toast .admin-toast-icon { font-size:20px; line-height:1; }
      #tablerToasts .admin-toast .admin-toast-body .title { font-weight:600; font-size:13px; }
      #tablerToasts .admin-toast .admin-toast-body .message { font-size:13px; color:#475569; }
    </style>
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
                <a class="nav-link text-white {{ Request::is('admin/djs*') ? 'active fw-bold' : '' }}" href="/admin/djs">
                  <i class="fas fa-headphones me-1"></i> DJs
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white {{ Request::is('admin/dates*') ? 'active fw-bold' : '' }}" href="/admin/dates">
                  <i class="fas fa-calendar me-1"></i> Dates
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

    <!-- Global toaster -->
    <div id="tablerToasts" class="position-fixed top-0 end-0 p-3" style="z-index:1080; width: 360px; margin-top: 64px;">
      <!-- toasts will be injected here -->
    </div>

    <script>
      (function(){
        // Expose a simple global toaster compatible with Tabler look
        window.adminToaster = {
          show: function(type, message, timeout = 4000){
            if(!message) return;
            var container = document.getElementById('tablerToasts');
            if(!container) return;

            var colors = {
              success: 'bg-success text-white',
              error: 'bg-danger text-white',
              warning: 'bg-warning text-dark',
              info: 'bg-info text-dark'
            };

            var colorClass = colors[type] || colors.info;

            var el = document.createElement('div');
            el.className = 'mb-2 p-2 d-flex align-items-center shadow-sm rounded bg-white border';
            el.style.minWidth = '260px';
            el.setAttribute('role','alert');

            var icon = document.createElement('div');
            icon.className = 'admin-toast-icon';
            var colors = {
              success: '#16a34a',
              error: '#dc2626',
              warning: '#f59e0b',
              info: '#2563eb'
            };
            var color = colors[type] || colors.info;

            // set left accent color and icon
            el.className = 'admin-toast';
            el.style.borderLeftColor = color;

            icon.innerHTML = (type === 'success') ? '<i class="fas fa-check-circle" style="color:' + color + '"></i>' :
                              (type === 'error') ? '<i class="fas fa-times-circle" style="color:' + color + '"></i>' :
                              (type === 'warning') ? '<i class="fas fa-exclamation-triangle" style="color:' + color + '"></i>' :
                              '<i class="fas fa-info-circle" style="color:' + color + '"></i>';

            var body = document.createElement('div');
            body.className = 'admin-toast-body';
            var title = document.createElement('div');
            title.className = 'title';
            title.innerText = (type === 'success') ? 'Success' : (type === 'error' ? 'Error' : (type === 'warning' ? 'Warning' : 'Info'));
            var msg = document.createElement('div');
            msg.className = 'message';
            msg.innerText = message;
            body.appendChild(title);
            body.appendChild(msg);

            var close = document.createElement('button');
            close.className = 'btn btn-link text-muted p-0 ms-3';
            close.style.border = 'none';
            close.innerHTML = '<i class="fas fa-times"></i>';
            close.addEventListener('click', function(){ if(container.contains(el)) container.removeChild(el); });

            el.appendChild(icon);
            el.appendChild(body);
            el.appendChild(close);
            container.appendChild(el);

            setTimeout(function(){
              if(container.contains(el)) container.removeChild(el);
            }, timeout);
          }
        };

        // Show any server flash messages if present
        document.addEventListener('DOMContentLoaded', function(){
          var flash = {
            success: @json(session('success')),
            error: @json(session('error')),
            info: @json(session('info')),
            warning: @json(session('warning'))
          };
          if(flash.success) window.adminToaster.show('success', flash.success);
          if(flash.error) window.adminToaster.show('error', flash.error);
          if(flash.info) window.adminToaster.show('info', flash.info);
          if(flash.warning) window.adminToaster.show('warning', flash.warning);
        });
      })();
    </script>
      <!-- jQuery and DataTables JS (for enhanced tables) -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="//cdn.datatables.net/2.3.6/js/dataTables.dataTables.min.js"></script>
  </body>
</html>
