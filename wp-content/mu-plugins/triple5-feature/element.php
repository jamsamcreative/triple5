<?php
/**
 * Triple 5 Feature Section — Cornerstone element definition.
 *
 * A reusable two-column image + content block: eyebrow, heading, body, a services
 * checklist (one item per line), an optional experience badge over the image, an
 * optional clay stat card, an optional second paragraph, and an optional CTA. The
 * layout can flip (image left/right) for alternating sections down a page.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -----------------------------------------------------------------------------
// Values (defaults) — adapted to Triple 5. Numbers are honest/on-brand starters
// (no fabricated stats): "2 — States, one standard" and "5★ — Star-rated service".
// -----------------------------------------------------------------------------

$triple5_feature_values = cs_compose_values(
	array(
		't5f_eyebrow'      => cs_value( 'About us', 'markup', true ),
		't5f_heading'      => cs_value( "Built for this climate,\nbacked by one crew", 'markup', true ),
		't5f_body'         => cs_value( 'Safeguard your home with metal roofing and siding engineered for Inland Northwest weather — snow load, freeze-thaw and wildfire exposure. We handle inspections, repairs and full installs with one licensed crew, start to finish.', 'markup', true ),
		't5f_checklist'    => cs_value( "Metal roofing, re-roofs & repairs\nStorm, hail & snow-load damage\nSiding, flashing & gutters", 'markup', true ),

		't5f_body2'        => cs_value( 'One contractor for the whole project — from first estimate to final inspection, no subcontractor handoffs, ever.', 'markup', true ),
		't5f_show_body2'   => cs_value( true, 'markup', true ),

		't5f_image_src'    => cs_value( '', 'markup', true ),
		't5f_image_retina' => cs_value( true, 'markup', true ),
		't5f_image_width'  => cs_value( '', 'markup', true ),
		't5f_image_height' => cs_value( '', 'markup', true ),
		't5f_image_side'   => cs_value( 'left', 'markup', true ),

		't5f_show_badge'   => cs_value( true, 'markup', true ),
		't5f_badge_number' => cs_value( '5★', 'markup', true ),
		't5f_badge_label'  => cs_value( 'Star-rated service, start to finish', 'markup', true ),

		't5f_show_stat'    => cs_value( true, 'markup', true ),
		't5f_stat_number'  => cs_value( '2', 'markup', true ),
		't5f_stat_label'   => cs_value( 'States, one standard', 'markup', true ),

		't5f_show_button'  => cs_value( true, 'markup', true ),
		't5f_button_text'  => cs_value( 'About us', 'markup', true ),
		't5f_button_url'   => cs_value( '', 'markup', true ),
	)
);

// -----------------------------------------------------------------------------
// Render helpers
// -----------------------------------------------------------------------------

/** Lucide "circle-check" icon markup (inherits currentColor). */
function triple5_feature_check_icon() {
	return '<svg class="t5-feature__check" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>';
}

/** Lucide "layers" icon markup for the stat card (inherits currentColor). */
function triple5_feature_stat_icon() {
	return '<svg class="t5-feature__stat-icon" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"></path><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"></path><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"></path></svg>';
}

// -----------------------------------------------------------------------------
// Render
// -----------------------------------------------------------------------------

function triple5_feature_render( $data ) {
	$side       = ( isset( $data['t5f_image_side'] ) && 'right' === $data['t5f_image_side'] ) ? 'right' : 'left';
	$section_cl = 't5-feature is-image-' . $side;

	// --- Image column ---
	$img_src   = isset( $data['t5f_image_src'] ) ? triple5_image_url( $data['t5f_image_src'] ) : '';
	$blueprint = '' === $img_src;
	$media     = '' !== $img_src
		? '<img class="t5-feature__img" src="' . esc_url( $img_src ) . '" alt="">'
		: '<div class="t5-feature__img is-blueprint" role="img" aria-label="Triple 5 job site"></div>';

	$badge = '';
	if ( ! empty( $data['t5f_show_badge'] ) ) {
		$badge = '<div class="t5-feature__badge">'
			. '<span class="t5-feature__badge-num">' . esc_html( $data['t5f_badge_number'] ) . '</span>'
			. '<span class="t5-feature__badge-label">' . esc_html( $data['t5f_badge_label'] ) . '</span>'
			. '</div>';
	}

	$image_col = '<div class="t5-feature__media">'
		. '<div class="t5-feature__frame">' . $media . $badge . '</div>'
		. '</div>';

	// --- Content column ---
	$eyebrow = '';
	if ( '' !== trim( (string) $data['t5f_eyebrow'] ) ) {
		$eyebrow = '<p class="t5-feature__eyebrow"><span class="t5-feature__slash">//</span> '
			. esc_html( $data['t5f_eyebrow'] )
			. ' <span class="t5-feature__slash">//</span></p>';
	}

	$heading = '<h2 class="t5-feature__heading">' . nl2br( esc_html( $data['t5f_heading'] ) ) . '</h2>';

	$body = '' !== trim( (string) $data['t5f_body'] )
		? '<p class="t5-feature__body">' . esc_html( $data['t5f_body'] ) . '</p>'
		: '';

	// Checklist — one item per line.
	$items = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $data['t5f_checklist'] ) ), 'strlen' );
	$list  = '';
	if ( $items ) {
		$list = '<ul class="t5-feature__list">';
		foreach ( $items as $item ) {
			$list .= '<li>' . triple5_feature_check_icon() . '<span>' . esc_html( $item ) . '</span></li>';
		}
		$list .= '</ul>';
	}

	// Stat card.
	$stat = '';
	if ( ! empty( $data['t5f_show_stat'] ) ) {
		$stat = '<div class="t5-feature__stat">'
			. triple5_feature_stat_icon()
			. '<div class="t5-feature__stat-body">'
			. '<span class="t5-feature__stat-num">' . esc_html( $data['t5f_stat_number'] ) . '</span>'
			. '<span class="t5-feature__stat-label">' . esc_html( $data['t5f_stat_label'] ) . '</span>'
			. '</div></div>';
	}

	$midrow = '';
	if ( '' !== $list || '' !== $stat ) {
		$midrow = '<div class="t5-feature__midrow">'
			. ( '' !== $list ? '<div class="t5-feature__midcol">' . $list . '</div>' : '' )
			. ( '' !== $stat ? $stat : '' )
			. '</div>';
	}

	$body2 = '';
	if ( ! empty( $data['t5f_show_body2'] ) && '' !== trim( (string) $data['t5f_body2'] ) ) {
		$body2 = '<p class="t5-feature__body2">' . esc_html( $data['t5f_body2'] ) . '</p>';
	}

	$button = '';
	if ( ! empty( $data['t5f_show_button'] ) && '' !== trim( (string) $data['t5f_button_text'] ) ) {
		$url     = trim( (string) $data['t5f_button_url'] );
		$href    = '' !== $url ? esc_url( $url ) : '#';
		$chevron = '<svg class="t5-feature__chev" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 17 5-5-5-5"></path><path d="m13 17 5-5-5-5"></path></svg>';
		$button  = '<a class="t5-feature__btn" href="' . $href . '"><span>' . esc_html( $data['t5f_button_text'] ) . '</span>' . $chevron . '</a>';
	}

	$content_col = '<div class="t5-feature__content">'
		. $eyebrow . $heading . $body . $midrow . $body2 . $button
		. '</div>';

	// Assemble — DOM order is image then content; CSS `is-image-right` reverses columns.
	return '<section class="' . esc_attr( $section_cl ) . '">'
		. '<div class="t5-feature__inner">'
		. $image_col . $content_col
		. '</div></section>';
}

// -----------------------------------------------------------------------------
// Builder controls
// -----------------------------------------------------------------------------

function triple5_feature_builder_setup() {
	$content = array(
		'type'     => 'group',
		'group'    => 't5-feature:content',
		'controls' => array(
			array( 'key' => 't5f_eyebrow', 'type' => 'text', 'label' => 'Eyebrow' ),
			array(
				'key'     => 't5f_heading',
				'type'    => 'textarea',
				'label'   => 'Heading',
				'options' => array( 'height' => 2 ),
			),
			array(
				'key'     => 't5f_body',
				'type'    => 'textarea',
				'label'   => 'Body paragraph',
				'options' => array( 'height' => 4 ),
			),
			array(
				'key'     => 't5f_checklist',
				'type'    => 'textarea',
				'label'   => 'Checklist (one item per line)',
				'options' => array( 'height' => 4 ),
			),
		),
	);

	$second = array(
		'type'     => 'group',
		'group'    => 't5-feature:second',
		'controls' => array(
			array( 'key' => 't5f_show_body2', 'type' => 'toggle', 'label' => 'Show second paragraph' ),
			array(
				'key'       => 't5f_body2',
				'type'      => 'textarea',
				'label'     => 'Second paragraph',
				'options'   => array( 'height' => 3 ),
				'condition' => array( 't5f_show_body2' => true ),
			),
			array( 'key' => 't5f_show_button', 'type' => 'toggle', 'label' => 'Show button' ),
			array(
				'key'       => 't5f_button_text',
				'type'      => 'text',
				'label'     => 'Button text',
				'condition' => array( 't5f_show_button' => true ),
			),
			array(
				'key'       => 't5f_button_url',
				'type'      => 'text',
				'label'     => 'Button link (URL)',
				'condition' => array( 't5f_show_button' => true ),
			),
		),
	);

	$image = array(
		'type'     => 'group',
		'group'    => 't5-feature:image',
		'controls' => array(
			array(
				'keys'  => array(
					'img_source' => 't5f_image_src',
					'is_retina'  => 't5f_image_retina',
					'width'      => 't5f_image_width',
					'height'     => 't5f_image_height',
				),
				'type'  => 'image',
				'title' => 'Image',
			),
			array(
				'key'     => 't5f_image_side',
				'type'    => 'choose',
				'label'   => 'Image side',
				'options' => array(
					'choices' => array(
						array( 'value' => 'left', 'label' => 'Left' ),
						array( 'value' => 'right', 'label' => 'Right' ),
					),
				),
			),
			array( 'key' => 't5f_show_badge', 'type' => 'toggle', 'label' => 'Show experience badge' ),
			array(
				'key'       => 't5f_badge_number',
				'type'      => 'text',
				'label'     => 'Badge number/value',
				'condition' => array( 't5f_show_badge' => true ),
			),
			array(
				'key'       => 't5f_badge_label',
				'type'      => 'text',
				'label'     => 'Badge label',
				'condition' => array( 't5f_show_badge' => true ),
			),
		),
	);

	$stat = array(
		'type'     => 'group',
		'group'    => 't5-feature:stat',
		'controls' => array(
			array( 'key' => 't5f_show_stat', 'type' => 'toggle', 'label' => 'Show stat card' ),
			array(
				'key'       => 't5f_stat_number',
				'type'      => 'text',
				'label'     => 'Stat number/value',
				'condition' => array( 't5f_show_stat' => true ),
			),
			array(
				'key'       => 't5f_stat_label',
				'type'      => 'text',
				'label'     => 'Stat label',
				'condition' => array( 't5f_show_stat' => true ),
			),
		),
	);

	return cs_compose_controls(
		array(
			'controls'    => array( $content, $second, $image, $stat ),
			'control_nav' => array(
				't5-feature'         => 'Feature',
				't5-feature:content' => 'Content',
				't5-feature:second'  => 'Second row & button',
				't5-feature:image'   => 'Image & badge',
				't5-feature:stat'    => 'Stat card',
			),
		)
	);
}

// -----------------------------------------------------------------------------
// Register
// -----------------------------------------------------------------------------

cs_register_element(
	't5-feature',
	array(
		'title'   => 'Triple 5 Feature Section',
		'values'  => $triple5_feature_values,
		'builder' => 'triple5_feature_builder_setup',
		'render'  => 'triple5_feature_render',
		'icon'    => 'native',
		'group'   => 'layout',
	)
);
