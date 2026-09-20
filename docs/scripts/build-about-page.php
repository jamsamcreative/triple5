<?php
/**
 * Build the About page (ID 13) from existing elements, adapting copy from the
 * live site (triple5construction.com/about-us) to the new brand voice.
 *
 * Sections: t5-tabs (single: header + "who we are" intro) → t5-feature (how we
 * work, image right) → t5-why (values) → t5-reviews → t5-faq. Plus SEO meta.
 *
 * Run from the Local site root: `wp eval-file <path>/build-about-page.php`.
 * Run TWICE in separate processes (first bake of a new arrangement can come out
 * empty). Idempotent; overwrites builder edits on this page.
 */
wp_set_current_user( 1 );

$id       = 13;
$settings = get_post_meta( 10, '_cornerstone_settings', true );

update_post_meta( $id, '_wp_page_template', 'template-blank-4.php' );
update_post_meta( $id, 't5seo_title', "About Triple 5 Construction | Metal Roofing & Siding, Spokane & Coeur d'Alene" );
update_post_meta( $id, 't5seo_desc', "Triple 5 Construction is a licensed, insured metal roofing and siding contractor based in Spokane, WA, serving the Inland Northwest into Coeur d'Alene, ID. One crew, start to finish. Free estimates." );

// 1. Header + intro ----------------------------------------------------------
$intro = array(
	'_type' => 't5-tabs', '_bp_base' => '4_4', '_m' => array( 'e' => 1 ), '_modules' => array(),
	't5t_show_header' => true,
	't5t_eyebrow'     => 'About us',
	't5t_heading'     => 'Stronger together',
	't5t_subtext'     => "Triple 5 Construction is a Spokane-based, licensed and insured contractor specializing in metal roofing and siding — and the framing, concrete and remodeling that go with it — for homes, shops and businesses across eastern Washington and North Idaho.",
	't5t_cta_url'     => '/contact-us/',
	't5t_tab1_label'   => 'Who we are',
	't5t_tab1_badge'   => 'Licensed in WA & ID',
	't5t_tab1_heading' => 'A metal roofing and siding contractor for the Inland Northwest',
	't5t_tab1_body'    => "Protecting a home from Inland Northwest weather starts with its exterior, and we've built our business on the strongest way to do that: metal. Standing seam and panel roofing, metal siding, and the flashing and details that make them last. We've been installing metal roofing and siding in Spokane for years, and we've grown into a full-service crew that can frame the addition, pour the slab and finish the exterior — so your project stays with one contractor from the first estimate to the final walk-through.",
	't5t_tab1_list'    => "Metal roofing — steel, aluminum & copper\nMetal siding — panel, board-and-batten & insulated\nRoof repair, replacement & maintenance\nFraming, concrete & remodeling\nLicensed & insured in Washington and Idaho\nBased in Spokane, working to Coeur d'Alene & Sandpoint",
	't5t_tab1_cta'     => 'Request a free estimate',
	't5t_tab1_caption' => 'The Triple 5 crew on site',
	't5t_tab2_label' => '', 't5t_tab3_label' => '', 't5t_tab4_label' => '', 't5t_tab5_label' => '', 't5t_tab6_label' => '',
);

// 2. How we work -------------------------------------------------------------
$process = array(
	'_type' => 't5-feature', '_bp_base' => '4_4', '_m' => array( 'e' => 2 ), '_modules' => array(),
	't5f_eyebrow'    => 'How we work',
	't5f_heading'    => "Precision from the first call\nto the final cleanup",
	't5f_body'       => "We treat every project with the same care, whether it's a shop roof in Mead or a full re-side on the South Hill. That means honest advice on what your home actually needs, a written quote with no surprises, and a crew that shows up when it says it will.",
	't5f_checklist'  => "Free on-site consultation & measurements\nClear written quote — options explained, no pressure\nPermits and inspections handled for you\nInstalled by our own licensed crew, not subcontractors\nSite cleaned and walked through with you at the end",
	't5f_body2'      => "Say goodbye to the repaint-and-replace cycle of asphalt and vinyl. A properly installed metal exterior is the last one most homes need — and we build every one to prove it.",
	't5f_show_body2' => true,
	't5f_image_side' => 'right',
	't5f_show_badge' => false,
	't5f_show_stat'  => true,
	't5f_stat_number' => '2',
	't5f_stat_label'  => 'States, one licensed crew',
	't5f_show_button' => true,
	't5f_button_text' => 'See our services',
	't5f_button_url'  => '/our-services/',
);

// 3. Values ------------------------------------------------------------------
$values = array(
	'_type' => 't5-why', '_bp_base' => '4_4', '_m' => array( 'e' => 3 ), '_modules' => array(),
	't5w_heading' => 'What we stand for',
	't5w_reason1_icon' => 'handshake',    't5w_reason1_title' => 'Honest advice',        't5w_reason1_body' => "We'll tell you what your roof or siding actually needs — including when a repair beats a replacement.",
	't5w_reason2_icon' => 'hard-hat',     't5w_reason2_title' => 'Craft, not shortcuts', 't5w_reason2_body' => 'Flashing, underlayment and fastening done right the first time. Attention to detail is the whole job.',
	't5w_reason3_icon' => 'map-pin',      't5w_reason3_title' => 'Local and accountable', 't5w_reason3_body' => 'We live and work here. Our reputation in Spokane and Coeur d\'Alene rides on every project we sign.',
	't5w_reason4_icon' => 'shield-check', 't5w_reason4_title' => 'Licensed & insured',   't5w_reason4_body' => 'Fully licensed and insured in Washington and Idaho, with permits pulled and inspections passed on every job.',
);

// 4. Reviews -----------------------------------------------------------------
$reviews = array(
	'_type' => 't5-reviews', '_bp_base' => '4_4', '_m' => array( 'e' => 4 ), '_modules' => array(),
	't5r_heading' => 'What our customers say',
	't5r_limit'   => '4',
	't5r_pf_google_url'     => 'https://www.google.com/maps/search/Triple+5+Construction+Spokane',
	't5r_pf_trustpilot_url' => 'https://www.trustpilot.com/',
);

// 5. FAQ ---------------------------------------------------------------------
$faq = array(
	'_type' => 't5-faq', '_bp_base' => '4_4', '_m' => array( 'e' => 5 ), '_modules' => array(),
	't5f_heading'    => "About Triple 5\nquestions",
	't5f_intro'      => "The things people ask before hiring us. Anything else — call (509) 251-2829 and talk to the crew directly.",
	't5f_open_index' => '1', 't5f_columns' => '2', 't5f_schema' => true,
	't5f_q1' => 'Are you licensed and insured?',
	't5f_a1' => 'Yes. Triple 5 Construction LLC is a licensed and insured general contractor in both Washington and Idaho. Ask us for current license numbers and certificates of insurance — we\'re happy to provide them.',
	't5f_q2' => 'Who actually does the work?',
	't5f_a2' => 'Our own crew. The people who measure and quote your project are the people who install it. We don\'t hand jobs off to subcontractors.',
	't5f_q3' => 'Where are you based and where do you work?',
	't5f_a3' => "We're based in Spokane, WA and work across the Inland Northwest — Spokane Valley, Mead, Airway Heights and Liberty Lake, and into North Idaho: Coeur d'Alene, Hayden, Post Falls, Rathdrum and Sandpoint.",
	't5f_q4' => 'Do you only do metal roofing?',
	't5f_a4' => 'Metal roofing and siding are our specialty, but we also frame, pour concrete and build additions and remodels — so a shop, addition or full exterior refresh can stay with one crew.',
	't5f_q5' => 'How do estimates work?',
	't5f_a5' => 'Every estimate is free and on-site. We measure, walk your options and follow up with a clear written quote. No pressure, no surprises.',
	't5f_q6' => 'What kind of warranty do you offer?',
	't5f_a6' => 'Metal roofing and siding panels carry manufacturer finish warranties, and we stand behind our workmanship. We\'ll spell out both in writing with your quote.',
	't5f_q7' => '', 't5f_q8' => '',
);

$doc = cornerstone( 'Resolver' )->getDocument( $id );
$doc->updateElements( array( $intro, $process, $values, $reviews, $faq ), $settings );
$c = get_post( $id )->post_content;
printf( "About (ID %d) %s — baked: header %d feature %d why %d reviews %d faq %d\n", $id, get_permalink( $id ), substr_count( $c, 't5-tabs__header' ), substr_count( $c, 't5-feature__inner' ), substr_count( $c, 't5-why__reason' ), substr_count( $c, 't5-reviews__card' ), substr_count( $c, 't5-faq__item' ) );
