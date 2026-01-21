<div class="container-fluid footer">
    <div class="container text-center py-4">
        <div class="row">
            <div class="col" data-aos="fade-up" data-aos-duration="700">
                <div class="image-container">
                    <img src="{{ asset('assets/images/logo.webp') }}" alt="Section Image" class="logo-1 mb-4">
                    <img src="{{ asset('assets/images/0001_bueaty light club_logo_White.webp') }}" alt="Section Image"
                        class="logo-2 mb-4">
                </div>
            </div>
            <div class="col" data-aos="fade-up" data-aos-duration="700" data-aos-delay="200">
                <div class="footer-links">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <ul>
                                <li><a href="{{ route('site.gallery') }}">menu</a></li>
                                <li><a
                                        href="https://outlook.office.com/book/YSLBEAUTYLIGHTCLUBRISINGBEATS@loreal.onmicrosoft.com/?ismsaljsauthenabled" target="_blank" rel="noopener">Register
                                        now</a></li>
                                <li><a href="#howToParticipate">How to participate?</a></li>
                            </ul>
                        </div>
                        <div class="col-md-6 col-12">
                            <ul>
                                <li><a href="{{ route('site.faq') }}">info</a></li>
                                <li><a href="{{ route('site.faq') }}">FAQs</a></li>
                                {{-- <li><a href="#">Privacy Policy</a></li> --}}
                                <li><a href="{{ route('site.pda') }}">Terms &amp; Conditions</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
