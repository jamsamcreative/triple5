/**
 * Triple 5 Service Tabs — tab switching.
 *
 * Progressive enhancement over plain anchors + panels. Without this script every
 * panel is visible (CSS only hides panels once `.is-enhanced` is set), so content
 * is never trapped. Handles click, arrow-key navigation, and deep links (#panel-id).
 */
(function () {
	'use strict';

	function enhance(root) {
		var tabs = Array.prototype.slice.call(root.querySelectorAll('.t5-tabs__tab'));
		var panels = Array.prototype.slice.call(root.querySelectorAll('.t5-tabs__panel'));
		if (!tabs.length) {
			return;
		}
		root.classList.add('is-enhanced');

		function activate(tab, focus) {
			tabs.forEach(function (t) {
				var on = t === tab;
				t.classList.toggle('is-active', on);
				t.setAttribute('aria-selected', on ? 'true' : 'false');
				t.setAttribute('tabindex', on ? '0' : '-1');
			});
			panels.forEach(function (p) {
				var on = '#' + p.id === tab.getAttribute('href');
				p.classList.toggle('is-active', on);
				p.hidden = !on;
			});
			if (focus) {
				tab.focus();
			}
		}

		tabs.forEach(function (tab, i) {
			tab.addEventListener('click', function (e) {
				e.preventDefault();
				activate(tab, false);
				if (window.history && history.replaceState) {
					history.replaceState(null, '', tab.getAttribute('href'));
				}
			});
			tab.addEventListener('keydown', function (e) {
				var next = null;
				if (e.key === 'ArrowRight') { next = tabs[(i + 1) % tabs.length]; }
				if (e.key === 'ArrowLeft') { next = tabs[(i - 1 + tabs.length) % tabs.length]; }
				if (e.key === 'Home') { next = tabs[0]; }
				if (e.key === 'End') { next = tabs[tabs.length - 1]; }
				if (next) {
					e.preventDefault();
					activate(next, true);
				}
			});
		});

		// Deep link: /our-services/#t5tabs-xxxx-panel-3
		if (location.hash) {
			var target = tabs.filter(function (t) { return t.getAttribute('href') === location.hash; })[0];
			if (target) {
				activate(target, false);
			}
		}
	}

	function init() {
		Array.prototype.forEach.call(document.querySelectorAll('[data-t5-tabs]'), enhance);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
