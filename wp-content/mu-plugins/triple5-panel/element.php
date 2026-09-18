<?php
/**
 * Triple 5 Service Panel — Cornerstone element definition.
 *
 * A dark full-bleed band holding two parts: a red intro card (heading, body copy,
 * dark CTA button) and a 2×3 grid of icon tiles (Lucide icon, title, one-line
 * description). Tiles with an empty title are skipped, so a placement can show
 * 1–6 tiles; the grid keeps three columns and wraps as needed.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Tile slots and their Triple 5 defaults: [icon, title, description]. */
function triple5_panel_tile_defaults() {
	return array(
		1 => array( 'house', 'Metal Roofs', 'Standing seam & corrugated' ),
		2 => array( 'shield-check', 'Siding', 'Insulated & weather-sealed' ),
		3 => array( 'wrench', 'Repairs', 'Leaks, flashing, storm damage' ),
		4 => array( 'droplets', 'Gutters', 'Seamless gutters & guards' ),
		5 => array( 'layers', 'Flat Roofing', 'TPO and modified bitumen' ),
		6 => array( 'badge-check', 'Inspections', 'Free 25-point roof report' ),
	);
}

/**
 * Lucide icon library available to tiles (24×24, stroke-based). Kept small and
 * trade-relevant; add paths here to expose more choices in the builder.
 */
function triple5_panel_icons() {
	return array(
		'house'        => array( 'House', '<path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>' ),
		'shield-check' => array( 'Shield check', '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>' ),
		'wrench'       => array( 'Wrench', '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>' ),
		'droplets'     => array( 'Droplets', '<path d="M7 16.3c2.2 0 4-1.83 4-4.05 0-1.16-.57-2.26-1.71-3.19S7.29 6.75 7 5.3c-.29 1.45-1.14 2.84-2.29 3.76S3 11.1 3 12.25c0 2.22 1.8 4.05 4 4.05z"/><path d="M12.56 6.6A10.97 10.97 0 0 0 14 3.02c.5 2.5 2 4.9 4 6.5s3 3.5 3 5.5a6.98 6.98 0 0 1-11.91 4.97"/>' ),
		'layers'       => array( 'Layers', '<path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"/><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"/><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"/>' ),
		'badge-check'  => array( 'Badge check', '<path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/>' ),
		'hammer'       => array( 'Hammer', '<path d="m15 12-8.373 8.373a1 1 0 1 1-3-3L12 9"/><path d="m18 15 4-4"/><path d="m21.5 11.5-1.914-1.914A2 2 0 0 1 19 8.172V7l-2.26-2.26a6 6 0 0 0-4.202-1.756L9 2.96l.92.82A6.18 6.18 0 0 1 12 8.4V10l2 2h1.172a2 2 0 0 1 1.414.586L18.5 14.5"/>' ),
		'snowflake'    => array( 'Snowflake', '<line x1="2" x2="22" y1="12" y2="12"/><line x1="12" x2="12" y1="2" y2="22"/><path d="m20 16-4-4 4-4"/><path d="m4 8 4 4-4 4"/><path d="m16 4-4 4-4-4"/><path d="m8 20 4-4 4 4"/>' ),
		'warehouse'    => array( 'Warehouse', '<path d="M22 8.35V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8.35A2 2 0 0 1 3.26 6.5l8-3.2a2 2 0 0 1 1.48 0l8 3.2A2 2 0 0 1 22 8.35Z"/><path d="M6 18h12"/><path d="M6 14h12"/><rect width="12" height="12" x="6" y="10"/>' ),
		'ruler'        => array( 'Ruler', '<path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.41 2.41 0 0 1 0-3.4l2.6-2.6a2.41 2.41 0 0 1 3.4 0Z"/><path d="m14.5 12.5 2-2"/><path d="m11.5 9.5 2-2"/><path d="m8.5 6.5 2-2"/><path d="m17.5 15.5 2-2"/>' ),
		'cloud-rain'   => array( 'Cloud rain', '<path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/><path d="M16 14v6"/><path d="M8 14v6"/><path d="M12 16v6"/>' ),
		'phone'        => array( 'Phone', '<path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/>' ),
	);
}

/** Inline SVG for a named icon (falls back to "house"). */
function triple5_panel_icon_svg( $name ) {
	$icons = triple5_panel_icons();
	$paths = isset( $icons[ $name ] ) ? $icons[ $name ][1] : $icons['house'][1];
	return '<svg class="t5-panel__icon" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
}

// -----------------------------------------------------------------------------
// Values (defaults)
// -----------------------------------------------------------------------------

$triple5_panel_vals = array(
	't5p_heading'  => cs_value( "Roofing and\nInstallation Services", 'markup', true ),
	't5p_body'     => cs_value( 'One licensed crew for the whole envelope of your home — roof, siding, and drainage, scheduled around your life.', 'markup', true ),
	't5p_cta_text' => cs_value( 'Contact', 'markup', true ),
	't5p_cta_url'  => cs_value( '/contact-us/', 'markup', true ),
);
foreach ( triple5_panel_tile_defaults() as $i => $t ) {
	$triple5_panel_vals[ "t5p_tile{$i}_icon" ]  = cs_value( $t[0], 'markup', true );
	$triple5_panel_vals[ "t5p_tile{$i}_title" ] = cs_value( $t[1], 'markup', true );
	$triple5_panel_vals[ "t5p_tile{$i}_desc" ]  = cs_value( $t[2], 'markup', true );
	$triple5_panel_vals[ "t5p_tile{$i}_url" ]   = cs_value( '', 'markup', true );
}
$triple5_panel_values = cs_compose_values( $triple5_panel_vals );

// -----------------------------------------------------------------------------
// Render
// -----------------------------------------------------------------------------

function triple5_panel_render( $data ) {
	$heading  = trim( (string) $data['t5p_heading'] );
	$body     = trim( (string) $data['t5p_body'] );
	$cta_text = trim( (string) $data['t5p_cta_text'] );
	$cta_url  = trim( (string) $data['t5p_cta_url'] );

	$card = '<div class="t5-panel__card">';
	if ( '' !== $heading ) {
		$card .= '<h2 class="t5-panel__heading">' . nl2br( esc_html( $heading ) ) . '</h2>';
	}
	if ( '' !== $body ) {
		$card .= '<p class="t5-panel__body">' . esc_html( $body ) . '</p>';
	}
	if ( '' !== $cta_text ) {
		$card .= '<a class="t5-panel__cta" href="' . esc_url( '' !== $cta_url ? $cta_url : '#' ) . '">' . esc_html( $cta_text ) . '</a>';
	}
	$card .= '</div>';

	// Tiles — skip any with an empty title. A tile with a URL becomes a link.
	$tiles = '';
	foreach ( array_keys( triple5_panel_tile_defaults() ) as $i ) {
		$title = isset( $data[ "t5p_tile{$i}_title" ] ) ? trim( (string) $data[ "t5p_tile{$i}_title" ] ) : '';
		if ( '' === $title ) {
			continue;
		}
		$icon = isset( $data[ "t5p_tile{$i}_icon" ] ) ? (string) $data[ "t5p_tile{$i}_icon" ] : 'house';
		$desc = isset( $data[ "t5p_tile{$i}_desc" ] ) ? trim( (string) $data[ "t5p_tile{$i}_desc" ] ) : '';
		$url  = isset( $data[ "t5p_tile{$i}_url" ] ) ? trim( (string) $data[ "t5p_tile{$i}_url" ] ) : '';

		$inner = triple5_panel_icon_svg( $icon )
			. '<h3 class="t5-panel__title">' . esc_html( $title ) . '</h3>'
			. ( '' !== $desc ? '<p class="t5-panel__desc">' . esc_html( $desc ) . '</p>' : '' );

		$tiles .= '' !== $url
			? '<a class="t5-panel__tile is-link" href="' . esc_url( $url ) . '">' . $inner . '</a>'
			: '<div class="t5-panel__tile">' . $inner . '</div>';
	}

	return '<section class="t5-panel">'
		. '<div class="t5-panel__inner">'
		. $card
		. '<div class="t5-panel__grid">' . $tiles . '</div>'
		. '</div></section>';
}

// -----------------------------------------------------------------------------
// Builder controls
// -----------------------------------------------------------------------------

function triple5_panel_builder_setup() {
	$icon_choices = array();
	foreach ( triple5_panel_icons() as $key => $icon ) {
		$icon_choices[] = array( 'value' => $key, 'label' => $icon[0] );
	}

	$groups = array(
		array(
			'type'     => 'group',
			'group'    => 't5-panel:card',
			'controls' => array(
				array(
					'key'     => 't5p_heading',
					'type'    => 'textarea',
					'label'   => 'Heading (line breaks kept)',
					'options' => array( 'height' => 2 ),
				),
				array(
					'key'     => 't5p_body',
					'type'    => 'textarea',
					'label'   => 'Body',
					'options' => array( 'height' => 4 ),
				),
				array( 'key' => 't5p_cta_text', 'type' => 'text', 'label' => 'Button text (empty = hidden)' ),
				array( 'key' => 't5p_cta_url', 'type' => 'text', 'label' => 'Button link (URL)' ),
			),
		),
	);

	$nav = array(
		't5-panel'      => 'Service Panel',
		't5-panel:card' => 'Intro card',
	);

	foreach ( array_keys( triple5_panel_tile_defaults() ) as $i ) {
		$group_key         = "t5-panel:tile{$i}";
		$nav[ $group_key ] = "Tile {$i}";
		$groups[]          = array(
			'type'     => 'group',
			'group'    => $group_key,
			'controls' => array(
				array( 'key' => "t5p_tile{$i}_title", 'type' => 'text', 'label' => "Tile {$i} title (empty = hidden)" ),
				array( 'key' => "t5p_tile{$i}_desc", 'type' => 'text', 'label' => 'Description' ),
				array(
					'key'     => "t5p_tile{$i}_icon",
					'type'    => 'select',
					'label'   => 'Icon',
					'options' => array( 'choices' => $icon_choices ),
				),
				array( 'key' => "t5p_tile{$i}_url", 'type' => 'text', 'label' => 'Link (optional — makes the tile clickable)' ),
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
	't5-panel',
	array(
		'title'   => 'Triple 5 Service Panel',
		'values'  => $triple5_panel_values,
		'builder' => 'triple5_panel_builder_setup',
		'render'  => 'triple5_panel_render',
		'icon'    => 'native',
		'group'   => 'layout',
	)
);
