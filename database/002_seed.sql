-- =============================================
-- Parkoló ABC — Seed Data
-- Run after 001_schema.sql
-- =============================================

-- ----- Site Settings -----
INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name',        'Parkoló ABC'),
('site_tagline',     'Parkolási és beléptetési megoldások'),
('meta_description', 'Professzionális parkoló, beléptető és munkaidő-nyilvántartó rendszerek telepítése és karbantartása.'),
('meta_keywords',    'parkoló, beléptető rendszer, munkaidő nyilvántartás, sorompó, parkolórendszer'),
('contact_email',    'info@parkoloabc.hu'),
('contact_phone',    '+36 1 234 5678'),
('contact_address',  '1234 Budapest, Példa utca 1.'),
('primary_color',    '#0067FF'),
('secondary_color',  '#005EE9'),
('facebook_url',     ''),
('og_default_image', '/assets/images/og-default.jpg');

-- ----- Pages -----
INSERT INTO pages (slug, title, template, meta_title, meta_description, meta_keywords, og_title, og_description, status, sort_order) VALUES
('home',              'Kezdőlap',         'hero_cards_cta',      'Parkoló ABC – Parkolási és beléptetési megoldások',        'Professzionális parkoló, beléptető és munkaidő-nyilvántartó rendszerek telepítése és karbantartása Budapesten és országszerte.', 'parkoló, beléptető rendszer, sorompó', 'Parkoló ABC', 'Parkolási és beléptetési megoldások szakértője.', 'published', 1),
('szolgaltatasaink',  'Szolgáltatásaink',  'hero_cards_cta',      'Szolgáltatásaink – Parkoló ABC',                           'Parkoló rendszerek, beléptető rendszerek és munkaidő-nyilvántartó rendszerek telepítése, karbantartása, szervize.',             'szolgáltatások, telepítés, karbantartás',         'Szolgáltatásaink – Parkoló ABC', 'Teljes körű parkolási és beléptetési megoldások.', 'published', 2),
('rolunk',            'Rólunk',            'hero_text_imagetext', 'Rólunk – Parkoló ABC',                                     'Ismerje meg a Parkoló ABC csapatát. Több éves tapasztalat a parkolási és beléptetési rendszerek piacán.',                       'rólunk, bemutatkozás, csapat',                    'Rólunk – Parkoló ABC', 'Ismerje meg csapatunkat.', 'published', 3),
('kapcsolat',         'Kapcsolat',         'hero_two_text',       'Kapcsolat – Parkoló ABC',                                  'Lépjen velünk kapcsolatba! Parkoló ABC – telefon, e-mail, cím.',                                                               'kapcsolat, elérhetőség, telefon, e-mail',         'Kapcsolat – Parkoló ABC', 'Vegye fel velünk a kapcsolatot.', 'published', 4);

-- ----- Sections — Kezdőlap (home) -----
INSERT INTO sections (page_id, type, content, sort_order) VALUES
(1, 'hero', JSON_OBJECT(
    'heading',  'Parkoló ABC',
    'subtitle', 'Professzionális parkoló, beléptető és munkaidő-nyilvántartó rendszerek telepítése és karbantartása.',
    'image',    '/assets/images/hero-home.jpg',
    'cta_text', 'Szolgáltatásaink',
    'cta_url',  '/szolgaltatasaink'
), 1),
(1, 'cards', JSON_OBJECT(
    'heading', 'Szolgáltatásaink',
    'cards',   JSON_ARRAY(
        JSON_OBJECT('title', 'Parkoló rendszerek',               'description', 'Sorompók, parkolóházak, fizetőautomaták telepítése és karbantartása.',               'icon', '🅿️', 'link', '/szolgaltatasaink'),
        JSON_OBJECT('title', 'Beléptető rendszerek',             'description', 'Kártyás, kódos és biometrikus beléptető rendszerek tervezése és kivitelezése.', 'icon', '🔐', 'link', '/szolgaltatasaink'),
        JSON_OBJECT('title', 'Munkaidő nyilvántartó rendszerek', 'description', 'Munkaidő rögzítés, jelenléti ív kiváltása modern digitális megoldásokkal.',     'icon', '⏱️', 'link', '/szolgaltatasaink')
    )
), 2),
(1, 'cta', JSON_OBJECT(
    'heading',          'Kérjen ingyenes ajánlatot!',
    'subtitle',         'Vegye fel velünk a kapcsolatot, és készítünk Önnek személyre szabott ajánlatot.',
    'button_text',      'Kapcsolat',
    'button_url',       '/kapcsolat',
    'background_color', '#0067FF'
), 3);

-- ----- Sections — Szolgáltatásaink -----
INSERT INTO sections (page_id, type, content, sort_order) VALUES
(2, 'hero', JSON_OBJECT(
    'heading',  'Szolgáltatásaink',
    'subtitle', 'Teljes körű parkolási és beléptetési megoldások vállalkozásoknak és intézményeknek.',
    'image',    '/assets/images/hero-services.jpg',
    'cta_text', '',
    'cta_url',  ''
), 1),
(2, 'cards', JSON_OBJECT(
    'heading', 'Miben segíthetünk?',
    'cards',   JSON_ARRAY(
        JSON_OBJECT('title', 'Tervezés és tanácsadás',  'description', 'Felmérjük az igényeket és megtervezzük az optimális rendszert.',                  'icon', '📐', 'link', ''),
        JSON_OBJECT('title', 'Telepítés',               'description', 'Szakszerű telepítés tapasztalt csapatunkkal, minimális fennakadással.',             'icon', '🔧', 'link', ''),
        JSON_OBJECT('title', 'Karbantartás és szerviz', 'description', 'Rendszeres karbantartás és gyors hibaelhárítás, hogy minden zökkenőmentesen működjön.', 'icon', '🛠️', 'link', ''),
        JSON_OBJECT('title', 'Bővítés és fejlesztés',   'description', 'Meglévő rendszerek korszerűsítése és bővítése az új igényeknek megfelelően.',      'icon', '📈', 'link', '')
    )
), 2),
(2, 'cta', JSON_OBJECT(
    'heading',          'Érdekli valamelyik szolgáltatásunk?',
    'subtitle',         'Keressen minket bizalommal, és kérjen személyre szabott ajánlatot!',
    'button_text',      'Ajánlatot kérek',
    'button_url',       '/kapcsolat',
    'background_color', '#0067FF'
), 3);

-- ----- Sections — Rólunk -----
INSERT INTO sections (page_id, type, content, sort_order) VALUES
(3, 'hero', JSON_OBJECT(
    'heading',  'Rólunk',
    'subtitle', 'Ismerje meg a Parkoló ABC csapatát és történetünket.',
    'image',    '/assets/images/hero-about.jpg',
    'cta_text', '',
    'cta_url',  ''
), 1),
(3, 'text', JSON_OBJECT(
    'heading', 'Kik vagyunk?',
    'body',    '<p>A Parkoló ABC több éves tapasztalattal rendelkezik a parkolási és beléptetési rendszerek tervezése, telepítése és karbantartása terén. Csapatunk elkötelezett a magas színvonalú szolgáltatás és a megbízható technológiai megoldások iránt.</p><p>Célunk, hogy ügyfeleink számára a lehető legjobb és legköltséghatékonyabb megoldásokat nyújtsuk, legyen szó kis irodaházról vagy nagy parkolóházról.</p>'
), 2),
(3, 'image_text', JSON_OBJECT(
    'heading',        'Tapasztalat és megbízhatóság',
    'body',           '<p>Büszkék vagyunk arra, hogy ügyfeleink hosszú távon bíznak bennünk. Partnereinkkel szoros együttműködésben dolgozunk, hogy a legjobb eredményt érjük el minden projektben.</p>',
    'image',          '/assets/images/team.jpg',
    'image_alt',      'A Parkoló ABC csapata',
    'image_position', 'right'
), 3);

-- ----- Sections — Kapcsolat -----
INSERT INTO sections (page_id, type, content, sort_order) VALUES
(4, 'hero', JSON_OBJECT(
    'heading',  'Kapcsolat',
    'subtitle', 'Lépjen velünk kapcsolatba, örömmel állunk rendelkezésére!',
    'image',    '/assets/images/hero-contact.jpg',
    'cta_text', '',
    'cta_url',  ''
), 1),
(4, 'text', JSON_OBJECT(
    'heading', 'Elérhetőségeink',
    'body',    '<p><strong>Cím:</strong> 1234 Budapest, Példa utca 1.</p><p><strong>Telefon:</strong> +36 1 234 5678</p><p><strong>E-mail:</strong> <a href="mailto:info@parkoloabc.hu">info@parkoloabc.hu</a></p>'
), 2),
(4, 'text', JSON_OBJECT(
    'heading', 'Nyitvatartás',
    'body',    '<p><strong>Hétfő – Péntek:</strong> 8:00 – 17:00</p><p><strong>Szombat – Vasárnap:</strong> Zárva</p><p>Hétvégén és ünnepnapokon csak előzetes egyeztetés alapján.</p>'
), 3);

-- ----- Menus -----
INSERT INTO menus (label, url, page_id, sort_order) VALUES
('Kezdőlap',         '/',                  1, 1),
('Szolgáltatásaink', '/szolgaltatasaink',   2, 2),
('Rólunk',           '/rolunk',             3, 3),
('Kapcsolat',        '/kapcsolat',          4, 4);
