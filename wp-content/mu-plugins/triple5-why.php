<?php
/**
 * Plugin Name: Triple 5 Why Choose Us
 * Description: Registers a reusable "Triple 5 Why Choose Us" Cornerstone element —
 *              a dark band with a centered heading and up to 4 reason columns
 *              (red ring icon, uppercase title, short body).
 * Version:     0.1.0
 * Author:      jamsamcreative
 *
 * @package triple5
 *
 * Source of truth: repo triple5 → wp-content/mu-plugins/triple5-why/
 * To revert entirely: remove this file and the triple5-why/ folder (or symlinks).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TRIPLE5_WHY_VERSION', '0.1.0' );

/** Base URL for this mu-plugin's assets (resolves through the symlink). */
function triple5_why_url( $path = '' ) {
	return content_url( 'mu-plugins/triple5-why/' . ltrim( $path, '/' ) );
}

/** Absolute path to a file inside this mu-plugin's folder. */
function triple5_why_path( $path = '' ) {
	return __DIR__ . '/triple5-why/' . ltrim( $path, '/' );
}

/** Register the element once Cornerstone has booted. */
add_action(
	'cs_register_elements',
	static function () {
		if ( function_exists( 'cs_register_element' ) ) {
			require_once triple5_why_path( 'element.php' );
		}
	}
);

/** Enqueue the element stylesheet on the front end (depends on brand tokens). */
add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style(
			'triple5-why',
			triple5_why_url( 'why.css' ),
			array( 'triple5-tokens' ),
			TRIPLE5_WHY_VERSION
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
					'triple5-why',
					triple5_why_url( 'why.css' ),
					array( 'triple5-tokens' ),
					TRIPLE5_WHY_VERSION
				);
			},
			21
		);
	}
);
