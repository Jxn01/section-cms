<?php
// Admin — Site Settings page (admin.settings.*) and API endpoints (admin.api.*).
return [
    // ── Page chrome ──
    'admin.settings.heading'        => 'Settings',
    'admin.settings.help'           => '<strong>⚙️ Global website settings.</strong> These values affect the entire website: header, footer, Google search results, social media sharing and branding. Changes take effect immediately after saving.',
    'admin.settings.msg_saved'      => 'Settings saved.',
    'admin.settings.legend_general' => 'General',
    'admin.settings.legend_smtp'    => '📧 Email notifications (SMTP)',
    'admin.settings.smtp_help'      => 'Messages received through the form are automatically saved to the database. If you also want to receive email notifications, fill in the SMTP settings below. With your hosting provider, first create an email account for your domain.',
    'admin.settings.tooltip_help'   => 'Help',
    'admin.settings.smtp_test_btn'  => '📧 Send test email',
    'admin.settings.smtp_sending'   => 'Sending...',
    'admin.settings.smtp_network_error' => 'Network error',

    // ── General fields: labels ──
    'admin.settings.field.site_name.label'         => 'Website name',
    'admin.settings.field.site_tagline.label'      => 'Tagline',
    'admin.settings.field.meta_description.label'  => 'Default meta description',
    'admin.settings.field.meta_keywords.label'     => 'Default keywords',
    'admin.settings.field.contact_email.label'     => 'Email address',
    'admin.settings.field.contact_phone.label'     => 'Phone number',
    'admin.settings.field.contact_address.label'   => 'Address',
    'admin.settings.field.primary_color.label'     => 'Primary color',
    'admin.settings.field.secondary_color.label'   => 'Secondary color',
    'admin.settings.field.facebook_url.label'      => 'Facebook URL',
    'admin.settings.field.og_default_image.label'  => 'Default OG image',
    'admin.settings.field.logo_url.label'          => 'Logo URL',
    'admin.settings.field.logo_display_mode.label' => 'Logo display mode',

    // ── General fields: hints ──
    'admin.settings.field.site_name.hint'         => 'Shown in the header, footer, browser tab and Google results. This is the website\'s "brand name".',
    'admin.settings.field.site_tagline.hint'      => 'A short motto that can appear next to the website name. E.g. "Reliable solutions for your business".',
    'admin.settings.field.meta_description.hint'  => 'This description appears in Google results when a page has no meta description of its own. Max. 160 characters recommended.',
    'admin.settings.field.meta_keywords.hint'     => 'Comma-separated keywords used as the default keywords for pages that do not specify their own.',
    'admin.settings.field.contact_email.hint'     => 'The contact email shown on the website. The contact form sends notifications here.',
    'admin.settings.field.contact_phone.hint'     => 'The phone number shown in the header and footer. Format: +36 1 234 5678.',
    'admin.settings.field.contact_address.hint'   => 'The company\'s physical address. Shown in the footer and in Google structured data.',
    'admin.settings.field.primary_color.hint'     => 'The website\'s main color: buttons, links, highlights. Defines the website\'s branding.',
    'admin.settings.field.secondary_color.hint'   => 'A complementary color for background elements and secondary highlights.',
    'admin.settings.field.facebook_url.hint'      => 'The full URL of the company\'s Facebook page. Shown in the footer and in JSON-LD structured data.',
    'admin.settings.field.og_default_image.hint'  => 'This image is shown when someone shares the page on Facebook or other social media and no dedicated featured image is set.',
    'admin.settings.field.logo_url.hint'          => 'The URL of the company logo. Shown in the header next to or instead of the website name (the display mode is set below). Upload a logo image on the Media page, then select it with the "Browse" button.',
    'admin.settings.field.logo_display_mode.hint' => 'Define how the logo appears in the header. "None" = only the website name is shown. "Logo replaces the name" = only the logo image is shown. "Logo beside the name" = the logo image and the website name side by side.',

    // ── logo_display_mode options ──
    'admin.settings.field.logo_display_mode.option.none'    => 'None (website name only)',
    'admin.settings.field.logo_display_mode.option.replace' => 'Logo replaces the name',
    'admin.settings.field.logo_display_mode.option.beside'  => 'Logo beside the name',

    // ── SMTP fields: labels ──
    'admin.settings.field.smtp_host.label' => 'SMTP server',
    'admin.settings.field.smtp_port.label' => 'SMTP port',
    'admin.settings.field.smtp_user.label' => 'SMTP username',
    'admin.settings.field.smtp_pass.label' => 'SMTP password',
    'admin.settings.field.smtp_from.label' => 'Sender email address',
    'admin.settings.field.smtp_to.label'   => 'Notification email',

    // ── SMTP fields: hints ──
    'admin.settings.field.smtp_host.hint' => 'The SMTP server address, e.g. smtp.example.com. You get this from your hosting provider.',
    'admin.settings.field.smtp_port.hint' => 'The SMTP port number. Usually 587 (STARTTLS) or 465 (SSL). Default: 587.',
    'admin.settings.field.smtp_user.hint' => 'The username of the email account (usually the email address itself), e.g. info@example.com.',
    'admin.settings.field.smtp_pass.hint' => 'The password of the email account. Stored securely in the database.',
    'admin.settings.field.smtp_from.hint' => 'The value of the "From" field in sent emails. If empty, the SMTP username is used.',
    'admin.settings.field.smtp_to.hint'   => 'Form notifications are delivered to this email address. If empty, the SMTP username is used.',

    // ── API endpoints (admin.api.*) ──
    'admin.api.invalid_csrf'      => 'Invalid CSRF token',
    'admin.api.missing_smtp'      => 'Missing SMTP settings. Save all fields first.',
    'admin.api.test_subject'      => 'Test email — Section CMS',
    'admin.api.test_body'         => '<h2>✅ This is a test email</h2><p>If you received this message, your SMTP settings are correct.</p><p><small>Sent: {datetime}</small></p>',
    'admin.api.test_altbody'      => "Test email\nIf you received this message, your SMTP settings are correct.\nSent: {datetime}",
    'admin.api.test_sent'         => 'Test email sent ({to})',
    'admin.api.send_error'        => 'Send error: {error}',
];
