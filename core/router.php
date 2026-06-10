<?php
// ═══════════════════════════════════════════════════════════════
// Router
// ═══════════════════════════════════════════════════════════════
// Resolves the slug from the request URL and looks up the matching
// published page in the database. Also handles special routes:
// sitemap.xml and robots.txt.
// ═══════════════════════════════════════════════════════════════

class Router {

    /**
     * Extract the slug from the request URI.
     * The "/" root URL maps to the "home" slug.
     *
     * @param string $uri  The full request URI (e.g. "/services?q=1")
     * @return string      The clean slug (e.g. "services")
     */
    public static function resolve(string $uri): string {
        $path = parse_url($uri, PHP_URL_PATH);
        $path = trim($path, '/');
        return $path === '' ? 'home' : $path;
    }

    /**
     * Fetch a published page by slug.
     * Only returns pages with status = 'published'.
     *
     * @param PDO    $pdo   Database connection
     * @param string $slug  The page URL slug
     * @return array|false  The page row, or false if it does not exist
     */
    public static function getPage(PDO $pdo, string $slug) {
        $stmt = $pdo->prepare(
            "SELECT * FROM pages WHERE slug = :slug AND status = 'published'"
        );
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }

    /**
     * Handle special routes (sitemap.xml, robots.txt).
     * If the route is special, the output is written directly.
     *
     * @return bool  true if the route was handled, false otherwise
     */
    public static function handleSpecialRoutes(PDO $pdo, string $slug): bool {
        if ($slug === 'sitemap.xml') {
            self::serveSitemap($pdo);
            return true;
        }
        if ($slug === 'robots.txt') {
            self::serveRobots();
            return true;
        }
        return false;
    }

    private static function serveSitemap(PDO $pdo): void {
        header('Content-Type: application/xml; charset=utf-8');

        $baseUrl = SITE_BASE_URL;
        $stmt = $pdo->query(
            "SELECT slug, updated_at, featured_image FROM pages WHERE status = 'published' ORDER BY sort_order"
        );
        $pages = $stmt->fetchAll();

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        echo '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
        foreach ($pages as $p) {
            $loc     = $p['slug'] === 'home' ? $baseUrl . '/' : $baseUrl . '/' . $p['slug'];
            $lastmod = date('Y-m-d', strtotime($p['updated_at']));
            $isHome  = $p['slug'] === 'home';
            $priority   = $isHome ? '1.0' : '0.8';
            $changefreq = $isHome ? 'daily' : 'weekly';
            echo "  <url>\n";
            echo "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
            echo "    <lastmod>{$lastmod}</lastmod>\n";
            echo "    <changefreq>{$changefreq}</changefreq>\n";
            echo "    <priority>{$priority}</priority>\n";
            if (!empty($p['featured_image'])) {
                $imgUrl = $p['featured_image'];
                if (strpos($imgUrl, 'http') !== 0) { $imgUrl = $baseUrl . $imgUrl; }
                echo "    <image:image>\n";
                echo "      <image:loc>" . htmlspecialchars($imgUrl) . "</image:loc>\n";
                echo "    </image:image>\n";
            }
            echo "  </url>\n";
        }
        echo '</urlset>';
    }

    private static function serveRobots(): void {
        header('Content-Type: text/plain');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin/\n\n";
        echo 'Sitemap: ' . SITE_BASE_URL . "/sitemap.xml\n";
    }
}
