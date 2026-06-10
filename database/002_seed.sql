-- =============================================
-- Section CMS — Demo Seed Data
-- Run after 001_schema.sql.
--
-- This is a generic business/agency demo that exercises several
-- section types. Edit or delete it in the admin panel — none of it
-- is required by the CMS itself.
-- =============================================

-- ----- Site Settings -----
INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name',         'Section CMS'),
('site_tagline',      'A lightweight, SEO-first CMS'),
('site_language',     'en'),
('meta_description',  'A clean, modular demo site built with Section CMS — a lightweight, SEO-first PHP content management system.'),
('meta_keywords',     'cms, website, php, seo, sections'),
('contact_email',     'hello@example.com'),
('contact_phone',     '+1 555 010 0000'),
('contact_address',   '123 Example Street, Anytown'),
('primary_color',     '#0067FF'),
('secondary_color',   '#005EE9'),
('facebook_url',      ''),
('logo_url',          ''),
('logo_display_mode', 'none'),
('og_default_image',  '/assets/images/og-default.jpg');

-- ----- Pages -----
INSERT INTO pages (slug, title, template, page_type, meta_title, meta_description, meta_keywords, og_title, og_description, status, sort_order) VALUES
('home',     'Home',     'hero_cards_cta',      'page', 'Section CMS – A lightweight, SEO-first CMS',  'A clean, modular demo site built with Section CMS — a lightweight, SEO-first PHP content management system.', 'cms, website, php, seo', 'Section CMS', 'A lightweight, SEO-first CMS.', 'published', 1),
('services', 'Services', 'hero_cards_cta',      'page', 'Services – Section CMS',                      'Everything you can build with Section CMS: pages, sections, media and SEO — all server-rendered.',            'services, features',     'Services – Section CMS', 'What we offer.', 'published', 2),
('about',    'About',    'hero_text_imagetext', 'page', 'About – Section CMS',                         'Learn more about this demo company and the people behind it.',                                                'about, team, company',   'About – Section CMS', 'Get to know us.', 'published', 3),
('contact',  'Contact',  'contact_page',        'page', 'Contact – Section CMS',                       'Get in touch — phone, email and address.',                                                                    'contact, email, phone',  'Contact – Section CMS', 'Get in touch.', 'published', 4);

-- ----- Sections — Home -----
INSERT INTO sections (page_id, type, content, sort_order) VALUES
(1, 'hero', JSON_OBJECT(
    'heading',  'Build a fast, SEO-first website',
    'subtitle', 'Section CMS assembles every page server-side from modular content blocks — no page builder bloat, fully crawlable.',
    'image',    '/assets/images/hero-home.jpg',
    'cta_text', 'See what you can build',
    'cta_url',  '/services'
), 1),
(1, 'cards', JSON_OBJECT(
    'heading', 'Why Section CMS?',
    'cards',   JSON_ARRAY(
        JSON_OBJECT('title', 'Modular sections', 'description', 'Compose pages from typed blocks: hero, text, gallery, cards, CTA and more.', 'icon', '🧩', 'link', '/services'),
        JSON_OBJECT('title', 'SEO by default',   'description', 'Clean URLs, meta tags, Open Graph, JSON-LD and an auto sitemap on every page.', 'icon', '🔍', 'link', '/services'),
        JSON_OBJECT('title', 'Runs anywhere',    'description', 'Plain PHP and MySQL — deploy on cheap shared hosting, no build step required.',  'icon', '🚀', 'link', '/services')
    )
), 2),
(1, 'cta', JSON_OBJECT(
    'heading',          'Ready to get started?',
    'subtitle',         'Reach out and we will help you get up and running.',
    'button_text',      'Contact us',
    'button_url',       '/contact',
    'background_color', '#0067FF'
), 3);

-- ----- Sections — Services -----
INSERT INTO sections (page_id, type, content, sort_order) VALUES
(2, 'hero', JSON_OBJECT(
    'heading',  'Services',
    'subtitle', 'A full set of building blocks for your website.',
    'image',    '/assets/images/hero-services.jpg',
    'cta_text', '',
    'cta_url',  ''
), 1),
(2, 'cards', JSON_OBJECT(
    'heading', 'What we offer',
    'cards',   JSON_ARRAY(
        JSON_OBJECT('title', 'Design & consulting', 'description', 'We map your needs and design the right structure for your site.', 'icon', '📐', 'link', ''),
        JSON_OBJECT('title', 'Build & launch',      'description', 'Page assembly, content modelling and a smooth go-live.',          'icon', '🔧', 'link', ''),
        JSON_OBJECT('title', 'Maintenance',         'description', 'Ongoing updates and quick fixes so everything keeps running.',     'icon', '🛠️', 'link', ''),
        JSON_OBJECT('title', 'Growth & SEO',        'description', 'Improve and extend your site as your needs evolve.',              'icon', '📈', 'link', '')
    )
), 2),
(2, 'stats', JSON_OBJECT(
    'heading', '',
    'background_color', '#0067FF',
    'items', JSON_ARRAY(
        JSON_OBJECT('number', '20+', 'label', 'Section types'),
        JSON_OBJECT('number', '100%', 'label', 'Server-rendered'),
        JSON_OBJECT('number', '0', 'label', 'Build steps')
    )
), 3),
(2, 'cta', JSON_OBJECT(
    'heading',          'Interested in any of these?',
    'subtitle',         'Get in touch and request a tailored proposal.',
    'button_text',      'Request a quote',
    'button_url',       '/contact',
    'background_color', '#0067FF'
), 4);

-- ----- Sections — About -----
INSERT INTO sections (page_id, type, content, sort_order) VALUES
(3, 'hero', JSON_OBJECT(
    'heading',  'About us',
    'subtitle', 'Get to know the team and our story.',
    'image',    '/assets/images/hero-about.jpg',
    'cta_text', '',
    'cta_url',  ''
), 1),
(3, 'text', JSON_OBJECT(
    'heading', 'Who we are',
    'body',    '<p>We are a small team that loves building fast, accessible websites. This page is demo content — replace it with your own story in the admin panel.</p><p>Our goal is to deliver the best, most cost-effective solutions for our clients, from a single landing page to a full company site.</p>'
), 2),
(3, 'image_text', JSON_OBJECT(
    'heading',        'Experience and reliability',
    'body',           '<p>We are proud that our clients trust us over the long term. We work closely with our partners to get the best result on every project.</p>',
    'image',          '/assets/images/team.jpg',
    'image_alt',      'Our team',
    'image_position', 'right'
), 3);

-- ----- Sections — Contact -----
INSERT INTO sections (page_id, type, content, sort_order) VALUES
(4, 'hero', JSON_OBJECT(
    'heading',  'Contact',
    'subtitle', 'Get in touch — we are happy to help!',
    'image',    '/assets/images/hero-contact.jpg',
    'cta_text', '',
    'cta_url',  ''
), 1),
(4, 'text', JSON_OBJECT(
    'heading', 'Our details',
    'body',    '<p><strong>Address:</strong> 123 Example Street, Anytown</p><p><strong>Phone:</strong> +1 555 010 0000</p><p><strong>Email:</strong> <a href="mailto:hello@example.com">hello@example.com</a></p>'
), 2),
(4, 'contact_form', JSON_OBJECT(
    'heading',         'Send us a message',
    'success_message', ''
), 3);

-- ----- Menus -----
INSERT INTO menus (label, url, page_id, sort_order) VALUES
('Home',     '/',         1, 1),
('Services', '/services', 2, 2),
('About',    '/about',    3, 3),
('Contact',  '/contact',  4, 4);
