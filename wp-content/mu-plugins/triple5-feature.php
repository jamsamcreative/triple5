<?php
/**
 * Plugin Name: Triple 5 Feature Section
 * Description: Registers a reusable "Triple 5 Feature Section" Cornerstone element —
 *              a two-column image + content block (eyebrow, heading, body, services
 *              checklist, stat card, experience badge, CTA) for use across the site.
 * Version:     0.1.0
 * Author:      jamsamcreative
 *
 * @package triple5
 *
 * Source of truth: repo triple5 → wp-content/mu-plugins/triple5-feature/
 * To revert entirely: remove this file and the triple5-feature/ folder (or symlinks).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TRIPLE5_FEATURE_VERSION', '0.1.0' );

/** Base URL for this mu-plugin's assets (resolves through the symlink). */
function triple5_feature_url( $path = '' ) {
	return content_url( 'mu-plugins/triple5-feature/' . ltrim( $path, '/' ) );
}

/** Absolute path to a file inside this mu-plugin's folder. */
function triple5_feature_path( $path = '' ) {
	return __DIR__ . '/triple5-feature/' . ltrim( $path, '/' );
}

/** Register the element once Cornerstone has booted. */
add_action(
	'cs_register_elements',
	static function () {
		if ( function_exists( 'cs_register_element' ) ) {
			require_once triple5_feature_path( 'element.php' );
		}
	}
);

/** Enqueue the element stylesheet on the front end (depends on brand tokens). */
add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style(
			'triple5-feature',
			triple5_feature_url( 'feature.css' ),
			array( 'triple5-tokens' ),
			TRIPLE5_FEATURE_VERSION
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
					'triple5-feature',
					triple5_feature_url( 'feature.css' ),
					array( 'triple5-tokens' ),
					TRIPLE5_FEATURE_VERSION
				);
			},
			21
		);
	}
);
