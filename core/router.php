<?php
// ═══════════════════════════════════════════════════════════════
// Router (Útvonalkezelő)
// ═══════════════════════════════════════════════════════════════
// A kérés URL-jéből kinyeri a slug-ot, és az adatbázisban
// megkeresi a hozzá tartozó publikált oldalt.
// Speciális útvonalakat is kezel: sitemap.xml, robots.txt
// ═══════════════════════════════════════════════════════════════

class Router {

    /**
     * Slug kinyerése a kérés URI-ból.
     * A "/" gyökér URL a "home" slug-ra képződik le.
     *
     * @param string $uri  A teljes kérés URI (pl. "/szolgaltatasaink?q=1")
     * @return string      A tiszta slug (pl. "szolgaltatasaink")
     */
    public static function resolve(string $uri): string {
        $path = parse_url($uri, PHP_URL_PATH);
        $path = trim($path, '/');
        return $path === '' ? 'home' : $path;
    }

    /**
     * Publikált oldal lekérése slug alapján.
     * Csak a status='published' oldalakat adja vissza.
     *
     * @param PDO    $pdo   Adatbázis kapcsolat
     * @param string $slug  Az oldal URL slug-ja
     * @return array|false  Az oldal adatai, vagy false ha nem létezik
     */
    public static function getPage(PDO $pdo, string $slug) {
        $stmt = $pdo->prepare(
            "SELECT * FROM pages WHERE slug = :slug AND status = 'published'"
        );
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }

    /**
     * Speciális útvonalak kezelése (sitemap.xml, robots.txt).
     * Ha az útvonal speciális, a kimenetet közvetlenül kiírja.
     *
     * @return bool  true ha kezelte az útvonalat, false ha nem
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
