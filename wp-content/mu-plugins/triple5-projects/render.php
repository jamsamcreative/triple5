<?php
/**
 * Triple 5 Projects — gallery rendering (runtime).
 *
 * The element bakes a mount div carrying its config; the `the_content` filter here
 * re-renders the gallery from the live Projects list on every page view, so adding
 * or re-categorising a project in wp-admin updates the page without re-saving it
 * in Cornerstone. Cards carry data-* attributes that gallery.js uses for filtering
 * and the lightbox; without JS the grid still shows every project and the card
 * photo links to the full-size image.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** YouTube URL → video ID ('' if not YouTube). */
function triple5_projects_youtube_id( $url ) {
	if ( preg_match( '~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~', (string) $url, $m ) ) {
		return $m[1];
	}
	return '';
}

function triple5_projects_icon( $name ) {
	switch ( $name ) {
		case 'pin':
			return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>';
		case 'image':
			return '<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>';
		case 'play':
			return '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13l11-6.5z"/></svg>';
		case 'photos':
			return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 22H4a2 2 0 0 1-2-2V6"/><path d="m22 13-1.296-1.296a2.41 2.41 0 0 0-3.408 0L11 18"/><circle cx="12" cy="8" r="2"/><rect width="16" height="16" x="6" y="2" rx="2"/></svg>';
	}
	return '';
}

/** Render the whole gallery section from a config array. */
function triple5_projects_render_section( array $c ) {
	$projects = triple5_projects_items( array( 'limit' => $c['limit'], 'categories' => $c['categories'] ) );
	$cats     = triple5_projects_categories();
	if ( ! empty( $c['categories'] ) ) {
		$cats = array_values( array_filter( $cats, static fn( $t ) => in_array( $t->slug, $c['categories'], true ) ) );
	}

	// Header.
	$head = '<div class="t5-gallery__head">'
		. ( '' !== $c['eyebrow'] ? '<p class="t5-gallery__eyebrow">' . esc_html( $c['eyebrow'] ) . '</p>' : '' )
		. ( '' !== $c['heading'] ? '<h2 class="t5-gallery__heading">' . esc_html( $c['heading'] ) . '</h2>' : '' )
		. ( '' !== $c['subtext'] ? '<p class="t5-gallery__sub">' . esc_html( $c['subtext'] ) . '</p>' : '' )
		. '</div>';

	// Filter pills — only when there is more than one category to filter by.
	$filters = '';
	if ( $c['show_filters'] && count( $cats ) > 1 ) {
		$filters = '<div class="t5-gallery__filters" role="group" aria-label="Filter projects by category">'
			. '<button type="button" class="t5-gallery__filter is-active" data-filter="*" aria-pressed="true">' . esc_html( $c['all_label'] ) . '</button>';
		foreach ( $cats as $t ) {
			$filters .= '<button type="button" class="t5-gallery__filter" data-filter="' . esc_attr( $t->slug ) . '" aria-pressed="false">' . esc_html( $t->name ) . '</button>';
		}
		$filters .= '</div>';
	}

	// Cards.
	$tones = array( 'charcoal', 'red', 'coal' );
	$cards = '';
	foreach ( $projects as $i => $p ) {
		$slugs   = array_map( static fn( $t ) => $t['slug'], $p['cats'] );
		$catname = $p['cats'][0]['name'] ?? '';
		$yt      = triple5_projects_youtube_id( $p['video'] );
		$has_vid = '' !== $p['video'];

		// Lightbox payload: photos + optional video, hex-JSON so attribute sanitizing can't mangle it.
		$payload = bin2hex(
			wp_json_encode(
				array(
					'title'  => $p['title'],
					'photos' => $p['photos'],
					'video'  => $has_vid ? ( '' !== $yt ? array( 'type' => 'youtube', 'id' => $yt ) : array( 'type' => 'file', 'src' => $p['video'] ) ) : null,
				)
			)
		);

		if ( '' !== $p['card'] ) {
			$media = '<img src="' . esc_url( $p['card'] ) . '" alt="' . esc_attr( $p['title'] ) . '" loading="lazy">';
		} else {
			$media = '<span class="t5-gallery__ph is-tone-' . esc_attr( $tones[ $i % 3 ] ) . '">' . triple5_projects_icon( 'image' )
				. '<span class="t5-gallery__ph-label">' . esc_html( '' !== $catname ? $catname : $p['title'] ) . '</span></span>';
		}
		$count_badge = count( $p['photos'] ) > 1 ? '<span class="t5-gallery__count">' . triple5_projects_icon( 'photos' ) . count( $p['photos'] ) . '</span>' : '';
		$play_badge  = $has_vid ? '<span class="t5-gallery__play" aria-hidden="true">' . triple5_projects_icon( 'play' ) . '</span>' : '';
		$href        = ! empty( $p['photos'] ) ? $p['photos'][0]['src'] : ( $has_vid ? $p['video'] : '#' );

		$cards .= '<article class="t5-gallery__card" data-cats="' . esc_attr( implode( ' ', $slugs ) ) . '">'
			. '<a class="t5-gallery__media" href="' . esc_url( $href ) . '" data-t5-lightbox="' . esc_attr( $payload ) . '" aria-label="' . esc_attr( 'View ' . $p['title'] ) . '">'
			. $media . $play_badge . $count_badge
			. '</a>'
			. '<div class="t5-gallery__body">'
			. ( '' !== $catname ? '<span class="t5-gallery__cat">' . esc_html( $catname ) . '</span>' : '' )
			. '<h3 class="t5-gallery__title">' . esc_html( $p['title'] ) . '</h3>'
			. ( '' !== $p['location'] ? '<span class="t5-gallery__loc">' . triple5_projects_icon( 'pin' ) . esc_html( $p['location'] ) . '</span>' : '' )
			. '</div></article>';
	}

	$grid = '' !== $cards
		? '<div class="t5-gallery__grid has-cols-' . esc_attr( $c['columns'] ) . '">' . $cards . '</div><p class="t5-gallery__none" hidden>No projects in this category yet.</p>'
		: '<p class="t5-gallery__none">No projects published yet — add some under <strong>Projects</strong> in the dashboard.</p>';

	return '<section class="t5-gallery" data-t5-gallery>'
		. '<div class="t5-gallery__inner">' . $head . $filters . $grid . '</div>'
		. '</section>';
}

/** Runtime swap: replace the baked mount with a fresh render on every view. */
add_filter(
	'the_content',
	static function ( $content ) {
		if ( false === strpos( $content, 't5-gallery-mount' ) ) {
			return $content;
		}
		return preg_replace_callback(
			'/<div class="t5-gallery-mount" data-t5-gallery="([0-9a-fA-F]*)">.*?<\/section><\/div>/s',
			static function ( $m ) {
				$json   = '' !== $m[1] ? @hex2bin( $m[1] ) : ''; // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$config = $json ? json_decode( $json, true ) : null;
				if ( ! is_array( $config ) ) {
					return $m[0];
				}
				return '<div class="t5-gallery-mount">' . triple5_projects_render_section( $config ) . '</div>';
			},
			$content
		);
	},
	20
);
