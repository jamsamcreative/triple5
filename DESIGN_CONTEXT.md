# Design Context — triple5

**Last updated:** 2026-09-18

> This is a living design-system briefing. It loads automatically when prompts or recent work involve UI/design. Update it whenever a design decision is made — new color, new pattern, new component convention, new motion rule. Capture the *reasoning*, not just the values. Tokens already live in code; this file holds the *why*.

> **Source of truth:** the `/triple5-design` skill (`~/.claude/skills/triple5-design/`) and the token files in `wp-content/mu-plugins/triple5-brand/tokens/`. This file is the reasoning layer over those.

---

## Design Principles

- **Sturdy, not soft.** This is a metal-roofing contractor — the UI should read grounded and industrial (squared corners, condensed signage type, solid color fields), never floaty or consumer-app playful.
- **Metal specialty leads.** Metal roofing/siding is the loudest thing the brand says; the other services are documented as breadth, not headlined equally.
- **Honest and concrete.** Real, climate-specific copy (snow load, freeze-thaw, wildfire) over generic "durable & energy-efficient." No invented stats. No emoji, ever.
- **Restraint.** One accent color, reserved for the single most important CTA. Motion is functional, short, never decorative.

---

## Visual Language

### Color
_Seeded from: `wp-content/mu-plugins/triple5-brand/tokens/colors.css`._
- **Charcoal `--brand-primary` #292622** (dark field #1F1D19) — dominant brand color: dark sections, nav, cards, structure. Warm near-black, never blue-black.
- **Brick red `--brand-accent` #8E231E** — the single accent. Reserved for the key CTA, headline accent words, eyebrows, links, check marks. Do not spread it further.
- **Iron `--brand-secondary` #6B665E** — neutral cool-gray support only (info status, muted UI). No blue anywhere in the palette.
- **Warm stone neutrals** (`--color-stone-*`, never cold gray) for text and surfaces. Backgrounds alternate stone-50 and white.
- Text: strong = charcoal-900, body = stone-700, muted = stone-500.
- **Recolor 2026-09-15:** the original navy/clay/steel palette was replaced with charcoal/red/iron to match the owner's reference mockup. Token file keeps `--color-navy-*`, `--color-clay-*`, `--color-steel-*` as **deprecated aliases** to the new ramps so any CSS still using old names renders correctly — new code must use `charcoal`/`red`/`iron` names. Services/Logos element tone *values* stay `navy`/`clay` internally (placed instances store them) but are labelled "Charcoal"/"Red" in the builder.
- Check-list icons moved from steel-blue to red — with blue gone, the accent is the only colour that distinguishes them from body text.

### Typography
_Seeded from: `tokens/typography.css` + `tokens/fonts.css` (Google Fonts substitutes — swap if licensed brand fonts exist)._
- **Barlow Semi Condensed** (700/800) — display + headings. Sturdy, condensed, signage-like.
- **Public Sans** — body + UI. Clean, legible.
- **IBM Plex Mono** — phone numbers, measurements, technical/spec detail.
- **Casing rule:** sentence case for body; **UPPERCASE with wide tracking** (`--tracking-caps` 0.08em) for display labels, buttons, eyebrows — the industrial-signage feel. **Exception (2026-09-15, owner's reference):** the main **nav bar** uses sentence-case Public Sans 600 links and sentence-case pill buttons — the header reads cleaner/more modern that way and the uppercase Barlow top bar above it still carries the signage feel. Section CTAs elsewhere stay uppercase.

### Spacing & Layout
_Seeded from: `tokens/spacing.css`._ 4px base grid. **Site content width is 1240px** — set by the Cornerstone header's `bar_content_max_length`, and every section wrapper matches it (`max-width: 1304px` = 1240 + 2×32px gutters, border-box) so the hero form card, section grids and footer columns all end flush with the header's "Get a Free Quote" button (owner request 2026-09-18; before this, sections ran at 1200/1240/1280 and the hero form stopped 30px short of the CTA). 720px narrow measure. Generous 64–96px vertical section rhythm.

### Elevation & Surfaces
_Seeded from: `tokens/effects.css`._ Soft, low, **warm-tinted** shadows (`rgba(31,29,25,…)`, charcoal-tinted) — believable elevation, nothing glowing. 1px hairline stone borders on cards; 2px on buttons/focus. Featured cards get a 3px red top rule.

### Corners
Small, **squared-off radii** (3–10px: `--radius-sm/md/lg`) — grounded, not pill-soft. `--radius-pill` reserved for tags/filters only.

### Iconography & Imagery
- **Lucide** outline icons (~2px stroke), functional accents only (phone, shield-check, map-pin, snowflake). Substitution — no brand icon set defined. Never emoji, never Unicode symbols.
- The **logo** (555 house monogram, "STRONGER TOGETHER" arch) is the one true mark — charcoal on light, white on dark. Do not redraw or alter it.
- Dark sections carry a faint **blueprint-grid** texture (subtle white hairlines) — the signature motif. Placeholder imagery uses the same treatment until real Inland-NW job photos replace it.

---

## Component Patterns

### Buttons
Display font, uppercase, wide tracking, `--radius-md` (6px), 2px border. **Primary** = solid charcoal; **accent** = solid red (one per view, the key CTA); **outline/ghost** = charcoal text, fills with stone-100 on hover. Hover darkens (charcoal→charcoal-700, red→red-500); press darkens further + 1px translate down.

### Hero (home)
Full-bleed two-column hero (`.t5-hero`, Cornerstone "Triple 5 Hero" element). Left: display headline (Barlow 800, uppercase, ~62px, line-height 0.98) with **red accent words**, subtext, "Financing options available" label, translucent review badges (Google/Trustpilot — each becomes an `<a target=_blank>` when given a URL, subtle bg-lighten on hover). Right: dark glassy lead-form card (`--radius-lg`, hairline border, `--shadow-lg`) with red focus inputs and one red pill submit. **Parallax (2026-09-18, owner request):** the background photo scrolls at 0.35× speed via `hero.js` (transform on an oversized, composited layer; one update per frame). It's the one decorative motion on the site and is justified as depth for the single hero photo — it is **off under `prefers-reduced-motion` and on touch devices**, where scroll-linked backgrounds jank, and it's a per-instance toggle in the builder. Don't add parallax to other sections. Background: photo with a **left-weighted charcoal scrim** for legibility (the one sanctioned gradient — overlay only, not a fill), falling back to the **charcoal blueprint-grid placeholder** until a real job photo is set. *Why the scrim gradient is allowed despite the no-gradients rule:* it's a legibility overlay on a photo, not a decorative color field. Submit uses `--radius-pill` (exception to squared-corner rule — matches the header CTA's pill button, kept consistent for the single key action).

### Feature Section (reusable)
Two-column image + content block (`.t5-feature`, Cornerstone "Triple 5 Feature Section" element), for About/service/landing sections. Left (or right — flippable for alternating rows): rounded image (`--radius-xl`) with an optional **red experience badge** anchored bottom-left, given a **scooped top-right corner** (`border-top-right-radius:72px`) — a deliberate soft-shape exception that reads as a physical label/sticker, not a soft UI radius. Right: red **eyebrow** wrapped in `// … //` slashes (signage tic), charcoal display heading, body, a **checklist** (Barlow-bold rows with red Lucide `circle-check` icons), an optional **red stat card** (icon + big number + caption — one accent moment per section), an optional bold second paragraph, and the red CTA button. Stat/badge default values are honest & on-brand ("2 — States, one standard", "5★ — Star-rated service"), never fabricated years/counts — replace with real figures if available. The red appears in three small marks here (eyebrow, badge, stat, button) which is acceptable because they're a single coordinated accent family within one section, not scattered CTAs.

### Services Grid (reusable)
Centered red eyebrow + charcoal uppercase display heading, then a 3-col responsive grid (`.t5-services`, Cornerstone "Triple 5 Services Grid" element) of up to 6 white cards (hairline stone border, `--radius-lg`, soft shadow, 3px lift on hover). Each card's media is a **blueprint-grid placeholder** in one of three tones — **navy**→now charcoal (white hairlines), **clay**→now red-tinted (`#241611` field + red hairlines), **coal** (near-black) — with a centered Lucide `image` glyph + the service name in mono; a real image swaps the placeholder out. Body: uppercase Barlow title, stone description, red "READ MORE →" link (arrow nudges right on hover). Tones alternate down the grid for rhythm. Empty-title cards are skipped, so the same element serves 2–6 services on any page. *Reasoning:* the blueprint-tint placeholders keep the grid on-brand and intentional before real job photos exist (same motif as the hero/feature placeholders).

### Service Panel (`.t5-panel`, Cornerstone "Triple 5 Service Panel")
Dark charcoal-900 band → red intro card (340px column) + 2×3 icon-tile grid. Built 2026-09-18 from the owner's reference (which used navy — translated to charcoal, no blue). **Why the CTA is charcoal, not red:** it sits on the red card, so the section's one action inverts to the dark field colour instead of adding a second red; it keeps the uppercase Barlow button convention. Tiles: red Lucide icon (36px, 1.75 stroke) → Barlow 700 22px title → stone-400 body line; dividers are 1px hairlines at 10% white via grid gap, not borders, so corners stay clean under the rounded clip. Tile titles are sentence/title case (not uppercase) to match the reference and keep the grid quiet — the red card's heading carries the display weight. Home copy now mirrors the real service lines (Roofing/Siding/Repairs/Concrete/Remodeling/Framing) and every tile links to its service page. **Linked-tile affordance:** the whole tile is the link, but the *title* carries the signal — red-300 + a slide-in "→" on hover — instead of a per-tile button, keeping six tiles from becoming six CTAs.

### Why Choose Us (`.t5-why`, Cornerstone "Triple 5 Why Choose Us")
Charcoal-800 band (one step lighter than the Service Panel's charcoal-900 so consecutive dark bands still read as separate sections), centered uppercase display heading, 4 centered reason columns. **Icon treatment is an outline ring** (2px red circle, red 40px Lucide glyph, no fill) — a mark, not a button, so the four red circles don't compete with the page's real CTAs. Titles uppercase Barlow 700 19px at 0.06em (tightened from the 0.08em caps default so "ONE CREW, START TO FINISH" holds one line at 1370px); bodies stone-300 for softer contrast than pure white. Default copy is the owner's; "Licensed WA & ID" is a real claim, keep it accurate.

### Testimonials (`.t5-reviews`, Cornerstone "Triple 5 Testimonials")
Light stone-50 section (follows the dark Why band, alternating rhythm), centered uppercase display heading, 4-up white cards (hairline stone border, `--radius-lg`, soft shadow, lift on hover only when the card links out). Card anatomy: **initial avatar** on a red-100 disc with a red letter (a photo-free way to keep the accent present; Google/Yelp avatar hot-linking is unreliable and Yelp's terms are strict), uppercase Barlow name + muted location, **source badge** as a small outlined disc with the platform's own mark (Google multicolour G / Yelp red burst — the only non-brand colours allowed, because they're trademarks, not palette), **amber stars** using `--color-warning` (same as the hero review badges — one star colour site-wide), body copy 17px. **Filtering is a product rule, not a style one:** default minimum 4★ hides 1–3★; individual reviews can be hidden in Settings regardless of rating. Sample reviews are placeholders — never present them as real.

### FAQ (`.t5-faq`, Cornerstone "Triple 5 FAQ")
White section after the stone Testimonials band. Header is a two-column row — big uppercase display heading left, plain intro right — so the section reads as editorial rather than a centered "FAQ" label. Accordion items are **native `<details>`**: zero JS, keyboard/screen-reader semantics for free, and the markup survives Cornerstone's bake. Question rows are uppercase Barlow 700 20px with a **red +/– built from two CSS bars** (the vertical bar rotates away on open — functional motion, 200ms). Open state tints the item to `--surface-sunken` stone-100 rather than adding a border colour, keeping the accent to the glyph. Grid is `align-items:start` so an open answer never stretches its row-mate (matches the reference's uneven columns). FAQPage JSON-LD is emitted by default — questions are real SEO surface for a local contractor.

### Service Tabs (`.t5-tabs`, Cornerstone "Triple 5 Service Tabs") — the Our Services page
Two-band page: **stone-50 header band** (red eyebrow, huge uppercase H1, plain subtext, hairline bottom rule) then a white body. **Tab bar** is text-only uppercase Barlow with a 3px red underline on the active tab sitting on a stone hairline — no pills/boxes, so the five service names read as a table of contents, not buttons. Panels are content-left / media-right, `align-items:center`. **"Metal specialty" badge** is the one solid-red pill above a heading; only the roofing and siding tabs carry it, enforcing the "metal leads" principle visually. Checklist = 2-col red check marks (Lucide `check`, 2.4 stroke). **Panel CTA** is the red pill with a white circled arrow — same family as the header "Get a Free Quote" so the page's quote actions rhyme. Media placeholder is the charcoal blueprint grid with a mono caption (same motif as hero/services). Tabs degrade to stacked sections without JS — a11y/robustness decision, not a visual one. **Single-tab mode** (`.is-single`, bar omitted) is the standard **service-page header**: same band + intro panel, so the overview page and its child pages share one visual grammar. Service pages then follow with a Why band (charcoal) and an FAQ (white) — light/dark/light rhythm matching Home.

### Footer (`.t5-footer`, Cornerstone "Triple 5 Footer" in the site-wide cs_footer)
Charcoal-900 field, stone-300 text, white uppercase Barlow column headings. Four columns: brand (white 555 mark + blurb), quick links (plain text links, no arrows — the reference's arrows were dropped as noise), contact and hours with **red outline Lucide icons** as the only accent in the lists, plus **three solid-red round social buttons** — the footer's single filled-red moment, deliberately small. The **service-area map band** reuses the charcoal blueprint placeholder (neutral glyph, not red) so it reads as "photo/map coming", consistent with every other placeholder on the site. Bottom bar steps up to charcoal-800 with **IBM Plex Mono** for the legal line — mono is the brand's "spec/technical detail" voice and keeps the copyright quiet.

### Reasons + Media (`.t5-reasons`, Cornerstone "Triple 5 Reasons + Media")
Stone-50 section between the white FAQ and the white services grid. Left: reason list with **solid-red check discs** — filled because a hollow ring would read as the Why band's icon style; kept to 40px so six in a column don't overpower the page. Titles uppercase Barlow 22px, bodies one line. CTA is the squared solid-red section button (not a pill — pills are reserved for header/hero/tab quote actions). Right: 16:9 photo over a 16:9 **YouTube embed on `youtube-nocookie.com`** (privacy-enhanced, lazy) — both fall back to the charcoal blueprint placeholder (image glyph / play glyph) so the section never shows a broken hole. On tablet the media stack moves above the list.

### Logo Strip (reusable)
Horizontal partner/manufacturer band (`.t5-logos`, Cornerstone "Triple 5 Logo Strip" element). Optional uppercase muted heading over a centered flex row of logo items. Background tone: **navy** (renders charcoal-900) or **coal** (dark, carrying the faint blueprint grid) or **stone** (light) — dark is the default, matching the reference. Each item is either a real logo image or a placeholder (red Lucide `shield-check` mark + Barlow-bold name). Uploaded logos get a **mono treatment** by default (`filter:brightness(0) invert(1)` on dark bands → solid white; grayscale on light) so mismatched partner logos read as one tidy set; switch to "original" to keep full-color. Up to 8 slots, empty skipped. *Reasoning:* a logo strip's job is quiet credibility, so it stays monochrome and low-contrast (muted heading, single accent only on placeholder marks) rather than competing with the page's real CTAs. Default names are generic placeholders — never ship invented partner brands.

### Forms & Inputs
3px red focus ring (`--shadow-focus`). 1px stone borders, small radius. (Base treatment lives in `triple5-brand/base.css`.)

### Empty / Loading / Error states
_To be filled in as built._

---

## Interaction & Motion
- **Durations:** 120ms fast / 200ms base / 320ms slow (`--duration-*`).
- **Easing:** standard `cubic-bezier(0.2,0,0.2,1)` and ease-out `cubic-bezier(0.16,1,0.3,1)`.
- **Rules:** buttons darken on hover, drop 1px on press; cards lift 3px with deepened shadow on hover. No bounces, no infinite loops.
- **Reduced motion:** _to be wired — fall back to instant._

---

## Accessibility Commitments
- Focus always visible (red ring). Contrast target WCAG AA. _Red #8E231E on white passes AA (≈7.6:1)._

---

## Voice & Tone
Professional, down-to-earth, friendly, knowledgeable — straight answers from the crew. Speaks to **"you"** (homeowner) as **"we"** (the crew). Signature lines: "Stronger Together," "One contractor, the whole project," "Two states, one standard." No emoji. Numbers stay concrete (licensed WA & ID, 5 service lines, 2 markets).

---

## Anti-Patterns (explicitly rejected)
- Gradients (use solid color fields).
- Pill-shaped buttons / large soft radii (reserved for tags only).
- Cold gray neutrals (use warm stone).
- Emoji or Unicode symbol "icons."
- Spreading the red accent widely — it loses its "this is the one action" meaning.

---

## Conventions
- Update this file the moment a design decision is made — not at end of session.
- Capture the *reasoning*, not just the value.
- Tokens live in `triple5-brand/tokens/` and the `/triple5-design` skill — link, don't duplicate.
- **Themeco Pro/Cornerstone note:** components are built in the Cornerstone visual builder, not React. The design-system's JSX components are reference only — translate their patterns into Pro elements + the brand tokens.
