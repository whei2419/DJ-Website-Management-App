<div class="container-fluid gallery-page">
    <div class="container brand-container" data-aos="fade-down" data-aos-duration="800">
        <img src="{{ asset('assets/images/0001_bueaty light club_logo_White.webp') }}" alt="Logo" class="logo-image">
    </div>

    <div class="gallery-section">
        <h1 data-aos="fade-up" data-aos-duration="700">Gallery</h1>

        <div class="gallery-date-controller" data-aos="fade-up" data-aos-duration="700" data-aos-delay="200">
            <button class="left gallery-arrow" aria-label="Previous date">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <p class="date" id="date">Loading dates...</p>
            <button class="right gallery-arrow" aria-label="Next date">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>

        <div class="gallery">
            <div class="gallery-content">
                <!-- DJ cards will be dynamically inserted here by gallery.js -->
                <p class="loading-message">Loading performances...</p>
            </div>
        </div>
    </div>

    <!-- Modal for full DJ video -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 dj-name" id="exampleModalLabel">DJ Performance</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <video class="selected-vide w-100" controls playsinline></video>
                </div>
            </div>
        </div>
    </div>
</div>
