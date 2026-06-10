<?php
// ─── Admin — Component Showcase ───
// Visual preview of all 22 section types available in the page builder.

require_once __DIR__ . '/auth.php';
requireLogin();

$h = function ($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); };

// Section type labels
$sectionLabels = [
    'hero'           => 'Hero (fejléckép)',
    'text'           => 'Szöveg',
    'image_text'     => 'Kép + szöveg',
    'cards'          => 'Kártyák',
    'cta'            => 'CTA (cselekvésre ösztönzés)',
    'gallery'        => 'Galéria',
    'ticker'         => 'Futó szöveg (hírszalag)',
    'accordion'      => 'Harmonika (GYIK)',
    'video'          => 'Videó',
    'divider'        => 'Elválasztó',
    'two_columns'    => 'Két oszlop',
    'testimonials'   => 'Vélemények',
    'stats'          => 'Számok / Statisztika',
    'page_list'      => 'Oldal/cikk lista',
    'map'            => 'Térkép',
    'contact_form'   => 'Kapcsolat űrlap',
    'keywords_cloud' => 'Kulcsszó felhő',
    'hero_slideshow'    => 'Hero diavetítés',
    'product_grid'      => 'Termékrács (hover leírás)',
    'seo_hidden'        => 'Rejtett SEO szöveg',
    'link_banner'       => 'Linksáv (hivatkozás)',
    'reference_gallery' => 'Referencia galéria',
    'sitemap'              => 'Oldaltérkép',
    'tudasmorzsak'         => 'Tudásmorzsák',
];

// Section type descriptions
$sectionDescriptions = [
    'hero'           => 'Teljes szélességű fejléckép nagy címsorral, alcímmel és opcionális CTA gombbal. Ideális az oldal tetejére, hogy azonnal megragadja a figyelmet.',
    'text'           => 'Egyszerű szöveges blokk címsorral és formázható tartalommal (félkövér, lista, link stb.). A leggyakrabban használt szekciótípus.',
    'image_text'     => 'Kép és szöveg egymás mellett. A kép lehet bal vagy jobb oldalon. Tökéletes szolgáltatás vagy termék bemutatásához.',
    'cards'          => 'Kártyák rácsban, mindegyik ikonnal, címmel és leírással. Ideális szolgáltatások vagy előnyök felsorolásához.',
    'cta'            => 'Cselekvésre ösztönző sáv háttérszínnel, szöveggel és gombbal. Használja az oldal közepén vagy végén a látogató aktivizálásához.',
    'gallery'        => 'Képgaléria rács elrendezésben. A képek kattintásra nagyíthatók. Ideális referenciák vagy projektek bemutatásához.',
    'ticker'         => 'Vízszintesen futó szövegszalag. Figyelemfelkeltő hírek, akciók vagy fontos információk megjelenítésére.',
    'accordion'      => 'Lenyíló kérdés-válasz elemek. Tökéletes GYIK (Gyakran Ismételt Kérdések) oldalakhoz. A Google is szereti, mert FAQ strukturált adatot generál.',
    'video'          => 'YouTube vagy Vimeo videó beágyazás. A videó reszponzívan jelenik meg minden eszközön.',
    'divider'        => 'Vizuális elválasztó vonal, pontok, hullám vagy üres tér. Szekciók közötti vizuális szünet létrehozásához.',
    'two_columns'    => 'Két oszlopos szövegelrendezés. Mindkét oszlop formázható (félkövér, lista, link stb.).',
    'testimonials'   => 'Ügyfélvélemények kártyákon, névvel, pozícióval és opcionális fotóval. Növeli a bizalmat az új látogatóknál.',
    'stats'          => 'Számok/statisztikák kiemelése nagy betűmérettel (pl. „100+ Ügyfél", „15 Év tapasztalat"). Háttérszín beállítható.',
    'page_list'      => 'Automatikus oldal- vagy cikklista. A rendszer a megadott típusú oldalakat (cikk/oldal) listázza ki címmel, leírással és képpel.',
    'map'            => 'Google Maps beágyazott térkép. Illessze be a Google Maps beágyazási URL-t az iroda vagy telephely megjelenítéséhez.',
    'contact_form'   => 'Kapcsolatfelvételi űrlap (név, e-mail, telefon, üzenet). A beérkezett üzenetek az Üzenetek menüben olvashatók.',
    'keywords_cloud' => 'Automatikus kulcsszó felhő — összegyűjti az összes oldal kulcsszavait és a leggyakoribbakat jeleníti meg linkekkel. Belső linkelésre kiváló.',
    'hero_slideshow'    => 'Főoldali diavetítés a Médiában kiemelt jelölésű képekből, automatikus képváltással. Navigációs dobozok a képen.',
    'product_grid'      => 'Termék/eszköz rács képekkel. Kurzor ráhúzásakor leírás, kattintásra a részletes oldalra navigál.',
    'seo_hidden'        => 'Rejtett lenyitható szöveg — Google indexeli, de a weboldalon csak gombra kattintva jelenik meg.',
    'link_banner'       => 'Széles színes sáv, amely egy másik oldalra mutat. Ikonnal, szöveggel és nyíllal.',
    'reference_gallery' => 'Projektek alapján csoportosított galéria. Borítóképek + hover-re részletképek.',
    'sitemap'              => 'Automatikus, hierarchikus oldaltérkép a menüszerkezet és a publikált oldalak alapján.',
    'tudasmorzsak'         => 'Kis tudásdobozok címmel, rövid leírással és linkkel. Random sorrendben jobbról beússzanak.',
];

// Admin field cheat-sheet for each type
$sectionFields = [
    'hero'           => ['Címsor', 'Alcím', 'Háttérkép URL', 'CTA gomb szöveg', 'CTA gomb URL'],
    'text'           => ['Címsor', 'Tartalom (WYSIWYG szerkesztő)'],
    'image_text'     => ['Címsor', 'Tartalom (WYSIWYG)', 'Kép URL', 'Kép alt szöveg', 'Kép pozíció (bal/jobb)'],
    'cards'          => ['Szekció címsor', 'Kártyák: ikon, cím, leírás, link (többször)'],
    'cta'            => ['Címsor', 'Alcím', 'Gomb szöveg', 'Gomb URL', 'Háttérszín'],
    'gallery'        => ['Címsor', 'Képek JSON (url + alt szöveg)'],
    'ticker'         => ['Elemek: szöveg + opcionális link', 'Háttérszín', 'Szöveg szín', 'Sebesség (mp)'],
    'accordion'      => ['Címsor', 'Kérdés–válasz párok (többször)'],
    'video'          => ['Címsor', 'Videó URL (YouTube/Vimeo)', 'Típus (YouTube/Vimeo)'],
    'divider'        => ['Stílus (vonal/pontok/hullám/üres tér)', 'Méret (kompakt/normál/széles)'],
    'two_columns'    => ['Címsor (opcionális)', 'Bal oszlop (WYSIWYG)', 'Jobb oszlop (WYSIWYG)'],
    'testimonials'   => ['Címsor', 'Vélemények: név, pozíció, vélemény szöveg, kép URL (többször)'],
    'stats'          => ['Címsor (opcionális)', 'Háttérszín', 'Számok: szám + címke (többször)'],
    'page_list'      => ['Címsor', 'Oldal típus szűrő (cikk/oldal/minden)', 'Darabszám'],
    'map'            => ['Címsor', 'Google Maps beágyazási URL', 'Magasság (px)'],
    'contact_form'   => ['Címsor', 'Sikeres küldés üzenet'],
    'keywords_cloud' => ['Címsor', 'Maximum kulcsszavak száma'],
    'hero_slideshow'    => ['Címsor', 'Alcím', 'CTA gomb szöveg', 'CTA gomb URL', 'Képváltás intervallum (mp)', 'Navigációs dobozok: cím, URL, méret (nagy/kicsi)'],
    'product_grid'      => ['Címsor', 'Oszlopok száma', 'Termékek: cím, rövid leírás, kép URL, kép alt, link URL'],
    'seo_hidden'        => ['Gomb szöveg', 'Tartalom (WYSIWYG szerkesztő)'],
    'link_banner'       => ['Szöveg', 'Ikon (emoji)', 'Link URL', 'Háttérszín'],
    'reference_gallery' => ['Címsor', 'Projektek: cím, borítókép URL, borító alt, további képek (url + alt)'],
    'sitemap'              => ['Címsor'],
    'tudasmorzsak'         => ['Címsor', 'Elemek: cím, rövid leírás, link URL (többször)'],
];

require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1>🧩 Komponens katalógus</h1>
</div>

<div class="help-box">
    <strong>Ez az oldal bemutatja az összes elérhető szekciótípust.</strong>
    Minden komponensről láthatja az élő előnézetet, a leírást és a szerkeszthető mezőket.
    Szekciók hozzáadásához nyisson meg egy oldalt a <a href="/admin/pages.php">Szerkesztés</a>
    gombbal, majd görgessen az „Új szekció hozzáadása" részhez.
</div>

<!-- Quick navigation -->
<div style="margin-bottom:2rem;">
    <strong>Ugrás:</strong>
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
                            <h1>Üdvözöljük a Parkoló ABC-nél</h1>
                            <p>Professzionális parkolási megoldások több mint 15 éve</p>
                            <span class="cp-btn">Szolgáltatásaink →</span>
                        </div>
                    </div>
                    <?php break;

                case 'text': ?>
                    <div class="cp-text">
                        <h2>Rólunk</h2>
                        <p>Cégünk 2008 óta foglalkozik parkoló rendszerek telepítésével és karbantartásával. Ügyfeleink között megtalálhatók bevásárlóközpontok, irodaházak és önkormányzatok egyaránt.</p>
                        <p>Célunk, hogy <strong>megbízható</strong> és <em>költséghatékony</em> megoldásokat kínáljunk minden partnerünknek.</p>
                    </div>
                    <?php break;

                case 'image_text': ?>
                    <div class="cp-image-text">
                        <div class="cp-image-text-img">
                            <div class="cp-placeholder-img">
                                <span>🖼️</span>
                                <small>Kép</small>
                            </div>
                        </div>
                        <div class="cp-image-text-content">
                            <h2>Sorompó rendszerek</h2>
                            <p>Modern sorompó rendszereink gyors és megbízható beléptetést biztosítanak. A rendszer automatikusan felismeri a jogosult járműveket és biztosítja a zökkenőmentes áthaladást.</p>
                        </div>
                    </div>
                    <?php break;

                case 'cards': ?>
                    <div class="cp-cards">
                        <h2>Szolgáltatásaink</h2>
                        <div class="cp-cards-grid">
                            <div class="cp-card">
                                <div class="cp-card-icon">🅿️</div>
                                <h3>Parkoló rendszerek</h3>
                                <p>Komplex parkolóház rendszerek tervezése és kivitelezése.</p>
                            </div>
                            <div class="cp-card">
                                <div class="cp-card-icon">🚧</div>
                                <h3>Sorompók</h3>
                                <p>Automata és félautomata sorompó rendszerek.</p>
                            </div>
                            <div class="cp-card">
                                <div class="cp-card-icon">🔑</div>
                                <h3>Beléptetés</h3>
                                <p>RFID, kártyás és rendszám-felismerő beléptetők.</p>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'cta': ?>
                    <div class="cp-cta">
                        <h2>Kérjen ingyenes árajánlatot!</h2>
                        <p>Vegye fel velünk a kapcsolatot és szakértőink személyre szabott megoldást dolgoznak ki.</p>
                        <span class="cp-btn-white">Kapcsolatfelvétel</span>
                    </div>
                    <?php break;

                case 'gallery': ?>
                    <div class="cp-gallery">
                        <h2>Referenciáink</h2>
                        <div class="cp-gallery-grid">
                            <?php for ($gi = 1; $gi <= 6; $gi++): ?>
                                <div class="cp-gallery-item">
                                    <span>🖼️</span>
                                    <small>Kép <?= $gi ?></small>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php break;

                case 'ticker': ?>
                    <div class="cp-ticker">
                        <div class="cp-ticker-track">
                            <span>📢 Új termék: Smart Parking rendszer elérhető!</span>
                            <span>⭐ 500+ elégedett ügyfél</span>
                            <span>🏆 Az év parkolási megoldása 2025</span>
                            <span>📞 Hívjon most: +36 1 234 5678</span>
                            <span>📢 Új termék: Smart Parking rendszer elérhető!</span>
                            <span>⭐ 500+ elégedett ügyfél</span>
                        </div>
                    </div>
                    <?php break;

                case 'accordion': ?>
                    <div class="cp-accordion">
                        <h2>Gyakran Ismételt Kérdések</h2>
                        <div class="cp-acc-item cp-acc-open">
                            <div class="cp-acc-header">
                                <span>Mennyi idő a telepítés?</span>
                                <span class="cp-acc-icon">−</span>
                            </div>
                            <div class="cp-acc-body">
                                <p>A telepítés mérettől függően 3–10 munkanap. Előtte helyszíni felmérést végzünk, ami ingyenes.</p>
                            </div>
                        </div>
                        <div class="cp-acc-item">
                            <div class="cp-acc-header">
                                <span>Van garancia a rendszerekre?</span>
                                <span class="cp-acc-icon">+</span>
                            </div>
                        </div>
                        <div class="cp-acc-item">
                            <div class="cp-acc-header">
                                <span>Milyen fizetési lehetőségek vannak?</span>
                                <span class="cp-acc-icon">+</span>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'video': ?>
                    <div class="cp-video">
                        <h2>Bemutató videó</h2>
                        <div class="cp-video-placeholder">
                            <div class="cp-play-btn">▶</div>
                            <p>YouTube / Vimeo videó</p>
                        </div>
                    </div>
                    <?php break;

                case 'divider': ?>
                    <div class="cp-divider">
                        <p style="text-align:center;color:#94A3B8;font-size:0.85rem;margin-bottom:0.75rem;">↑ Felette lévő szekció</p>
                        <div class="cp-divider-examples">
                            <div>
                                <small>Vonal</small>
                                <hr style="border:none;border-top:2px solid #D1DAE5;margin:0.5rem 0;">
                            </div>
                            <div>
                                <small>Pontok</small>
                                <div style="text-align:center;color:#6B7280;font-size:1.2rem;letter-spacing:0.5rem;margin:0.5rem 0;">•••</div>
                            </div>
                            <div>
                                <small>Hullám</small>
                                <svg viewBox="0 0 300 15" preserveAspectRatio="none" style="width:100%;height:15px;margin:0.5rem 0;">
                                    <path d="M0,8 Q38,0 75,8 T150,8 T225,8 T300,8 V15 H0 Z" fill="#D1DAE5"/>
                                </svg>
                            </div>
                            <div>
                                <small>Üres tér</small>
                                <div style="height:2rem;background:repeating-linear-gradient(45deg,transparent,transparent 5px,#F1F5F9 5px,#F1F5F9 10px);border-radius:4px;margin:0.5rem 0;"></div>
                            </div>
                        </div>
                        <p style="text-align:center;color:#94A3B8;font-size:0.85rem;margin-top:0.75rem;">↓ Alatta lévő szekció</p>
                    </div>
                    <?php break;

                case 'two_columns': ?>
                    <div class="cp-two-columns">
                        <h2>Két oszlopos elrendezés</h2>
                        <div class="cp-two-columns-grid">
                            <div class="cp-column">
                                <h3>Bal oszlop</h3>
                                <p>A bal oldali tartalom ide kerül. Támogatja a <strong>formázott szöveget</strong>, listákat és linkeket.</p>
                                <ul>
                                    <li>Első elem</li>
                                    <li>Második elem</li>
                                </ul>
                            </div>
                            <div class="cp-column">
                                <h3>Jobb oszlop</h3>
                                <p>A jobb oldali tartalom ide kerül. Mindkét oszlop önálló WYSIWYG szerkesztővel rendelkezik.</p>
                                <ul>
                                    <li>Harmadik elem</li>
                                    <li>Negyedik elem</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'testimonials': ?>
                    <div class="cp-testimonials">
                        <h2>Ügyfeleink mondták</h2>
                        <div class="cp-testimonials-grid">
                            <div class="cp-testimonial">
                                <p class="cp-testimonial-text">&ldquo;Kiváló munkát végeztek, a rendszer tökéletesen működik!&rdquo;</p>
                                <div class="cp-testimonial-author">
                                    <div class="cp-avatar">K</div>
                                    <div>
                                        <strong>Kovács János</strong>
                                        <small>Ügyvezető, ABC Kft.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="cp-testimonial">
                                <p class="cp-testimonial-text">&ldquo;Gyors telepítés, profi csapat, maximálisan ajánlom!&rdquo;</p>
                                <div class="cp-testimonial-author">
                                    <div class="cp-avatar">N</div>
                                    <div>
                                        <strong>Nagy Éva</strong>
                                        <small>Ingatlankezelő</small>
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
                                <span class="cp-stat-label">Elégedett ügyfél</span>
                            </div>
                            <div class="cp-stat">
                                <span class="cp-stat-number">15+</span>
                                <span class="cp-stat-label">Év tapasztalat</span>
                            </div>
                            <div class="cp-stat">
                                <span class="cp-stat-number">1200+</span>
                                <span class="cp-stat-label">Telepített rendszer</span>
                            </div>
                            <div class="cp-stat">
                                <span class="cp-stat-number">24/7</span>
                                <span class="cp-stat-label">Ügyfélszolgálat</span>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'page_list': ?>
                    <div class="cp-page-list">
                        <h2>Legújabb cikkeink</h2>
                        <div class="cp-page-list-grid">
                            <div class="cp-page-card">
                                <div class="cp-page-card-img"><span>🖼️</span></div>
                                <div class="cp-page-card-content">
                                    <h3>Hogyan válasszunk parkoló rendszert?</h3>
                                    <p>A megfelelő parkoló rendszer kiválasztása nem egyszerű feladat. Cikkünkben segítünk...</p>
                                    <small>2026. 02. 15.</small>
                                </div>
                            </div>
                            <div class="cp-page-card">
                                <div class="cp-page-card-img"><span>🖼️</span></div>
                                <div class="cp-page-card-content">
                                    <h3>RFID vs. rendszámfelismerés</h3>
                                    <p>Összehasonlítjuk a két legelterjedtebb beléptető technológiát...</p>
                                    <small>2026. 01. 20.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'map': ?>
                    <div class="cp-map">
                        <h2>Elérhetőségünk</h2>
                        <div class="cp-map-placeholder">
                            <span>📍</span>
                            <p>Google Maps térkép</p>
                            <small>Beágyazott, interaktív térkép a megadott címmel</small>
                        </div>
                    </div>
                    <?php break;

                case 'contact_form': ?>
                    <div class="cp-contact">
                        <h2>Kapcsolatfelvétel</h2>
                        <div class="cp-form-grid">
                            <div class="cp-form-field">
                                <label>Név *</label>
                                <div class="cp-input">Az Ön neve</div>
                            </div>
                            <div class="cp-form-field">
                                <label>E-mail *</label>
                                <div class="cp-input">pelda@email.hu</div>
                            </div>
                            <div class="cp-form-field">
                                <label>Telefon</label>
                                <div class="cp-input">+36 ...</div>
                            </div>
                            <div class="cp-form-field cp-full">
                                <label>Üzenet *</label>
                                <div class="cp-textarea">Írja le üzenetét...</div>
                            </div>
                        </div>
                        <span class="cp-btn">Üzenet küldése</span>
                    </div>
                    <?php break;

                case 'keywords_cloud': ?>
                    <div class="cp-keywords">
                        <h2>Kulcsszavak</h2>
                        <div class="cp-keywords-cloud">
                            <?php
                            $demoKeywords = [
                                'parkoló' => 2.2, 'sorompó' => 1.8, 'beléptető rendszer' => 1.5,
                                'RFID' => 1.2, 'rendszámfelismerés' => 1.6, 'parkolóház' => 1.3,
                                'karbantartás' => 1.0, 'munkaidő nyilvántartás' => 1.4,
                                'automata' => 0.9, 'biztonság' => 1.1, 'telepítés' => 1.7,
                                'garancia' => 0.95, 'szerviz' => 1.05, 'modern' => 0.85,
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
                            <h1 style="font-size:1.4rem;">Parkoló ABC</h1>
                            <p style="font-size:0.8rem;">Automatikus diavetítés a kiemelt képekből</p>
                            <span class="cp-btn" style="font-size:0.75rem;padding:0.3rem 0.75rem;">Szolgáltatásaink →</span>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.25rem;margin-top:0.5rem;max-width:200px;width:100%;">
                                <span style="grid-column:span 2;background:rgba(255,255,255,0.15);backdrop-filter:blur(4px);color:#fff;padding:0.3rem;border-radius:4px;font-size:0.6rem;text-align:center;border:1px solid rgba(255,255,255,0.25);">Fizetős parkoló</span>
                                <span style="background:rgba(255,255,255,0.15);backdrop-filter:blur(4px);color:#fff;padding:0.3rem;border-radius:4px;font-size:0.55rem;text-align:center;border:1px solid rgba(255,255,255,0.25);">Beléptető</span>
                                <span style="background:rgba(255,255,255,0.15);backdrop-filter:blur(4px);color:#fff;padding:0.3rem;border-radius:4px;font-size:0.55rem;text-align:center;border:1px solid rgba(255,255,255,0.25);">Vegyes</span>
                            </div>
                        </div>
                    </div>
                    <?php break;

                case 'product_grid': ?>
                    <div style="padding:1.5rem;">
                        <h2 style="text-align:center;margin-bottom:1rem;">Termékek</h2>
                        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:0.5rem;">
                            <?php
                            $products = ['Sorompó', 'Automata', 'Jegykiadó', 'RFID olvasó', 'Kamera'];
                            foreach ($products as $p): ?>
                                <div style="aspect-ratio:1;background:#f1f5f9;border-radius:6px;display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:0.7rem;position:relative;overflow:hidden;cursor:pointer;" class="cp-pg-demo">
                                    <span style="font-size:1.5rem;">📦</span>
                                    <span style="font-size:0.65rem;font-weight:600;margin-top:0.25rem;"><?= $h($p) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p style="text-align:center;font-size:0.7rem;color:#64748b;margin-top:0.75rem;">← Kurzor ráhúzáskor a leírás jelenik meg →</p>
                    </div>
                    <?php break;

                case 'seo_hidden': ?>
                    <div style="padding:1.5rem;">
                        <details style="max-width:600px;margin:0 auto;">
                            <summary style="cursor:pointer;padding:0.6rem 1rem;background:#E8F4FF;border-radius:6px;font-weight:600;color:#0067FF;list-style:none;display:flex;align-items:center;gap:0.4rem;">
                                <span style="transition:transform 0.2s;font-size:0.7rem;">▸</span> Tovább olvasom...
                            </summary>
                            <div style="padding:1rem;font-size:0.85rem;line-height:1.8;color:#475569;">
                                <p>Ez a szöveg a Google keresőrobotok számára látható és indexelhető, de a weboldalon alapértelmezetten el van rejtve. Csak akkor jelenik meg, ha a látogató rákattint a gombra.</p>
                                <p>Ideális hirdetési szövegek, kulcsszavak és helyi SEO szövegek elhelyezésére.</p>
                            </div>
                        </details>
                    </div>
                    <?php break;

                case 'link_banner': ?>
                    <div style="background:#0067FF;padding:1rem 2rem;">
                        <div style="display:flex;align-items:center;justify-content:center;gap:0.75rem;color:#fff;">
                            <span style="font-size:1.25rem;">📖</span>
                            <span style="font-weight:600;font-size:0.95rem;">Alapfogalmak és jelmagyarázat — Parkolórendszer szakkifejezések</span>
                            <span style="font-size:1.1rem;">→</span>
                        </div>
                    </div>
                    <?php break;

                case 'reference_gallery': ?>
                    <div style="padding:1.5rem;">
                        <h2 style="text-align:center;margin-bottom:1rem;">Referenciáink</h2>
                        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.75rem;">
                            <?php
                            $refs = ['Bevásárlóközpont', 'Irodaház', 'Kórház', 'Gyár'];
                            foreach ($refs as $r): ?>
                                <div style="aspect-ratio:4/3;background:linear-gradient(135deg,#e2e8f0,#cbd5e1);border-radius:6px;position:relative;overflow:hidden;">
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:1.5rem;">🏢</div>
                                    <div style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(transparent,rgba(15,23,42,0.8));color:#fff;padding:1.5rem 0.5rem 0.4rem;font-size:0.7rem;font-weight:600;"><?= $h($r) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p style="text-align:center;font-size:0.7rem;color:#64748b;margin-top:0.75rem;">← Kurzor ráhúzáskor a projekt részletképei jelennek meg →</p>
                    </div>
                    <?php break;

                case 'sitemap': ?>
                    <div style="padding:1.5rem;">
                        <h2 style="text-align:center;margin-bottom:1rem;">Oldaltérkép</h2>
                        <div style="max-width:400px;margin:0 auto;">
                            <ul style="list-style:none;padding:0;font-size:0.85rem;line-height:2;">
                                <li><strong style="color:#0067FF;">Főoldal</strong></li>
                                <li><strong style="color:#0067FF;">Szolgáltatásaink</strong>
                                    <ul style="list-style:none;padding-left:1.25rem;border-left:2px solid #e2e8f0;margin-left:0.4rem;">
                                        <li style="color:#364151;">Fizetős parkoló rendszer</li>
                                        <li style="color:#364151;">Beléptető rendszer</li>
                                        <li style="color:#364151;">Vegyes parkoló rendszer</li>
                                    </ul>
                                </li>
                                <li><strong style="color:#0067FF;">Termékek</strong></li>
                                <li><strong style="color:#0067FF;">Referenciák</strong></li>
                                <li><strong style="color:#0067FF;">Kapcsolat</strong></li>
                            </ul>
                        </div>
                        <p style="text-align:center;font-size:0.7rem;color:#64748b;margin-top:0.75rem;">Automatikusan generált, hierarchikus oldaltérkép</p>
                    </div>
                    <?php break;

                case 'tudasmorzsak': ?>
                    <div style="padding:1.5rem;background:#E7F6FF;">
                        <h2 style="text-align:center;margin-bottom:1rem;color:#0F172A;">Tudásmorzsák</h2>
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.75rem;">
                            <?php
                            $morzsak = [
                                ['Sorompó', 'Fizikai akadály, amely a járművek be- és kilépését szabályozza.'],
                                ['RFID', 'Rádiófrekvenciás azonosítás — érintés nélküli beléptető technológia.'],
                                ['Parkoló automata', 'Jegykiadó és fizetési terminál kombinációja.'],
                            ];
                            foreach ($morzsak as $m): ?>
                                <div style="background:#fff;border-radius:6px;padding:0.75rem;border:1px solid #D1DAE5;">
                                    <h3 style="font-size:0.85rem;font-weight:700;color:#0F172A;margin-bottom:0.25rem;"><?= $h($m[0]) ?></h3>
                                    <p style="font-size:0.75rem;color:#364151;line-height:1.4;margin:0;"><?= $h($m[1]) ?></p>
                                    <span style="font-size:0.7rem;color:#0067FF;font-weight:600;margin-top:0.35rem;display:inline-block;">Tovább →</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p style="text-align:center;font-size:0.7rem;color:#64748b;margin-top:0.75rem;">← Jobbról beúszó, randomizált sorrend →</p>
                    </div>
                    <?php break;

            endswitch; ?>
        </div>
    </div>

    <div class="component-fields">
        <strong>Szerkeszthető mezők:</strong>
        <ul>
            <?php foreach (($sectionFields[$type] ?? []) as $field): ?>
                <li><?= $h($field) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endforeach; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
