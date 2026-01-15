@php $menu = config('admin.menu', []); @endphp
<ul class="navbar-nav">
  @foreach($menu as $item)
    @php
      $title = $item['title'] ?? '';
      $icon = $item['icon'] ?? '';
      $route = $item['route'] ?? null;
      $url = $item['url'] ?? null;
      $params = $item['route_params'] ?? [];
      $active = $route ? request()->routeIs($route . '*') : (url()->current() === url($url));
    @endphp
    <li class="nav-item {{ $active ? 'active' : '' }}">
      <a class="nav-link" href="{{ $route ? route($route, $params) : ($url ?? '#') }}" aria-current="{{ $active ? 'page' : '' }}">
        <span class="nav-link-icon d-md-none d-lg-inline-block">
          <i class="{{ $icon }}"></i>
        </span>
        <span class="nav-link-title"> {{ $title }} </span>
        @if(!empty($item['badge']))
          <span class="badge badge-sm bg-{{ $item['badge_color'] ?? 'primary' }} ms-2">{{ $item['badge'] }}</span>
        @endif
      </a>
    </li>
  @endforeach
</ul>
