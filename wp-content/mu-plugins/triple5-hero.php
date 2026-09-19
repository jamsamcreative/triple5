<?php
/**
 * Plugin Name: Triple 5 Hero Element
 * Description: Registers a "Triple 5 Hero" Cornerstone element (full-bleed hero with
 *              editable headline/subtext, swappable background image, and a lead form)
 *              plus the lead capture (CPT + email) that backs its form.
 * Version:     0.1.0
 * Author:      jamsamcreative
 *
 * @package triple5
 *
 * Source of truth: repo triple5 → wp-content/mu-plugins/triple5-hero/
 * To revert entirely: remove this file and the triple5-hero/ folder (or their symlinks).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TRIPLE5_HERO_VERSION', '0.1.0' );

/**
 * Base URL for this mu-plugin's assets. content_url() resolves correctly even
 * though the folder is symlinked in from the triple5 repo.
 */
function triple5_hero_url( $path = '' ) {
	return content_url( 'mu-plugins/triple5-hero/' . ltrim( $path, '/' ) );
}

/** Absolute path to a file inside this mu-plugin's folder. */
function triple5_hero_path( $path = '' ) {
	return __DIR__ . '/triple5-hero/' . ltrim( $path, '/' );
}

// Lead capture (CPT + form handler) — always loaded (front end + admin).
require_once triple5_hero_path( 'leads.php' );

// Quote Requests dashboard — admin + admin-post (CSV export) only.
if ( is_admin() ) {
	require_once triple5_hero_path( 'admin.php' );
}

/**
 * Register the "Triple 5 Hero" element with Cornerstone. cs_register_element() is
 * only available once Cornerstone has booted, so we hook its registration action.
 */
add_action(
	'cs_register_elements',
	static function () {
		if ( function_exists( 'cs_register_element' ) ) {
			require_once triple5_hero_path( 'element.php' );
		}
	}
);

/**
 * Enqueue the hero stylesheet on the front end. Depends on the brand tokens so the
 * CSS custom properties (--brand-*, --color-*, --font-*) are defined.
 */
add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style(
			'triple5-hero',
			triple5_hero_url( 'hero.css' ),
			array( 'triple5-tokens' ),
			TRIPLE5_HERO_VERSION
		);
		// Background parallax (progressive enhancement; no-ops under reduced motion).
		wp_enqueue_script( 'triple5-hero', triple5_hero_url( 'hero.js' ), array(), TRIPLE5_HERO_VERSION, true );
	},
	21
);

/**
 * Load the same stylesheet inside the Cornerstone/Pro builder preview so the hero
 * looks identical while editing. Mirrors the triple5-brand plugin's approach.
 */
add_action(
	'cornerstone_before_boot_app',
	static function () {
		add_action(
			'wp_enqueue_scripts',
			static function () {
				wp_enqueue_style(
					'triple5-hero',
					triple5_hero_url( 'hero.css' ),
					array( 'triple5-tokens' ),
					TRIPLE5_HERO_VERSION
				);
			},
			21
		);
	}
);
