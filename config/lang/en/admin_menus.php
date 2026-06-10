<?php
// Admin — Menu Management page (admin.menus.*).
return [
    // ── Messages ──
    'admin.menus.msg_added'         => 'Menu item added.',
    'admin.menus.msg_saved'         => 'Saved.',
    'admin.menus.msg_deleted'       => 'Menu item deleted.',
    'admin.menus.msg_done'          => 'Done.',

    // ── Page chrome ──
    'admin.menus.heading'           => 'Menu management',
    'admin.menus.help'              => '<strong>🧭 The navigation menu appears in the website header.</strong> Visitors use it to navigate between pages. Menu items can be top-level (always visible) or sub-items (shown in a dropdown menu).',
    'admin.menus.help_toggle'       => 'How does it work?',
    'admin.menus.help_label'        => '<strong>Label:</strong> The displayed name of the menu item in the header (e.g. "Our services").',
    'admin.menus.help_page'         => '<strong>Select a page:</strong> If you link it to an existing page, the URL is set automatically. This is the recommended method!',
    'admin.menus.help_url'          => '<strong>Custom URL:</strong> If you do not link it to a page, enter a custom URL (e.g. an external link: <code>https://example.com</code>).',
    'admin.menus.help_parent'       => '<strong>Parent menu item:</strong> If you set a parent, the menu item appears in a dropdown sub-menu under that parent.',
    'admin.menus.help_order'        => '<strong>Order:</strong> Use the ▲/▼ buttons to change the order of menu items. The order applies within the same level.',
    'admin.menus.help_delete'       => '<strong>Delete:</strong> If you delete a parent menu item, its sub-items become top-level.',

    // ── Current menu structure table ──
    'admin.menus.structure_heading' => 'Current menu structure',
    'admin.menus.col_label'         => 'Label',
    'admin.menus.col_url'           => 'URL',
    'admin.menus.col_parent'        => 'Parent',
    'admin.menus.col_page'          => 'Page',
    'admin.menus.confirm_delete'    => 'Are you sure you want to delete it?',
    'admin.menus.empty'             => 'No menu items.',

    // ── Edit form ──
    'admin.menus.legend_edit'       => 'Edit menu item: {label}',
    'admin.menus.field_label'       => 'Label',
    'admin.menus.field_url'         => 'Custom URL (if not linked to a page)',
    'admin.menus.url_placeholder'   => '/page-name',
    'admin.menus.field_page'        => 'Select a page (optional)',
    'admin.menus.option_no_page'    => '– None (custom URL) –',
    'admin.menus.field_parent'      => 'Parent menu item (for dropdown)',
    'admin.menus.option_top_level'  => '– Top level –',

    // ── Add form ──
    'admin.menus.legend_add'        => 'Add new menu item',
    'admin.menus.tooltip_help'      => 'Help',
    'admin.menus.tooltip_label'     => 'The text of the menu item shown in the navigation bar. Keep it short and clear (e.g. "Our services", "Contact").',
    'admin.menus.tooltip_url'       => 'Only needs to be filled in if you do not select a page below. Use it for external links (e.g. https://facebook.com) or special paths.',
    'admin.menus.tooltip_page'      => 'If you link the menu item to an existing page, the URL is automatically set to the page slug. This is the recommended method!',
    'admin.menus.tooltip_parent'    => 'If you select a parent, this menu item appears as a sub-item (in a dropdown menu) under the selected parent. "Top level" = a standalone main menu item.',
    'admin.menus.label_placeholder' => 'e.g. Services',
    'admin.menus.url_placeholder_add' => '/page-name or https://...',
];
