<?php
// Admin — Menü kezelő oldal (admin.menus.*).
return [
    // ── Üzenetek ──
    'admin.menus.msg_added'         => 'Menüpont hozzáadva.',
    'admin.menus.msg_saved'         => 'Mentve.',
    'admin.menus.msg_deleted'       => 'Menüpont törölve.',
    'admin.menus.msg_done'          => 'Kész.',

    // ── Oldal fejléc ──
    'admin.menus.heading'           => 'Menü kezelése',
    'admin.menus.help'              => '<strong>🧭 A navigációs menü a weboldal fejlécében jelenik meg.</strong> A látogatók innen navigálnak az oldalak között. A menüpontok lehetnek felső szintű (mindig látható) vagy almenüpontok (legördülő menüben jelennek meg).',
    'admin.menus.help_toggle'       => 'Hogyan működik?',
    'admin.menus.help_label'        => '<strong>Címke:</strong> A menüpont megjelenő neve a fejlécben (pl. „Szolgáltatásaink").',
    'admin.menus.help_page'         => '<strong>Oldal kiválasztása:</strong> Ha egy meglévő oldalhoz köti, az URL automatikusan beáll. Ez az ajánlott módszer!',
    'admin.menus.help_url'          => '<strong>Egyedi URL:</strong> Ha nem oldalhoz köti, adjon meg egyedi URL-t (pl. külső link: <code>https://example.com</code>).',
    'admin.menus.help_parent'       => '<strong>Szülő menüpont:</strong> Ha beállít szülőt, a menüpont legördülő almenüben jelenik meg az adott szülő alatt.',
    'admin.menus.help_order'        => '<strong>Sorrend:</strong> A ▲/▼ gombokkal változtathatja a menüpontok sorrendjét. A sorrend azonos szinten belül érvényes.',
    'admin.menus.help_delete'       => '<strong>Törlés:</strong> Ha egy szülő menüpontot töröl, az almenüpontjai felső szintűvé válnak.',

    // ── Jelenlegi menüszerkezet táblázat ──
    'admin.menus.structure_heading' => 'Jelenlegi menüszerkezet',
    'admin.menus.col_label'         => 'Címke',
    'admin.menus.col_url'           => 'URL',
    'admin.menus.col_parent'        => 'Szülő',
    'admin.menus.col_page'          => 'Oldal',
    'admin.menus.confirm_delete'    => 'Biztosan törli?',
    'admin.menus.empty'             => 'Nincs menüpont.',

    // ── Szerkesztő űrlap ──
    'admin.menus.legend_edit'       => 'Menüpont szerkesztése: {label}',
    'admin.menus.field_label'       => 'Címke',
    'admin.menus.field_url'         => 'Egyedi URL (ha nincs oldalhoz kötve)',
    'admin.menus.url_placeholder'   => '/oldal-neve',
    'admin.menus.field_page'        => 'Oldal kiválasztása (opcionális)',
    'admin.menus.option_no_page'    => '– Nincs (egyedi URL) –',
    'admin.menus.field_parent'      => 'Szülő menüpont (legördülőhöz)',
    'admin.menus.option_top_level'  => '– Felső szint –',

    // ── Hozzáadás űrlap ──
    'admin.menus.legend_add'        => 'Új menüpont hozzáadása',
    'admin.menus.tooltip_help'      => 'Segítség',
    'admin.menus.tooltip_label'     => 'A menüpont szövege, ami a navigációs sávban megjelenik. Legyen rövid és érthető (pl. „Szolgáltatásaink", „Kapcsolat").',
    'admin.menus.tooltip_url'       => 'Csak akkor kell kitölteni, ha nem oldalt választ alább. Használja külső linkekhez (pl. https://facebook.com) vagy speciális útvonalakhoz.',
    'admin.menus.tooltip_page'      => 'Ha egy meglévő oldalhoz köti a menüpontot, az URL automatikusan az oldal slug-jára áll be. Ez az ajánlott módszer!',
    'admin.menus.tooltip_parent'    => 'Ha szülőt választ, ez a menüpont almenüként (legördülő menüben) jelenik meg a kiválasztott szülő alatt. „Felső szint" = önálló fő menüpont.',
    'admin.menus.label_placeholder' => 'pl. Szolgáltatások',
    'admin.menus.url_placeholder_add' => '/oldal-neve vagy https://...',
];
