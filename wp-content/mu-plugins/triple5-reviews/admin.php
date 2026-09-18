<?php
/**
 * Triple 5 Testimonials — Settings → Triple 5 Reviews.
 *
 * API credentials, cache TTL, a "Refresh now" action, and a moderation table where
 * individual fetched reviews can be hidden/unhidden. Manual reviews are edited on
 * the element itself in Cornerstone, not here.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function triple5_reviews_admin_slug() {
	return 'triple5-reviews';
}

function triple5_reviews_admin_cap() {
	return 'manage_options';
}

function triple5_reviews_admin_url( array $args = array() ) {
	return add_query_arg( array_merge( array( 'page' => triple5_reviews_admin_slug() ), $args ), admin_url( 'options-general.php' ) );
}

add_action(
	'admin_menu',
	static function () {
		add_options_page(
			'Triple 5 Reviews',
			'Triple 5 Reviews',
			triple5_reviews_admin_cap(),
			triple5_reviews_admin_slug(),
			'triple5_reviews_render_settings'
		);
	}
);

// -----------------------------------------------------------------------------
// Actions (POST → redirect → GET)
// -----------------------------------------------------------------------------

add_action(
	'admin_post_triple5_reviews_save',
	static function () {
		if ( ! current_user_can( triple5_reviews_admin_cap() ) ) {
			wp_die( 'Not allowed.' );
		}
		check_admin_referer( 'triple5_reviews_save' );

		$patch = array(
			'google_place_id'  => sanitize_text_field( wp_unslash( $_POST['google_place_id'] ?? '' ) ),
			'yelp_business_id' => sanitize_text_field( wp_unslash( $_POST['yelp_business_id'] ?? '' ) ),
			'cache_hours'      => max( 1, min( 168, (int) ( $_POST['cache_hours'] ?? 12 ) ) ),
		);
		// Keys: blank field = keep existing (so the page never echoes secrets back).
		foreach ( array( 'google_api_key', 'yelp_api_key' ) as $k ) {
			$v = trim( (string) wp_unslash( $_POST[ $k ] ?? '' ) );
			if ( '' !== $v ) {
				$patch[ $k ] = sanitize_text_field( $v );
			}
			if ( ! empty( $_POST[ $k . '_clear' ] ) ) {
				$patch[ $k ] = '';
			}
		}
		triple5_reviews_update_settings( $patch );

		// Credentials changed → drop the cache so the next refresh uses them.
		delete_transient( triple5_reviews_cache_key() );

		wp_safe_redirect( triple5_reviews_admin_url( array( 't5msg' => 'saved' ) ) );
		exit;
	}
);

add_action(
	'admin_post_triple5_reviews_refresh',
	static function () {
		if ( ! current_user_can( triple5_reviews_admin_cap() ) ) {
			wp_die( 'Not allowed.' );
		}
		check_admin_referer( 'triple5_reviews_refresh' );
		$got = triple5_reviews_refresh();
		wp_safe_redirect( triple5_reviews_admin_url( array( 't5msg' => 'refreshed', 't5n' => count( $got ) ) ) );
		exit;
	}
);

add_action(
	'admin_post_triple5_reviews_moderate',
	static function () {
		if ( ! current_user_can( triple5_reviews_admin_cap() ) ) {
			wp_die( 'Not allowed.' );
		}
		check_admin_referer( 'triple5_reviews_moderate' );
		$id = sanitize_text_field( wp_unslash( $_POST['review_id'] ?? '' ) );
		if ( '' !== $id ) {
			triple5_reviews_set_hidden( $id, ! empty( $_POST['hide'] ) );
		}
		wp_safe_redirect( triple5_reviews_admin_url( array( 't5msg' => 'moderated' ) ) );
		exit;
	}
);

// -----------------------------------------------------------------------------
// Page
// -----------------------------------------------------------------------------

function triple5_reviews_render_settings() {
	$s      = triple5_reviews_settings();
	$labels = triple5_reviews_source_labels();
	$hidden = triple5_reviews_hidden_ids();
	$all    = triple5_reviews_fetched();
	$msg    = sanitize_key( $_GET['t5msg'] ?? '' );

	$notice = '';
	if ( 'saved' === $msg ) {
		$notice = 'Settings saved. Cache cleared — click "Refresh now" to pull reviews with the new credentials.';
	} elseif ( 'refreshed' === $msg ) {
		$notice = sprintf( 'Refreshed — %d review(s) fetched.', (int) ( $_GET['t5n'] ?? 0 ) );
	} elseif ( 'moderated' === $msg ) {
		$notice = 'Moderation updated.';
	}
	?>
	<div class="wrap">
		<h1>Triple 5 Reviews</h1>
		<p>Reviews are pulled from the sources below, cached, and shown by the <strong>Triple 5 Testimonials</strong> Cornerstone element. Star-rating filtering (e.g. hide 1–3★) is set on the element itself; use this page to hide specific reviews regardless of rating.</p>

		<?php if ( $notice ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice ); ?></p></div>
		<?php endif; ?>
		<?php if ( '' !== $s['last_error'] ) : ?>
			<div class="notice notice-error"><p><strong>Last fetch error:</strong> <?php echo esc_html( $s['last_error'] ); ?> — previous reviews from that source are still being served.</p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'triple5_reviews_save' ); ?>
			<input type="hidden" name="action" value="triple5_reviews_save">
			<h2>Google</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="google_place_id">Place ID</label></th>
					<td><input name="google_place_id" id="google_place_id" type="text" class="regular-text code" value="<?php echo esc_attr( $s['google_place_id'] ); ?>" placeholder="ChIJ…">
					<p class="description">Find it with Google's <a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank" rel="noopener">Place ID finder</a>. Google returns at most 5 reviews per place.</p></td>
				</tr>
				<tr>
					<th scope="row"><label for="google_api_key">API key</label></th>
					<td><input name="google_api_key" id="google_api_key" type="password" class="regular-text code" autocomplete="off" placeholder="<?php echo '' !== $s['google_api_key'] ? '•••••••• (saved — leave blank to keep)' : 'AIza…'; ?>">
					<?php if ( '' !== $s['google_api_key'] ) : ?><label style="margin-left:8px"><input type="checkbox" name="google_api_key_clear" value="1"> Clear</label><?php endif; ?>
					<p class="description">Google Cloud key with <strong>Places API (New)</strong> enabled; restrict it to that API. Billing account required (Places has a monthly free tier).</p></td>
				</tr>
			</table>

			<h2>Yelp</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="yelp_business_id">Business ID or alias</label></th>
					<td><input name="yelp_business_id" id="yelp_business_id" type="text" class="regular-text code" value="<?php echo esc_attr( $s['yelp_business_id'] ); ?>" placeholder="triple-5-construction-spokane">
					<p class="description">The slug from the business's Yelp URL. Yelp returns 3 reviews, text truncated to ~160 characters (each card links to the full review, per Yelp's terms).</p></td>
				</tr>
				<tr>
					<th scope="row"><label for="yelp_api_key">API key</label></th>
					<td><input name="yelp_api_key" id="yelp_api_key" type="password" class="regular-text code" autocomplete="off" placeholder="<?php echo '' !== $s['yelp_api_key'] ? '•••••••• (saved — leave blank to keep)' : ''; ?>">
					<?php if ( '' !== $s['yelp_api_key'] ) : ?><label style="margin-left:8px"><input type="checkbox" name="yelp_api_key_clear" value="1"> Clear</label><?php endif; ?>
					<p class="description">From a <a href="https://www.yelp.com/developers/v3/manage_app" target="_blank" rel="noopener">Yelp Fusion app</a>.</p></td>
				</tr>
			</table>

			<h2>Cache</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="cache_hours">Refresh every</label></th>
					<td><input name="cache_hours" id="cache_hours" type="number" min="1" max="168" class="small-text" value="<?php echo esc_attr( (int) $s['cache_hours'] ); ?>"> hours
					<p class="description">Last fetch: <?php echo $s['last_fetch'] ? esc_html( wp_date( 'M j, Y g:i a', (int) $s['last_fetch'] ) ) : '—'; ?>
					<?php if ( ! empty( $s['google_rating'] ) ) : ?> · Google aggregate: <strong><?php echo esc_html( number_format( (float) $s['google_rating'], 1 ) ); ?>★</strong> from <?php echo esc_html( number_format_i18n( (int) $s['google_count'] ) ); ?> ratings<?php endif; ?></p></td>
				</tr>
			</table>
			<?php submit_button( 'Save settings' ); ?>
		</form>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:-8px 0 32px">
			<?php wp_nonce_field( 'triple5_reviews_refresh' ); ?>
			<input type="hidden" name="action" value="triple5_reviews_refresh">
			<?php submit_button( 'Refresh now', 'secondary', 'submit', false ); ?>
			<span class="description" style="margin-left:8px">Pulls fresh reviews from every configured source immediately.</span>
		</form>

		<h2>Fetched reviews (<?php echo count( $all ); ?>)</h2>
		<?php if ( empty( $all ) ) : ?>
			<p>No reviews fetched yet. Add credentials above and click <em>Refresh now</em>. Until then the element shows only its manual reviews.</p>
		<?php else : ?>
			<table class="widefat striped">
				<thead><tr><th style="width:90px">Source</th><th style="width:70px">Rating</th><th>Review</th><th style="width:120px">Date</th><th style="width:110px">Visibility</th></tr></thead>
				<tbody>
				<?php foreach ( $all as $r ) : $is_hidden = in_array( $r['id'], $hidden, true ); ?>
					<tr<?php echo $is_hidden ? ' style="opacity:.55"' : ''; ?>>
						<td><?php echo esc_html( $labels[ $r['source'] ] ?? $r['source'] ); ?></td>
						<td><span style="color:#C98A16"><?php echo esc_html( str_repeat( '★', (int) $r['rating'] ) ); ?></span><span style="color:#ccc"><?php echo esc_html( str_repeat( '★', 5 - (int) $r['rating'] ) ); ?></span></td>
						<td><strong><?php echo esc_html( $r['author'] ); ?></strong><?php echo $r['url'] ? ' <a href="' . esc_url( $r['url'] ) . '" target="_blank" rel="noopener">view</a>' : ''; ?><br><?php echo esc_html( wp_trim_words( $r['text'], 40 ) ); ?></td>
						<td><?php echo $r['time'] ? esc_html( wp_date( 'M j, Y', (int) $r['time'] ) ) : '—'; ?></td>
						<td>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
								<?php wp_nonce_field( 'triple5_reviews_moderate' ); ?>
								<input type="hidden" name="action" value="triple5_reviews_moderate">
								<input type="hidden" name="review_id" value="<?php echo esc_attr( $r['id'] ); ?>">
								<input type="hidden" name="hide" value="<?php echo $is_hidden ? '0' : '1'; ?>">
								<button type="submit" class="button button-small"><?php echo $is_hidden ? 'Unhide' : 'Hide'; ?></button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	<?php
}
