// Site JS entry: import compiled Bootstrap CSS + JS
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

if (typeof console !== 'undefined' && typeof console.log === 'function') {
	console.log('site.js loaded');
}

// Simple, performant parallax for hero video — initialize after DOM ready
function initHeroParallax() {
	const hero = document.querySelector('.hero-section');
	if (!hero) return;

	const video = hero.querySelector('.hero-video');
	if (!video) return;

	let latestScrollY = window.scrollY || window.pageYOffset;
	let ticking = false;

	const speed = 0.25; // lower = slower parallax

	function update() {
		ticking = false;
		const scrollY = latestScrollY;
		const offsetTop = hero.offsetTop;
		const y = (scrollY - offsetTop) * speed;
		video.style.transform = `translate3d(-50%, calc(-50% + ${y}px), 0)`;
	}

	function onScroll() {
		latestScrollY = window.scrollY || window.pageYOffset;
		if (!ticking) {
			window.requestAnimationFrame(update);
			ticking = true;
		}
	}

	window.addEventListener('scroll', onScroll, { passive: true });
	// initialize position
	onScroll();
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initHeroParallax);
} else {
	initHeroParallax();
}
