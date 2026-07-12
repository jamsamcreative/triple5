<?php
/**
 * Triple5 theme setup.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style(
			'triple5-style',
			get_stylesheet_uri(),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
);
