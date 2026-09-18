<?php
/**
 * Triple 5 Why Choose Us — Cornerstone element definition.
 *
 * A dark full-bleed band with a centered uppercase heading and up to 4 "reason"
 * columns, each a red ring icon (Lucide), an uppercase title and a short body.
 * Columns with an empty title are skipped, so a placement can show 2–4 reasons;
 * the row stays evenly distributed.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Reason slots and their Triple 5 defaults: [icon, title, body]. */
function triple5_why_reason_defaults() {
	return array(
		1 => array( 'phone-call', 'Get in touch today', "Call or message us and we'll walk your project and give you a straight, honest ballpark." ),
		2 => array( 'clipboard-check', 'Free on-site estimates', 'We visit, take precise measurements, discuss options and provide a clear final quote.' ),
		3 => array( 'house', 'One crew, start to finish', 'From first estimate to final inspection — no subcontractor handoffs, ever.' ),
		4 => array( 'shield-check', 'Licensed WA & ID', 'Fully licensed and insured in both states, committed locally to both markets.' ),
	);
}

/** Lucide icon library available to reasons (24×24, stroke-based). */
function triple5_why_icons() {
	return array(
		'phone-call'      => array( 'Phone call', '<path d="M13 2a9 9 0 0 1 9 9"/><path d="M13 6a5 5 0 0 1 5 5"/><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/>' ),
		'clipboard-check' => array( 'Clipboard check', '<rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/>' ),
		'house'           => array( 'House', '<path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>' ),
		'shield-check'    => array( 'Shield check', '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>' ),
		'badge-check'     => array( 'Badge check', '<path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/>' ),
		'wrench'          => array( 'Wrench', '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>' ),
		'hard-hat'        => array( 'Hard hat', '<path d="M10 10V5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5"/><path d="M14 6a6 6 0 0 1 6 6v3"/><path d="M4 15v-3a6 6 0 0 1 6-6"/><rect x="2" y="15" width="20" height="4" rx="1"/>' ),
		'clock'           => array( 'Clock', '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>' ),
		'map-pin'         => array( 'Map pin', '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>' ),
		'snowflake'       => array( 'Snowflake', '<line x1="2" x2="22" y1="12" y2="12"/><line x1="12" x2="12" y1="2" y2="22"/><path d="m20 16-4-4 4-4"/><path d="m4 8 4 4-4 4"/><path d="m16 4-4 4-4-4"/><path d="m8 20 4-4 4 4"/>' ),
		'star'            => array( 'Star', '<path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/>' ),
		'handshake'       => array( 'Handshake', '<path d="m11 17 2 2a1 1 0 1 0 3-3"/><path d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.88-3.88a3 3 0 0 0-4.24 0l-.88.88a1 1 0 1 1-3-3l2.81-2.81a5.79 5.79 0 0 1 7.06-.87l.47.28a2 2 0 0 0 1.42.25L21 4"/><path d="m21 3 1 11h-2"/><path d="M3 3 2 14l6.5 6.5a1 1 0 1 0 3-3"/><path d="M3 4h8"/>' ),
	);
}

/** Inline SVG for a named icon (falls back to "shield-check"). */
function triple5_why_icon_svg( $name ) {
	$icons = triple5_why_icons();
	$paths = isset( $icons[ $name ] ) ? $icons[ $name ][1] : $icons['shield-check'][1];
	return '<svg class="t5-why__icon" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
}

// -----------------------------------------------------------------------------
// Values (defaults)
// -----------------------------------------------------------------------------

$triple5_why_vals = array(
	't5w_heading' => cs_value( 'Why choose us?', 'markup', true ),
);
foreach ( triple5_why_reason_defaults() as $i => $r ) {
	$triple5_why_vals[ "t5w_reason{$i}_icon" ]  = cs_value( $r[0], 'markup', true );
	$triple5_why_vals[ "t5w_reason{$i}_title" ] = cs_value( $r[1], 'markup', true );
	$triple5_why_vals[ "t5w_reason{$i}_body" ]  = cs_value( $r[2], 'markup', true );
}
$triple5_why_values = cs_compose_values( $triple5_why_vals );

// -----------------------------------------------------------------------------
// Render
// -----------------------------------------------------------------------------

function triple5_why_render( $data ) {
	$heading = trim( (string) $data['t5w_heading'] );

	$reasons = '';
	foreach ( array_keys( triple5_why_reason_defaults() ) as $i ) {
		$title = isset( $data[ "t5w_reason{$i}_title" ] ) ? trim( (string) $data[ "t5w_reason{$i}_title" ] ) : '';
		if ( '' === $title ) {
			continue;
		}
		$icon = isset( $data[ "t5w_reason{$i}_icon" ] ) ? (string) $data[ "t5w_reason{$i}_icon" ] : 'shield-check';
		$body = isset( $data[ "t5w_reason{$i}_body" ] ) ? trim( (string) $data[ "t5w_reason{$i}_body" ] ) : '';

		$reasons .= '<div class="t5-why__reason">'
			. '<span class="t5-why__ring">' . triple5_why_icon_svg( $icon ) . '</span>'
			. '<h3 class="t5-why__title">' . esc_html( $title ) . '</h3>'
			. ( '' !== $body ? '<p class="t5-why__body">' . esc_html( $body ) . '</p>' : '' )
			. '</div>';
	}

	return '<section class="t5-why">'
		. '<div class="t5-why__inner">'
		. ( '' !== $heading ? '<h2 class="t5-why__heading">' . esc_html( $heading ) . '</h2>' : '' )
		. '<div class="t5-why__grid">' . $reasons . '</div>'
		. '</div></section>';
}

// -----------------------------------------------------------------------------
// Builder controls
// -----------------------------------------------------------------------------

function triple5_why_builder_setup() {
	$icon_choices = array();
	foreach ( triple5_why_icons() as $key => $icon ) {
		$icon_choices[] = array( 'value' => $key, 'label' => $icon[0] );
	}

	$groups = array(
		array(
			'type'     => 'group',
			'group'    => 't5-why:header',
			'controls' => array(
				array( 'key' => 't5w_heading', 'type' => 'text', 'label' => 'Heading' ),
			),
		),
	);

	$nav = array(
		't5-why'        => 'Why Choose Us',
		't5-why:header' => 'Heading',
	);

	foreach ( array_keys( triple5_why_reason_defaults() ) as $i ) {
		$group_key         = "t5-why:reason{$i}";
		$nav[ $group_key ] = "Reason {$i}";
		$groups[]          = array(
			'type'     => 'group',
			'group'    => $group_key,
			'controls' => array(
				array( 'key' => "t5w_reason{$i}_title", 'type' => 'text', 'label' => "Reason {$i} title (empty = hidden)" ),
				array(
					'key'     => "t5w_reason{$i}_body",
					'type'    => 'textarea',
					'label'   => 'Body',
					'options' => array( 'height' => 3 ),
				),
				array(
					'key'     => "t5w_reason{$i}_icon",
					'type'    => 'select',
					'label'   => 'Icon',
					'options' => array( 'choices' => $icon_choices ),
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
	't5-why',
	array(
		'title'   => 'Triple 5 Why Choose Us',
		'values'  => $triple5_why_values,
		'builder' => 'triple5_why_builder_setup',
		'render'  => 'triple5_why_render',
		'icon'    => 'native',
		'group'   => 'layout',
	)
);
