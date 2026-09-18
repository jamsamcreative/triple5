<?php
/**
 * Plugin Name: Triple 5 Service Tabs
 * Description: Registers a reusable "Triple 5 Service Tabs" Cornerstone element —
 *              a stone page header + tabbed service panels (up to 6 tabs, each
 *              with badge, heading, body, checklist, CTA and image).
 * Version:     0.1.0
 * Author:      jamsamcreative
 *
 * @package triple5
 *
 * Source of truth: repo triple5 → wp-content/mu-plugins/triple5-tabs/
 * To revert entirely: remove this file and the triple5-tabs/ folder (or symlinks).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TRIPLE5_TABS_VERSION', '0.1.0' );

/** Base URL for this mu-plugin's assets (resolves through the symlink). */
function triple5_tabs_url( $path = '' ) {
	return content_url( 'mu-plugins/triple5-tabs/' . ltrim( $path, '/' ) );
}

/** Absolute path to a file inside this mu-plugin's folder. */
function triple5_tabs_path( $path = '' ) {
	return __DIR__ . '/triple5-tabs/' . ltrim( $path, '/' );
}

/** Register the element once Cornerstone has booted. */
add_action(
	'cs_register_elements',
	static function () {
		if ( function_exists( 'cs_register_element' ) ) {
			require_once triple5_tabs_path( 'element.php' );
		}
	}
);

/** Enqueue the element stylesheet on the front end (depends on brand tokens). */
add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style(
			'triple5-tabs',
			triple5_tabs_url( 'tabs.css' ),
			array( 'triple5-tokens' ),
			TRIPLE5_TABS_VERSION
		);
		wp_enqueue_script( 'triple5-tabs', triple5_tabs_url( 'tabs.js' ), array(), TRIPLE5_TABS_VERSION, true );
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
					'triple5-tabs',
					triple5_tabs_url( 'tabs.css' ),
					array( 'triple5-tokens' ),
					TRIPLE5_TABS_VERSION
				);
				wp_enqueue_script( 'triple5-tabs', triple5_tabs_url( 'tabs.js' ), array(), TRIPLE5_TABS_VERSION, true );
			},
			21
		);
	}
);
