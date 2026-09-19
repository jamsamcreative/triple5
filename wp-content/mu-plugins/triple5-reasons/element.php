<?php
/**
 * Triple 5 Reasons + Media — Cornerstone element definition.
 *
 * Centered uppercase heading, then two columns: left is a list of up to 6
 * reasons (red check-circle, bold title, one-line body) with a CTA button;
 * right is a stacked photo + YouTube video (each optional; blueprint placeholder
 * when no image is set, video hidden when no URL). Reasons with an empty title
 * are skipped.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Reason slots and their Triple 5 defaults: [title, body]. */
function triple5_reasons_defaults() {
	return array(
		1 => array( 'Metal that lasts', 'We install the same standing seam and panel systems we\'d put on our own homes and shops.' ),
		2 => array( 'Built for this climate', 'Snow load, freeze-thaw and wildfire exposure are designed in, not bolted on afterwards.' ),
		3 => array( 'Licensed in WA & ID', 'Fully licensed and insured in both states, working from Spokane to Coeur d\'Alene.' ),
		4 => array( 'One crew, start to finish', 'The crew that quotes your project is the crew that builds it — no subcontractor hand-offs.' ),
		5 => array( 'Straight pricing & financing', 'Clear written quotes and financing options so the right roof fits the budget.' ),
		6 => array( 'Locally owned', 'We live here, drive past our work every day, and our reputation rides on it.' ),
	);
}

// -----------------------------------------------------------------------------
// Values (defaults)
// -----------------------------------------------------------------------------

$triple5_reasons_vals = array(
	't5rs_heading'      => cs_value( 'Why homeowners choose Triple 5', 'markup', true ),
	't5rs_cta_text'     => cs_value( "Let's get started", 'markup', true ),
	't5rs_cta_url'      => cs_value( '/contact-us/', 'markup', true ),
	't5rs_image_src'    => cs_value( '', 'markup', true ),
	't5rs_image_retina' => cs_value( true, 'markup', true ),
	't5rs_image_width'  => cs_value( '', 'markup', true ),
	't5rs_image_height' => cs_value( '', 'markup', true ),
	't5rs_image_alt'    => cs_value( 'Completed metal roofing project', 'markup', true ),
	't5rs_video_url'    => cs_value( '', 'markup', true ),
	't5rs_video_title'  => cs_value( 'Triple 5 Construction — project walkthrough', 'markup', true ),
	't5rs_show_video'   => cs_value( true, 'markup', true ),
);
foreach ( triple5_reasons_defaults() as $i => $r ) {
	$triple5_reasons_vals[ "t5rs_r{$i}_title" ] = cs_value( $r[0], 'markup', true );
	$triple5_reasons_vals[ "t5rs_r{$i}_body" ]  = cs_value( $r[1], 'markup', true );
}
$triple5_reasons_values = cs_compose_values( $triple5_reasons_vals );

// -----------------------------------------------------------------------------
// Render
// -----------------------------------------------------------------------------

/** YouTube URL (watch / youtu.be / shorts / embed) → video ID, or '' if not YouTube. */
function triple5_reasons_youtube_id( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( preg_match( '~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $url, $m ) ) {
		return $m[1];
	}
	return preg_match( '/^[A-Za-z0-9_-]{11}$/', $url ) ? $url : '';
}

function triple5_reasons_icon( $name ) {
	switch ( $name ) {
		case 'check':
			return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
		case 'image':
			return '<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>';
		case 'play':
			return '<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>';
	}
	return '';
}

function triple5_reasons_render( $data ) {
	$heading = trim( (string) $data['t5rs_heading'] );

	$items = '';
	foreach ( array_keys( triple5_reasons_defaults() ) as $i ) {
		$title = trim( (string) ( $data[ "t5rs_r{$i}_title" ] ?? '' ) );
		if ( '' === $title ) {
			continue;
		}
		$body   = trim( (string) ( $data[ "t5rs_r{$i}_body" ] ?? '' ) );
		$items .= '<li class="t5-reasons__item">'
			. '<span class="t5-reasons__check" aria-hidden="true">' . triple5_reasons_icon( 'check' ) . '</span>'
			. '<div class="t5-reasons__text">'
			. '<h3 class="t5-reasons__title">' . esc_html( $title ) . '</h3>'
			. ( '' !== $body ? '<p class="t5-reasons__body">' . esc_html( $body ) . '</p>' : '' )
			. '</div></li>';
	}

	$cta_text = trim( (string) $data['t5rs_cta_text'] );
	$cta_url  = trim( (string) $data['t5rs_cta_url'] );
	$cta      = '' !== $cta_text
		? '<a class="t5-reasons__cta" href="' . esc_url( '' !== $cta_url ? $cta_url : '#' ) . '">' . esc_html( $cta_text ) . '</a>'
		: '';

	// Photo.
	$img = triple5_image_url( $data['t5rs_image_src'] );
	$alt = trim( (string) $data['t5rs_image_alt'] );
	if ( '' !== $img ) {
		$photo = '<figure class="t5-reasons__media has-image"><img src="' . esc_url( $img ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy"></figure>';
	} else {
		$photo = '<div class="t5-reasons__media is-blueprint">' . triple5_reasons_icon( 'image' )
			. ( '' !== $alt ? '<span class="t5-reasons__media-label">' . esc_html( $alt ) . '</span>' : '' ) . '</div>';
	}

	// Video (YouTube, privacy-enhanced domain).
	$video = '';
	if ( ! empty( $data['t5rs_show_video'] ) ) {
		$vid   = triple5_reasons_youtube_id( $data['t5rs_video_url'] );
		$title = trim( (string) $data['t5rs_video_title'] );
		if ( '' !== $vid ) {
			$video = '<div class="t5-reasons__media t5-reasons__video"><iframe src="https://www.youtube-nocookie.com/embed/' . esc_attr( $vid ) . '?rel=0" title="' . esc_attr( '' !== $title ? $title : 'Video' ) . '" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe></div>';
		} else {
			$video = '<div class="t5-reasons__media is-blueprint t5-reasons__video">' . triple5_reasons_icon( 'play' )
				. '<span class="t5-reasons__media-label">' . esc_html( '' !== $title ? $title : 'Video' ) . '</span></div>';
		}
	}

	return '<section class="t5-reasons">'
		. '<div class="t5-reasons__inner">'
		. ( '' !== $heading ? '<h2 class="t5-reasons__heading">' . esc_html( $heading ) . '</h2>' : '' )
		. '<div class="t5-reasons__grid">'
		. '<div class="t5-reasons__col">'
		. ( '' !== $items ? '<ul class="t5-reasons__list">' . $items . '</ul>' : '' )
		. $cta
		. '</div>'
		. '<div class="t5-reasons__col t5-reasons__stack">' . $photo . $video . '</div>'
		. '</div></div></section>';
}

// -----------------------------------------------------------------------------
// Builder controls
// -----------------------------------------------------------------------------

function triple5_reasons_builder_setup() {
	$groups = array(
		array(
			'type'     => 'group',
			'group'    => 't5-reasons:setup',
			'controls' => array(
				array( 'key' => 't5rs_heading', 'type' => 'text', 'label' => 'Heading' ),
				array( 'key' => 't5rs_cta_text', 'type' => 'text', 'label' => 'Button text (empty = hidden)' ),
				array( 'key' => 't5rs_cta_url', 'type' => 'text', 'label' => 'Button link (URL)' ),
			),
		),
		array(
			'type'     => 'group',
			'group'    => 't5-reasons:media',
			'controls' => array(
				array(
					'keys'  => array(
						'img_source' => 't5rs_image_src',
						'is_retina'  => 't5rs_image_retina',
						'width'      => 't5rs_image_width',
						'height'     => 't5rs_image_height',
					),
					'type'  => 'image',
					'title' => 'Photo (blueprint placeholder until set)',
				),
				array( 'key' => 't5rs_image_alt', 'type' => 'text', 'label' => 'Photo alt text / placeholder caption' ),
				array( 'key' => 't5rs_show_video', 'type' => 'toggle', 'label' => 'Show video slot' ),
				array( 'key' => 't5rs_video_url', 'type' => 'text', 'label' => 'YouTube URL (watch, youtu.be or shorts link)' ),
				array( 'key' => 't5rs_video_title', 'type' => 'text', 'label' => 'Video title (accessibility / placeholder caption)' ),
			),
		),
	);

	$nav = array(
		't5-reasons'       => 'Reasons + Media',
		't5-reasons:setup' => 'Heading & button',
		't5-reasons:media' => 'Photo & video',
	);

	foreach ( array_keys( triple5_reasons_defaults() ) as $i ) {
		$group_key         = "t5-reasons:r{$i}";
		$nav[ $group_key ] = "Reason {$i}";
		$groups[]          = array(
			'type'     => 'group',
			'group'    => $group_key,
			'controls' => array(
				array( 'key' => "t5rs_r{$i}_title", 'type' => 'text', 'label' => "Reason {$i} title (empty = hidden)" ),
				array(
					'key'     => "t5rs_r{$i}_body",
					'type'    => 'textarea',
					'label'   => 'One-line description',
					'options' => array( 'height' => 2 ),
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
	't5-reasons',
	array(
		'title'   => 'Triple 5 Reasons + Media',
		'values'  => $triple5_reasons_values,
		'builder' => 'triple5_reasons_builder_setup',
		'render'  => 'triple5_reasons_render',
		'icon'    => 'native',
		'group'   => 'layout',
	)
);
