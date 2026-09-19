<?php
/**
 * Plugin Name: Triple 5 Reasons + Media
 * Description: Registers a reusable "Triple 5 Reasons + Media" Cornerstone element —
 *              a centered heading, a check-circle reason list + CTA on the
 *              left, and a photo + YouTube video stack on the right.
 * Version:     0.1.0
 * Author:      jamsamcreative
 *
 * @package triple5
 *
 * Source of truth: repo triple5 → wp-content/mu-plugins/triple5-reasons/
 * To revert entirely: remove this file and the triple5-reasons/ folder (or symlinks).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TRIPLE5_REASONS_VERSION', '0.1.0' );

/** Base URL for this mu-plugin's assets (resolves through the symlink). */
function triple5_reasons_url( $path = '' ) {
	return content_url( 'mu-plugins/triple5-reasons/' . ltrim( $path, '/' ) );
}

/** Absolute path to a file inside this mu-plugin's folder. */
function triple5_reasons_path( $path = '' ) {
	return __DIR__ . '/triple5-reasons/' . ltrim( $path, '/' );
}

/** Register the element once Cornerstone has booted. */
add_action(
	'cs_register_elements',
	static function () {
		if ( function_exists( 'cs_register_element' ) ) {
			require_once triple5_reasons_path( 'element.php' );
		}
	}
);

/** Enqueue the element stylesheet on the front end (depends on brand tokens). */
add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style(
			'triple5-reasons',
			triple5_reasons_url( 'reasons.css' ),
			array( 'triple5-tokens' ),
			TRIPLE5_REASONS_VERSION
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
					'triple5-reasons',
					triple5_reasons_url( 'reasons.css' ),
					array( 'triple5-tokens' ),
					TRIPLE5_REASONS_VERSION
				);
			},
			21
		);
	}
);
