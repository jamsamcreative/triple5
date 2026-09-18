<?php
/**
 * Triple 5 Testimonials — Cornerstone element definition.
 *
 * Centered uppercase heading + a responsive grid of review cards. Cards come from
 * two pools merged by sources.php: reviews fetched from Google / Yelp (cached and
 * moderated on Settings → Triple 5 Reviews) and up to 6 hand-entered reviews on
 * this element. A minimum-star control filters out poor reviews (default 4 →
 * 1–3★ never shown). Each card: initial avatar, name, location, source badge,
 * stars, quote.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Manual slots and their sample defaults: [name, location, rating, text, source]. */
function triple5_reviews_manual_defaults() {
	return array(
		1 => array( 'Mark', 'Spokane Valley, WA', 5, 'Triple 5 replaced our metal roof and siding as one project. Ordered materials, made sure it all matched, and left the site spotless. Will use them again.', 'google' ),
		2 => array( 'Sadia', "Coeur d'Alene, ID", 5, 'I called about hail damage on our roof. They replied immediately, visited the next day, and had the issue fixed so quickly. Thoroughly recommend.', 'google' ),
		3 => array( 'Arden', 'Rathdrum, ID', 5, 'Great communication, polite and friendly. Very knowledgeable and honest about what we actually needed. Standing seam roof looks fantastic.', 'google' ),
		4 => array( 'Priya & Sam', 'Mead, WA', 5, 'Reliable, on time, and genuinely local. They handled our shop building metal roof start to finish. Fair price, excellent work.', 'google' ),
		5 => array( '', '', 5, '', 'google' ),
		6 => array( '', '', 5, '', 'google' ),
	);
}

// -----------------------------------------------------------------------------
// Values (defaults)
// -----------------------------------------------------------------------------

$triple5_reviews_vals = array(
	't5r_heading'      => cs_value( 'What our clients say', 'markup', true ),
	't5r_min_rating'   => cs_value( '4', 'markup', true ),
	't5r_limit'        => cs_value( '4', 'markup', true ),
	't5r_columns'      => cs_value( '4', 'markup', true ),
	't5r_use_google'   => cs_value( true, 'markup', true ),
	't5r_use_yelp'     => cs_value( true, 'markup', true ),
	't5r_use_manual'   => cs_value( true, 'markup', true ),
	't5r_manual_first' => cs_value( false, 'markup', true ),
	't5r_show_badge'   => cs_value( true, 'markup', true ),
	't5r_link_cards'   => cs_value( true, 'markup', true ),
);
foreach ( triple5_reviews_manual_defaults() as $i => $m ) {
	$triple5_reviews_vals[ "t5r_m{$i}_name" ]     = cs_value( $m[0], 'markup', true );
	$triple5_reviews_vals[ "t5r_m{$i}_location" ] = cs_value( $m[1], 'markup', true );
	$triple5_reviews_vals[ "t5r_m{$i}_rating" ]   = cs_value( (string) $m[2], 'markup', true );
	$triple5_reviews_vals[ "t5r_m{$i}_text" ]     = cs_value( $m[3], 'markup', true );
	$triple5_reviews_vals[ "t5r_m{$i}_source" ]   = cs_value( $m[4], 'markup', true );
	$triple5_reviews_vals[ "t5r_m{$i}_url" ]      = cs_value( '', 'markup', true );
}
$triple5_reviews_values = cs_compose_values( $triple5_reviews_vals );

// -----------------------------------------------------------------------------
// Render
// -----------------------------------------------------------------------------

/**
 * Element data → plain config array. Everything the runtime renderer needs rides
 * in here, so fetched reviews can be pulled fresh on every page view instead of
 * being frozen into post_content at Cornerstone's bake time.
 */
function triple5_reviews_config_from_data( $data ) {
	$manual = array();
	if ( ! empty( $data['t5r_use_manual'] ) ) {
		foreach ( array_keys( triple5_reviews_manual_defaults() ) as $i ) {
			$name = trim( (string) ( $data[ "t5r_m{$i}_name" ] ?? '' ) );
			$text = trim( (string) ( $data[ "t5r_m{$i}_text" ] ?? '' ) );
			if ( '' === $name || '' === $text ) {
				continue;
			}
			$src      = (string) ( $data[ "t5r_m{$i}_source" ] ?? 'manual' );
			$manual[] = array(
				'id'       => "manual:{$i}",
				'source'   => array_key_exists( $src, triple5_reviews_source_labels() ) ? $src : 'manual',
				'author'   => $name,
				'location' => trim( (string) ( $data[ "t5r_m{$i}_location" ] ?? '' ) ),
				'rating'   => (int) ( $data[ "t5r_m{$i}_rating" ] ?? 5 ),
				'text'     => $text,
				'time'     => 0,
				'url'      => trim( (string) ( $data[ "t5r_m{$i}_url" ] ?? '' ) ),
			);
		}
	}

	$sources = array();
	if ( ! empty( $data['t5r_use_google'] ) ) {
		$sources[] = 'google';
	}
	if ( ! empty( $data['t5r_use_yelp'] ) ) {
		$sources[] = 'yelp';
	}

	return array(
		'heading'      => trim( (string) $data['t5r_heading'] ),
		'min_rating'   => (int) $data['t5r_min_rating'],
		'limit'        => (int) $data['t5r_limit'],
		'columns'      => in_array( (string) $data['t5r_columns'], array( '2', '3', '4' ), true ) ? (string) $data['t5r_columns'] : '4',
		'sources'      => $sources,
		'manual'       => $manual,
		'manual_first' => ! empty( $data['t5r_manual_first'] ),
		'show_badge'   => ! empty( $data['t5r_show_badge'] ),
		'link_cards'   => ! empty( $data['t5r_link_cards'] ),
	);
}

/**
 * Cornerstone render. Outputs the section (so the builder preview and the baked
 * post_content both show cards) wrapped in a mount div carrying the hex-encoded
 * config; the `the_content` filter below re-renders it fresh on every front-end
 * view so newly fetched / moderated reviews appear without re-saving the page.
 */
function triple5_reviews_render( $data ) {
	$config  = triple5_reviews_config_from_data( $data );
	$payload = bin2hex( wp_json_encode( $config ) );
	return '<div class="t5-reviews-mount" data-t5-reviews="' . esc_attr( $payload ) . '">'
		. triple5_reviews_render_section( $config )
		. '</div>';
}

// -----------------------------------------------------------------------------
// Builder controls
// -----------------------------------------------------------------------------

function triple5_reviews_builder_setup() {
	$rating_choices = array();
	foreach ( array( 5, 4, 3, 2, 1 ) as $n ) {
		$rating_choices[] = array( 'value' => (string) $n, 'label' => str_repeat( '★', $n ) . " ({$n})" );
	}
	$source_choices = array();
	foreach ( triple5_reviews_source_labels() as $k => $l ) {
		$source_choices[] = array( 'value' => $k, 'label' => $l );
	}

	$groups = array(
		array(
			'type'     => 'group',
			'group'    => 't5-reviews:setup',
			'controls' => array(
				array( 'key' => 't5r_heading', 'type' => 'text', 'label' => 'Heading' ),
				array(
					'key'     => 't5r_min_rating',
					'type'    => 'select',
					'label'   => 'Minimum star rating shown (hides anything lower)',
					'options' => array(
						'choices' => array(
							array( 'value' => '5', 'label' => '5★ only' ),
							array( 'value' => '4', 'label' => '4★ and up (hides 1–3★)' ),
							array( 'value' => '3', 'label' => '3★ and up' ),
							array( 'value' => '2', 'label' => '2★ and up' ),
							array( 'value' => '1', 'label' => 'Show all' ),
						),
					),
				),
				array( 'key' => 't5r_limit', 'type' => 'text', 'label' => 'Max reviews to show (0 = all)' ),
				array(
					'key'     => 't5r_columns',
					'type'    => 'choose',
					'label'   => 'Columns',
					'options' => array(
						'choices' => array(
							array( 'value' => '2', 'label' => '2' ),
							array( 'value' => '3', 'label' => '3' ),
							array( 'value' => '4', 'label' => '4' ),
						),
					),
				),
			),
		),
		array(
			'type'     => 'group',
			'group'    => 't5-reviews:sources',
			'controls' => array(
				array( 'key' => 't5r_use_google', 'type' => 'toggle', 'label' => 'Include Google reviews (needs Settings → Triple 5 Reviews)' ),
				array( 'key' => 't5r_use_yelp', 'type' => 'toggle', 'label' => 'Include Yelp reviews (needs Settings → Triple 5 Reviews)' ),
				array( 'key' => 't5r_use_manual', 'type' => 'toggle', 'label' => 'Include the manual reviews below' ),
				array( 'key' => 't5r_manual_first', 'type' => 'toggle', 'label' => 'Show manual reviews before fetched ones' ),
				array( 'key' => 't5r_show_badge', 'type' => 'toggle', 'label' => 'Show source badge (G / Yelp) on cards' ),
				array( 'key' => 't5r_link_cards', 'type' => 'toggle', 'label' => 'Link cards to the original review when a URL exists' ),
			),
		),
	);

	$nav = array(
		't5-reviews'         => 'Testimonials',
		't5-reviews:setup'   => 'Setup & filter',
		't5-reviews:sources' => 'Sources',
	);

	foreach ( array_keys( triple5_reviews_manual_defaults() ) as $i ) {
		$group_key         = "t5-reviews:m{$i}";
		$nav[ $group_key ] = "Manual review {$i}";
		$groups[]          = array(
			'type'     => 'group',
			'group'    => $group_key,
			'controls' => array(
				array( 'key' => "t5r_m{$i}_name", 'type' => 'text', 'label' => "Reviewer name (empty = hidden)" ),
				array( 'key' => "t5r_m{$i}_location", 'type' => 'text', 'label' => 'Location (e.g. Spokane Valley, WA)' ),
				array(
					'key'     => "t5r_m{$i}_rating",
					'type'    => 'select',
					'label'   => 'Rating',
					'options' => array( 'choices' => $rating_choices ),
				),
				array(
					'key'     => "t5r_m{$i}_text",
					'type'    => 'textarea',
					'label'   => 'Review text',
					'options' => array( 'height' => 4 ),
				),
				array(
					'key'     => "t5r_m{$i}_source",
					'type'    => 'select',
					'label'   => 'Source badge',
					'options' => array( 'choices' => $source_choices ),
				),
				array( 'key' => "t5r_m{$i}_url", 'type' => 'text', 'label' => 'Link to original review (optional)' ),
			),
		);
	}

	return cs_compose_controls(
		array(
			'controls'    => $groups,
			'control_nav' => $nav,
		)
	);
}

// -----------------------------------------------------------------------------
// Register
// -----------------------------------------------------------------------------

cs_register_element(
	't5-reviews',
	array(
		'title'   => 'Triple 5 Testimonials',
		'values'  => $triple5_reviews_values,
		'builder' => 'triple5_reviews_builder_setup',
		'render'  => 'triple5_reviews_render',
		'icon'    => 'native',
		'group'   => 'layout',
	)
);
