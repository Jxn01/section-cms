<?php
// ─── Page Templates ───
// Each template defines a preset layout of section types.
// When creating a new page, the user picks a template and
// sections are auto-generated with placeholder content.

return [
    'hero_two_text' => [
        'name' => 'Fejléckép + Két szövegblokk',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Oldal címe',
                'subtitle' => 'Alcím vagy rövid leírás.',
                'image'    => '/assets/images/hero-default.jpg',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'text', 'content' => [
                'heading' => 'Első szövegblokk',
                'body'    => '<p>Írja ide a szöveget…</p>',
            ]],
            ['type' => 'text', 'content' => [
                'heading' => 'Második szövegblokk',
                'body'    => '<p>Írja ide a szöveget…</p>',
            ]],
        ],
    ],

    'hero_text_imagetext' => [
        'name' => 'Fejléckép + Szöveg + Kép & szöveg',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Oldal címe',
                'subtitle' => 'Alcím vagy rövid leírás.',
                'image'    => '/assets/images/hero-default.jpg',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'text', 'content' => [
                'heading' => 'Szövegblokk',
                'body'    => '<p>Írja ide a szöveget…</p>',
            ]],
            ['type' => 'image_text', 'content' => [
                'heading'        => 'Kép és szöveg',
                'body'           => '<p>Írja ide a szöveget…</p>',
                'image'          => '/assets/images/placeholder.jpg',
                'image_alt'      => 'Kép leírása',
                'image_position' => 'right',
            ]],
        ],
    ],

    'hero_cards_cta' => [
        'name' => 'Fejléckép + Kártyák + CTA',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Oldal címe',
                'subtitle' => 'Alcím vagy rövid leírás.',
                'image'    => '/assets/images/hero-default.jpg',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'cards', 'content' => [
                'heading' => 'Kártyák',
                'cards'   => [
                    ['title' => 'Kártya 1', 'description' => 'Leírás…', 'icon' => '⭐', 'link' => ''],
                    ['title' => 'Kártya 2', 'description' => 'Leírás…', 'icon' => '⭐', 'link' => ''],
                    ['title' => 'Kártya 3', 'description' => 'Leírás…', 'icon' => '⭐', 'link' => ''],
                ],
            ]],
            ['type' => 'cta', 'content' => [
                'heading'          => 'Cselekvésre ösztönzés',
                'subtitle'         => 'Rövid leírás a gomb fölött.',
                'button_text'      => 'Tovább',
                'button_url'       => '/kapcsolat',
                'background_color' => '#0067FF',
            ]],
        ],
    ],

    'hero_gallery' => [
        'name' => 'Fejléckép + Galéria',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Galéria',
                'subtitle' => '',
                'image'    => '/assets/images/hero-default.jpg',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'gallery', 'content' => [
                'heading' => 'Képgaléria',
                'images'  => [
                    ['url' => '/assets/images/placeholder.jpg', 'alt' => 'Kép 1'],
                    ['url' => '/assets/images/placeholder.jpg', 'alt' => 'Kép 2'],
                    ['url' => '/assets/images/placeholder.jpg', 'alt' => 'Kép 3'],
                ],
            ]],
        ],
    ],

    'simple_text' => [
        'name' => 'Egyszerű szöveges oldal',
        'sections' => [
            ['type' => 'text', 'content' => [
                'heading' => 'Oldal címe',
                'body'    => '<p>Írja ide a szöveget…</p>',
            ]],
        ],
    ],

    'article' => [
        'name' => 'Cikk (szöveges + kapcsolódó tartalom)',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Cikk címe',
                'subtitle' => 'Rövid összefoglaló.',
                'image'    => '',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'text', 'content' => [
                'heading' => '',
                'body'    => '<p>A cikk szövege…</p>',
            ]],
            ['type' => 'cta', 'content' => [
                'heading'          => 'Szeretne többet tudni?',
                'subtitle'         => 'Vegye fel velünk a kapcsolatot!',
                'button_text'      => 'Kapcsolat',
                'button_url'       => '/kapcsolat',
                'background_color' => '#0067FF',
            ]],
        ],
    ],

    'landing_page' => [
        'name' => 'Landing oldal (teljes)',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Főcím',
                'subtitle' => 'Alcím szöveg ide.',
                'image'    => '',
                'cta_text' => 'Kezdjük el',
                'cta_url'  => '#szolgaltatasok',
            ]],
            ['type' => 'ticker', 'content' => [
                'items' => [
                    ['text' => 'Legfrissebb hír 1', 'link' => ''],
                    ['text' => 'Legfrissebb hír 2', 'link' => ''],
                    ['text' => 'Legfrissebb hír 3', 'link' => ''],
                ],
                'speed' => 30,
                'background_color' => '#0067FF',
                'text_color' => '#FFFFFF',
            ]],
            ['type' => 'cards', 'content' => [
                'heading' => 'Szolgáltatásaink',
                'cards'   => [
                    ['title' => 'Szolgáltatás 1', 'description' => 'Leírás…', 'icon' => '🚀', 'link' => ''],
                    ['title' => 'Szolgáltatás 2', 'description' => 'Leírás…', 'icon' => '💡', 'link' => ''],
                    ['title' => 'Szolgáltatás 3', 'description' => 'Leírás…', 'icon' => '🎯', 'link' => ''],
                ],
            ]],
            ['type' => 'stats', 'content' => [
                'heading' => '',
                'background_color' => '#0067FF',
                'items' => [
                    ['number' => '100+', 'label' => 'Elégedett ügyfél'],
                    ['number' => '50+', 'label' => 'Projekt'],
                    ['number' => '10+', 'label' => 'Év tapasztalat'],
                ],
            ]],
            ['type' => 'two_columns', 'content' => [
                'heading'    => 'Rólunk',
                'left_body'  => '<p>Bal oldali szöveg…</p>',
                'right_body' => '<p>Jobb oldali szöveg…</p>',
            ]],
            ['type' => 'testimonials', 'content' => [
                'heading' => 'Ügyfeleink mondták',
                'items'   => [
                    ['name' => 'Kiss Péter', 'text' => 'Kiváló szolgáltatás!', 'role' => 'Ügyvezető', 'image' => ''],
                ],
            ]],
            ['type' => 'cta', 'content' => [
                'heading'          => 'Készen áll?',
                'subtitle'         => 'Lépjen velünk kapcsolatba még ma!',
                'button_text'      => 'Kapcsolat',
                'button_url'       => '/kapcsolat',
                'background_color' => '#0067FF',
            ]],
        ],
    ],

    'faq_page' => [
        'name' => 'GYIK oldal',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Gyakran Ismételt Kérdések',
                'subtitle' => 'Válaszok a leggyakoribb kérdésekre.',
                'image'    => '',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'accordion', 'content' => [
                'heading' => '',
                'items'   => [
                    ['question' => 'Első kérdés?', 'answer' => '<p>Válasz szövege…</p>'],
                    ['question' => 'Második kérdés?', 'answer' => '<p>Válasz szövege…</p>'],
                    ['question' => 'Harmadik kérdés?', 'answer' => '<p>Válasz szövege…</p>'],
                ],
            ]],
            ['type' => 'cta', 'content' => [
                'heading'          => 'Nem találta a választ?',
                'subtitle'         => 'Írjon nekünk!',
                'button_text'      => 'Kapcsolat',
                'button_url'       => '/kapcsolat',
                'background_color' => '#0067FF',
            ]],
        ],
    ],

    'contact_page' => [
        'name' => 'Kapcsolat oldal (űrlap + térkép)',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Kapcsolat',
                'subtitle' => 'Lépjen velünk kapcsolatba!',
                'image'    => '',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'contact_form', 'content' => [
                'heading'         => 'Írjon nekünk',
                'success_message' => 'Köszönjük az üzenetet! Hamarosan felvesszük Önnel a kapcsolatot.',
            ]],
            ['type' => 'map', 'content' => [
                'heading'   => 'Térkép',
                'embed_url' => '',
                'height'    => '400',
            ]],
        ],
    ],

    'blog_listing' => [
        'name' => 'Cikk lista oldal',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Cikkek',
                'subtitle' => 'Legfrissebb cikkeink.',
                'image'    => '',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'page_list', 'content' => [
                'heading'   => '',
                'page_type' => 'article',
                'count'     => 20,
            ]],
        ],
    ],

    'keywords_page' => [
        'name' => 'Kulcsszó felhő oldal',
        'sections' => [
            ['type' => 'text', 'content' => [
                'heading' => 'Témakörök',
                'body'    => '<p>Böngésszen témakörök szerint!</p>',
            ]],
            ['type' => 'keywords_cloud', 'content' => [
                'heading' => '',
                'count'   => 50,
            ]],
        ],
    ],

    'product_catalog' => [
        'name' => 'Termékkatalógus oldal (hero + termékrács + linksáv + CTA)',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Termékkatalógus',
                'subtitle' => 'Fedezze fel termékeinket és kiegészítőinket.',
                'image'    => '',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'image_text', 'content' => [
                'heading'        => 'Termékcsalád bemutatása',
                'body'           => '<p>Írja ide a szöveget…</p>',
                'image'          => '',
                'image_alt'      => '',
                'image_position' => 'left',
            ]],
            ['type' => 'product_grid', 'content' => [
                'heading' => 'Termékek és kiegészítők',
                'columns' => 5,
                'items'   => [
                    ['title' => 'Termék 1', 'short_desc' => 'Rövid leírás hover-re.', 'image' => '', 'image_alt' => '', 'url' => ''],
                    ['title' => 'Termék 2', 'short_desc' => 'Rövid leírás hover-re.', 'image' => '', 'image_alt' => '', 'url' => ''],
                    ['title' => 'Termék 3', 'short_desc' => 'Rövid leírás hover-re.', 'image' => '', 'image_alt' => '', 'url' => ''],
                ],
            ]],
            ['type' => 'link_banner', 'content' => [
                'text'             => 'Alapfogalmak és jelmagyarázat',
                'url'              => '/alapfogalmak',
                'icon'             => '📖',
                'background_color' => '#0067FF',
            ]],
            ['type' => 'cta', 'content' => [
                'heading'          => 'Kérjen árajánlatot!',
                'subtitle'         => 'Személyre szabott megoldás az Ön igényeire.',
                'button_text'      => 'Kapcsolatfelvétel',
                'button_url'       => '/kapcsolat',
                'background_color' => '#0067FF',
            ]],
        ],
    ],

    'reference_page' => [
        'name' => 'Referencia oldal (hero + galéria + CTA)',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Referenciáink',
                'subtitle' => 'Büszkék vagyunk eddigi munkáinkra.',
                'image'    => '',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'reference_gallery', 'content' => [
                'heading'  => 'Korábbi projektjeink',
                'projects' => [
                    ['title' => 'Projekt 1', 'cover_image' => '', 'cover_alt' => '', 'images' => []],
                ],
            ]],
            ['type' => 'cta', 'content' => [
                'heading'          => 'Legyen Ön a következő!',
                'subtitle'         => 'Kérjen ingyenes helyszíni felmérést.',
                'button_text'      => 'Kapcsolatfelvétel',
                'button_url'       => '/kapcsolat',
                'background_color' => '#0067FF',
            ]],
        ],
    ],

    'faq_page' => [
        'name' => 'Alapfogalmak / GYIK (hero + harmonikák + CTA)',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Alapfogalmak',
                'subtitle' => 'Szakkifejezések és fogalmak érthetően.',
                'image'    => '',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'accordion', 'content' => [
                'heading' => 'Fogalmak',
                'items'   => [
                    ['question' => 'Kérdés 1', 'answer' => '<p>Válasz…</p>'],
                    ['question' => 'Kérdés 2', 'answer' => '<p>Válasz…</p>'],
                ],
            ]],
            ['type' => 'cta', 'content' => [
                'heading'          => 'Kérdése van?',
                'subtitle'         => 'Írjon nekünk!',
                'button_text'      => 'Kapcsolatfelvétel',
                'button_url'       => '/kapcsolat',
                'background_color' => '#0067FF',
            ]],
        ],
    ],

    'homepage_slideshow' => [
        'name' => 'Főoldal diavetítéssel (slideshow + kártyák + SEO szöveg + CTA)',
        'sections' => [
            ['type' => 'hero_slideshow', 'content' => [
                'heading'      => 'Parkoló ABC',
                'subtitle'     => 'Parkolórendszerek, beléptető rendszerek, munkaidő nyilvántartás.',
                'cta_text'     => 'Szolgáltatásaink',
                'cta_url'      => '/szolgaltatasaink',
                'interval'     => 4,
                'overlay_boxes' => [
                    ['title' => 'Fizetős parkolórendszerek',   'url' => '/fizetos-parkolo-rendszerek',    'size' => 'large'],
                    ['title' => 'Beléptetős parkolórendszerek', 'url' => '/beleptetos-parkolo-rendszerek', 'size' => 'large'],
                    ['title' => 'Vegyes rendszerek',            'url' => '/vegyes-parkolo-rendszerek',     'size' => 'small'],
                    ['title' => 'Beléptető rendszerek',         'url' => '/belepteto-rendszerek',          'size' => 'large'],
                    ['title' => 'Munkaidő nyilvántartás',       'url' => '/munkaido-nyilvantarto-rendszerek', 'size' => 'small'],
                    ['title' => 'Referenciáink',                'url' => '/referenciaink',                 'size' => 'small'],
                ],
            ]],
            ['type' => 'cards', 'content' => [
                'heading' => 'Szolgáltatásaink',
                'cards'   => [
                    ['title' => 'Parkoló rendszerek', 'description' => 'Fizetős, beléptetős és vegyes parkolórendszerek.', 'icon' => '🅿️', 'link' => '/parkolo-rendszerek'],
                    ['title' => 'Beléptető rendszerek', 'description' => 'RFID, kártyás és biometrikus beléptetés.', 'icon' => '🚧', 'link' => '/belepteto-rendszerek'],
                    ['title' => 'Munkaidő nyilvántartás', 'description' => 'Terminálok és szoftver a pontos jelenléti adatokért.', 'icon' => '⏱️', 'link' => '/munkaido-nyilvantarto-rendszerek'],
                ],
            ]],
            ['type' => 'seo_hidden', 'content' => [
                'button_text' => 'Tovább olvasom...',
                'body'        => '<p>Parkoló rendszerek, beléptető rendszerek és munkaidő nyilvántartó rendszerek telepítése és karbantartása Magyarországon.</p>',
            ]],
            ['type' => 'cta', 'content' => [
                'heading'          => 'Kérjen ingyenes árajánlatot!',
                'subtitle'         => 'Szakértőink segítenek megtalálni a tökéletes megoldást.',
                'button_text'      => 'Kapcsolatfelvétel',
                'button_url'       => '/kapcsolat',
                'background_color' => '#0067FF',
            ]],
        ],
    ],
];
