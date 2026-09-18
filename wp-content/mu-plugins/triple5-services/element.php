<?php
/**
 * Triple 5 Services Grid — Cornerstone element definition.
 *
 * A centered section header (eyebrow + heading) followed by a responsive grid of up
 * to 6 service cards. Each card has a title, description, "Read more" link, a media
 * tone (navy / clay / coal blueprint placeholder), and an optional real image.
 * Cards with an empty title are skipped, so a placement can show 2–6 cards.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Max card slots and their Triple 5 defaults: [title, description, tone]. */
function triple5_services_card_defaults() {
	return array(
		1 => array( 'Metal Roofing', 'Standing seam, corrugated and metal shingle systems in steel, aluminum and copper — built to last.', 'navy' ),
		2 => array( 'Roofing Repairs', 'Leak repairs, storm damage and re-flashing to get your roof watertight again, fast.', 'clay' ),
		3 => array( 'Metal Siding', 'Vertical, horizontal and board-and-batten panel siding, color-matched to your roof.', 'coal' ),
		4 => array( 'Storm & Insurance Work', 'Wind, hail and snow-load damage assessments and insurance-ready repair estimates.', 'navy' ),
		5 => array( 'Concrete & Framing', 'Driveways, patios, foundations, flatwork and framing for new builds and additions.', 'clay' ),
		6 => array( 'Commercial & Ag', 'Durable, low-maintenance metal exteriors for shops, barns and light commercial.', 'navy' ),
	);
}

// -----------------------------------------------------------------------------
// Values (defaults)
// -----------------------------------------------------------------------------

$triple5_services_vals = array(
	't5s_eyebrow' => cs_value( 'Triple 5 Construction', 'markup', true ),
	't5s_heading' => cs_value( "Residential & commercial\nroofing & siding services", 'markup', true ),
);
foreach ( triple5_services_card_defaults() as $i => $c ) {
	$triple5_services_vals[ "t5s_card{$i}_title" ]        = cs_value( $c[0], 'markup', true );
	$triple5_services_vals[ "t5s_card{$i}_desc" ]         = cs_value( $c[1], 'markup', true );
	$triple5_services_vals[ "t5s_card{$i}_url" ]          = cs_value( '#', 'markup', true );
	$triple5_services_vals[ "t5s_card{$i}_tone" ]         = cs_value( $c[2], 'markup', true );
	$triple5_services_vals[ "t5s_card{$i}_image_src" ]    = cs_value( '', 'markup', true );
	$triple5_services_vals[ "t5s_card{$i}_image_retina" ] = cs_value( true, 'markup', true );
	$triple5_services_vals[ "t5s_card{$i}_image_width" ]  = cs_value( '', 'markup', true );
	$triple5_services_vals[ "t5s_card{$i}_image_height" ] = cs_value( '', 'markup', true );
}
$triple5_services_values = cs_compose_values( $triple5_services_vals );

// -----------------------------------------------------------------------------
// Render
// -----------------------------------------------------------------------------

/** Lucide "image" icon for the placeholder media. */
function triple5_services_image_icon() {
	return '<svg class="t5-services__media-icon" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg>';
}

function triple5_services_render( $data ) {
	// Header.
	$eyebrow = '' !== trim( (string) $data['t5s_eyebrow'] )
		? '<p class="t5-services__eyebrow">' . esc_html( $data['t5s_eyebrow'] ) . '</p>'
		: '';
	$heading = '' !== trim( (string) $data['t5s_heading'] )
		? '<h2 class="t5-services__heading">' . nl2br( esc_html( $data['t5s_heading'] ) ) . '</h2>'
		: '';

	// Cards — skip any with an empty title.
	$cards     = '';
	$arrow     = '<svg class="t5-services__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>';
	$valid_tones = array( 'navy', 'clay', 'coal' );

	foreach ( array_keys( triple5_services_card_defaults() ) as $i ) {
		$title = isset( $data[ "t5s_card{$i}_title" ] ) ? trim( (string) $data[ "t5s_card{$i}_title" ] ) : '';
		if ( '' === $title ) {
			continue;
		}
		$desc = isset( $data[ "t5s_card{$i}_desc" ] ) ? (string) $data[ "t5s_card{$i}_desc" ] : '';
		$url  = isset( $data[ "t5s_card{$i}_url" ] ) ? trim( (string) $data[ "t5s_card{$i}_url" ] ) : '';
		$href = '' !== $url ? esc_url( $url ) : '#';
		$tone = isset( $data[ "t5s_card{$i}_tone" ] ) && in_array( $data[ "t5s_card{$i}_tone" ], $valid_tones, true ) ? $data[ "t5s_card{$i}_tone" ] : 'navy';
		$img  = isset( $data[ "t5s_card{$i}_image_src" ] ) ? trim( (string) $data[ "t5s_card{$i}_image_src" ] ) : '';

		if ( '' !== $img ) {
			$media = '<div class="t5-services__media has-image" style="background-image:url(\'' . esc_url( $img ) . '\')"></div>';
		} else {
			$media = '<div class="t5-services__media is-tone-' . esc_attr( $tone ) . '">'
				. triple5_services_image_icon()
				. '<span class="t5-services__media-label">' . esc_html( $title ) . '</span>'
				. '</div>';
		}

		$cards .= '<div class="t5-services__card">'
			. $media
			. '<div class="t5-services__body">'
			. '<h3 class="t5-services__title">' . esc_html( $title ) . '</h3>'
			. ( '' !== trim( $desc ) ? '<p class="t5-services__desc">' . esc_html( $desc ) . '</p>' : '' )
			. '<a class="t5-services__more" href="' . $href . '">Read more ' . $arrow . '</a>'
			. '</div></div>';
	}

	return '<section class="t5-services">'
		. '<div class="t5-services__inner">'
		. '<div class="t5-services__head">' . $eyebrow . $heading . '</div>'
		. '<div class="t5-services__grid">' . $cards . '</div>'
		. '</div></section>';
}

// -----------------------------------------------------------------------------
// Builder controls
// -----------------------------------------------------------------------------

function triple5_services_builder_setup() {
	$groups = array(
		array(
			'type'     => 'group',
			'group'    => 't5-services:header',
			'controls' => array(
				array( 'key' => 't5s_eyebrow', 'type' => 'text', 'label' => 'Eyebrow' ),
				array(
					'key'     => 't5s_heading',
					'type'    => 'textarea',
					'label'   => 'Heading',
					'options' => array( 'height' => 2 ),
				),
			),
		),
	);

	$nav = array(
		't5-services'        => 'Services',
		't5-services:header' => 'Header',
	);

	foreach ( array_keys( triple5_services_card_defaults() ) as $i ) {
		$group_key   = "t5-services:card{$i}";
		$nav[ $group_key ] = "Card {$i}";
		$groups[]    = array(
			'type'     => 'group',
			'group'    => $group_key,
			'controls' => array(
				array( 'key' => "t5s_card{$i}_title", 'type' => 'text', 'label' => "Card {$i} title (empty = hidden)" ),
				array(
					'key'     => "t5s_card{$i}_desc",
					'type'    => 'textarea',
					'label'   => 'Description',
					'options' => array( 'height' => 3 ),
				),
				array( 'key' => "t5s_card{$i}_url", 'type' => 'text', 'label' => 'Read more link (URL)' ),
				array(
					'key'     => "t5s_card{$i}_tone",
					'type'    => 'choose',
					'label'   => 'Placeholder tone',
					'options' => array(
						'choices' => array(
							array( 'value' => 'navy', 'label' => 'Charcoal' ),
							array( 'value' => 'clay', 'label' => 'Red' ),
							array( 'value' => 'coal', 'label' => 'Coal' ),
						),
					),
				),
				array(
					'keys'  => array(
						'img_source' => "t5s_card{$i}_image_src",
						'is_retina'  => "t5s_card{$i}_image_retina",
						'width'      => "t5s_card{$i}_image_width",
						'height'     => "t5s_card{$i}_image_height",
					),
					'type'  => 'image',
					'title' => 'Image (overrides placeholder)',
				),
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
	't5-services',
	array(
		'title'   => 'Triple 5 Services Grid',
		'values'  => $triple5_services_values,
		'builder' => 'triple5_services_builder_setup',
		'render'  => 'triple5_services_render',
		'icon'    => 'native',
		'group'   => 'layout',
	)
);
