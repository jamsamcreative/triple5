<?php
/**
 * Triple 5 Hero — lead capture.
 *
 * Registers the `t5_lead` custom post type (a private "Leads" list in wp-admin) and
 * handles submissions from the hero's lead form: validate → store → email.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the lead form at runtime via a shortcode. The hero element embeds
 * `[t5_hero_form data="<base64 json>"]` in its (baked) markup; this renders the
 * live form on every page load so the nonce is always fresh and the success/error
 * notice reflects the current request. Baking the form as static HTML is not an
 * option: form/input tags are stripped by post-content sanitizing, and a baked
 * nonce would be stale and invalid for visitors.
 *
 * @param array $args heading, recipient, button, success.
 */
function triple5_hero_render_form( $args ) {
	$heading   = isset( $args['heading'] ) ? (string) $args['heading'] : '';
	$recipient = isset( $args['recipient'] ) ? (string) $args['recipient'] : '';
	$button    = isset( $args['button'] ) && '' !== $args['button'] ? (string) $args['button'] : 'Send request';
	$success   = isset( $args['success'] ) ? (string) $args['success'] : 'Thanks — we will be in touch shortly.';

	$status = isset( $_GET['t5_lead'] ) ? sanitize_key( wp_unslash( $_GET['t5_lead'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$notice = '';
	if ( 'success' === $status ) {
		$notice = '<div class="t5-hero__notice is-success" role="status">' . esc_html( $success ) . '</div>';
	} elseif ( 'error' === $status ) {
		$notice = '<div class="t5-hero__notice is-error" role="alert">Please add your name and an email or phone so we can reach you.</div>';
	}

	$fields = ''
		. '<div class="t5-hero__row2">'
		. '<input class="t5-hero__input" type="text" name="t5_name" placeholder="Full name" required>'
		. '<input class="t5-hero__input" type="email" name="t5_email" placeholder="Email">'
		. '</div>'
		. '<input class="t5-hero__input" type="tel" name="t5_phone" placeholder="Phone">'
		. '<input class="t5-hero__input" type="text" name="t5_address" placeholder="Address">'
		. '<input class="t5-hero__input" type="text" name="t5_referral" placeholder="Where did you hear about us?">'
		. '<textarea class="t5-hero__input t5-hero__textarea" name="t5_message" rows="3" placeholder="Message"></textarea>';

	$arrow = '<svg class="t5-hero__arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>';

	return '<div class="t5-hero__formwrap" id="t5-hero-form">'
		. $notice
		. '<form class="t5-hero__form" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'
		. '<div class="t5-hero__form-heading">' . nl2br( esc_html( $heading ) ) . '</div>'
		. $fields
		. wp_nonce_field( 't5_hero_lead', 't5_hero_nonce', true, false )
		. '<input type="hidden" name="action" value="t5_lead">'
		. '<input type="hidden" name="t5_recipient" value="' . esc_attr( $recipient ) . '">'
		. '<div class="t5-hero__hp" aria-hidden="true"><label>Leave this empty<input type="text" name="t5_website" tabindex="-1" autocomplete="off"></label></div>'
		. '<button class="t5-hero__submit" type="submit"><span>' . esc_html( $button ) . '</span>' . $arrow . '</button>'
		. '</form></div>';
}

/**
 * The hero element bakes an inert mount div into post_content:
 *   <div class="t5-hero__form-mount" data-t5-form="<hex(json)>"></div>
 * We swap it for the live form on `the_content` (which runs at front-end display,
 * NOT during Cornerstone's bake — so the marker survives baking and the form is
 * rendered fresh each request). A shortcode can't be used: Cornerstone expands
 * shortcodes while baking, and the expanded form is then stripped from post_content.
 *
 * Hex encoding keeps the payload to [0-9a-f] so no attribute sanitizing touches it.
 */
add_filter(
	'the_content',
	static function ( $content ) {
		if ( false === strpos( $content, 't5-hero__form-mount' ) ) {
			return $content;
		}
		return preg_replace_callback(
			'/<div class="t5-hero__form-mount" data-t5-form="([0-9a-fA-F]*)"><\/div>/',
			static function ( $m ) {
				$json    = '' !== $m[1] ? @hex2bin( $m[1] ) : ''; // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$decoded = $json ? json_decode( $json, true ) : array();
				if ( ! is_array( $decoded ) ) {
					$decoded = array();
				}
				return triple5_hero_render_form( $decoded );
			},
			$content
		);
	},
	20
);

/** The six form fields, in display order: form key => admin label. */
function triple5_hero_lead_fields() {
	return array(
		't5_name'     => 'Full name',
		't5_email'    => 'Email',
		't5_phone'    => 'Phone',
		't5_address'  => 'Address',
		't5_referral' => 'Where did you hear about us?',
		't5_message'  => 'Message',
	);
}

/**
 * The quote-request pipeline. Stored per lead as the `t5_status` meta value.
 * Order defines both tab order and pipeline progression.
 */
function triple5_hero_statuses() {
	return array(
		'new'       => 'New',
		'contacted' => 'Contacted',
		'quoted'    => 'Quoted',
		'won'       => 'Won',
		'lost'      => 'Lost',
	);
}

/** Default status for a brand-new request. */
function triple5_hero_default_status() {
	return 'new';
}

/**
 * Register the Leads custom post type. Not publicly queryable — this is an internal
 * inbox, shown only in wp-admin to users who can manage the site.
 */
add_action(
	'init',
	static function () {
		register_post_type(
			't5_lead',
			array(
				'labels'          => array(
					'name'          => 'Leads',
					'singular_name' => 'Lead',
					'menu_name'     => 'Leads',
					'all_items'     => 'All Leads',
					'edit_item'     => 'View Lead',
					'search_items'  => 'Search Leads',
					'not_found'     => 'No leads yet.',
				),
				'public'          => false,
				'show_ui'         => true,
				// The custom "Quote Requests" page (admin.php) is the single entry
				// point; the CPT keeps show_ui so its edit screen still resolves.
				'show_in_menu'    => false,
				'capability_type' => 'post',
				'map_meta_cap'    => true,
				'supports'        => array( 'title' ),
				'has_archive'     => false,
				'rewrite'         => false,
				'exclude_from_search' => true,
			)
		);
	}
);

/**
 * Handle a hero form submission. Bound to both the logged-in and logged-out
 * admin-post actions so it works for site visitors.
 */
function triple5_hero_handle_lead() {
	$redirect = wp_get_referer();
	if ( ! $redirect ) {
		$redirect = home_url( '/' );
	}

	// Nonce check — silently bounce on failure.
	if ( ! isset( $_POST['t5_hero_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['t5_hero_nonce'] ), 't5_hero_lead' ) ) {
		wp_safe_redirect( add_query_arg( 't5_lead', 'error', $redirect ) . '#t5-hero-form' );
		exit;
	}

	// Honeypot: real users leave `t5_website` empty. Bots fill it → pretend success.
	if ( ! empty( $_POST['t5_website'] ) ) {
		wp_safe_redirect( add_query_arg( 't5_lead', 'success', $redirect ) . '#t5-hero-form' );
		exit;
	}

	// Collect + sanitize.
	$values = array();
	foreach ( array_keys( triple5_hero_lead_fields() ) as $key ) {
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
		if ( 't5_email' === $key ) {
			$values[ $key ] = sanitize_email( $raw );
		} elseif ( 't5_message' === $key ) {
			$values[ $key ] = sanitize_textarea_field( $raw );
		} else {
			$values[ $key ] = sanitize_text_field( $raw );
		}
	}

	// Minimal validation: name + a way to reach them (email or phone).
	$has_contact = ( '' !== $values['t5_email'] && is_email( $values['t5_email'] ) ) || '' !== $values['t5_phone'];
	if ( '' === $values['t5_name'] || ! $has_contact ) {
		wp_safe_redirect( add_query_arg( 't5_lead', 'error', $redirect ) . '#t5-hero-form' );
		exit;
	}

	// Store the lead.
	$post_id = wp_insert_post(
		array(
			'post_type'   => 't5_lead',
			'post_status' => 'publish',
			'post_title'  => sprintf(
				'%s — %s',
				$values['t5_name'],
				gmdate( 'M j, Y' )
			),
		),
		true
	);

	if ( ! is_wp_error( $post_id ) ) {
		foreach ( $values as $key => $val ) {
			update_post_meta( $post_id, $key, $val );
		}
		update_post_meta( $post_id, 't5_source_url', esc_url_raw( $redirect ) );
		update_post_meta( $post_id, 't5_status', triple5_hero_default_status() );
	}

	// Email the site's chosen recipient.
	$recipient = isset( $_POST['t5_recipient'] ) ? sanitize_email( wp_unslash( $_POST['t5_recipient'] ) ) : '';
	if ( '' === $recipient || ! is_email( $recipient ) ) {
		$recipient = get_option( 'admin_email' );
	}

	$labels = triple5_hero_lead_fields();
	$lines  = array( 'New callout request from the website:', '' );
	foreach ( $values as $key => $val ) {
		$lines[] = $labels[ $key ] . ': ' . ( '' !== $val ? $val : '—' );
	}
	$lines[] = '';
	$lines[] = 'Source: ' . $redirect;

	wp_mail(
		$recipient,
		'New callout request — ' . $values['t5_name'],
		implode( "\n", $lines )
	);

	wp_safe_redirect( add_query_arg( 't5_lead', 'success', $redirect ) . '#t5-hero-form' );
	exit;
}
add_action( 'admin_post_t5_lead', 'triple5_hero_handle_lead' );
add_action( 'admin_post_nopriv_t5_lead', 'triple5_hero_handle_lead' );

/**
 * Admin list columns for Leads — show contact info at a glance instead of just title.
 */
add_filter(
	'manage_t5_lead_posts_columns',
	static function ( $cols ) {
		return array(
			'cb'         => isset( $cols['cb'] ) ? $cols['cb'] : '',
			'title'      => 'Name',
			't5_email'   => 'Email',
			't5_phone'   => 'Phone',
			't5_message' => 'Message',
			'date'       => 'Received',
		);
	}
);

add_action(
	'manage_t5_lead_posts_custom_column',
	static function ( $column, $post_id ) {
		if ( in_array( $column, array( 't5_email', 't5_phone', 't5_message' ), true ) ) {
			$val = (string) get_post_meta( $post_id, $column, true );
			if ( 't5_message' === $column && strlen( $val ) > 60 ) {
				$val = substr( $val, 0, 60 ) . '…';
			}
			echo esc_html( '' !== $val ? $val : '—' );
		}
	},
	10,
	2
);

/**
 * Show the stored field values on the Lead editor screen (read-only metabox), since
 * the CPT only "supports" the title.
 */
add_action(
	'add_meta_boxes_t5_lead',
	static function () {
		add_meta_box(
			't5_lead_details',
			'Lead details',
			static function ( $post ) {
				$labels = triple5_hero_lead_fields();
				echo '<table class="widefat striped"><tbody>';
				foreach ( $labels as $key => $label ) {
					$val = (string) get_post_meta( $post->ID, $key, true );
					printf(
						'<tr><th style="width:220px">%s</th><td>%s</td></tr>',
						esc_html( $label ),
						nl2br( esc_html( '' !== $val ? $val : '—' ) )
					);
				}
				$src = (string) get_post_meta( $post->ID, 't5_source_url', true );
				if ( '' !== $src ) {
					printf(
						'<tr><th>Source page</th><td><a href="%1$s">%1$s</a></td></tr>',
						esc_url( $src )
					);
				}
				echo '</tbody></table>';
			},
			't5_lead',
			'normal',
			'high'
		);
	}
);
