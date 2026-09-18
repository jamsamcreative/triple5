<?php
/**
 * Plugin Name: Triple 5 Testimonials
 * Description: Registers a reusable "Triple 5 Testimonials" Cornerstone element that
 *              shows client reviews pulled from Google and Yelp (cached, moderated,
 *              filtered by minimum star rating) merged with hand-entered reviews.
 *              Adds Settings → Triple 5 Reviews for API keys and moderation.
 * Version:     0.1.0
 * Author:      jamsamcreative
 *
 * @package triple5
 *
 * Source of truth: repo triple5 → wp-content/mu-plugins/triple5-reviews/
 * To revert entirely: remove this file and the triple5-reviews/ folder (or symlinks).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TRIPLE5_REVIEWS_VERSION', '0.1.0' );

/** Base URL for this mu-plugin's assets (resolves through the symlink). */
function triple5_reviews_url( $path = '' ) {
	return content_url( 'mu-plugins/triple5-reviews/' . ltrim( $path, '/' ) );
}

/** Absolute path to a file inside this mu-plugin's folder. */
function triple5_reviews_path( $path = '' ) {
	return __DIR__ . '/triple5-reviews/' . ltrim( $path, '/' );
}

// Data layer (fetch / normalize / cache / moderate) + admin settings page.
require_once triple5_reviews_path( 'sources.php' );
require_once triple5_reviews_path( 'render.php' );
if ( is_admin() ) {
	require_once triple5_reviews_path( 'admin.php' );
}

/** Register the element once Cornerstone has booted. */
add_action(
	'cs_register_elements',
	static function () {
		if ( function_exists( 'cs_register_element' ) ) {
			require_once triple5_reviews_path( 'element.php' );
		}
	}
);

/** Enqueue the element stylesheet on the front end (depends on brand tokens). */
add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style(
			'triple5-reviews',
			triple5_reviews_url( 'reviews.css' ),
			array( 'triple5-tokens' ),
			TRIPLE5_REVIEWS_VERSION
		);
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
					'triple5-reviews',
					triple5_reviews_url( 'reviews.css' ),
					array( 'triple5-tokens' ),
					TRIPLE5_REVIEWS_VERSION
				);
			},
			21
		);
	}
);
