<?php
// Admin panel chrome: navigation, login, dashboard, shared admin labels.
return [
    'admin.csrf_invalid' => 'Invalid security token. Please refresh the page and try again.',
    'admin.admin'        => 'Administration',
    'admin.menu_aria'    => 'Menu',
    'admin.help_aria'    => 'Help',
    'admin.language'     => 'Language',
    'admin.logout'       => 'Log out',

    // Sidebar / top navigation
    'admin.nav.dashboard'  => 'Dashboard',
    'admin.nav.pages'      => 'Pages',
    'admin.nav.menus'      => 'Menu',
    'admin.nav.media'      => 'Media',
    'admin.nav.messages'   => 'Messages',
    'admin.nav.settings'   => 'Settings',
    'admin.nav.password'   => 'Password',
    'admin.nav.components' => 'Components',
    'admin.nav.view_site'  => 'View site',

    // Login
    'admin.login.title'    => 'Sign in – Admin',
    'admin.login.subtitle' => 'Administration',
    'admin.login.username' => 'Username',
    'admin.login.password' => 'Password',
    'admin.login.submit'   => 'Sign in',
    'admin.login.error'    => 'Incorrect username or password.',

    // Dashboard — welcome + heading
    'admin.dash.welcome_title' => '👋 Welcome to the dashboard!',
    'admin.dash.welcome_text'  => 'From here you can manage all of your website\'s content: pages, menu, images and settings. The summary below shows the current state of your site.',
    'admin.dash.title'         => 'Dashboard',

    // Dashboard — stat cards
    'admin.dash.stat_pages'        => 'Pages',
    'admin.dash.stat_pages_tip'    => 'The total number of pages created (both published and draft). Pages make up the main content of the website.',
    'admin.dash.stat_articles'     => 'Articles',
    'admin.dash.stat_articles_tip' => 'The number of "article" type pages. Articles get extra SEO markup (author, date) — ideal for blog posts or news.',
    'admin.dash.stat_drafts'       => 'Drafts',
    'admin.dash.stat_drafts_tip'   => 'Pages that have not been published yet. Draft pages do not appear on the website and are not visible to Google.',
    'admin.dash.stat_sections'     => 'Sections',
    'admin.dash.stat_sections_tip' => 'Sections are the building blocks of a page (e.g. hero image, text, cards, gallery, etc.). Every page is made up of any number of sections.',
    'admin.dash.stat_media'        => 'Media',
    'admin.dash.stat_media_tip'    => 'The number of uploaded images. Images can be used in sections (hero background, gallery, image + text, etc.).',
    'admin.dash.stat_messages'     => 'New messages',
    'admin.dash.stat_messages_tip' => 'Unread messages received through the contact form on the website. Remember to check them regularly!',

    // Dashboard — tip cards
    'admin.dash.tip_pages_title'  => 'Pages and sections',
    'admin.dash.tip_pages_text'   => 'Every page contains sections (hero, text, gallery, etc.). The order of the sections defines the page layout — move them up/down into the order you want.',
    'admin.dash.tip_seo_title'    => 'SEO (search engine optimization)',
    'admin.dash.tip_seo_text'     => 'Every page has a meta title and description. These appear in Google search results. Make sure to fill them in with unique, relevant text!',
    'admin.dash.tip_images_title' => 'Images and alt text',
    'admin.dash.tip_images_text'  => 'Upload images in the Media menu. Give every image alt text — this matters for Google image search and for visually impaired users.',
    'admin.dash.tip_nav_title'    => 'Navigation',
    'admin.dash.tip_nav_text'     => 'In Menu management you decide which links appear in the website header. You can create dropdown submenus by setting a parent menu item.',

    // Dashboard — quick links
    'admin.dash.quick_title'    => 'Quick links',
    'admin.dash.ql_pages'       => 'Manage pages',
    'admin.dash.ql_pages_desc'  => '— create, edit and delete pages',
    'admin.dash.ql_new'         => 'Create a new page',
    'admin.dash.ql_new_desc'    => '— pick a template and start editing',
    'admin.dash.ql_menus'       => 'Manage menu',
    'admin.dash.ql_menus_desc'  => '— edit navigation, order, submenus',
    'admin.dash.ql_media'       => 'Manage media',
    'admin.dash.ql_media_desc'  => '— image upload, alt text, gallery images',
    'admin.dash.ql_messages'    => 'Messages',
    'admin.dash.ql_messages_desc' => '— contact form messages',
    'admin.dash.ql_settings'    => 'Settings',
    'admin.dash.ql_settings_desc' => '— site name, contact details, colors',
    'admin.dash.ql_password'    => 'Change password',
    'admin.dash.ql_password_desc' => '— admin account security',
    'admin.dash.ql_view'        => 'View website ↗',
    'admin.dash.ql_view_desc'   => '— preview the public site',
    'admin.dash.new_badge'      => 'new',
];
