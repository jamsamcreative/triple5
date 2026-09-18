<?php
/**
 * Triple 5 — "Quote Requests" admin dashboard.
 *
 * A branded top-level admin page that lists every hero-form submission (t5_lead)
 * as a status pipeline (New → Contacted → Quoted → Won / Lost), with tab count
 * badges, search, per-row status changes, a detail view, CSV export, and a
 * wp-admin Dashboard summary widget.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Admin page slug + capability. */
function triple5_quote_slug() {
	return 't5-quote-requests';
}
function triple5_quote_cap() {
	return 'edit_posts';
}

/** Per-page row count for the list. */
function triple5_quote_per_page() {
	return 20;
}

// -----------------------------------------------------------------------------
// Data
// -----------------------------------------------------------------------------

/**
 * Load every request as a normalized row (newest first). Small dataset (contractor
 * leads), so we load once and filter/count/paginate in PHP.
 *
 * @return array<int,array> each: id, status, date_ts, date, source, fields{...}
 */
function triple5_quote_all_rows() {
	$q = new WP_Query(
		array(
			'post_type'      => 't5_lead',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
		)
	);

	$statuses = triple5_hero_statuses();
	$fields   = triple5_hero_lead_fields();
	$rows     = array();

	foreach ( $q->posts as $p ) {
		$status = (string) get_post_meta( $p->ID, 't5_status', true );
		if ( ! isset( $statuses[ $status ] ) ) {
			$status = triple5_hero_default_status(); // legacy/missing → New.
		}
		$row = array(
			'id'      => $p->ID,
			'status'  => $status,
			'date_ts' => get_post_time( 'U', true, $p ),
			'date'    => get_the_date( 'M j, Y g:i a', $p ),
			'source'  => (string) get_post_meta( $p->ID, 't5_source_url', true ),
			'fields'  => array(),
		);
		foreach ( array_keys( $fields ) as $key ) {
			$row['fields'][ $key ] = (string) get_post_meta( $p->ID, $key, true );
		}
		$rows[] = $row;
	}
	return $rows;
}

/** Tally counts per status plus 'all'. */
function triple5_quote_counts( $rows ) {
	$counts = array( 'all' => count( $rows ) );
	foreach ( array_keys( triple5_hero_statuses() ) as $s ) {
		$counts[ $s ] = 0;
	}
	foreach ( $rows as $r ) {
		$counts[ $r['status'] ]++;
	}
	return $counts;
}

/** Filter rows by tab (status or 'all') and a free-text search across fields. */
function triple5_quote_filter( $rows, $status, $search ) {
	$search = trim( (string) $search );
	return array_values(
		array_filter(
			$rows,
			static function ( $r ) use ( $status, $search ) {
				if ( 'all' !== $status && $r['status'] !== $status ) {
					return false;
				}
				if ( '' === $search ) {
					return true;
				}
				$hay = strtolower( implode( ' ', $r['fields'] ) );
				return false !== strpos( $hay, strtolower( $search ) );
			}
		)
	);
}

// -----------------------------------------------------------------------------
// Menu + assets
// -----------------------------------------------------------------------------

add_action(
	'admin_menu',
	static function () {
		$counts = triple5_quote_counts( triple5_quote_all_rows() );
		$new    = $counts['new'];
		$bubble = $new > 0 ? ' <span class="update-plugins count-' . $new . '"><span class="plugin-count">' . number_format_i18n( $new ) . '</span></span>' : '';

		add_menu_page(
			'Quote Requests',
			'Quote Requests' . $bubble,
			triple5_quote_cap(),
			triple5_quote_slug(),
			'triple5_quote_render_page',
			'dashicons-clipboard',
			26
		);
	}
);

add_action(
	'admin_enqueue_scripts',
	static function ( $hook ) {
		if ( 'toplevel_page_' . triple5_quote_slug() === $hook || 'index.php' === $hook ) {
			wp_enqueue_style(
				'triple5-quote-admin',
				triple5_hero_url( 'admin.css' ),
				array(),
				TRIPLE5_HERO_VERSION
			);
		}
	}
);

// -----------------------------------------------------------------------------
// Status change (PRG: process on admin_init, then redirect back)
// -----------------------------------------------------------------------------

add_action(
	'admin_init',
	static function () {
		if ( ! isset( $_POST['t5_status_update'] ) ) {
			return;
		}
		if ( ! current_user_can( triple5_quote_cap() ) ) {
			wp_die( 'Not allowed.' );
		}
		check_admin_referer( 't5_status_update', 't5_status_nonce' );

		$id  = isset( $_POST['t5_lead_id'] ) ? absint( $_POST['t5_lead_id'] ) : 0;
		$new = isset( $_POST['t5_new_status'] ) ? sanitize_key( wp_unslash( $_POST['t5_new_status'] ) ) : '';

		if ( $id && isset( triple5_hero_statuses()[ $new ] ) && 't5_lead' === get_post_type( $id ) ) {
			update_post_meta( $id, 't5_status', $new );
		}

		$back = isset( $_POST['t5_return'] ) ? esc_url_raw( wp_unslash( $_POST['t5_return'] ) ) : '';
		if ( '' === $back ) {
			$back = admin_url( 'admin.php?page=' . triple5_quote_slug() );
		}
		wp_safe_redirect( add_query_arg( 'updated', '1', $back ) );
		exit;
	}
);

// -----------------------------------------------------------------------------
// Trash a request (native WP trash — recoverable for 30 days). Nonce'd GET link
// following the standard wp-admin row-action pattern; processed on admin_init.
// -----------------------------------------------------------------------------

add_action(
	'admin_init',
	static function () {
		if ( ! isset( $_GET['t5delete'] ) ) {
			return;
		}
		if ( ! current_user_can( triple5_quote_cap() ) ) {
			wp_die( 'Not allowed.' );
		}
		$id = absint( $_GET['t5delete'] );
		check_admin_referer( 't5_trash_' . $id );

		if ( $id && 't5_lead' === get_post_type( $id ) ) {
			wp_trash_post( $id );
		}
		wp_safe_redirect( add_query_arg( 'trashed', '1', admin_url( 'admin.php?page=' . triple5_quote_slug() ) ) );
		exit;
	}
);

/** Nonce'd trash URL for a given request. */
function triple5_quote_trash_url( $id ) {
	return wp_nonce_url(
		add_query_arg(
			array(
				'page'     => triple5_quote_slug(),
				't5delete' => $id,
			),
			admin_url( 'admin.php' )
		),
		't5_trash_' . $id
	);
}

// -----------------------------------------------------------------------------
// Notes — timestamped log entries per request (stored as the `t5_notes` meta
// array: {text, time (UTC), author}). Added from the detail view; PRG redirect.
// -----------------------------------------------------------------------------

add_action(
	'admin_init',
	static function () {
		if ( ! isset( $_POST['t5_add_note'] ) ) {
			return;
		}
		if ( ! current_user_can( triple5_quote_cap() ) ) {
			wp_die( 'Not allowed.' );
		}
		check_admin_referer( 't5_add_note', 't5_note_nonce' );

		$id   = isset( $_POST['t5_lead_id'] ) ? absint( $_POST['t5_lead_id'] ) : 0;
		$text = isset( $_POST['t5_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['t5_note'] ) ) : '';

		if ( $id && 't5_lead' === get_post_type( $id ) && '' !== trim( $text ) ) {
			$notes = get_post_meta( $id, 't5_notes', true );
			if ( ! is_array( $notes ) ) {
				$notes = array();
			}
			$user    = wp_get_current_user();
			$notes[] = array(
				'text'   => $text,
				'time'   => time(), // UTC; displayed in site timezone via wp_date().
				'author' => $user ? $user->display_name : '',
			);
			update_post_meta( $id, 't5_notes', $notes );
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'  => triple5_quote_slug(),
					't5req' => $id,
					'noted' => '1',
				),
				admin_url( 'admin.php' )
			) . '#t5q-notes'
		);
		exit;
	}
);

/** Render the Notes card (existing entries newest-first + add form). */
function triple5_quote_render_notes( $id ) {
	$notes = get_post_meta( $id, 't5_notes', true );
	if ( ! is_array( $notes ) ) {
		$notes = array();
	}

	$out  = '<div class="t5q-card t5q-notes" id="t5q-notes">';
	$out .= '<h2>Notes</h2>';

	if ( $notes ) {
		$out .= '<ul class="t5q-note-list">';
		foreach ( array_reverse( $notes ) as $n ) {
			$when   = isset( $n['time'] ) ? wp_date( 'M j, Y \a\t g:i a', (int) $n['time'] ) : '';
			$author = ! empty( $n['author'] ) ? ' <span class="t5q-note-author">— ' . esc_html( $n['author'] ) . '</span>' : '';
			$out   .= '<li>'
				. '<div class="t5q-note-meta"><span class="t5q-note-time">' . esc_html( $when ) . '</span>' . $author . '</div>'
				. '<div class="t5q-note-text">' . nl2br( esc_html( isset( $n['text'] ) ? $n['text'] : '' ) ) . '</div>'
				. '</li>';
		}
		$out .= '</ul>';
	} else {
		$out .= '<p class="t5q-note-empty">No notes yet — add the first one below.</p>';
	}

	$out .= '<form method="post" class="t5q-note-form">'
		. '<input type="hidden" name="t5_add_note" value="1">'
		. '<input type="hidden" name="t5_lead_id" value="' . esc_attr( $id ) . '">'
		. wp_nonce_field( 't5_add_note', 't5_note_nonce', true, false )
		. '<textarea name="t5_note" rows="3" placeholder="Add a note… (e.g. Left voicemail; scheduled site visit Thursday 2pm)" required></textarea>'
		. '<button class="button button-primary">Add note</button>'
		. '</form>';

	$out .= '</div>';
	return $out;
}

// -----------------------------------------------------------------------------
// Render helpers
// -----------------------------------------------------------------------------

/** A status <select> inside its own form (auto-submits on change). */
function triple5_quote_status_form( $id, $current, $return_url ) {
	$out  = '<form method="post" class="t5q-statusform">';
	$out .= '<input type="hidden" name="t5_status_update" value="1">';
	$out .= '<input type="hidden" name="t5_lead_id" value="' . esc_attr( $id ) . '">';
	$out .= '<input type="hidden" name="t5_return" value="' . esc_attr( $return_url ) . '">';
	$out .= wp_nonce_field( 't5_status_update', 't5_status_nonce', true, false );
	$out .= '<select name="t5_new_status" class="t5q-status is-' . esc_attr( $current ) . '" onchange="this.form.submit()">';
	foreach ( triple5_hero_statuses() as $key => $label ) {
		$out .= '<option value="' . esc_attr( $key ) . '"' . selected( $current, $key, false ) . '>' . esc_html( $label ) . '</option>';
	}
	$out .= '</select><noscript><button class="button">Update</button></noscript></form>';
	return $out;
}

/** Small colored status pill (read-only display). */
function triple5_quote_status_pill( $status ) {
	$labels = triple5_hero_statuses();
	$label  = isset( $labels[ $status ] ) ? $labels[ $status ] : ucfirst( $status );
	return '<span class="t5q-pill is-' . esc_attr( $status ) . '">' . esc_html( $label ) . '</span>';
}

// -----------------------------------------------------------------------------
// Page render
// -----------------------------------------------------------------------------

function triple5_quote_render_page() {
	if ( ! current_user_can( triple5_quote_cap() ) ) {
		wp_die( 'Not allowed.' );
	}

	// Detail view?
	$detail_id = isset( $_GET['t5req'] ) ? absint( $_GET['t5req'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $detail_id ) {
		triple5_quote_render_detail( $detail_id );
		return;
	}

	$rows   = triple5_quote_all_rows();
	$counts = triple5_quote_counts( $rows );

	$status = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : 'all'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'all' !== $status && ! isset( triple5_hero_statuses()[ $status ] ) ) {
		$status = 'all';
	}
	$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$filtered = triple5_quote_filter( $rows, $status, $search );

	// Pagination.
	$per   = triple5_quote_per_page();
	$paged = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$total = count( $filtered );
	$pages = max( 1, (int) ceil( $total / $per ) );
	$paged = min( $paged, $pages );
	$page_rows = array_slice( $filtered, ( $paged - 1 ) * $per, $per );

	$base_url   = admin_url( 'admin.php?page=' . triple5_quote_slug() );
	$return_url = add_query_arg(
		array_filter(
			array(
				'page'   => triple5_quote_slug(),
				'status' => 'all' !== $status ? $status : null,
				's'      => '' !== $search ? $search : null,
				'paged'  => $paged > 1 ? $paged : null,
			)
		),
		admin_url( 'admin.php' )
	);

	$export_url = wp_nonce_url(
		add_query_arg(
			array_filter(
				array(
					'action' => 't5_leads_export',
					'status' => 'all' !== $status ? $status : null,
					's'      => '' !== $search ? $search : null,
				)
			),
			admin_url( 'admin-post.php' )
		),
		't5_export',
		't5_export_nonce'
	);

	echo '<div class="wrap t5q-wrap">';
	echo '<div class="t5q-head"><h1>Quote Requests</h1>';
	echo '<a class="button button-primary t5q-export" href="' . esc_url( $export_url ) . '">Export CSV</a></div>';

	if ( isset( $_GET['updated'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<div class="notice notice-success is-dismissible"><p>Request updated.</p></div>';
	}
	if ( isset( $_GET['trashed'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<div class="notice notice-success is-dismissible"><p>Quote request moved to Trash.</p></div>';
	}

	// Tabs.
	echo '<nav class="t5q-tabs">';
	$tabs = array_merge( array( 'all' => 'All' ), triple5_hero_statuses() );
	foreach ( $tabs as $key => $label ) {
		$url    = add_query_arg(
			array_filter(
				array(
					'page'   => triple5_quote_slug(),
					'status' => 'all' !== $key ? $key : null,
					's'      => '' !== $search ? $search : null,
				)
			),
			admin_url( 'admin.php' )
		);
		$active = ( $key === $status ) ? ' is-active' : '';
		$n      = isset( $counts[ $key ] ) ? (int) $counts[ $key ] : 0;
		echo '<a class="t5q-tab is-' . esc_attr( $key ) . $active . '" href="' . esc_url( $url ) . '">'
			. esc_html( $label ) . ' <span class="t5q-count">' . esc_html( $n ) . '</span></a>';
	}
	echo '</nav>';

	// Search.
	echo '<form class="t5q-search" method="get"><input type="hidden" name="page" value="' . esc_attr( triple5_quote_slug() ) . '">';
	if ( 'all' !== $status ) {
		echo '<input type="hidden" name="status" value="' . esc_attr( $status ) . '">';
	}
	echo '<input type="search" name="s" value="' . esc_attr( $search ) . '" placeholder="Search name, email, phone, message…">';
	echo '<button class="button">Search</button>';
	if ( '' !== $search ) {
		echo ' <a class="button-link" href="' . esc_url( add_query_arg( array_filter( array( 'page' => triple5_quote_slug(), 'status' => 'all' !== $status ? $status : null ) ), admin_url( 'admin.php' ) ) ) . '">Clear</a>';
	}
	echo '</form>';

	// Table.
	echo '<table class="wp-list-table widefat fixed striped t5q-table"><thead><tr>';
	echo '<th class="t5q-col-name">Name</th><th>Contact</th><th class="t5q-col-msg">Message</th><th class="t5q-col-when">Received</th><th class="t5q-col-status">Status</th>';
	echo '</tr></thead><tbody>';

	if ( empty( $page_rows ) ) {
		echo '<tr><td colspan="5" class="t5q-empty">No quote requests' . ( '' !== $search ? ' match your search' : ' yet' ) . '.</td></tr>';
	} else {
		foreach ( $page_rows as $r ) {
			$f          = $r['fields'];
			$detail_url = add_query_arg(
				array(
					'page'  => triple5_quote_slug(),
					't5req' => $r['id'],
				),
				admin_url( 'admin.php' )
			);
			$msg = $f['t5_message'];
			if ( strlen( $msg ) > 90 ) {
				$msg = substr( $msg, 0, 90 ) . '…';
			}

			$trash_url = triple5_quote_trash_url( $r['id'] );
			echo '<tr>';
			echo '<td class="t5q-col-name"><a class="row-title" href="' . esc_url( $detail_url ) . '"><strong>' . esc_html( $f['t5_name'] !== '' ? $f['t5_name'] : '(no name)' ) . '</strong></a>'
				. '<div class="row-actions">'
				. '<span class="view"><a href="' . esc_url( $detail_url ) . '">View</a> | </span>'
				. '<span class="trash"><a class="submitdelete" href="' . esc_url( $trash_url ) . '" onclick="return confirm(\'Move this quote request to Trash?\');">Trash</a></span>'
				. '</div></td>';
			echo '<td>';
			if ( '' !== $f['t5_email'] ) {
				echo '<a href="mailto:' . esc_attr( $f['t5_email'] ) . '">' . esc_html( $f['t5_email'] ) . '</a><br>';
			}
			echo '<span class="t5q-phone">' . esc_html( $f['t5_phone'] ) . '</span></td>';
			echo '<td class="t5q-col-msg">' . esc_html( $msg ) . '</td>';
			echo '<td class="t5q-col-when">' . esc_html( $r['date'] ) . '</td>';
			echo '<td class="t5q-col-status">' . triple5_quote_status_form( $r['id'], $r['status'], $return_url ) . '</td>';
			echo '</tr>';
		}
	}
	echo '</tbody></table>';

	// Pagination.
	if ( $pages > 1 ) {
		echo '<div class="tablenav"><div class="tablenav-pages">';
		echo '<span class="displaying-num">' . esc_html( number_format_i18n( $total ) ) . ' items</span> ';
		for ( $i = 1; $i <= $pages; $i++ ) {
			$url = add_query_arg(
				array_filter(
					array(
						'page'   => triple5_quote_slug(),
						'status' => 'all' !== $status ? $status : null,
						's'      => '' !== $search ? $search : null,
						'paged'  => $i > 1 ? $i : null,
					)
				),
				admin_url( 'admin.php' )
			);
			$cls = $i === $paged ? ' class="button button-primary"' : ' class="button"';
			echo '<a' . $cls . ' href="' . esc_url( $url ) . '">' . esc_html( $i ) . '</a> ';
		}
		echo '</div></div>';
	}

	echo '</div>';
}

/** Detail view for a single request. */
function triple5_quote_render_detail( $id ) {
	if ( 't5_lead' !== get_post_type( $id ) ) {
		echo '<div class="wrap"><h1>Quote request</h1><p>Not found.</p></div>';
		return;
	}

	$labels = triple5_hero_lead_fields();
	$status = (string) get_post_meta( $id, 't5_status', true );
	if ( ! isset( triple5_hero_statuses()[ $status ] ) ) {
		$status = triple5_hero_default_status();
	}
	$source     = (string) get_post_meta( $id, 't5_source_url', true );
	$list_url   = admin_url( 'admin.php?page=' . triple5_quote_slug() );
	$return_url = add_query_arg( array( 'page' => triple5_quote_slug(), 't5req' => $id ), admin_url( 'admin.php' ) );

	echo '<div class="wrap t5q-wrap t5q-detail">';
	echo '<div class="t5q-head"><h1>Quote request</h1>';
	echo '<div class="t5q-head-actions">';
	echo '<a class="button" href="' . esc_url( $list_url ) . '">&larr; Back to all requests</a> ';
	echo '<a class="button t5q-trash-btn" href="' . esc_url( triple5_quote_trash_url( $id ) ) . '" onclick="return confirm(\'Move this quote request to Trash?\');">Trash request</a>';
	echo '</div></div>';

	if ( isset( $_GET['updated'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<div class="notice notice-success is-dismissible"><p>Request updated.</p></div>';
	}
	if ( isset( $_GET['trashed'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<div class="notice notice-success is-dismissible"><p>Quote request moved to Trash.</p></div>';
	}
	if ( isset( $_GET['noted'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<div class="notice notice-success is-dismissible"><p>Note added.</p></div>';
	}

	echo '<div class="t5q-detail-grid">';
	echo '<div class="t5q-card">';
	echo '<table class="t5q-detail-table"><tbody>';
	foreach ( $labels as $key => $label ) {
		$val = (string) get_post_meta( $id, $key, true );
		if ( 't5_email' === $key && '' !== $val ) {
			$val = '<a href="mailto:' . esc_attr( $val ) . '">' . esc_html( $val ) . '</a>';
		} else {
			$val = nl2br( esc_html( '' !== $val ? $val : '—' ) );
		}
		echo '<tr><th>' . esc_html( $label ) . '</th><td>' . $val . '</td></tr>';
	}
	echo '<tr><th>Received</th><td>' . esc_html( get_the_date( 'F j, Y \a\t g:i a', $id ) ) . '</td></tr>';
	if ( '' !== $source ) {
		echo '<tr><th>Source page</th><td><a href="' . esc_url( $source ) . '">' . esc_html( $source ) . '</a></td></tr>';
	}
	echo '</tbody></table></div>';

	echo '<div class="t5q-card t5q-side">';
	echo '<h2>Status</h2>';
	echo '<p>' . triple5_quote_status_pill( $status ) . '</p>';
	echo triple5_quote_status_form( $id, $status, $return_url );
	echo '</div>';
	echo '</div>'; // close .t5q-detail-grid

	echo triple5_quote_render_notes( $id );

	echo '</div>'; // close .wrap
}

// -----------------------------------------------------------------------------
// CSV export
// -----------------------------------------------------------------------------

add_action(
	'admin_post_t5_leads_export',
	static function () {
		if ( ! current_user_can( triple5_quote_cap() ) ) {
			wp_die( 'Not allowed.' );
		}
		check_admin_referer( 't5_export', 't5_export_nonce' );

		$status = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : 'all';
		if ( 'all' !== $status && ! isset( triple5_hero_statuses()[ $status ] ) ) {
			$status = 'all';
		}
		$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

		$rows   = triple5_quote_filter( triple5_quote_all_rows(), $status, $search );
		$labels = triple5_hero_lead_fields();

		$filename = 'triple5-quote-requests-' . $status . '-' . gmdate( 'Ymd' ) . '.csv';
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $filename );

		$out = fopen( 'php://output', 'w' );
		$header = array_merge( array( 'Received', 'Status' ), array_values( $labels ), array( 'Source' ) );
		fputcsv( $out, $header );

		$status_labels = triple5_hero_statuses();
		foreach ( $rows as $r ) {
			$line = array(
				$r['date'],
				isset( $status_labels[ $r['status'] ] ) ? $status_labels[ $r['status'] ] : $r['status'],
			);
			foreach ( array_keys( $labels ) as $key ) {
				$line[] = $r['fields'][ $key ];
			}
			$line[] = $r['source'];
			fputcsv( $out, $line );
		}
		fclose( $out );
		exit;
	}
);

// -----------------------------------------------------------------------------
// Dashboard summary widget
// -----------------------------------------------------------------------------

add_action(
	'wp_dashboard_setup',
	static function () {
		if ( ! current_user_can( triple5_quote_cap() ) ) {
			return;
		}
		wp_add_dashboard_widget( 't5_quote_widget', 'Quote Requests', 'triple5_quote_dashboard_widget' );
	}
);

function triple5_quote_dashboard_widget() {
	$rows   = triple5_quote_all_rows();
	$counts = triple5_quote_counts( $rows );
	$base   = admin_url( 'admin.php?page=' . triple5_quote_slug() );

	echo '<div class="t5q-widget">';
	echo '<ul class="t5q-widget-counts">';
	foreach ( array_merge( array( 'all' => 'All' ), triple5_hero_statuses() ) as $key => $label ) {
		$url = 'all' === $key ? $base : add_query_arg( 'status', $key, $base );
		echo '<li class="is-' . esc_attr( $key ) . '"><a href="' . esc_url( $url ) . '"><span class="t5q-widget-num">' . esc_html( (int) $counts[ $key ] ) . '</span><span class="t5q-widget-lbl">' . esc_html( $label ) . '</span></a></li>';
	}
	echo '</ul>';

	$recent = array_slice( $rows, 0, 5 );
	if ( $recent ) {
		echo '<h3 class="t5q-widget-h">Latest</h3><ul class="t5q-widget-recent">';
		foreach ( $recent as $r ) {
			$url = add_query_arg( array( 'page' => triple5_quote_slug(), 't5req' => $r['id'] ), admin_url( 'admin.php' ) );
			echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $r['fields']['t5_name'] !== '' ? $r['fields']['t5_name'] : '(no name)' ) . '</a> '
				. triple5_quote_status_pill( $r['status'] )
				. '<span class="t5q-widget-date">' . esc_html( $r['date'] ) . '</span></li>';
		}
		echo '</ul>';
	} else {
		echo '<p>No quote requests yet.</p>';
	}
	echo '<p class="t5q-widget-foot"><a href="' . esc_url( $base ) . '">View all quote requests →</a></p>';
	echo '</div>';
}
