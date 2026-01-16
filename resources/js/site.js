// Site JS entry: import compiled Bootstrap CSS + JS
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

// Animation libraries
import AOS from 'aos';
import 'aos/dist/aos.css';

import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
gsap.registerPlugin(ScrollTrigger);

if (typeof console !== 'undefined' && typeof console.log === 'function') {
	console.log('site.js loaded');
}

// Initialize AOS for simple declarative reveals
if (typeof AOS !== 'undefined') {
	AOS.init({ once: true, duration: 650, easing: 'ease-out-cubic' });
}

// Simple, performant parallax for hero video — initialize after DOM ready
function initHeroParallax() {
	console.log('initHeroParallax: initializing');
	const hero = document.querySelector('.hero-section');
	if (!hero) {
		console.log('initHeroParallax: .hero-section not found');
		return;
	}

	const video = hero.querySelector('.hero-video');
	if (!video) {
		console.log('initHeroParallax: .hero-video not found inside .hero-section');
		return;
	}

	let latestScrollY = window.scrollY || window.pageYOffset;
	let ticking = false;

	const speed = 0.25; // lower = slower parallax

	function update() {
		ticking = false;
		const scrollY = latestScrollY;
		const offsetTop = hero.offsetTop;
		const y = (scrollY - offsetTop) * speed;
		video.style.transform = `translate3d(-50%, calc(-50% + ${y}px), 0)`;
		// occasional debug: small, non-spammy log
		if (Math.abs(y) > 1 && typeof console !== 'undefined' && typeof console.debug === 'function') {
			console.debug('hero parallax y:', Math.round(y));
		}
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

// Only run the custom parallax if GSAP/ScrollTrigger isn't available
if (typeof gsap === 'undefined') {
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initHeroParallax);
	} else {
		initHeroParallax();
	}
} else {
	if (typeof console !== 'undefined' && typeof console.log === 'function') {
		console.log('Skipping custom parallax: GSAP is available and handles transforms');
	}
}

// Navbar scroll behavior: toggle `.scrolled` on the nav container
function initNavScroll() {
	const nav = document.querySelector('.nav-container');
	if (!nav) return;

	let latest = window.scrollY || window.pageYOffset;
	let ticking = false;
	const threshold = 60; // px to trigger scrolled state

	function apply() {
		ticking = false;
		if (latest > threshold) {
			nav.classList.add('scrolled');
		} else {
			nav.classList.remove('scrolled');
		}
	}

	function onScrollNav() {
		latest = window.scrollY || window.pageYOffset;
		if (!ticking) {
			window.requestAnimationFrame(apply);
			ticking = true;
		}
	}

	window.addEventListener('scroll', onScrollNav, { passive: true });
	// init
	onScrollNav();
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initNavScroll);
} else {
	initNavScroll();
}

// Mobile menu toggle
function initMobileMenu() {
	const toggle = document.querySelector('.mobile-menu-toggle');
	const navBottom = document.querySelector('.nav-bottom');
	
	if (!toggle || !navBottom) return;
	
	toggle.addEventListener('click', function() {
		const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
		toggle.setAttribute('aria-expanded', !isExpanded);
		navBottom.classList.toggle('active');
		
		// Prevent body scroll when menu is open
		if (!isExpanded) {
			document.body.style.overflow = 'hidden';
		} else {
			document.body.style.overflow = '';
		}
	});
	
	// Close menu when clicking nav links
	const navLinks = navBottom.querySelectorAll('a');
	navLinks.forEach(link => {
		link.addEventListener('click', function() {
			if (window.innerWidth <= 768) {
				toggle.setAttribute('aria-expanded', 'false');
				navBottom.classList.remove('active');
				document.body.style.overflow = '';
			}
		});
	});
	
	// Close menu on window resize if switching to desktop
	window.addEventListener('resize', function() {
		if (window.innerWidth > 768) {
			toggle.setAttribute('aria-expanded', 'false');
			navBottom.classList.remove('active');
			document.body.style.overflow = '';
		}
	});
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initMobileMenu);
} else {
	initMobileMenu();
}

// GSAP ScrollTrigger animations (hero and reveal elements)
function initGsapScroll() {
	// hero video parallax via GSAP ScrollTrigger (non-destructive if video exists)
	const hero = document.querySelector('.hero-section');
	const video = hero ? hero.querySelector('.hero-video') : null;
	if (hero && video) {
		gsap.to(video, {
			y: 150,
			ease: 'none',
			scrollTrigger: {
				trigger: hero,
				start: 'top top',
				end: 'bottom top',
				scrub: 1,
			},
		});
	}

	// reveal elements using GSAP (complements AOS; use one or the other per element)
	gsap.utils.toArray('.reveal').forEach((el) => {
		gsap.from(el, {
			y: 36,
			opacity: 0,
			duration: 0.8,
			ease: 'power2.out',
			scrollTrigger: { trigger: el, start: 'top 85%', toggleActions: 'play none none none' },
		});
	});
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initGsapScroll);
} else {
	initGsapScroll();
}
