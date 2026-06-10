# Section CMS

[![CI](https://github.com/Jxn01/section-cms/actions/workflows/ci.yml/badge.svg)](https://github.com/Jxn01/section-cms/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
![PHP 8.1+](https://img.shields.io/badge/PHP-8.1%2B-777BB4)

A lightweight, modular, **SEO-first content management system** written from scratch in plain PHP — no framework, no build step, no Composer dependencies (PHPMailer is vendored).

Pages are composed from typed **section blocks** (hero, text, gallery, cards, CTA, accordion, …) and assembled **entirely server-side**, so every page is fully crawlable. It runs comfortably on the cheapest shared hosting — including hosting that's sold and provisioned for WordPress (see [Running on WordPress hosting](#running-on-wordpress-hosting)).

> **Status:** portfolio / reference project. English-first codebase and docs; the public website is fully bilingual (English + Hungarian) through a small built-in i18n layer.

---

## Highlights

- **Section-based page builder** — 20+ block types, drag-to-reorder, JSON content per block.
- **SEO by default** — clean URLs, per-page meta + Open Graph + Twitter Cards, JSON-LD (`Organization`, `WebSite`, `WebPage`/`Article`, `BreadcrumbList`), canonical tags, and an auto-generated `sitemap.xml` / `robots.txt`.
- **Fully server-rendered** — complete HTML on first byte; no JavaScript required to see content.
- **Bilingual** — English + Hungarian out of the box, driven by a `site_language` setting; the admin panel has a live language switcher. Adding a language is one dictionary folder.
- **Self-contained admin** — pages, sections, media library, nested menus, site settings, contact-form inbox, SMTP test, password change. Session auth + CSRF protection.
- **Security-conscious** — prepared statements everywhere, whitelist HTML sanitizer for rich text, CSRF tokens, output escaping, and a strict Content-Security-Policy via `.htaccess`.
- **Runs anywhere** — PHP 8.x + MySQL/MariaDB. Deploy by copying files over FTP; an optional Go deployer is included.

---

## Tech stack

| Layer | Choice |
|-------|--------|
| Language | PHP 8.x (no framework) |
| Database | MySQL / MariaDB (PDO) |
| Front end | Server-rendered HTML, vanilla CSS & JS, [Quill](https://quilljs.com/) for rich text in the admin |
| Email | [PHPMailer](https://github.com/PHPMailer/PHPMailer) (vendored) over SMTP |
| Tooling | Optional Go FTP/FTPS deployer (`tools/deployer`) |

---

## Architecture in 30 seconds

```
Request → .htaccess rewrite → index.php (front controller)
        → Router::resolve()  → slug
        → load page + sections from DB
        → render each section template → HTML
        → wrap in base template (header/nav/footer) → response
```

Everything funnels through `index.php`. A page is a row in `pages`; its content is a list of rows in `sections`, each with a `type` and a JSON `content` blob. The renderer maps each `type` to a file in `templates/sections/`. Want a new block? Drop a new template file in — no core changes needed.

See [`docs/architecture.md`](docs/architecture.md) for the full tour.

---

## Project structure

```
index.php              Front controller (all public requests)
.htaccess              Rewrites, gzip, caching, security headers
config/
  db.php               PDO connection + canonical SITE_BASE_URL (reads .env)
  templates.php        Page templates (preset section layouts)
  lang/                i18n dictionaries: en/*.php, hu/*.php
core/
  i18n.php             Translation layer (t(), te(), I18n)
  router.php           URL → page resolution, sitemap.xml, robots.txt
  renderer.php         Builds HTML from a page's sections
  seo.php              Meta tags, Open Graph, JSON-LD
  sanitize.php         Whitelist HTML sanitizer for WYSIWYG content
templates/
  base.php             HTML skeleton (header, nav, footer)
  sections/            One template per section type
admin/                 Back-office (auth, pages, media, menus, settings, …)
database/              Schema, demo seed, browser setup script
lib/PHPMailer/         Vendored SMTP mailer
tools/deployer/        Optional Go FTP/FTPS deployer
docs/                  Architecture, deployment, user guide
```

---

## Quick start (local)

Requirements: PHP 8.1+ with PDO MySQL, and a MySQL/MariaDB database.

```bash
# 1. Clone
git clone https://github.com/Jxn01/section-cms.git
cd section-cms

# 2. Configure environment
cp .env.example .env
#    edit .env: DB_HOST, DB_NAME, DB_USER, DB_PASS, SITE_URL

# 3. Serve it
php -S localhost:8000

# 4. Create the schema + demo content + first admin user:
#    visit http://localhost:8000/database/setup.php once,
#    note the generated admin password, then DELETE the database/ folder.
```

Then open `http://localhost:8000/` for the site and `http://localhost:8000/admin/` to log in.

> The demo seed is a generic business/agency site (Home, Services, About, Contact) that exercises several section types. Delete or edit any of it in the admin panel — none of it is required by the CMS.

Full instructions, including production deployment, are in [`docs/deployment.md`](docs/deployment.md).

---

## Running on WordPress hosting

A lot of cheap shared hosting is marketed as "WordPress hosting": you get PHP, MySQL and FTP/SFTP access to a web root that ships with a WordPress install. Because Section CMS is just PHP + MySQL, **you can repurpose the hosting you already pay for** — replace the WordPress files in the web root with Section CMS and reuse the bundled database — instead of renting a separate VPS.

In short: back up and clear the WordPress files/tables, upload Section CMS to the web root, point `.env` at the existing database, and run the setup script. The included deployers (`deploy.sh` and the Go tool in `tools/deployer/`) push the files over FTP/FTPS.

This only applies to hosting **you own or are authorized to manage**. Step-by-step instructions and caveats are in [`docs/wordpress-hosting.md`](docs/wordpress-hosting.md).

---

## Internationalization

- The UI ships with **English (default)** and **Hungarian**.
- Public-site language follows the `site_language` setting; the admin panel has a per-session switcher (the `EN` / `HU` links in the sidebar).
- Strings live in `config/lang/en/*.php` and `config/lang/hu/*.php` (merged by `config/lang/{en,hu}.php`). English is the fallback for any missing key.
- **Add a language:** create `config/lang/<code>/` with the same keys, add the code to `I18n::SUPPORTED`, done. Page/section content itself is authored per page in the admin and can be in any language.

---

## Testing & CI

A dependency-free test runner exercises the core logic that doesn't need a
database — i18n (fallback, placeholders, locale dates), the HTML sanitizer,
routing, SEO meta/JSON-LD building, EN/HU dictionary parity, and the page
templates:

```bash
php tests/run.php
```

[GitHub Actions](.github/workflows/ci.yml) runs on every push: it lints all
PHP files and runs the test suite across **PHP 8.1–8.4**, and builds, vets and
format-checks the Go deployer.

## Security notes

- All database access uses **prepared statements** (PDO, emulation off).
- Rich-text content is run through a **whitelist HTML sanitizer** (`core/sanitize.php`).
- Admin forms are **CSRF-protected**; sessions use `HttpOnly` + `SameSite=Strict` cookies.
- `.htaccess` sets a strict **Content-Security-Policy** and the usual hardening headers, and forces HTTPS.
- Secrets live in `.env` (gitignored). **Never commit `.env` or `deploy.conf`.**
- After install, **delete `database/setup.php`** (and the whole `database/` folder) from the server and change the default admin password.

---

## License

Released under the [MIT License](LICENSE).
