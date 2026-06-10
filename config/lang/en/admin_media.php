<?php
// Admin — Media Manager page (admin.media.*).
return [
    // ── Upload error / messages ──
    'admin.media.err_upload'        => 'Upload error. Please try again.',
    'admin.media.err_too_large'     => 'The file is too large. Maximum size: 10 MB.',
    'admin.media.err_type'          => 'File type not allowed. Only JPEG, PNG, GIF and WebP are allowed.',
    'admin.media.msg_uploaded'      => 'Image uploaded successfully.',
    'admin.media.msg_deleted'       => 'Image deleted.',
    'admin.media.msg_saved'         => 'Saved.',
    'admin.media.msg_done'          => 'Done.',

    // ── Browse / picker mode ──
    'admin.media.browser_title'     => 'Media browser',
    'admin.media.pick_heading'      => 'Select an image',
    'admin.media.upload_new'        => 'Upload a new image',
    'admin.media.alt_text'          => 'Alt text',
    'admin.media.alt_placeholder'   => 'Image description',
    'admin.media.empty_browse'      => 'No media uploaded. Upload images using the form above.',

    // ── Page chrome ──
    'admin.media.heading'           => 'Media',
    'admin.media.help'              => '<strong>🖼️ Manage images for the website.</strong> Upload images that you can use in sections (hero background, gallery, image+text, etc.). Images can be selected in the editor with the "Browse" button.',
    'admin.media.help_toggle'       => 'Important notes',
    'admin.media.help_formats'      => '<strong>Allowed formats:</strong> JPEG, PNG, GIF, WebP. Maximum size: 10 MB.',
    'admin.media.help_alt'          => '<strong>Alt text (very important!):</strong> Briefly describe what the image shows. This matters for two things:',
    'admin.media.help_alt_seo'      => '<em>SEO:</em> Google ranks images in image search based on the alt text.',
    'admin.media.help_alt_a11y'     => '<em>Accessibility:</em> Visitors using a screen reader hear this instead of the image.',
    'admin.media.help_size'         => '<strong>Image size tip:</strong> At least 1920×1080 px is recommended for hero background images. 800×600 px is enough for gallery images.',
    'admin.media.help_filename'     => '<strong>Filename:</strong> The system automatically generates a safe filename, you do not need to worry about it.',

    // ── Upload form ──
    'admin.media.legend_upload'     => 'Upload a new image',
    'admin.media.field_file'        => 'Image file',
    'admin.media.field_alt_seo'     => 'Alt text (SEO)',
    'admin.media.recommended'       => '*recommended',
    'admin.media.alt_placeholder_seo' => 'Image description for search engines',
    'admin.media.alt_warning'       => '⚠ Alt text is important for Google image search and accessibility.',
    'admin.media.featured_label'    => '⭐ Featured image (appears in the homepage slideshow)',

    // ── Media grid ──
    'admin.media.alt_placeholder_short' => 'Alt text',
    'admin.media.featured_short'    => '⭐ Featured',
    'admin.media.confirm_delete'    => 'Are you sure you want to delete it?',
    'admin.media.empty'             => 'No media uploaded yet.',
];
