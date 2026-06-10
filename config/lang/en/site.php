<?php
// Public website chrome (header, footer, 404, search, contact form).
// Page/section content itself is authored in the admin panel.
return [
    // Header / navigation
    'site.skip_to_content' => 'Skip to content',
    'site.home'            => 'Home',
    'site.home_aria'       => '{site} – Home',
    'site.open_menu'       => 'Open menu',
    'site.main_nav'        => 'Main navigation',

    // Footer
    'site.footer_nav'        => 'Footer navigation',
    'site.sitemap'           => 'Sitemap',
    'site.phone_label'       => 'Phone',
    'site.email_label'       => 'Email',
    'site.rights_reserved'   => 'All rights reserved.',

    // 404 page
    'site.404_text' => 'The page you are looking for could not be found.',
    'site.404_back' => 'Back to home',

    // Keyword results
    'site.keyword_breadcrumb' => 'Keyword: {keyword}',
    'site.keyword_heading'    => 'Keyword: {keyword}',
    'site.keyword_title'      => '{keyword} – Keyword – {site}',
    'site.keyword_meta'       => 'Pages tagged with the keyword: {keyword}',
    'site.keyword_no_results' => 'No pages match this keyword.',

    // Generic empty state
    'site.no_content' => 'No content to display.',

    // Contact form
    'site.contact.name'        => 'Name',
    'site.contact.name_ph'     => 'Your name',
    'site.contact.email'       => 'Email',
    'site.contact.email_ph'    => 'you@example.com',
    'site.contact.phone'       => 'Phone',
    'site.contact.phone_ph'    => '+1 ...',
    'site.contact.message'     => 'Message',
    'site.contact.message_ph'  => 'Write your message...',
    'site.contact.submit'      => 'Send message',
    'site.contact.success'     => 'Thank you for your message! We will get back to you soon.',
    'site.contact.error'       => 'Something went wrong. Please try again.',

    // Contact notification email (sent to the site owner)
    'site.contact.email_subject' => 'New message: {preview}…',
    'site.contact.email_heading' => 'New contact form message',
    'site.contact.email_name'    => 'Name',
    'site.contact.email_email'   => 'Email',
    'site.contact.email_phone'   => 'Phone',
    'site.contact.email_page'    => 'Page',

    // Section fallbacks / controls
    'site.read_more'      => 'Read more',
    'site.read_more_long' => 'Read more...',
    'site.ticker_pause'   => 'Pause ticker',
    'site.ticker_play'    => 'Resume ticker',
    'site.more_pages'     => 'More pages',
    'site.map'            => 'Map',
    'site.hero'           => 'Hero image',
    'site.image'          => 'Image',
    'site.keywords'       => 'Keywords',
    'site.kw_pages_title' => '{count} pages',
    'site.kw_pages_aria'  => '{keyword} — {count} related pages',
];
