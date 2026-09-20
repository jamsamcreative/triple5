<?php
/**
 * Triple 5 Contact — Cornerstone element definition.
 *
 * Stone page-header band (eyebrow, H1, subtext) then two columns:
 *   left  — contact details (phone, email, location, hours) as icon rows, a
 *           "what happens next" 3-step list, and an optional map/photo slot
 *   right — the lead form (same runtime-rendered form as the hero, so entries
 *           go to Quote Requests and email the recipient), in the dark card.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$triple5_contact_values = cs_compose_values(
	array(
		't5c_eyebrow'    => cs_value( 'Contact us', 'markup', true ),
		't5c_heading'    => cs_value( "Let's talk about your project", 'markup', true ),
		't5c_subtext'    => cs_value( "Call, email or send the form — we'll set up a free on-site estimate anywhere from Spokane to Coeur d'Alene and Sandpoint.", 'markup', true ),
		// Details column.
		't5c_details_heading' => cs_value( 'Reach the crew', 'markup', true ),
		't5c_phone'      => cs_value( '(509) 251-2829', 'markup', true ),
		't5c_email'      => cs_value( 'info@triple5construction.com', 'markup', true ),
		't5c_location'   => cs_value( "Based in Spokane, WA · Serving eastern Washington & North Idaho", 'markup', true ),
		't5c_hours'      => cs_value( 'Mon–Fri 7:00am–5:00pm · 24hr storm-damage callout', 'markup', true ),
		't5c_steps_heading' => cs_value( 'What happens next', 'markup', true ),
		't5c_steps'      => cs_value( "We call or email back within one business day\nFree on-site visit — we measure and walk your options\nClear written quote, no pressure", 'markup', true ),
		't5c_map_src'    => cs_value( '', 'markup', true ),
		't5c_map_retina' => cs_value( true, 'markup', true ),
		't5c_map_width'  => cs_value( '', 'markup', true ),
		't5c_map_height' => cs_value( '', 'markup', true ),
		't5c_map_url'    => cs_value( '', 'markup', true ),
		't5c_map_caption' => cs_value( "Service area — Spokane & Coeur d'Alene", 'markup', true ),
		't5c_show_map'   => cs_value( true, 'markup', true ),
		// Form.
		't5c_form_heading'   => cs_value( "Request a roofing,\nsiding or repair callout", 'markup', true ),
		't5c_form_recipient' => cs_value( '', 'markup', true ),
		't5c_form_button'    => cs_value( 'Send request', 'markup', true ),
		't5c_form_success'   => cs_value( "Thanks — we've got your request and will be in touch within one business day.", 'markup', true ),
	)
);

function triple5_contact_icon( $name ) {
	$paths = array(
		'phone' => '<path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/>',
		'mail'  => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
		'pin'   => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>',
		'clock' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
		'image' => '<rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>',
	);
	$size = 'image' === $name ? 30 : 20;
	return '<svg class="t5-contact__icon" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ( $paths[ $name ] ?? '' ) . '</svg>';
}

function triple5_contact_render( $data ) {
	$t = static fn( $k ) => trim( (string) ( $data[ $k ] ?? '' ) );

	// Header band.
	$header = '<header class="t5-contact__header"><div class="t5-contact__header-inner">'
		. ( '' !== $t( 't5c_eyebrow' ) ? '<p class="t5-contact__eyebrow">' . esc_html( $t( 't5c_eyebrow' ) ) . '</p>' : '' )
		. ( '' !== $t( 't5c_heading' ) ? '<h1 class="t5-contact__title">' . esc_html( $t( 't5c_heading' ) ) . '</h1>' : '' )
		. ( '' !== $t( 't5c_subtext' ) ? '<p class="t5-contact__sub">' . esc_html( $t( 't5c_subtext' ) ) . '</p>' : '' )
		. '</div></header>';

	// Details.
	$rows = '';
	if ( '' !== $t( 't5c_phone' ) ) {
		$rows .= '<li>' . triple5_contact_icon( 'phone' ) . '<a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $t( 't5c_phone' ) ) ) . '">' . esc_html( $t( 't5c_phone' ) ) . '</a></li>';
	}
	if ( '' !== $t( 't5c_email' ) ) {
		$rows .= '<li>' . triple5_contact_icon( 'mail' ) . '<a href="mailto:' . esc_attr( $t( 't5c_email' ) ) . '">' . esc_html( $t( 't5c_email' ) ) . '</a></li>';
	}
	if ( '' !== $t( 't5c_location' ) ) {
		$rows .= '<li>' . triple5_contact_icon( 'pin' ) . '<span>' . esc_html( $t( 't5c_location' ) ) . '</span></li>';
	}
	if ( '' !== $t( 't5c_hours' ) ) {
		$rows .= '<li>' . triple5_contact_icon( 'clock' ) . '<span>' . esc_html( $t( 't5c_hours' ) ) . '</span></li>';
	}

	$steps = '';
	foreach ( preg_split( '/\r\n|\r|\n/', $t( 't5c_steps' ) ) as $line ) {
		$line = trim( $line );
		if ( '' !== $line ) {
			$steps .= '<li>' . esc_html( $line ) . '</li>';
		}
	}

	$map = '';
	if ( ! empty( $data['t5c_show_map'] ) ) {
		$src     = triple5_image_url( $data['t5c_map_src'] ?? '' );
		$caption = $t( 't5c_map_caption' );
		$url     = $t( 't5c_map_url' );
		if ( '' !== $src ) {
			$inner = '<img src="' . esc_url( $src ) . '" alt="' . esc_attr( $caption ) . '" loading="lazy">';
			$map   = '' !== $url
				? '<a class="t5-contact__map has-image" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . $inner . '</a>'
				: '<div class="t5-contact__map has-image">' . $inner . '</div>';
		} else {
			$map = '<div class="t5-contact__map is-blueprint">' . triple5_contact_icon( 'image' )
				. ( '' !== $caption ? '<span class="t5-contact__map-label">' . esc_html( $caption ) . '</span>' : '' ) . '</div>';
		}
	}

	$details = '<div class="t5-contact__details">'
		. ( '' !== $t( 't5c_details_heading' ) ? '<h2 class="t5-contact__h2">' . esc_html( $t( 't5c_details_heading' ) ) . '</h2>' : '' )
		. ( '' !== $rows ? '<ul class="t5-contact__rows">' . $rows . '</ul>' : '' )
		. ( '' !== $steps ? '<h2 class="t5-contact__h2">' . esc_html( $t( 't5c_steps_heading' ) ) . '</h2><ol class="t5-contact__steps">' . $steps . '</ol>' : '' )
		. $map
		. '</div>';

	// Form — the hero's runtime mount (leads.php swaps it for the live form on
	// every page view; the nonce stays fresh and submissions go to Quote Requests).
	$payload = bin2hex(
		wp_json_encode(
			array(
				'heading'   => $t( 't5c_form_heading' ),
				'recipient' => $t( 't5c_form_recipient' ),
				'button'    => $t( 't5c_form_button' ),
				'success'   => $t( 't5c_form_success' ),
			)
		)
	);
	$form = '<div class="t5-contact__formcol"><div class="t5-hero__form-mount" data-t5-form="' . esc_attr( $payload ) . '"></div></div>';

	return '<section class="t5-contact">'
		. $header
		. '<div class="t5-contact__inner">' . $details . $form . '</div>'
		. '</section>';
}

function triple5_contact_builder_setup() {
	return cs_compose_controls(
		array(
			'controls'    => array(
				array(
					'type'     => 'group',
					'group'    => 't5-contact:header',
					'controls' => array(
						array( 'key' => 't5c_eyebrow', 'type' => 'text', 'label' => 'Eyebrow' ),
						array( 'key' => 't5c_heading', 'type' => 'text', 'label' => 'Heading (H1)' ),
						array( 'key' => 't5c_subtext', 'type' => 'textarea', 'label' => 'Subtext', 'options' => array( 'height' => 3 ) ),
					),
				),
				array(
					'type'     => 'group',
					'group'    => 't5-contact:details',
					'controls' => array(
						array( 'key' => 't5c_details_heading', 'type' => 'text', 'label' => 'Details heading' ),
						array( 'key' => 't5c_phone', 'type' => 'text', 'label' => 'Phone (tap-to-call)' ),
						array( 'key' => 't5c_email', 'type' => 'text', 'label' => 'Email (mailto)' ),
						array( 'key' => 't5c_location', 'type' => 'text', 'label' => 'Location line' ),
						array( 'key' => 't5c_hours', 'type' => 'text', 'label' => 'Hours line' ),
						array( 'key' => 't5c_steps_heading', 'type' => 'text', 'label' => 'Steps heading' ),
						array( 'key' => 't5c_steps', 'type' => 'textarea', 'label' => 'Steps — one per line (numbered)', 'options' => array( 'height' => 4 ) ),
						array( 'key' => 't5c_show_map', 'type' => 'toggle', 'label' => 'Show map / photo slot' ),
						array(
							'keys'  => array( 'img_source' => 't5c_map_src', 'is_retina' => 't5c_map_retina', 'width' => 't5c_map_width', 'height' => 't5c_map_height' ),
							'type'  => 'image',
							'title' => 'Map or photo (blueprint placeholder until set)',
						),
						array( 'key' => 't5c_map_caption', 'type' => 'text', 'label' => 'Map caption / alt text' ),
						array( 'key' => 't5c_map_url', 'type' => 'text', 'label' => 'Map link (e.g. Google Maps, optional)' ),
					),
				),
				array(
					'type'     => 'group',
					'group'    => 't5-contact:form',
					'controls' => array(
						array( 'key' => 't5c_form_heading', 'type' => 'textarea', 'label' => 'Form heading', 'options' => array( 'height' => 2 ) ),
						array( 'key' => 't5c_form_recipient', 'type' => 'text', 'label' => 'Send notifications to (email; blank = site admin email)' ),
						array( 'key' => 't5c_form_button', 'type' => 'text', 'label' => 'Button text' ),
						array( 'key' => 't5c_form_success', 'type' => 'textarea', 'label' => 'Success message', 'options' => array( 'height' => 2 ) ),
					),
				),
			),
			'control_nav' => array(
				't5-contact'         => 'Contact',
				't5-contact:header'  => 'Page header',
				't5-contact:details' => 'Contact details',
				't5-contact:form'    => 'Form',
			),
		)
	);
}

cs_register_element(
	't5-contact',
	array(
		'title'   => 'Triple 5 Contact',
		'values'  => $triple5_contact_values,
		'builder' => 'triple5_contact_builder_setup',
		'render'  => 'triple5_contact_render',
		'icon'    => 'native',
		'group'   => 'layout',
	)
);
