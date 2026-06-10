# Copilot Instructions

## Project

- **Name:** Section CMS
- **Description:** A lightweight, modular, SEO-first content management system written from scratch in PHP. Pages are assembled server-side from typed "section" blocks, guaranteeing full crawlability. It is designed to run on cheap shared hosting — including hosting sold for WordPress — without a separate VPS.

## Architecture

A single `index.php` front controller handles every public request. It resolves the URL slug, loads the page and its section blocks from the database, assembles complete HTML server-side and returns it. There is no client-side rendering.

### Routing

All requests are funnelled through `index.php` via `.htaccess` rewrite rules. Clean URLs like `/services` are resolved by looking up the slug in the `pages` table. `sitemap.xml` and `robots.txt` are generated dynamically.

### Database schema

| Table | Purpose |
|-------|---------|
| `users` | Admin login |
| `site_settings` | Site name, language, logo, colors, SEO defaults, contact info, SMTP |
| `pages` | Slug, title, template, page type, meta/OG tags, status, sort order |
| `sections` | Belongs to a page — typed blocks with a JSON `content` field and sort order |
| `media` | Uploaded images with alt text |
| `menus` | Navigation items (supports nesting via `parent_id`) |
| `contact_messages` | Submissions from the contact form |

### Section types

Each section has a `type` and a JSON `content` field. One template file per type lives in `templates/sections/`. Add a new type by creating a new template file — the renderer picks it up automatically.

### Internationalization

The UI is bilingual (English default + Hungarian) via a small i18n layer:

- `core/i18n.php` — `I18n` class plus the `t()` / `te()` helpers.
- `config/lang/en/*.php` and `config/lang/hu/*.php` — dictionary part-files merged by `config/lang/{en,hu}.php`.
- The public site's language comes from the `site_language` setting; the admin panel has a per-session language switch.

**Rule:** never hardcode user-facing strings. Add a key to the appropriate part-file (English is the fallback; keep EN/HU key sets identical) and render it with `t()` / `te()`. Page/section *content* is authored in the admin panel and stored in the DB, so it is not part of the dictionaries.

### SEO (a primary goal)

- Per-page `<title>`, meta description and keywords
- Open Graph + Twitter Card tags, canonical URLs, JSON-LD (Organization, WebSite, WebPage/Article, BreadcrumbList)
- Semantic HTML5, alt text on images, auto-generated `sitemap.xml` / `robots.txt`
- Full server-side rendering

### Admin panel

Lives at `/admin`. Session-based auth, CSRF-protected forms. Manages pages, sections (drag-to-reorder), media, menus, settings, messages and the admin password.

### File structure

```
index.php              ← front controller (public requests)
.htaccess              ← rewrite rules, caching, security headers
config/
  db.php               ← DB connection + SITE_BASE_URL (reads .env)
  templates.php        ← page templates (preset section layouts)
  lang/                ← i18n dictionaries (en/, hu/)
core/
  i18n.php             ← translation layer
  router.php           ← URL → page resolution, sitemap/robots
  renderer.php         ← assembles HTML from page + sections
  seo.php              ← meta tags, JSON-LD
  sanitize.php         ← whitelist HTML sanitizer for WYSIWYG content
templates/
  base.php             ← HTML skeleton
  sections/            ← one file per section type
admin/                 ← back-office (auth, pages, media, menus, settings…)
database/              ← schema, demo seed, browser setup script
lib/PHPMailer/         ← bundled SMTP mailer
tools/deployer/        ← optional Go FTP/FTPS deployer
```

## Conventions

- Write clean, readable PHP. Prefer simplicity over cleverness; match the existing style.
- Spaces, not tabs. Keep functions short and single-purpose.
- Escape all output (`htmlspecialchars` / the `$h` helper / `te()`); use prepared statements for all queries.
- Comments in English, only where the *why* isn't obvious.
- Commit messages in imperative mood, focused on a single change.
- Keep the README and `docs/` up to date with code changes.
