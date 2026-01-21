// Gallery page functionality
console.log('gallery.js loaded');

let dates = [];
let currentDateIndex = 0;
let currentDate = null;
let currentCardIndex = 0;
let cardWidth = 0;
let autoAdvanceInterval = null;
const AUTO_ADVANCE_MS = 4000;

// Initialize gallery on page load
document.addEventListener('DOMContentLoaded', function () {
    loadDates();
    setupEventListeners();
});

// Setup event listeners for navigation arrows
function setupEventListeners() {
    const leftArrow = document.querySelector('.gallery-arrow.left');
    const rightArrow = document.querySelector('.gallery-arrow.right');

    if (leftArrow) {
        leftArrow.addEventListener('click', () => navigateDate('prev'));
    }

    if (rightArrow) {
        rightArrow.addEventListener('click', () => navigateDate('next'));
    }
}

// Load all available dates
async function loadDates() {
    try {
        const response = await fetch('/api/dates', { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error('Failed to fetch dates: ' + response.status);

        const payload = await response.json();
        // support API that returns { data: [...] } or raw array
        let rawDates = Array.isArray(payload) ? payload : (payload.data || payload);

        // Normalize date objects to ensure `id` and `date` (YYYY-MM-DD) are present
        dates = rawDates.map(d => {
            const id = d.id || d.ID || d.date_id || null;
            let dateVal = null;
            if (!d.date) {
                dateVal = null;
            } else if (typeof d.date === 'string') {
                dateVal = d.date.split('T')[0];
            } else if (d.date.date) {
                // Carbon serialized form
                dateVal = (typeof d.date.date === 'string') ? d.date.date.split('T')[0] : null;
            } else {
                dateVal = null;
            }

            return { id, date: dateVal, event_name: d.event_name || d.eventName || null, raw: d };
        });

        console.log('Loaded dates payload:', dates);

        if (dates.length > 0) {
            // Find the index of the date nearest to today
            const today = new Date();
            let nearestIndex = 0;
            let minDiff = Infinity;
            dates.forEach((dateObj, idx) => {
                if (!dateObj.date) return;
                const dateVal = new Date(dateObj.date);
                if (isNaN(dateVal)) return;
                const diff = Math.abs(dateVal - today);
                if (diff < minDiff) {
                    minDiff = diff;
                    nearestIndex = idx;
                }
            });
            currentDateIndex = nearestIndex;
            displayCurrentDate();
            loadDJsForCurrentDate();
        } else {
            displayNoDataMessage('No dates available');
        }
    } catch (error) {
        console.error('Error loading dates:', error);
        displayNoDataMessage('Error loading dates');
    }
}

// Navigate to previous or next date
function navigateDate(direction) {
    if (dates.length === 0) return;

    if (direction === 'prev') {
        currentDateIndex = (currentDateIndex - 1 + dates.length) % dates.length;
    } else {
        currentDateIndex = (currentDateIndex + 1) % dates.length;
    }

    displayCurrentDate();
    loadDJsForCurrentDate();
}

// Display current date in the UI
function displayCurrentDate() {
    currentDate = dates[currentDateIndex];
    const dateElement = document.getElementById('gallery-date');

    if (dateElement && currentDate) {
        if (!currentDate.date) {
            dateElement.textContent = 'Date unavailable';
            return;
        }
        // Format date as 'February 22, 2026' only
        const dateObj = new Date(currentDate.date);
        if (isNaN(dateObj)) {
            dateElement.textContent = 'Date unavailable';
            return;
        }
        const formattedDate = dateObj.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        dateElement.textContent = formattedDate;
    }
}

// Load DJs for the current selected date
async function loadDJsForCurrentDate() {
    if (!currentDate) return;

    try {
        const identifier = currentDate.id || currentDate.date;
        const response = await fetch(`/api/dates/${encodeURIComponent(identifier)}/djs`, { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error('Failed to fetch DJs: ' + response.status);

        const payload = await response.json();
        const djs = Array.isArray(payload) ? payload : (payload.data || payload);
        displayDJs(djs);
    } catch (error) {
        console.error('Error loading DJs:', error);
        displayNoDataMessage('Error loading DJ performances');
    }
}

// Display DJs in the gallery
function displayDJs(djs) {
    const galleryContent = document.querySelector('.gallery-content');

    if (!galleryContent) return;

    // Clear existing content
    galleryContent.innerHTML = '';
    currentCardIndex = 0;

    if (djs.length === 0) {
        // clear any existing cards
        galleryContent.innerHTML = '';
        // remove previous no-data if present
        const viewport = document.querySelector('.carousel-viewport');
        if (viewport) {
            const prev = viewport.querySelector('.no-data, .loading-message');
            if (prev) prev.remove();
            const noData = document.createElement('p');
            noData.className = 'no-data';
            noData.textContent = 'More videos will be uploaded soon';
            viewport.appendChild(noData);
        }
        return;
    }

    // Create card for each DJ inside the carousel track
    djs.forEach((dj, index) => {
        const card = createDJCard(dj, index);
        // layout handled by CSS for responsive sizing
        card.style.flex = '0 0 auto';
        galleryContent.appendChild(card);
    });

    // Initialize carousel controls
    setupCarouselControls();
    // try to autoplay visible videos
    try { playVisibleCards(); } catch (e) { /* ignore */ }

    // start/stop autoplay and show/hide arrows based on card count
    const track = document.querySelector('.carousel-track');
    const count = track ? track.querySelectorAll('.card').length : 0;
    if (count > 1) startAutoAdvance(); else stopAutoAdvance();
    const prevBtn = document.querySelector('.cards-arrow.left');
    const nextBtn = document.querySelector('.cards-arrow.right');
    if (prevBtn) prevBtn.style.display = count > 1 ? 'flex' : 'none';
    if (nextBtn) nextBtn.style.display = count > 1 ? 'flex' : 'none';
}

function setupCarouselControls() {
    const viewport = document.querySelector('.carousel-viewport');
    const track = document.querySelector('.carousel-track');
    const prevBtn = document.querySelector('.cards-arrow.left');
    const nextBtn = document.querySelector('.cards-arrow.right');

    if (!track || !viewport) return;

    // compute card width (use first card)
    const firstCard = track.querySelector('.card');
    cardWidth = firstCard ? firstCard.getBoundingClientRect().width : viewport.getBoundingClientRect().width;

    // apply track styles
    track.style.display = 'flex';
    track.style.transition = 'transform 300ms ease';

    const update = () => {
        const cards = track.querySelectorAll('.card');
        if (!cards.length) return;
        cardWidth = cards[0].getBoundingClientRect().width;
        moveToCard(currentCardIndex);
    };

    window.addEventListener('resize', update);

    if (prevBtn) prevBtn.addEventListener('click', () => prevCard());
    if (nextBtn) nextBtn.addEventListener('click', () => nextCard());

    // enable swipe for touch devices
    let startX = 0;
    let isTouch = false;
    track.addEventListener('touchstart', (e) => {
        isTouch = true;
        startX = e.touches[0].clientX;
    });
    track.addEventListener('touchend', (e) => {
        if (!isTouch) return;
        const diff = startX - (e.changedTouches[0].clientX || 0);
        if (Math.abs(diff) > 50) {
            if (diff > 0) nextCard(); else prevCard();
        }
        isTouch = false;
    });
    // pause autoplay on hover over viewport/track
    const viewportEl = document.querySelector('.carousel-viewport');
    if (viewportEl) {
        viewportEl.addEventListener('mouseenter', () => stopAutoAdvance());
        viewportEl.addEventListener('mouseleave', () => startAutoAdvance());
    }
}

function moveToCard(index) {
    const track = document.querySelector('.carousel-track');
    if (!track) return;
    const cards = track.querySelectorAll('.card');
    if (!cards.length) return;
    // clamp index
    currentCardIndex = Math.max(0, Math.min(index, cards.length - 1));
    // compute offset to align the requested card to the left edge of the viewport
    const viewport = document.querySelector('.carousel-viewport');
    const vpStyle = viewport ? window.getComputedStyle(viewport) : null;
    const padLeft = vpStyle ? parseFloat(vpStyle.paddingLeft || 0) : 0;
    const card = cards[currentCardIndex];
    const offset = (card && typeof card.offsetLeft === 'number') ? (card.offsetLeft - padLeft) : (currentCardIndex * cardWidth);
    track.style.transform = `translateX(-${offset}px)`;
    // attempt to play visible cards after moving
    try { playVisibleCards(); } catch (e) { /* ignore */ }
}

function nextCard() {
    const track = document.querySelector('.carousel-track');
    if (!track) return;
    const count = track.querySelectorAll('.card').length;
    const { firstVisible, lastVisible } = getVisibleIndices();
    // if everything fits already, do nothing
    if (firstVisible === 0 && lastVisible === count - 1) return;
    if (lastVisible < count - 1) {
        moveToCard(lastVisible + 1);
    } else {
        moveToCard(0);
    }
    resetAutoAdvance();
}

function prevCard() {
    const track = document.querySelector('.carousel-track');
    if (!track) return;
    const count = track.querySelectorAll('.card').length;
    const { firstVisible, lastVisible } = getVisibleIndices();
    // if everything fits already, do nothing
    if (firstVisible === 0 && lastVisible === count - 1) return;
    if (firstVisible > 0) {
        moveToCard(firstVisible - 1);
    } else {
        moveToCard(count - 1);
    }
    resetAutoAdvance();
}

function getVisibleIndices() {
    const track = document.querySelector('.carousel-track');
    const viewport = document.querySelector('.carousel-viewport');
    const result = { firstVisible: 0, lastVisible: 0 };
    if (!track || !viewport) return result;
    const cards = track.querySelectorAll('.card');
    if (!cards.length) return result;
    const vpRect = viewport.getBoundingClientRect();

    let first = null;
    let last = null;
    cards.forEach((card, i) => {
        const r = card.getBoundingClientRect();
        // consider a card fully visible if its whole width fits inside the viewport (with small tolerance)
        const fullyVisible = (r.left >= vpRect.left + 2 && r.right <= vpRect.right - 2);
        if (fullyVisible) {
            if (first === null) first = i;
            last = i;
        }
    });

    if (first === null) {
        // no fully visible cards — pick the first partially visible range
        cards.forEach((card, i) => {
            const r = card.getBoundingClientRect();
            if (r.right > vpRect.left + 2 && r.left < vpRect.right - 2) {
                if (first === null) first = i;
                last = i;
            }
        });
    }

    if (first === null) first = 0;
    if (last === null) last = Math.max(0, first);

    result.firstVisible = first;
    result.lastVisible = last;
    return result;
}
function startAutoAdvance() {
    stopAutoAdvance();
    autoAdvanceInterval = setInterval(() => {
        try { nextCard(); } catch (e) { /* ignore */ }
    }, AUTO_ADVANCE_MS);
}

function stopAutoAdvance() {
    if (autoAdvanceInterval) {
        clearInterval(autoAdvanceInterval);
        autoAdvanceInterval = null;
    }
}

function resetAutoAdvance() {
    stopAutoAdvance();
    startAutoAdvance();
}
function playVisibleCards() {
    const track = document.querySelector('.carousel-track');
    const viewport = document.querySelector('.carousel-viewport');
    if (!track || !viewport) return;
    const cards = track.querySelectorAll('.card');
    if (!cards.length) return;
    const vpRect = viewport.getBoundingClientRect();
    cards.forEach(card => {
        const vid = card.querySelector('video');
        if (!vid) return;
        const r = card.getBoundingClientRect();
        // play if visible in viewport (allow partial visibility)
        if (r.right > vpRect.left + 10 && r.left < vpRect.right - 10) {
            vid.muted = true;
            vid.play().catch(() => { });
        } else {
            try { vid.pause(); } catch (e) { }
        }
    });
}

// Create a DJ card element
function createDJCard(dj, index) {
    const card = document.createElement('div');
    card.className = 'card';
    card.setAttribute('data-aos', 'fade-up');
    card.setAttribute('data-aos-duration', '600');
    card.setAttribute('data-aos-delay', (index * 100).toString());

    // Determine video source (preview or full)
    const videoSrc = dj.preview_video_path
        ? `/storage/${dj.preview_video_path}`
        : (dj.video_path ? `/storage/${dj.video_path}` : '');

    const posterSrc = dj.poster_path ? `/storage/${dj.poster_path}` : '';

    card.innerHTML = `
        <video class="dj-video-preview" src="${videoSrc}" ${posterSrc ? `poster="${posterSrc}"` : ''} muted autoplay loop playsinline preload="metadata"></video>
        <p class="dj-name">DJ ${dj.name || ''}</p>
    `;

    // Add click event to open modal
    card.addEventListener('click', () => openDJModal(dj));

    // Add hover effect to play preview (improved load/play handling to reduce glitches)
    const video = card.querySelector('.dj-video-preview');
    if (video && videoSrc) {
        // Prepare video for autoplay on hover: muted, loop, preload metadata
        try {
            video.muted = true;
            video.loop = true;
            video.playsInline = true;
            video.preload = 'metadata';
        } catch (e) {
            // ignore if properties aren't writable
        }

        let playPending = false;
        const tryPlay = () => {
            if (video.readyState >= 3) { // HAVE_FUTURE_DATA / can play through
                video.play().catch(err => console.log('Video play failed:', err));
            } else if (!playPending) {
                playPending = true;
                const onCanPlay = () => {
                    playPending = false;
                    video.play().catch(err => console.log('Video play failed:', err));
                    video.removeEventListener('canplay', onCanPlay);
                };
                video.addEventListener('canplay', onCanPlay);
                // Trigger load in case preload didn't start
                try { video.load(); } catch (e) { /* ignore */ }
            }
        };

        card.addEventListener('mouseenter', () => {
            tryPlay();
        });

        card.addEventListener('mouseleave', () => {
            try { video.pause(); } catch (e) { /* ignore */ }
            // Do not reset currentTime here to avoid repeated seeking glitches
        });
    }

    return card;
}

// Open modal with DJ details
function openDJModal(dj) {
    const modalEl = document.getElementById('exampleModal');

    // Set DJ name in modal (display name only)
    const modalTitle = modalEl.querySelector('.modal-title.dj-name');
    if (modalTitle) modalTitle.textContent = `DJ ${dj.name || ''}`;

    // Set video in modal
    const modalVideo = modalEl.querySelector('.selected-vide');
    const fullVideoSrc = dj.video_path ? `/storage/${dj.video_path}` : (dj.preview_video_path ? `/storage/${dj.preview_video_path}` : '');
    if (modalVideo) {
        modalVideo.src = fullVideoSrc;
        modalVideo.controls = true;
        modalVideo.poster = dj.poster_path ? `/storage/${dj.poster_path}` : '';
        try { modalVideo.load(); } catch (e) { /* ignore */ }
    }

    // If Bootstrap's JS is available, use it; otherwise use a lightweight DOM fallback
    if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
        const modal = new bootstrap.Modal(modalEl);

        if (modalVideo) {
            modalEl.addEventListener('shown.bs.modal', () => {
                modalVideo.play().catch(err => console.log('Video play failed:', err));
            }, { once: true });

            modalEl.addEventListener('hidden.bs.modal', () => {
                try {
                    modalVideo.pause();
                    modalVideo.currentTime = 0;
                    // clear source and poster so next open starts clean
                    modalVideo.removeAttribute('src');
                    modalVideo.removeAttribute('poster');
                    try { modalVideo.load(); } catch (e) { /* ignore */ }
                } catch (e) { /* ignore */ }
            }, { once: true });
        }

        modal.show();
        return;
    }

    // Lightweight fallback: show modal, add backdrop, and wire close handling
    modalEl.classList.add('show');
    modalEl.style.display = 'block';
    modalEl.setAttribute('aria-modal', 'true');
    modalEl.removeAttribute('aria-hidden');
    document.body.classList.add('modal-open');

    // create backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    document.body.appendChild(backdrop);

    const cleanup = () => {
        modalEl.classList.remove('show');
        modalEl.style.display = 'none';
        modalEl.setAttribute('aria-hidden', 'true');
        modalEl.removeAttribute('aria-modal');
        document.body.classList.remove('modal-open');
        if (backdrop && backdrop.parentNode) backdrop.parentNode.removeChild(backdrop);
        if (modalVideo) {
            try { modalVideo.pause(); modalVideo.currentTime = 0; } catch (e) { /* ignore */ }
            modalVideo.src = '';
        }
        // remove listeners
        const closeBtn = modalEl.querySelector('[data-bs-dismiss="modal"]');
        if (closeBtn) closeBtn.removeEventListener('click', onClose);
        backdrop.removeEventListener('click', onClose);
    };

    const onClose = (e) => {
        e && e.preventDefault();
        cleanup();
    };

    // wire close button and backdrop
    const closeBtn = modalEl.querySelector('[data-bs-dismiss="modal"]');
    if (closeBtn) closeBtn.addEventListener('click', onClose);
    backdrop.addEventListener('click', onClose);
}

// Display message when no data is available
function displayNoDataMessage(message) {
    const dateElement = document.getElementById('gallery-date');
    const galleryContent = document.querySelector('.gallery-content');

    if (dateElement) dateElement.textContent = message;

    // place message centered in the viewport (not inside the track) so centering works reliably
    const viewport = document.querySelector('.carousel-viewport');
    if (viewport) {
        // remove any existing messages
        const prev = viewport.querySelector('.no-data, .loading-message');
        if (prev) prev.remove();
        // clear gallery content
        if (galleryContent) galleryContent.innerHTML = '';
        const msg = document.createElement('p');
        msg.className = 'no-data';
        msg.textContent = message;
        viewport.appendChild(msg);
    }
}
