# Design: Triple 5 Hero — Cornerstone Custom Element

**Date:** 2026-07-22
**Status:** Approved (design), pending implementation plan

## Goal

A full-bleed hero header for the homepage, placed directly below the Cornerstone
nav. Modeled on the approved reference (Desktop screenshot 2026-07-22) and the
`triple5-design` skill's `Home.jsx` hero, adapted to Triple 5 branding. Delivered
as a **registered Cornerstone custom Element** ("Triple 5 Hero") so the site owner
edits text, background image, badges, and form settings directly in the Pro/
Cornerstone builder.

## Non-Goals

- Not building a general-purpose form plugin. The form is specific to this hero.
- Not styling the rest of the homepage. Only the hero unit + its lead handling.
- Not redrawing the logo or introducing new brand tokens.

## Layout & Content (source of truth: `triple5-design` Home.jsx hero)

Two-column hero over a dark, overlaid background image:

- **Left column**
  - H1 (Barlow Semi Condensed 800, uppercase, ~62px, line-height ~0.98), 3 lines
    with clay (`--brand-accent`) accent words. Default:
    "**TRIPLE 5** ROOFING / ALWAYS AIMING FOR A / **5 STAR** SERVICE".
  - Subtext (~18px, white 82%): "We deliver fast, honest and expert metal roofing
    and siding that protects your home and your peace of mind."
  - "FINANCING OPTIONS AVAILABLE" display label.
  - Review badges row (repeatable): Google 5.0, Trustpilot 4.9 defaults —
    circular monogram + rating + "★★★★★" + "<source> reviews".
- **Right column** — glassy lead-form card (dark translucent, hairline border,
  `--radius-lg`): heading "Request a roofing, siding or repair callout"; fields
  Full Name, Email, Phone, Address, "Where did you hear about us?", Message;
  clay "SEND REQUEST" submit (full width, space-between, arrow).
- **Background** — media image with a left-weighted dark gradient overlay for text
  legibility. Until a real photo is set, a navy + blueprint-grid placeholder
  (brand signature motif) renders instead.

Responsive: single column under ~900px (form drops below the copy). Focus rings
steel-blue; squared radii; warm shadows; no gradients on solid fields (overlay
gradient on the photo is allowed for legibility, consistent with Home.jsx).

## Architecture

New must-use plugin **`wp-content/mu-plugins/triple5-hero/`** + loader
`triple5-hero.php` (mirrors the `triple5-brand` symlink + tokens-in-builder
pattern). Three responsibilities:

### 1. Element registration (`element.php`)
Register "Triple 5 Hero" through **Cornerstone's Element API**. The exact API
signature varies by Cornerstone version, so the FIRST implementation step is to
read the installed Pro/Cornerstone element-registration code and match it — do not
guess. Expected shape: a definition (title, icon, group), a `values` map of
defaults (all copy above), a `builder`/controls definition grouping the inspector
into Content / Background / Badges / Form, and a `render` callback that outputs the
hero markup from those values (escaped).

**Controls exposed:**
- Content: 3 headline lines + per-line accent-word field, subtext, financing text,
  eyebrow/label toggles.
- Background: image (media picker — the swappable background image), overlay
  darkness (slider), "use blueprint placeholder" toggle (default ON).
- Badges: repeatable {source label, rating}.
- Form: heading text, recipient email, success message, per-field on/off,
  submit-button label.

### 2. Lead handling (`leads.php`)
- Register CPT **`t5_lead`** ("Leads", admin-only UI, not public) with the six
  fields stored as post meta; admin columns show name/email/phone/date.
- Submit target: `admin-post.php` with `action=t5_lead` (works logged-out via
  `admin_post_nopriv_`). Handler: verify nonce + honeypot, sanitize each field,
  `wp_insert_post` the lead, `wp_mail()` to the recipient control (subject includes
  name + market), then redirect back to the referring page with a
  `#t5-hero-success` flag. Missing required fields redirect back with an error flag.
- On Local, `wp_mail` is caught by Local's Mailpit; delivers for real once
  deployed. No external service.

### 3. Styling (`hero.css`)
Scoped `.t5-hero` styles built entirely on existing brand tokens. Enqueued on the
front end AND inside the Cornerstone builder preview (same `cornerstone_before_boot_app`
hook the brand plugin uses), so the builder preview matches the front end.

## Placement

Once the element is registered, drop "Triple 5 Hero" into Home (page ID 10) content
in Cornerstone, below the nav. Can be placed programmatically via the Document API
(as the header was) or by hand in the builder.

## Data Flow

Visitor submits form → `admin-post.php?action=t5_lead` → validate/sanitize →
`wp_insert_post(t5_lead)` + `wp_mail(recipient)` → redirect back with success anchor →
hero shows inline success message. Owner reviews leads under **Leads** in wp-admin.

## Testing / Verification

1. Element appears in the builder element list; dropping it renders the hero with
   default Triple 5 copy.
2. Editing headline/subtext/background image/recipient in the builder round-trips
   (save → reload → front end reflects changes).
3. Front-end submit with valid data: new `t5_lead` post exists with correct meta,
   Mailpit shows the email, page shows success message.
4. Submit with missing required field: error flag, no post created.
5. Honeypot filled: silently rejected.
6. Blueprint placeholder shows when no image set; real image shows when set.
7. Builder preview visually matches the front end (tokens loaded).
8. Responsive: form stacks under the copy on narrow viewports.

## Risks / Open Questions

- **Element API signature** — mitigated by reading installed Cornerstone first.
- **Local mail** — Mailpit only; real delivery is a post-deploy concern.
- Reuses the symlink + mu-plugin conventions already established; no new infra.
