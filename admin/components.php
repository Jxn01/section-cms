<?php
// ─── Admin — Component Showcase ───
// Visual preview of all 22 section types available in the page builder.

require_once __DIR__ . '/auth.php';
requireLogin();

$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };

// Section type labels
$sectionLabels = [
    'hero'           => t('admin.components.hero_label'),
    'text'           => t('admin.components.text_label'),
    'image_text'     => t('admin.components.image_text_label'),
    'cards'          => t('admin.components.cards_label'),
    'cta'            => t('admin.components.cta_label'),
    'gallery'        => t('admin.components.gallery_label'),
    'ticker'         => t('admin.components.ticker_label'),
    'accordion'      => t('admin.components.accordion_label'),
    'video'          => t('admin.components.video_label'),
    'divider'        => t('admin.components.divider_label'),
    'two_columns'    => t('admin.components.two_columns_label'),
    'testimonials'   => t('admin.components.testimonials_label'),
    'stats'          => t('admin.components.stats_label'),
    'page_list'      => t('admin.components.page_list_label'),
    'map'            => t('admin.components.map_label'),
    'contact_form'   => t('admin.components.contact_form_label'),
    'keywords_cloud' => t('admin.components.keywords_cloud_label'),
    'hero_slideshow'    => t('admin.components.hero_slideshow_label'),
    'product_grid'      => t('admin.components.product_grid_label'),
    'seo_hidden'        => t('admin.components.seo_hidden_label'),
    'link_banner'       => t('admin.components.link_banner_label'),
    'reference_gallery' => t('admin.components.reference_gallery_label'),
    'sitemap'              => t('admin.components.sitemap_label'),
    'tudasmorzsak'         => t('admin.components.tudasmorzsak_label'),
];

// Section type descriptions
$sectionDescriptions = [
    'hero'           => t('admin.components.hero_desc'),
    'text'           => t('admin.components.text_desc'),
    'image_text'     => t('admin.components.image_text_desc'),
    'cards'          => t('admin.components.cards_desc'),
    'cta'            => t('admin.components.cta_desc'),
    'gallery'        => t('admin.components.gallery_desc'),
    'ticker'         => t('admin.components.ticker_desc'),
    'accordion'      => t('admin.components.accordion_desc'),
    'video'          => t('admin.components.video_desc'),
    'divider'        => t('admin.components.divider_desc'),
    'two_columns'    => t('admin.components.two_columns_desc'),
    'testimonials'   => t('admin.components.testimonials_desc'),
    'stats'          => t('admin.components.stats_desc'),
    'page_list'      => t('admin.components.page_list_desc'),
    'map'            => t('admin.components.map_desc'),
    'contact_form'   => t('admin.components.contact_form_desc'),
    'keywords_cloud' => t('admin.components.keywords_cloud_desc'),
    'hero_slideshow'    => t('admin.components.hero_slideshow_desc'),
    'product_grid'      => t('admin.components.product_grid_desc'),
    'seo_hidden'        => t('admin.components.seo_hidden_desc'),
    'link_banner'       => t('admin.components.link_banner_desc'),
    'reference_gallery' => t('admin.components.reference_gallery_desc'),
    'sitemap'              => t('admin.components.sitemap_desc'),
    'tudasmorzsak'         => t('admin.components.tudasmorzsak_desc'),
];

// Admin field cheat-sheet for each type
$sectionFields = [
    'hero'           => [t('admin.components.hero_field_heading'), t('admin.components.hero_field_subheading'), t('admin.components.hero_field_bg_url'), t('admin.components.hero_field_cta_text'), t('admin.components.hero_field_cta_url')],
    'text'           => [t('admin.components.text_field_heading'), t('admin.components.text_field_content')],
    'image_text'     => [t('admin.components.image_text_field_heading'), t('admin.components.image_text_field_content'), t('admin.components.image_text_field_image_url'), t('admin.components.image_text_field_image_alt'), t('admin.components.image_text_field_image_pos')],
    'cards'          => [t('admin.components.cards_field_heading'), t('admin.components.cards_field_cards')],
    'cta'            => [t('admin.components.cta_field_heading'), t('admin.components.cta_field_subheading'), t('admin.components.cta_field_button_text'), t('admin.components.cta_field_button_url'), t('admin.components.cta_field_bg_color')],
    'gallery'        => [t('admin.components.gallery_field_heading'), t('admin.components.gallery_field_images')],
    'ticker'         => [t('admin.components.ticker_field_items'), t('admin.components.ticker_field_bg_color'), t('admin.components.ticker_field_text_color'), t('admin.components.ticker_field_speed')],
    'accordion'      => [t('admin.components.accordion_field_heading'), t('admin.components.accordion_field_qa')],
    'video'          => [t('admin.components.video_field_heading'), t('admin.components.video_field_url'), t('admin.components.video_field_type')],
    'divider'        => [t('admin.components.divider_field_style'), t('admin.components.divider_field_size')],
    'two_columns'    => [t('admin.components.two_columns_field_heading'), t('admin.components.two_columns_field_left'), t('admin.components.two_columns_field_right')],
    'testimonials'   => [t('admin.components.testimonials_field_heading'), t('admin.components.testimonials_field_items')],
    'stats'          => [t('admin.components.stats_field_heading'), t('admin.components.stats_field_bg_color'), t('admin.components.stats_field_numbers')],
    'page_list'      => [t('admin.components.page_list_field_heading'), t('admin.components.page_list_field_filter'), t('admin.components.page_list_field_count')],
    'map'            => [t('admin.components.map_field_heading'), t('admin.components.map_field_url'), t('admin.components.map_field_height')],
    'contact_form'   => [t('admin.components.contact_form_field_heading'), t('admin.components.contact_form_field_success')],
    'keywords_cloud' => [t('admin.components.keywords_cloud_field_heading'), t('admin.components.keywords_cloud_field_max')],
    'hero_slideshow'    => [t('admin.components.hero_slideshow_field_heading'), t('admin.components.hero_slideshow_field_subheading'), t('admin.components.hero_slideshow_field_cta_text'), t('admin.components.hero_slideshow_field_cta_url'), t('admin.components.hero_slideshow_field_interval'), t('admin.components.hero_slideshow_field_nav')],
    'product_grid'      => [t('admin.components.product_grid_field_heading'), t('admin.components.product_grid_field_columns'), t('admin.components.product_grid_field_products')],
    'seo_hidden'        => [t('admin.components.seo_hidden_field_button'), t('admin.components.seo_hidden_field_content')],
    'link_banner'       => [t('admin.components.link_banner_field_text'), t('admin.components.link_banner_field_icon'), t('admin.components.link_banner_field_url'), t('admin.components.link_banner_field_bg_color')],
    'reference_gallery' => [t('admin.components.reference_gallery_field_heading'), t('admin.components.reference_gallery_field_projects')],
    'sitemap'              => [t('admin.components.sitemap_field_heading')],
    'tudasmorzsak'         => [t('admin.components.tudasmorzsak_field_heading'), t('admin.components.tudasmorzsak_field_items')],
];

require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1>🧩 <?= te('admin.components.page_title') ?></h1>
</div>

<div class="help-box">
    <strong><?= te('admin.components.help_intro') ?></strong>
    <?= te('admin.components.help_body') ?>
    <?= te('admin.components.help_add_before') ?> <a href="/admin/pages.php"><?= te('common.edit') ?></a>
    <?= te('admin.components.help_add_after') ?>
</div>

<!-- Quick navigation -->
<div style="margin-bottom:2rem;">
    <strong><?= te('admin.components.jump_to') ?></strong>
    <div style="display:flex;flex-wrap:wrap;gap:0.4rem;margin-top:0.5rem;">
        <?php foreach ($sectionLabels as $key => $label): ?>
            <a href="#comp-<?= $key ?>" class="btn btn-sm" style="font-size:0.8rem;"><?= $h($label) ?></a>
        <?php endforeach; ?>
    </div>
</div>

<?php foreach ($sectionLabels as $type => $label): ?>
<div class="component-showcase" id="comp-<?= $type ?>">
    <div class="component-header">
        <h2><?= $h($label) ?></h2>
        <span class="component-type-badge"><?= $h($type) ?></span>
    </div>
    <p class="component-desc"><?= $h($sectionDescriptions[$type] ?? '') ?></p>

    <div class="component-preview-wrap" style="color-scheme:light">
        <div class="component-preview" style="color-scheme:light">
            <?php
            // Render a static HTML preview for each section type
            switch ($type):
                case 'hero': ?>
                    <div class="cp-hero">
                        <div class="cp-hero-overlay"></div>
                        <div class="cp-hero-content">
                            <h1><?= te('admin.components.hero_preview_title') ?></h1>
                            <p><?= te('admin.components.hero_preview_subtitle') ?></p>
                            <span class="cp-btn"><?= te('admin.components.hero_preview_button') ?> →</span>
                        </div>
                    </div>
                    <?php break;

                case 'text': ?>
                    <div class="cp-text">
                        <h2><?= te('admin.components.text_preview_title') ?></h2>
                        <p><?= te('admin.components.text_preview_p1') ?></p>
                        <p><?= te('admin.components.text_preview_p2') ?></p>
                    </div>
                    <?php break;

                case 'image_text': ?>
                    <div class="cp-image-text">
                        <div class="cp-image-text-img">
                            <div class="cp-placeholder-img">
                                <span>🖼️</span>
                                <small><?= te('admin.components.label_image') ?></small>
                            </div>
                        </div>
                        <div class="cp-image-text-content">
                            <h2><?= te('admin.components.image_text_preview_title') ?></h2>
                            <p><?= te('admin.components.image_text_preview_body') ?></p>
                        </div>
                    </div>
                    <?php break;

                case 'cards': ?>
                    <div class="cp-cards">
                        <h2><?= te('admin.components.cards_preview_title') ?></h2>
                        <div class="cp-cards-grid">
                            <div class="cp-card">
                                <div class="cp-card-icon">🅿️</div>
                                <h3><?= te('admin.components.cards_preview_c1_title') ?></h3>
                                <p><?= te('admin.components.cards_preview_c1_body') ?></p>
                            </div>
                            <div class="cp-card">
                                <div class="cp-card-icon">🚧</div>
                                <h3><?= te('admin.components.cards_preview_c2_title') ?></h3>
                                <p><?= te('admin.components.cards_preview_c2_body') ?></p>
                            </div>
                            <div class="cp-card">
                                <div class="cp-card-icon">🔑</div>
                                <h3><?= te('admin.components.cards_preview_c3_title') ?></h3>
                                <p><?= te('admin.components.cards_preview_c3_body') ?></p>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'cta': ?>
                    <div class="cp-cta">
                        <h2><?= te('admin.components.cta_preview_title') ?></h2>
                        <p><?= te('admin.components.cta_preview_body') ?></p>
                        <span class="cp-btn-white"><?= te('admin.components.cta_preview_button') ?></span>
                    </div>
                    <?php break;

                case 'gallery': ?>
                    <div class="cp-gallery">
                        <h2><?= te('admin.components.gallery_preview_title') ?></h2>
                        <div class="cp-gallery-grid">
                            <?php for ($gi = 1; $gi <= 6; $gi++): ?>
                                <div class="cp-gallery-item">
                                    <span>🖼️</span>
                                    <small><?= $h(t('admin.components.label_image')) ?> <?= $gi ?></small>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php break;

                case 'ticker': ?>
                    <div class="cp-ticker">
                        <div class="cp-ticker-track">
                            <span>📢 <?= te('admin.components.ticker_preview_1') ?></span>
                            <span>⭐ <?= te('admin.components.ticker_preview_2') ?></span>
                            <span>🏆 <?= te('admin.components.ticker_preview_3') ?></span>
                            <span>📞 <?= te('admin.components.ticker_preview_4') ?></span>
                            <span>📢 <?= te('admin.components.ticker_preview_1') ?></span>
                            <span>⭐ <?= te('admin.components.ticker_preview_2') ?></span>
                        </div>
                    </div>
                    <?php break;

                case 'accordion': ?>
                    <div class="cp-accordion">
                        <h2><?= te('admin.components.accordion_preview_title') ?></h2>
                        <div class="cp-acc-item cp-acc-open">
                            <div class="cp-acc-header">
                                <span><?= te('admin.components.accordion_preview_q1') ?></span>
                                <span class="cp-acc-icon">−</span>
                            </div>
                            <div class="cp-acc-body">
                                <p><?= te('admin.components.accordion_preview_a1') ?></p>
                            </div>
                        </div>
                        <div class="cp-acc-item">
                            <div class="cp-acc-header">
                                <span><?= te('admin.components.accordion_preview_q2') ?></span>
                                <span class="cp-acc-icon">+</span>
                            </div>
                        </div>
                        <div class="cp-acc-item">
                            <div class="cp-acc-header">
                                <span><?= te('admin.components.accordion_preview_q3') ?></span>
                                <span class="cp-acc-icon">+</span>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'video': ?>
                    <div class="cp-video">
                        <h2><?= te('admin.components.video_preview_title') ?></h2>
                        <div class="cp-video-placeholder">
                            <div class="cp-play-btn">▶</div>
                            <p><?= te('admin.components.video_preview_caption') ?></p>
                        </div>
                    </div>
                    <?php break;

                case 'divider': ?>
                    <div class="cp-divider">
                        <p style="text-align:center;color:#94A3B8;font-size:0.85rem;margin-bottom:0.75rem;">↑ <?= te('admin.components.divider_preview_above') ?></p>
                        <div class="cp-divider-examples">
                            <div>
                                <small><?= te('admin.components.divider_style_line') ?></small>
                                <hr style="border:none;border-top:2px solid #D1DAE5;margin:0.5rem 0;">
                            </div>
                            <div>
                                <small><?= te('admin.components.divider_style_dots') ?></small>
                                <div style="text-align:center;color:#6B7280;font-size:1.2rem;letter-spacing:0.5rem;margin:0.5rem 0;">•••</div>
                            </div>
                            <div>
                                <small><?= te('admin.components.divider_style_wave') ?></small>
                                <svg viewBox="0 0 300 15" preserveAspectRatio="none" style="width:100%;height:15px;margin:0.5rem 0;">
                                    <path d="M0,8 Q38,0 75,8 T150,8 T225,8 T300,8 V15 H0 Z" fill="#D1DAE5"/>
                                </svg>
                            </div>
                            <div>
                                <small><?= te('admin.components.divider_style_space') ?></small>
                                <div style="height:2rem;background:repeating-linear-gradient(45deg,transparent,transparent 5px,#F1F5F9 5px,#F1F5F9 10px);border-radius:4px;margin:0.5rem 0;"></div>
                            </div>
                        </div>
                        <p style="text-align:center;color:#94A3B8;font-size:0.85rem;margin-top:0.75rem;">↓ <?= te('admin.components.divider_preview_below') ?></p>
                    </div>
                    <?php break;

                case 'two_columns': ?>
                    <div class="cp-two-columns">
                        <h2><?= te('admin.components.two_columns_preview_title') ?></h2>
                        <div class="cp-two-columns-grid">
                            <div class="cp-column">
                                <h3><?= te('admin.components.two_columns_preview_left_title') ?></h3>
                                <p><?= te('admin.components.two_columns_preview_left_body') ?></p>
                                <ul>
                                    <li><?= te('admin.components.two_columns_preview_item1') ?></li>
                                    <li><?= te('admin.components.two_columns_preview_item2') ?></li>
                                </ul>
                            </div>
                            <div class="cp-column">
                                <h3><?= te('admin.components.two_columns_preview_right_title') ?></h3>
                                <p><?= te('admin.components.two_columns_preview_right_body') ?></p>
                                <ul>
                                    <li><?= te('admin.components.two_columns_preview_item3') ?></li>
                                    <li><?= te('admin.components.two_columns_preview_item4') ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'testimonials': ?>
                    <div class="cp-testimonials">
                        <h2><?= te('admin.components.testimonials_preview_title') ?></h2>
                        <div class="cp-testimonials-grid">
                            <div class="cp-testimonial">
                                <p class="cp-testimonial-text">&ldquo;<?= te('admin.components.testimonials_preview_t1_text') ?>&rdquo;</p>
                                <div class="cp-testimonial-author">
                                    <div class="cp-avatar">A</div>
                                    <div>
                                        <strong><?= te('admin.components.testimonials_preview_t1_name') ?></strong>
                                        <small><?= te('admin.components.testimonials_preview_t1_role') ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="cp-testimonial">
                                <p class="cp-testimonial-text">&ldquo;<?= te('admin.components.testimonials_preview_t2_text') ?>&rdquo;</p>
                                <div class="cp-testimonial-author">
                                    <div class="cp-avatar">B</div>
                                    <div>
                                        <strong><?= te('admin.components.testimonials_preview_t2_name') ?></strong>
                                        <small><?= te('admin.components.testimonials_preview_t2_role') ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'stats': ?>
                    <div class="cp-stats">
                        <div class="cp-stats-row">
                            <div class="cp-stat">
                                <span class="cp-stat-number">500+</span>
                                <span class="cp-stat-label"><?= te('admin.components.stats_preview_label1') ?></span>
                            </div>
                            <div class="cp-stat">
                                <span class="cp-stat-number">15+</span>
                                <span class="cp-stat-label"><?= te('admin.components.stats_preview_label2') ?></span>
                            </div>
                            <div class="cp-stat">
                                <span class="cp-stat-number">1200+</span>
                                <span class="cp-stat-label"><?= te('admin.components.stats_preview_label3') ?></span>
                            </div>
                            <div class="cp-stat">
                                <span class="cp-stat-number">24/7</span>
                                <span class="cp-stat-label"><?= te('admin.components.stats_preview_label4') ?></span>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'page_list': ?>
                    <div class="cp-page-list">
                        <h2><?= te('admin.components.page_list_preview_title') ?></h2>
                        <div class="cp-page-list-grid">
                            <div class="cp-page-card">
                                <div class="cp-page-card-img"><span>🖼️</span></div>
                                <div class="cp-page-card-content">
                                    <h3><?= te('admin.components.page_list_preview_a1_title') ?></h3>
                                    <p><?= te('admin.components.page_list_preview_a1_body') ?></p>
                                    <small><?= te('admin.components.page_list_preview_a1_date') ?></small>
                                </div>
                            </div>
                            <div class="cp-page-card">
                                <div class="cp-page-card-img"><span>🖼️</span></div>
                                <div class="cp-page-card-content">
                                    <h3><?= te('admin.components.page_list_preview_a2_title') ?></h3>
                                    <p><?= te('admin.components.page_list_preview_a2_body') ?></p>
                                    <small><?= te('admin.components.page_list_preview_a2_date') ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'map': ?>
                    <div class="cp-map">
                        <h2><?= te('admin.components.map_preview_title') ?></h2>
                        <div class="cp-map-placeholder">
                            <span>📍</span>
                            <p><?= te('admin.components.map_preview_caption') ?></p>
                            <small><?= te('admin.components.map_preview_note') ?></small>
                        </div>
                    </div>
                    <?php break;

                case 'contact_form': ?>
                    <div class="cp-contact">
                        <h2><?= te('admin.components.contact_form_preview_title') ?></h2>
                        <div class="cp-form-grid">
                            <div class="cp-form-field">
                                <label><?= te('admin.components.contact_form_preview_name') ?> *</label>
                                <div class="cp-input"><?= te('admin.components.contact_form_preview_name_ph') ?></div>
                            </div>
                            <div class="cp-form-field">
                                <label><?= te('admin.components.contact_form_preview_email') ?> *</label>
                                <div class="cp-input"><?= te('admin.components.contact_form_preview_email_ph') ?></div>
                            </div>
                            <div class="cp-form-field">
                                <label><?= te('admin.components.contact_form_preview_phone') ?></label>
                                <div class="cp-input">+36 ...</div>
                            </div>
                            <div class="cp-form-field cp-full">
                                <label><?= te('admin.components.contact_form_preview_message') ?> *</label>
                                <div class="cp-textarea"><?= te('admin.components.contact_form_preview_message_ph') ?></div>
                            </div>
                        </div>
                        <span class="cp-btn"><?= te('admin.components.contact_form_preview_submit') ?></span>
                    </div>
                    <?php break;

                case 'keywords_cloud': ?>
                    <div class="cp-keywords">
                        <h2><?= te('admin.components.keywords_cloud_preview_title') ?></h2>
                        <div class="cp-keywords-cloud">
                            <?php
                            $demoKeywords = [
                                t('admin.components.keywords_cloud_kw1') => 2.2,
                                t('admin.components.keywords_cloud_kw2') => 1.8,
                                t('admin.components.keywords_cloud_kw3') => 1.5,
                                t('admin.components.keywords_cloud_kw4') => 1.2,
                                t('admin.components.keywords_cloud_kw5') => 1.6,
                                t('admin.components.keywords_cloud_kw6') => 1.3,
                                t('admin.components.keywords_cloud_kw7') => 1.0,
                                t('admin.components.keywords_cloud_kw8') => 1.4,
                                t('admin.components.keywords_cloud_kw9') => 0.9,
                                t('admin.components.keywords_cloud_kw10') => 1.1,
                                t('admin.components.keywords_cloud_kw11') => 1.7,
                                t('admin.components.keywords_cloud_kw12') => 0.95,
                                t('admin.components.keywords_cloud_kw13') => 1.05,
                                t('admin.components.keywords_cloud_kw14') => 0.85,
                            ];
                            foreach ($demoKeywords as $kw => $size): ?>
                                <span class="cp-keyword" style="font-size:<?= $size ?>rem;"><?= $h($kw) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php break;

                case 'hero_slideshow': ?>
                    <div class="cp-hero" style="position:relative;min-height:200px;">
                        <div class="cp-hero-overlay"></div>
                        <div class="cp-hero-content" style="display:flex;flex-direction:column;align-items:center;gap:0.5rem;">
                            <div style="font-size:0.65rem;background:rgba(255,255,255,0.2);padding:0.2rem 0.5rem;border-radius:4px;color:#fff;">🖼️ Logo</div>
                            <h1 style="font-size:1.4rem;">Section CMS</h1>
                            <p style="font-size:0.8rem;"><?= te('admin.components.hero_slideshow_preview_subtitle') ?></p>
                            <span class="cp-btn" style="font-size:0.75rem;padding:0.3rem 0.75rem;"><?= te('admin.components.hero_slideshow_preview_button') ?> →</span>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.25rem;margin-top:0.5rem;max-width:200px;width:100%;">
                                <span style="grid-column:span 2;background:rgba(255,255,255,0.15);backdrop-filter:blur(4px);color:#fff;padding:0.3rem;border-radius:4px;font-size:0.6rem;text-align:center;border:1px solid rgba(255,255,255,0.25);"><?= te('admin.components.hero_slideshow_preview_nav1') ?></span>
                                <span style="background:rgba(255,255,255,0.15);backdrop-filter:blur(4px);color:#fff;padding:0.3rem;border-radius:4px;font-size:0.55rem;text-align:center;border:1px solid rgba(255,255,255,0.25);"><?= te('admin.components.hero_slideshow_preview_nav2') ?></span>
                                <span style="background:rgba(255,255,255,0.15);backdrop-filter:blur(4px);color:#fff;padding:0.3rem;border-radius:4px;font-size:0.55rem;text-align:center;border:1px solid rgba(255,255,255,0.25);"><?= te('admin.components.hero_slideshow_preview_nav3') ?></span>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'product_grid': ?>
                    <div style="padding:1.5rem;">
                        <h2 style="text-align:center;margin-bottom:1rem;"><?= te('admin.components.product_grid_preview_title') ?></h2>
                        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:0.5rem;">
                            <?php
                            $products = [
                                t('admin.components.product_grid_preview_p1'),
                                t('admin.components.product_grid_preview_p2'),
                                t('admin.components.product_grid_preview_p3'),
                                t('admin.components.product_grid_preview_p4'),
                                t('admin.components.product_grid_preview_p5'),
                            ];
                            foreach ($products as $p): ?>
                                <div style="aspect-ratio:1;background:#f1f5f9;border-radius:6px;display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:0.7rem;position:relative;overflow:hidden;cursor:pointer;" class="cp-pg-demo">
                                    <span style="font-size:1.5rem;">📦</span>
                                    <span style="font-size:0.65rem;font-weight:600;margin-top:0.25rem;"><?= $h($p) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p style="text-align:center;font-size:0.7rem;color:#64748b;margin-top:0.75rem;">← <?= te('admin.components.product_grid_preview_hint') ?> →</p>
                    </div>
                    <?php break;

                case 'seo_hidden': ?>
                    <div style="padding:1.5rem;">
                        <details style="max-width:600px;margin:0 auto;">
                            <summary style="cursor:pointer;padding:0.6rem 1rem;background:#E8F4FF;border-radius:6px;font-weight:600;color:#0067FF;list-style:none;display:flex;align-items:center;gap:0.4rem;">
                                <span style="transition:transform 0.2s;font-size:0.7rem;">▸</span> <?= te('admin.components.seo_hidden_preview_button') ?>
                            </summary>
                            <div style="padding:1rem;font-size:0.85rem;line-height:1.8;color:#475569;">
                                <p><?= te('admin.components.seo_hidden_preview_p1') ?></p>
                                <p><?= te('admin.components.seo_hidden_preview_p2') ?></p>
                            </div>
                        </details>
                    </div>
                    <?php break;

                case 'link_banner': ?>
                    <div style="background:#0067FF;padding:1rem 2rem;">
                        <div style="display:flex;align-items:center;justify-content:center;gap:0.75rem;color:#fff;">
                            <span style="font-size:1.25rem;">📖</span>
                            <span style="font-weight:600;font-size:0.95rem;"><?= te('admin.components.link_banner_preview_text') ?></span>
                            <span style="font-size:1.1rem;">→</span>
                        </div>
                    </div>
                    <?php break;

                case 'reference_gallery': ?>
                    <div style="padding:1.5rem;">
                        <h2 style="text-align:center;margin-bottom:1rem;"><?= te('admin.components.reference_gallery_preview_title') ?></h2>
                        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.75rem;">
                            <?php
                            $refs = [
                                t('admin.components.reference_gallery_preview_r1'),
                                t('admin.components.reference_gallery_preview_r2'),
                                t('admin.components.reference_gallery_preview_r3'),
                                t('admin.components.reference_gallery_preview_r4'),
                            ];
                            foreach ($refs as $r): ?>
                                <div style="aspect-ratio:4/3;background:linear-gradient(135deg,#e2e8f0,#cbd5e1);border-radius:6px;position:relative;overflow:hidden;">
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:1.5rem;">🏢</div>
                                    <div style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(transparent,rgba(15,23,42,0.8));color:#fff;padding:1.5rem 0.5rem 0.4rem;font-size:0.7rem;font-weight:600;"><?= $h($r) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p style="text-align:center;font-size:0.7rem;color:#64748b;margin-top:0.75rem;">← <?= te('admin.components.reference_gallery_preview_hint') ?> →</p>
                    </div>
                    <?php break;

                case 'sitemap': ?>
                    <div style="padding:1.5rem;">
                        <h2 style="text-align:center;margin-bottom:1rem;"><?= te('admin.components.sitemap_preview_title') ?></h2>
                        <div style="max-width:400px;margin:0 auto;">
                            <ul style="list-style:none;padding:0;font-size:0.85rem;line-height:2;">
                                <li><strong style="color:#0067FF;"><?= te('admin.components.sitemap_preview_home') ?></strong></li>
                                <li><strong style="color:#0067FF;"><?= te('admin.components.sitemap_preview_services') ?></strong>
                                    <ul style="list-style:none;padding-left:1.25rem;border-left:2px solid #e2e8f0;margin-left:0.4rem;">
                                        <li style="color:#364151;"><?= te('admin.components.sitemap_preview_sub1') ?></li>
                                        <li style="color:#364151;"><?= te('admin.components.sitemap_preview_sub2') ?></li>
                                        <li style="color:#364151;"><?= te('admin.components.sitemap_preview_sub3') ?></li>
                                    </ul>
                                </li>
                                <li><strong style="color:#0067FF;"><?= te('admin.components.sitemap_preview_products') ?></strong></li>
                                <li><strong style="color:#0067FF;"><?= te('admin.components.sitemap_preview_references') ?></strong></li>
                                <li><strong style="color:#0067FF;"><?= te('admin.components.sitemap_preview_contact') ?></strong></li>
                            </ul>
                        </div>
                        <p style="text-align:center;font-size:0.7rem;color:#64748b;margin-top:0.75rem;"><?= te('admin.components.sitemap_preview_note') ?></p>
                    </div>
                    <?php break;

                case 'tudasmorzsak': ?>
                    <div style="padding:1.5rem;background:#E7F6FF;">
                        <h2 style="text-align:center;margin-bottom:1rem;color:#0F172A;"><?= te('admin.components.tudasmorzsak_preview_title') ?></h2>
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.75rem;">
                            <?php
                            $morzsak = [
                                [t('admin.components.tudasmorzsak_preview_m1_title'), t('admin.components.tudasmorzsak_preview_m1_body')],
                                [t('admin.components.tudasmorzsak_preview_m2_title'), t('admin.components.tudasmorzsak_preview_m2_body')],
                                [t('admin.components.tudasmorzsak_preview_m3_title'), t('admin.components.tudasmorzsak_preview_m3_body')],
                            ];
                            foreach ($morzsak as $m): ?>
                                <div style="background:#fff;border-radius:6px;padding:0.75rem;border:1px solid #D1DAE5;">
                                    <h3 style="font-size:0.85rem;font-weight:700;color:#0F172A;margin-bottom:0.25rem;"><?= $h($m[0]) ?></h3>
                                    <p style="font-size:0.75rem;color:#364151;line-height:1.4;margin:0;"><?= $h($m[1]) ?></p>
                                    <span style="font-size:0.7rem;color:#0067FF;font-weight:600;margin-top:0.35rem;display:inline-block;"><?= te('admin.components.tudasmorzsak_preview_more') ?> →</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p style="text-align:center;font-size:0.7rem;color:#64748b;margin-top:0.75rem;">← <?= te('admin.components.tudasmorzsak_preview_hint') ?> →</p>
                    </div>
                    <?php break;

            endswitch; ?>
        </div>
    </div>

    <div class="component-fields">
        <strong><?= te('admin.components.editable_fields') ?></strong>
        <ul>
            <?php foreach (($sectionFields[$type] ?? []) as $field): ?>
                <li><?= $h($field) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endforeach; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
