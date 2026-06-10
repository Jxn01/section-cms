# Copilot Instructions

## Project

- **Name:** parkoloabc
- **Domain:** parkoloabc.hu
- **Description:** Custom website replacing a WordPress installation on shared hosting (rackhost.hu). The WordPress files and database tables have been (or will be) removed entirely. This is a from-scratch custom site served from the same webroot.

## Infrastructure

- **Hosting:** rackhost.hu (shared hosting)
- **Webroot access:** ftp.example.com (FTP/SFTP)
- **Database:** MariaDB/MySQL at mysql.rackhost.hu, database name: `__REDACTED_DBUSER__`
- **phpMyAdmin:** https://www.rackhost.hu/wpma/14/index.php (DB management UI, accessible from browser)
- **Email domain:** parkoloabc.hu (details TBD)
- **Credentials:** stored in `.env` at the project root (never commit this file)

## Architecture

This project is a **micro-CMS** — a lightweight, modular, SEO-first content management system built from scratch in PHP.

### Core Concept

A single `index.php` front controller handles all requests. It resolves the URL slug, queries the database for page content and section blocks, assembles a complete HTML page server-side, and serves it to the browser. This guarantees full SEO crawlability — no client-side rendering.

### Routing

All requests are funneled through `index.php` via `.htaccess` rewrite rules. Clean URLs like `/szolgaltatasaink` are resolved by looking up the slug in the `pages` table.

### Database Schema

| Table | Purpose |
|-------|---------|
| `users` | Admin login (just the customer) |
| `site_settings` | Site name, logo, colors, fonts, global SEO defaults, contact info |
| `pages` | Slug, title, meta title/description/keywords, OG tags, status, sort order |
| `sections` | Belongs to a page — typed blocks (hero, text, image, gallery, CTA, etc.) with JSON content, sort order |
| `media` | Uploaded images with alt text (SEO!) |
| `menus` | Navigation items, order, links |

### Section Types (modular components)

Each section has a `type` and a JSON `content` field. Templates render each type:

- `hero` — background image, headline, subtitle, CTA button
- `text` — rich text block
- `image_text` — image + text side by side
- `gallery` — image grid
- `cta` — call to action banner
- `cards` — service cards in a grid
- More can be added anytime by creating a new template file

### SEO (main priority)

- Proper `<title>`, `<meta description>`, `<meta keywords>` per page
- Open Graph + Twitter Card meta tags
- Semantic HTML5 (`<header>`, `<main>`, `<section>`, `<article>`, `<footer>`)
- JSON-LD structured data (Organization, WebSite, WebPage)
- Canonical URLs
- Auto-generated `sitemap.xml` and `robots.txt`
- Clean URLs (no query string IDs)
- Alt text on every image
- Full server-side rendering — complete HTML delivered to the browser

### Admin Panel

- Lives at `/admin`
- Hardcoded login page with session-based auth
- Dashboard to manage pages, sections, media, site settings, menus
- Section ordering via drag-and-drop (simple JS)
- Image upload (PHP `move_uploaded_file` to `uploads/` directory)

### File Structure

```
index.php              ← front controller (all public requests)
.htaccess              ← rewrite all URLs to index.php
config/
  db.php               ← DB connection (reads credentials from .env)
core/
  router.php           ← URL → page resolution
  renderer.php         ← assembles full HTML from page + sections
  seo.php              ← meta tags, JSON-LD, sitemap generation
templates/
  base.php             ← HTML skeleton (doctype, head, body, footer)
  sections/            ← one PHP file per section type
admin/
  index.php            ← admin panel entry point
  auth.php             ← login / session logic
assets/
  css/                 ← stylesheets
  js/                  ← scripts
  uploads/             ← user-uploaded media
```

## General Guidelines

- Write clean, readable, and maintainable code.
- Prefer simplicity over cleverness.
- Follow existing code style and conventions in the repository.
- Use meaningful variable and function names.
- Add comments only when the *why* isn't obvious from the code itself.

## Code Style

- Use consistent indentation (spaces, not tabs).
- Keep functions short and focused on a single responsibility.
- Avoid deeply nested logic — extract helpers when nesting exceeds 2–3 levels.

## Git

- Write clear, concise commit messages in imperative mood (e.g., "Add parking logic").
- Keep commits focused on a single change.

## Testing

- Write tests for new functionality.
- Ensure existing tests pass before committing.

## Documentation

- Keep README and docs up to date with code changes.
- Document public APIs and non-obvious behavior.
