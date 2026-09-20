<?php
/**
 * Triple 5 Testimonials — review sources.
 *
 * Fetches reviews from Google (Places API, New) and Yelp (Fusion API), normalizes
 * them into one shape, caches the merged list, and applies moderation (hidden IDs).
 * The last successful fetch is kept in an option so an API failure never blanks the
 * section on the live site.
 *
 * Normalized review shape:
 *   id       string  stable, source-prefixed ("google:…", "yelp:…", "manual:…")
 *   source   string  google | yelp | manual
 *   author   string
 *   location string  (Google/Yelp don't expose reviewer location; blank unless manual)
 *   rating   int     1–5
 *   text     string
 *   time     int     unix timestamp (0 if unknown)
 *   url      string  link to the review / profile (may be blank)
 *
 * Providers are simple callables keyed by source name — add Facebook etc. in
 * triple5_reviews_providers().
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -----------------------------------------------------------------------------
// Settings
// -----------------------------------------------------------------------------

/** Option name holding all settings + moderation state. */
function triple5_reviews_option_key() {
	return 'triple5_reviews_settings';
}

/** Settings with defaults. */
function triple5_reviews_settings() {
	$defaults = array(
		'google_api_key'   => '',
		'google_place_id'  => '',
		'yelp_api_key'     => '',
		'yelp_business_id' => '',
		'cache_hours'      => 12,
		'hidden_ids'       => array(),
		'last_fetch'       => 0,
		'last_error'       => '',
	);
	$saved = get_option( triple5_reviews_option_key(), array() );
	return wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults );
}

function triple5_reviews_update_settings( array $patch ) {
	update_option( triple5_reviews_option_key(), array_merge( triple5_reviews_settings(), $patch ), false );
}

/** Source labels shared by the builder, the admin page and the card badge. */
function triple5_reviews_source_labels() {
	return array(
		'google'     => 'Google',
		'yelp'       => 'Yelp',
		'facebook'   => 'Facebook',
		'trustpilot' => 'Trustpilot',
		'manual'     => 'Manual',
	);
}

// -----------------------------------------------------------------------------
// Providers
// -----------------------------------------------------------------------------

/** Source name → fetch callable. Each returns array<review> or WP_Error. */
function triple5_reviews_providers() {
	return array(
		'google' => 'triple5_reviews_fetch_google',
		'yelp'   => 'triple5_reviews_fetch_yelp',
	);
}

/** Whether a provider has enough config to be called. */
function triple5_reviews_provider_configured( $source ) {
	$s = triple5_reviews_settings();
	switch ( $source ) {
		case 'google':
			return '' !== trim( $s['google_api_key'] ) && '' !== trim( $s['google_place_id'] );
		case 'yelp':
			return '' !== trim( $s['yelp_api_key'] ) && '' !== trim( $s['yelp_business_id'] );
	}
	return false;
}

/**
 * Google Places API (New): GET /v1/places/{placeId} with a field mask.
 * Returns at most 5 reviews (API limit). Requires "Places API (New)" enabled.
 */
function triple5_reviews_fetch_google() {
	$s   = triple5_reviews_settings();
	$url = 'https://places.googleapis.com/v1/places/' . rawurlencode( trim( $s['google_place_id'] ) )
		. '?languageCode=en';

	$res = wp_remote_get(
		$url,
		array(
			'timeout' => 12,
			'headers' => array(
				'X-Goog-Api-Key'   => trim( $s['google_api_key'] ),
				'X-Goog-FieldMask' => 'reviews,rating,userRatingCount,googleMapsUri',
			),
		)
	);
	if ( is_wp_error( $res ) ) {
		return $res;
	}
	$code = wp_remote_retrieve_response_code( $res );
	$body = json_decode( wp_remote_retrieve_body( $res ), true );
	if ( 200 !== $code ) {
		$msg = isset( $body['error']['message'] ) ? $body['error']['message'] : 'HTTP ' . $code;
		return new WP_Error( 'google', 'Google: ' . $msg );
	}

	$out = array();
	foreach ( (array) ( $body['reviews'] ?? array() ) as $r ) {
		$text = isset( $r['text']['text'] ) ? $r['text']['text'] : ( $r['originalText']['text'] ?? '' );
		if ( '' === trim( (string) $text ) ) {
			continue; // rating-only reviews have nothing to show.
		}
		$id    = isset( $r['name'] ) ? md5( $r['name'] ) : md5( ( $r['authorAttribution']['displayName'] ?? '' ) . ( $r['publishTime'] ?? '' ) );
		$out[] = array(
			'id'       => 'google:' . $id,
			'source'   => 'google',
			'author'   => (string) ( $r['authorAttribution']['displayName'] ?? 'Google user' ),
			'location' => '',
			'rating'   => (int) round( (float) ( $r['rating'] ?? 0 ) ),
			'text'     => (string) $text,
			'time'     => isset( $r['publishTime'] ) ? (int) strtotime( $r['publishTime'] ) : 0,
			'url'      => (string) ( $r['authorAttribution']['uri'] ?? ( $body['googleMapsUri'] ?? '' ) ),
		);
	}

	// Keep the aggregate for the settings page / future badge use.
	triple5_reviews_update_settings(
		array(
			'google_rating' => isset( $body['rating'] ) ? (float) $body['rating'] : 0,
			'google_count'  => isset( $body['userRatingCount'] ) ? (int) $body['userRatingCount'] : 0,
		)
	);

	return $out;
}

/**
 * Yelp Fusion: GET /v3/businesses/{id}/reviews — returns 3 reviews, text is
 * truncated by Yelp to ~160 chars (their terms require linking to the full review).
 */
function triple5_reviews_fetch_yelp() {
	$s   = triple5_reviews_settings();
	$url = 'https://api.yelp.com/v3/businesses/' . rawurlencode( trim( $s['yelp_business_id'] ) ) . '/reviews?limit=20&sort_by=yelp_sort';

	$res = wp_remote_get(
		$url,
		array(
			'timeout' => 12,
			'headers' => array(
				'Authorization' => 'Bearer ' . trim( $s['yelp_api_key'] ),
				'Accept'        => 'application/json',
			),
		)
	);
	if ( is_wp_error( $res ) ) {
		return $res;
	}
	$code = wp_remote_retrieve_response_code( $res );
	$body = json_decode( wp_remote_retrieve_body( $res ), true );
	if ( 200 !== $code ) {
		$msg = isset( $body['error']['description'] ) ? $body['error']['description'] : 'HTTP ' . $code;
		return new WP_Error( 'yelp', 'Yelp: ' . $msg );
	}

	$out = array();
	foreach ( (array) ( $body['reviews'] ?? array() ) as $r ) {
		if ( '' === trim( (string) ( $r['text'] ?? '' ) ) ) {
			continue;
		}
		$out[] = array(
			'id'       => 'yelp:' . (string) ( $r['id'] ?? md5( wp_json_encode( $r ) ) ),
			'source'   => 'yelp',
			'author'   => (string) ( $r['user']['name'] ?? 'Yelp user' ),
			'location' => '',
			'rating'   => (int) ( $r['rating'] ?? 0 ),
			'text'     => (string) $r['text'],
			'time'     => isset( $r['time_created'] ) ? (int) strtotime( $r['time_created'] ) : 0,
			'url'      => (string) ( $r['url'] ?? '' ),
		);
	}
	return $out;
}

// -----------------------------------------------------------------------------
// Cache + refresh
// -----------------------------------------------------------------------------

function triple5_reviews_cache_key() {
	return 'triple5_reviews_cache';
}

/** Option holding the last successful merged fetch (survives transient expiry). */
function triple5_reviews_last_good_key() {
	return 'triple5_reviews_last_good';
}

/**
 * Fetch every configured provider, merge, store. Returns the merged list.
 * Providers that error are logged to settings.last_error and their previous
 * reviews are kept from the last-good copy so one bad API doesn't drop a source.
 */
function triple5_reviews_refresh() {
	$last_good = get_option( triple5_reviews_last_good_key(), array() );
	$last_good = is_array( $last_good ) ? $last_good : array();
	$merged    = array();
	$errors    = array();

	foreach ( triple5_reviews_providers() as $source => $fn ) {
		if ( ! triple5_reviews_provider_configured( $source ) ) {
			continue;
		}
		$got = call_user_func( $fn );
		if ( is_wp_error( $got ) ) {
			$errors[] = $got->get_error_message();
			foreach ( $last_good as $r ) {
				if ( $r['source'] === $source ) {
					$merged[] = $r;
				}
			}
			continue;
		}
		$merged = array_merge( $merged, $got );
	}

	// Newest first.
	usort( $merged, static fn( $a, $b ) => $b['time'] <=> $a['time'] );

	$s   = triple5_reviews_settings();
	$ttl = max( 1, (int) $s['cache_hours'] ) * HOUR_IN_SECONDS;
	set_transient( triple5_reviews_cache_key(), $merged, $ttl );
	update_option( triple5_reviews_last_good_key(), $merged, false );
	triple5_reviews_update_settings(
		array(
			'last_fetch' => time(),
			'last_error' => implode( ' · ', $errors ),
		)
	);

	return $merged;
}

/**
 * All fetched reviews (unmoderated, unfiltered). Serves the cache, falling back
 * to the last-good copy, and only hits the APIs when both are empty/expired.
 * Never fetches during a front-end render if a last-good copy exists — the cron
 * job keeps it fresh instead.
 */
function triple5_reviews_fetched() {
	$cached = get_transient( triple5_reviews_cache_key() );
	if ( is_array( $cached ) ) {
		return $cached;
	}
	$last_good = get_option( triple5_reviews_last_good_key(), null );
	if ( is_array( $last_good ) ) {
		// Stale but usable; schedule a background refresh rather than blocking.
		if ( ! wp_next_scheduled( 'triple5_reviews_cron_refresh' ) ) {
			wp_schedule_single_event( time() + 60, 'triple5_reviews_cron_refresh' );
		}
		return $last_good;
	}
	// First run with providers configured: fetch inline once.
	$any = false;
	foreach ( array_keys( triple5_reviews_providers() ) as $source ) {
		$any = $any || triple5_reviews_provider_configured( $source );
	}
	return $any ? triple5_reviews_refresh() : array();
}

/** Daily cron keeps the cache warm; the transient TTL is the freshness bound. */
add_action( 'triple5_reviews_cron_refresh', 'triple5_reviews_refresh' );
add_action(
	'init',
	static function () {
		if ( ! wp_next_scheduled( 'triple5_reviews_cron_refresh' ) ) {
			wp_schedule_event( time() + 300, 'twicedaily', 'triple5_reviews_cron_refresh' );
		}
	}
);

// -----------------------------------------------------------------------------
// Moderation + filtering
// -----------------------------------------------------------------------------

/** Hidden review IDs (moderated out on the settings page). */
function triple5_reviews_hidden_ids() {
	$ids = triple5_reviews_settings()['hidden_ids'];
	return is_array( $ids ) ? array_values( array_filter( array_map( 'strval', $ids ) ) ) : array();
}

function triple5_reviews_set_hidden( $id, $hidden ) {
	$ids = triple5_reviews_hidden_ids();
	$ids = array_diff( $ids, array( (string) $id ) );
	if ( $hidden ) {
		$ids[] = (string) $id;
	}
	triple5_reviews_update_settings( array( 'hidden_ids' => array_values( array_unique( $ids ) ) ) );
}

/**
 * Reviews ready to display.
 *
 * @param array $args {
 *   @type int      $min_rating  Hide anything below this (default 4 → 1–3★ hidden).
 *   @type string[] $sources     Which fetched sources to include.
 *   @type array    $manual      Hand-entered reviews in the normalized shape.
 *   @type int      $limit       Max cards (0 = all).
 *   @type bool     $manual_first Put manual reviews ahead of fetched ones.
 * }
 */
function triple5_reviews_for_display( array $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'min_rating'   => 4,
			'sources'      => array( 'google', 'yelp' ),
			'manual'       => array(),
			'limit'        => 0,
			'manual_first' => false,
		)
	);

	$hidden  = triple5_reviews_hidden_ids();
	$fetched = array_filter(
		triple5_reviews_fetched(),
		static fn( $r ) => in_array( $r['source'], $args['sources'], true ) && ! in_array( $r['id'], $hidden, true )
	);

	$all = $args['manual_first']
		? array_merge( $args['manual'], array_values( $fetched ) )
		: array_merge( array_values( $fetched ), $args['manual'] );

	$min = max( 1, min( 5, (int) $args['min_rating'] ) );
	$all = array_values( array_filter( $all, static fn( $r ) => (int) $r['rating'] >= $min && '' !== trim( (string) $r['text'] ) ) );

	if ( $args['limit'] > 0 ) {
		$all = array_slice( $all, 0, (int) $args['limit'] );
	}
	return $all;
}
