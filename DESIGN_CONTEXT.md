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
_Seeded from: `tokens/spacing.css`._ 4px base grid. 1200px max container, 720px narrow, 24px gutters. Generous 64–96px vertical section rhythm.

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
Full-bleed two-column hero (`.t5-hero`, Cornerstone "Triple 5 Hero" element). Left: display headline (Barlow 800, uppercase, ~62px, line-height 0.98) with **red accent words**, subtext, "Financing options available" label, translucent review badges (Google/Trustpilot — each becomes an `<a target=_blank>` when given a URL, subtle bg-lighten on hover). Right: dark glassy lead-form card (`--radius-lg`, hairline border, `--shadow-lg`) with red focus inputs and one red pill submit. Background: photo with a **left-weighted charcoal scrim** for legibility (the one sanctioned gradient — overlay only, not a fill), falling back to the **charcoal blueprint-grid placeholder** until a real job photo is set. *Why the scrim gradient is allowed despite the no-gradients rule:* it's a legibility overlay on a photo, not a decorative color field. Submit uses `--radius-pill` (exception to squared-corner rule — matches the header CTA's pill button, kept consistent for the single key action).

### Feature Section (reusable)
Two-column image + content block (`.t5-feature`, Cornerstone "Triple 5 Feature Section" element), for About/service/landing sections. Left (or right — flippable for alternating rows): rounded image (`--radius-xl`) with an optional **red experience badge** anchored bottom-left, given a **scooped top-right corner** (`border-top-right-radius:72px`) — a deliberate soft-shape exception that reads as a physical label/sticker, not a soft UI radius. Right: red **eyebrow** wrapped in `// … //` slashes (signage tic), charcoal display heading, body, a **checklist** (Barlow-bold rows with red Lucide `circle-check` icons), an optional **red stat card** (icon + big number + caption — one accent moment per section), an optional bold second paragraph, and the red CTA button. Stat/badge default values are honest & on-brand ("2 — States, one standard", "5★ — Star-rated service"), never fabricated years/counts — replace with real figures if available. The red appears in three small marks here (eyebrow, badge, stat, button) which is acceptable because they're a single coordinated accent family within one section, not scattered CTAs.

### Services Grid (reusable)
Centered red eyebrow + charcoal uppercase display heading, then a 3-col responsive grid (`.t5-services`, Cornerstone "Triple 5 Services Grid" element) of up to 6 white cards (hairline stone border, `--radius-lg`, soft shadow, 3px lift on hover). Each card's media is a **blueprint-grid placeholder** in one of three tones — **navy**→now charcoal (white hairlines), **clay**→now red-tinted (`#241611` field + red hairlines), **coal** (near-black) — with a centered Lucide `image` glyph + the service name in mono; a real image swaps the placeholder out. Body: uppercase Barlow title, stone description, red "READ MORE →" link (arrow nudges right on hover). Tones alternate down the grid for rhythm. Empty-title cards are skipped, so the same element serves 2–6 services on any page. *Reasoning:* the blueprint-tint placeholders keep the grid on-brand and intentional before real job photos exist (same motif as the hero/feature placeholders).

### Service Panel (`.t5-panel`, Cornerstone "Triple 5 Service Panel")
Dark charcoal-900 band → red intro card (340px column) + 2×3 icon-tile grid. Built 2026-09-18 from the owner's reference (which used navy — translated to charcoal, no blue). **Why the CTA is charcoal, not red:** it sits on the red card, so the section's one action inverts to the dark field colour instead of adding a second red; it keeps the uppercase Barlow button convention. Tiles: red Lucide icon (36px, 1.75 stroke) → Barlow 700 22px title → stone-400 body line; dividers are 1px hairlines at 10% white via grid gap, not borders, so corners stay clean under the rounded clip. Tile titles are sentence/title case (not uppercase) to match the reference and keep the grid quiet — the red card's heading carries the display weight. Default copy is the reference's; "Free 25-point roof report" is a placeholder claim — confirm before publishing (honesty principle).

### Why Choose Us (`.t5-why`, Cornerstone "Triple 5 Why Choose Us")
Charcoal-800 band (one step lighter than the Service Panel's charcoal-900 so consecutive dark bands still read as separate sections), centered uppercase display heading, 4 centered reason columns. **Icon treatment is an outline ring** (2px red circle, red 40px Lucide glyph, no fill) — a mark, not a button, so the four red circles don't compete with the page's real CTAs. Titles uppercase Barlow 700 19px at 0.06em (tightened from the 0.08em caps default so "ONE CREW, START TO FINISH" holds one line at 1370px); bodies stone-300 for softer contrast than pure white. Default copy is the owner's; "Licensed WA & ID" is a real claim, keep it accurate.

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
