<?php
// ═══════════════════════════════════════════════════════════════
// SEO (Search Engine Optimization)
// ═══════════════════════════════════════════════════════════════
// Builds the per-page meta tags, Open Graph, Twitter Card and
// JSON-LD structured data.
// ═══════════════════════════════════════════════════════════════

class SEO {

    /**
     * Build the SEO data for a single page.
     * Returns an associative array: title, description, keywords,
     * canonical, og:*, twitter:*, json-ld, etc.
     */
    public static function buildMeta(array $page, array $settings): array {
        $siteName = $settings['site_name'] ?? 'Section CMS';
        $baseUrl  = SITE_BASE_URL;
        $slug     = $page['slug'] === 'home' ? '' : $page['slug'];
        $canonical = $baseUrl . '/' . $slug;

        return [
            'title'            => $page['meta_title']       ?: $page['title'] . ' – ' . $siteName,
            'meta_description' => $page['meta_description'] ?: ($settings['meta_description'] ?? ''),
            'meta_keywords'    => $page['meta_keywords']    ?: ($settings['meta_keywords'] ?? ''),
            'canonical'        => $canonical,
            'og_title'         => $page['og_title']         ?: $page['meta_title'] ?: $page['title'],
            'og_description'   => $page['og_description']   ?: $page['meta_description'] ?: '',
            'og_image'         => $page['og_image']         ?: ($page['featured_image'] ?? '') ?: ($settings['og_default_image'] ?? ''),
            'og_url'           => $canonical,
            'og_type'          => ($page['page_type'] ?? '') === 'article' ? 'article' : 'website',
            'og_site_name'     => $siteName,
            'author'           => ($page['page_type'] ?? '') === 'article' ? $siteName : '',
        ];
    }

    /**
     * Render all <meta> tags for the <head>.
     */
    public static function renderMetaTags(array $seo): string {
        $h = function ($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); };

        $html  = '<title>' . $h($seo['title']) . "</title>\n";
        $html .= '    <meta name="description" content="' . $h($seo['meta_description']) . "\">\n";
        if (!empty($seo['meta_keywords'])) {
            $html .= '    <meta name="keywords" content="' . $h($seo['meta_keywords']) . "\">\n";
        }
        if (!empty($seo['author'])) {
            $html .= '    <meta name="author" content="' . $h($seo['author']) . "\">\n";
        }
        // Googlebot — allow maximum preview sizes in search results
        if (strpos($seo['robots'] ?? '', 'noindex') === false) {
            $html .= '    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">' . "\n";
        }
        $html .= '    <link rel="canonical" href="' . $h($seo['canonical']) . "\">\n";

        // Open Graph
        $html .= '    <meta property="og:title" content="' . $h($seo['og_title']) . "\">\n";
        $html .= '    <meta property="og:description" content="' . $h($seo['og_description']) . "\">\n";
        $html .= '    <meta property="og:type" content="' . $h($seo['og_type']) . "\">\n";
        $html .= '    <meta property="og:url" content="' . $h($seo['og_url']) . "\">\n";
        $html .= '    <meta property="og:site_name" content="' . $h($seo['og_site_name']) . "\">\n";
        if (!empty($seo['og_image'])) {
            $ogImg = $seo['og_image'];
            if (strpos($ogImg, 'http') !== 0) {
                $ogImg = SITE_BASE_URL . $ogImg;
            }
            $html .= '    <meta property="og:image" content="' . $h($ogImg) . "\">\n";
        }

        // Twitter Card
        $html .= '    <meta name="twitter:card" content="summary_large_image">' . "\n";
        $html .= '    <meta name="twitter:title" content="' . $h($seo['og_title']) . "\">\n";
        $html .= '    <meta name="twitter:description" content="' . $h($seo['og_description']) . "\">\n";
        if (!empty($seo['og_image'])) {
            $twImg = $seo['og_image'];
            if (strpos($twImg, 'http') !== 0) {
                $twImg = SITE_BASE_URL . $twImg;
            }
            $html .= '    <meta name="twitter:image" content="' . $h($twImg) . "\">\n";
        }
        // Locale
        $html .= '    <meta property="og:locale" content="' . $h(I18n::ogLocale()) . "\">\n";
        return $html;
    }

    /**
     * Build JSON-LD structured data for the page.
     */
    public static function renderJsonLd(array $page, array $settings): string {
        $siteName  = $settings['site_name'] ?? 'Section CMS';
        $baseUrl   = SITE_BASE_URL;

        $graph = [];

        // Organization
        $graph[] = [
            '@type' => 'Organization',
            '@id'   => $baseUrl . '/#organization',
            'name'  => $siteName,
            'url'   => $baseUrl,
        ];
        if (!empty($settings['contact_email'])) {
            $graph[0]['email'] = $settings['contact_email'];
        }
        if (!empty($settings['contact_phone'])) {
            $graph[0]['telephone'] = $settings['contact_phone'];
        }
        if (!empty($settings['contact_address'])) {
            $graph[0]['address'] = [
                '@type'          => 'PostalAddress',
                'streetAddress'  => $settings['contact_address'],
            ];
        }

        // WebSite
        $graph[] = [
            '@type'     => 'WebSite',
            '@id'       => $baseUrl . '/#website',
            'name'      => $siteName,
            'url'       => $baseUrl,
            'publisher' => ['@id' => $baseUrl . '/#organization'],
        ];

        // WebPage (or Article for article-type pages)
        $slug      = $page['slug'] === 'home' ? '' : $page['slug'];
        $canonical = $baseUrl . '/' . $slug;
        $isArticle = ($page['page_type'] ?? '') === 'article';

        $webPage = [
            '@type'        => $isArticle ? 'Article' : 'WebPage',
            '@id'          => $canonical . '#webpage',
            'name'         => $page['meta_title'] ?: $page['title'],
            'url'          => $canonical,
            'isPartOf'     => ['@id' => $baseUrl . '/#website'],
            'dateModified' => !empty($page['updated_at']) ? date('c', strtotime($page['updated_at'])) : '',
            'description'  => $page['meta_description'] ?? '',
        ];

        // Article-specific structured data
        if ($isArticle) {
            $webPage['author'] = ['@id' => $baseUrl . '/#organization'];
            $webPage['publisher'] = ['@id' => $baseUrl . '/#organization'];
            if (!empty($page['featured_image'])) {
                $featImg = $page['featured_image'];
                if (strpos($featImg, 'http') !== 0) { $featImg = $baseUrl . $featImg; }
                $webPage['image'] = $featImg;
            }
        }

        // BreadcrumbList (helps Google show breadcrumbs in SERPs)
        if ($page['slug'] !== 'home' && $page['slug'] !== '404') {
            $graph[] = [
                '@type' => 'BreadcrumbList',
                '@id'   => $canonical . '#breadcrumb',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => t('site.home'),
                        'item' => $baseUrl . '/',
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $page['title'] ?? '',
                        'item' => $canonical,
                    ],
                ],
            ];
            $webPage['breadcrumb'] = ['@id' => $canonical . '#breadcrumb'];
        }

        $graph[] = $webPage;

        $ld = [
            '@context' => 'https://schema.org',
            '@graph'   => $graph,
        ];

        return '<script type="application/ld+json">'
            . json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            . '</script>';
    }
}
