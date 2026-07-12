# Project Context — triple5

**Last updated:** 2026-07-12

> This is a living briefing document. Claude must revise it continuously during work — see CLAUDE.md for the rules. A Stop hook will remind you if source files are newer than this file.

---

## What This Is

The custom-code repo for **triple5.local**, the WordPress marketing site for **Triple 5 Construction LLC** — a licensed general contractor and metal roofing/siding specialist serving Spokane, WA and Coeur d'Alene, ID.

- **Local dev site:** `triple5.local` (WordPress 7.0.1 · PHP 8.2 · MySQL 8.4), managed by **Local** (Flywheel/WP Engine) at `~/Local Sites/triple5`.
- **This repo** tracks **custom code only** (theme + brand mu-plugin). WordPress core and content live in the Local site and are intentionally NOT version-controlled here.
- **Remote:** `github.com/jamsamcreative/triple5` (no commits pushed yet).
- **Connection model:** real files live here in the repo and are **symlinked into the Local site**, so edits here go live immediately on `triple5.local`. See README.md.

## Current State

Fresh WordPress install being branded. Active theme is **Themeco Pro** (v6.8.11) with Cornerstone builder bundled. Brand tokens are wired in site-wide via a must-use plugin. No git commits yet — everything below is uncommitted working-tree state.

### What's Working
- **`wp-content/themes/triple5/`** — custom starter **block theme** (style.css, theme.json, functions.php, templates/index.html, parts/header+footer). Symlinked into the Local site.
- **`wp-content/mu-plugins/triple5-brand.php` + `triple5-brand/`** — must-use plugin loading the design-system fonts + tokens + a brand base layer on the front end and in the Cornerstone builder. Symlinked into the Local site. Verified enqueued + HTTP 200.
- **Native Cornerstone header** — `cs_header` **"Primary Header" (ID 26)**, built via Cornerstone's Document API. Two bars in the `top` region: (1) clay top bar with contact text (left) + hours (right); (2) navy nav bar with logo image + nav-inline + pill "Get a Free Quote" button (arrow-in-circle graphic) + clay bottom rule. Active/hover nav link = clay. Editable in Pro → Headers (round-trip verified). Assigned **site-wide** (`site:entire-site`). Nav bound to the WP **primary** menu location. Live on all pages.
  - Known minor: CTA arrow-circle renders on the LEFT of the text; target has it on the right — trivial to move in the builder (couldn't force via `anchor_text_reverse`). Build scripts: scratchpad `build-header*.php` (used `cornerstone('Elements')->get_element()->get_defaults()` + safe-key override; assignment rules are `['condition'=>..., 'value'=>'', 'toggle'=>true]`).
- **WP menu "Primary"** (term 5): Home/About/Our Services/Projects/Contact Us, assigned to the `primary` location (Appearance → Menus). Nav pages: About (13), Our Services (14), Projects (15), Contact Us (16).
- **Home page** (ID 10) is the static front page, on Pro template **`template-blank-5.php`** ("No Container | Header, No Footer") so Pro renders the Cornerstone header (no footer placeholder).
- **`triple5-header` mu-plugin** (coded header) — now **DISABLED** (`triple5_header_should_render()` returns false); superseded by the Cornerstone header. Kept in repo for reference/rollback. Its earlier clay **top bar** (email/phone/hours) is NOT in the Cornerstone header yet.
- **Themeco Pro** installed and active; Cornerstone bundled inside the theme (no separate plugin).
- **Design system** installed as a personal Claude skill at `~/.claude/skills/triple5-design/` (`/triple5-design`).

### Recent Focus Areas (last ~5 sessions)
1. Connected this repo to the Local site (symlink model, custom-code-only, WP `.gitignore`, README).
2. Installed + activated the Themeco Pro theme; reviewed the design system + installed it as a skill.
3. Wired the design tokens/fonts into the live site via the `triple5-brand` mu-plugin; set up the project-context system.
4. Built the coded site **header** (`triple5-header` mu-plugin) from an approved screenshot; set Home as static front page on the no-chrome Pro template so it renders clean.

### Header approach (decided)
Header/footer are built as **coded mu-plugins** (fully controlled here, brand-token styled), NOT in the Cornerstone builder — the user chose this over a Pro-native builder header. Pro's default theme chrome is removed per-page via the `template-blank-6.php` page template. A footer is not built yet (front page currently has no footer).

### Known Issues / Tech Debt
- **Nothing committed yet** — needs an initial commit + push to `jamsamcreative/triple5` when ready.
- **Pro license not validated** — updates/template library/extensions locked until you enter the Themeco key in wp-admin (Pro/Cornerstone → Validation). Interactive, browser-only.
- **Pro global color/font pickers** in the Cornerstone UI still need to be set to match the brand tokens (interactive). The mu-plugin gives a CSS baseline; the builder's own swatches are separate.
- Substituted assets in the design system: Google Fonts (not licensed brand fonts), Lucide icons, placeholder imagery. Real job photos are the top asset need.
- `triple5` block theme is a minimal starter, not a finished design.

---

## Key Architectural Decisions

- **Repo is the source of truth; Local site holds symlinks.** Git can't version-control contents through a symlink pointing the other way, so real files live here and are symlinked into `~/Local Sites/triple5/app/public/wp-content/`. This gives both version control and live edits.
- **Custom code only in git.** `.gitignore` excludes WP core, default themes, uploads. Only `wp-content/themes/triple5/` and `wp-content/mu-plugins/triple5-brand*` are tracked.
- **Brand delivered as a must-use plugin**, not baked into the theme — so it applies regardless of active theme (currently Pro) and is trivially reversible.
- **WP-CLI** runs from `~/Local Sites/triple5/app/public` using Local's bundled binaries (see README for the env exports).

---

## Conventions

- Keep this file updated continuously during work sessions.
- Design decisions go in DESIGN_CONTEXT.md (auto-loads on UI work), not here.
- Commit/push only when the user asks.
