# triple5

Custom code for the **triple5.local** WordPress site (developed in [Local](https://localwp.com/)).

## How this connects to the Local site

This repo is the source of truth for the site's **custom code only** — currently the
`triple5` block theme. WordPress core and content live in the Local site and are **not**
tracked here.

The custom code is **symlinked** into the running Local site, so edits made in this repo
take effect immediately on `triple5.local`:

```
~/.superset/projects/triple5/wp-content/themes/triple5   (real files, git-tracked)
        ▲
        │ symlink
~/Local Sites/triple5/app/public/wp-content/themes/triple5
```

## Recreating the symlink

If the Local site is recreated or the link is lost:

```bash
ln -s "$HOME/.superset/projects/triple5/wp-content/themes/triple5" \
      "$HOME/Local Sites/triple5/app/public/wp-content/themes/triple5"
```

## Site details

- URL: http://triple5.local
- WordPress: 7.0.1 · PHP 8.2 · MySQL 8.4
- Active theme: `triple5`

## WP-CLI

Run WP-CLI from the Local site's public dir (or use Local's "Open site shell"):

```bash
cd "$HOME/Local Sites/triple5/app/public"
wp theme list
```
