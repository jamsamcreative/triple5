<?php
/**
 * Plugin Name: Triple 5 Header
 * Description: Renders the Triple 5 Construction site header (clay top bar + navy nav + CTA)
 *              on the home page, using the brand design tokens. Coded header — edit here, not in Cornerstone.
 * Version:     0.1.0
 * Author:      jamsamcreative
 *
 * @package triple5
 *
 * Source of truth: repo triple5 → wp-content/mu-plugins/triple5-header/ (+ this file), symlinked into the Local site.
 * Scope: renders only on the front page (is_front_page()). Broaden in triple5_header_should_render() to go site-wide.
 * To revert: remove this file + the triple5-header/ folder (or their symlinks).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Where this coded header should appear.
 *
 * DISABLED: superseded by the native Cornerstone header ("Primary Header",
 * cs_header ID 26), which is editable in the Pro builder and assigned to the
 * Front Page. This file is kept for reference/rollback — flip to
 * `return is_front_page();` and switch the Home page off template-blank-5 to
 * bring the coded header back.
 */
function triple5_header_should_render() {
	return false;
}

function triple5_header_url( $path = '' ) {
	return content_url( 'mu-plugins/triple5-header/' . ltrim( $path, '/' ) );
}

/*
 * Note: Pro's own theme header/footer are removed from the Home page by
 * assigning it the "Blank - No Container | No Header, No Footer" page
 * template (template-blank-6.php) — the officially-supported Pro way to
 * strip theme chrome. This coded header then renders via wp_body_open.
 */

/**
 * Enqueue the header stylesheet (depends on the brand tokens from triple5-brand).
 */
add_action(
	'wp_enqueue_scripts',
	static function () {
		if ( ! triple5_header_should_render() ) {
			return;
		}
		wp_enqueue_style(
			'triple5-header',
			triple5_header_url( 'header.css' ),
			array( 'triple5-tokens' ),
			'0.1.0'
		);
	},
	30
);

/**
 * Small inline SVG icons (self-contained, no external requests).
 */
function triple5_header_icon( $name ) {
	$common = 'width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';
	switch ( $name ) {
		case 'mail':
			return '<svg ' . $common . '><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>';
		case 'phone':
			return '<svg ' . $common . '><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
		case 'clock':
			return '<svg ' . $common . '><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
		case 'arrow':
			return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>';
	}
	return '';
}

/**
 * Render the header right after <body> opens.
 */
add_action(
	'wp_body_open',
	static function () {
		if ( ! triple5_header_should_render() ) {
			return;
		}

		$email      = 'info@triple5construction.com';
		$phone      = '(509) 251-2829';
		$phone_href = 'tel:+15092512829';
		$hours      = 'Mon–Fri 7am–5pm · 24hr storm callout';

		$nav = array(
			array(
				'label' => 'Home',
				'url'   => home_url( '/' ),
			),
			array(
				'label' => 'About',
				'url'   => home_url( '/about/' ),
			),
			array(
				'label' => 'Our Services',
				'url'   => home_url( '/our-services/' ),
			),
			array(
				'label' => 'Projects',
				'url'   => home_url( '/projects/' ),
			),
			array(
				'label' => 'Contact Us',
				'url'   => home_url( '/contact-us/' ),
			),
		);

		$current = home_url( add_query_arg( array(), $GLOBALS['wp']->request ) );
		$logo    = triple5_header_url( 'assets/triple5-logo-white.png' );
		?>
		<header class="t5-header" role="banner">
			<div class="t5-topbar">
				<div class="t5-container t5-topbar__inner">
					<div class="t5-topbar__left">
						<a class="t5-topbar__item" href="mailto:<?php echo esc_attr( $email ); ?>">
							<?php echo triple5_header_icon( 'mail' ); // phpcs:ignore ?>
							<span><?php echo esc_html( strtoupper( $email ) ); ?></span>
						</a>
						<a class="t5-topbar__item" href="<?php echo esc_attr( $phone_href ); ?>">
							<?php echo triple5_header_icon( 'phone' ); // phpcs:ignore ?>
							<span><?php echo esc_html( $phone ); ?></span>
						</a>
					</div>
					<div class="t5-topbar__right t5-topbar__item">
						<?php echo triple5_header_icon( 'clock' ); // phpcs:ignore ?>
						<span><?php echo esc_html( strtoupper( $hours ) ); ?></span>
					</div>
				</div>
			</div>

			<div class="t5-nav">
				<div class="t5-container t5-nav__inner">
					<a class="t5-nav__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Triple 5 Construction — home">
						<img src="<?php echo esc_url( $logo ); ?>" alt="Triple 5 Construction" />
					</a>

					<nav class="t5-nav__menu" aria-label="Primary">
						<?php
						foreach ( $nav as $item ) {
							$is_active = untrailingslashit( $item['url'] ) === untrailingslashit( $current );
							printf(
								'<a class="t5-nav__link%s" href="%s"%s>%s</a>',
								$is_active ? ' is-active' : '',
								esc_url( $item['url'] ),
								$is_active ? ' aria-current="page"' : '',
								esc_html( strtoupper( $item['label'] ) )
							);
						}
						?>
					</nav>

					<a class="t5-nav__cta" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">
						<span>Get a Free Quote</span>
						<span class="t5-nav__cta-icon"><?php echo triple5_header_icon( 'arrow' ); // phpcs:ignore ?></span>
					</a>
				</div>
			</div>
		</header>
		<?php
	},
	5
);
