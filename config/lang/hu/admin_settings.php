<?php
// Admin — Site Settings page (admin.settings.*) and API endpoints (admin.api.*).
return [
    // ── Page chrome ──
    'admin.settings.heading'        => 'Beállítások',
    'admin.settings.help'           => '<strong>⚙️ Weboldal globális beállítások.</strong> Ezek az értékek az egész weboldalra hatással vannak: fejléc, lábléc, Google keresési eredmények, közösségi média megosztás és az arculat. A módosítások a mentés után azonnal érvénybe lépnek.',
    'admin.settings.msg_saved'      => 'Beállítások mentve.',
    'admin.settings.legend_general' => 'Általános',
    'admin.settings.legend_smtp'    => '📧 E-mail értesítések (SMTP)',
    'admin.settings.smtp_help'      => 'Az űrlapon keresztül érkező üzenetek automatikusan mentésre kerülnek az adatbázisba. Ha szeretne e-mail értesítést is kapni, töltse ki az alábbi SMTP beállításokat. A tárhelyszolgáltatónál először hozzon létre egy e-mail fiókot a domainhez.',
    'admin.settings.tooltip_help'   => 'Segítség',
    'admin.settings.smtp_test_btn'  => '📧 Teszt e-mail küldése',
    'admin.settings.smtp_sending'   => 'Küldés...',
    'admin.settings.smtp_network_error' => 'Hálózati hiba',

    // ── General fields: labels ──
    'admin.settings.field.site_name.label'         => 'Weboldal neve',
    'admin.settings.field.site_tagline.label'      => 'Szlogen',
    'admin.settings.field.meta_description.label'  => 'Alapértelmezett meta leírás',
    'admin.settings.field.meta_keywords.label'     => 'Alapértelmezett kulcsszavak',
    'admin.settings.field.contact_email.label'     => 'E-mail cím',
    'admin.settings.field.contact_phone.label'     => 'Telefonszám',
    'admin.settings.field.contact_address.label'   => 'Cím',
    'admin.settings.field.primary_color.label'     => 'Elsődleges szín',
    'admin.settings.field.secondary_color.label'   => 'Másodlagos szín',
    'admin.settings.field.facebook_url.label'      => 'Facebook URL',
    'admin.settings.field.og_default_image.label'  => 'Alapértelmezett OG kép',
    'admin.settings.field.logo_url.label'          => 'Logo URL',
    'admin.settings.field.logo_display_mode.label' => 'Logo megjelenítés módja',

    // ── General fields: hints ──
    'admin.settings.field.site_name.hint'         => 'Megjelenik a fejlécben, láblécben, böngésző fülön és a Google találatokban. Ez a weboldal „márkaneve".',
    'admin.settings.field.site_tagline.hint'      => 'Rövid mottó, ami a weboldal neve mellett jelenhet meg. Pl. „Megbízható megoldások vállalkozásának".',
    'admin.settings.field.meta_description.hint'  => 'Ez a leírás jelenik meg a Google találatoknál, ha egy oldalnak nincs saját meta leírása. Max. 160 karakter ajánlott.',
    'admin.settings.field.meta_keywords.hint'     => 'Vesszővel elválasztott kulcsszavak, amelyek az oldalak alapértelmezett kulcsszavai lesznek, ha nem adnak meg sajátot.',
    'admin.settings.field.contact_email.hint'     => 'A weboldalon megjelenő kapcsolattartási e-mail. A kapcsolati űrlap ide küld értesítést.',
    'admin.settings.field.contact_phone.hint'     => 'A fejlécben és láblécben megjelenő telefonszám. Formátum: +36 1 234 5678.',
    'admin.settings.field.contact_address.hint'   => 'A cég fizikai címe. Megjelenik a láblécben és a Google strukturált adatokban.',
    'admin.settings.field.primary_color.hint'     => 'A weboldal fő színe: gombok, linkek, kiemelések. A weoldal arculatát határozza meg.',
    'admin.settings.field.secondary_color.hint'   => 'Kiegészítő szín háttérelemekhez és másodlagos kiemelésekhez.',
    'admin.settings.field.facebook_url.hint'      => 'A cég Facebook oldalának teljes URL-je. Megjelenik a láblécben és a JSON-LD strukturált adatokban.',
    'admin.settings.field.og_default_image.hint'  => 'Ez a kép jelenik meg, ha valaki megosztja az oldalt Facebookon vagy más közösségi médiában, és nincs saját kiemelt kép beállítva.',
    'admin.settings.field.logo_url.hint'          => 'A cég logójának URL-je. Megjelenik a fejlécben a weboldal neve mellett vagy helyett (a megjelenítési mód lentebb állítható). Töltsön fel egy logó képet a Média oldalon, majd válassza ki a „Tallózás" gombbal.',
    'admin.settings.field.logo_display_mode.hint' => 'Határozza meg, hogyan jelenjen meg a logo a fejlécben. „Nincs" = csak a weboldal neve jelenik meg. „Logo helyettesíti a nevet" = csak a logókép jelenik meg. „Logo a név mellett" = a logókép és a weboldal neve egymás mellett.',

    // ── logo_display_mode options ──
    'admin.settings.field.logo_display_mode.option.none'    => 'Nincs (csak a weboldal neve)',
    'admin.settings.field.logo_display_mode.option.replace' => 'Logo helyettesíti a nevet',
    'admin.settings.field.logo_display_mode.option.beside'  => 'Logo a név mellett',

    // ── SMTP fields: labels ──
    'admin.settings.field.smtp_host.label' => 'SMTP szerver',
    'admin.settings.field.smtp_port.label' => 'SMTP port',
    'admin.settings.field.smtp_user.label' => 'SMTP felhasználónév',
    'admin.settings.field.smtp_pass.label' => 'SMTP jelszó',
    'admin.settings.field.smtp_from.label' => 'Feladó e-mail cím',
    'admin.settings.field.smtp_to.label'   => 'Értesítési e-mail',

    // ── SMTP fields: hints ──
    'admin.settings.field.smtp_host.hint' => 'Az SMTP szerver címe, pl. smtp.example.com. Ezt a tárhelyszolgáltatótól kapja.',
    'admin.settings.field.smtp_port.hint' => 'Az SMTP port száma. Általában 587 (STARTTLS) vagy 465 (SSL). Alapértelmezett: 587.',
    'admin.settings.field.smtp_user.hint' => 'Az e-mail fiók felhasználóneve (általában maga az e-mail cím), pl. info@example.com.',
    'admin.settings.field.smtp_pass.hint' => 'Az e-mail fiók jelszava. Biztonságosan tárolva az adatbázisban.',
    'admin.settings.field.smtp_from.hint' => 'A „Feladó" mező értéke a küldött e-mailekben. Ha üres, az SMTP felhasználónév lesz használva.',
    'admin.settings.field.smtp_to.hint'   => 'Erre az e-mail címre érkeznek az űrlap-értesítések. Ha üres, az SMTP felhasználónév lesz használva.',

    // ── API endpoints (admin.api.*) ──
    'admin.api.invalid_csrf'      => 'Érvénytelen CSRF token',
    'admin.api.missing_smtp'      => 'Hiányzó SMTP beállítások. Először mentse el az összes mezőt.',
    'admin.api.test_subject'      => 'Teszt e-mail — Section CMS',
    'admin.api.test_body'         => '<h2>✅ Ez egy teszt e-mail</h2><p>Ha ezt a levelet megkapta, az SMTP beállítások helyesek.</p><p><small>Küldve: {datetime}</small></p>',
    'admin.api.test_altbody'      => "Teszt e-mail\nHa ezt a levelet megkapta, az SMTP beállítások helyesek.\nKüldve: {datetime}",
    'admin.api.test_sent'         => 'Teszt e-mail elküldve ({to})',
    'admin.api.send_error'        => 'Küldési hiba: {error}',
];
