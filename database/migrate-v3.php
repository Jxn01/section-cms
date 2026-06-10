<?php
// ─── V3 Content Migration ───
// Creates the full page hierarchy and menu structure.
// Run once: https://www.parkoloabc.hu/database/migrate-v3.php
// Then DELETE this file from the server!

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/db.php';

echo "<pre>\n";
echo "═══ V3 Content Migration ═══\n\n";

// ── 1. Schema changes ──
echo "→ Schema changes...\n";
try {
    $pdo->exec("ALTER TABLE media ADD COLUMN is_featured TINYINT(1) DEFAULT 0");
    echo "  ✓ Added is_featured to media\n";
} catch (Exception $e) {
    echo "  ⚠ is_featured: " . $e->getMessage() . "\n";
}
$pdo->exec("INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES ('logo_url', '')");
echo "  ✓ logo_url setting ensured\n";

// ── 2. Fetch site name ──
$siteNameRow = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'site_name'")->fetch();
$siteName = $siteNameRow ? $siteNameRow['setting_value'] : 'Parkoló ABC';

// ── Helper ──
function createPage(PDO $pdo, string $slug, string $title, string $siteName, array $meta, array $sections): int {
    $check = $pdo->prepare("SELECT id FROM pages WHERE slug = :slug");
    $check->execute(['slug' => $slug]);
    if ($existing = $check->fetch()) {
        echo "  ⚠ '{$slug}' already exists (id={$existing['id']}), skipping.\n";
        return (int)$existing['id'];
    }

    $stmt = $pdo->prepare(
        "INSERT INTO pages (slug, title, template, page_type, meta_title, meta_description, meta_keywords, status, sort_order)
         VALUES (:slug, :title, :tpl, 'page', :mt, :md, :mk, 'published',
                 (SELECT COALESCE(MAX(sort_order),0)+1 FROM pages p2))"
    );
    $stmt->execute([
        'slug'  => $slug,
        'title' => $title,
        'tpl'   => $meta['template'] ?? 'simple_text',
        'mt'    => $meta['meta_title'] ?? ($title . ' – ' . $siteName),
        'md'    => $meta['meta_description'] ?? '',
        'mk'    => $meta['meta_keywords'] ?? '',
    ]);
    $pageId = (int)$pdo->lastInsertId();

    $order = 1;
    foreach ($sections as $sec) {
        $pdo->prepare(
            "INSERT INTO sections (page_id, type, content, sort_order) VALUES (:pid, :type, :content, :ord)"
        )->execute([
            'pid'     => $pageId,
            'type'    => $sec['type'],
            'content' => json_encode($sec['content'], JSON_UNESCAPED_UNICODE),
            'ord'     => $order++,
        ]);
    }
    echo "  ✓ Created '{$slug}' (id={$pageId}) with " . count($sections) . " sections\n";
    return $pageId;
}

// ── 3. Create pages ──
echo "\n→ Creating pages...\n";

// ─ Parkoló rendszerek ─
$parkId = createPage($pdo, 'parkolo-rendszerek', 'Parkoló rendszerek', $siteName, [
    'template' => 'hero_cards_cta',
    'meta_description' => 'Professzionális parkoló rendszerek tervezése, telepítése és karbantartása. Fizetős, beléptetős és vegyes parkolórendszerek.',
    'meta_keywords' => 'parkoló rendszer, parkolóház, parkoló automata, sorompó rendszer, fizetős parkoló, beléptetős parkoló',
], [
    ['type' => 'hero', 'content' => [
        'heading' => 'Parkoló rendszerek', 'subtitle' => 'Tervezés, telepítés, karbantartás — teljes körű parkolórendszer megoldások.',
        'image' => '', 'cta_text' => 'Árajánlat kérés', 'cta_url' => '/kapcsolat',
    ]],
    ['type' => 'image_text', 'content' => [
        'heading' => 'Komplex parkolórendszer megoldások',
        'body' => '<p>Cégünk teljes körű parkolórendszer megoldásokat kínál a tervezéstől a telepítésen át a karbantartásig. Rendszereink bevásárlóközpontokban, irodaházakban, kórházakban és reptéri parkolókban egyaránt megtalálhatók.</p><p>Minden rendszer egyedi igényekre szabottan készül, a legkorszerűbb technológiákat alkalmazva.</p>',
        'image' => '', 'image_alt' => 'Parkoló rendszer', 'image_position' => 'left',
    ]],
    ['type' => 'cards', 'content' => [
        'heading' => 'Parkolórendszer típusok',
        'cards' => [
            ['title' => 'Fizetős parkolórendszerek', 'description' => 'Automata fizetős rendszerek bevásárlóközpontokhoz, reptéri és közterületi parkolókhoz.', 'icon' => '💳', 'link' => '/fizetos-parkolo-rendszerek'],
            ['title' => 'Beléptetős parkolórendszerek', 'description' => 'RFID, kártyás és rendszámfelismerő beléptetéssel működő parkolórendszerek.', 'icon' => '🚧', 'link' => '/beleptetos-parkolo-rendszerek'],
            ['title' => 'Vegyes parkolórendszerek', 'description' => 'Fizetős és beléptetős funkciók kombinálása egyedi igények szerint.', 'icon' => '🔄', 'link' => '/vegyes-parkolo-rendszerek'],
        ],
    ]],
    ['type' => 'product_grid', 'content' => [
        'heading' => 'Parkolórendszer kiegészítők',
        'columns' => 5,
        'items' => [
            ['title' => 'Sorompó', 'short_desc' => 'Gyors nyitású automata sorompók járműforgalom szabályozáshoz.', 'image' => '', 'image_alt' => 'Sorompó', 'url' => ''],
            ['title' => 'Fizető automata', 'short_desc' => 'Érmés, bankjegyes és bankkártyás fizetési lehetőség.', 'image' => '', 'image_alt' => 'Fizető automata', 'url' => ''],
            ['title' => 'Jegykiadó', 'short_desc' => 'Belépő jegyeket kiadó automata a parkoló bejáratánál.', 'image' => '', 'image_alt' => 'Jegykiadó', 'url' => ''],
            ['title' => 'Rendszámfelismerő', 'short_desc' => 'ANPR kamerás automatikus rendszámfelismerő rendszer.', 'image' => '', 'image_alt' => 'Rendszámfelismerő', 'url' => ''],
            ['title' => 'Útjelző tábla', 'short_desc' => 'LED kijelzős szabad/foglalt jelző és irányító rendszer.', 'image' => '', 'image_alt' => 'Útjelző tábla', 'url' => ''],
            ['title' => 'Intercom', 'short_desc' => 'Segélyhívó oszlop és kommunikációs rendszer.', 'image' => '', 'image_alt' => 'Intercom', 'url' => ''],
            ['title' => 'Hurokdetektor', 'short_desc' => 'Járműérzékelő hurok a sorompók automatikus vezérléséhez.', 'image' => '', 'image_alt' => 'Hurokdetektor', 'url' => ''],
            ['title' => 'Parkolás vezérlő', 'short_desc' => 'Központi szerver szoftver a teljes rendszer felügyeletéhez.', 'image' => '', 'image_alt' => 'Parkolás vezérlő', 'url' => ''],
            ['title' => 'Forgóvilla', 'short_desc' => 'Gyalogos beléptető forgóvilla a parkoló melletti gyalogos bejárathoz.', 'image' => '', 'image_alt' => 'Forgóvilla', 'url' => ''],
            ['title' => 'Térfigyelő kamera', 'short_desc' => 'IP kamerás megfigyelő rendszer a parkoló biztonságáért.', 'image' => '', 'image_alt' => 'Térfigyelő kamera', 'url' => ''],
        ],
    ]],
    ['type' => 'link_banner', 'content' => [
        'text' => 'Alapfogalmak és jelmagyarázat — Parkolórendszer szakkifejezések', 'url' => '/alapfogalmak', 'icon' => '📖', 'background_color' => '#0067FF',
    ]],
    ['type' => 'cta', 'content' => [
        'heading' => 'Kérjen ingyenes árajánlatot!', 'subtitle' => 'Szakértőink személyre szabott parkolórendszer megoldást dolgoznak ki.',
        'button_text' => 'Kapcsolatfelvétel', 'button_url' => '/kapcsolat', 'background_color' => '#0067FF',
    ]],
]);

// ─ Fizetős parkolórendszerek ─
createPage($pdo, 'fizetos-parkolo-rendszerek', 'Fizetős parkolórendszerek', $siteName, [
    'template' => 'hero_text_imagetext',
    'meta_description' => 'Automata fizetős parkolórendszerek telepítése. Érmés, bankjegyes és bankkártyás fizető automaták.',
    'meta_keywords' => 'fizetős parkoló, fizető automata, parkolójegy, bankkártyás fizetés, parkoló rendszer',
], [
    ['type' => 'hero', 'content' => ['heading' => 'Fizetős parkolórendszerek', 'subtitle' => 'Modern fizetési megoldások automatizált parkolórendszerekhez.', 'image' => '', 'cta_text' => '', 'cta_url' => '']],
    ['type' => 'image_text', 'content' => [
        'heading' => 'Automatizált fizetős parkolás',
        'body' => '<p>Fizetős parkolórendszereink a legmodernebb technológiát alkalmazzák. Az ügyfél a parkoló bejáratánál jegyet kap, a távozás előtt pedig az automatánál fizet — készpénzzel vagy bankkártyával.</p><p>A rendszer teljesen automatizált, emberi beavatkozás nélkül üzemel.</p>',
        'image' => '', 'image_alt' => 'Fizetős parkolórendszer', 'image_position' => 'left',
    ]],
    ['type' => 'product_grid', 'content' => [
        'heading' => 'Fizetős rendszer komponensek', 'columns' => 5,
        'items' => [
            ['title' => 'Érmés fizető automata', 'short_desc' => 'Hagyományos érmés fizetésre alkalmas automata.', 'image' => '', 'image_alt' => 'Érmés automata', 'url' => ''],
            ['title' => 'Bankkártyás terminál', 'short_desc' => 'Érintéses és chipes bankkártya elfogadás.', 'image' => '', 'image_alt' => 'Bankkártyás terminál', 'url' => ''],
            ['title' => 'Bankjegy elfogadó', 'short_desc' => 'Papírpénz elfogadó és visszaadó egység.', 'image' => '', 'image_alt' => 'Bankjegy elfogadó', 'url' => ''],
            ['title' => 'Központi fizető', 'short_desc' => 'Központi pénztárgép manuális fizetéshez.', 'image' => '', 'image_alt' => 'Központi fizető', 'url' => ''],
            ['title' => 'QR kódos fizetés', 'short_desc' => 'Mobilfizetés QR kód beolvasással.', 'image' => '', 'image_alt' => 'QR kódos fizetés', 'url' => ''],
        ],
    ]],
    ['type' => 'link_banner', 'content' => ['text' => 'Alapfogalmak — Fizetős parkolórendszer szakkifejezések', 'url' => '/alapfogalmak', 'icon' => '📖', 'background_color' => '#0067FF']],
    ['type' => 'cta', 'content' => ['heading' => 'Érdekli a fizetős parkolórendszer?', 'subtitle' => 'Kérjen személyre szabott árajánlatot!', 'button_text' => 'Árajánlat kérés', 'button_url' => '/kapcsolat', 'background_color' => '#0067FF']],
]);

// ─ Beléptetős parkolórendszerek ─
createPage($pdo, 'beleptetos-parkolo-rendszerek', 'Beléptetős parkolórendszerek', $siteName, [
    'template' => 'hero_text_imagetext',
    'meta_description' => 'RFID, kártyás és rendszámfelismerő beléptetős parkolórendszerek. Gyors és biztonságos ki-bejárás.',
    'meta_keywords' => 'beléptetős parkoló, RFID beléptető, parkoló kártya, rendszámfelismerő parkoló',
], [
    ['type' => 'hero', 'content' => ['heading' => 'Beléptetős parkolórendszerek', 'subtitle' => 'RFID, kártyás és rendszámfelismerő rendszerek.', 'image' => '', 'cta_text' => '', 'cta_url' => '']],
    ['type' => 'image_text', 'content' => [
        'heading' => 'Intelligens beléptető parkolás',
        'body' => '<p>Beléptetős rendszereink lehetővé teszik, hogy előre regisztrált felhasználók gyorsan és automatikusan ki-be járjanak a parkolóba. A rendszám automatikusan felismerésre kerül, vagy RFID kártyát használ a felhasználó.</p><p>Ideális irodaházakhoz, lakóparkokhoz és zártkertes területekhez.</p>',
        'image' => '', 'image_alt' => 'Beléptetős parkoló', 'image_position' => 'right',
    ]],
    ['type' => 'product_grid', 'content' => [
        'heading' => 'Beléptető rendszer komponensek', 'columns' => 5,
        'items' => [
            ['title' => 'RFID olvasó', 'short_desc' => 'Nagy hatótávolságú RFID kártyaolvasó.', 'image' => '', 'image_alt' => 'RFID olvasó', 'url' => ''],
            ['title' => 'ANPR kamera', 'short_desc' => 'Automatikus rendszámfelismerő kamera.', 'image' => '', 'image_alt' => 'ANPR kamera', 'url' => ''],
            ['title' => 'Proximity kártya', 'short_desc' => 'Közelítős belépőkártya.', 'image' => '', 'image_alt' => 'Proximity kártya', 'url' => ''],
            ['title' => 'Vezérlőegység', 'short_desc' => 'Hálózati vezérlő a központi irányításhoz.', 'image' => '', 'image_alt' => 'Vezérlőegység', 'url' => ''],
            ['title' => 'Kezelőfelület', 'short_desc' => 'Webes felület jogosultságok kezeléséhez.', 'image' => '', 'image_alt' => 'Kezelőfelület', 'url' => ''],
        ],
    ]],
    ['type' => 'link_banner', 'content' => ['text' => 'Alapfogalmak — Beléptető parkolórendszer szakkifejezések', 'url' => '/alapfogalmak', 'icon' => '📖', 'background_color' => '#0067FF']],
    ['type' => 'cta', 'content' => ['heading' => 'Érdekli a beléptető rendszer?', 'subtitle' => 'Kérjen személyre szabott árajánlatot!', 'button_text' => 'Árajánlat kérés', 'button_url' => '/kapcsolat', 'background_color' => '#0067FF']],
]);

// ─ Vegyes parkolórendszerek ─
createPage($pdo, 'vegyes-parkolo-rendszerek', 'Vegyes parkolórendszerek', $siteName, [
    'template' => 'hero_text_imagetext',
    'meta_description' => 'Fizetős és beléptetős funkciók egyetlen rendszerben. Vegyes parkolórendszerek egyedi igényekre.',
    'meta_keywords' => 'vegyes parkolórendszer, kombinált parkoló, fizetős beléptető, parkolóház rendszer',
], [
    ['type' => 'hero', 'content' => ['heading' => 'Vegyes parkolórendszerek', 'subtitle' => 'Fizetős és beléptetős funkciók kombinálása egyedi igényekre.', 'image' => '', 'cta_text' => '', 'cta_url' => '']],
    ['type' => 'image_text', 'content' => [
        'heading' => 'Kombinált megoldások',
        'body' => '<p>Vegyes rendszereink ötvözik a fizetős és beléptetős megoldások előnyeit. Így egyetlen rendszerrel kezelhetők az állandó bérlők (RFID/kártya) és az alkalmi parkolók (jegy + fizetés) egyaránt.</p><p>Például irodaház parkolókban az alkalmazottak kártyával lépnek be, a vendégek pedig fizető automatánál fizetnek.</p>',
        'image' => '', 'image_alt' => 'Vegyes parkolórendszer', 'image_position' => 'left',
    ]],
    ['type' => 'product_grid', 'content' => [
        'heading' => 'Vegyes rendszer komponensek', 'columns' => 5,
        'items' => [
            ['title' => 'Kombó terminál', 'short_desc' => 'Egy egységben RFID olvasó és jegykiadó.', 'image' => '', 'image_alt' => 'Kombó terminál', 'url' => ''],
            ['title' => 'Vendég jegykiadó', 'short_desc' => 'Jegykiadó az alkalmi parkolók számára.', 'image' => '', 'image_alt' => 'Vendég jegykiadó', 'url' => ''],
            ['title' => 'Bérlet kezelő', 'short_desc' => 'Szoftver modul bérletes parkolókhoz.', 'image' => '', 'image_alt' => 'Bérlet kezelő', 'url' => ''],
            ['title' => 'Kétoldalú sorompó', 'short_desc' => 'Ki- és bejárati sorompó integráltan.', 'image' => '', 'image_alt' => 'Kétoldalú sorompó', 'url' => ''],
            ['title' => 'Menedzsment szoftver', 'short_desc' => 'Szoftver bérletek, fizetések és riportok kezelésére.', 'image' => '', 'image_alt' => 'Menedzsment szoftver', 'url' => ''],
        ],
    ]],
    ['type' => 'link_banner', 'content' => ['text' => 'Alapfogalmak — Vegyes parkolórendszer szakkifejezések', 'url' => '/alapfogalmak', 'icon' => '📖', 'background_color' => '#0067FF']],
    ['type' => 'cta', 'content' => ['heading' => 'Tervezzen velünk!', 'subtitle' => 'Egyedi vegyes parkolórendszer az Ön igényeire.', 'button_text' => 'Árajánlat kérés', 'button_url' => '/kapcsolat', 'background_color' => '#0067FF']],
]);

// ─ Beléptető rendszerek (main) ─
createPage($pdo, 'belepteto-rendszerek', 'Beléptető rendszerek', $siteName, [
    'template' => 'hero_text_imagetext',
    'meta_description' => 'Beléptető rendszerek tervezése és telepítése. RFID, kártyás, biometrikus és rendszámfelismerő megoldások.',
    'meta_keywords' => 'beléptető rendszer, RFID beléptető, biometrikus beléptető, kártyás beléptetés, jogosultság kezelés',
], [
    ['type' => 'hero', 'content' => ['heading' => 'Beléptető rendszerek', 'subtitle' => 'Komplex beléptetési megoldások az Ön biztonságáért.', 'image' => '', 'cta_text' => 'Árajánlat kérés', 'cta_url' => '/kapcsolat']],
    ['type' => 'image_text', 'content' => [
        'heading' => 'Modern beléptetési megoldások',
        'body' => '<p>Beléptető rendszereink biztosítják, hogy csak az arra jogosult személyek léphessenek be az adott területre. RFID, kártyás, PIN kódos és biometrikus (ujjlenyomat, arcfelismerő) azonosítási módokat kínálunk.</p>',
        'image' => '', 'image_alt' => 'Beléptető rendszer', 'image_position' => 'left',
    ]],
    ['type' => 'text', 'content' => [
        'heading' => 'Miért válasszon minket?',
        'body' => '<p>Több mint 15 éves tapasztalattal rendelkezünk beléptető rendszerek tervezésében és telepítésében.</p><ul><li>Egyedi tervezés az Ön igényeire</li><li>Professzionális telepítés és beüzemelés</li><li>Garanciális és garancián túli szerviz</li><li>24/7 hibaelhárítási lehetőség</li></ul>',
    ]],
    ['type' => 'product_grid', 'content' => [
        'heading' => 'Beléptető rendszer kiegészítők', 'columns' => 4,
        'items' => [
            ['title' => 'RFID kártyaolvasó', 'short_desc' => 'Nagy megbízhatóságú proximity kártyaolvasó.', 'image' => '', 'image_alt' => 'RFID kártyaolvasó', 'url' => ''],
            ['title' => 'Ujjlenyomat olvasó', 'short_desc' => 'Biometrikus azonosítás ujjlenyomattal.', 'image' => '', 'image_alt' => 'Ujjlenyomat olvasó', 'url' => ''],
            ['title' => 'Arcfelismerő kamera', 'short_desc' => 'AI alapú arcfelismerő beléptető.', 'image' => '', 'image_alt' => 'Arcfelismerő', 'url' => ''],
            ['title' => 'Forgóvilla', 'short_desc' => 'Gyalogos forgóvilla szabályozott áthaladáshoz.', 'image' => '', 'image_alt' => 'Forgóvilla', 'url' => ''],
            ['title' => 'Forgókapu', 'short_desc' => 'Teljes magasságú forgókapu magas biztonsággal.', 'image' => '', 'image_alt' => 'Forgókapu', 'url' => ''],
            ['title' => 'Gyorskapu', 'short_desc' => 'Elegáns speed gate irodaházak recepcióihoz.', 'image' => '', 'image_alt' => 'Gyorskapu', 'url' => ''],
            ['title' => 'Elektromos zár', 'short_desc' => 'Elektromos ajtózár beléptetőhöz csatlakoztatva.', 'image' => '', 'image_alt' => 'Elektromos zár', 'url' => ''],
            ['title' => 'Vezérlő szoftver', 'short_desc' => 'Központi szoftver jogosultságok és naplók kezeléséhez.', 'image' => '', 'image_alt' => 'Vezérlő szoftver', 'url' => ''],
        ],
    ]],
    ['type' => 'link_banner', 'content' => ['text' => 'Alapfogalmak és jelmagyarázat — Beléptető rendszer szakkifejezések', 'url' => '/alapfogalmak', 'icon' => '📖', 'background_color' => '#0067FF']],
    ['type' => 'cta', 'content' => ['heading' => 'Érdeklődik beléptető rendszer iránt?', 'subtitle' => 'Ingyenes konzultáció és árajánlat!', 'button_text' => 'Kapcsolatfelvétel', 'button_url' => '/kapcsolat', 'background_color' => '#0067FF']],
]);

// ─ Munkaidő nyilvántartó rendszerek ─
createPage($pdo, 'munkaido-nyilvantarto-rendszerek', 'Munkaidő nyilvántartó rendszerek', $siteName, [
    'template' => 'hero_text_imagetext',
    'meta_description' => 'Munkaidő nyilvántartó rendszerek telepítése. Pontos jelenléti adatok ujjlenyomatos, kártyás és arcfelismerős terminálokkal.',
    'meta_keywords' => 'munkaidő nyilvántartás, jelenléti rendszer, munkaidő terminál, ujjlenyomatos blokkoló, munkaidő szoftver',
], [
    ['type' => 'hero', 'content' => ['heading' => 'Munkaidő nyilvántartó rendszerek', 'subtitle' => 'Pontos jelenléti nyilvántartás — automatikusan, megbízhatóan.', 'image' => '', 'cta_text' => 'Árajánlat kérés', 'cta_url' => '/kapcsolat']],
    ['type' => 'image_text', 'content' => [
        'heading' => 'Automatizált munkaidő nyilvántartás',
        'body' => '<p>Munkaidő nyilvántartó rendszereink pontos és megbízható jelenléti adatokat biztosítanak. A dolgozók RFID kártyával, ujjlenyomattal vagy arcfelismeréssel regisztrálják az érkezést és távozást.</p>',
        'image' => '', 'image_alt' => 'Munkaidő nyilvántartó', 'image_position' => 'left',
    ]],
    ['type' => 'text', 'content' => [
        'heading' => 'Funkciók és előnyök',
        'body' => '<p>Rendszereink a legkorszerűbb technológiát alkalmazzák és megfelelnek a magyar munkaügyi előírásoknak.</p><ul><li>Automatikus munkaidő számítás (rendes, túlóra, éjszakai)</li><li>Szabadság és betegszabadság nyilvántartás</li><li>Bérszámfejtő programokkal való integráció</li><li>Webes és mobil kezelőfelület</li><li>Többtelephelyes megoldás</li><li>Riportok és kimutatások exportja</li></ul>',
    ]],
    ['type' => 'product_grid', 'content' => [
        'heading' => 'Munkaidő nyilvántartó eszközök', 'columns' => 4,
        'items' => [
            ['title' => 'Kártyás terminál', 'short_desc' => 'RFID kártyás munkaidő terminál.', 'image' => '', 'image_alt' => 'Kártyás terminál', 'url' => ''],
            ['title' => 'Ujjlenyomatos terminál', 'short_desc' => 'Biometrikus ujjlenyomat-olvasós blokkoló.', 'image' => '', 'image_alt' => 'Ujjlenyomatos terminál', 'url' => ''],
            ['title' => 'Arcfelismerő terminál', 'short_desc' => 'Modern arcfelismerő munkaidő terminál.', 'image' => '', 'image_alt' => 'Arcfelismerő terminál', 'url' => ''],
            ['title' => 'Munkaidő szoftver', 'short_desc' => 'Központi szoftver a munkaidő adatok feldolgozásához.', 'image' => '', 'image_alt' => 'Munkaidő szoftver', 'url' => ''],
        ],
    ]],
    ['type' => 'link_banner', 'content' => ['text' => 'Alapfogalmak — Munkaidő nyilvántartó szakkifejezések', 'url' => '/alapfogalmak', 'icon' => '📖', 'background_color' => '#0067FF']],
    ['type' => 'cta', 'content' => ['heading' => 'Érdekli a munkaidő nyilvántartás?', 'subtitle' => 'Kérjen ingyenes bemutatót és árajánlatot!', 'button_text' => 'Kapcsolatfelvétel', 'button_url' => '/kapcsolat', 'background_color' => '#0067FF']],
]);

// ─ Alapfogalmak ─
createPage($pdo, 'alapfogalmak', 'Alapfogalmak és jelmagyarázat', $siteName, [
    'template' => 'faq_page',
    'meta_description' => 'Parkoló, beléptető és munkaidő nyilvántartó rendszerek alapfogalmai és szakkifejezések magyarázata.',
    'meta_keywords' => 'alapfogalmak, jelmagyarázat, parkoló szakkifejezések, RFID, ANPR, sorompó, forgóvilla, beléptető fogalmak',
], [
    ['type' => 'hero', 'content' => ['heading' => 'Alapfogalmak és jelmagyarázat', 'subtitle' => 'Szakkifejezések és fogalmak érthetően.', 'image' => '', 'cta_text' => '', 'cta_url' => '']],
    ['type' => 'accordion', 'content' => [
        'heading' => 'Parkolórendszer fogalmak',
        'items' => [
            ['question' => 'Sorompó', 'answer' => '<p>Automata vagy kézi működtetésű fizikai akadály, amely a járművek be- és kilépését szabályozza.</p>'],
            ['question' => 'Fizető automata', 'answer' => '<p>Automata gép, ahol a parkoló díj fizethető. Elfogadhat érmét, bankjegyet, bankkártyát vagy mobilfizetést.</p>'],
            ['question' => 'ANPR (Automatic Number Plate Recognition)', 'answer' => '<p>Automatikus rendszámfelismerő rendszer. Kamerával olvassa le a járművek rendszámát.</p>'],
            ['question' => 'Hurokdetektor', 'answer' => '<p>Az útburkolat alá épített induktív hurok, amely érzékeli a fölötte elhaladó járművet.</p>'],
            ['question' => 'Parkoló menedzsment szoftver', 'answer' => '<p>Központi szoftver, amely a teljes parkolórendszert felügyeli: forgalmi adatok, bevételek, bérletek, riportok.</p>'],
        ],
    ]],
    ['type' => 'accordion', 'content' => [
        'heading' => 'Beléptető rendszer fogalmak',
        'items' => [
            ['question' => 'RFID (Radio-Frequency Identification)', 'answer' => '<p>Rádiófrekvenciás azonosítási technológia. A kártya vagy tag egyedi azonosítót sugároz.</p>'],
            ['question' => 'Proximity kártya', 'answer' => '<p>Közelítős kártya, amelyet az olvasóhoz közel tartva azonosítja a felhasználót.</p>'],
            ['question' => 'Biometrikus azonosítás', 'answer' => '<p>Az emberi test egyedi jellemzőin alapuló azonosítás: ujjlenyomat, arcfelismerés, írisz.</p>'],
            ['question' => 'Forgóvilla', 'answer' => '<p>Háromkarú forgó kapu, amely egyszerre egy személy áthaladását engedi.</p>'],
            ['question' => 'Speed gate / Gyorskapu', 'answer' => '<p>Elegáns, üvegszárnyas gyors áthaladást biztosító beléptető. Irodaházak recepcióin gyakori.</p>'],
        ],
    ]],
    ['type' => 'accordion', 'content' => [
        'heading' => 'Munkaidő nyilvántartó fogalmak',
        'items' => [
            ['question' => 'Blokkoló óra', 'answer' => '<p>Munkaidő nyilvántartó terminál, amelyen a dolgozó regisztrálja az érkezését és távozását.</p>'],
            ['question' => 'Jelenléti ív', 'answer' => '<p>Digitális vagy papíralapú nyilvántartás a dolgozók jelenlétéről.</p>'],
            ['question' => 'Műszakbeosztás', 'answer' => '<p>A dolgozók munkaidő beosztása: rendes, délelőttös, délutános, éjszakai műszak.</p>'],
        ],
    ]],
    ['type' => 'cta', 'content' => ['heading' => 'Kérdése van?', 'subtitle' => 'Ha nem találta a keresett fogalmat, írjon nekünk!', 'button_text' => 'Kapcsolatfelvétel', 'button_url' => '/kapcsolat', 'background_color' => '#0067FF']],
]);

// ─ Referenciáink ─
createPage($pdo, 'referenciaink', 'Referenciáink', $siteName, [
    'template' => 'hero_gallery',
    'meta_description' => 'Referenciáink — korábbi projektjeink és telepítéseink bemutatója.',
    'meta_keywords' => 'referenciák, projektek, telepítés, parkoló rendszer referencia, beléptető rendszer referencia',
], [
    ['type' => 'hero', 'content' => ['heading' => 'Referenciáink', 'subtitle' => 'Büszkék vagyunk eddigi munkáinkra.', 'image' => '', 'cta_text' => '', 'cta_url' => '']],
    ['type' => 'reference_gallery', 'content' => [
        'heading' => 'Korábbi projektjeink',
        'projects' => [
            ['title' => 'Bevásárlóközpont parkoló', 'cover_image' => '', 'cover_alt' => 'Bevásárlóközpont parkoló', 'images' => []],
            ['title' => 'Irodaház beléptető', 'cover_image' => '', 'cover_alt' => 'Irodaház beléptető rendszer', 'images' => []],
            ['title' => 'Kórház parkoló', 'cover_image' => '', 'cover_alt' => 'Kórházi parkolórendszer', 'images' => []],
            ['title' => 'Gyár munkaidő nyilvántartás', 'cover_image' => '', 'cover_alt' => 'Gyári munkaidő rendszer', 'images' => []],
        ],
    ]],
    ['type' => 'cta', 'content' => ['heading' => 'Legyen Ön a következő referenciánk!', 'subtitle' => 'Kérjen ingyenes helyszíni felmérést!', 'button_text' => 'Kapcsolatfelvétel', 'button_url' => '/kapcsolat', 'background_color' => '#0067FF']],
]);

// ── 4. Recreate menu ──
echo "\n→ Updating menu...\n";
$pdo->exec("DELETE FROM menus");
echo "  ✓ Old menu items cleared\n";

function getPageId(PDO $pdo, string $slug): ?int {
    $stmt = $pdo->prepare("SELECT id FROM pages WHERE slug = :slug");
    $stmt->execute(['slug' => $slug]);
    $row = $stmt->fetch();
    return $row ? (int)$row['id'] : null;
}

$menuItems = [
    ['label' => 'Kezdőlap',       'pid' => getPageId($pdo, 'home'),       'url' => '/',        'sort' => 1, 'children' => []],
    ['label' => 'Parkoló rendszerek', 'pid' => getPageId($pdo, 'parkolo-rendszerek'), 'url' => '/parkolo-rendszerek', 'sort' => 2, 'children' => [
        ['label' => 'Fizetős parkolórendszerek',   'pid' => getPageId($pdo, 'fizetos-parkolo-rendszerek'),   'url' => '/fizetos-parkolo-rendszerek',   'sort' => 1],
        ['label' => 'Beléptetős parkolórendszerek', 'pid' => getPageId($pdo, 'beleptetos-parkolo-rendszerek'), 'url' => '/beleptetos-parkolo-rendszerek', 'sort' => 2],
        ['label' => 'Vegyes parkolórendszerek',     'pid' => getPageId($pdo, 'vegyes-parkolo-rendszerek'),   'url' => '/vegyes-parkolo-rendszerek',   'sort' => 3],
    ]],
    ['label' => 'Beléptető rendszerek',  'pid' => getPageId($pdo, 'belepteto-rendszerek'),  'url' => '/belepteto-rendszerek',  'sort' => 3, 'children' => []],
    ['label' => 'Munkaidő nyilvántartás', 'pid' => getPageId($pdo, 'munkaido-nyilvantarto-rendszerek'), 'url' => '/munkaido-nyilvantarto-rendszerek', 'sort' => 4, 'children' => []],
    ['label' => 'Szolgáltatásaink',      'pid' => getPageId($pdo, 'szolgaltatasaink'),      'url' => '/szolgaltatasaink',      'sort' => 5, 'children' => []],
    ['label' => 'Referenciáink',         'pid' => getPageId($pdo, 'referenciaink'),         'url' => '/referenciaink',         'sort' => 6, 'children' => []],
    ['label' => 'Alapfogalmak',          'pid' => getPageId($pdo, 'alapfogalmak'),          'url' => '/alapfogalmak',          'sort' => 7, 'children' => []],
    ['label' => 'Kapcsolat',             'pid' => getPageId($pdo, 'kapcsolat'),             'url' => '/kapcsolat',             'sort' => 8, 'children' => []],
];

$ins = $pdo->prepare("INSERT INTO menus (label, url, page_id, sort_order, parent_id) VALUES (:l, :u, :p, :s, :par)");
foreach ($menuItems as $item) {
    $ins->execute(['l' => $item['label'], 'u' => $item['url'], 'p' => $item['pid'], 's' => $item['sort'], 'par' => null]);
    $parentId = (int)$pdo->lastInsertId();
    echo "  ✓ {$item['label']}\n";
    foreach ($item['children'] as $child) {
        $ins->execute(['l' => $child['label'], 'u' => $child['url'], 'p' => $child['pid'], 's' => $child['sort'], 'par' => $parentId]);
        echo "    ✓ {$child['label']}\n";
    }
}

echo "\n═══ Migration complete! ═══\n\n";
echo "Next steps:\n";
echo "  1. Upload product images → Média\n";
echo "  2. Mark reference images as 'Kiemelt' for homepage slideshow\n";
echo "  3. Upload logo → Beállítások → Logo URL\n";
echo "  4. On homepage: add 'Hero diavetítés' section, configure overlay boxes\n";
echo "  5. Add 'Rejtett SEO szöveg' section to homepage for ad texts/keywords\n";
echo "  6. DELETE this file from the server!\n";
echo "</pre>";
