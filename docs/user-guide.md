# User guide (admin panel)

This is a tour of the back-office at `/admin`. Log in with the credentials created during setup.

## Dashboard

The landing page shows counts (pages, articles, drafts, sections, media, unread messages) and quick links. Use the **EN / HU** links at the bottom of the sidebar to switch the admin panel's language at any time.

## Pages

**Pages** lists every page with its status and section count.

- **New page** — pick a *template* (a preset layout of sections), give it a title and a slug (the URL). The slug is auto-generated from the title if you leave it blank.
- **Edit** — opens the page editor.
- **Status** — `Draft` pages are hidden from the public site and from search engines; `Published` pages are live.
- **Type** — `Article` pages get extra SEO markup (author/date) and can be listed by the *Page list* section.

### The page editor

Top section: title, slug, status, type, featured image, and the **SEO** fieldset (meta title/description/keywords and Open Graph overrides). Fill the SEO fields with unique, relevant text — they're what appears in Google and on social shares.

Below that is the **sections** list — the actual page content. Each section is a block you can:

- **edit** inline (fields depend on the block type),
- **reorder** with the up/down controls (order = top-to-bottom on the page),
- **delete**.

Add a new block from the section-type picker; each type has a short description of what it does.

## Sections (block types)

A few of the 20+ types: **Hero** (banner with headline + CTA), **Text** (rich text), **Image + text**, **Gallery**, **Cards**, **CTA**, **Accordion** (FAQ), **Testimonials**, **Stats**, **Video**, **Map**, **Contact form**, **Page list**, **Keyword cloud**, **Hero slideshow**, **Product grid**. The **Components** page in the admin shows a live preview of every type.

## Media

Upload images (JPEG/PNG/GIF/WebP, up to 10 MB). **Always set alt text** — it matters for accessibility and Google image search. Images can be picked from the library wherever a section asks for an image.

## Menu

Build the header navigation. Each item has a label and either links to a page or to a custom URL. Set a **parent** item to create a dropdown submenu (nesting is unlimited). Drag to reorder. The footer sitemap is generated from the same menu tree.

## Settings

Global site configuration: **site name**, **language** (the public site's default language), tagline, default meta description/keywords, contact email/phone/address, brand colors, logo and display mode, default Open Graph image, and **SMTP** for contact-form email notifications. Use **Send test email** to verify SMTP.

## Messages

Submissions from the public contact form land here (and are emailed to you if SMTP is configured). Mark them read or delete them.

## Password

Change the admin password. Do this immediately after the first login.

## The public site

- Clean URLs map to page slugs (`/about` → the page with slug `about`).
- `/<your-domain>/sitemap.xml` and `/robots.txt` are generated automatically.
- The site language (chrome strings, dates) follows the **Settings → Language** value; page content is whatever you typed.
