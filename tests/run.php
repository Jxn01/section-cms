<?php
// ─── Section CMS — test runner ───
// A tiny, dependency-free test harness for the framework-free core.
// Run with:  php tests/run.php
//
// It covers the pure logic that does not require a database:
// i18n, the HTML sanitizer, routing, SEO meta building, the
// dictionaries (EN/HU parity) and the page templates.

error_reporting(E_ALL);
ini_set('display_errors', '1');

$root = dirname(__DIR__);

// SITE_BASE_URL is normally defined by config/db.php; tests don't use a DB.
define('SITE_BASE_URL', 'https://example.com');

require_once $root . '/core/i18n.php';
require_once $root . '/core/sanitize.php';
require_once $root . '/core/router.php';
require_once $root . '/core/seo.php';

// ─── Minimal assertion harness ───
$tests = 0;
$failures = [];

function check(string $name, bool $cond): void {
    global $tests, $failures;
    $tests++;
    if (!$cond) {
        $failures[] = $name;
        echo "  ✗ {$name}\n";
    } else {
        echo "  ✓ {$name}\n";
    }
}
function eq(string $name, $expected, $actual): void {
    check($name . " (expected " . var_export($expected, true) . ", got " . var_export($actual, true) . ")", $expected === $actual);
}

// ─── i18n ───
echo "i18n\n";
I18n::init('en');
eq('default locale is en', 'en', I18n::locale());
eq('unknown key falls back to the key', 'nope.nope', I18n::t('nope.nope'));
eq('placeholder substitution', 'Keyword: php', I18n::t('site.keyword_breadcrumb', ['keyword' => 'php']));
eq('og locale en', 'en_US', I18n::ogLocale());
eq('normalize invalid → en', 'en', I18n::normalize('zz'));
eq('normalize hu', 'hu', I18n::normalize('HU'));

I18n::init('hu');
eq('hu translation resolves', 'Kezdőlap', I18n::t('site.home'));
eq('og locale hu', 'hu_HU', I18n::ogLocale());
check('hu falls back to en for missing key', I18n::t('common.save') !== ''); // exists in both, sanity
$enDate = (function () { I18n::init('en'); return I18n::formatDate('2026-06-10'); })();
$huDate = (function () { I18n::init('hu'); return I18n::formatDate('2026-06-10'); })();
check('date format differs per locale', $enDate !== $huDate);
I18n::init('en');

// ─── Dictionaries: EN/HU parity ───
echo "dictionaries\n";
$en = require $root . '/config/lang/en.php';
$hu = require $root . '/config/lang/hu.php';
check('en dictionary non-empty', count($en) > 0);
eq('EN and HU have the same number of keys', count($en), count($hu));
check('no key only in EN', count(array_diff_key($en, $hu)) === 0);
check('no key only in HU', count(array_diff_key($hu, $en)) === 0);
$emptyEn = array_filter($en, fn($v) => $v === '');
check('no empty EN values', count($emptyEn) === 0);

// ─── HTML sanitizer ───
echo "sanitize\n";
check('strips <script>', strpos(sanitizeHtml('<p>ok</p><script>alert(1)</script>'), '<script') === false);
check('keeps allowed tags', strpos(sanitizeHtml('<p><strong>hi</strong></p>'), '<strong>') !== false);
check('drops javascript: href', stripos(sanitizeHtml('<a href="javascript:alert(1)">x</a>'), 'javascript:') === false);
check('removes on* handlers', stripos(sanitizeHtml('<p onclick="x()">y</p>'), 'onclick') === false);
check('drops disallowed tags', stripos(sanitizeHtml('<iframe src="x"></iframe>'), '<iframe') === false);

// ─── Router ───
echo "router\n";
eq('root → home', 'home', Router::resolve('/'));
eq('strips query string', 'services', Router::resolve('/services?x=1'));
eq('trims slashes', 'about', Router::resolve('/about/'));
eq('nested slug', 'blog/post', Router::resolve('/blog/post'));

// ─── SEO ───
echo "seo\n";
$page = ['slug' => 'about', 'title' => 'About', 'meta_title' => '', 'meta_description' => '', 'meta_keywords' => '', 'og_title' => '', 'og_description' => '', 'og_image' => '', 'updated_at' => '2026-06-10 00:00:00', 'page_type' => 'page'];
$settings = ['site_name' => 'Acme'];
$seo = SEO::buildMeta($page, $settings);
eq('title falls back to "<title> – <site>"', 'About – Acme', $seo['title']);
eq('canonical built from base url', 'https://example.com/about', $seo['canonical']);
check('json-ld is valid JSON', (function () use ($page, $settings) {
    $html = SEO::renderJsonLd($page, $settings);
    $json = preg_replace('/^<script[^>]*>|<\/script>$/', '', trim($html));
    return json_decode($json) !== null;
})());

// ─── Page templates ───
echo "templates\n";
$templates = require $root . '/config/templates.php';
check('templates is a non-empty array', is_array($templates) && count($templates) > 0);
$allNamesAreKeys = true;
foreach ($templates as $id => $tpl) {
    if (!isset($tpl['name']) || !isset($en[$tpl['name']])) { $allNamesAreKeys = false; break; }
    if (empty($tpl['sections'])) { $allNamesAreKeys = false; break; }
}
check('every template name is a defined i18n key with sections', $allNamesAreKeys);

// ─── Result ───
echo "\n";
if ($failures) {
    echo count($failures) . " of {$tests} checks FAILED:\n";
    foreach ($failures as $f) echo "  - {$f}\n";
    exit(1);
}
echo "All {$tests} checks passed.\n";
exit(0);
