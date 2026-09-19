<?php
/**
 * Triple 5 Footer — Cornerstone element definition.
 *
 * Site footer on the charcoal field, matching the owner's reference:
 *   row 1  [logo + blurb] [Quick links] [Contact us: location / email / phone]
 *          [Working hours: hours / storm line / social buttons]
 *   row 2  full-width service-area map (image slot; blueprint placeholder until set)
 *   row 3  slim bar — legal links (left) · copyright line (right), mono type
 *
 * Link lists are edited as "Label | URL" lines so adding/reordering is a text
 * edit. Lives in the site-wide cs_footer document (Pro → Footers).
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -----------------------------------------------------------------------------
// Values (defaults)
// -----------------------------------------------------------------------------

$triple5_footer_values = cs_compose_values(
	array(
		// Brand column.
		't5ft_logo_src'     => cs_value( '', 'markup', true ),
		't5ft_logo_retina'  => cs_value( true, 'markup', true ),
		't5ft_logo_width'   => cs_value( '', 'markup', true ),
		't5ft_logo_height'  => cs_value( '', 'markup', true ),
		't5ft_blurb'        => cs_value( "Triple 5 Construction LLC — licensed metal roofing & siding specialists and full-service crew serving Spokane, WA and Coeur d'Alene, ID. Stronger together.", 'markup', true ),
		// Quick links.
		't5ft_links_heading' => cs_value( 'Quick links', 'markup', true ),
		't5ft_links'         => cs_value( "Home | /\nAbout | /about/\nServices | /our-services/\nProjects | /projects/\nContact Us | /contact-us/", 'markup', true ),
		// Contact.
		't5ft_contact_heading' => cs_value( 'Contact us', 'markup', true ),
		't5ft_location'        => cs_value( "Serving Spokane, WA & Coeur d'Alene, ID", 'markup', true ),
		't5ft_email'           => cs_value( 'info@triple5construction.com', 'markup', true ),
		't5ft_phone'           => cs_value( '(509) 251-2829', 'markup', true ),
		// Hours + social.
		't5ft_hours_heading' => cs_value( 'Working hours', 'markup', true ),
		't5ft_hours'         => cs_value( 'Mon–Fri · 7:00am–5:00pm', 'markup', true ),
		't5ft_storm'         => cs_value( '24hr storm-damage callout', 'markup', true ),
		't5ft_facebook'      => cs_value( 'https://www.facebook.com/', 'markup', true ),
		't5ft_instagram'     => cs_value( 'https://www.instagram.com/', 'markup', true ),
		't5ft_youtube'       => cs_value( 'https://www.youtube.com/', 'markup', true ),
		// Map band.
		't5ft_show_map'    => cs_value( true, 'markup', true ),
		't5ft_map_src'     => cs_value( '', 'markup', true ),
		't5ft_map_retina'  => cs_value( true, 'markup', true ),
		't5ft_map_width'   => cs_value( '', 'markup', true ),
		't5ft_map_height'  => cs_value( '', 'markup', true ),
		't5ft_map_caption' => cs_value( "Service area map — Spokane & Coeur d'Alene", 'markup', true ),
		't5ft_map_url'     => cs_value( '', 'markup', true ),
		// Bottom bar.
		't5ft_legal_links' => cs_value( 'Terms & Conditions | /terms/', 'markup', true ),
		't5ft_legal'       => cs_value( '© {year} Triple 5 Construction LLC · Licensed in WA & ID · All Rights Reserved', 'markup', true ),
	)
);

// -----------------------------------------------------------------------------
// Render
// -----------------------------------------------------------------------------

/** Lucide + simple brand glyphs used in the footer. */
function triple5_footer_icon( $name ) {
	$stroke = array(
		'pin'   => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>',
		'mail'  => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
		'phone' => '<path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/>',
		'clock' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
		'storm' => '<path d="M6 16.326A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 .5 8.973"/><path d="m13 12-3 5h4l-3 5"/>',
		'image' => '<rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>',
	);
	$fill = array(
		'facebook'  => '<path d="M13.5 22v-8h2.7l.4-3.2h-3.1V8.8c0-.9.3-1.6 1.6-1.6h1.7V4.4c-.3 0-1.3-.1-2.5-.1-2.5 0-4.1 1.5-4.1 4.2v2.3H7.4V14h2.8v8h3.3z"/>',
		'instagram' => '<path d="M12 7.3a4.7 4.7 0 1 0 0 9.4 4.7 4.7 0 0 0 0-9.4zm0 7.7a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm6-7.9a1.1 1.1 0 1 1-2.2 0 1.1 1.1 0 0 1 2.2 0zM21.2 8.2c-.1-1.5-.4-2.8-1.5-3.9S17.3 2.9 15.8 2.8C14.3 2.7 9.7 2.7 8.2 2.8 6.7 2.9 5.4 3.2 4.3 4.3S2.9 6.7 2.8 8.2c-.1 1.5-.1 6.1 0 7.6.1 1.5.4 2.8 1.5 3.9s2.4 1.4 3.9 1.5c1.5.1 6.1.1 7.6 0 1.5-.1 2.8-.4 3.9-1.5s1.4-2.4 1.5-3.9c.1-1.5.1-6.1 0-7.6zm-2 9.2c-.3.8-1 1.5-1.8 1.8-1.3.5-4.3.4-5.4.4s-4.1.1-5.4-.4c-.8-.3-1.5-1-1.8-1.8-.5-1.3-.4-4.3-.4-5.4s-.1-4.1.4-5.4c.3-.8 1-1.5 1.8-1.8 1.3-.5 4.3-.4 5.4-.4s4.1-.1 5.4.4c.8.3 1.5 1 1.8 1.8.5 1.3.4 4.3.4 5.4s.1 4.1-.4 5.4z"/>',
		'youtube'   => '<path d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2 26 26 0 0 0 2 12a26 26 0 0 0 .4 4.8 2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8A26 26 0 0 0 22 12a26 26 0 0 0-.4-4.8zM10 15V9l5.2 3L10 15z"/>',
	);
	if ( isset( $stroke[ $name ] ) ) {
		$size = 'image' === $name ? 30 : 20;
		return '<svg class="t5-footer__icon" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $stroke[ $name ] . '</svg>';
	}
	if ( isset( $fill[ $name ] ) ) {
		return '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">' . $fill[ $name ] . '</svg>';
	}
	return '';
}

/** "Label | URL" lines → <li> markup. Lines without a URL render as plain text. */
function triple5_footer_link_list( $raw ) {
	$out = '';
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $raw ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		$label = $parts[0];
		$url   = $parts[1] ?? '';
		$out  .= '' !== $url
			? '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>'
			: '<li><span>' . esc_html( $label ) . '</span></li>';
	}
	return $out;
}

function triple5_footer_render( $data ) {
	$heading = static function ( $text ) {
		return '' !== trim( (string) $text ) ? '<h2 class="t5-footer__heading">' . esc_html( $text ) . '</h2>' : '';
	};

	// Brand column.
	$logo_src = trim( (string) $data['t5ft_logo_src'] );
	if ( '' === $logo_src ) {
		$logo_src = content_url( 'mu-plugins/triple5-brand/assets/logo-white.png' );
	}
	$brand = '<div class="t5-footer__col t5-footer__brand">'
		. '<a class="t5-footer__logo" href="' . esc_url( home_url( '/' ) ) . '" aria-label="Triple 5 Construction — home"><img src="' . esc_url( $logo_src ) . '" alt="" width="96" height="96" loading="lazy"></a>'
		. ( '' !== trim( (string) $data['t5ft_blurb'] ) ? '<p class="t5-footer__blurb">' . esc_html( $data['t5ft_blurb'] ) . '</p>' : '' )
		. '</div>';

	// Quick links.
	$links = triple5_footer_link_list( $data['t5ft_links'] );
	$quick = '<div class="t5-footer__col">' . $heading( $data['t5ft_links_heading'] )
		. ( '' !== $links ? '<ul class="t5-footer__links">' . $links . '</ul>' : '' ) . '</div>';

	// Contact.
	$rows  = '';
	$loc   = trim( (string) $data['t5ft_location'] );
	$email = trim( (string) $data['t5ft_email'] );
	$phone = trim( (string) $data['t5ft_phone'] );
	if ( '' !== $loc ) {
		$rows .= '<li>' . triple5_footer_icon( 'pin' ) . '<span>' . esc_html( $loc ) . '</span></li>';
	}
	if ( '' !== $email ) {
		$rows .= '<li>' . triple5_footer_icon( 'mail' ) . '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a></li>';
	}
	if ( '' !== $phone ) {
		$rows .= '<li>' . triple5_footer_icon( 'phone' ) . '<a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ) . '">' . esc_html( $phone ) . '</a></li>';
	}
	$contact = '<div class="t5-footer__col">' . $heading( $data['t5ft_contact_heading'] )
		. ( '' !== $rows ? '<ul class="t5-footer__iconlist">' . $rows . '</ul>' : '' ) . '</div>';

	// Hours + social.
	$rows  = '';
	$hours = trim( (string) $data['t5ft_hours'] );
	$storm = trim( (string) $data['t5ft_storm'] );
	if ( '' !== $hours ) {
		$rows .= '<li>' . triple5_footer_icon( 'clock' ) . '<span>' . esc_html( $hours ) . '</span></li>';
	}
	if ( '' !== $storm ) {
		$rows .= '<li>' . triple5_footer_icon( 'storm' ) . '<span>' . esc_html( $storm ) . '</span></li>';
	}
	$social = '';
	foreach ( array( 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube' ) as $key => $label ) {
		$url = trim( (string) $data[ "t5ft_{$key}" ] );
		if ( '' !== $url ) {
			$social .= '<a class="t5-footer__social" href="' . esc_url( $url ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $label ) . '">' . triple5_footer_icon( $key ) . '</a>';
		}
	}
	$hours_col = '<div class="t5-footer__col">' . $heading( $data['t5ft_hours_heading'] )
		. ( '' !== $rows ? '<ul class="t5-footer__iconlist">' . $rows . '</ul>' : '' )
		. ( '' !== $social ? '<div class="t5-footer__socials">' . $social . '</div>' : '' )
		. '</div>';

	// Map band.
	$map = '';
	if ( ! empty( $data['t5ft_show_map'] ) ) {
		$src     = trim( (string) $data['t5ft_map_src'] );
		$caption = trim( (string) $data['t5ft_map_caption'] );
		$map_url = trim( (string) $data['t5ft_map_url'] );
		if ( '' !== $src ) {
			$inner = '<img src="' . esc_url( $src ) . '" alt="' . esc_attr( $caption ) . '" loading="lazy">';
			$map   = '' !== $map_url
				? '<a class="t5-footer__map has-image" href="' . esc_url( $map_url ) . '" target="_blank" rel="noopener">' . $inner . '</a>'
				: '<div class="t5-footer__map has-image">' . $inner . '</div>';
		} else {
			$map = '<div class="t5-footer__map is-blueprint">' . triple5_footer_icon( 'image' )
				. ( '' !== $caption ? '<span class="t5-footer__map-label">' . esc_html( $caption ) . '</span>' : '' )
				. '</div>';
		}
	}

	// Bottom bar.
	$legal_links = triple5_footer_link_list( $data['t5ft_legal_links'] );
	$legal       = str_replace( '{year}', gmdate( 'Y' ), trim( (string) $data['t5ft_legal'] ) );
	$bar = '<div class="t5-footer__bar"><div class="t5-footer__bar-inner">'
		. ( '' !== $legal_links ? '<ul class="t5-footer__legal-links">' . $legal_links . '</ul>' : '<span></span>' )
		. ( '' !== $legal ? '<p class="t5-footer__legal">' . esc_html( $legal ) . '</p>' : '' )
		. '</div></div>';

	return '<footer class="t5-footer">'
		. '<div class="t5-footer__inner">'
		. '<div class="t5-footer__grid">' . $brand . $quick . $contact . $hours_col . '</div>'
		. $map
		. '</div>'
		. $bar
		. '</footer>';
}

// -----------------------------------------------------------------------------
// Builder controls
// -----------------------------------------------------------------------------

function triple5_footer_builder_setup() {
	$img = static function ( $prefix, $title ) {
		return array(
			'keys'  => array(
				'img_source' => "{$prefix}_src",
				'is_retina'  => "{$prefix}_retina",
				'width'      => "{$prefix}_width",
				'height'     => "{$prefix}_height",
			),
			'type'  => 'image',
			'title' => $title,
		);
	};

	return cs_compose_controls(
		array(
			'controls'    => array(
				array(
					'type'     => 'group',
					'group'    => 't5-footer:brand',
					'controls' => array(
						$img( 't5ft_logo', 'Logo (defaults to the white 555 mark)' ),
						array( 'key' => 't5ft_blurb', 'type' => 'textarea', 'label' => 'Blurb', 'options' => array( 'height' => 5 ) ),
					),
				),
				array(
					'type'     => 'group',
					'group'    => 't5-footer:links',
					'controls' => array(
						array( 'key' => 't5ft_links_heading', 'type' => 'text', 'label' => 'Heading' ),
						array( 'key' => 't5ft_links', 'type' => 'textarea', 'label' => 'Links — one per line: Label | URL', 'options' => array( 'height' => 6 ) ),
					),
				),
				array(
					'type'     => 'group',
					'group'    => 't5-footer:contact',
					'controls' => array(
						array( 'key' => 't5ft_contact_heading', 'type' => 'text', 'label' => 'Heading' ),
						array( 'key' => 't5ft_location', 'type' => 'text', 'label' => 'Location line' ),
						array( 'key' => 't5ft_email', 'type' => 'text', 'label' => 'Email (mailto link)' ),
						array( 'key' => 't5ft_phone', 'type' => 'text', 'label' => 'Phone (tap-to-call link)' ),
					),
				),
				array(
					'type'     => 'group',
					'group'    => 't5-footer:hours',
					'controls' => array(
						array( 'key' => 't5ft_hours_heading', 'type' => 'text', 'label' => 'Heading' ),
						array( 'key' => 't5ft_hours', 'type' => 'text', 'label' => 'Hours line' ),
						array( 'key' => 't5ft_storm', 'type' => 'text', 'label' => 'Storm callout line' ),
						array( 'key' => 't5ft_facebook', 'type' => 'text', 'label' => 'Facebook URL (empty = hidden)' ),
						array( 'key' => 't5ft_instagram', 'type' => 'text', 'label' => 'Instagram URL (empty = hidden)' ),
						array( 'key' => 't5ft_youtube', 'type' => 'text', 'label' => 'YouTube URL (empty = hidden)' ),
					),
				),
				array(
					'type'     => 'group',
					'group'    => 't5-footer:map',
					'controls' => array(
						array( 'key' => 't5ft_show_map', 'type' => 'toggle', 'label' => 'Show service-area map band' ),
						$img( 't5ft_map', 'Map image (blueprint placeholder until set)' ),
						array( 'key' => 't5ft_map_caption', 'type' => 'text', 'label' => 'Caption / alt text' ),
						array( 'key' => 't5ft_map_url', 'type' => 'text', 'label' => 'Map link (e.g. Google Maps URL, optional)' ),
					),
				),
				array(
					'type'     => 'group',
					'group'    => 't5-footer:bar',
					'controls' => array(
						array( 'key' => 't5ft_legal_links', 'type' => 'textarea', 'label' => 'Left links — one per line: Label | URL', 'options' => array( 'height' => 3 ) ),
						array( 'key' => 't5ft_legal', 'type' => 'text', 'label' => 'Copyright line ({year} = current year)' ),
					),
				),
			),
			'control_nav' => array(
				't5-footer'         => 'Footer',
				't5-footer:brand'   => 'Logo & blurb',
				't5-footer:links'   => 'Quick links',
				't5-footer:contact' => 'Contact us',
				't5-footer:hours'   => 'Hours & social',
				't5-footer:map'     => 'Map band',
				't5-footer:bar'     => 'Bottom bar',
			),
		)
	);
}

// -----------------------------------------------------------------------------
// Register
// -----------------------------------------------------------------------------

cs_register_element(
	't5-footer',
	array(
		'title'   => 'Triple 5 Footer',
		'values'  => $triple5_footer_values,
		'builder' => 'triple5_footer_builder_setup',
		'render'  => 'triple5_footer_render',
		'icon'    => 'native',
		'group'   => 'layout',
	)
);
