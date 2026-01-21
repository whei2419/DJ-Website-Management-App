@include('website.partials.fonts')

<div class="nav-container">
    <div class="container-fluid p-0 m-0 ">
        <div class="nav-header">
            <div class="logo-container">
                <a href="{{ route('site.index') }}" class="logo-link">
                    <img src="{{ asset('assets/images/logo.webp') }}" alt="Logo" class="logo-image">
                </a>
            </div>
            <button class="mobile-menu-toggle" aria-label="Toggle menu" aria-expanded="false">
                <span class="hamburger-icon"></span>
            </button>
        </div>
        <div class="nav-bottom">
            <ul>
                <li class="nav-item">
                    <a href="{{ route('site.index') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('site.gallery') }}">Gallery</a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" aria-haspopup="true" aria-expanded="false" tabindex="0">Learn more</a>
                    <div class="dropdown-menu" aria-label="submenu">
                        <a href="{{ route('site.faq') }}">FAQ</a>
                        <a href="{{ route('site.pda') }}">Terms &amp; Conditions</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a
                        href="https://outlook.office.com/book/YSLBEAUTYLIGHTCLUBRISINGBEATS@loreal.onmicrosoft.com/?ismsaljsauthenabled">Register
                        now</a>
                </li>
            </ul>

        </div>
    </div>
</div>
