<?php
/**
 * Triple 5 Testimonials — front-end rendering.
 *
 * Loaded on every request (not just inside Cornerstone) because the `the_content`
 * filter here re-renders the section fresh on each page view from the config the
 * element baked into post_content. That is what makes fetched / moderated reviews
 * appear live without re-saving the page.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Platform mark: brand glyph for Google / Yelp / Facebook / Trustpilot, quote mark otherwise. */
function triple5_reviews_platform_glyph( $source, $size = 14 ) {
	switch ( $source ) {
		case 'google':
			return '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" aria-hidden="true"><path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.4h6.5a5.6 5.6 0 0 1-2.4 3.7v3h3.9c2.3-2.1 3.5-5.2 3.5-8.8z"/><path fill="#34A853" d="M12 24c3.2 0 6-1.1 7.9-2.9l-3.9-3a7.2 7.2 0 0 1-10.7-3.8H1.4v3.1A12 12 0 0 0 12 24z"/><path fill="#FBBC05" d="M5.3 14.3a7.2 7.2 0 0 1 0-4.6V6.6H1.4a12 12 0 0 0 0 10.8l3.9-3.1z"/><path fill="#EA4335" d="M12 4.8c1.8 0 3.3.6 4.6 1.8l3.4-3.4A12 12 0 0 0 1.4 6.6l3.9 3.1A7.2 7.2 0 0 1 12 4.8z"/></svg>';
		case 'yelp':
			return '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" aria-hidden="true"><path fill="#D32323" d="M10.3 13.6 4 15.7c-.7.2-1.3-.4-1.2-1.1l.3-2.7c.1-.7.9-1.1 1.5-.8l5.9 2.1c.5.2.4.9-.2.4zm.9-1.9L5.4 6.4c-.5-.5-.3-1.3.4-1.5L8.6 4c.6-.2 1.3.2 1.3.9l.6 6.5c.1.6-.7.9-.9.3zm2 2.6 3.4 5.6c.4.6 0 1.4-.7 1.4l-2.8-.2c-.7 0-1.1-.8-.8-1.4l2.1-5.4c.3-.6.9-.5.8 0zm.5-1.6 6.1-1.8c.7-.2 1.3.4 1.2 1.1l-.4 2.7c-.1.7-.9 1.1-1.5.8l-5.7-2.4c-.5-.2-.3-.9.3-.4zm-.4-1.7 3.6-5.3c.4-.6 1.3-.5 1.6.1l1.2 2.5c.3.6-.1 1.4-.8 1.4l-6-.3c-.6 0-.8-.8-.2-.9z"/></svg>';
		case 'facebook':
			return '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" aria-hidden="true"><circle cx="12" cy="12" r="12" fill="#1877F2"/><path fill="#fff" d="M13.5 21v-7.2h2.4l.4-2.9h-2.8V9.1c0-.8.2-1.4 1.4-1.4h1.5V5.1c-.3 0-1.2-.1-2.2-.1-2.2 0-3.7 1.3-3.7 3.8v2.1H8v2.9h2.5V21h3z"/></svg>';
		case 'trustpilot':
			return '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" aria-hidden="true"><path fill="#00B67A" d="M12 2.5l2.9 6.2 6.7.8-5 4.6 1.3 6.7L12 17.5l-5.9 3.3 1.3-6.7-5-4.6 6.7-.8z"/></svg>';
	}
	return '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" fill="currentColor" aria-hidden="true"><path d="M7.2 5C4.9 5 3 6.9 3 9.2c0 2.1 1.5 3.8 3.5 4.1-.4 1.6-1.5 2.8-3.1 3.5-.3.1-.4.5-.2.8.1.2.3.3.5.3h.1C8.4 17 11 13.9 11 10.1V9.2C11 6.9 9.3 5 7.2 5zm10 0C14.9 5 13 6.9 13 9.2c0 2.1 1.5 3.8 3.5 4.1-.4 1.6-1.5 2.8-3.1 3.5-.3.1-.4.5-.2.8.1.2.3.3.5.3h.1C18.4 17 21 13.9 21 10.1V9.2C21 6.9 19.3 5 17.2 5z"/></svg>';
}

/**
 * Card badge: platform glyph + name. Links to the review itself when it has a
 * URL, otherwise to the platform's review page from the platform row.
 */
function triple5_reviews_badge( $source, $review_url = '', $platform_url = '', $link = true ) {
	$labels = triple5_reviews_source_labels();
	$label  = $labels[ $source ] ?? ucfirst( $source );
	$inner  = triple5_reviews_platform_glyph( $source ) . '<span class="t5-reviews__badge-name">' . esc_html( $label ) . '</span>';
	$href   = '' !== $review_url ? $review_url : $platform_url;
	if ( $link && '' !== $href ) {
		return '<a class="t5-reviews__badge is-' . esc_attr( $source ) . ' is-link" href="' . esc_url( $href ) . '" target="_blank" rel="noopener" title="' . esc_attr( 'View on ' . $label ) . '">' . $inner . '<span class="screen-reader-text"> (opens in a new tab)</span></a>';
	}
	return '<span class="t5-reviews__badge is-' . esc_attr( $source ) . '">' . $inner . '</span>';
}

/** Platform row: "Read our reviews on  [G Google 5.0 ★ · 38 reviews ↗] [Trustpilot 4.9 ★ ↗] …" */
function triple5_reviews_platform_row( array $c ) {
	if ( empty( $c['show_platforms'] ) || empty( $c['platforms'] ) ) {
		return '';
	}
	$labels = triple5_reviews_source_labels();
	$items  = '';
	foreach ( $c['platforms'] as $key => $pf ) {
		if ( empty( $pf['show'] ) ) {
			continue;
		}
		$name  = $labels[ $key ] ?? ucfirst( $key );
		$meta  = '';
		if ( '' !== $pf['rating'] ) {
			$meta .= '<span class="t5-reviews__pf-rating">' . esc_html( $pf['rating'] ) . '<svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor" aria-hidden="true"><path d="M12 2.5l2.9 6.2 6.7.8-5 4.6 1.3 6.7L12 17.5l-5.9 3.3 1.3-6.7-5-4.6 6.7-.8z"/></svg></span>';
		}
		if ( '' !== $pf['count'] ) {
			$meta .= '<span class="t5-reviews__pf-count">' . esc_html( $pf['count'] ) . '</span>';
		}
		$inner = triple5_reviews_platform_glyph( $key, 18 ) . '<span class="t5-reviews__pf-name">' . esc_html( $name ) . '</span>' . $meta;
		$items .= '' !== $pf['url']
			? '<a class="t5-reviews__pf is-link" href="' . esc_url( $pf['url'] ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( 'Read our ' . $name . ' reviews (opens in a new tab)' ) . '">' . $inner . '<svg class="t5-reviews__pf-arrow" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7"/><path d="M8 7h9v9"/></svg></a>'
			: '<span class="t5-reviews__pf">' . $inner . '</span>';
	}
	if ( '' === $items ) {
		return '';
	}
	return '<div class="t5-reviews__platforms">'
		. ( '' !== $c['platforms_label'] ? '<span class="t5-reviews__pf-label">' . esc_html( $c['platforms_label'] ) . '</span>' : '' )
		. $items . '</div>';
}

function triple5_reviews_stars( $rating ) {
	$rating = max( 0, min( 5, (int) $rating ) );
	$star   = '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M12 2.5l2.9 6.2 6.7.8-5 4.6 1.3 6.7L12 17.5l-5.9 3.3 1.3-6.7-5-4.6 6.7-.8z"/></svg>';
	return '<span class="t5-reviews__stars" aria-label="' . esc_attr( $rating . ' out of 5 stars' ) . '">'
		. str_repeat( '<span class="t5-reviews__star is-on">' . $star . '</span>', $rating )
		. str_repeat( '<span class="t5-reviews__star">' . $star . '</span>', 5 - $rating )
		. '</span>';
}

/** Render the section from a config array (used at bake time AND at runtime). */
function triple5_reviews_render_section( array $c ) {
	$reviews = triple5_reviews_for_display(
		array(
			'min_rating'   => $c['min_rating'],
			'sources'      => $c['sources'],
			'manual'       => $c['manual'],
			'limit'        => $c['limit'],
			'manual_first' => $c['manual_first'],
		)
	);

	$cards = '';
	foreach ( $reviews as $r ) {
		$initial      = mb_strtoupper( mb_substr( trim( $r['author'] ), 0, 1 ) );
		$platform_url = $c['platforms'][ $r['source'] ]['url'] ?? '';
		$inner   = '<div class="t5-reviews__head">'
			. '<span class="t5-reviews__avatar" aria-hidden="true">' . esc_html( $initial ) . '</span>'
			. '<span class="t5-reviews__who">'
			. '<span class="t5-reviews__name">' . esc_html( $r['author'] ) . '</span>'
			. ( '' !== $r['location'] ? '<span class="t5-reviews__loc">' . esc_html( $r['location'] ) . '</span>' : '' )
			. '</span>'
			. '</div>'
			. '<div class="t5-reviews__meta">'
			. triple5_reviews_stars( $r['rating'] )
			. ( $c['show_badge'] ? triple5_reviews_badge( $r['source'], $r['url'], $platform_url, $c['link_cards'] ) : '' )
			. '</div>'
			. '<p class="t5-reviews__text">' . esc_html( $r['text'] ) . '</p>';

		// Cards are articles, not links, so the badge inside can be the outbound link.
		$cards .= '<article class="t5-reviews__card">' . $inner . '</article>';
	}

	return '<section class="t5-reviews">'
		. '<div class="t5-reviews__inner">'
		. ( '' !== $c['heading'] ? '<h2 class="t5-reviews__heading">' . esc_html( $c['heading'] ) . '</h2>' : '' )
		. triple5_reviews_platform_row( $c )
		. ( '' !== $cards
			? '<div class="t5-reviews__grid has-cols-' . esc_attr( $c['columns'] ) . '">' . $cards . '</div>'
			: '<p class="t5-reviews__empty">No reviews to show yet.</p>' )
		. '</div></section>';
}

add_filter(
	'the_content',
	static function ( $content ) {
		if ( false === strpos( $content, 't5-reviews-mount' ) ) {
			return $content;
		}
		return preg_replace_callback(
			'/<div class="t5-reviews-mount" data-t5-reviews="([0-9a-fA-F]*)">.*?<\/section><\/div>/s',
			static function ( $m ) {
				$json   = '' !== $m[1] ? @hex2bin( $m[1] ) : ''; // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$config = $json ? json_decode( $json, true ) : null;
				if ( ! is_array( $config ) ) {
					return $m[0]; // leave the baked markup in place
				}
				return '<div class="t5-reviews-mount">' . triple5_reviews_render_section( $config ) . '</div>';
			},
			$content
		);
	},
	20
);

