<?php
/**
 * Plugin Name: Triple 5 SEO
 * Description: Per-page SEO title, meta description and local-business schema —
 *              an "SEO" box on pages/projects, output as <title>, <meta name=description>,
 *              canonical, Open Graph and JSON-LD (RoofingContractor with areaServed).
 *              Lightweight stand-in until/unless a full SEO plugin is installed.
 * Version:     0.1.0
 * Author:      jamsamcreative
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -----------------------------------------------------------------------------
// Business facts (single source for schema / defaults)
// -----------------------------------------------------------------------------

function triple5_seo_business() {
	return apply_filters(
		'triple5_seo_business',
		array(
			'name'      => 'Triple 5 Construction LLC',
			'phone'     => '+1-509-251-2829',
			'email'     => 'info@triple5construction.com',
			'city'      => 'Spokane',
			'region'    => 'WA',
			'country'   => 'US',
			'hours'     => 'Mo-Fr 07:00-17:00',
			'services'  => array( 'Metal roofing', 'Metal siding', 'Roof repair & storm damage', 'Remodeling & additions', 'Concrete', 'Framing & post-frame buildings' ),
			'areas'     => array( 'Spokane, WA', 'Spokane Valley, WA', 'Mead, WA', 'Airway Heights, WA', 'Liberty Lake, WA', "Coeur d'Alene, ID", 'Hayden, ID', 'Post Falls, ID', 'Rathdrum, ID', 'Sandpoint, ID' ),
			'logo'      => content_url( 'mu-plugins/triple5-brand/assets/logo-white.png' ),
		)
	);
}

function triple5_seo_post_types() {
	return array( 'page', 't5_project' );
}

// -----------------------------------------------------------------------------
// Meta box
// -----------------------------------------------------------------------------

add_action(
	'add_meta_boxes',
	static function () {
		foreach ( triple5_seo_post_types() as $pt ) {
			add_meta_box( 't5seo', 'SEO', 'triple5_seo_meta_box', $pt, 'normal', 'default' );
		}
	}
);

function triple5_seo_meta_box( $post ) {
	wp_nonce_field( 't5seo_save', 't5seo_nonce' );
	$title = get_post_meta( $post->ID, 't5seo_title', true );
	$desc  = get_post_meta( $post->ID, 't5seo_desc', true );
	$area  = get_post_meta( $post->ID, 't5seo_area', true );
	$noidx = get_post_meta( $post->ID, 't5seo_noindex', true );
	?>
	<p><label for="t5seo_title"><strong>Title tag</strong></label><br>
	<input type="text" id="t5seo_title" name="t5seo_title" class="large-text" maxlength="70" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php echo esc_attr( get_the_title( $post ) . ' | Triple 5 Construction' ); ?>">
	<span class="description">≈ 50–60 characters. Blank = page title + site name.</span></p>

	<p><label for="t5seo_desc"><strong>Meta description</strong></label><br>
	<textarea id="t5seo_desc" name="t5seo_desc" class="large-text" rows="3" maxlength="170"><?php echo esc_textarea( $desc ); ?></textarea>
	<span class="description">≈ 140–160 characters. Blank = excerpt, else nothing.</span></p>

	<p><label for="t5seo_area"><strong>Service area for this page</strong> (location pages)</label><br>
	<input type="text" id="t5seo_area" name="t5seo_area" class="regular-text" value="<?php echo esc_attr( $area ); ?>" placeholder="Spokane, WA">
	<span class="description">When set, the page emits RoofingContractor schema with <code>areaServed</code> = this place, plus a Service list.</span></p>

	<p><label><input type="checkbox" name="t5seo_noindex" value="1" <?php checked( $noidx, '1' ); ?>> Hide from search engines (noindex)</label></p>
	<?php
}

add_action(
	'save_post',
	static function ( $post_id ) {
		if ( ! isset( $_POST['t5seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['t5seo_nonce'] ) ), 't5seo_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		update_post_meta( $post_id, 't5seo_title', sanitize_text_field( wp_unslash( $_POST['t5seo_title'] ?? '' ) ) );
		update_post_meta( $post_id, 't5seo_desc', sanitize_textarea_field( wp_unslash( $_POST['t5seo_desc'] ?? '' ) ) );
		update_post_meta( $post_id, 't5seo_area', sanitize_text_field( wp_unslash( $_POST['t5seo_area'] ?? '' ) ) );
		update_post_meta( $post_id, 't5seo_noindex', empty( $_POST['t5seo_noindex'] ) ? '' : '1' );
	}
);

// -----------------------------------------------------------------------------
// Output
// -----------------------------------------------------------------------------

function triple5_seo_current_id() {
	return is_singular() ? get_queried_object_id() : 0;
}

function triple5_seo_description( $id ) {
	$desc = trim( (string) get_post_meta( $id, 't5seo_desc', true ) );
	if ( '' === $desc && has_excerpt( $id ) ) {
		$desc = wp_strip_all_tags( get_the_excerpt( $id ) );
	}
	return $desc;
}

/** <title>: custom title wins outright (site name already included by the author). */
add_filter(
	'pre_get_document_title',
	static function ( $title ) {
		$id = triple5_seo_current_id();
		if ( $id ) {
			$custom = trim( (string) get_post_meta( $id, 't5seo_title', true ) );
			if ( '' !== $custom ) {
				return $custom;
			}
		}
		return $title;
	},
	20
);

add_action(
	'wp_head',
	static function () {
		$id = triple5_seo_current_id();
		if ( ! $id ) {
			return;
		}
		$b     = triple5_seo_business();
		$desc  = triple5_seo_description( $id );
		$url   = get_permalink( $id );
		$title = wp_get_document_title();
		$img   = get_the_post_thumbnail_url( $id, 'large' );

		if ( '' !== $desc ) {
			echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
		}
		if ( '1' === get_post_meta( $id, 't5seo_noindex', true ) ) {
			echo '<meta name="robots" content="noindex,follow">' . "\n";
		}
		echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
		echo '<meta property="og:type" content="website">' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( $b['name'] ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		if ( '' !== $desc ) {
			echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
		}
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
		if ( $img ) {
			echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";
		}

		// JSON-LD: business on the front page; RoofingContractor + areaServed on location pages.
		$area = trim( (string) get_post_meta( $id, 't5seo_area', true ) );
		if ( is_front_page() || '' !== $area ) {
			$schema = array(
				'@context'    => 'https://schema.org',
				'@type'       => 'RoofingContractor',
				'@id'         => home_url( '/#business' ),
				'name'        => $b['name'],
				'url'         => is_front_page() ? home_url( '/' ) : $url,
				'telephone'   => $b['phone'],
				'email'       => $b['email'],
				'image'       => $img ? $img : $b['logo'],
				'logo'        => $b['logo'],
				'address'     => array( '@type' => 'PostalAddress', 'addressLocality' => $b['city'], 'addressRegion' => $b['region'], 'addressCountry' => $b['country'] ),
				'openingHours' => $b['hours'],
				'areaServed'  => '' !== $area
					? array( '@type' => 'City', 'name' => $area )
					: array_map( static fn( $a ) => array( '@type' => 'City', 'name' => $a ), $b['areas'] ),
				'hasOfferCatalog' => array(
					'@type'           => 'OfferCatalog',
					'name'            => 'Services',
					'itemListElement' => array_map(
						static fn( $s ) => array( '@type' => 'Offer', 'itemOffered' => array( '@type' => 'Service', 'name' => $s, 'areaServed' => '' !== $area ? $area : $b['areas'] ) ),
						$b['services']
					),
				),
			);
			echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
		}
	},
	5
);
