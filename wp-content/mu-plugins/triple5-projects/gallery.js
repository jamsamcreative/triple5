/**
 * Triple 5 Project Gallery — category filter + lightbox.
 *
 * Progressive enhancement over the server-rendered grid: pills toggle cards by
 * data-cats; clicking a card opens a lightbox that steps through the project's
 * photos and plays its video (YouTube iframe or <video>). Without JS the grid
 * shows everything and cards link to the full-size image.
 */
(function () {
	'use strict';

	// ---------------------------------------------------------------- filtering
	function enhanceFilters(root) {
		var buttons = Array.prototype.slice.call(root.querySelectorAll('.t5-gallery__filter'));
		var cards   = Array.prototype.slice.call(root.querySelectorAll('.t5-gallery__card'));
		var none    = root.querySelector('.t5-gallery__none');
		if (!buttons.length) { return; }

		function apply(slug) {
			var shown = 0;
			cards.forEach(function (card) {
				var cats = (card.getAttribute('data-cats') || '').split(' ');
				var on = slug === '*' || cats.indexOf(slug) !== -1;
				card.hidden = !on;
				if (on) { shown++; }
			});
			buttons.forEach(function (b) {
				var active = b.getAttribute('data-filter') === slug;
				b.classList.toggle('is-active', active);
				b.setAttribute('aria-pressed', active ? 'true' : 'false');
			});
			if (none) { none.hidden = shown > 0; }
		}

		buttons.forEach(function (b) {
			b.addEventListener('click', function () {
				apply(b.getAttribute('data-filter'));
				if (history.replaceState) {
					var slug = b.getAttribute('data-filter');
					history.replaceState(null, '', slug === '*' ? location.pathname : '#' + slug);
				}
			});
		});

		// Deep link: /projects/#metal-roofing
		var hash = location.hash.replace('#', '');
		if (hash && buttons.some(function (b) { return b.getAttribute('data-filter') === hash; })) {
			apply(hash);
		}
	}

	// ----------------------------------------------------------------- lightbox
	var lb = null, state = null;

	function buildLightbox() {
		lb = document.createElement('div');
		lb.className = 't5-lightbox';
		lb.setAttribute('role', 'dialog');
		lb.setAttribute('aria-modal', 'true');
		lb.hidden = true;
		lb.innerHTML =
			'<div class="t5-lightbox__backdrop" data-close></div>' +
			'<div class="t5-lightbox__panel">' +
				'<button type="button" class="t5-lightbox__close" data-close aria-label="Close">&times;</button>' +
				'<button type="button" class="t5-lightbox__nav is-prev" data-prev aria-label="Previous">&#8249;</button>' +
				'<div class="t5-lightbox__stage"></div>' +
				'<button type="button" class="t5-lightbox__nav is-next" data-next aria-label="Next">&#8250;</button>' +
				'<div class="t5-lightbox__bar"><span class="t5-lightbox__title"></span><span class="t5-lightbox__counter"></span></div>' +
			'</div>';
		document.body.appendChild(lb);

		lb.addEventListener('click', function (e) {
			if (e.target.closest('[data-close]')) { close(); }
			if (e.target.closest('[data-prev]')) { step(-1); }
			if (e.target.closest('[data-next]')) { step(1); }
		});
		document.addEventListener('keydown', function (e) {
			if (lb.hidden) { return; }
			if (e.key === 'Escape') { close(); }
			if (e.key === 'ArrowLeft') { step(-1); }
			if (e.key === 'ArrowRight') { step(1); }
		});
	}

	function slides(data) {
		var s = (data.photos || []).map(function (p) { return { kind: 'image', src: p.src, alt: p.alt || data.title || '' }; });
		if (data.video) { s.push({ kind: 'video', video: data.video }); }
		return s;
	}

	function render() {
		var stage = lb.querySelector('.t5-lightbox__stage');
		var slide = state.slides[state.index];
		stage.innerHTML = '';
		if (slide.kind === 'image') {
			var img = document.createElement('img');
			img.src = slide.src; img.alt = slide.alt;
			stage.appendChild(img);
		} else if (slide.video.type === 'youtube') {
			var f = document.createElement('iframe');
			f.src = 'https://www.youtube-nocookie.com/embed/' + slide.video.id + '?autoplay=1&rel=0';
			f.title = state.title || 'Video';
			f.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
			f.setAttribute('allowfullscreen', '');
			stage.appendChild(f);
		} else {
			var v = document.createElement('video');
			v.src = slide.video.src; v.controls = true; v.autoplay = true; v.playsInline = true;
			stage.appendChild(v);
		}
		lb.querySelector('.t5-lightbox__title').textContent = state.title || '';
		lb.querySelector('.t5-lightbox__counter').textContent = state.slides.length > 1 ? (state.index + 1) + ' / ' + state.slides.length : '';
		lb.querySelector('[data-prev]').hidden = state.slides.length < 2;
		lb.querySelector('[data-next]').hidden = state.slides.length < 2;
	}

	function open(data, opener) {
		if (!lb) { buildLightbox(); }
		var s = slides(data);
		if (!s.length) { return; }
		state = { slides: s, index: 0, title: data.title || '', opener: opener };
		lb.hidden = false;
		document.body.classList.add('t5-lightbox-open');
		render();
		lb.querySelector('.t5-lightbox__close').focus();
	}

	function close() {
		if (!lb || lb.hidden) { return; }
		lb.hidden = true;
		lb.querySelector('.t5-lightbox__stage').innerHTML = '';
		document.body.classList.remove('t5-lightbox-open');
		if (state && state.opener) { state.opener.focus(); }
		state = null;
	}

	function step(dir) {
		if (!state || state.slides.length < 2) { return; }
		state.index = (state.index + dir + state.slides.length) % state.slides.length;
		render();
	}

	function hexToJson(hex) {
		try {
			var str = '';
			for (var i = 0; i < hex.length; i += 2) { str += String.fromCharCode(parseInt(hex.substr(i, 2), 16)); }
			return JSON.parse(decodeURIComponent(escape(str)));
		} catch (e) { return null; }
	}

	function enhanceLightbox(root) {
		root.addEventListener('click', function (e) {
			var a = e.target.closest('[data-t5-lightbox]');
			if (!a) { return; }
			var data = hexToJson(a.getAttribute('data-t5-lightbox'));
			if (!data) { return; }
			e.preventDefault();
			open(data, a);
		});
	}

	function init() {
		Array.prototype.forEach.call(document.querySelectorAll('[data-t5-gallery]'), function (root) {
			enhanceFilters(root);
			enhanceLightbox(root);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
