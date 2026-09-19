<?php
/**
 * Triple 5 Logo Strip — Cornerstone element definition.
 *
 * A horizontal band of partner / manufacturer logos. Optional heading, a background
 * tone (navy / coal / stone), and up to 8 logo slots — each a real logo image (or an
 * icon + name placeholder), with an optional link. Empty-name slots are skipped.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Number of logo slots and their placeholder names (generic — replace with real). */
function triple5_logos_defaults() {
	return array( 1 => 'Manufacturer', 2 => 'Supplier', 3 => 'Distributor', 4 => 'Association', 5 => 'Certification', 6 => 'Partner', 7 => '', 8 => '' );
}

// -----------------------------------------------------------------------------
// Values
// -----------------------------------------------------------------------------

$triple5_logos_vals = array(
	't5l_show_heading' => cs_value( true, 'markup', true ),
	't5l_heading'      => cs_value( 'Trusted materials & manufacturers', 'markup', true ),
	't5l_bg'           => cs_value( 'navy', 'markup', true ),
	't5l_logo_style'   => cs_value( 'mono', 'markup', true ),
);
foreach ( triple5_logos_defaults() as $i => $name ) {
	$triple5_logos_vals[ "t5l_item{$i}_name" ]         = cs_value( $name, 'markup', true );
	$triple5_logos_vals[ "t5l_item{$i}_url" ]          = cs_value( '', 'markup', true );
	$triple5_logos_vals[ "t5l_item{$i}_image_src" ]    = cs_value( '', 'markup', true );
	$triple5_logos_vals[ "t5l_item{$i}_image_retina" ] = cs_value( true, 'markup', true );
	$triple5_logos_vals[ "t5l_item{$i}_image_width" ]  = cs_value( '', 'markup', true );
	$triple5_logos_vals[ "t5l_item{$i}_image_height" ] = cs_value( '', 'markup', true );
}
$triple5_logos_values = cs_compose_values( $triple5_logos_vals );

// -----------------------------------------------------------------------------
// Render
// -----------------------------------------------------------------------------

/** Lucide "shield-check" placeholder mark for logo slots without an image. */
function triple5_logos_mark() {
	return '<svg class="t5-logos__mark" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg>';
}

function triple5_logos_render( $data ) {
	$bg    = in_array( $data['t5l_bg'], array( 'navy', 'coal', 'stone' ), true ) ? $data['t5l_bg'] : 'navy';
	$style = ( isset( $data['t5l_logo_style'] ) && 'original' === $data['t5l_logo_style'] ) ? 'original' : 'mono';

	$heading = '';
	if ( ! empty( $data['t5l_show_heading'] ) && '' !== trim( (string) $data['t5l_heading'] ) ) {
		$heading = '<p class="t5-logos__heading">' . esc_html( $data['t5l_heading'] ) . '</p>';
	}

	$items = '';
	foreach ( array_keys( triple5_logos_defaults() ) as $i ) {
		$name = isset( $data[ "t5l_item{$i}_name" ] ) ? trim( (string) $data[ "t5l_item{$i}_name" ] ) : '';
		$img  = isset( $data[ "t5l_item{$i}_image_src" ] ) ? triple5_image_url( $data[ "t5l_item{$i}_image_src" ] ) : '';
		if ( '' === $name && '' === $img ) {
			continue;
		}

		if ( '' !== $img ) {
			$inner = '<img class="t5-logos__img is-' . esc_attr( $style ) . '" src="' . esc_url( $img ) . '" alt="' . esc_attr( $name ) . '">';
		} else {
			$inner = triple5_logos_mark() . '<span class="t5-logos__name">' . esc_html( $name ) . '</span>';
		}

		$url = isset( $data[ "t5l_item{$i}_url" ] ) ? trim( (string) $data[ "t5l_item{$i}_url" ] ) : '';
		if ( '' !== $url ) {
			$items .= '<a class="t5-logos__item" href="' . esc_url( $url ) . '">' . $inner . '</a>';
		} else {
			$items .= '<div class="t5-logos__item">' . $inner . '</div>';
		}
	}

	return '<section class="t5-logos is-bg-' . esc_attr( $bg ) . '">'
		. '<div class="t5-logos__inner">'
		. $heading
		. '<div class="t5-logos__row">' . $items . '</div>'
		. '</div></section>';
}

// -----------------------------------------------------------------------------
// Builder controls
// -----------------------------------------------------------------------------

function triple5_logos_builder_setup() {
	$setup = array(
		'type'     => 'group',
		'group'    => 't5-logos:setup',
		'controls' => array(
			array( 'key' => 't5l_show_heading', 'type' => 'toggle', 'label' => 'Show heading' ),
			array(
				'key'       => 't5l_heading',
				'type'      => 'text',
				'label'     => 'Heading',
				'condition' => array( 't5l_show_heading' => true ),
			),
			array(
				'key'     => 't5l_bg',
				'type'    => 'choose',
				'label'   => 'Band background',
				'options' => array(
					'choices' => array(
						array( 'value' => 'navy', 'label' => 'Charcoal' ),
						array( 'value' => 'coal', 'label' => 'Coal' ),
						array( 'value' => 'stone', 'label' => 'Stone (light)' ),
					),
				),
			),
			array(
				'key'     => 't5l_logo_style',
				'type'    => 'choose',
				'label'   => 'Logo image treatment',
				'options' => array(
					'choices' => array(
						array( 'value' => 'mono', 'label' => 'Mono (white)' ),
						array( 'value' => 'original', 'label' => 'Original' ),
					),
				),
			),
		),
	);

	$groups = array( $setup );
	$nav    = array(
		't5-logos'       => 'Logos',
		't5-logos:setup' => 'Setup',
	);

	foreach ( array_keys( triple5_logos_defaults() ) as $i ) {
		$gk          = "t5-logos:item{$i}";
		$nav[ $gk ]  = "Logo {$i}";
		$groups[]    = array(
			'type'     => 'group',
			'group'    => $gk,
			'controls' => array(
				array( 'key' => "t5l_item{$i}_name", 'type' => 'text', 'label' => "Logo {$i} name (empty = hidden)" ),
				array( 'key' => "t5l_item{$i}_url", 'type' => 'text', 'label' => 'Link (URL, optional)' ),
				array(
					'keys'  => array(
						'img_source' => "t5l_item{$i}_image_src",
						'is_retina'  => "t5l_item{$i}_image_retina",
						'width'      => "t5l_item{$i}_image_width",
						'height'     => "t5l_item{$i}_image_height",
					),
					'type'  => 'image',
					'title' => 'Logo image (overrides icon + name)',
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
	't5-logos',
	array(
		'title'   => 'Triple 5 Logo Strip',
		'values'  => $triple5_logos_values,
		'builder' => 'triple5_logos_builder_setup',
		'render'  => 'triple5_logos_render',
		'icon'    => 'native',
		'group'   => 'layout',
	)
);
