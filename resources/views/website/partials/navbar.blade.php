@include('website.partials.fonts')

<div class="nav-container">
    <div class="container-fluid">
        <div class="logo-container">
            <a href="{{ route('site.index') }}" class="logo-link">
                <img src="{{ asset('assets/images/logo.webp') }}" alt="Logo" class="logo-image">
            </a>
        </div>
        <div class="nav-bottom">
            <ul>
                <li class="nav-item">
                    <a href="">Home</a>
                </li>
                <li class="nav-item">
                    <a href="">Gallery</a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle" aria-haspopup="true" aria-expanded="false" tabindex="0">Learn more</a>
                    <div class="dropdown-menu" aria-label="submenu">
                        <a href="{{ url('/about') }}">About</a>
                        <a href="{{ url('/faq') }}">FAQ</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="">Register now</a>
                </li>
            </ul>

        </div>
    </div>
</div>

