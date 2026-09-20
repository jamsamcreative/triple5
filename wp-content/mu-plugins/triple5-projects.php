<?php
/**
 * Plugin Name: Triple 5 Projects
 * Description: Registers a reusable "Triple 5 Projects" Cornerstone element that
 *              shows client reviews pulled from Google and Yelp (cached, moderated,
 *              filtered by minimum star rating) merged with hand-entered reviews.
 *              Adds Settings → Triple 5 Reviews for API keys and moderation.
 * Version:     0.1.0
 * Author:      jamsamcreative
 *
 * @package triple5
 *
 * Source of truth: repo triple5 → wp-content/mu-plugins/triple5-projects/
 * To revert entirely: remove this file and the triple5-projects/ folder (or symlinks).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TRIPLE5_PROJECTS_VERSION', '0.1.0' );

/** Base URL for this mu-plugin's assets (resolves through the symlink). */
function triple5_projects_url( $path = '' ) {
	return content_url( 'mu-plugins/triple5-projects/' . ltrim( $path, '/' ) );
}

/** Absolute path to a file inside this mu-plugin's folder. */
function triple5_projects_path( $path = '' ) {
	return __DIR__ . '/triple5-projects/' . ltrim( $path, '/' );
}

// Post type + taxonomy + project meta (always), runtime renderer (always).
require_once triple5_projects_path( 'cpt.php' );
require_once triple5_projects_path( 'render.php' );

/** Register the element once Cornerstone has booted. */
add_action(
	'cs_register_elements',
	static function () {
		if ( function_exists( 'cs_register_element' ) ) {
			require_once triple5_projects_path( 'element.php' );
		}
	}
);

/** Enqueue the element stylesheet on the front end (depends on brand tokens). */
add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style(
			'triple5-projects',
			triple5_projects_url( 'gallery.css' ),
			array( 'triple5-tokens' ),
			TRIPLE5_PROJECTS_VERSION
		);
		// Filter pills + lightbox (progressive enhancement; grid is usable without it).
		wp_enqueue_script( 'triple5-projects', triple5_projects_url( 'gallery.js' ), array(), TRIPLE5_PROJECTS_VERSION, true );
	},
	21
);

/** Load the same stylesheet inside the Cornerstone builder preview. */
add_action(
	'cornerstone_before_boot_app',
	static function () {
		add_action(
			'wp_enqueue_scripts',
			static function () {
				wp_enqueue_style(
					'triple5-projects',
					triple5_projects_url( 'gallery.css' ),
					array( 'triple5-tokens' ),
					TRIPLE5_PROJECTS_VERSION
				);
			},
			21
		);
	}
);
