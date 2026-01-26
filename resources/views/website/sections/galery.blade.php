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
            <p class="date" id="gallery-date">Loading dates...</p>
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

                /* Card sizing: let CSS control responsive widths */
                .gallery-content .card {
                    flex: 0 0 auto;
                    width: 320px;
                    max-width: 100%;
                }

                .gallery-content .card .dj-video-preview {
                    display: block;
                    width: 100%;
                    height: 220px;
                    object-fit: cover;
                    border-radius: 6px;
                }

                .gallery-content .card .dj-name {
                    margin-top: 10px;
                    color: #ffffff;
                    font-size: 1rem;
                    text-align: center;
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
                    min-height: 520px;
                }

                /* Responsive adjustments */
                @media (max-width: 1200px) {
                    .gallery-content .card {
                        width: 300px;
                    }

                    .gallery-content .card .dj-video-preview {
                        height: 200px;
                    }
                }

                @media (max-width: 992px) {
                    .gallery-content .card {
                        width: 260px;
                    }

                    .carousel-viewport {
                        padding: 8px 40px;
                        min-height: 460px;
                    }

                    .gallery-content .card .dj-video-preview {
                        height: 180px;
                    }
                }

                @media (max-width: 768px) {
                    .gallery-content {
                        padding: 0 8px;
                    }

                    .carousel-viewport {
                        padding: 8px 24px;
                        min-height: 380px;
                    }

                    /* show single card mostly fullscreen on small screens */
                    .gallery-content .card {
                        width: calc(100% - 32px);
                        margin: 0 auto;
                    }

                    .cards-arrow {
                        display: none;
                    }

                    .gallery-date-controller .gallery-arrow {
                        padding: 4px;
                    }

                    .gallery-content .card .dj-video-preview {
                        height: 260px;
                    }
                }

                @media (max-width: 480px) {
                    .gallery-content .card .dj-video-preview {
                        height: 200px;
                    }

                    .gallery-content .card .dj-name {
                        font-size: 0.95rem;
                    }

                    .carousel-viewport {
                        min-height: 320px;
                        padding: 6px 16px;
                    }
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

                /* Also handle messages appended directly to the viewport (preferred)
                   so they are visually centered despite transforms on the track */
                .carousel-viewport>.no-data,
                .carousel-viewport>.loading-message {
                    position: absolute;
                    left: 50%;
                    top: 50%;
                    transform: translate(-50%, -50%);
                    margin: 0;
                    color: #fff;
                    font-size: 1rem;
                    text-align: center;
                    z-index: 25;
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

                <!-- hls.js for HLS playback in the gallery modal -->
                <script src="https://cdn.jsdelivr.net/npm/hls.js@1"></script>
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
                    <div class="ms-auto d-flex gap-2 align-items-center">
                        <button type="button" id="modalCopyBtn" class="btn btn-outline-light btn-sm"
                            title="Copy share link" aria-label="Copy share link">
                            <i class="fas fa-link" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body">
                    <video class="selected-vide w-100" controls playsinline></video>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Glass modal style for gallery */
        .modal-content {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px) saturate(120%);
            -webkit-backdrop-filter: blur(10px) saturate(120%);
            color: #fff;
        }

        .modal-header,
        .modal-body {
            background: transparent;
            border: none;
        }

        .modal-title.dj-name {
            font-weight: 600;
        }

        .btn-outline-light {
            color: rgba(255, 255, 255, 0.9);
            border-color: rgba(255, 255, 255, 0.12);
            background: transparent;
        }

        .btn-close-white {
            filter: invert(1) grayscale(1) contrast(200%);
        }

        .modal-backdrop.show {
            background-color: rgba(0, 0, 0, 0.55);
        }
    </style>
</div>
