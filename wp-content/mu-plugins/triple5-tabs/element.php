<?php
/**
 * Triple 5 Service Tabs — Cornerstone element definition.
 *
 * A stone page header (eyebrow, display heading, subtext) followed by a tab bar
 * and one panel per service line (up to 6). Each panel: optional red pill badge,
 * heading, body, checklist (one item per line, two columns), pill CTA with a
 * circled arrow, and an image with a blueprint-grid placeholder + caption.
 *
 * Tabs are plain anchors + panels; tabs.js switches them (ARIA tablist). Without
 * JS every panel simply stacks, so the content is always reachable and the markup
 * needs nothing that Cornerstone's bake would strip. Tabs with an empty label are
 * skipped.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Tab slots and their Triple 5 defaults: [label, badge, heading, body, checklist, cta, image caption]. */
function triple5_tabs_defaults() {
	return array(
		1 => array(
			'Roofing',
			'Metal specialty',
			'Metal roofing',
			'Our flagship. Standing seam, corrugated and metal shingle systems in steel, aluminum and copper — engineered for Inland Northwest snow load, freeze-thaw cycling and wildfire exposure.',
			"Standing seam (concealed fastener)\nCorrugated & exposed-fastener panel\nMetal shingle & stone-coated\nSteel, aluminum & copper",
			'Request a metal roofing quote',
			'Metal Roofing — completed project',
		),
		2 => array(
			'Siding',
			'Metal specialty',
			'Metal siding',
			'Vertical, horizontal and board-and-batten metal panel siding, color-matched to your roof. Low maintenance, fire-resistant and built for our climate swings.',
			"Board-and-batten & vertical panel\nHorizontal lap & flush panel\nTrim, flashing & soffit\nColor-matched to your roof",
			'Request a siding quote',
			'Metal Siding — completed project',
		),
		3 => array(
			'Remodeling',
			'',
			'Remodeling & additions',
			'Exterior and structural remodeling handled by the same licensed crew — additions, shop conversions, porches and the framing behind them.',
			"Additions & bump-outs\nShop & garage conversions\nPorches, decks & covers\nExterior refresh packages",
			'Talk to us about a remodel',
			'Remodeling — completed project',
		),
		4 => array(
			'Concrete',
			'',
			'Concrete',
			'Driveways, patios, foundations and flatwork poured to spec, with drainage and frost depth planned for the Inland Northwest.',
			"Driveways & approaches\nPatios & walkways\nFoundations & footings\nShop & barn slabs",
			'Request a concrete quote',
			'Concrete — completed project',
		),
		5 => array(
			'Framing',
			'',
			'Framing',
			'Residential and light commercial framing for new builds and additions — square, plumb and ready for the trades that follow.',
			"New-build & addition framing\nPost-frame & pole buildings\nRoof & floor systems\nStructural repairs",
			'Request a framing quote',
			'Framing — completed project',
		),
		6 => array( '', '', '', '', '', '', '' ),
	);
}

// -----------------------------------------------------------------------------
// Values (defaults)
// -----------------------------------------------------------------------------

$triple5_tabs_vals = array(
	't5t_show_header' => cs_value( true, 'markup', true ),
	't5t_eyebrow'     => cs_value( 'Our services', 'markup', true ),
	't5t_heading'     => cs_value( 'From the roof down to the foundation', 'markup', true ),
	't5t_subtext'     => cs_value( 'Five service lines, one licensed crew. Metal roofing and siding is our specialty — the rest keeps your whole project under one roof.', 'markup', true ),
	't5t_cta_url'     => cs_value( '/contact-us/', 'markup', true ),
);
foreach ( triple5_tabs_defaults() as $i => $t ) {
	$triple5_tabs_vals[ "t5t_tab{$i}_label" ]        = cs_value( $t[0], 'markup', true );
	$triple5_tabs_vals[ "t5t_tab{$i}_badge" ]        = cs_value( $t[1], 'markup', true );
	$triple5_tabs_vals[ "t5t_tab{$i}_heading" ]      = cs_value( $t[2], 'markup', true );
	$triple5_tabs_vals[ "t5t_tab{$i}_body" ]         = cs_value( $t[3], 'markup', true );
	$triple5_tabs_vals[ "t5t_tab{$i}_list" ]         = cs_value( $t[4], 'markup', true );
	$triple5_tabs_vals[ "t5t_tab{$i}_cta" ]          = cs_value( $t[5], 'markup', true );
	$triple5_tabs_vals[ "t5t_tab{$i}_cta_url" ]      = cs_value( '', 'markup', true );
	$triple5_tabs_vals[ "t5t_tab{$i}_caption" ]      = cs_value( $t[6], 'markup', true );
	$triple5_tabs_vals[ "t5t_tab{$i}_image_src" ]    = cs_value( '', 'markup', true );
	$triple5_tabs_vals[ "t5t_tab{$i}_image_retina" ] = cs_value( true, 'markup', true );
	$triple5_tabs_vals[ "t5t_tab{$i}_image_width" ]  = cs_value( '', 'markup', true );
	$triple5_tabs_vals[ "t5t_tab{$i}_image_height" ] = cs_value( '', 'markup', true );
}
$triple5_tabs_values = cs_compose_values( $triple5_tabs_vals );

// -----------------------------------------------------------------------------
// Render
// -----------------------------------------------------------------------------

function triple5_tabs_icon( $name ) {
	switch ( $name ) {
		case 'check':
			return '<svg class="t5-tabs__check" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
		case 'arrow':
			return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>';
		case 'image':
			return '<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>';
	}
	return '';
}

function triple5_tabs_render( $data ) {
	$uid     = 't5tabs-' . substr( md5( wp_json_encode( array( $data['t5t_heading'], $data['t5t_tab1_label'] ) ) ), 0, 6 );
	$default = trim( (string) $data['t5t_cta_url'] );

	$header = '';
	if ( ! empty( $data['t5t_show_header'] ) ) {
		$eyebrow = trim( (string) $data['t5t_eyebrow'] );
		$heading = trim( (string) $data['t5t_heading'] );
		$sub     = trim( (string) $data['t5t_subtext'] );
		$header  = '<header class="t5-tabs__header"><div class="t5-tabs__header-inner">'
			. ( '' !== $eyebrow ? '<p class="t5-tabs__eyebrow">' . esc_html( $eyebrow ) . '</p>' : '' )
			. ( '' !== $heading ? '<h1 class="t5-tabs__title">' . esc_html( $heading ) . '</h1>' : '' )
			. ( '' !== $sub ? '<p class="t5-tabs__sub">' . esc_html( $sub ) . '</p>' : '' )
			. '</div></header>';
	}

	$tabs   = '';
	$panels = '';
	$n      = 0;
	foreach ( array_keys( triple5_tabs_defaults() ) as $i ) {
		$label = trim( (string) ( $data[ "t5t_tab{$i}_label" ] ?? '' ) );
		if ( '' === $label ) {
			continue;
		}
		$n++;
		$active   = ( 1 === $n );
		$panel_id = "{$uid}-panel-{$i}";
		$tab_id   = "{$uid}-tab-{$i}";

		$tabs .= '<a class="t5-tabs__tab' . ( $active ? ' is-active' : '' ) . '" id="' . esc_attr( $tab_id ) . '" href="#' . esc_attr( $panel_id ) . '" role="tab" aria-controls="' . esc_attr( $panel_id ) . '" aria-selected="' . ( $active ? 'true' : 'false' ) . '" tabindex="' . ( $active ? '0' : '-1' ) . '">' . esc_html( $label ) . '</a>';

		$badge   = trim( (string) ( $data[ "t5t_tab{$i}_badge" ] ?? '' ) );
		$heading = trim( (string) ( $data[ "t5t_tab{$i}_heading" ] ?? '' ) );
		$body    = trim( (string) ( $data[ "t5t_tab{$i}_body" ] ?? '' ) );
		$cta     = trim( (string) ( $data[ "t5t_tab{$i}_cta" ] ?? '' ) );
		$cta_url = trim( (string) ( $data[ "t5t_tab{$i}_cta_url" ] ?? '' ) );
		$caption = trim( (string) ( $data[ "t5t_tab{$i}_caption" ] ?? '' ) );
		$img     = trim( (string) ( $data[ "t5t_tab{$i}_image_src" ] ?? '' ) );

		$items = '';
		foreach ( preg_split( '/\r\n|\r|\n/', (string) ( $data[ "t5t_tab{$i}_list" ] ?? '' ) ) as $line ) {
			$line = trim( $line );
			if ( '' !== $line ) {
				$items .= '<li>' . triple5_tabs_icon( 'check' ) . '<span>' . esc_html( $line ) . '</span></li>';
			}
		}

		if ( '' !== $img ) {
			$media = '<figure class="t5-tabs__media has-image"><img src="' . esc_url( $img ) . '" alt="' . esc_attr( '' !== $caption ? $caption : $heading ) . '" loading="lazy">'
				. ( '' !== $caption ? '<figcaption class="t5-tabs__caption">' . esc_html( $caption ) . '</figcaption>' : '' )
				. '</figure>';
		} else {
			$media = '<div class="t5-tabs__media is-blueprint">' . triple5_tabs_icon( 'image' )
				. ( '' !== $caption ? '<span class="t5-tabs__media-label">' . esc_html( $caption ) . '</span>' : '' )
				. '</div>';
		}

		$href    = '' !== $cta_url ? $cta_url : ( '' !== $default ? $default : '#' );
		$panels .= '<div class="t5-tabs__panel' . ( $active ? ' is-active' : '' ) . '" id="' . esc_attr( $panel_id ) . '" role="tabpanel" aria-labelledby="' . esc_attr( $tab_id ) . '">'
			. '<div class="t5-tabs__content">'
			. ( '' !== $badge ? '<span class="t5-tabs__badge">' . esc_html( $badge ) . '</span>' : '' )
			. ( '' !== $heading ? '<h2 class="t5-tabs__heading">' . esc_html( $heading ) . '</h2>' : '' )
			. ( '' !== $body ? '<p class="t5-tabs__body">' . esc_html( $body ) . '</p>' : '' )
			. ( '' !== $items ? '<ul class="t5-tabs__list">' . $items . '</ul>' : '' )
			. ( '' !== $cta ? '<a class="t5-tabs__cta" href="' . esc_url( $href ) . '"><span>' . esc_html( $cta ) . '</span><span class="t5-tabs__cta-arrow">' . triple5_tabs_icon( 'arrow' ) . '</span></a>' : '' )
			. '</div>'
			. $media
			. '</div>';
	}

	return '<section class="t5-tabs" data-t5-tabs>'
		. $header
		. '<div class="t5-tabs__inner">'
		. '<nav class="t5-tabs__bar" role="tablist" aria-label="Services">' . $tabs . '</nav>'
		. $panels
		. '</div></section>';
}

// -----------------------------------------------------------------------------
// Builder controls
// -----------------------------------------------------------------------------

function triple5_tabs_builder_setup() {
	$groups = array(
		array(
			'type'     => 'group',
			'group'    => 't5-tabs:header',
			'controls' => array(
				array( 'key' => 't5t_show_header', 'type' => 'toggle', 'label' => 'Show page header band' ),
				array( 'key' => 't5t_eyebrow', 'type' => 'text', 'label' => 'Eyebrow' ),
				array( 'key' => 't5t_heading', 'type' => 'text', 'label' => 'Heading (H1)' ),
				array(
					'key'     => 't5t_subtext',
					'type'    => 'textarea',
					'label'   => 'Subtext',
					'options' => array( 'height' => 3 ),
				),
				array( 'key' => 't5t_cta_url', 'type' => 'text', 'label' => 'Default CTA link (used when a tab has none)' ),
			),
		),
	);

	$nav = array(
		't5-tabs'        => 'Service Tabs',
		't5-tabs:header' => 'Page header',
	);

	foreach ( array_keys( triple5_tabs_defaults() ) as $i ) {
		$group_key         = "t5-tabs:tab{$i}";
		$nav[ $group_key ] = "Tab {$i}";
		$groups[]          = array(
			'type'     => 'group',
			'group'    => $group_key,
			'controls' => array(
				array( 'key' => "t5t_tab{$i}_label", 'type' => 'text', 'label' => "Tab {$i} label (empty = hidden)" ),
				array( 'key' => "t5t_tab{$i}_badge", 'type' => 'text', 'label' => 'Pill badge (optional, e.g. Metal specialty)' ),
				array( 'key' => "t5t_tab{$i}_heading", 'type' => 'text', 'label' => 'Heading' ),
				array(
					'key'     => "t5t_tab{$i}_body",
					'type'    => 'textarea',
					'label'   => 'Body',
					'options' => array( 'height' => 4 ),
				),
				array(
					'key'     => "t5t_tab{$i}_list",
					'type'    => 'textarea',
					'label'   => 'Checklist (one item per line)',
					'options' => array( 'height' => 4 ),
				),
				array( 'key' => "t5t_tab{$i}_cta", 'type' => 'text', 'label' => 'Button text (empty = hidden)' ),
				array( 'key' => "t5t_tab{$i}_cta_url", 'type' => 'text', 'label' => 'Button link (blank = default CTA link)' ),
				array(
					'keys'  => array(
						'img_source' => "t5t_tab{$i}_image_src",
						'is_retina'  => "t5t_tab{$i}_image_retina",
						'width'      => "t5t_tab{$i}_image_width",
						'height'     => "t5t_tab{$i}_image_height",
					),
					'type'  => 'image',
					'title' => 'Image (blueprint placeholder until set)',
				),
				array( 'key' => "t5t_tab{$i}_caption", 'type' => 'text', 'label' => 'Image caption / alt text' ),
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
	't5-tabs',
	array(
		'title'   => 'Triple 5 Service Tabs',
		'values'  => $triple5_tabs_values,
		'builder' => 'triple5_tabs_builder_setup',
		'render'  => 'triple5_tabs_render',
		'icon'    => 'native',
		'group'   => 'layout',
	)
);
