/**
 * Triple 5 Hero — background parallax.
 *
 * Moves `.t5-hero__bg` at a fraction of the scroll speed (data-t5-parallax, e.g.
 * 0.35) while the hero is in view. Uses transform on a composited layer, throttled
 * to one update per frame. Skipped entirely when the user prefers reduced motion
 * or on coarse-pointer (touch) devices, where fixed/parallax backgrounds jank.
 */
(function () {
	'use strict';

	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var touch  = window.matchMedia && window.matchMedia('(hover: none) and (pointer: coarse)').matches;
	if (reduce || touch) {
		return;
	}

	var heroes = Array.prototype.slice.call(document.querySelectorAll('.t5-hero[data-t5-parallax]'));
	if (!heroes.length) {
		return;
	}

	var items = heroes.map(function (hero) {
		return { hero: hero, bg: hero.querySelector('.t5-hero__bg'), speed: parseFloat(hero.getAttribute('data-t5-parallax')) || 0.35 };
	}).filter(function (i) { return i.bg; });

	var ticking = false;

	function update() {
		ticking = false;
		var vh = window.innerHeight;
		items.forEach(function (i) {
			var rect = i.hero.getBoundingClientRect();
			if (rect.bottom < 0 || rect.top > vh) {
				return; // off-screen — leave as is
			}
			// Distance the hero's top has travelled past the viewport top (positive as
			// you scroll down). The photo lags behind by (1 - speed).
			var offset = -rect.top * i.speed;
			i.bg.style.transform = 'translate3d(0,' + offset.toFixed(1) + 'px,0)';
		});
	}

	function onScroll() {
		if (!ticking) {
			ticking = true;
			window.requestAnimationFrame(update);
		}
	}

	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll);
	update();
})();
