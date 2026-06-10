<?php
// ─── Page Templates ───
// Each template defines a preset layout of section types.
// When creating a new page, the user picks a template and the
// sections are auto-generated with placeholder content (which the
// editor then replaces).
//
// The "name" of each template is an i18n key (see config/lang/*/
// admin_templates.php); the admin renders it through t(). Placeholder
// section content is written in the default locale (English).

return [
    'hero_two_text' => [
        'name' => 'admin.template.hero_two_text',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Page title',
                'subtitle' => 'Subtitle or short description.',
                'image'    => '/assets/images/hero-default.jpg',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'text', 'content' => [
                'heading' => 'First text block',
                'body'    => '<p>Write your text here…</p>',
            ]],
            ['type' => 'text', 'content' => [
                'heading' => 'Second text block',
                'body'    => '<p>Write your text here…</p>',
            ]],
        ],
    ],

    'hero_text_imagetext' => [
        'name' => 'admin.template.hero_text_imagetext',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Page title',
                'subtitle' => 'Subtitle or short description.',
                'image'    => '/assets/images/hero-default.jpg',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'text', 'content' => [
                'heading' => 'Text block',
                'body'    => '<p>Write your text here…</p>',
            ]],
            ['type' => 'image_text', 'content' => [
                'heading'        => 'Image and text',
                'body'           => '<p>Write your text here…</p>',
                'image'          => '/assets/images/placeholder.jpg',
                'image_alt'      => 'Image description',
                'image_position' => 'right',
            ]],
        ],
    ],

    'hero_cards_cta' => [
        'name' => 'admin.template.hero_cards_cta',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Page title',
                'subtitle' => 'Subtitle or short description.',
                'image'    => '/assets/images/hero-default.jpg',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'cards', 'content' => [
                'heading' => 'Cards',
                'cards'   => [
                    ['title' => 'Card 1', 'description' => 'Description…', 'icon' => '⭐', 'link' => ''],
                    ['title' => 'Card 2', 'description' => 'Description…', 'icon' => '⭐', 'link' => ''],
                    ['title' => 'Card 3', 'description' => 'Description…', 'icon' => '⭐', 'link' => ''],
                ],
            ]],
            ['type' => 'cta', 'content' => [
                'heading'          => 'Call to action',
                'subtitle'         => 'Short description above the button.',
                'button_text'      => 'Learn more',
                'button_url'       => '/contact',
                'background_color' => '#0067FF',
            ]],
        ],
    ],

    'hero_gallery' => [
        'name' => 'admin.template.hero_gallery',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Gallery',
                'subtitle' => '',
                'image'    => '/assets/images/hero-default.jpg',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'gallery', 'content' => [
                'heading' => 'Image gallery',
                'images'  => [
                    ['url' => '/assets/images/placeholder.jpg', 'alt' => 'Image 1'],
                    ['url' => '/assets/images/placeholder.jpg', 'alt' => 'Image 2'],
                    ['url' => '/assets/images/placeholder.jpg', 'alt' => 'Image 3'],
                ],
            ]],
        ],
    ],

    'simple_text' => [
        'name' => 'admin.template.simple_text',
        'sections' => [
            ['type' => 'text', 'content' => [
                'heading' => 'Page title',
                'body'    => '<p>Write your text here…</p>',
            ]],
        ],
    ],

    'article' => [
        'name' => 'admin.template.article',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Article title',
                'subtitle' => 'Short summary.',
                'image'    => '',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'text', 'content' => [
                'heading' => '',
                'body'    => '<p>The article text…</p>',
            ]],
            ['type' => 'cta', 'content' => [
                'heading'          => 'Want to know more?',
                'subtitle'         => 'Get in touch with us!',
                'button_text'      => 'Contact',
                'button_url'       => '/contact',
                'background_color' => '#0067FF',
            ]],
        ],
    ],

    'landing_page' => [
        'name' => 'admin.template.landing_page',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Main headline',
                'subtitle' => 'Subtitle text goes here.',
                'image'    => '',
                'cta_text' => 'Get started',
                'cta_url'  => '#services',
            ]],
            ['type' => 'ticker', 'content' => [
                'items' => [
                    ['text' => 'Latest news 1', 'link' => ''],
                    ['text' => 'Latest news 2', 'link' => ''],
                    ['text' => 'Latest news 3', 'link' => ''],
                ],
                'speed' => 30,
                'background_color' => '#0067FF',
                'text_color' => '#FFFFFF',
            ]],
            ['type' => 'cards', 'content' => [
                'heading' => 'Our services',
                'cards'   => [
                    ['title' => 'Service 1', 'description' => 'Description…', 'icon' => '🚀', 'link' => ''],
                    ['title' => 'Service 2', 'description' => 'Description…', 'icon' => '💡', 'link' => ''],
                    ['title' => 'Service 3', 'description' => 'Description…', 'icon' => '🎯', 'link' => ''],
                ],
            ]],
            ['type' => 'stats', 'content' => [
                'heading' => '',
                'background_color' => '#0067FF',
                'items' => [
                    ['number' => '100+', 'label' => 'Happy clients'],
                    ['number' => '50+', 'label' => 'Projects'],
                    ['number' => '10+', 'label' => 'Years of experience'],
                ],
            ]],
            ['type' => 'two_columns', 'content' => [
                'heading'    => 'About us',
                'left_body'  => '<p>Left column text…</p>',
                'right_body' => '<p>Right column text…</p>',
            ]],
            ['type' => 'testimonials', 'content' => [
                'heading' => 'What our clients say',
                'items'   => [
                    ['name' => 'Jane Doe', 'text' => 'Excellent service!', 'role' => 'CEO', 'image' => ''],
                ],
            ]],
            ['type' => 'cta', 'content' => [
                'heading'          => 'Ready to start?',
                'subtitle'         => 'Get in touch with us today!',
                'button_text'      => 'Contact',
                'button_url'       => '/contact',
                'background_color' => '#0067FF',
            ]],
        ],
    ],

    'contact_page' => [
        'name' => 'admin.template.contact_page',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Contact',
                'subtitle' => 'Get in touch with us!',
                'image'    => '',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'contact_form', 'content' => [
                'heading'         => 'Write to us',
                'success_message' => '',
            ]],
            ['type' => 'map', 'content' => [
                'heading'   => 'Map',
                'embed_url' => '',
                'height'    => '400',
            ]],
        ],
    ],

    'blog_listing' => [
        'name' => 'admin.template.blog_listing',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Articles',
                'subtitle' => 'Our latest articles.',
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
        'name' => 'admin.template.keywords_page',
        'sections' => [
            ['type' => 'text', 'content' => [
                'heading' => 'Topics',
                'body'    => '<p>Browse by topic!</p>',
            ]],
            ['type' => 'keywords_cloud', 'content' => [
                'heading' => '',
                'count'   => 50,
            ]],
        ],
    ],

    'product_catalog' => [
        'name' => 'admin.template.product_catalog',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Product catalog',
                'subtitle' => 'Explore our products and accessories.',
                'image'    => '',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'image_text', 'content' => [
                'heading'        => 'Product range overview',
                'body'           => '<p>Write your text here…</p>',
                'image'          => '',
                'image_alt'      => '',
                'image_position' => 'left',
            ]],
            ['type' => 'product_grid', 'content' => [
                'heading' => 'Products and accessories',
                'columns' => 5,
                'items'   => [
                    ['title' => 'Product 1', 'short_desc' => 'Short description on hover.', 'image' => '', 'image_alt' => '', 'url' => ''],
                    ['title' => 'Product 2', 'short_desc' => 'Short description on hover.', 'image' => '', 'image_alt' => '', 'url' => ''],
                    ['title' => 'Product 3', 'short_desc' => 'Short description on hover.', 'image' => '', 'image_alt' => '', 'url' => ''],
                ],
            ]],
            ['type' => 'link_banner', 'content' => [
                'text'             => 'Glossary and basics',
                'url'              => '/glossary',
                'icon'             => '📖',
                'background_color' => '#0067FF',
            ]],
            ['type' => 'cta', 'content' => [
                'heading'          => 'Request a quote!',
                'subtitle'         => 'A tailored solution for your needs.',
                'button_text'      => 'Get in touch',
                'button_url'       => '/contact',
                'background_color' => '#0067FF',
            ]],
        ],
    ],

    'reference_page' => [
        'name' => 'admin.template.reference_page',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Our references',
                'subtitle' => 'We are proud of our work so far.',
                'image'    => '',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'reference_gallery', 'content' => [
                'heading'  => 'Past projects',
                'projects' => [
                    ['title' => 'Project 1', 'cover_image' => '', 'cover_alt' => '', 'images' => []],
                ],
            ]],
            ['type' => 'cta', 'content' => [
                'heading'          => 'You could be next!',
                'subtitle'         => 'Request a free, no-obligation consultation.',
                'button_text'      => 'Get in touch',
                'button_url'       => '/contact',
                'background_color' => '#0067FF',
            ]],
        ],
    ],

    'faq_page' => [
        'name' => 'admin.template.faq_page',
        'sections' => [
            ['type' => 'hero', 'content' => [
                'heading'  => 'Frequently Asked Questions',
                'subtitle' => 'Answers to the most common questions.',
                'image'    => '',
                'cta_text' => '',
                'cta_url'  => '',
            ]],
            ['type' => 'accordion', 'content' => [
                'heading' => '',
                'items'   => [
                    ['question' => 'First question?', 'answer' => '<p>Answer text…</p>'],
                    ['question' => 'Second question?', 'answer' => '<p>Answer text…</p>'],
                    ['question' => 'Third question?', 'answer' => '<p>Answer text…</p>'],
                ],
            ]],
            ['type' => 'cta', 'content' => [
                'heading'          => 'Didn\'t find your answer?',
                'subtitle'         => 'Write to us!',
                'button_text'      => 'Contact',
                'button_url'       => '/contact',
                'background_color' => '#0067FF',
            ]],
        ],
    ],

    'homepage_slideshow' => [
        'name' => 'admin.template.homepage_slideshow',
        'sections' => [
            ['type' => 'hero_slideshow', 'content' => [
                'heading'      => 'Welcome',
                'subtitle'     => 'A short, compelling description of what you do.',
                'cta_text'     => 'Our services',
                'cta_url'      => '/services',
                'interval'     => 4,
                'overlay_boxes' => [
                    ['title' => 'Service one',   'url' => '/services',  'size' => 'large'],
                    ['title' => 'Service two',   'url' => '/services',  'size' => 'large'],
                    ['title' => 'Service three', 'url' => '/services',  'size' => 'small'],
                    ['title' => 'About us',      'url' => '/about',     'size' => 'large'],
                    ['title' => 'References',    'url' => '/references', 'size' => 'small'],
                    ['title' => 'Contact',       'url' => '/contact',   'size' => 'small'],
                ],
            ]],
            ['type' => 'cards', 'content' => [
                'heading' => 'Our services',
                'cards'   => [
                    ['title' => 'Service 1', 'description' => 'A short description of this service.', 'icon' => '🚀', 'link' => '/services'],
                    ['title' => 'Service 2', 'description' => 'A short description of this service.', 'icon' => '💡', 'link' => '/services'],
                    ['title' => 'Service 3', 'description' => 'A short description of this service.', 'icon' => '🎯', 'link' => '/services'],
                ],
            ]],
            ['type' => 'seo_hidden', 'content' => [
                'button_text' => 'Read more...',
                'body'        => '<p>A longer, SEO-friendly description of your business, services and the areas you serve.</p>',
            ]],
            ['type' => 'cta', 'content' => [
                'heading'          => 'Request a free quote!',
                'subtitle'         => 'Our experts will help you find the perfect solution.',
                'button_text'      => 'Get in touch',
                'button_url'       => '/contact',
                'background_color' => '#0067FF',
            ]],
        ],
    ],
];
