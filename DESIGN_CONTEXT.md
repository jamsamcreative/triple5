# Design Context — triple5

**Last updated:** 2026-07-12

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
- **Navy `--brand-primary` #15293C** — dominant brand color: dark sections, primary actions, trust. Roofs/structure.
- **Clay `--brand-accent` #C24B27** — the single accent (terracotta shingle warmth). Reserved for the most important CTA and highlights. Do not spread it around.
- **Steel blue `--brand-secondary` #3E6B89** — support: links, focus ring, sky.
- **Warm stone neutrals** (`--color-stone-*`, never cold gray) for text and surfaces. Backgrounds alternate stone-50 and white.
- Text: strong = navy-900, body = stone-700, muted = stone-500.

### Typography
_Seeded from: `tokens/typography.css` + `tokens/fonts.css` (Google Fonts substitutes — swap if licensed brand fonts exist)._
- **Barlow Semi Condensed** (700/800) — display + headings. Sturdy, condensed, signage-like.
- **Public Sans** — body + UI. Clean, legible.
- **IBM Plex Mono** — phone numbers, measurements, technical/spec detail.
- **Casing rule:** sentence case for body; **UPPERCASE with wide tracking** (`--tracking-caps` 0.08em) for display labels, buttons, eyebrows, nav — the industrial-signage feel.

### Spacing & Layout
_Seeded from: `tokens/spacing.css`._ 4px base grid. 1200px max container, 720px narrow, 24px gutters. Generous 64–96px vertical section rhythm.

### Elevation & Surfaces
_Seeded from: `tokens/effects.css`._ Soft, low, **warm-tinted** shadows (`rgba(20,30,43,…)`) — believable elevation, nothing glowing. 1px hairline stone borders on cards; 2px on buttons/focus. Featured cards get a 3px clay top rule.

### Corners
Small, **squared-off radii** (3–10px: `--radius-sm/md/lg`) — grounded, not pill-soft. `--radius-pill` reserved for tags/filters only.

### Iconography & Imagery
- **Lucide** outline icons (~2px stroke), functional accents only (phone, shield-check, map-pin, snowflake). Substitution — no brand icon set defined. Never emoji, never Unicode symbols.
- The **logo** (555 house monogram, "STRONGER TOGETHER" arch) is the one true mark — navy on light, white on dark. Do not redraw or alter it.
- Dark sections carry a faint **blueprint-grid** texture (subtle white hairlines) — the signature motif. Placeholder imagery uses the same treatment until real Inland-NW job photos replace it.

---

## Component Patterns

### Buttons
Display font, uppercase, wide tracking, `--radius-md` (6px), 2px border. **Primary** = solid navy; **accent** = solid clay (one per view, the key CTA); **outline/ghost** = navy text, fills with stone-100 on hover. Hover darkens (navy→navy-700, clay→clay-500); press darkens further + 1px translate down.

### Forms & Inputs
3px steel-blue focus ring (`--shadow-focus`). 1px stone borders, small radius. (Base treatment lives in `triple5-brand/base.css`.)

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
- Focus always visible (steel-blue ring). Contrast target WCAG AA. _Verify clay-on-white for any text use._

---

## Voice & Tone
Professional, down-to-earth, friendly, knowledgeable — straight answers from the crew. Speaks to **"you"** (homeowner) as **"we"** (the crew). Signature lines: "Stronger Together," "One contractor, the whole project," "Two states, one standard." No emoji. Numbers stay concrete (licensed WA & ID, 5 service lines, 2 markets).

---

## Anti-Patterns (explicitly rejected)
- Gradients (use solid color fields).
- Pill-shaped buttons / large soft radii (reserved for tags only).
- Cold gray neutrals (use warm stone).
- Emoji or Unicode symbol "icons."
- Spreading the clay accent widely — it loses its "this is the one action" meaning.

---

## Conventions
- Update this file the moment a design decision is made — not at end of session.
- Capture the *reasoning*, not just the value.
- Tokens live in `triple5-brand/tokens/` and the `/triple5-design` skill — link, don't duplicate.
- **Themeco Pro/Cornerstone note:** components are built in the Cornerstone visual builder, not React. The design-system's JSX components are reference only — translate their patterns into Pro elements + the brand tokens.
