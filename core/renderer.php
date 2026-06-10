<?php
// ═══════════════════════════════════════════════════════════════
// Renderer
// ═══════════════════════════════════════════════════════════════
// Fetches sections, settings and menus from the database and
// renders the section templates to HTML.
// ═══════════════════════════════════════════════════════════════

class Renderer {

    /**
     * Fetch all sections for a page, ordered by sort_order.
     */
    public static function getSections(PDO $pdo, int $pageId): array {
        $stmt = $pdo->prepare(
            "SELECT * FROM sections WHERE page_id = :pid ORDER BY sort_order ASC"
        );
        $stmt->execute(['pid' => $pageId]);
        return $stmt->fetchAll();
    }

    /**
     * Fetch all site_settings as key => value.
     */
    public static function getSettings(PDO $pdo): array {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
        $settings = [];
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    /**
     * Fetch navigation menu items as a tree (top-level + children).
     */
    public static function getMenus(PDO $pdo): array {
        $stmt = $pdo->query(
            "SELECT m.*, p.slug as page_slug
             FROM menus m
             LEFT JOIN pages p ON m.page_id = p.id
             ORDER BY m.sort_order ASC"
        );
        $all = $stmt->fetchAll();

        // Build tree: top-level items with children arrays
        $byId = [];
        foreach ($all as &$item) {
            $item['children'] = [];
            // Always use live page slug when linked to a page (prevents stale URLs)
            if ($item['page_slug']) {
                $item['url'] = $item['page_slug'] === 'home' ? '/' : '/' . $item['page_slug'];
            }
            $byId[$item['id']] = &$item;
        }
        unset($item);

        $tree = [];
        foreach ($all as &$item) {
            if ($item['parent_id'] && isset($byId[$item['parent_id']])) {
                $byId[$item['parent_id']]['children'][] = &$item;
            } else {
                $tree[] = &$item;
            }
        }
        unset($item);

        return $tree;
    }

    /**
     * Render a single section by including its template file.
     * The template receives $content (decoded JSON), $section (full row), and $pdo.
     */
    public static function renderSection(array $section, ?PDO $pdo = null): string {
        $type        = $section['type'];
        $content     = json_decode($section['content'], true);

        // Validate section type: only allow alphanumeric + underscore (no path traversal)
        if (!preg_match('/^[a-z0-9_]+$/', $type)) {
            return '<!-- invalid section type -->';
        }

        $templateFile = dirname(__DIR__) . '/templates/sections/' . $type . '.php';

        if (!file_exists($templateFile)) {
            return '<!-- unknown section type -->';
        }

        ob_start();
        require $templateFile;
        return ob_get_clean();
    }

    /**
     * Render all sections for a page and return the combined HTML.
     */
    public static function renderAllSections(array $sections, ?PDO $pdo = null): string {
        $html = '';
        foreach ($sections as $section) {
            $html .= self::renderSection($section, $pdo);
        }
        return $html;
    }

    /**
     * Get keyword cloud data from all published pages.
     * Returns array of [keyword => count] sorted by frequency.
     */
    public static function getKeywordCloud(PDO $pdo, int $limit = 50): array {
        $stmt = $pdo->query("SELECT meta_keywords FROM pages WHERE status = 'published' AND meta_keywords != '' AND meta_keywords IS NOT NULL");
        $keywords = [];
        foreach ($stmt->fetchAll() as $row) {
            $kws = array_map('trim', explode(',', $row['meta_keywords']));
            foreach ($kws as $kw) {
                $kw = mb_strtolower(trim($kw));
                if ($kw !== '') {
                    $keywords[$kw] = ($keywords[$kw] ?? 0) + 1;
                }
            }
        }
        arsort($keywords);
        return array_slice($keywords, 0, $limit, true);
    }

    /**
     * Find pages matching a keyword in their meta_keywords.
     */
    public static function getPagesByKeyword(PDO $pdo, string $keyword): array {
        $stmt = $pdo->prepare(
            "SELECT slug, title, meta_description, featured_image, updated_at
             FROM pages
             WHERE status = 'published'
               AND LOWER(meta_keywords) LIKE :kw
             ORDER BY updated_at DESC"
        );
        $escaped = str_replace(['%', '_'], ['\%', '\_'], mb_strtolower($keyword));
        $stmt->execute(['kw' => '%' . $escaped . '%']);
        return $stmt->fetchAll();
    }
}
