<?php
// Admin — Média kezelő oldal (admin.media.*).
return [
    // ── Feltöltési hibák / üzenetek ──
    'admin.media.err_upload'        => 'Feltöltési hiba. Kérjük próbálja újra.',
    'admin.media.err_too_large'     => 'A fájl túl nagy. Maximum méret: 10 MB.',
    'admin.media.err_type'          => 'Nem engedélyezett fájltípus. Csak JPEG, PNG, GIF és WebP engedélyezett.',
    'admin.media.msg_uploaded'      => 'Kép sikeresen feltöltve.',
    'admin.media.msg_deleted'       => 'Kép törölve.',
    'admin.media.msg_saved'         => 'Mentve.',
    'admin.media.msg_done'          => 'Kész.',

    // ── Böngésző / választó mód ──
    'admin.media.browser_title'     => 'Média böngésző',
    'admin.media.pick_heading'      => 'Kép kiválasztása',
    'admin.media.upload_new'        => 'Új kép feltöltése',
    'admin.media.alt_text'          => 'Alt szöveg',
    'admin.media.alt_placeholder'   => 'Kép leírása',
    'admin.media.empty_browse'      => 'Nincs feltöltött média. Töltsön fel képeket a fenti űrlappal.',

    // ── Oldal fejléc ──
    'admin.media.heading'           => 'Média',
    'admin.media.help'              => '<strong>🖼️ Képek kezelése a weboldalhoz.</strong> Töltsön fel képeket, amelyeket a szekciókban használhat (hero háttér, galéria, kép+szöveg, stb.). A képek a szerkesztőben a „Tallózás" gombbal választhatók ki.',
    'admin.media.help_toggle'       => 'Fontos tudnivalók',
    'admin.media.help_formats'      => '<strong>Engedélyezett formátumok:</strong> JPEG, PNG, GIF, WebP. Maximum méret: 10 MB.',
    'admin.media.help_alt'          => '<strong>Alt szöveg (nagyon fontos!):</strong> Írja le röviden, mit ábrázol a kép. Ez két dologért fontos:',
    'admin.media.help_alt_seo'      => '<em>SEO:</em> A Google a képkeresésben az alt szöveg alapján rangsorol.',
    'admin.media.help_alt_a11y'     => '<em>Akadálymentesség:</em> Képernyőolvasót használó látogatók ezt hallják a kép helyett.',
    'admin.media.help_size'         => '<strong>Képméret tipp:</strong> Hero háttérképhez legalább 1920×1080 px javasolt. Galéria képekhez 800×600 px elegendő.',
    'admin.media.help_filename'     => '<strong>Fájlnév:</strong> A rendszer automatikusan biztonságos fájlnevet generál, nem kell vele foglalkozni.',

    // ── Feltöltési űrlap ──
    'admin.media.legend_upload'     => 'Új kép feltöltése',
    'admin.media.field_file'        => 'Képfájl',
    'admin.media.field_alt_seo'     => 'Alt szöveg (SEO)',
    'admin.media.recommended'       => '*ajánlott',
    'admin.media.alt_placeholder_seo' => 'A kép leírása keresőmotoroknak',
    'admin.media.alt_warning'       => '⚠ Az alt szöveg fontos a Google képkereséshez és akadálymentességhez.',
    'admin.media.featured_label'    => '⭐ Kiemelt kép (megjelenik a főoldali diavetítésben)',

    // ── Média rács ──
    'admin.media.alt_placeholder_short' => 'Alt szöveg',
    'admin.media.featured_short'    => '⭐ Kiemelt',
    'admin.media.confirm_delete'    => 'Biztosan törli?',
    'admin.media.empty'             => 'Nincs még feltöltött média.',
];
