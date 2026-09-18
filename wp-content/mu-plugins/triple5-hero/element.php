<?php
/**
 * Triple 5 Hero — Cornerstone element definition.
 *
 * Registered on the `cs_register_elements` hook (see triple5-hero.php). Provides a
 * full-bleed hero with an editable headline/subtext, a swappable background image
 * (with a brand blueprint placeholder fallback), review badges, and a lead form that
 * posts to the handler in leads.php.
 *
 * API mirrors core definitions (see cornerstone/includes/elements/definitions/*.php):
 * values via cs_value/cs_compose_values, a builder callback returning composed
 * controls, and a render callback returning markup.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -----------------------------------------------------------------------------
// Values (defaults). Copy seeded from the /triple5-design skill's Home.jsx hero.
// In the headline, words wrapped in *asterisks* render in the red accent color.
// -----------------------------------------------------------------------------

$triple5_hero_values = cs_compose_values(
	array(
		't5_heading'         => cs_value( "*TRIPLE 5* ROOFING\nALWAYS AIMING FOR A\n*5 STAR* SERVICE", 'markup', true ),
		't5_subheading'      => cs_value( 'We deliver fast, honest and expert metal roofing and siding that protects your home and your peace of mind.', 'markup', true ),
		't5_financing'       => cs_value( 'Financing options available', 'markup', true ),

		't5_bg_image_src'    => cs_value( '', 'markup', true ),
		't5_bg_image_retina' => cs_value( true, 'markup', true ),
		't5_bg_image_width'  => cs_value( '', 'markup', true ),
		't5_bg_image_height' => cs_value( '', 'markup', true ),
		't5_overlay'         => cs_value( '82%', 'markup', true ),
		't5_use_blueprint'   => cs_value( true, 'markup', true ),

		't5_badge1_label'    => cs_value( 'Google', 'markup', true ),
		't5_badge1_rating'   => cs_value( '5.0', 'markup', true ),
		't5_badge1_url'      => cs_value( '', 'markup', true ),
		't5_badge2_label'    => cs_value( 'Trustpilot', 'markup', true ),
		't5_badge2_rating'   => cs_value( '4.9', 'markup', true ),
		't5_badge2_url'      => cs_value( '', 'markup', true ),

		't5_form_heading'    => cs_value( "Request a roofing,\nsiding or repair callout", 'markup', true ),
		't5_form_recipient'  => cs_value( get_option( 'admin_email' ), 'markup', true ),
		't5_form_button'     => cs_value( 'Send request', 'markup', true ),
		't5_form_success'    => cs_value( "Thanks — we've got your request and will be in touch shortly.", 'markup', true ),
	)
);

// -----------------------------------------------------------------------------
// Render helpers
// -----------------------------------------------------------------------------

/**
 * Escape headline text, convert *asterisk-wrapped* runs to red accent spans, and
 * turn newlines into <br>. Returns safe HTML.
 */
function triple5_hero_format_heading( $text ) {
	$escaped = esc_html( (string) $text );
	$escaped = preg_replace(
		'/\*(.+?)\*/s',
		'<span class="t5-hero__accent">$1</span>',
		$escaped
	);
	return nl2br( $escaped );
}

/**
 * Render a single review badge (circular monogram + rating + stars + source).
 * When a URL is provided the badge becomes a link opening in a new tab.
 */
function triple5_hero_badge( $label, $rating, $url = '' ) {
	$label  = trim( (string) $label );
	$rating = trim( (string) $rating );
	$url    = trim( (string) $url );
	if ( '' === $label && '' === $rating ) {
		return '';
	}
	$initial = '' !== $label ? mb_substr( $label, 0, 1 ) : '★';

	$inner = '<span class="t5-hero__badge-mark">' . esc_html( $initial ) . '</span>'
		. '<span class="t5-hero__badge-body">'
		. '<span class="t5-hero__badge-rating">' . esc_html( $rating ) . ' <span class="t5-hero__stars" aria-hidden="true">★★★★★</span></span>'
		. '<span class="t5-hero__badge-src">' . esc_html( $label ) . ' reviews</span>'
		. '</span>';

	if ( '' !== $url ) {
		return '<a class="t5-hero__badge is-link" href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">'
			. $inner
			. '<span class="screen-reader-text">' . esc_html( $label ) . ' reviews (opens in a new tab)</span>'
			. '</a>';
	}

	return '<div class="t5-hero__badge">' . $inner . '</div>';
}

// -----------------------------------------------------------------------------
// Render
// -----------------------------------------------------------------------------

function triple5_hero_render( $data ) {
	// Section CSS vars: overlay strength.
	$overlay = max( 0, min( 100, intval( $data['t5_overlay'] ) ) ) / 100;

	$bg_src      = isset( $data['t5_bg_image_src'] ) ? trim( (string) $data['t5_bg_image_src'] ) : '';
	$blueprint   = ! empty( $data['t5_use_blueprint'] ) && '' === $bg_src;
	$bg_classes  = 't5-hero__bg' . ( $blueprint ? ' is-blueprint' : '' );
	$bg_style    = '' !== $bg_src ? ' style="background-image:url(\'' . esc_url( $bg_src ) . '\')"' : '';

	// Badges.
	$badges = triple5_hero_badge( $data['t5_badge1_label'], $data['t5_badge1_rating'], isset( $data['t5_badge1_url'] ) ? $data['t5_badge1_url'] : '' )
		. triple5_hero_badge( $data['t5_badge2_label'], $data['t5_badge2_rating'], isset( $data['t5_badge2_url'] ) ? $data['t5_badge2_url'] : '' );

	// The form is rendered at runtime from an inert mount div (see leads.php), because
	// form/input markup does not survive being baked into post_content and a baked
	// nonce would be stale. Only the form's editable text rides along, hex-encoded so
	// no attribute sanitizing touches it.
	$form_payload = bin2hex(
		wp_json_encode(
			array(
				'heading'   => (string) $data['t5_form_heading'],
				'recipient' => (string) $data['t5_form_recipient'],
				'button'    => (string) $data['t5_form_button'],
				'success'   => (string) $data['t5_form_success'],
			)
		)
	);
	$form = '<div class="t5-hero__form-mount" data-t5-form="' . esc_attr( $form_payload ) . '"></div>';

	$copy = '<div class="t5-hero__copy">'
		. '<h1 class="t5-hero__title">' . triple5_hero_format_heading( $data['t5_heading'] ) . '</h1>'
		. ( '' !== trim( (string) $data['t5_subheading'] ) ? '<p class="t5-hero__sub">' . esc_html( $data['t5_subheading'] ) . '</p>' : '' )
		. ( '' !== trim( (string) $data['t5_financing'] ) ? '<div class="t5-hero__financing">' . esc_html( $data['t5_financing'] ) . '</div>' : '' )
		. ( '' !== $badges ? '<div class="t5-hero__badges">' . $badges . '</div>' : '' )
		. '</div>';

	return '<section class="t5-hero" style="--t5-overlay-opacity:' . esc_attr( $overlay ) . '">'
		. '<div class="' . esc_attr( $bg_classes ) . '"' . $bg_style . '></div>'
		. '<div class="t5-hero__scrim"></div>'
		. '<div class="t5-hero__inner">' . $copy . $form . '</div>'
		. '</section>';
}

// -----------------------------------------------------------------------------
// Builder controls
// -----------------------------------------------------------------------------

function triple5_hero_builder_setup() {
	$content = array(
		'type'     => 'group',
		'group'    => 't5-hero:content',
		'controls' => array(
			array(
				'key'     => 't5_heading',
				'type'    => 'textarea',
				'label'   => 'Headline (wrap words in *asterisks* to color them red)',
				'options' => array( 'height' => 3 ),
			),
			array(
				'key'     => 't5_subheading',
				'type'    => 'textarea',
				'label'   => 'Subtext',
				'options' => array( 'height' => 3 ),
			),
			array(
				'key'   => 't5_financing',
				'type'  => 'text',
				'label' => 'Financing line',
			),
		),
	);

	$background = array(
		'type'     => 'group',
		'group'    => 't5-hero:background',
		'controls' => array(
			array(
				'keys'  => array(
					'img_source' => 't5_bg_image_src',
					'is_retina'  => 't5_bg_image_retina',
					'width'      => 't5_bg_image_width',
					'height'     => 't5_bg_image_height',
				),
				'type'  => 'image',
				'title' => 'Background image',
			),
			array(
				'key'     => 't5_overlay',
				'type'    => 'unit-slider',
				'label'   => 'Overlay darkness',
				'options' => array(
					'available_units' => array( '%' ),
					'fallback_value'  => '82%',
					'ranges'          => array( '%' => array( 'min' => 0, 'max' => 100, 'step' => 1 ) ),
				),
			),
			array(
				'key'   => 't5_use_blueprint',
				'type'  => 'toggle',
				'label' => 'Use blueprint placeholder when no image is set',
			),
		),
	);

	$badges = array(
		'type'     => 'group',
		'group'    => 't5-hero:badges',
		'controls' => array(
			array( 'key' => 't5_badge1_label', 'type' => 'text', 'label' => 'Badge 1 source' ),
			array( 'key' => 't5_badge1_rating', 'type' => 'text', 'label' => 'Badge 1 rating' ),
			array( 'key' => 't5_badge1_url', 'type' => 'text', 'label' => 'Badge 1 link (Google reviews URL)' ),
			array( 'key' => 't5_badge2_label', 'type' => 'text', 'label' => 'Badge 2 source' ),
			array( 'key' => 't5_badge2_rating', 'type' => 'text', 'label' => 'Badge 2 rating' ),
			array( 'key' => 't5_badge2_url', 'type' => 'text', 'label' => 'Badge 2 link (Trustpilot URL)' ),
		),
	);

	$form = array(
		'type'     => 'group',
		'group'    => 't5-hero:form',
		'controls' => array(
			array(
				'key'     => 't5_form_heading',
				'type'    => 'textarea',
				'label'   => 'Form heading',
				'options' => array( 'height' => 2 ),
			),
			array( 'key' => 't5_form_recipient', 'type' => 'text', 'label' => 'Send submissions to (email)' ),
			array( 'key' => 't5_form_button', 'type' => 'text', 'label' => 'Submit button label' ),
			array(
				'key'     => 't5_form_success',
				'type'    => 'textarea',
				'label'   => 'Success message',
				'options' => array( 'height' => 2 ),
			),
		),
	);

	return cs_compose_controls(
		array(
			'controls'    => array( $content, $background, $badges, $form ),
			'control_nav' => array(
				't5-hero'            => 'Hero',
				't5-hero:content'    => 'Content',
				't5-hero:background' => 'Background',
				't5-hero:badges'     => 'Review badges',
				't5-hero:form'       => 'Lead form',
			),
		)
	);
}

// -----------------------------------------------------------------------------
// Register
// -----------------------------------------------------------------------------

cs_register_element(
	't5-hero',
	array(
		'title'    => 'Triple 5 Hero',
		'values'   => $triple5_hero_values,
		'builder'  => 'triple5_hero_builder_setup',
		'render'   => 'triple5_hero_render',
		'icon'     => 'native',
		'group'    => 'layout',
	)
);
