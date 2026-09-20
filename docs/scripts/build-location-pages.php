<?php
/**
 * Build the location pages as children of Locations (ID 47) and the Locations index.
 *
 * Each location page = t5-tabs (localized header + 5 service tabs, each with its
 * own CTA) → t5-why (4 local reasons) → t5-faq (4 local questions, FAQPage schema),
 * plus SEO meta (title, description, areaServed) for triple5-seo.php.
 *
 * Run from the Local site root with WP-CLI: `wp eval-file <path>/build-location-pages.php`.
 * Run it TWICE in separate processes — the first bake of freshly-created documents
 * comes out empty (see PROJECT_CONTEXT.md, bake gotcha); the second is real.
 * Idempotent by slug; re-running overwrites builder edits on these pages.
 */
wp_set_current_user( 1 );

$parent   = 47;
$settings = get_post_meta( 10, '_cornerstone_settings', true );

/*
 * Location facts used in copy. Keep these honest and general — climate, terrain
 * and who issues permits — not invented stats. [name, state, slug, county/permit
 * authority, climate note, character note, nearby].
 */
$locations = array(
	'spokane' => array(
		'name'    => 'Spokane', 'state' => 'WA', 'area' => 'Spokane, WA',
		'permits' => 'the City of Spokane or Spokane County, depending on the address',
		'climate' => 'Spokane averages around four feet of snow a season, with freeze-thaw cycles from November into March and hot, dry summers that bring wildfire smoke and ember risk',
		'character' => "from South Hill craftsman homes and North Side ranchers to shops and outbuildings on the county edges",
		'nearby'  => 'Spokane Valley, Mead, Airway Heights and Liberty Lake',
	),
	'coeur-dalene' => array(
		'name'    => "Coeur d'Alene", 'state' => 'ID', 'area' => "Coeur d'Alene, ID",
		'permits' => "the City of Coeur d'Alene or Kootenai County",
		'climate' => "Coeur d'Alene sits at the foot of the mountains, so snow loads run heavier than Spokane's and lake-effect moisture tests every flashing and gutter detail; summers bring wildfire exposure on the forested edges",
		'character' => 'from lakefront and Fernan-area homes to shops and acreage out toward the prairie',
		'nearby'  => 'Hayden, Post Falls, Rathdrum and Dalton Gardens',
	),
	'hayden' => array(
		'name'    => 'Hayden', 'state' => 'ID', 'area' => 'Hayden, ID',
		'permits' => 'the City of Hayden or Kootenai County',
		'climate' => 'Hayden gets real North Idaho winters — deep, wet snow that sits on roofs for weeks — followed by dry summers with wildfire risk along the timbered lots',
		'character' => 'newer subdivisions, Hayden Lake homes, and shops and barns on larger parcels',
		'nearby'  => "Coeur d'Alene, Rathdrum, Post Falls and Athol",
	),
	'mead' => array(
		'name'    => 'Mead', 'state' => 'WA', 'area' => 'Mead, WA',
		'permits' => 'Spokane County (Mead is unincorporated)',
		'climate' => 'Mead and the Peone Prairie see heavier snow than downtown Spokane and take the full brunt of winter wind across open ground, which is hard on exposed-fastener roofs and siding',
		'character' => 'acreage homes, shops, barns and agricultural buildings',
		'nearby'  => 'Colbert, Chattaroy, Green Bluff and north Spokane',
	),
	'airway-heights' => array(
		'name'    => 'Airway Heights', 'state' => 'WA', 'area' => 'Airway Heights, WA',
		'permits' => 'the City of Airway Heights or Spokane County',
		'climate' => 'the West Plains are open and windy — snow drifts, wind-driven rain and summer heat all work on a roof harder than they do in town',
		'character' => 'newer residential neighborhoods, homes near Fairchild, and commercial and light-industrial buildings along Highway 2',
		'nearby'  => 'Medical Lake, Cheney, Four Lakes and west Spokane',
	),
	'liberty-lake' => array(
		'name'    => 'Liberty Lake', 'state' => 'WA', 'area' => 'Liberty Lake, WA',
		'permits' => 'the City of Liberty Lake',
		'climate' => "Liberty Lake gets Spokane-Valley snow with extra moisture off the lake and the Idaho foothills, so ice dams and valley flashing get tested every winter",
		'character' => 'planned neighborhoods with HOA guidelines, lakeside homes and modern commercial buildings',
		'nearby'  => 'Spokane Valley, Otis Orchards, Post Falls and Newman Lake',
	),
	'sandpoint' => array(
		'name'    => 'Sandpoint', 'state' => 'ID', 'area' => 'Sandpoint, ID',
		'permits' => 'the City of Sandpoint or Bonner County',
		'climate' => 'Sandpoint has some of the heaviest snow loads in our service area — lake-effect snow off Pend Oreille and Schweitzer-country storms — which is exactly where a properly engineered metal roof earns its keep',
		'character' => 'lake and mountain homes, older in-town houses, and shops, barns and cabins on acreage',
		'nearby'  => 'Ponderay, Kootenai, Sagle, Dover and Hope',
	),
);

/* Service tabs — {name} / {state} / {permits} / {climate} are filled per location. */
$tabs = array(
	array(
		'Roofing', 'Metal specialty', 'Metal roofing in {name}',
		"Standing seam, corrugated and metal shingle roofing for {name} homes, shops and barns. {climate}. We spec panel gauge, underlayment, ice-and-water and snow retention for {name}'s conditions, not a national average, and pull permits through {permits}.",
		"Standing seam (concealed fastener)\nCorrugated & exposed-fastener panel\nMetal shingle & stone-coated\nRe-roofs over existing decking\nSnow guards & ice management\nRoof repair & storm damage",
		'Request a {name} roofing quote', 'Metal roofing — {name}, {state}',
	),
	array(
		'Siding', 'Metal specialty', 'Metal siding in {name}',
		"Board-and-batten, vertical and horizontal metal panel siding for {name}, color-matched to your roof. Metal doesn't rot, feed pests or need repainting, and it stands up to the wind-driven weather {name} gets — with trim, flashing and soffit detailed as one system.",
		"Board-and-batten & vertical panel\nHorizontal lap & flush panel\nTrim, flashing & soffit\nColor-matched to your roof\nAccent walls & wainscot\nRe-side over sound existing siding",
		'Request a {name} siding quote', 'Metal siding — {name}, {state}',
	),
	array(
		'Remodeling', '', 'Remodeling & additions in {name}',
		"Additions, shop conversions, covered porches and exterior refreshes for {name} properties — framed, roofed and sided by the same licensed crew, with permits and inspections coordinated through {permits}.",
		"Room additions & bump-outs\nShop & garage conversions\nCovered porches & decks\nExterior refresh (roof + siding)\nStructural repairs\nPermits & inspections handled",
		'Talk to us about a {name} remodel', 'Remodeling — {name}, {state}',
	),
	array(
		'Concrete', '', 'Concrete in {name}',
		"Driveways, patios, walkways, foundations and shop slabs in {name}, formed to grade for drainage and set to local frost depth so they hold up through {name} freeze-thaw winters.",
		"Driveways & approaches\nPatios, walkways & steps\nFoundations & footings\nShop, barn & garage slabs\nRetaining & stem walls\nTear-out & replacement",
		'Request a {name} concrete quote', 'Concrete — {name}, {state}',
	),
	array(
		'Framing', '', 'Framing & post-frame in {name}',
		"Stick-frame and post-frame construction for {name} homes, shops, barns and ag buildings — trusses and roof systems specified for {name} snow loads, and framed with the metal roof and siding we'll install next in mind.",
		"New-build & addition framing\nPost-frame & pole buildings\nRoof & floor systems\nShops, barns & ag buildings\nStructural repairs & sistering\nLight commercial",
		'Request a {name} framing quote', 'Framing — {name}, {state}',
	),
);

$fill = static function ( $text, $loc ) {
	return strtr( $text, array( '{name}' => $loc['name'], '{state}' => $loc['state'], '{permits}' => $loc['permits'], '{climate}' => $loc['climate'], '{nearby}' => $loc['nearby'], '{character}' => $loc['character'] ) );
};

$made = array();
foreach ( $locations as $slug => $loc ) {
	$existing = get_page_by_path( "locations/{$slug}", OBJECT, 'page' );
	$args     = array(
		'post_title'  => $loc['name'] . ', ' . $loc['state'],
		'post_name'   => $slug,
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_parent' => $parent,
		'menu_order'  => array_search( $slug, array_keys( $locations ), true ),
	);
	$id = $existing ? wp_update_post( array_merge( $args, array( 'ID' => $existing->ID ) ) ) : wp_insert_post( $args );
	update_post_meta( $id, '_wp_page_template', 'template-blank-4.php' );

	// SEO.
	update_post_meta( $id, 't5seo_title', "Metal Roofing & Siding in {$loc['name']}, {$loc['state']} | Triple 5 Construction" );
	update_post_meta( $id, 't5seo_desc', $fill( "Licensed metal roofing, metal siding, remodeling, concrete and framing contractor serving {name}, {state} and {nearby}. Free on-site estimates — call (509) 251-2829.", $loc ) );
	update_post_meta( $id, 't5seo_area', $loc['area'] );

	// --- t5-tabs ---
	$el = array(
		'_type' => 't5-tabs', '_bp_base' => '4_4', '_m' => array( 'e' => 1 ), '_modules' => array(),
		't5t_show_header' => true,
		't5t_eyebrow'     => 'Serving ' . $loc['name'] . ', ' . $loc['state'],
		't5t_heading'     => "Metal roofing, siding & construction in {$loc['name']}",
		't5t_subtext'     => $fill( "Triple 5 Construction is a licensed, insured contractor working across {name} — {character} — plus {nearby}. {climate}. Every roof, wall and slab we build is detailed for it.", $loc ),
		't5t_cta_url'     => '/contact-us/',
	);
	foreach ( $tabs as $n => $t ) {
		$i = $n + 1;
		$el[ "t5t_tab{$i}_label" ]   = $t[0];
		$el[ "t5t_tab{$i}_badge" ]   = $t[1];
		$el[ "t5t_tab{$i}_heading" ] = $fill( $t[2], $loc );
		$el[ "t5t_tab{$i}_body" ]    = $fill( $t[3], $loc );
		$el[ "t5t_tab{$i}_list" ]    = $t[4];
		$el[ "t5t_tab{$i}_cta" ]     = $fill( $t[5], $loc );
		$el[ "t5t_tab{$i}_caption" ] = $fill( $t[6], $loc );
	}
	$el['t5t_tab6_label'] = '';

	// --- t5-why ---
	$why = array(
		'_type' => 't5-why', '_bp_base' => '4_4', '_m' => array( 'e' => 2 ), '_modules' => array(),
		't5w_heading' => "Why {$loc['name']} homeowners choose Triple 5",
		't5w_reason1_icon' => 'map-pin',      't5w_reason1_title' => "We work in {$loc['name']} every week", 't5w_reason1_body' => $fill( 'A local crew, not a call center — we know the neighborhoods, the inspectors and the weather in {name} and {nearby}.', $loc ),
		't5w_reason2_icon' => 'snowflake',    't5w_reason2_title' => 'Built for the local climate',            't5w_reason2_body' => $fill( "Snow retention, ice-and-water, flashing and drainage sized for what {name} actually gets — not a national spec.", $loc ),
		't5w_reason3_icon' => 'shield-check', 't5w_reason3_title' => "Licensed & insured in {$loc['state']}",   't5w_reason3_body' => $fill( 'Fully licensed and insured, with permits pulled through {permits}.', $loc ),
		't5w_reason4_icon' => 'house',        't5w_reason4_title' => 'One crew, start to finish',                't5w_reason4_body' => 'Roofing, siding, framing and concrete from the same licensed crew — one estimate, one point of contact.',
	);

	// --- t5-faq ---
	$faq = array(
		'_type' => 't5-faq', '_bp_base' => '4_4', '_m' => array( 'e' => 3 ), '_modules' => array(),
		't5f_heading'    => "{$loc['name']}\nquestions",
		't5f_intro'      => $fill( "What {name} homeowners and shop owners ask us most. Not covered? Call (509) 251-2829 and we'll walk you through it.", $loc ),
		't5f_open_index' => '1', 't5f_columns' => '2', 't5f_schema' => true,
		't5f_q1' => "Do you serve all of {$loc['name']}?",
		't5f_a1' => $fill( "Yes — all of {name} and the surrounding area, including {nearby}. If you're not sure you're in range, call and we'll tell you straight.", $loc ),
		't5f_q2' => "Is metal roofing a good choice for {$loc['name']}'s snow?",
		't5f_a2' => $fill( "It's one of the best. {climate}. A properly installed metal roof sheds snow and ice instead of holding it, and we add snow guards above entries and walkways so it releases safely.", $loc ),
		't5f_q3' => "Do you handle permits in {$loc['name']}?",
		't5f_a3' => $fill( 'Yes. Permits for {name} go through {permits}; we prepare the paperwork, pull the permit and schedule inspections as part of the job.', $loc ),
		't5f_q4' => 'How do I get an estimate?',
		't5f_a4' => $fill( "Call (509) 251-2829 or use the form on our contact page. We'll set up a free on-site visit in {name}, take measurements, walk your options and follow up with a clear written quote.", $loc ),
		't5f_q5' => '', 't5f_q6' => '', 't5f_q7' => '', 't5f_q8' => '',
	);

	$doc = cornerstone( 'Resolver' )->getDocument( $id );
	$doc->updateElements( array( $el, $why, $faq ), $settings );
	$c      = get_post( $id )->post_content;
	$made[] = sprintf( '%-16s ID %d %s — baked: tabs %d why %d faq %d', $slug, $id, get_permalink( $id ), substr_count( $c, 't5-tabs__panel' ), substr_count( $c, 't5-why__reason' ), substr_count( $c, 't5-faq__item' ) );
}

// --- Locations index (47): header band + 7 location cards + FAQ ---
update_post_meta( $parent, '_wp_page_template', 'template-blank-4.php' );
update_post_meta( $parent, 't5seo_title', "Service Areas — Spokane, Coeur d'Alene & North Idaho | Triple 5 Construction" );
update_post_meta( $parent, 't5seo_desc', "Metal roofing, siding, remodeling, concrete and framing across Spokane, Mead, Airway Heights, Liberty Lake, Coeur d'Alene, Hayden and Sandpoint. Licensed in WA & ID." );

$header = array(
	'_type' => 't5-tabs', '_bp_base' => '4_4', '_m' => array( 'e' => 1 ), '_modules' => array(),
	't5t_show_header' => true,
	't5t_eyebrow'     => 'Where we work',
	't5t_heading'     => "Spokane to Sandpoint, one licensed crew",
	't5t_subtext'     => "Triple 5 Construction serves homeowners and businesses across eastern Washington and North Idaho. Pick your area for the services, conditions and answers specific to it.",
	't5t_cta_url'     => '/contact-us/',
	't5t_tab1_label'  => 'Service area', 't5t_tab1_badge' => 'Licensed in WA & ID', 't5t_tab1_heading' => 'Two states, one standard',
	't5t_tab1_body'   => "Our crew is based in Spokane and works the corridor from the West Plains through Spokane Valley and Liberty Lake, across the state line into Coeur d'Alene and Hayden, and north to Sandpoint. Same crew, same materials, same detailing everywhere we go.",
	't5t_tab1_list'   => "Spokane & Mead, WA\nAirway Heights & the West Plains\nLiberty Lake & Spokane Valley\nCoeur d'Alene & Hayden, ID\nSandpoint & Bonner County\nPost Falls, Rathdrum & Athol",
	't5t_tab1_cta'    => 'Request a free estimate', 't5t_tab1_caption' => 'Service area map — Spokane & Coeur d\'Alene',
	't5t_tab2_label' => '', 't5t_tab3_label' => '', 't5t_tab4_label' => '', 't5t_tab5_label' => '', 't5t_tab6_label' => '',
);
$grid = array(
	'_type' => 't5-services', '_bp_base' => '4_4', '_m' => array( 'e' => 2 ), '_modules' => array(),
	't5s_eyebrow' => 'Locations', 't5s_heading' => "Choose your area",
);
$tones = array( 'navy', 'clay', 'coal' );
$i = 0;
foreach ( $locations as $slug => $loc ) {
	$i++;
	$grid[ "t5s_card{$i}_title" ] = $loc['name'] . ', ' . $loc['state'];
	$grid[ "t5s_card{$i}_desc" ]  = $fill( 'Metal roofing, siding, remodeling, concrete and framing in {name} and {nearby}.', $loc );
	$grid[ "t5s_card{$i}_url" ]   = "/locations/{$slug}/";
	$grid[ "t5s_card{$i}_tone" ]  = $tones[ ( $i - 1 ) % 3 ];
}
$grid['t5s_card8_title'] = '';

$doc = cornerstone( 'Resolver' )->getDocument( $parent );
$doc->updateElements( array( $header, array( '_type' => 'section', '_bp_base' => '4_4', '_m' => array( 'e' => 10 ), 'section_padding' => '!0px 0px 65px 0px', '_modules' => array( array( '_type' => 'layout-row', '_bp_base' => '4_4', '_m' => array( 'e' => 11 ), '_modules' => array( array( '_type' => 'layout-column', '_bp_base' => '4_4', '_m' => array( 'e' => 12 ), '_modules' => array( $grid ) ) ) ) ) ) ), $settings );
$c      = get_post( $parent )->post_content;
$made[] = sprintf( '%-16s ID %d %s — baked: header %d cards %d', 'locations index', $parent, get_permalink( $parent ), substr_count( $c, 't5-tabs__header' ), substr_count( $c, 't5-services__card' ) );

echo implode( "\n", $made ) . "\n";
