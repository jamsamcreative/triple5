<?php
/**
 * Triple 5 FAQ — Cornerstone element definition.
 *
 * Heading (left) + intro copy (right), then a two-column accordion of up to 8
 * question/answer items. Items use native <details>/<summary> so they work with
 * no JavaScript and survive Cornerstone's bake; the +/– glyph is CSS. Items with
 * an empty question are skipped. Emits FAQPage JSON-LD for the visible items so
 * the questions are eligible for rich results.
 *
 * @package triple5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Q&A slots and their Triple 5 defaults: [question, answer]. */
function triple5_faq_item_defaults() {
	return array(
		1 => array( 'What areas do you cover?', "We serve Spokane, Spokane Valley, Mead and the surrounding area in Washington, plus Coeur d'Alene, Post Falls, Hayden and Rathdrum in North Idaho." ),
		2 => array( 'Do you offer free estimates?', 'Yes — every on-site estimate is free and no-pressure. We measure, walk your options, and give you a clear written quote.' ),
		3 => array( 'What metal roofing types do you install?', 'Standing seam, exposed-fastener corrugated and ribbed panels, and metal shingle systems in steel, aluminum and copper — matched to your budget and the look you want.' ),
		4 => array( 'Are you licensed and insured?', 'Yes. Triple 5 Construction LLC is a licensed and insured general contractor in both Washington and Idaho.' ),
		5 => array( 'Do you handle storm and insurance work?', "We assess wind, hail and snow-load damage, document it, and provide insurance-ready repair estimates. We'll work alongside your adjuster." ),
		6 => array( 'Do you do more than roofing?', 'Yes — metal siding, gutters, concrete and framing, and light commercial and agricultural buildings. One licensed crew for the whole project.' ),
		7 => array( '', '' ),
		8 => array( '', '' ),
	);
}

// -----------------------------------------------------------------------------
// Values (defaults)
// -----------------------------------------------------------------------------

$triple5_faq_vals = array(
	't5f_heading'    => cs_value( "Frequently asked\nquestions", 'markup', true ),
	't5f_intro'      => cs_value( "Find answers to the questions homeowners ask us most about metal roofing, siding and repairs across Spokane and Coeur d'Alene.", 'markup', true ),
	't5f_open_index' => cs_value( '2', 'markup', true ),
	't5f_columns'    => cs_value( '2', 'markup', true ),
	't5f_schema'     => cs_value( true, 'markup', true ),
);
foreach ( triple5_faq_item_defaults() as $i => $qa ) {
	$triple5_faq_vals[ "t5f_q{$i}" ] = cs_value( $qa[0], 'markup', true );
	$triple5_faq_vals[ "t5f_a{$i}" ] = cs_value( $qa[1], 'markup', true );
}
$triple5_faq_values = cs_compose_values( $triple5_faq_vals );

// -----------------------------------------------------------------------------
// Render
// -----------------------------------------------------------------------------

function triple5_faq_render( $data ) {
	$heading = trim( (string) $data['t5f_heading'] );
	$intro   = trim( (string) $data['t5f_intro'] );
	$open    = (int) $data['t5f_open_index'];
	$cols    = in_array( (string) $data['t5f_columns'], array( '1', '2' ), true ) ? (string) $data['t5f_columns'] : '2';

	$items  = '';
	$schema = array();
	foreach ( array_keys( triple5_faq_item_defaults() ) as $i ) {
		$q = isset( $data[ "t5f_q{$i}" ] ) ? trim( (string) $data[ "t5f_q{$i}" ] ) : '';
		$a = isset( $data[ "t5f_a{$i}" ] ) ? trim( (string) $data[ "t5f_a{$i}" ] ) : '';
		if ( '' === $q ) {
			continue;
		}
		$is_open = ( $i === $open );
		$items  .= '<details class="t5-faq__item"' . ( $is_open ? ' open' : '' ) . '>'
			. '<summary class="t5-faq__q"><span class="t5-faq__q-text">' . esc_html( $q ) . '</span><span class="t5-faq__toggle" aria-hidden="true"></span></summary>'
			. '<div class="t5-faq__a">' . wpautop( esc_html( $a ) ) . '</div>'
			. '</details>';
		if ( '' !== $a ) {
			$schema[] = array(
				'@type'          => 'Question',
				'name'           => $q,
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $a,
				),
			);
		}
	}

	$json_ld = '';
	if ( ! empty( $data['t5f_schema'] ) && ! empty( $schema ) ) {
		$json_ld = '<script type="application/ld+json">' . wp_json_encode(
			array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => $schema,
			),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		) . '</script>';
	}

	return '<section class="t5-faq">'
		. '<div class="t5-faq__inner">'
		. '<div class="t5-faq__head">'
		. ( '' !== $heading ? '<h2 class="t5-faq__heading">' . nl2br( esc_html( $heading ) ) . '</h2>' : '' )
		. ( '' !== $intro ? '<p class="t5-faq__intro">' . esc_html( $intro ) . '</p>' : '' )
		. '</div>'
		. '<div class="t5-faq__list has-cols-' . $cols . '">' . $items . '</div>'
		. '</div>'
		. $json_ld
		. '</section>';
}

// -----------------------------------------------------------------------------
// Builder controls
// -----------------------------------------------------------------------------

function triple5_faq_builder_setup() {
	$open_choices = array( array( 'value' => '0', 'label' => 'None (all closed)' ) );
	foreach ( array_keys( triple5_faq_item_defaults() ) as $i ) {
		$open_choices[] = array( 'value' => (string) $i, 'label' => "Question {$i}" );
	}

	$groups = array(
		array(
			'type'     => 'group',
			'group'    => 't5-faq:header',
			'controls' => array(
				array(
					'key'     => 't5f_heading',
					'type'    => 'textarea',
					'label'   => 'Heading (line breaks kept)',
					'options' => array( 'height' => 2 ),
				),
				array(
					'key'     => 't5f_intro',
					'type'    => 'textarea',
					'label'   => 'Intro copy (right column)',
					'options' => array( 'height' => 3 ),
				),
				array(
					'key'     => 't5f_open_index',
					'type'    => 'select',
					'label'   => 'Open by default',
					'options' => array( 'choices' => $open_choices ),
				),
				array(
					'key'     => 't5f_columns',
					'type'    => 'choose',
					'label'   => 'Columns',
					'options' => array(
						'choices' => array(
							array( 'value' => '1', 'label' => '1' ),
							array( 'value' => '2', 'label' => '2' ),
						),
					),
				),
				array( 'key' => 't5f_schema', 'type' => 'toggle', 'label' => 'Output FAQPage schema (SEO rich results)' ),
			),
		),
	);

	$nav = array(
		't5-faq'        => 'FAQ',
		't5-faq:header' => 'Heading & intro',
	);

	foreach ( array_keys( triple5_faq_item_defaults() ) as $i ) {
		$group_key         = "t5-faq:q{$i}";
		$nav[ $group_key ] = "Question {$i}";
		$groups[]          = array(
			'type'     => 'group',
			'group'    => $group_key,
			'controls' => array(
				array( 'key' => "t5f_q{$i}", 'type' => 'text', 'label' => "Question {$i} (empty = hidden)" ),
				array(
					'key'     => "t5f_a{$i}",
					'type'    => 'textarea',
					'label'   => 'Answer (blank line = new paragraph)',
					'options' => array( 'height' => 5 ),
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
	't5-faq',
	array(
		'title'   => 'Triple 5 FAQ',
		'values'  => $triple5_faq_values,
		'builder' => 'triple5_faq_builder_setup',
		'render'  => 'triple5_faq_render',
		'icon'    => 'native',
		'group'   => 'layout',
	)
);
