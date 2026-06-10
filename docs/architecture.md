# Architecture

Section CMS is a small, framework-free PHP application. The guiding idea: **a page is a list of typed content blocks, assembled into complete HTML on the server.** No client-side rendering, no build step.

## Request lifecycle

```
Browser
  │  GET /services
  ▼
.htaccess  ── rewrites everything (except /admin, /assets and real files) to:
  ▼
index.php (front controller)
  1. require config/db.php        → $pdo + SITE_BASE_URL
  2. require core/{i18n,router,renderer,seo}.php
  3. $slug = Router::resolve($_SERVER['REQUEST_URI'])
  4. Router::handleSpecialRoutes() → sitemap.xml / robots.txt (and exit)
  5. $settings = Renderer::getSettings(); I18n::init($settings['site_language'])
  6. $menus    = Renderer::getMenus()
  7. handle /keyword/{kw} and contact-form POST if applicable
  8. $page = Router::getPage($slug)   (404 if none)
  9. $seo  = SEO::buildMeta($page, $settings)
 10. $bodyHtml = Renderer::renderAllSections(Renderer::getSections($page.id))
 11. require templates/base.php      → final HTML
  ▼
Response (complete HTML document)
```

## Data model

| Table | Notes |
|-------|-------|
| `pages` | One row per page. `slug` is the URL, `template` is the preset it was created from, `page_type` is `page` or `article`, plus meta/OG fields, `status`, `sort_order`. |
| `sections` | Belongs to a page (`page_id`, `ON DELETE CASCADE`). `type` selects the template; `content` is a JSON blob specific to that type; `sort_order` defines vertical order. |
| `site_settings` | Key/value store: `site_name`, `site_language`, colors, logo, SEO defaults, contact info, SMTP. |
| `media` | Uploaded images + `alt_text`; `is_featured` flags images used by the hero slideshow. |
| `menus` | Navigation. `parent_id` enables nesting; links can point to a `page_id` (slug resolved live) or a raw `url`. |
| `contact_messages` | Contact-form submissions, with `is_read`. |
| `users` | Admin accounts (bcrypt password hashes). |

## Core classes (`core/`)

- **`Router`** — `resolve()` (URI → slug, `/` → `home`), `getPage()` (published lookup), and `handleSpecialRoutes()` which streams `sitemap.xml` (from published pages, with image entries) and `robots.txt`.
- **`Renderer`** — fetches settings, builds the nested menu tree, fetches a page's sections, and renders each via its template. `renderSection()` validates the `type` against `^[a-z0-9_]+$` before `require`-ing `templates/sections/<type>.php` (no path traversal). Also powers dynamic sections (keyword cloud, page list).
- **`SEO`** — `buildMeta()` merges page-level and site-level values; `renderMetaTags()` emits title/description/OG/Twitter/canonical; `renderJsonLd()` emits the schema.org graph. Locale comes from `I18n`.
- **`I18n`** — `init($locale)` loads `config/lang/<locale>.php` with English fallback; `t()/te()` translate (with `{placeholder}` substitution); `formatDate()` localizes dates; `ogLocale()`/`htmlLang()` feed SEO and `<html lang>`.
- **`sanitizeHtml()`** (`sanitize.php`) — whitelist sanitizer for WYSIWYG content: allowed tags/attributes only, strips `javascript:`/`data:` URLs, event handlers and dangerous CSS.

## Sections

Each section type is a self-contained template in `templates/sections/`. The template runs with `$content` (decoded JSON), `$section` (the row) and `$pdo` in scope, and echoes HTML.

To **add a section type**:

1. Create `templates/sections/my_block.php` that renders from `$content`.
2. Add its default content + label + description in `admin/page-edit.php` (`$sectionDefaults`, `$sectionLabels`, `$sectionDescriptions`) and the `admin.section.*` keys to the dictionaries.
3. Optionally add an editor UI for it in `page-edit.php` and a preview in `admin/components.php`.

No changes to the renderer or router are required — the type name maps directly to the file name.

## Templates (`config/templates.php`)

A *page template* is a preset list of sections inserted when a page is created. Each entry has a `name` (an i18n key) and a `sections` array of `{type, content}` defaults. These are starting points only; the editor can add/remove/reorder freely afterwards.

## Internationalization

The dictionaries are split into part-files (`config/lang/en/*.php`, `config/lang/hu/*.php`) and merged by `config/lang/{en,hu}.php`. Keys are namespaced (`common.*`, `site.*`, `admin.*`). The public site's locale is the `site_language` setting; the admin uses a per-session locale toggled via `?lang=`. English is always the fallback, so a missing translation degrades gracefully rather than rendering blank.

## Why server-side rendering?

The original goal was maximum SEO with minimum hosting cost. Server-rendered HTML is trivially crawlable, fast on cheap shared hosting, and needs no JavaScript toolchain. The trade-off — less rich interactivity — is acceptable for content/marketing sites, which is the target use case.
