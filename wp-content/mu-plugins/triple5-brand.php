<?php
/**
 * Plugin Name: Triple 5 Brand Tokens
 * Description: Loads the Triple 5 Construction design-system fonts + tokens and a
 *              light brand base layer site-wide (front end + Cornerstone builder).
 *              Must-use plugin: active automatically, no activation needed.
 * Version:     0.1.0
 * Author:      jamsamcreative
 *
 * @package triple5
 *
 * Source of truth: repo triple5 → wp-content/mu-plugins/triple5-brand/
 * To revert entirely: remove this file and the triple5-brand/ folder (or their symlinks).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base URL for this mu-plugin's assets. content_url() resolves correctly even
 * though the folder is symlinked in from the triple5 repo (nginx follows it).
 */
function triple5_brand_url( $path = '' ) {
	return content_url( 'mu-plugins/triple5-brand/' . ltrim( $path, '/' ) );
}

/**
 * Enqueue the token stack + brand base layer on the front end.
 * styles.css @imports fonts + all tokens; base.css maps them onto elements.
 */
add_action(
	'wp_enqueue_scripts',
	static function () {
		$ver = '0.1.0';
		wp_enqueue_style( 'triple5-tokens', triple5_brand_url( 'styles.css' ), array(), $ver );
		wp_enqueue_style( 'triple5-brand-base', triple5_brand_url( 'base.css' ), array( 'triple5-tokens' ), $ver );
	},
	20
);

/**
 * Load the same tokens inside the Cornerstone / Pro builder preview so what you
 * design matches the front end. Guarded so it no-ops if Pro isn't active.
 */
add_action(
	'cornerstone_before_boot_app',
	static function () {
		add_action(
			'wp_enqueue_scripts',
			static function () {
				$ver = '0.1.0';
				wp_enqueue_style( 'triple5-tokens', triple5_brand_url( 'styles.css' ), array(), $ver );
				wp_enqueue_style( 'triple5-brand-base', triple5_brand_url( 'base.css' ), array( 'triple5-tokens' ), $ver );
			},
			20
		);
	}
);

/**
 * Resolve a Cornerstone image-control value to a URL.
 *
 * The builder's image picker stores attachment references like "88:full" (id:size),
 * not URLs. Every Triple 5 element that outputs an image must pass the control
 * value through this so uploads chosen in the builder actually render.
 */
function triple5_image_url( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	if ( function_exists( 'cs_resolve_image_source' ) ) {
		$resolved = cs_resolve_image_source( $value );
		if ( is_string( $resolved ) && '' !== $resolved ) {
			return $resolved;
		}
	}
	// Fallback outside Cornerstone: "123:size" or bare "123" → attachment URL.
	if ( preg_match( '/^(\d+)(?::([a-z0-9_-]+))?$/i', $value, $m ) ) {
		$src = wp_get_attachment_image_src( (int) $m[1], $m[2] ?? 'full' );
		return $src ? $src[0] : '';
	}
	return $value;
}
