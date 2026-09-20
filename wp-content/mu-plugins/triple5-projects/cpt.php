<?php
/**
 * Triple 5 Projects — post type, taxonomy and project meta.
 *
 * Owner workflow in wp-admin → Projects:
 *   - Add New: title, featured image (the card photo), Project Category, and the
 *     "Project details" box: location, extra photos (media picker, multiple) and an
 *     optional video (YouTube link OR an MP4 picked/uploaded from the media library).
 *   - Projects → Categories: add/rename/reorder the filter pills freely.
 *
 * The gallery element reads all of this at render time, so publishing a project
 * updates every gallery on the site without touching Cornerstone.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function triple5_projects_post_type() {
	return 't5_project';
}

function triple5_projects_taxonomy() {
	return 't5_project_cat';
}

// -----------------------------------------------------------------------------
// Registration
// -----------------------------------------------------------------------------

add_action(
	'init',
	static function () {
		register_post_type(
			triple5_projects_post_type(),
			array(
				'labels'       => array(
					'name'               => 'Projects',
					'singular_name'      => 'Project',
					'add_new_item'       => 'Add New Project',
					'edit_item'          => 'Edit Project',
					'new_item'           => 'New Project',
					'all_items'          => 'All Projects',
					'search_items'       => 'Search Projects',
					'not_found'          => 'No projects yet. Add one and give it a featured image and a category.',
					'featured_image'     => 'Card photo',
					'set_featured_image' => 'Set card photo',
				),
				'public'       => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-format-gallery',
				'menu_position' => 27,
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
				'has_archive'  => false,
				'rewrite'      => array( 'slug' => 'project', 'with_front' => false ),
			)
		);

		register_taxonomy(
			triple5_projects_taxonomy(),
			triple5_projects_post_type(),
			array(
				'labels'            => array(
					'name'          => 'Project Categories',
					'singular_name' => 'Project Category',
					'add_new_item'  => 'Add New Category',
					'menu_name'     => 'Categories',
				),
				'hierarchical'      => true,
				'public'            => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => 'projects', 'with_front' => false ),
			)
		);

		// Meta (exposed to REST so the block editor / future tooling can read it).
		foreach ( array( 't5p_location' => 'string', 't5p_video' => 'string', 't5p_photos' => 'string' ) as $key => $type ) {
			register_post_meta(
				triple5_projects_post_type(),
				$key,
				array(
					'type'          => $type,
					'single'        => true,
					'show_in_rest'  => true,
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback' => static function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
);

// -----------------------------------------------------------------------------
// Project details meta box
// -----------------------------------------------------------------------------

add_action(
	'add_meta_boxes',
	static function () {
		add_meta_box( 't5p_details', 'Project details', 'triple5_projects_meta_box', triple5_projects_post_type(), 'normal', 'high' );
	}
);

function triple5_projects_meta_box( $post ) {
	wp_nonce_field( 't5p_details_save', 't5p_details_nonce' );
	$location = get_post_meta( $post->ID, 't5p_location', true );
	$video    = get_post_meta( $post->ID, 't5p_video', true );
	$photos   = get_post_meta( $post->ID, 't5p_photos', true ); // comma-separated attachment IDs
	$ids      = array_filter( array_map( 'absint', explode( ',', (string) $photos ) ) );
	?>
	<style>
		.t5p-field { margin: 0 0 18px; }
		.t5p-field label { display: block; font-weight: 600; margin-bottom: 6px; }
		.t5p-field .description { margin-top: 6px; }
		.t5p-photos { display: flex; flex-wrap: wrap; gap: 8px; margin: 8px 0; }
		.t5p-photos li { position: relative; margin: 0; }
		.t5p-photos img { display: block; width: 96px; height: 72px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
		.t5p-photos button { position: absolute; top: -6px; right: -6px; width: 20px; height: 20px; border-radius: 50%; border: 0; background: #8E231E; color: #fff; font-size: 12px; line-height: 20px; cursor: pointer; padding: 0; }
	</style>

	<div class="t5p-field">
		<label for="t5p_location">Location</label>
		<input type="text" id="t5p_location" name="t5p_location" class="regular-text" value="<?php echo esc_attr( $location ); ?>" placeholder="Spokane Valley, WA">
		<p class="description">Shown under the project title with a pin icon.</p>
	</div>

	<div class="t5p-field">
		<label>Extra photos</label>
		<ul class="t5p-photos" id="t5p_photos_list">
			<?php foreach ( $ids as $id ) : ?>
				<li data-id="<?php echo (int) $id; ?>"><?php echo wp_get_attachment_image( $id, 'thumbnail' ); ?><button type="button" aria-label="Remove">×</button></li>
			<?php endforeach; ?>
		</ul>
		<input type="hidden" id="t5p_photos" name="t5p_photos" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>">
		<button type="button" class="button" id="t5p_photos_add">Add photos</button>
		<p class="description">Opens in the lightbox after the card photo. Select several at once; drag to reorder isn't needed — they show in the order added.</p>
	</div>

	<div class="t5p-field">
		<label for="t5p_video">Video (optional)</label>
		<input type="text" id="t5p_video" name="t5p_video" class="large-text" value="<?php echo esc_attr( $video ); ?>" placeholder="https://youtu.be/… or an MP4 from the media library">
		<button type="button" class="button" id="t5p_video_pick" style="margin-top:6px">Choose / upload video</button>
		<p class="description">Paste a YouTube link, or pick an MP4 you've uploaded to the Media Library. The card shows a play badge and the lightbox plays it.</p>
	</div>

	<script>
	(function ($) {
		var frame, videoFrame;
		function syncIds() {
			var ids = $('#t5p_photos_list li').map(function () { return $(this).data('id'); }).get();
			$('#t5p_photos').val(ids.join(','));
		}
		$('#t5p_photos_add').on('click', function (e) {
			e.preventDefault();
			frame = frame || wp.media({ title: 'Add project photos', multiple: true, library: { type: 'image' }, button: { text: 'Add to project' } });
			frame.off('select').on('select', function () {
				frame.state().get('selection').each(function (att) {
					var a = att.toJSON(), thumb = (a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url);
					if ($('#t5p_photos_list li[data-id="' + a.id + '"]').length) { return; }
					$('#t5p_photos_list').append('<li data-id="' + a.id + '"><img src="' + thumb + '" alt=""><button type="button" aria-label="Remove">×</button></li>');
				});
				syncIds();
			});
			frame.open();
		});
		$('#t5p_photos_list').on('click', 'button', function () { $(this).closest('li').remove(); syncIds(); });
		$('#t5p_video_pick').on('click', function (e) {
			e.preventDefault();
			videoFrame = videoFrame || wp.media({ title: 'Choose a video', multiple: false, library: { type: 'video' }, button: { text: 'Use this video' } });
			videoFrame.off('select').on('select', function () {
				$('#t5p_video').val(videoFrame.state().get('selection').first().toJSON().url);
			});
			videoFrame.open();
		});
	})(jQuery);
	</script>
	<?php
}

add_action(
	'admin_enqueue_scripts',
	static function ( $hook ) {
		if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && get_post_type() === triple5_projects_post_type() ) {
			wp_enqueue_media();
		}
	}
);

add_action(
	'save_post_' . triple5_projects_post_type(),
	static function ( $post_id ) {
		if ( ! isset( $_POST['t5p_details_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['t5p_details_nonce'] ) ), 't5p_details_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		update_post_meta( $post_id, 't5p_location', sanitize_text_field( wp_unslash( $_POST['t5p_location'] ?? '' ) ) );
		update_post_meta( $post_id, 't5p_video', esc_url_raw( trim( (string) wp_unslash( $_POST['t5p_video'] ?? '' ) ) ) );
		$ids = array_filter( array_map( 'absint', explode( ',', (string) wp_unslash( $_POST['t5p_photos'] ?? '' ) ) ) );
		update_post_meta( $post_id, 't5p_photos', implode( ',', $ids ) );
	}
);

// -----------------------------------------------------------------------------
// Query helpers (used by render.php)
// -----------------------------------------------------------------------------

/** Categories in the order set in wp-admin (term order is alphabetical unless a plugin adds ordering; menu_order-like "description" hack avoided). */
function triple5_projects_categories() {
	$terms = get_terms(
		array(
			'taxonomy'   => triple5_projects_taxonomy(),
			'hide_empty' => true,
			'orderby'    => 'name',
		)
	);
	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Normalized projects for display.
 * @param array $args { limit int (0 = all), categories string[] slugs (empty = all) }
 */
function triple5_projects_items( array $args = array() ) {
	$args  = wp_parse_args( $args, array( 'limit' => 0, 'categories' => array() ) );
	$query = array(
		'post_type'      => triple5_projects_post_type(),
		'post_status'    => 'publish',
		'posts_per_page' => $args['limit'] > 0 ? (int) $args['limit'] : -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	);
	if ( ! empty( $args['categories'] ) ) {
		$query['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array( 'taxonomy' => triple5_projects_taxonomy(), 'field' => 'slug', 'terms' => $args['categories'] ),
		);
	}

	$out = array();
	foreach ( get_posts( $query ) as $p ) {
		$terms = get_the_terms( $p, triple5_projects_taxonomy() );
		$terms = is_array( $terms ) ? $terms : array();
		$thumb = get_post_thumbnail_id( $p );
		$ids   = array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( $p->ID, 't5p_photos', true ) ) ) );
		$photos = array();
		foreach ( array_merge( $thumb ? array( $thumb ) : array(), $ids ) as $id ) {
			$full = wp_get_attachment_image_src( $id, 'full' );
			if ( $full ) {
				$photos[] = array( 'src' => $full[0], 'w' => $full[1], 'h' => $full[2], 'alt' => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
			}
		}
		$out[] = array(
			'id'        => $p->ID,
			'title'     => get_the_title( $p ),
			'excerpt'   => has_excerpt( $p ) ? get_the_excerpt( $p ) : '',
			'location'  => (string) get_post_meta( $p->ID, 't5p_location', true ),
			'video'     => (string) get_post_meta( $p->ID, 't5p_video', true ),
			'cats'      => array_map( static fn( $t ) => array( 'slug' => $t->slug, 'name' => $t->name ), $terms ),
			'card'      => $thumb ? ( wp_get_attachment_image_src( $thumb, 'large' )[0] ?? '' ) : '',
			'photos'    => $photos,
		);
	}
	return $out;
}
