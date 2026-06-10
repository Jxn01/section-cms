<?php
// Admin — Component showcase page (admin.components.*).
return [
    // ── Page chrome ──
    'admin.components.page_title'        => 'Component catalog',
    'admin.components.help_intro'        => 'This page shows every available section type.',
    'admin.components.help_body'         => 'For each component you can see a live preview, the description and the editable fields.',
    'admin.components.help_add_before'   => 'To add sections, open a page with the',
    'admin.components.help_add_after'    => 'button, then scroll to the "Add new section" area.',
    'admin.components.jump_to'           => 'Jump to:',
    'admin.components.editable_fields'   => 'Editable fields:',
    'admin.components.label_image'       => 'Image',

    // ── Type labels ──
    'admin.components.hero_label'              => 'Hero (header image)',
    'admin.components.text_label'              => 'Text',
    'admin.components.image_text_label'        => 'Image + text',
    'admin.components.cards_label'             => 'Cards',
    'admin.components.cta_label'               => 'CTA (call to action)',
    'admin.components.gallery_label'           => 'Gallery',
    'admin.components.ticker_label'            => 'Ticker (news bar)',
    'admin.components.accordion_label'         => 'Accordion (FAQ)',
    'admin.components.video_label'             => 'Video',
    'admin.components.divider_label'           => 'Divider',
    'admin.components.two_columns_label'       => 'Two columns',
    'admin.components.testimonials_label'      => 'Testimonials',
    'admin.components.stats_label'             => 'Numbers / Statistics',
    'admin.components.page_list_label'         => 'Page/article list',
    'admin.components.map_label'               => 'Map',
    'admin.components.contact_form_label'      => 'Contact form',
    'admin.components.keywords_cloud_label'    => 'Keyword cloud',
    'admin.components.hero_slideshow_label'    => 'Hero slideshow',
    'admin.components.product_grid_label'      => 'Product grid (hover description)',
    'admin.components.seo_hidden_label'        => 'Hidden SEO text',
    'admin.components.link_banner_label'       => 'Link banner (reference)',
    'admin.components.reference_gallery_label' => 'Reference gallery',
    'admin.components.sitemap_label'           => 'Sitemap',
    'admin.components.tudasmorzsak_label'      => 'Knowledge bites',

    // ── Type descriptions ──
    'admin.components.hero_desc'              => 'Full-width header image with a large heading, subheading and an optional CTA button. Ideal for the top of the page to grab attention right away.',
    'admin.components.text_desc'              => 'A simple text block with a heading and formattable content (bold, lists, links, etc.). The most frequently used section type.',
    'admin.components.image_text_desc'        => 'Image and text side by side. The image can be on the left or right. Perfect for presenting a service or product.',
    'admin.components.cards_desc'             => 'Cards in a grid, each with an icon, title and description. Ideal for listing services or benefits.',
    'admin.components.cta_desc'               => 'A call-to-action bar with a background color, text and a button. Use it in the middle or at the end of the page to activate visitors.',
    'admin.components.gallery_desc'           => 'Image gallery in a grid layout. Images can be enlarged on click. Ideal for showcasing references or projects.',
    'admin.components.ticker_desc'            => 'A horizontally scrolling text strip. For displaying eye-catching news, promotions or important information.',
    'admin.components.accordion_desc'         => 'Expandable question-and-answer items. Perfect for FAQ (Frequently Asked Questions) pages. Google likes it too, because it generates FAQ structured data.',
    'admin.components.video_desc'             => 'YouTube or Vimeo video embed. The video displays responsively on every device.',
    'admin.components.divider_desc'           => 'A visual divider line, dots, wave or empty space. For creating a visual break between sections.',
    'admin.components.two_columns_desc'       => 'Two-column text layout. Both columns are formattable (bold, lists, links, etc.).',
    'admin.components.testimonials_desc'      => 'Customer testimonials on cards, with a name, role and an optional photo. Builds trust with new visitors.',
    'admin.components.stats_desc'             => 'Highlight numbers/statistics with a large font size (e.g. "100+ Clients", "15 Years of experience"). Background color can be set.',
    'admin.components.page_list_desc'         => 'Automatic page or article list. The system lists pages of the given type (article/page) with title, description and image.',
    'admin.components.map_desc'               => 'Embedded Google Maps map. Paste the Google Maps embed URL to display an office or site location.',
    'admin.components.contact_form_desc'      => 'Contact form (name, e-mail, phone, message). Incoming messages can be read in the Messages menu.',
    'admin.components.keywords_cloud_desc'    => 'Automatic keyword cloud — it gathers the keywords of every page and displays the most common ones as links. Excellent for internal linking.',
    'admin.components.hero_slideshow_desc'    => 'Homepage slideshow built from images flagged as featured in Media, with automatic image transitions. Navigation boxes over the image.',
    'admin.components.product_grid_desc'      => 'Grid of products/devices with images. On hover the description appears, on click it navigates to the detail page.',
    'admin.components.seo_hidden_desc'        => 'Hidden expandable text — Google indexes it, but on the website it only appears after clicking a button.',
    'admin.components.link_banner_desc'       => 'A wide colored bar that points to another page. With an icon, text and an arrow.',
    'admin.components.reference_gallery_desc' => 'A gallery grouped by projects. Cover images + detail images on hover.',
    'admin.components.sitemap_desc'           => 'Automatic, hierarchical sitemap based on the menu structure and the published pages.',
    'admin.components.tudasmorzsak_desc'      => 'Small knowledge boxes with a title, short description and a link. They slide in from the right in random order.',

    // ── Editable fields: hero ──
    'admin.components.hero_field_heading'    => 'Heading',
    'admin.components.hero_field_subheading' => 'Subheading',
    'admin.components.hero_field_bg_url'     => 'Background image URL',
    'admin.components.hero_field_cta_text'   => 'CTA button text',
    'admin.components.hero_field_cta_url'    => 'CTA button URL',

    // ── Editable fields: text ──
    'admin.components.text_field_heading' => 'Heading',
    'admin.components.text_field_content' => 'Content (WYSIWYG editor)',

    // ── Editable fields: image_text ──
    'admin.components.image_text_field_heading'   => 'Heading',
    'admin.components.image_text_field_content'   => 'Content (WYSIWYG)',
    'admin.components.image_text_field_image_url' => 'Image URL',
    'admin.components.image_text_field_image_alt' => 'Image alt text',
    'admin.components.image_text_field_image_pos' => 'Image position (left/right)',

    // ── Editable fields: cards ──
    'admin.components.cards_field_heading' => 'Section heading',
    'admin.components.cards_field_cards'   => 'Cards: icon, title, description, link (repeatable)',

    // ── Editable fields: cta ──
    'admin.components.cta_field_heading'     => 'Heading',
    'admin.components.cta_field_subheading'  => 'Subheading',
    'admin.components.cta_field_button_text' => 'Button text',
    'admin.components.cta_field_button_url'  => 'Button URL',
    'admin.components.cta_field_bg_color'    => 'Background color',

    // ── Editable fields: gallery ──
    'admin.components.gallery_field_heading' => 'Heading',
    'admin.components.gallery_field_images'  => 'Images JSON (url + alt text)',

    // ── Editable fields: ticker ──
    'admin.components.ticker_field_items'      => 'Items: text + optional link',
    'admin.components.ticker_field_bg_color'   => 'Background color',
    'admin.components.ticker_field_text_color' => 'Text color',
    'admin.components.ticker_field_speed'      => 'Speed (sec)',

    // ── Editable fields: accordion ──
    'admin.components.accordion_field_heading' => 'Heading',
    'admin.components.accordion_field_qa'      => 'Question–answer pairs (repeatable)',

    // ── Editable fields: video ──
    'admin.components.video_field_heading' => 'Heading',
    'admin.components.video_field_url'     => 'Video URL (YouTube/Vimeo)',
    'admin.components.video_field_type'    => 'Type (YouTube/Vimeo)',

    // ── Editable fields: divider ──
    'admin.components.divider_field_style' => 'Style (line/dots/wave/empty space)',
    'admin.components.divider_field_size'  => 'Size (compact/normal/wide)',

    // ── Editable fields: two_columns ──
    'admin.components.two_columns_field_heading' => 'Heading (optional)',
    'admin.components.two_columns_field_left'    => 'Left column (WYSIWYG)',
    'admin.components.two_columns_field_right'   => 'Right column (WYSIWYG)',

    // ── Editable fields: testimonials ──
    'admin.components.testimonials_field_heading' => 'Heading',
    'admin.components.testimonials_field_items'   => 'Testimonials: name, role, testimonial text, image URL (repeatable)',

    // ── Editable fields: stats ──
    'admin.components.stats_field_heading'  => 'Heading (optional)',
    'admin.components.stats_field_bg_color' => 'Background color',
    'admin.components.stats_field_numbers'  => 'Numbers: number + label (repeatable)',

    // ── Editable fields: page_list ──
    'admin.components.page_list_field_heading' => 'Heading',
    'admin.components.page_list_field_filter'  => 'Page type filter (article/page/all)',
    'admin.components.page_list_field_count'   => 'Count',

    // ── Editable fields: map ──
    'admin.components.map_field_heading' => 'Heading',
    'admin.components.map_field_url'     => 'Google Maps embed URL',
    'admin.components.map_field_height'  => 'Height (px)',

    // ── Editable fields: contact_form ──
    'admin.components.contact_form_field_heading' => 'Heading',
    'admin.components.contact_form_field_success' => 'Successful submission message',

    // ── Editable fields: keywords_cloud ──
    'admin.components.keywords_cloud_field_heading' => 'Heading',
    'admin.components.keywords_cloud_field_max'     => 'Maximum number of keywords',

    // ── Editable fields: hero_slideshow ──
    'admin.components.hero_slideshow_field_heading'    => 'Heading',
    'admin.components.hero_slideshow_field_subheading' => 'Subheading',
    'admin.components.hero_slideshow_field_cta_text'   => 'CTA button text',
    'admin.components.hero_slideshow_field_cta_url'    => 'CTA button URL',
    'admin.components.hero_slideshow_field_interval'   => 'Image transition interval (sec)',
    'admin.components.hero_slideshow_field_nav'        => 'Navigation boxes: title, URL, size (large/small)',

    // ── Editable fields: product_grid ──
    'admin.components.product_grid_field_heading'  => 'Heading',
    'admin.components.product_grid_field_columns'  => 'Number of columns',
    'admin.components.product_grid_field_products' => 'Products: title, short description, image URL, image alt, link URL',

    // ── Editable fields: seo_hidden ──
    'admin.components.seo_hidden_field_button'  => 'Button text',
    'admin.components.seo_hidden_field_content' => 'Content (WYSIWYG editor)',

    // ── Editable fields: link_banner ──
    'admin.components.link_banner_field_text'     => 'Text',
    'admin.components.link_banner_field_icon'     => 'Icon (emoji)',
    'admin.components.link_banner_field_url'      => 'Link URL',
    'admin.components.link_banner_field_bg_color' => 'Background color',

    // ── Editable fields: reference_gallery ──
    'admin.components.reference_gallery_field_heading'  => 'Heading',
    'admin.components.reference_gallery_field_projects' => 'Projects: title, cover image URL, cover alt, additional images (url + alt)',

    // ── Editable fields: sitemap ──
    'admin.components.sitemap_field_heading' => 'Heading',

    // ── Editable fields: tudasmorzsak ──
    'admin.components.tudasmorzsak_field_heading' => 'Heading',
    'admin.components.tudasmorzsak_field_items'   => 'Items: title, short description, link URL (repeatable)',

    // ── Preview: hero ──
    'admin.components.hero_preview_title'    => 'Welcome to Section CMS',
    'admin.components.hero_preview_subtitle' => 'Professional solutions for over 15 years',
    'admin.components.hero_preview_button'   => 'Our services',

    // ── Preview: text ──
    'admin.components.text_preview_title' => 'About us',
    'admin.components.text_preview_p1'    => 'Our company has been helping businesses grow since 2008. Our clients include small startups, established companies and public organizations alike.',
    'admin.components.text_preview_p2'    => 'Our goal is to offer <strong>reliable</strong> and <em>cost-effective</em> solutions to all our partners.',

    // ── Preview: image_text ──
    'admin.components.image_text_preview_title' => 'Our solutions',
    'admin.components.image_text_preview_body'  => 'Our modern solutions help your business work faster and more reliably. The system adapts automatically to your needs and ensures a smooth experience.',

    // ── Preview: cards ──
    'admin.components.cards_preview_title'    => 'Our services',
    'admin.components.cards_preview_c1_title' => 'Consulting',
    'admin.components.cards_preview_c1_body'  => 'Planning and implementation of tailor-made business solutions.',
    'admin.components.cards_preview_c2_title' => 'Fast delivery',
    'admin.components.cards_preview_c2_body'  => 'Reliable, on-time delivery for projects of any size.',
    'admin.components.cards_preview_c3_title' => '24/7 support',
    'admin.components.cards_preview_c3_body'  => 'Phone, email and chat support whenever you need it.',

    // ── Preview: cta ──
    'admin.components.cta_preview_title'  => 'Request a free quote!',
    'admin.components.cta_preview_body'   => 'Get in touch with us and our experts will develop a tailor-made solution.',
    'admin.components.cta_preview_button' => 'Get in touch',

    // ── Preview: gallery ──
    'admin.components.gallery_preview_title' => 'Our references',

    // ── Preview: ticker ──
    'admin.components.ticker_preview_1' => 'We have launched a new feature!',
    'admin.components.ticker_preview_2' => '500+ satisfied customers',
    'admin.components.ticker_preview_3' => 'Read our latest case study',
    'admin.components.ticker_preview_4' => 'Call now: +36 1 234 5678',

    // ── Preview: accordion ──
    'admin.components.accordion_preview_title' => 'Frequently Asked Questions',
    'admin.components.accordion_preview_q1'    => 'How long does onboarding take?',
    'admin.components.accordion_preview_a1'    => 'Depending on the plan, onboarding takes 3–10 working days. Beforehand we hold a free consultation to map your needs.',
    'admin.components.accordion_preview_q2'    => 'Is there a guarantee on the service?',
    'admin.components.accordion_preview_q3'    => 'What payment options are available?',

    // ── Preview: video ──
    'admin.components.video_preview_title'   => 'Demo video',
    'admin.components.video_preview_caption' => 'YouTube / Vimeo video',

    // ── Preview: divider ──
    'admin.components.divider_preview_above' => 'Section above',
    'admin.components.divider_preview_below' => 'Section below',
    'admin.components.divider_style_line'    => 'Line',
    'admin.components.divider_style_dots'    => 'Dots',
    'admin.components.divider_style_wave'    => 'Wave',
    'admin.components.divider_style_space'   => 'Empty space',

    // ── Preview: two_columns ──
    'admin.components.two_columns_preview_title'       => 'Two-column layout',
    'admin.components.two_columns_preview_left_title'  => 'Left column',
    'admin.components.two_columns_preview_left_body'   => 'The left-hand content goes here. It supports <strong>formatted text</strong>, lists and links.',
    'admin.components.two_columns_preview_right_title' => 'Right column',
    'admin.components.two_columns_preview_right_body'  => 'The right-hand content goes here. Both columns have their own WYSIWYG editor.',
    'admin.components.two_columns_preview_item1'       => 'First item',
    'admin.components.two_columns_preview_item2'       => 'Second item',
    'admin.components.two_columns_preview_item3'       => 'Third item',
    'admin.components.two_columns_preview_item4'       => 'Fourth item',

    // ── Preview: testimonials ──
    'admin.components.testimonials_preview_title'   => 'What our clients said',
    'admin.components.testimonials_preview_t1_text' => 'They did excellent work, the system works perfectly!',
    'admin.components.testimonials_preview_t1_name' => 'John Smith',
    'admin.components.testimonials_preview_t1_role' => 'Managing Director, ABC Ltd.',
    'admin.components.testimonials_preview_t2_text' => 'Fast installation, professional team, highly recommended!',
    'admin.components.testimonials_preview_t2_name' => 'Eve Brown',
    'admin.components.testimonials_preview_t2_role' => 'Property manager',

    // ── Preview: stats ──
    'admin.components.stats_preview_label1' => 'Satisfied customers',
    'admin.components.stats_preview_label2' => 'Years of experience',
    'admin.components.stats_preview_label3' => 'Completed projects',
    'admin.components.stats_preview_label4' => 'Customer support',

    // ── Preview: page_list ──
    'admin.components.page_list_preview_title'    => 'Our latest articles',
    'admin.components.page_list_preview_a1_title' => 'How to choose the right plan',
    'admin.components.page_list_preview_a1_body'  => 'Choosing the right plan is not an easy task. In our article we help you...',
    'admin.components.page_list_preview_a1_date'  => 'Feb 15, 2026',
    'admin.components.page_list_preview_a2_title' => 'Getting started guide',
    'admin.components.page_list_preview_a2_body'  => 'A step-by-step walkthrough of the most important first steps...',
    'admin.components.page_list_preview_a2_date'  => 'Jan 20, 2026',

    // ── Preview: map ──
    'admin.components.map_preview_title'   => 'How to reach us',
    'admin.components.map_preview_caption' => 'Google Maps map',
    'admin.components.map_preview_note'    => 'Embedded, interactive map with the given address',

    // ── Preview: contact_form ──
    'admin.components.contact_form_preview_title'      => 'Contact us',
    'admin.components.contact_form_preview_name'       => 'Name',
    'admin.components.contact_form_preview_name_ph'    => 'Your name',
    'admin.components.contact_form_preview_email'      => 'E-mail',
    'admin.components.contact_form_preview_email_ph'   => 'example@example.com',
    'admin.components.contact_form_preview_phone'      => 'Phone',
    'admin.components.contact_form_preview_message'    => 'Message',
    'admin.components.contact_form_preview_message_ph' => 'Write your message...',
    'admin.components.contact_form_preview_submit'     => 'Send message',

    // ── Preview: keywords_cloud ──
    'admin.components.keywords_cloud_preview_title' => 'Keywords',
    'admin.components.keywords_cloud_kw1'  => 'services',
    'admin.components.keywords_cloud_kw2'  => 'pricing',
    'admin.components.keywords_cloud_kw3'  => 'support',
    'admin.components.keywords_cloud_kw4'  => 'about',
    'admin.components.keywords_cloud_kw5'  => 'blog',
    'admin.components.keywords_cloud_kw6'  => 'contact',
    'admin.components.keywords_cloud_kw7'  => 'team',
    'admin.components.keywords_cloud_kw8'  => 'careers',
    'admin.components.keywords_cloud_kw9'  => 'features',
    'admin.components.keywords_cloud_kw10' => 'security',
    'admin.components.keywords_cloud_kw11' => 'onboarding',
    'admin.components.keywords_cloud_kw12' => 'guarantee',
    'admin.components.keywords_cloud_kw13' => 'solutions',
    'admin.components.keywords_cloud_kw14' => 'modern',

    // ── Preview: hero_slideshow ──
    'admin.components.hero_slideshow_preview_subtitle' => 'Automatic slideshow from the featured images',
    'admin.components.hero_slideshow_preview_button'   => 'Our services',
    'admin.components.hero_slideshow_preview_nav1'      => 'Services',
    'admin.components.hero_slideshow_preview_nav2'      => 'Solutions',
    'admin.components.hero_slideshow_preview_nav3'      => 'References',

    // ── Preview: product_grid ──
    'admin.components.product_grid_preview_title' => 'Products',
    'admin.components.product_grid_preview_p1'    => 'Starter',
    'admin.components.product_grid_preview_p2'    => 'Professional',
    'admin.components.product_grid_preview_p3'    => 'Enterprise',
    'admin.components.product_grid_preview_p4'    => 'Add-on',
    'admin.components.product_grid_preview_p5'    => 'Custom',
    'admin.components.product_grid_preview_hint'  => 'The description appears on hover',

    // ── Preview: seo_hidden ──
    'admin.components.seo_hidden_preview_button' => 'Read more...',
    'admin.components.seo_hidden_preview_p1'     => 'This text is visible and indexable for Google search bots, but is hidden by default on the website. It only appears when the visitor clicks the button.',
    'admin.components.seo_hidden_preview_p2'     => 'Ideal for placing advertising copy, keywords and local SEO text.',

    // ── Preview: link_banner ──
    'admin.components.link_banner_preview_text' => 'Basic concepts and legend — Glossary of terms',

    // ── Preview: reference_gallery ──
    'admin.components.reference_gallery_preview_title' => 'Our references',
    'admin.components.reference_gallery_preview_r1'    => 'Project A',
    'admin.components.reference_gallery_preview_r2'    => 'Project B',
    'admin.components.reference_gallery_preview_r3'    => 'Project C',
    'admin.components.reference_gallery_preview_r4'    => 'Project D',
    'admin.components.reference_gallery_preview_hint'  => 'The project detail images appear on hover',

    // ── Preview: sitemap ──
    'admin.components.sitemap_preview_title'      => 'Sitemap',
    'admin.components.sitemap_preview_home'       => 'Home',
    'admin.components.sitemap_preview_services'   => 'Our services',
    'admin.components.sitemap_preview_sub1'       => 'Service A',
    'admin.components.sitemap_preview_sub2'       => 'Service B',
    'admin.components.sitemap_preview_sub3'       => 'Service C',
    'admin.components.sitemap_preview_products'   => 'Products',
    'admin.components.sitemap_preview_references' => 'References',
    'admin.components.sitemap_preview_contact'    => 'Contact',
    'admin.components.sitemap_preview_note'       => 'Automatically generated, hierarchical sitemap',

    // ── Preview: tudasmorzsak ──
    'admin.components.tudasmorzsak_preview_title'    => 'Knowledge bites',
    'admin.components.tudasmorzsak_preview_m1_title' => 'Onboarding',
    'admin.components.tudasmorzsak_preview_m1_body'  => 'The process of getting a new customer set up and ready to use the service.',
    'admin.components.tudasmorzsak_preview_m2_title' => 'Dashboard',
    'admin.components.tudasmorzsak_preview_m2_body'  => 'A central screen that displays your most important data at a glance.',
    'admin.components.tudasmorzsak_preview_m3_title' => 'API',
    'admin.components.tudasmorzsak_preview_m3_body'  => 'An interface that lets other systems connect to and exchange data with the service.',
    'admin.components.tudasmorzsak_preview_more'     => 'Read more',
    'admin.components.tudasmorzsak_preview_hint'     => 'Slides in from the right, randomized order',
];
