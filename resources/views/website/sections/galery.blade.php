<div class="container-fluid gallery-page">
    <div class="container brand-container" data-aos="fade-down" data-aos-duration="800">
        <img src="{{ asset('assets/images/0001_bueaty light club_logo_White.webp') }}" alt="Logo" class="logo-image">
    </div>

    <div class="gallery-section">
        <h1 data-aos="fade-up" data-aos-duration="700">Gallery</h1>

        <style>
            /* Date selector: plain arrow icons without circular button background */
            .gallery-date-controller .gallery-arrow {
                background: transparent !important;
                border: none !important;
                width: auto !important;
                height: auto !important;
                padding: 6px !important;
                border-radius: 0 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                color: #fff !important;
                box-shadow: none !important;
            }

            .gallery-date-controller .gallery-arrow svg {
                stroke: currentColor;
                width: 22px;
                height: 22px;
            }
        </style>

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
            <style>
                .gallery {
                    position: relative;
                }

                .carousel-viewport {
                    overflow: hidden;
                    position: relative;
                }

                .carousel-track {
                    display: flex;
                    gap: 16px;
                    align-items: stretch;
                }

                .cards-arrow {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    border: none;
                    background: transparent;
                    width: 44px;
                    height: 44px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    color: #fff;
                    z-index: 20;
                }

                .cards-arrow.left {
                    left: 8px;
                }

                .cards-arrow.right {
                    right: 8px;
                }

                .cards-arrow svg {
                    width: 28px;
                    height: 28px;
                    stroke: currentColor;
                }

                .carousel-viewport {
                    padding: 8px 56px;
                    min-height: 600px
                }

                .gallery-content .card {
                    box-shadow: none;
                    border: none;
                }

                /* Center empty/loading messages inside the carousel viewport */
                .carousel-viewport .gallery-content .no-data,
                .carousel-viewport .gallery-content .loading-message {
                    position: absolute;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                    margin: 0;
                    color: #fff;
                    font-size: 1rem;
                    text-align: center;
                }
            </style>
            <button class="cards-arrow left" aria-label="Previous performance">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>

            <div class="carousel-viewport">
                <div class="gallery-content carousel-track">
                    <!-- DJ cards will be dynamically inserted here by gallery.js -->
                    <p class="loading-message">Loading performances...</p>
                </div>
            </div>

            <button class="cards-arrow right" aria-label="Next performance">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>
    </div>

    <!-- Modal for full DJ video -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border">
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
