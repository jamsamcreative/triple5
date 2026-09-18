<?php
/**
 * Build the five individual service pages as children of Our Services (ID 14).
 * Each page = t5-tabs (single panel: header band + intro) → t5-why → t5-faq.
 * Idempotent: re-running updates the existing page by slug.
 *
 * Run from the Local site root with WP-CLI: `wp eval-file <path>/build-service-pages.php`.
 * Run it TWICE in separate processes — the first bake of freshly-created documents
 * comes out empty (see PROJECT_CONTEXT.md, bake gotcha); the second is real.
 */
wp_set_current_user( 1 );

$parent   = 14;
$settings = get_post_meta( 10, '_cornerstone_settings', true );

$services = array(
	'roofing' => array(
		'title'   => 'Metal Roofing',
		'eyebrow' => 'Our services · Roofing',
		'h1'      => 'Metal roofing built for the Inland Northwest',
		'sub'     => 'Standing seam, corrugated and metal shingle systems in steel, aluminum and copper — engineered for snow load, freeze-thaw cycling and wildfire exposure.',
		'badge'   => 'Metal specialty',
		'heading' => 'Our flagship service',
		'body'    => "A metal roof is the last roof most homes need. We install concealed-fastener standing seam for a clean, weather-tight finish, exposed-fastener panels for shops and barns, and metal shingle systems where you want a traditional profile with metal's lifespan. Every install is detailed for our climate — ice-and-water at eaves and valleys, snow retention where it matters, and flashing done right the first time.",
		'list'    => "Standing seam (concealed fastener)\nCorrugated & exposed-fastener panel\nMetal shingle & stone-coated\nSteel, aluminum & copper\nRe-roofs over existing decking\nSnow retention & ice management",
		'cta'     => 'Request a metal roofing quote',
		'caption' => 'Metal Roofing — completed project',
		'why'     => array(
			array( 'snowflake', 'Engineered for snow load', 'Panel gauge, fastening and snow retention sized for Spokane and Coeur d\'Alene winters — not a national one-size spec.' ),
			array( 'shield-check', 'Wildfire-resistant', 'Class A fire-rated metal sheds embers instead of catching them — a real advantage on the urban-wildland edge.' ),
			array( 'clock', 'Decades, not years', 'A properly installed metal roof routinely outlasts two or three asphalt roofs, with far less maintenance.' ),
			array( 'house', 'One crew, start to finish', 'The same licensed crew that quotes your roof installs it and walks the final inspection with you.' ),
		),
		'faq'     => array(
			array( 'How long does a metal roof last?', 'Most steel and aluminum systems are rated for 40–60 years; copper and zinc longer. Fastener and sealant maintenance is minimal compared with asphalt.' ),
			array( 'Is metal roofing loud in the rain?', 'Not when installed over solid decking with underlayment, which is how we build every roof. Sound is comparable to an asphalt roof.' ),
			array( 'Can you install over my existing roof?', 'Often, yes — a single layer of asphalt in sound condition can be roofed over, saving tear-off cost. We confirm on the site visit.' ),
			array( 'What about snow sliding off?', 'We add snow guards or retention bars above entries, walkways and lower roofs so snow releases in a controlled way.' ),
		),
	),
	'siding' => array(
		'title'   => 'Metal Siding',
		'eyebrow' => 'Our services · Siding',
		'h1'      => 'Metal siding that matches your roof',
		'sub'     => 'Board-and-batten, vertical and horizontal metal panel siding — low maintenance, fire-resistant and color-matched to the roof above it.',
		'badge'   => 'Metal specialty',
		'heading' => 'Metal siding & exterior panels',
		'body'    => "Metal siding gives a home or shop the same durability we build into our roofs — no rot, no repainting cycle, and it shrugs off hail and wind. We install board-and-batten and vertical panel for a modern farmhouse look, horizontal lap and flush panel for a cleaner contemporary line, and finish every job with matching trim, flashing and soffit so the whole envelope reads as one system.",
		'list'    => "Board-and-batten & vertical panel\nHorizontal lap & flush panel\nTrim, flashing & soffit\nColor-matched to your roof\nInsulated panel options\nAccent walls & wainscot",
		'cta'     => 'Request a siding quote',
		'caption' => 'Metal Siding — completed project',
		'why'     => array(
			array( 'shield-check', 'Fire & weather resistant', 'Steel panels don\'t burn, rot, or feed pests — and they hold their finish through freeze-thaw and summer heat.' ),
			array( 'badge-check', 'Roof-matched finish', 'Because we install both, your siding and roof come from coordinated color lines and trim profiles.' ),
			array( 'wrench', 'Low maintenance', 'Rinse it and you\'re done. No caulk-and-paint cycle every few years.' ),
			array( 'house', 'Whole-envelope thinking', 'Flashing, weeps and transitions are detailed with the roof, so water has nowhere to hide.' ),
		),
		'faq'     => array(
			array( 'Does metal siding dent easily?', 'Quality 26- and 24-gauge panels resist typical hail and impacts. We can also specify heavier gauge for exposed elevations.' ),
			array( 'Can it go over my existing siding?', 'Frequently, yes — over sound wood or fiber-cement with furring and a proper weather barrier. We confirm during the estimate.' ),
			array( 'What colors are available?', 'Dozens of factory finishes, including the same color lines as our roofing panels so both can match exactly.' ),
			array( 'Is metal siding insulated?', 'Panels themselves are thin; insulation comes from the wall assembly behind them. Insulated composite panels are available for shops and commercial work.' ),
		),
	),
	'remodeling' => array(
		'title'   => 'Remodeling & Additions',
		'eyebrow' => 'Our services · Remodeling',
		'h1'      => 'Remodeling and additions, one licensed crew',
		'sub'     => 'Additions, shop conversions, porches and exterior refreshes — framed, roofed and sided by the same team so nothing falls between trades.',
		'badge'   => '',
		'heading' => 'Remodeling & additions',
		'body'    => "Most remodels stall at the hand-offs — framer to roofer to siding crew. We do all three, so an addition or shop conversion moves from footings to finished exterior with one point of contact. We handle the structural work, tie the new roof and siding into the existing home, and leave you with a weather-tight envelope ready for interior finish.",
		'list'    => "Room additions & bump-outs\nShop & garage conversions\nCovered porches & decks\nExterior refresh (roof + siding)\nStructural repairs\nPermits & inspections coordinated",
		'cta'     => 'Talk to us about a remodel',
		'caption' => 'Remodeling — completed project',
		'why'     => array(
			array( 'handshake', 'One contract, one crew', 'Framing, roofing and siding under a single licensed contractor — no subcontractor hand-offs.' ),
			array( 'ruler', 'Tied in properly', 'New roof and wall planes are flashed and integrated into the existing structure, not just butted against it.' ),
			array( 'clipboard-check', 'Permits handled', 'We coordinate permits and inspections in Washington and Idaho so the project stays on schedule.' ),
			array( 'clock', 'Realistic timelines', 'You get a straight schedule up front and updates as we go — no surprises.' ),
		),
		'faq'     => array(
			array( 'Do you do interior remodeling?', 'Our focus is structural and exterior work — additions, conversions, porches, roof and siding. We can recommend finish trades we trust for interiors.' ),
			array( 'Do I need a permit for an addition?', 'Yes in nearly every case. We prepare the drawings and pull the permit as part of the project.' ),
			array( 'How long does an addition take?', 'A typical single-room addition runs several weeks from footings to weather-tight shell, depending on size and season. We give you a written schedule with the quote.' ),
			array( 'Can you match my existing roof and siding?', 'Usually — and where an exact match isn\'t possible we\'ll show you options that blend or intentionally contrast.' ),
		),
	),
	'concrete' => array(
		'title'   => 'Concrete',
		'eyebrow' => 'Our services · Concrete',
		'h1'      => 'Concrete flatwork and foundations',
		'sub'     => 'Driveways, patios, foundations and shop slabs — poured to spec with drainage and frost depth planned for our winters.',
		'badge'   => '',
		'heading' => 'Concrete work',
		'body'    => "Good concrete starts below the surface. We prep and compact the base, set forms to grade for drainage, place steel where it belongs, and pour and finish for the use the slab will see — broom finish for traction outdoors, smooth trowel in shops. Footings and foundations are set to local frost depth and inspected before we build on them.",
		'list'    => "Driveways & approaches\nPatios, walkways & steps\nFoundations & footings\nShop, barn & garage slabs\nRetaining & stem walls\nTear-out & replacement",
		'cta'     => 'Request a concrete quote',
		'caption' => 'Concrete — completed project',
		'why'     => array(
			array( 'snowflake', 'Frost-depth foundations', 'Footings set below local frost line so slabs and structures stay put through freeze-thaw.' ),
			array( 'ruler', 'Graded for drainage', 'Every slab is formed to move water away from the building — the #1 cause of concrete and siding problems.' ),
			array( 'hard-hat', 'Prepped, not just poured', 'Compacted base, proper reinforcement and control joints where they belong.' ),
			array( 'house', 'Part of the whole build', 'Slab, framing and roof from one crew means the shop you\'re planning is quoted as one project.' ),
		),
		'faq'     => array(
			array( 'When can I drive on a new driveway?', 'Foot traffic in a day or two; vehicles after about a week; full strength at 28 days. We\'ll give you specifics for your mix and the weather.' ),
			array( 'Can you pour in winter?', 'Cold-weather pours are possible with the right mix, blankets and timing, but we\'ll tell you honestly if waiting gives a better result.' ),
			array( 'Will my concrete crack?', 'Concrete moves; we control where it cracks with properly spaced joints and reinforcement so cracks stay in the joints, not across the slab.' ),
			array( 'Do you remove old concrete?', 'Yes — tear-out, haul-off and base re-prep are quoted as part of a replacement.' ),
		),
	),
	'framing' => array(
		'title'   => 'Framing',
		'eyebrow' => 'Our services · Framing',
		'h1'      => 'Framing for new builds, shops and additions',
		'sub'     => 'Residential and light commercial framing — square, plumb and ready for the trades that follow, from the same crew that can roof and side it.',
		'badge'   => '',
		'heading' => 'Framing & post-frame',
		'body'    => "We frame homes, additions, shops and agricultural buildings — conventional stick framing and post-frame (pole) construction. Because we also install the roof and siding, our framing is laid out with those systems in mind: correct overhangs, blocking where panels need it, and openings sized for the trim we'll install later.",
		'list'    => "New-build & addition framing\nPost-frame & pole buildings\nRoof & floor systems\nShops, barns & ag buildings\nStructural repairs & sistering\nLight commercial",
		'cta'     => 'Request a framing quote',
		'caption' => 'Framing — completed project',
		'why'     => array(
			array( 'ruler', 'Square, plumb, on layout', 'Framing that makes every following trade faster and cleaner.' ),
			array( 'warehouse', 'Post-frame specialists', 'Shops, barns and ag buildings framed for metal roofing and siding from day one.' ),
			array( 'snowflake', 'Snow-load engineered', 'Trusses and roof systems specified for local snow loads, not a national minimum.' ),
			array( 'house', 'Roofed and sided by us', 'One crew carries the building from slab to weather-tight — no coordination gaps.' ),
		),
		'faq'     => array(
			array( 'Do you build post-frame (pole) buildings?', 'Yes — from the slab or piers through framing, metal roof and siding. It\'s one of our most common shop and barn projects.' ),
			array( 'Do you provide engineered plans?', 'We work from your plans or coordinate engineered drawings and truss packages as part of the project.' ),
			array( 'Can you frame just the shell?', 'Yes. Many customers have us deliver a weather-tight shell — framing, roof and siding — and finish the interior themselves.' ),
			array( 'Do you do structural repairs?', 'Yes — sagging roofs, rotted sills, undersized headers and storm damage. We assess, quote and fix.' ),
		),
	),
);

$blank_tab = static function ( $i ) {
	return array(
		"t5t_tab{$i}_label" => '', "t5t_tab{$i}_badge" => '', "t5t_tab{$i}_heading" => '', "t5t_tab{$i}_body" => '',
		"t5t_tab{$i}_list"  => '', "t5t_tab{$i}_cta" => '', "t5t_tab{$i}_caption" => '',
	);
};

$made = array();
foreach ( $services as $slug => $s ) {
	$existing = get_page_by_path( "our-services/{$slug}", OBJECT, 'page' );
	$args     = array(
		'post_title'  => $s['title'],
		'post_name'   => $slug,
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_parent' => $parent,
		'menu_order'  => array_search( $slug, array_keys( $services ), true ),
	);
	if ( $existing ) {
		$args['ID'] = $existing->ID;
		$id         = wp_update_post( $args );
	} else {
		$id = wp_insert_post( $args );
	}
	update_post_meta( $id, '_wp_page_template', 'template-blank-5.php' );

	// --- t5-tabs (single panel) ---
	$tabs = array(
		'_type' => 't5-tabs', '_bp_base' => '4_4', '_m' => array( 'e' => 1 ), '_modules' => array(),
		't5t_show_header' => true,
		't5t_eyebrow'     => $s['eyebrow'],
		't5t_heading'     => $s['h1'],
		't5t_subtext'     => $s['sub'],
		't5t_cta_url'     => '/contact-us/',
		't5t_tab1_label'   => $s['title'],
		't5t_tab1_badge'   => $s['badge'],
		't5t_tab1_heading' => $s['heading'],
		't5t_tab1_body'    => $s['body'],
		't5t_tab1_list'    => $s['list'],
		't5t_tab1_cta'     => $s['cta'],
		't5t_tab1_caption' => $s['caption'],
	);
	foreach ( range( 2, 6 ) as $i ) {
		$tabs = array_merge( $tabs, $blank_tab( $i ) );
	}

	// --- t5-why ---
	$why = array( '_type' => 't5-why', '_bp_base' => '4_4', '_m' => array( 'e' => 2 ), '_modules' => array(), 't5w_heading' => 'Why choose Triple 5 for ' . strtolower( $s['title'] ) . '?' );
	foreach ( $s['why'] as $n => $r ) {
		$i = $n + 1;
		$why[ "t5w_reason{$i}_icon" ]  = $r[0];
		$why[ "t5w_reason{$i}_title" ] = $r[1];
		$why[ "t5w_reason{$i}_body" ]  = $r[2];
	}

	// --- t5-faq ---
	$faq = array(
		'_type' => 't5-faq', '_bp_base' => '4_4', '_m' => array( 'e' => 3 ), '_modules' => array(),
		't5f_heading'    => $s['title'] . "\nquestions",
		't5f_intro'      => 'Straight answers to what homeowners and shop owners ask us most about ' . strtolower( $s['title'] ) . '. Not covered? Call or message us and we\'ll walk you through it.',
		't5f_open_index' => '1',
		't5f_columns'    => '2',
		't5f_schema'     => true,
	);
	foreach ( range( 1, 8 ) as $i ) {
		$qa                   = $s['faq'][ $i - 1 ] ?? array( '', '' );
		$faq[ "t5f_q{$i}" ]   = $qa[0];
		$faq[ "t5f_a{$i}" ]   = $qa[1];
	}

	$doc = cornerstone( 'Resolver' )->getDocument( $id );
	$doc->updateElements( array( $tabs, $why, $faq ), $settings );
	$c      = get_post( $id )->post_content;
	$made[] = sprintf( '%s (ID %d) %s — baked: tabs %d why %d faq %d', $slug, $id, get_permalink( $id ), substr_count( $c, 't5-tabs__panel' ), substr_count( $c, 't5-why__reason' ), substr_count( $c, 't5-faq__item' ) );
}
echo implode( "\n", $made ) . "\n";
