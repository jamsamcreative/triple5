<?php
/**
 * Triple 5 Project Gallery — Cornerstone element definition.
 *
 * Centered eyebrow / heading / subtext, category filter pills (from Projects →
 * Categories), and a card grid of published Projects with a lightbox. Content is
 * managed under wp-admin → Projects; this element only controls the header copy,
 * layout and which categories/how many projects to include.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$triple5_projects_values = cs_compose_values(
	array(
		't5g_eyebrow'      => cs_value( 'Our work', 'markup', true ),
		't5g_heading'      => cs_value( "Work we're proud to sign", 'markup', true ),
		't5g_subtext'      => cs_value( "Before-and-after from completed jobs across Spokane and Coeur d'Alene.", 'markup', true ),
		't5g_show_filters' => cs_value( true, 'markup', true ),
		't5g_all_label'    => cs_value( 'All', 'markup', true ),
		't5g_columns'      => cs_value( '3', 'markup', true ),
		't5g_limit'        => cs_value( '0', 'markup', true ),
		't5g_categories'   => cs_value( '', 'markup', true ),
	)
);

/** Element data → plain config (what render.php needs at runtime). */
function triple5_projects_config_from_data( $data ) {
	$cats = array_values( array_filter( array_map( 'sanitize_title', preg_split( '/[\s,]+/', (string) $data['t5g_categories'] ) ) ) );
	return array(
		'eyebrow'      => trim( (string) $data['t5g_eyebrow'] ),
		'heading'      => trim( (string) $data['t5g_heading'] ),
		'subtext'      => trim( (string) $data['t5g_subtext'] ),
		'show_filters' => ! empty( $data['t5g_show_filters'] ),
		'all_label'    => '' !== trim( (string) $data['t5g_all_label'] ) ? trim( (string) $data['t5g_all_label'] ) : 'All',
		'columns'      => in_array( (string) $data['t5g_columns'], array( '2', '3', '4' ), true ) ? (string) $data['t5g_columns'] : '3',
		'limit'        => max( 0, (int) $data['t5g_limit'] ),
		'categories'   => $cats,
	);
}

function triple5_projects_render( $data ) {
	$config  = triple5_projects_config_from_data( $data );
	$payload = bin2hex( wp_json_encode( $config ) );
	return '<div class="t5-gallery-mount" data-t5-gallery="' . esc_attr( $payload ) . '">'
		. triple5_projects_render_section( $config )
		. '</div>';
}

function triple5_projects_builder_setup() {
	return cs_compose_controls(
		array(
			'controls'    => array(
				array(
					'type'     => 'group',
					'group'    => 't5-gallery:header',
					'controls' => array(
						array( 'key' => 't5g_eyebrow', 'type' => 'text', 'label' => 'Eyebrow' ),
						array( 'key' => 't5g_heading', 'type' => 'text', 'label' => 'Heading' ),
						array( 'key' => 't5g_subtext', 'type' => 'textarea', 'label' => 'Subtext', 'options' => array( 'height' => 2 ) ),
					),
				),
				array(
					'type'     => 'group',
					'group'    => 't5-gallery:grid',
					'controls' => array(
						array( 'key' => 't5g_show_filters', 'type' => 'toggle', 'label' => 'Show category filter pills' ),
						array( 'key' => 't5g_all_label', 'type' => 'text', 'label' => '"All" pill label' ),
						array(
							'key'     => 't5g_columns',
							'type'    => 'choose',
							'label'   => 'Columns',
							'options' => array(
								'choices' => array(
									array( 'value' => '2', 'label' => '2' ),
									array( 'value' => '3', 'label' => '3' ),
									array( 'value' => '4', 'label' => '4' ),
								),
							),
						),
						array( 'key' => 't5g_limit', 'type' => 'text', 'label' => 'Max projects (0 = all)' ),
						array( 'key' => 't5g_categories', 'type' => 'text', 'label' => 'Only these category slugs (comma-separated; blank = all)' ),
					),
				),
			),
			'control_nav' => array(
				't5-gallery'        => 'Project Gallery',
				't5-gallery:header' => 'Header',
				't5-gallery:grid'   => 'Grid & filters',
			),
		)
	);
}

cs_register_element(
	't5-gallery',
	array(
		'title'   => 'Triple 5 Project Gallery',
		'values'  => $triple5_projects_values,
		'builder' => 'triple5_projects_builder_setup',
		'render'  => 'triple5_projects_render',
		'icon'    => 'native',
		'group'   => 'layout',
	)
);
