#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Generates three Hungarian DOCX documents for the ParkoloABC project:
1. Felhasznaloi Kezikonyv (User Guide)
2. Fejlesztoi Dokumentacio (Developer Documentation)
3. Funkciolista (Feature List)
"""

from docx import Document
from docx.shared import Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
import os

OUTPUT_DIR = os.path.dirname(os.path.abspath(__file__))

# Hungarian open/close quote helper
OQ = '\u00ab'  # <<
CQ = '\u00bb'  # >>


def q(text):
    """Wrap text in Hungarian-style quotes."""
    return f'{OQ}{text}{CQ}'


def setup_styles(doc):
    style = doc.styles['Normal']
    font = style.font
    font.name = 'Calibri'
    font.size = Pt(11)
    style.paragraph_format.space_after = Pt(6)
    for level in range(1, 5):
        h = doc.styles[f'Heading {level}']
        h.font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)
    return doc


def add_cover_page(doc, title, subtitle, version="1.0"):
    for _ in range(6):
        doc.add_paragraph()
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run(title)
    run.font.size = Pt(28)
    run.font.bold = True
    run.font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)

    p2 = doc.add_paragraph()
    p2.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run2 = p2.add_run(subtitle)
    run2.font.size = Pt(14)
    run2.font.color.rgb = RGBColor(0x64, 0x74, 0x8B)

    doc.add_paragraph()
    p3 = doc.add_paragraph()
    p3.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run3 = p3.add_run(f'Verzi\u00f3: {version}')
    run3.font.size = Pt(11)
    run3.font.color.rgb = RGBColor(0x94, 0xA3, 0xB8)

    p4 = doc.add_paragraph()
    p4.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run4 = p4.add_run('www.parkoloabc.hu')
    run4.font.size = Pt(11)
    run4.font.color.rgb = RGBColor(0x00, 0x67, 0xFF)

    doc.add_page_break()


def add_table(doc, headers, rows):
    table = doc.add_table(rows=1, cols=len(headers))
    table.style = 'Light Grid Accent 1'
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    for i, h in enumerate(headers):
        cell = table.rows[0].cells[i]
        cell.text = h
        for paragraph in cell.paragraphs:
            for run in paragraph.runs:
                run.font.bold = True
                run.font.size = Pt(10)
    for row_data in rows:
        row = table.add_row()
        for i, val in enumerate(row_data):
            row.cells[i].text = str(val)
            for paragraph in row.cells[i].paragraphs:
                for run in paragraph.runs:
                    run.font.size = Pt(10)
    doc.add_paragraph()
    return table


def add_tip(doc, text, prefix='\U0001f4a1 Tipp:'):
    p = doc.add_paragraph()
    run = p.add_run(f'{prefix} ')
    run.font.bold = True
    run.font.color.rgb = RGBColor(0x00, 0x67, 0xFF)
    run2 = p.add_run(text)
    run2.font.italic = True
    run2.font.color.rgb = RGBColor(0x47, 0x55, 0x69)


# =====================================================================
# 1. USER GUIDE
# =====================================================================
def generate_user_guide():
    doc = Document()
    setup_styles(doc)
    add_cover_page(doc, 'Parkol\u00f3ABC.hu', 'Felhaszn\u00e1l\u00f3i K\u00e9zik\u00f6nyv', '1.0')

    doc.add_heading('Tartalomjegyz\u00e9k', level=1)
    toc = [
        '1. Bevezet\u00e9s',
        '2. Bejelentkez\u00e9s az admin fel\u00fcletre',
        '3. Ir\u00e1ny\u00edt\u00f3pult (Dashboard)',
        '4. Oldalak kezel\u00e9se',
        '5. Szekci\u00f3k (tartalomblokkok) \u2014 mind a 24 t\u00edpus r\u00e9szletesen',
        '6. M\u00e9dia (k\u00e9pek kezel\u00e9se)',
        '7. Men\u00fc szerkeszt\u00e9se',
        '8. Be\u00e1ll\u00edt\u00e1sok',
        '9. E-mail \u00e9rtes\u00edt\u00e9sek be\u00e1ll\u00edt\u00e1sa',
        '10. \u00dczenetek',
        '11. Jelsz\u00f3 m\u00f3dos\u00edt\u00e1sa',
        '12. Weboldal friss\u00edt\u00e9se (telep\u00edt\u00e9s)',
        '13. Gyakran Ism\u00e9telt K\u00e9rd\u00e9sek',
    ]
    for item in toc:
        doc.add_paragraph(item)
    doc.add_page_break()

    # 1. Intro
    doc.add_heading('1. Bevezet\u00e9s', level=1)
    doc.add_paragraph(
        '\u00dcdv\u00f6z\u00f6lj\u00fck a Parkol\u00f3ABC.hu weboldal kezel\u00e9si \u00fatmutat\u00f3j\u00e1ban! '
        'Ez a dokumentum l\u00e9p\u00e9sr\u0151l l\u00e9p\u00e9sre bemutatja, hogyan haszn\u00e1lhatja a weboldal '
        'adminisztr\u00e1ci\u00f3s fel\u00fclet\u00e9t tartalom szerkeszt\u00e9s\u00e9re, k\u00e9pek felt\u00f6lt\u00e9s\u00e9re, '
        'men\u00fcrendszer kezel\u00e9s\u00e9re \u00e9s a weboldal be\u00e1ll\u00edt\u00e1sok m\u00f3dos\u00edt\u00e1s\u00e1ra.'
    )
    doc.add_paragraph(
        'A weboldal egy egyedi fejleszt\u00e9s\u0171 tartalomkezel\u0151 rendszert (CMS) haszn\u00e1l, '
        'amelyet kifejezetten az \u00d6n ig\u00e9nyeire tervezt\u00fcnk. '
        'Nem sz\u00fcks\u00e9ges semmilyen programoz\u00e1si vagy informatikai tud\u00e1s a haszn\u00e1lat\u00e1hoz.'
    )
    doc.add_heading('Hogyan \u00e9rhet\u0151 el a weboldal?', level=2)
    add_table(doc, ['C\u00edm', 'Le\u00edr\u00e1s'], [
        ['https://www.parkoloabc.hu/', 'A nyilv\u00e1nos weboldal (amit a l\u00e1togat\u00f3k l\u00e1tnak)'],
        ['https://www.parkoloabc.hu/admin/', 'Az adminisztr\u00e1ci\u00f3s fel\u00fclet (kezel\u0151panel)'],
    ])

    # 2. Login
    doc.add_heading('2. Bejelentkez\u00e9s az admin fel\u00fcletre', level=1)
    doc.add_paragraph(
        'Nyissa meg a b\u00f6ng\u00e9sz\u0151j\u00e9ben a https://www.parkoloabc.hu/admin/ c\u00edmet. '
        'Megjelenik egy bejelentkez\u00e9si oldal, ahol adja meg a felhaszn\u00e1l\u00f3nev\u00e9t \u00e9s jelsz\u00e1v\u00e1t.'
    )
    doc.add_paragraph('A bejelentkez\u00e9s ut\u00e1n az ir\u00e1ny\u00edt\u00f3pult jelenik meg.')
    add_tip(doc, 'Ha elfelejtette a jelszav\u00e1t, vegye fel a kapcsolatot a weboldal fejleszt\u0151j\u00e9vel.')
    add_tip(doc, 'A munkamenet (session) 24 \u00f3ra inaktivit\u00e1s ut\u00e1n lej\u00e1r, ekkor \u00fajra be kell jelentkeznie.', '\u26a0\ufe0f Figyelem:')

    # 3. Dashboard
    doc.add_heading('3. Ir\u00e1ny\u00edt\u00f3pult (Dashboard)', level=1)
    doc.add_paragraph('A bejelentkez\u00e9s ut\u00e1n az ir\u00e1ny\u00edt\u00f3pult jelenik meg, amely \u00f6sszefoglalja a weboldal legfontosabb adatait:')
    for item in [
        'Oldalak sz\u00e1ma (publik\u00e1lt \u00e9s v\u00e1zlat)',
        'Szekci\u00f3k (tartalomblokkok) sz\u00e1ma',
        'Felt\u00f6lt\u00f6tt m\u00e9diaf\u00e1jlok (k\u00e9pek) sz\u00e1ma',
        'Men\u00fcelemek sz\u00e1ma',
        'Olvasatlan \u00fczenetek sz\u00e1ma',
    ]:
        doc.add_paragraph(item, style='List Bullet')
    doc.add_paragraph('Minden k\u00e1rty\u00e1ra kattintva a megfelel\u0151 kezel\u0151oldalra juthat. A bal oldali men\u00fcs\u00e1vban is megtal\u00e1lhat\u00f3 minden funkci\u00f3.')

    # 4. Pages
    doc.add_heading('4. Oldalak kezel\u00e9se', level=1)
    doc.add_heading('4.1 Oldal lista', level=2)
    doc.add_paragraph(
        f'A bal men\u00fcben kattintson az {q("Oldalak")} men\u00fcpontra. Megjelenik az \u00f6sszes oldal list\u00e1ja, '
        't\u00e1bl\u00e1zatos form\u00e1ban: c\u00edm, URL c\u00edm (slug), \u00e1llapot \u00e9s m\u0171veletek.'
    )
    doc.add_heading('4.2 \u00daj oldal l\u00e9trehoz\u00e1sa', level=2)
    doc.add_paragraph(f'Kattintson az {q("\u00daj oldal")} gombra. A megjelen\u0151 \u0171rlapon t\u00f6ltse ki:')
    for item in [
        f'Oldal c\u00edme \u2014 ez jelenik meg a b\u00f6ng\u00e9sz\u0151 f\u00fcl\u00f6n \u00e9s a keres\u0151ben',
        f'Slug (URL c\u00edm) \u2014 az oldal el\u00e9r\u00e9si \u00fatja, pl. {q("szolgaltatasaink")} \u2192 parkoloabc.hu/szolgaltatasaink',
        f'\u00c1llapot \u2014 {q("Publik\u00e1lt")} (l\u00e1that\u00f3 a weboldalon) vagy {q("V\u00e1zlat")} (nem l\u00e1that\u00f3)',
        'Sorrend \u2014 az oldalak megjelen\u00e9si sorrendje a list\u00e1kban',
    ]:
        doc.add_paragraph(item, style='List Bullet')
    add_tip(doc, f'A slug csak kisbet\u0171ket, sz\u00e1mokat \u00e9s k\u00f6t\u0151jelet tartalmazhat, \u00e9kezet n\u00e9lk\u00fcl. Pl. {q("szolgaltatasaink")} \u00e9s nem {q("Szolg\u00e1ltat\u00e1saink")}.')

    doc.add_heading('4.3 SEO be\u00e1ll\u00edt\u00e1sok (keres\u0151-optimaliz\u00e1l\u00e1s)', level=2)
    doc.add_paragraph('Minden oldalhoz be\u00e1ll\u00edthat\u00f3k SEO mez\u0151k, amelyek seg\u00edtik, hogy az oldal jobb helyen jelenjen meg a Google keres\u0151ben:')
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'Aj\u00e1nlott hossz'], [
        ['Meta c\u00edm', 'A Google tal\u00e1latban megjelen\u0151 c\u00edmsor', '50-60 karakter'],
        ['Meta le\u00edr\u00e1s', 'A Google tal\u00e1latban a c\u00edm alatti sz\u00f6veg', '120-160 karakter'],
        ['Meta kulcsszavak', 'Vessz\u0151vel elv\u00e1lasztott kulcsszavak', '5-10 kulcssz\u00f3'],
        ['OG c\u00edm', 'K\u00f6z\u00f6ss\u00e9gi m\u00e9di\u00e1ban (Facebook) megjelen\u0151 c\u00edm', '50-60 karakter'],
        ['OG le\u00edr\u00e1s', 'K\u00f6z\u00f6ss\u00e9gi m\u00e9di\u00e1ban megjelen\u0151 le\u00edr\u00e1s', '120-160 karakter'],
        ['OG k\u00e9p', 'Facebook megoszt\u00e1skor megjelen\u0151 k\u00e9p URL', '1200x630 px aj\u00e1nlott'],
    ])
    add_tip(doc, 'Ha nem t\u00f6lt ki SEO mez\u0151t, a rendszer az oldal c\u00edm\u00e9t \u00e9s a glob\u00e1lis be\u00e1ll\u00edt\u00e1sokat haszn\u00e1lja.')

    doc.add_heading('4.4 Oldal szerkeszt\u00e9se', level=2)
    doc.add_paragraph(f'Az oldalak list\u00e1j\u00e1ban kattintson a {q("Szerkeszt\u00e9s")} gombra. Megjelenik az oldal szerkeszt\u0151 fel\u00fclete, ahol m\u00f3dos\u00edthatja az oldal c\u00edm\u00e9t, SEO be\u00e1ll\u00edt\u00e1sait, \u00e9s kezelheti a szekci\u00f3k list\u00e1j\u00e1t.')

    doc.add_heading('4.5 Oldal t\u00f6rl\u00e9se', level=2)
    doc.add_paragraph(f'Az oldalak list\u00e1j\u00e1ban kattintson a {q("T\u00f6rl\u00e9s")} gombra. A rendszer meger\u0151s\u00edt\u00e9st k\u00e9r \u2014 ez a m\u0171velet nem vonhat\u00f3 vissza! A t\u00f6rl\u00e9s az oldal \u00f6sszes szekci\u00f3j\u00e1t is elt\u00e1vol\u00edtja.')
    add_tip(doc, f'T\u00f6rl\u00e9s helyett el\u0151sz\u00f6r pr\u00f3b\u00e1lja az oldalt {q("V\u00e1zlat")} \u00e1llapotba \u00e1ll\u00edtani \u2014 \u00edgy nem jelenik meg, de megmarad.', '\u26a0\ufe0f Figyelem:')

    # 5. Sections
    doc.add_heading('5. Szekci\u00f3k (tartalomblokkok)', level=1)
    doc.add_paragraph('Minden oldal tartalomblokkok (szekci\u00f3k) sorozat\u00e1b\u00f3l \u00e1ll. Egy oldalhoz tetsz\u0151leges sz\u00e1m\u00fa szekci\u00f3t adhat hozz\u00e1, \u00e9s ezeket tetsz\u0151leges sorrendbe rendezheti.')

    doc.add_heading('5.1 Szekci\u00f3 hozz\u00e1ad\u00e1sa', level=2)
    doc.add_paragraph(f'Oldal szerkeszt\u00e9sekor g\u00f6rgessen a {q("Szekci\u00f3k")} r\u00e9szhez. V\u00e1lassza ki a k\u00edv\u00e1nt t\u00edpust a leg\u00f6rd\u00fcl\u0151 list\u00e1b\u00f3l, \u00e9s kattintson az {q("\u00daj szekci\u00f3 hozz\u00e1ad\u00e1sa")} gombra.')

    doc.add_heading('5.2 Szekci\u00f3k sorrendj\u00e9nek m\u00f3dos\u00edt\u00e1sa', level=2)
    doc.add_paragraph('A szekci\u00f3k sorrendj\u00e9t h\u00fazd-\u00e9s-ejtsd (drag & drop) m\u00f3dszerrel v\u00e1ltoztathatja meg. Fogja meg a szekci\u00f3 melletti \u2630 ikont, \u00e9s h\u00fazza a k\u00edv\u00e1nt poz\u00edci\u00f3ba. A sorrend automatikusan ment\u0151dik.')

    doc.add_heading('5.3 El\u00e9rhet\u0151 szekci\u00f3t\u00edpusok \u00e1ttekint\u00e9se', level=2)
    doc.add_paragraph('Az al\u00e1bbi t\u00e1bl\u00e1zat r\u00f6vid \u00e1ttekint\u00e9st ad a 24 szekci\u00f3t\u00edpusr\u00f3l. Mindegyik r\u00e9szletes le\u00edr\u00e1sa az 5.4\u20135.27 alfejezetekben tal\u00e1lhat\u00f3.')
    section_types = [
        ['1. Hero (fejl\u00e9ck\u00e9p)', 'Teljes sz\u00e9less\u00e9g\u0171 fejl\u00e9ck\u00e9p c\u00edmsorral \u00e9s CTA gombbal.'],
        ['2. Hero diavet\u00edt\u00e9s', 'Automatikus k\u00e9pv\u00e1lt\u00e1sos fejl\u00e9c navig\u00e1ci\u00f3s dobozokkal.'],
        ['3. Sz\u00f6veg', 'Egyszer\u0171 sz\u00f6veges blokk c\u00edmsorral \u00e9s gazdag form\u00e1z\u00e1ssal.'],
        ['4. K\u00e9p + sz\u00f6veg', 'K\u00e9p \u00e9s sz\u00f6veg egym\u00e1s mellett, \u00e1ll\u00edthat\u00f3 poz\u00edci\u00f3val.'],
        ['5. K\u00e1rty\u00e1k', 'Szolg\u00e1ltat\u00e1s- vagy inform\u00e1ci\u00f3s k\u00e1rty\u00e1k r\u00e1csban.'],
        ['6. CTA (cselekv\u00e9sre \u00f6szt\u00f6nz\u00e9s)', 'Sz\u00ednes s\u00e1v nagybet\u0171s felirattal \u00e9s gombbal.'],
        ['7. Gal\u00e9ria', 'K\u00e9pgal\u00e9ria r\u00e1cs elrendez\u00e9sben.'],
        ['8. Harmonika (GYIK)', 'Leny\u00edl\u00f3 k\u00e9rd\u00e9s-v\u00e1lasz blokkok, Google FAQ adattal.'],
        ['9. Fut\u00f3 sz\u00f6veg (h\u00edrszalag)', 'V\u00edzszintesen fut\u00f3 sz\u00f6vegs\u00e1v, random sorrendben.'],
        ['10. Sz\u00e1mok / Statisztika', 'Kiemelked\u0151 sz\u00e1madatok (pl. 100+ \u00dcgyf\u00e9l).'],
        ['11. K\u00e9t oszlop', 'K\u00e9t oszlopos sz\u00f6vegelrendez\u00e9s.'],
        ['12. V\u00e9lem\u00e9nyek', '\u00dcgyf\u00e9lv\u00e9lem\u00e9nyek k\u00e1rty\u00e1kon n\u00e9vvel \u00e9s fot\u00f3val.'],
        ['13. Elv\u00e1laszt\u00f3', 'Vizu\u00e1lis elv\u00e1laszt\u00f3 (vonal, pontok, hull\u00e1m, \u00fcres t\u00e9r).'],
        ['14. Vide\u00f3', 'YouTube vagy Vimeo vide\u00f3 be\u00e1gyaz\u00e1s.'],
        ['15. Oldal/cikk lista', 'Automatikus lista m\u00e1s publik\u00e1lt oldalakb\u00f3l.'],
        ['16. T\u00e9rk\u00e9p', 'Google Maps be\u00e1gyazott t\u00e9rk\u00e9p.'],
        ['17. Kapcsolat \u0171rlap', '\u00dczenetk\u00fcld\u0151 \u0171rlap CSRF v\u00e9delemmel.'],
        ['18. Kulcssz\u00f3 felh\u0151', 'Automatikusan gener\u00e1lt kulcssz\u00f3-linkek.'],
        ['19. Term\u00e9kr\u00e1cs', 'Term\u00e9kk\u00e1rty\u00e1k k\u00e9ppel, hover-re le\u00edr\u00e1ssal.'],
        ['20. Rejtett SEO sz\u00f6veg', 'Google indexeli, de felhaszn\u00e1l\u00f3 csak gombra kattintva l\u00e1tja.'],
        ['21. Links\u00e1v', 'Sz\u00e9les sz\u00ednes kattinthat\u00f3 s\u00e1v hivatkoz\u00e1ssal.'],
        ['22. Referencia gal\u00e9ria', 'Projektenkinti gal\u00e9ria bor\u00edt\u00f3k\u00e9ppel + r\u00e9szletk\u00e9pekkel.'],
        ['23. Oldalt\u00e9rk\u00e9p', 'Automatikus oldalt\u00e9rk\u00e9p a men\u00fc \u00e9s oldalak alapj\u00e1n.'],
        ['24. Tud\u00e1smorzsak', 'Anim\u00e1lt tud\u00e1sdobozok linkkel, random sorrendben.'],
    ]
    add_table(doc, ['#  T\u00edpus', 'R\u00f6vid le\u00edr\u00e1s'], section_types)
    doc.add_page_break()

    # ── 5.4–5.27: Detailed description of each section type ──

    doc.add_heading('5.4 Hero (fejl\u00e9ck\u00e9p)', level=2)
    doc.add_paragraph(
        'A Hero szekci\u00f3 a weboldal egyik legfontosabb vizu\u00e1lis eleme. Teljes sz\u00e9less\u00e9g\u0171, nagy m\u00e9ret\u0171 '
        'h\u00e1tt\u00e9rk\u00e9pet jelenit meg s\u00f6t\u00e9t \u00e1ttet\u0151 f\u00e1tyollal, amelyen egy felt\u0171n\u0151 c\u00edmsor, alc\u00edm \u00e9s '
        'egy opcion\u00e1lis CTA (cselekv\u00e9sre \u00f6szt\u00f6nz\u0151) gomb jelenik meg. Ez \u00e1ltal\u00e1ban az oldal els\u0151 szekci\u00f3ja, '
        'amely azonnal megragadja a l\u00e1togat\u00f3 figyelm\u00e9t.'
    )
    doc.add_paragraph('Ide\u00e1lis haszn\u00e1lat: f\u0151oldal, szolg\u00e1ltat\u00e1s oldalak, landing page-ek tetej\u00e9n.')
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'Az oldal f\u0151c\u00edme, nagy bet\u0171m\u00e9rettel jelenik meg. HTML H1 c\u00edmk\u00e9t kap.', 'Igen'],
        ['Alc\u00edm (subtitle)', 'A c\u00edmsor alatti magyar\u00e1z\u00f3 sz\u00f6veg.', 'Nem'],
        ['H\u00e1tt\u00e9rk\u00e9p (image)', 'A nagy h\u00e1tt\u00e9rk\u00e9p URL-je. V\u00e1lassza a Tall\u00f3z\u00e1s gombbal.', 'Aj\u00e1nlott'],
        ['CTA gomb sz\u00f6vege (cta_text)', 'A gomb felirata (pl. "Tov\u00e1bb", "\u00c9rdekl\u0151d\u00f6m").', 'Nem'],
        ['CTA gomb linkje (cta_url)', 'Hov\u00e1 mutasson a gomb (bels\u0151 oldal vagy k\u00fcls\u0151 URL).', 'Nem'],
    ])
    add_tip(doc, 'Haszn\u00e1ljon nagy felbont\u00e1s\u00fa, sz\u00e9les k\u00e9pet (legal\u00e1bb 1920 px sz\u00e9les). A s\u00f6t\u00e9t f\u00e1tyol gondoskodik r\u00f3la, hogy a sz\u00f6veg olvashat\u00f3 maradjon b\u00e1rmilyen k\u00e9p el\u0151tt.')

    doc.add_heading('5.5 Hero diavet\u00edt\u00e9s', level=2)
    doc.add_paragraph(
        'A Hero diavet\u00edt\u00e9s a f\u0151oldal speci\u00e1lis fejl\u00e9ce, amely automatikusan v\u00e1ltakozik a kiemelt k\u00e9pek k\u00f6z\u00f6tt. '
        'A h\u00e1tt\u00e9rben l\u00e1tv\u00e1nyos \u00e1tt\u0171n\u00e9ssel cser\u00e9l\u0151dnek a k\u00e9pek, el\u0151t\u00e9rben pedig '
        'a c\u00edmsor, alc\u00edm, CTA gomb \u00e9s opcion\u00e1lis navig\u00e1ci\u00f3s dobozok l\u00e1that\u00f3k.'
    )
    doc.add_paragraph(
        'A h\u00e1tt\u00e9rk\u00e9pek a M\u00e9dia kezel\u0151b\u0151l j\u00f6nnek: azok a k\u00e9pek jelennek meg a diavet\u00edt\u00e9sben, '
        'amelyeket a M\u00e9dia oldalon a "\u2b50 Kiemel\u00e9s" gombbal kiemelt\u00e9nek jel\u00f6lt.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'F\u0151c\u00edm a diavet\u00edt\u00e9s f\u00f6l\u00f6tt.', 'Igen'],
        ['Alc\u00edm (subtitle)', 'Tov\u00e1bbi sz\u00f6veg a c\u00edmsor alatt.', 'Nem'],
        ['CTA gomb sz\u00f6vege (cta_text)', 'Gomb felirata.', 'Nem'],
        ['CTA gomb linkje (cta_url)', 'Gomb c\u00e9l URL-je.', 'Nem'],
        ['V\u00e1lt\u00e1si sebess\u00e9g (interval)', 'H\u00e1ny m\u00e1sodpercenk\u00e9nt v\u00e1ltson a k\u00e9p. Alap\u00e9rtelmezett: 4 mp.', 'Nem'],
        ['Navig\u00e1ci\u00f3s dobozok (overlay_boxes)', 'Kattinthat\u00f3 dobozok a fejl\u00e9c alj\u00e1n. Mindegyikn\u00e9l: c\u00edm, link, m\u00e9ret (large/small).', 'Nem'],
    ])
    add_tip(doc, 'A kiemelt k\u00e9peket a M\u00e9dia oldalon \u00e1ll\u00edthatja be. Ide\u00e1lisan 3\u20136 kiemelt k\u00e9p ad sz\u00e9p diavet\u00edt\u00e9st.')
    add_tip(doc, 'Ha nincsenek kiemelt k\u00e9pek, a rendszer sz\u00ednes gradiens h\u00e1tteret jelenít meg.')

    doc.add_heading('5.6 Sz\u00f6veg', level=2)
    doc.add_paragraph(
        'Az egyik leggyakrabban haszn\u00e1lt szekci\u00f3t\u00edpus. Egyszer\u0171 sz\u00f6veges blokk, amely egy opcion\u00e1lis c\u00edmsort \u00e9s '
        'gazdag form\u00e1z\u00e1s\u00fa sz\u00f6vegtartalmat jelen\u00edt meg. A sz\u00f6vegben haszn\u00e1lhat\u00f3 vastag, d\u0151lt, '
        'al\u00e1h\u00faz\u00e1s, hivatkoz\u00e1s, list\u00e1k \u00e9s b\u00e1rmilyen HTML form\u00e1z\u00e1s.'
    )
    doc.add_paragraph('Ide\u00e1lis haszn\u00e1lat: bemutatkoz\u00f3 sz\u00f6vegek, cikkek, le\u00edr\u00e1sok, jogi sz\u00f6vegek.')
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A szekci\u00f3 c\u00edme (H2 c\u00edmk\u00e9vel).', 'Nem'],
        ['Sz\u00f6vegt\u00f6rzs (body)', 'A f\u0151 tartalom. Gazdag sz\u00f6vegszerkeszt\u0151vel szerkeszthet\u0151.', 'Igen'],
    ])

    doc.add_heading('5.7 K\u00e9p + sz\u00f6veg', level=2)
    doc.add_paragraph(
        'K\u00e9t oszlopos elrendez\u00e9s, ahol az egyik oldalon egy k\u00e9p, a m\u00e1sikon sz\u00f6veg jelenik meg. '
        'A k\u00e9p poz\u00edci\u00f3ja \u00e1ll\u00edthat\u00f3 (bal vagy jobb oldal). Ez a szekci\u00f3 ide\u00e1lis term\u00e9kek, '
        'szolg\u00e1ltat\u00e1sok vagy csapattagok bemutat\u00e1s\u00e1ra.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A szekci\u00f3 c\u00edme.', 'Nem'],
        ['Sz\u00f6vegt\u00f6rzs (body)', 'Gazdag sz\u00f6veg a k\u00e9p mellett.', 'Igen'],
        ['K\u00e9p (image)', 'A megjelen\u00edtend\u0151 k\u00e9p URL-je.', 'Igen'],
        ['K\u00e9p alt sz\u00f6veg (image_alt)', 'A k\u00e9p sz\u00f6veges le\u00edr\u00e1sa (SEO \u00e9s akad\u00e1lymentess\u00e9g).', 'Aj\u00e1nlott'],
        ['K\u00e9p poz\u00edci\u00f3 (image_position)', 'A k\u00e9p helye: "left" (bal) vagy "right" (jobb). Alap\u00e9rtelmezett: jobb.', 'Nem'],
    ])
    add_tip(doc, 'V\u00e1ltogassa a k\u00e9p poz\u00edci\u00f3j\u00e1t az egym\u00e1s ut\u00e1ni szekci\u00f3kban (bal, jobb, bal, jobb) a v\u00e1ltozatos megjelen\u00e9s \u00e9rdek\u00e9ben.')

    doc.add_heading('5.8 K\u00e1rty\u00e1k', level=2)
    doc.add_paragraph(
        'T\u00f6bb k\u00e1rty\u00e1t jelen\u00edt meg r\u00e1cs (grid) elrendez\u00e9sben. Minden k\u00e1rty\u00e1nak van ikonja, c\u00edme, '
        'le\u00edr\u00e1sa \u00e9s opcion\u00e1lisan linkje. Ide\u00e1lis szolg\u00e1ltat\u00e1sok, el\u0151ny\u00f6k vagy kateg\u00f3ri\u00e1k bemutat\u00e1s\u00e1ra. '
        'Tetsz\u0151leges sz\u00e1m\u00fa k\u00e1rty\u00e1t tartalmazhat.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A szekci\u00f3 f\u0151c\u00edme a k\u00e1rty\u00e1k f\u00f6l\u00f6tt.', 'Nem'],
        ['K\u00e1rty\u00e1k (cards)', 'K\u00e1rtya lista. Minden k\u00e1rty\u00e1nak:', 'Igen'],
        ['  \u2192 C\u00edm (title)', 'A k\u00e1rtya c\u00edme.', 'Igen'],
        ['  \u2192 Le\u00edr\u00e1s (description)', 'R\u00f6vid le\u00edr\u00f3 sz\u00f6veg.', 'Igen'],
        ['  \u2192 Ikon (icon)', 'Emoji vagy sz\u00f6veges ikon (pl. \u2b50, \u2705, \u2764).', 'Nem'],
        ['  \u2192 Link (link)', 'Ha kit\u00f6lti, a k\u00e1rtya kattinthat\u00f3 lesz.', 'Nem'],
    ])
    add_tip(doc, f'A {q("+ K\u00e1rtya hozz\u00e1ad\u00e1sa")} gombbal vehet fel \u00faj k\u00e1rty\u00e1t. A {q("T\u00f6rl\u00e9s")} gombbal t\u00e1vol\u00edthat el.')

    doc.add_heading('5.9 CTA (cselekv\u00e9sre \u00f6szt\u00f6nz\u00e9s)', level=2)
    doc.add_paragraph(
        'Teljes sz\u00e9less\u00e9g\u0171, sz\u00ednes h\u00e1tter\u0171 s\u00e1v egy nagy felirattal, alc\u00edmmel \u00e9s felt\u0171n\u0151 gombbal. '
        'C\u00e9lja, hogy a l\u00e1togat\u00f3t cselekv\u00e9sre \u00f6szt\u00f6n\u00f6zze: \u00e9rdekl\u0151d\u00e9s jelz\u00e9se, kapcsolatfelv\u00e9tel, '
        'aj\u00e1nlatk\u00e9r\u00e9s stb. Jellemz\u0151en az oldal alj\u00e1n vagy k\u00e9t tartalmi blokk k\u00f6z\u00f6tt haszn\u00e1lj\u00e1k.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A f\u0151 \u00fczenet (pl. "K\u00e9rjen \u00e1raj\u00e1nlatot!").', 'Igen'],
        ['Alc\u00edm (subtitle)', 'Kieg\u00e9sz\u00edt\u0151 sz\u00f6veg a c\u00edm alatt.', 'Nem'],
        ['Gomb sz\u00f6vege (button_text)', 'A CTA gomb felirata (pl. "Kapcsolat").', 'Igen'],
        ['Gomb linkje (button_url)', 'A gomb c\u00e9l URL-je.', 'Igen'],
        ['H\u00e1tt\u00e9rsz\u00edn (background_color)', 'A s\u00e1v h\u00e1ttersz\u00edne HEX form\u00e1tumban. Alap: #0067FF (k\u00e9k).', 'Nem'],
    ])

    doc.add_heading('5.10 Gal\u00e9ria', level=2)
    doc.add_paragraph(
        'K\u00e9pgal\u00e9ria r\u00e1cs elrendez\u00e9sben. A k\u00e9pek automatikusan illeszkednek a rendelkez\u00e9sre \u00e1ll\u00f3 t\u00e9rbe. '
        'Ide\u00e1lis referencia k\u00e9pek, munkak\u00e9pek vagy term\u00e9kfot\u00f3k bemutat\u00e1s\u00e1ra. '
        'Minden k\u00e9p lusta bet\u00f6lt\u00e9st (lazy loading) haszn\u00e1l a gyorsabb oldal megjelen\u00e9s \u00e9rdek\u00e9ben.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A gal\u00e9ria c\u00edme.', 'Nem'],
        ['K\u00e9pek (images)', 'K\u00e9p lista. Minden k\u00e9pn\u00e9l:', 'Igen'],
        ['  \u2192 K\u00e9p URL (url)', 'A k\u00e9p el\u00e9r\u00e9si \u00fatja.', 'Igen'],
        ['  \u2192 Alt sz\u00f6veg (alt)', 'A k\u00e9p sz\u00f6veges le\u00edr\u00e1sa (SEO!).', 'Aj\u00e1nlott'],
    ])
    add_tip(doc, f'A {q("+ K\u00e9p hozz\u00e1ad\u00e1sa")} gombbal vehet fel \u00faj k\u00e9pet, \u00e9s a {q("Tall\u00f3z\u00e1s")} gombbal v\u00e1laszthat a felt\u00f6lt\u00f6tt k\u00e9pek k\u00f6z\u00fcl.')

    doc.add_heading('5.11 Harmonika (GYIK / Accordion)', level=2)
    doc.add_paragraph(
        'Leny\u00edl\u00f3 k\u00e9rd\u00e9s-v\u00e1lasz blokkok, amelyek ide\u00e1lisak Gyakran Ism\u00e9telt K\u00e9rd\u00e9sek (GYIK) oldalhoz, '
        'alapfogalmak magyar\u00e1zat\u00e1hoz vagy b\u00e1rmilyen kinyithat\u00f3 tartalomhoz. '
        'Alap\u00e1llapotban csak a k\u00e9rd\u00e9sek l\u00e1that\u00f3k; kattint\u00e1sra ny\u00edlik le a v\u00e1lasz.'
    )
    doc.add_paragraph(
        'SEO el\u0151ny: A rendszer automatikusan FAQPage struktur\u00e1lt adatot (schema.org) gener\u00e1l, '
        'amelyet a Google Rich Results-k\u00e9nt jelen\u00edthet meg a keres\u0151 tal\u00e1latokban. '
        'Ez azt jelenti, hogy a k\u00e9rd\u00e9sek \u00e9s v\u00e1laszok k\u00f6zvetlen\u00fcl a Google tal\u00e1lati oldalon jelenhetnek meg!'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A szekci\u00f3 c\u00edme a harmonik\u00e1k f\u00f6l\u00f6tt.', 'Nem'],
        ['Elemek (items)', 'K\u00e9rd\u00e9s-v\u00e1lasz p\u00e1rok list\u00e1ja:', 'Igen'],
        ['  \u2192 K\u00e9rd\u00e9s (question)', 'A kattinthat\u00f3 c\u00edmsor sz\u00f6vege.', 'Igen'],
        ['  \u2192 V\u00e1lasz (answer)', 'A leny\u00edl\u00f3 tartalom (HTML form\u00e1z\u00e1ssal).', 'Igen'],
    ])
    add_tip(doc, '\u00cdrjon pontos, r\u00f6vid k\u00e9rd\u00e9seket \u00e9s r\u00e9szletes v\u00e1laszokat. A Google a k\u00e9rd\u00e9seket \u00e9s v\u00e1laszokat k\u00f6zvetlen\u00fcl megjelen\u00edtheti a tal\u00e1lati oldalon.')

    doc.add_heading('5.12 Fut\u00f3 sz\u00f6veg (ticker / h\u00edrszalag)', level=2)
    doc.add_paragraph(
        'V\u00edzszintesen jobb\u00f3l balra g\u00f6rd\u00fcl\u0151 sz\u00f6vegs\u00e1v, amely felt\u0171n\u0151en jelen\u00edti meg az aktu\u00e1lis h\u00edreket, '
        'aj\u00e1nlatokat vagy fontos inform\u00e1ci\u00f3kat. A sz\u00f6vegek random sorrendben jelennek meg, \u00edgy minden '
        'oldalbetolt\u00e9skor m\u00e1s \u00e9lm\u00e9nyt ad. A l\u00e1togat\u00f3 sz\u00fcneteltetheti a fut\u00e1st.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['Elemek (items)', 'A fut\u00f3 sz\u00f6vegek list\u00e1ja:', 'Igen'],
        ['  \u2192 Sz\u00f6veg (text)', 'A megjelen\u00edtend\u0151 sz\u00f6veg.', 'Igen'],
        ['  \u2192 Link (link)', 'Kattint\u00e1sra hova vigyen.', 'Nem'],
        ['Sebess\u00e9g (speed)', 'Az anim\u00e1ci\u00f3 sebess\u00e9ge m\u00e1sodpercben. Alap\u00e9rtelmezett: 30 mp.', 'Nem'],
        ['H\u00e1tt\u00e9rsz\u00edn (background_color)', 'A s\u00e1v h\u00e1tt\u00e9rsz\u00edne. Alap: #0067FF.', 'Nem'],
        ['Sz\u00f6vegsz\u00edn (text_color)', 'A sz\u00f6veg sz\u00edne. Alap: feh\u00e9r.', 'Nem'],
    ])

    doc.add_heading('5.13 Sz\u00e1mok / Statisztika', level=2)
    doc.add_paragraph(
        'Kiemelked\u0151 sz\u00e1madatok megjelen\u00edt\u00e9se vizu\u00e1lisan felt\u0171n\u0151 m\u00f3don. Ide\u00e1lis tapasztalat, '
        '\u00fcgyfelek sz\u00e1ma, projektek vagy b\u00e1rmilyen mennyis\u00e9gi adat kiemel\u00e9s\u00e9re.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A szekci\u00f3 c\u00edme.', 'Nem'],
        ['H\u00e1tt\u00e9rsz\u00edn (background_color)', 'H\u00e1tt\u00e9rsz\u00edn (ha be van \u00e1ll\u00edtva, a sz\u00f6veg feh\u00e9r lesz).', 'Nem'],
        ['Elemek (items)', 'Sz\u00e1m-c\u00edmke p\u00e1rok:', 'Igen'],
        ['  \u2192 Sz\u00e1m (number)', 'A kiemelt sz\u00e1madat (pl. "100+", "50", "10+ \u00e9v").', 'Igen'],
        ['  \u2192 C\u00edmke (label)', 'A sz\u00e1m al\u00e1tti magyar\u00e1z\u00f3 sz\u00f6veg (pl. "El\u00e9gedett \u00fcgyf\u00e9l").', 'Igen'],
    ])

    doc.add_heading('5.14 K\u00e9t oszlop', level=2)
    doc.add_paragraph(
        'K\u00e9t egyenl\u0151 oszlopos elrendez\u00e9s, ahol mindkef\u00e9l oldalon k\u00fcl\u00f6nb\u00f6z\u0151 sz\u00f6veg jelenhet meg. '
        'Mindkef\u00e9l oszlop t\u00e1mogatja a teljes HTML form\u00e1z\u00e1st (vastag, d\u0151lt, list\u00e1k, hivatkoz\u00e1sok). '
        'Ide\u00e1lis \u00f6sszehasonlit\u00e1sokhoz, el\u0151ny/h\u00e1tr\u00e1ny list\u00e1khoz, vagy az elrendez\u00e9s v\u00e1ltozatoss\u00e1g\u00e1hoz.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A szekci\u00f3 c\u00edme a k\u00e9t oszlop f\u00f6l\u00f6tt.', 'Nem'],
        ['Bal oldali sz\u00f6veg (left_body)', 'A bal oszlop tartalma (HTML).', 'Igen'],
        ['Jobb oldali sz\u00f6veg (right_body)', 'A jobb oszlop tartalma (HTML).', 'Igen'],
    ])

    doc.add_heading('5.15 V\u00e9lem\u00e9nyek (Testimonials)', level=2)
    doc.add_paragraph(
        '\u00dcgyf\u00e9lv\u00e9lem\u00e9nyek \u00e9s aj\u00e1nl\u00e1sok megjelen\u00edt\u00e9se eleg\u00e1ns k\u00e1rty\u00e1kon. Minden k\u00e1rty\u00e1n '
        'id\u00e9z\u0151jeles v\u00e9lem\u00e9ny, a v\u00e9lem\u00e9nyez\u0151 neve, poz\u00edci\u00f3ja \u00e9s opcion\u00e1lisan fot\u00f3ja l\u00e1that\u00f3. '
        'Ha nincs fot\u00f3, a rendszer automatikusan egy k\u00f6r\u00f6n bel\u00fcli kezd\u0151bet\u0171t jelen\u00edt meg.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A szekci\u00f3 c\u00edme.', 'Nem'],
        ['V\u00e9lem\u00e9nyek (items)', 'V\u00e9lem\u00e9ny lista:', 'Igen'],
        ['  \u2192 N\u00e9v (name)', 'A v\u00e9lem\u00e9nyez\u0151 neve.', 'Igen'],
        ['  \u2192 V\u00e9lem\u00e9ny (text)', 'Az id\u00e9zett sz\u00f6veg.', 'Igen'],
        ['  \u2192 Poz\u00edci\u00f3 (role)', 'C\u00e9g / beosztás (pl. "\u00dcgyvezet\u0151, ABC Kft.").', 'Nem'],
        ['  \u2192 K\u00e9p (image)', 'A szem\u00e9ly fot\u00f3ja.', 'Nem'],
    ])
    add_tip(doc, 'Val\u00f3s v\u00e9lem\u00e9nyeket haszn\u00e1ljon \u2014 a l\u00e1togat\u00f3k megb\u00edznak a hiteles aj\u00e1nl\u00e1sokban.')

    doc.add_heading('5.16 Elv\u00e1laszt\u00f3 (Divider)', level=2)
    doc.add_paragraph(
        'Vizu\u00e1lis elv\u00e1laszt\u00f3 elem k\u00e9t szekci\u00f3 k\u00f6z\u00f6tt. T\u00f6bb st\u00edlus k\u00f6z\u00fcl v\u00e1laszthat: '
        'v\u00e9kony vonal, dekorat\u00edv pontok, hull\u00e1mvonal (SVG) vagy egyszer\u0171 \u00fcres t\u00e9r. '
        'Seg\u00edt vizu\u00e1lisan tagolni az oldal tartalm\u00e1t.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['St\u00edlus (style)', 'Megjelen\u00e9s: "line" (vonal), "dots" (pontok), "wave" (hull\u00e1m), "space" (\u00fcres). Alap: vonal.', 'Nem'],
        ['T\u00e9rk\u00f6z (spacing)', 'Kompakt / norm\u00e1l / sz\u00e9les t\u00e1vols\u00e1g.', 'Nem'],
    ])

    doc.add_heading('5.17 Vide\u00f3', level=2)
    doc.add_paragraph(
        'YouTube vagy Vimeo vide\u00f3 be\u00e1gyaz\u00e1sa reszponz\u00edv m\u00f3don. Csak m\u00e1solja be a vide\u00f3 URL-j\u00e9t \u2014 '
        'a rendszer automatikusan felismeri a platformot \u00e9s \u00e1talak\u00edtja be\u00e1gyaz\u00e1si (embed) form\u00e1tumra. '
        'A vide\u00f3 lusta bet\u00f6lt\u00e9st (lazy loading) haszn\u00e1l.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A vide\u00f3 f\u00f6l\u00f6tti c\u00edm.', 'Nem'],
        ['Vide\u00f3 URL (url)', 'A YouTube vagy Vimeo vide\u00f3 URL-je. P\u00e9ld\u00e1ul: https://www.youtube.com/watch?v=XXXXX', 'Igen'],
        ['Platform (type)', 'Automatikusan \u00e9rz\u00e9keli, de manu\u00e1lisan is \u00e1ll\u00edthat\u00f3: "youtube" vagy "vimeo".', 'Nem'],
    ])
    add_tip(doc, 'Nem kell a be\u00e1gyaz\u00e1si (embed) linket haszn\u00e1lnia \u2014 a sima YouTube/Vimeo link is m\u0171k\u00f6dik!')

    doc.add_heading('5.18 Oldal/cikk lista', level=2)
    doc.add_paragraph(
        'Automatikusan list\u00e1zza a weboldal m\u00e1s publik\u00e1lt oldalait k\u00e1rty\u00e1s form\u00e1ban. '
        'Minden k\u00e1rty\u00e1n az oldal c\u00edme, le\u00edr\u00e1sa \u00e9s d\u00e1tuma l\u00e1that\u00f3. '
        'Ide\u00e1lis cikkek, h\u00edrek vagy blog bejegyz\u00e9sek gy\u0171jtem\u00e9ny\u00e9nek megjelen\u00edt\u00e9s\u00e9re.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A lista c\u00edme.', 'Nem'],
        ['Oldal t\u00edpus (page_type)', 'Sz\u0171r\u00e9s: "article" (csak cikkek), "page" (csak oldalak), vagy \u00fcres (mind).', 'Nem'],
        ['Darabsz\u00e1m (count)', 'Maximum ennyi oldal jelenik meg. Alap\u00e9rtelmezett: 10.', 'Nem'],
    ])
    add_tip(doc, 'Ez a szekci\u00f3 automatikusan friss\u00fcl, amikor \u00faj oldalt publik\u00e1l.')

    doc.add_heading('5.19 T\u00e9rk\u00e9p (Google Maps)', level=2)
    doc.add_paragraph(
        'Google Maps t\u00e9rk\u00e9p be\u00e1gyaz\u00e1sa, amely megmutatja a c\u00e9g hely\u00e9t vagy egy adott helysz\u00ednt. '
        'A t\u00e9rk\u00e9p interakt\u00edv: a l\u00e1togat\u00f3 g\u00f6rgethet, nagy\u00edthat \u00e9s kattinthat rajta.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A t\u00e9rk\u00e9p f\u00f6l\u00f6tti c\u00edm.', 'Nem'],
        ['Be\u00e1gyaz\u00e1si URL (embed_url)', 'A Google Maps "Be\u00e1gyaz\u00e1s" URL-je. L\u00e1sd tipp.', 'Igen'],
        ['Magass\u00e1g (height)', 'A t\u00e9rk\u00e9p magass\u00e1ga pixelben. Alap\u00e9rtelmezett: 400 px.', 'Nem'],
    ])
    add_tip(doc, 'A be\u00e1gyaz\u00e1si URL-t \u00edgy szerezheti meg: nyissa meg a Google Maps-ot \u2192 kattintson a "Megoszt\u00e1s" gombra \u2192 v\u00e1lassza a "T\u00e9rk\u00e9p be\u00e1gyaz\u00e1sa" f\u00fclet \u2192 m\u00e1solja ki az src="..." \u00e9rt\u00e9ket.')

    doc.add_heading('5.20 Kapcsolat \u0171rlap', level=2)
    doc.add_paragraph(
        '\u00dczenetk\u00fcld\u0151 \u0171rlap, amelyen a l\u00e1togat\u00f3k k\u00f6zvetlen\u00fcl az oldalr\u00f3l k\u00fclhetnek \u00fczenetet. '
        'Az \u0171rlap CSRF v\u00e9delemmel rendelkezik, \u00e9s az \u00fczenetek az adatb\u00e1zisba ment\u0151dnek '
        '(teh\u00e1t akkor sem vesznek el, ha az e-mail \u00e9rtes\u00edt\u00e9s nincs be\u00e1ll\u00edtva). '
        'Ha az SMTP konfigur\u00e1lva van, e-mail \u00e9rtes\u00edt\u00e9st is k\u00fcld.'
    )
    doc.add_paragraph('\u0170rlap mez\u0151k: N\u00e9v (k\u00f6telez\u0151), E-mail (k\u00f6telez\u0151), Telefon (opcion\u00e1lis), \u00dczenet (k\u00f6telez\u0151).')
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'Az \u0171rlap f\u00f6l\u00f6tti c\u00edm.', 'Nem'],
        ['Sikeres k\u00fcld\u00e9s \u00fczenet (success_message)', 'Az \u0171rlap bek\u00fcld\u00e9se ut\u00e1n megjelen\u0151 sz\u00f6veg.', 'Nem'],
    ])
    add_tip(doc, 'Az \u00fczenetek az admin panel \u00dczenetek men\u00fcj\u00e9ben olvashat\u00f3k \u2014 e-mail be\u00e1ll\u00edt\u00e1s n\u00e9lk\u00fcl is!')

    doc.add_heading('5.21 Kulcssz\u00f3 felh\u0151', level=2)
    doc.add_paragraph(
        'Automatikusan l\u00e9trehozott kulcssz\u00f3-felh\u0151, amely az oldalak meta kulcsszavaib\u00f3l \u00e9p\u00fcl fel. '
        'Minden kulcssz\u00f3 kattinthat\u00f3 link, amely list\u00e1zza az adott kulcssz\u00f3hoz tartoz\u00f3 oldalakat. '
        'A bet\u0171m\u00e9ret a gyakoris\u00e1got t\u00fckr\u00f6zi: a leggyakoribb kulcsszavak nagyobb bet\u0171vel jelennek meg.'
    )
    doc.add_paragraph('Ez a szekci\u00f3 automatikus \u2014 nem ig\u00e9nyel k\u00e9zi adatbevitelt (az oldalak kulcsszavaib\u00f3l gener\u00e1l\u00f3dik).')
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A felh\u0151 c\u00edme.', 'Nem'],
        ['Darabsz\u00e1m (count)', 'Maximum ennyi kulcssz\u00f3 jelenik meg. Alap\u00e9rtelmezett: 50.', 'Nem'],
    ])
    add_tip(doc, 'Min\u00e9l t\u00f6bb oldalhoz ad meg kulcsszavakat (SEO be\u00e1ll\u00edt\u00e1sok), ann\u00e1l gazdagabb lesz a kulcssz\u00f3 felh\u0151.')

    doc.add_heading('5.22 Term\u00e9kr\u00e1cs (Product Grid)', level=2)
    doc.add_paragraph(
        'Term\u00e9kk\u00e1rty\u00e1k r\u00e1cs elrendez\u00e9sben, ahol minden k\u00e1rty\u00e1nak van k\u00e9pe, c\u00edme, '
        'hover-re (eg\u00e9r r\u00e1vitel\u00e9re) megjelen\u0151 r\u00f6vid le\u00edr\u00e1sa \u00e9s opcion\u00e1lis linkje. '
        'Az oszlopok sz\u00e1ma \u00e1ll\u00edthat\u00f3. Ide\u00e1lis term\u00e9kek, kieg\u00e9sz\u00edt\u0151k vagy berendez\u00e9sek bemutat\u00e1s\u00e1ra.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A r\u00e1cs c\u00edme.', 'Nem'],
        ['Oszlopok sz\u00e1ma (columns)', 'H\u00e1ny oszlopos legyen a r\u00e1cs. Alap\u00e9rtelmezett: 5.', 'Nem'],
        ['Term\u00e9kek (items)', 'Term\u00e9k lista:', 'Igen'],
        ['  \u2192 C\u00edm (title)', 'A term\u00e9k neve.', 'Igen'],
        ['  \u2192 K\u00e9p (image)', 'Term\u00e9k k\u00e9p URL.', 'Aj\u00e1nlott'],
        ['  \u2192 K\u00e9p alt (image_alt)', 'K\u00e9p le\u00edr\u00e1s (SEO).', 'Aj\u00e1nlott'],
        ['  \u2192 R\u00f6vid le\u00edr\u00e1s (short_desc)', 'Hover-re megjelen\u0151 sz\u00f6veg.', 'Nem'],
        ['  \u2192 Link (url)', 'Kattint\u00e1sra hova vigyen.', 'Nem'],
    ])

    doc.add_heading('5.23 Rejtett SEO sz\u00f6veg', level=2)
    doc.add_paragraph(
        'Olyan sz\u00f6vegblokk, amely alap\u00e9rtelmez\u00e9s szerint \u00f6sszecsukva (rejtve) jelenik meg, '
        'de a keresőmotorok (Google) teljes m\u00e9rt\u00e9kben indexelik a tartalm\u00e1t. A l\u00e1togat\u00f3 egy gombra '
        'kattintva olvashatja el. Ide\u00e1lis hossz\u00fa SEO sz\u00f6vegekhez, amelyek fontosak a keres\u0151-optimaliz\u00e1l\u00e1shoz, '
        'de nem szeretn\u00e9 , ha elnyomn\u00e1k a f\u0151 tartalmat.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['Gomb sz\u00f6veg (button_text)', 'A kinyit\u00f3 gomb felirata. Alap: "Tov\u00e1bb olvasom...".', 'Nem'],
        ['Sz\u00f6vegt\u00f6rzs (body)', 'A rejtett tartalom (HTML form\u00e1z\u00e1ssal).', 'Igen'],
    ])
    add_tip(doc, 'Ez a szekci\u00f3 nat\u00edv HTML5 <details> elemet haszn\u00e1l, amelyet a Google hivatalosan is indexel.')

    doc.add_heading('5.24 Links\u00e1v (Link Banner)', level=2)
    doc.add_paragraph(
        'Teljes sz\u00e9less\u00e9g\u0171, sz\u00ednes, kattinthat\u00f3 s\u00e1v, amely egy hivatkoz\u00e1st jelen\u00edt meg ikonnal \u00e9s ny\u00edllal. '
        'Az eg\u00e9sz s\u00e1v egyetlen nagy link. Ide\u00e1lis k\u00fcl\u00f6n\u00e1ll\u00f3 hivatkoz\u00e1s kiemel\u00e9s\u00e9re, '
        'p\u00e9ld\u00e1ul "Olvassa el az alapfogalmakat \u2192" vagy "T\u00f6ltse le a katal\u00f3gust \u2192".'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['Sz\u00f6veg (text)', 'A s\u00e1von megjelen\u0151 sz\u00f6veg.', 'Igen'],
        ['Link (url)', 'A c\u00e9l URL.', 'Igen'],
        ['Ikon (icon)', 'Emoji vagy ikon a sz\u00f6veg el\u0151tt (pl. \u2764, \u2b07\ufe0f).', 'Nem'],
        ['H\u00e1tt\u00e9rsz\u00edn (background_color)', 'A s\u00e1v sz\u00edne. Alap: #0067FF.', 'Nem'],
    ])

    doc.add_heading('5.25 Referencia gal\u00e9ria', level=2)
    doc.add_paragraph(
        'K\u00e9t szint\u0171 gal\u00e9ria, amely projektekbe rendezve mutatja be a referenci\u00e1kat. '
        'Minden projektn\u00e9l van egy bor\u00edt\u00f3k\u00e9p (amely mindig l\u00e1that\u00f3) \u00e9s tov\u00e1bbi r\u00e9szletk\u00e9pek, '
        'amelyek kattint\u00e1sra/hover-re jelennek meg. Ide\u00e1lis kor\u00e1bbi munk\u00e1k, telep\u00edt\u00e9sek bemutat\u00e1s\u00e1ra.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A gal\u00e9ria c\u00edme.', 'Nem'],
        ['Projektek (projects)', 'Projekt lista:', 'Igen'],
        ['  \u2192 C\u00edm (title)', 'A projekt neve.', 'Igen'],
        ['  \u2192 Bor\u00edt\u00f3k\u00e9p (cover_image)', 'A projekt f\u0151 k\u00e9pe.', 'Aj\u00e1nlott'],
        ['  \u2192 Bor\u00edt\u00f3 alt (cover_alt)', 'A bor\u00edt\u00f3k\u00e9p le\u00edr\u00e1sa (SEO).', 'Aj\u00e1nlott'],
        ['  \u2192 R\u00e9szletk\u00e9pek (images)', 'Tov\u00e1bbi k\u00e9pek list\u00e1ja (url + alt).', 'Nem'],
    ])
    add_tip(doc, f'A {q("+ Projekt hozz\u00e1ad\u00e1sa")} gombbal vehet fel \u00faj projektet, a projekteken bel\u00fcl pedig {q("+ K\u00e9p")} gombbal adhat hozz\u00e1 r\u00e9szletk\u00e9peket.')

    doc.add_heading('5.26 Oldalt\u00e9rk\u00e9p (Sitemap)', level=2)
    doc.add_paragraph(
        'Automatikusan gener\u00e1lt hierarchikus oldalt\u00e9rk\u00e9p, amely a men\u00fcszerkezet \u00e9s az \u00f6sszes '
        'publik\u00e1lt oldal alapj\u00e1n \u00e9p\u00fcl fel. A men\u00fcben l\u00e9v\u0151 oldalak f\u00e1s szerkezetben, '
        'a men\u00fcben nem l\u00e9v\u0151 oldalak k\u00fcl\u00f6n "Tov\u00e1bbi oldalak" r\u00e9szben jelennek meg.'
    )
    doc.add_paragraph('Ez a szekci\u00f3 teljesen automatikus \u2014 nem ig\u00e9nyel be\u00e1ll\u00edt\u00e1st a men\u00fcszerkezeten k\u00edv\u00fcl.')
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'Az oldalt\u00e9rk\u00e9p c\u00edme. Alap: "Oldalt\u00e9rk\u00e9p".', 'Nem'],
    ])
    add_tip(doc, 'Az oldalt\u00e9rk\u00e9p automatikusan friss\u00fcl, amikor \u00faj oldalt publik\u00e1l vagy a men\u00fct m\u00f3dos\u00edtja.')

    doc.add_heading('5.27 Tud\u00e1smorzsak', level=2)
    doc.add_paragraph(
        'R\u00f6vid, hasznos inform\u00e1ci\u00f3kat tartalmaz\u00f3 k\u00e1rty\u00e1k (tud\u00e1smorzsak), amelyek random sorrendben '
        'jelennek meg \u00e9s l\u00e1tv\u00e1nyos anim\u00e1ci\u00f3val \u00fasznak be jobb oldalr\u00f3l. Minden k\u00e1rty\u00e1nak van c\u00edme, '
        'r\u00f6vid le\u00edr\u00e1sa \u00e9s opcion\u00e1lis linkje. Ide\u00e1lis tippek, \u00e9rdekess\u00e9gek vagy gyors inform\u00e1ci\u00f3k megjelen\u00edt\u00e9s\u00e9re.'
    )
    add_table(doc, ['Mez\u0151', 'Le\u00edr\u00e1s', 'K\u00f6telez\u0151?'], [
        ['C\u00edmsor (heading)', 'A szekci\u00f3 c\u00edme. Alap: "Tud\u00e1smorzsak".', 'Nem'],
        ['Elemek (items)', 'Tud\u00e1smorzsa lista:', 'Igen'],
        ['  \u2192 C\u00edm (title)', 'A morzsa c\u00edme.', 'Igen'],
        ['  \u2192 Le\u00edr\u00e1s (description)', 'R\u00f6vid sz\u00f6veg.', 'Igen'],
        ['  \u2192 Link (url)', 'Ha megad, a k\u00e1rtya kattinthat\u00f3 lesz.', 'Nem'],
    ])
    add_tip(doc, 'A morzsak minden oldalbetolt\u00e9skor m\u00e1s sorrendben jelennek meg, ez \u00e9rdekess\u00e9 teszi a vissza\u00e9rkez\u0151 l\u00e1togat\u00f3k sz\u00e1m\u00e1ra.')

    doc.add_page_break()

    doc.add_heading('5.28 Szekci\u00f3 szerkeszt\u00e9se (\u00e1ltal\u00e1nos)', level=2)
    doc.add_paragraph(f'A szekci\u00f3 melletti {q("Szerkeszt\u00e9s")} gombra kattintva megjelenik a szekci\u00f3 t\u00edpus\u00e1nak megfelel\u0151 szerkeszt\u0151fel\u00fclet. Minden szekci\u00f3n\u00e1l m\u00e1s mez\u0151k jelennek meg \u2014 a r\u00e9szleteket l\u00e1sd az 5.4\u20135.27 alfejezetekben.')

    doc.add_heading('K\u00e9p kiv\u00e1laszt\u00e1sa szekci\u00f3kban', level=3)
    doc.add_paragraph(f'A legt\u00f6bb szekci\u00f3 tartalmaz egy {q("Tall\u00f3z\u00e1s")} gombot a k\u00e9p mez\u0151 mellett. Erre kattintva megjelenik a felt\u00f6lt\u00f6tt k\u00e9pek list\u00e1ja, amelyb\u0151l egyszer\u0171en kiv\u00e1laszthatja a k\u00edv\u00e1nt k\u00e9pet.')

    doc.add_heading('Oldal-hivatkoz\u00e1s (link) be\u00e1ll\u00edt\u00e1sa', level=3)
    doc.add_paragraph(f'A szekci\u00f3kban l\u00e9v\u0151 URL/link mez\u0151k mellett egy {q("Oldal kiv\u00e1laszt\u00e1sa")} leg\u00f6rd\u00fcl\u0151 men\u00fc tal\u00e1lhat\u00f3. V\u00e1lassza ki a k\u00edv\u00e1nt oldalt, \u00e9s a rendszer automatikusan be\u00edrja az oldal URL-j\u00e9t.')
    add_tip(doc, 'Ha k\u00fcls\u0151 weboldalra szeretn\u00e9 linkelni, \u00edrja be a teljes URL-t (pl. https://www.google.com).')

    doc.add_heading('5.29 Szekci\u00f3 t\u00f6rl\u00e9se', level=2)
    doc.add_paragraph(f'A szekci\u00f3 {q("T\u00f6rl\u00e9s")} gombj\u00e1ra kattintva a szekci\u00f3 v\u00e9glegesen elt\u00e1vol\u00edt\u00f3dik. Ez a m\u0171velet nem vonhat\u00f3 vissza!')

    # 6. Media
    doc.add_heading('6. M\u00e9dia (k\u00e9pek kezel\u00e9se)', level=1)
    doc.add_paragraph(f'A {q("M\u00e9dia")} men\u00fcponton kereszt\u00fcl kezelheti az \u00f6sszes felt\u00f6lt\u00f6tt k\u00e9pet.')

    doc.add_heading('6.1 K\u00e9p felt\u00f6lt\u00e9se', level=2)
    doc.add_paragraph(f'Kattintson a {q("K\u00e9p felt\u00f6lt\u00e9se")} gombra, v\u00e1lassza ki a k\u00e9pet a sz\u00e1m\u00edt\u00f3g\u00e9p\u00e9r\u0151l, \u00e9s t\u00f6ltse ki az {q("Alt sz\u00f6veg")} mez\u0151t (ez a k\u00e9p sz\u00f6veges le\u00edr\u00e1sa \u2014 fontos a keres\u0151-optimaliz\u00e1l\u00e1shoz).')
    add_tip(doc, f'Mindig \u00edrjon \u00e9rtelmes alt sz\u00f6veget! Pl. {q("Parkol\u00f3 soromp\u00f3 telep\u00edt\u00e9s Budapesten")} \u00e9s ne csak {q("kep1")}.')

    doc.add_heading('6.2 K\u00e9p kiemel\u00e9se (Featured)', level=2)
    doc.add_paragraph(f'A k\u00e9pek list\u00e1j\u00e1ban a {q("\u2b50 Kiemel\u00e9s")} gombbal jel\u00f6lheti meg, mely k\u00e9pek jelenjenek meg a Hero diavet\u00edt\u00e9s szekci\u00f3ban.')

    doc.add_heading('6.3 K\u00e9p t\u00f6rl\u00e9se', level=2)
    doc.add_paragraph(f'A {q("T\u00f6rl\u00e9s")} gombbal elt\u00e1vol\u00edthatja a k\u00e9pet a rendszerb\u0151l. A k\u00e9p a szerverr\u0151l is t\u00f6rl\u0151dik.')

    # 7. Menu
    doc.add_heading('7. Men\u00fc szerkeszt\u00e9se', level=1)
    doc.add_paragraph(f'A {q("Men\u00fck")} oldalon kezelheti a weboldal navig\u00e1ci\u00f3s men\u00fcj\u00e9t. A men\u00fc t\u00e1mogatja a t\u00f6bbszint\u0171 (leg\u00f6rd\u00fcl\u0151) men\u00fcrendszert, ak\u00e1r 5 szint m\u00e9lys\u00e9gig.')

    doc.add_heading('7.1 Men\u00fcpont hozz\u00e1ad\u00e1sa', level=2)
    doc.add_paragraph('T\u00f6ltse ki az al\u00e1bbi mez\u0151ket:')
    for item in [
        'Men\u00fcpont neve \u2014 a men\u00fcben megjelen\u0151 sz\u00f6veg',
        'Hivatkozott oldal \u2014 v\u00e1lassza ki, melyik oldalra mutasson',
        'Egyedi URL \u2014 vagy \u00edrjon be k\u00fcls\u0151 linket (ha nem oldalt v\u00e1laszt)',
        'Sz\u00fcl\u0151 men\u00fcpont \u2014 ha ez egy almen\u00fc, v\u00e1lassza ki melyik f\u0151 men\u00fcpont al\u00e1 tartozzon',
        'Sorrend \u2014 a megjelen\u00e9si sorrend (kisebb sz\u00e1m = el\u0151r\u00e9bb)',
    ]:
        doc.add_paragraph(item, style='List Bullet')
    add_tip(doc, 'Ha egy men\u00fcpont sz\u00fcl\u0151j\u00e9t m\u00e1sik men\u00fcpontra \u00e1ll\u00edtja, az almen\u00fc (dropdown) lesz.')

    doc.add_heading('7.2 Men\u00fcpont szerkeszt\u00e9se \u00e9s t\u00f6rl\u00e9se', level=2)
    doc.add_paragraph(f'A men\u00fcpontok list\u00e1j\u00e1ban a {q("Szerkeszt\u00e9s")} \u00e9s {q("T\u00f6rl\u00e9s")} gombokkal m\u00f3dos\u00edthatja vagy elt\u00e1vol\u00edthatja a men\u00fcpontokat.')
    add_tip(doc, 'Ha t\u00f6r\u00f6l egy sz\u00fcl\u0151 men\u00fcpontot, az almen\u00fcpontok \u00e1rva elemekk\u00e9 v\u00e1lnak.', '\u26a0\ufe0f Figyelem:')

    # 8. Settings
    doc.add_heading('8. Be\u00e1ll\u00edt\u00e1sok', level=1)
    doc.add_paragraph(f'A {q("Be\u00e1ll\u00edt\u00e1sok")} oldalon a weboldal glob\u00e1lis be\u00e1ll\u00edt\u00e1sait m\u00f3dos\u00edthatja. Ezek az eg\u00e9sz weboldalra hat\u00e1ssal vannak.')
    add_table(doc, ['Be\u00e1ll\u00edt\u00e1s', 'Mit csin\u00e1l?'], [
        ['Weboldal neve', 'A b\u00f6ng\u00e9sz\u0151 f\u00fcl\u00f6n, fejl\u00e9cben \u00e9s a Google tal\u00e1latokban megjelen\u0151 n\u00e9v.'],
        ['Szlogen', 'R\u00f6vid mott\u00f3 a weboldal neve mellett.'],
        ['Alap\u00e9rtelmezett meta le\u00edr\u00e1s', 'Ha egy oldalnak nincs saj\u00e1t meta le\u00edr\u00e1sa, ez jelenik meg a Google-ben.'],
        ['Alap\u00e9rtelmezett kulcsszavak', 'Alap\u00e9rtelmezett kulcsszavak, ha az oldal nem ad meg saj\u00e1tot.'],
        ['E-mail c\u00edm', 'A weboldalon megjelen\u0151 e-mail. Az \u00fczenetk\u00fcld\u0151 \u0171rlap c\u00edmzettje.'],
        ['Telefonsz\u00e1m', 'A fejl\u00e9cben \u00e9s l\u00e1bl\u00e9cben megjelen\u0151 telefonsz\u00e1m.'],
        ['C\u00edm', 'A c\u00e9g fizikai c\u00edme.'],
        ['Els\u0151dleges sz\u00edn', 'A weboldal f\u0151 sz\u00edne (gombok, linkek, kiemel\u00e9sek).'],
        ['M\u00e1sodlagos sz\u00edn', 'Kieg\u00e9sz\u00edt\u0151 sz\u00edn h\u00e1tt\u00e9relemekhez.'],
        ['Facebook URL', 'A c\u00e9g Facebook oldal\u00e1nak URL-je (megjelenik a l\u00e1bl\u00e9cben).'],
        ['Alap\u00e9rtelmezett OG k\u00e9p', 'Facebook megoszt\u00e1skor megjelen\u0151 k\u00e9p, ha az oldalnak nincs saj\u00e1t k\u00e9pe.'],
        ['Logo URL', 'A weboldal log\u00f3j\u00e1nak k\u00e9pe.'],
        ['Logo megjelen\u00edt\u00e9s m\u00f3dja', 'Logo helyettes\u00edti a nevet / Logo a n\u00e9v mellett / Nincs logo.'],
    ])

    # 9. Email
    doc.add_heading('9. E-mail \u00e9rtes\u00edt\u00e9sek be\u00e1ll\u00edt\u00e1sa', level=1)
    doc.add_paragraph(f'A Be\u00e1ll\u00edt\u00e1sok oldalon, az {q("\U0001f4e7 E-mail \u00e9rtes\u00edt\u00e9sek (SMTP)")} r\u00e9szben \u00e1ll\u00edthatja be, hogy a kapcsolatfelv\u00e9teli \u0171rlapon \u00e9rkez\u0151 \u00fczenetekr\u0151l kapjon-e e-mail \u00e9rtes\u00edt\u00e9st.')

    doc.add_heading('9.1 El\u0151felt\u00e9tel: e-mail fi\u00f3k l\u00e9trehoz\u00e1sa', level=2)
    doc.add_paragraph('El\u0151sz\u00f6r a t\u00e1rhelyszolg\u00e1ltat\u00f3n\u00e1l (rackhost.hu) l\u00e9tre kell hoznia egy e-mail fi\u00f3kot a parkoloabc.hu domainhez. P\u00e9ld\u00e1ul: info@parkoloabc.hu')
    doc.add_paragraph('L\u00e9p\u00e9sek:')
    for item in [
        'L\u00e9pjen be a rackhost.hu vez\u00e9rl\u0151pultra',
        f'Keresse meg az {q("E-mail fi\u00f3kok")} vagy {q("Levelez\u00e9s")} men\u00fcpontot',
        'Hozzon l\u00e9tre egy \u00faj e-mail fi\u00f3kot (pl. info@parkoloabc.hu)',
        'Jegyezze fel a jelsz\u00f3t',
    ]:
        doc.add_paragraph(item, style='List Number')

    doc.add_heading('9.2 SMTP be\u00e1ll\u00edt\u00e1sok kit\u00f6lt\u00e9se', level=2)
    add_table(doc, ['Mez\u0151', 'Mit \u00edrjon be?', 'P\u00e9lda'], [
        ['SMTP szerver', 'A levelez\u0151szerver c\u00edme', 'smtp.rackhost.hu'],
        ['SMTP port', 'A szerver portja', '587'],
        ['SMTP felhaszn\u00e1l\u00f3n\u00e9v', 'Az e-mail fi\u00f3k neve', 'info@parkoloabc.hu'],
        ['SMTP jelsz\u00f3', 'Az e-mail fi\u00f3k jelszava', '(amit a rackhostn\u00e1l be\u00e1ll\u00edtott)'],
        ['Felad\u00f3 e-mail c\u00edm', 'Kit\u0151l j\u00f6n a lev\u00e9l', 'info@parkoloabc.hu'],
        ['\u00c9rtes\u00edt\u00e9si e-mail', 'Hova \u00e9rkezzen az \u00e9rtes\u00edt\u00e9s', 'info@parkoloabc.hu'],
    ])

    doc.add_heading('9.3 Tesztel\u00e9s', level=2)
    doc.add_paragraph(f'A be\u00e1ll\u00edt\u00e1sok ment\u00e9se ut\u00e1n kattintson a {q("\U0001f4e7 Teszt e-mail k\u00fcld\u00e9se")} gombra. Ha minden helyes, z\u00f6ld pipa jelenik meg. Ha hiba van, piros X \u00e9s a hiba\u00fczenet.')
    add_tip(doc, 'Az \u0171rlap-\u00fczenetek az e-mail be\u00e1ll\u00edt\u00e1st\u00f3l f\u00fcggetlen\u00fcl mindig ment\u0151dnek az adatb\u00e1zisba, teh\u00e1t az \u00dczenetek men\u00fcben akkor is olvashat\u00f3k, ha az e-mail nem m\u0171k\u00f6dik.')

    # 10. Messages
    doc.add_heading('10. \u00dczenetek', level=1)
    doc.add_paragraph(f'Az {q("\u00dczenetek")} men\u00fcponton az \u00f6sszes kapcsolatfelv\u00e9teli \u00fczenet olvashat\u00f3, amit a l\u00e1togat\u00f3k az \u0171rlapon k\u00fcltek.')
    doc.add_paragraph('Minden \u00fczenet tartalmazza:')
    for item in ['A felad\u00f3 nev\u00e9t', 'E-mail c\u00edm\u00e9t', 'Telefonsz\u00e1m\u00e1t (ha megadta)', 'Az \u00fczenet sz\u00f6veg\u00e9t', 'Melyik oldalr\u00f3l k\u00fcldte', 'D\u00e1tum \u00e9s id\u0151']:
        doc.add_paragraph(item, style='List Bullet')
    doc.add_paragraph(f'Az \u00fczeneteket a {q("T\u00f6rl\u00e9s")} gombbal t\u00f6r\u00f6lheti.')

    # 11. Password
    doc.add_heading('11. Jelsz\u00f3 m\u00f3dos\u00edt\u00e1sa', level=1)
    doc.add_paragraph(f'A {q("Jelsz\u00f3")} men\u00fcponton megv\u00e1ltoztathatja az admin fel\u00fclet jelszav\u00e1t. Adja meg a jelenlegi jelsz\u00f3t, majd k\u00e9tszer az \u00fajat. Aj\u00e1nlott legal\u00e1bb 8 karakteres, bet\u0171ket \u00e9s sz\u00e1mokat tartalmaz\u00f3 jelsz\u00f3t haszn\u00e1lni.')
    add_tip(doc, 'Rendszeresen v\u00e1ltoztassa meg a jelszav\u00e1t a biztons\u00e1g \u00e9rdek\u00e9ben!', '\U0001f512 Biztons\u00e1g:')

    # 12. Deployment
    doc.add_heading('12. Weboldal friss\u00edt\u00e9se (telep\u00edt\u00e9s)', level=1)
    doc.add_paragraph('Ha a fejleszt\u0151 \u00faj verzi\u00f3t k\u00e9sz\u00edt a weboldalb\u00f3l, \u00d6n egyszer\u0171en friss\u00edtheti a weboldalt a mell\u00e9kelt Telep\u00edt\u0151 program seg\u00edts\u00e9g\u00e9vel.')

    doc.add_heading('12.1 A Telep\u00edt\u0151 program haszn\u00e1lata', level=2)
    doc.add_paragraph('L\u00e9p\u00e9sek:')
    for item in [
        f'Kattintson dupl\u00e1n a {q("ParkoloABC_Telepito.exe")} f\u00e1jlra',
        'Megjelenik egy ablak magyar nyelven',
        'A program automatikusan felt\u00f6lti a f\u00e1jlokat a szerverre',
        f'V\u00e1rjon, am\u00edg a {q("K\u00e9sz!")} \u00fczenet megjelenik',
        'Nyomja meg az Enter billenty\u0171t a bez\u00e1r\u00e1shoz',
    ]:
        doc.add_paragraph(item, style='List Number')
    add_tip(doc, 'A Telep\u00edt\u0151 programot \u00e9s a weboldal f\u00e1jlokat a fejleszt\u0151t\u0151l kapja meg egy mapp\u00e1ban.', '\U0001f4e6 Fontos:')

    # 13. FAQ
    doc.add_heading('13. Gyakran Ism\u00e9telt K\u00e9rd\u00e9sek', level=1)
    faq = [
        ('Nem l\u00e1tom a m\u00f3dos\u00edt\u00e1saimat a weboldalon!', 'A b\u00f6ng\u00e9sz\u0151 gyors\u00edt\u00f3t\u00e1razza a r\u00e9gi verzi\u00f3t. Nyomjon Ctrl+Shift+R billenty\u0171kombin\u00e1ci\u00f3t a friss\u00edt\u00e9shez.'),
        ('Elfelejtett jelsz\u00f3 \u2014 mit tegyek?', 'Vegye fel a kapcsolatot a weboldal fejleszt\u0151j\u00e9vel, aki k\u00f6zvetlen\u00fcl az adatb\u00e1zisban tudja vissza\u00e1ll\u00edtani.'),
        ('A k\u00e9p nem jelenik meg a weboldalon.', f'Ellen\u0151rizze, hogy a k\u00e9p fel van-e t\u00f6ltve a M\u00e9dia oldalon, \u00e9s a szekci\u00f3 a helyes URL-t haszn\u00e1lja (Tall\u00f3z\u00e1s gomb).'),
        ('Az oldal 404 hib\u00e1t mutat.', f'Ellen\u0151rizze, hogy az oldal {q("Publik\u00e1lt")} \u00e1llapotban van-e, \u00e9s a slug helyes-e.'),
        ('Az e-mail \u00e9rtes\u00edt\u00e9s nem m\u0171k\u00f6dik.', f'Ellen\u0151rizze az SMTP be\u00e1ll\u00edt\u00e1sokat, \u00e9s haszn\u00e1lja a {q("Teszt e-mail")} gombot.'),
        ('Hogyan v\u00e1ltoztatom meg a weboldal sz\u00edneit?', 'Be\u00e1ll\u00edt\u00e1sok \u2192 Els\u0151dleges sz\u00edn \u00e9s M\u00e1sodlagos sz\u00edn. Kattintson a sz\u00ednv\u00e1laszt\u00f3ra, v\u00e1lasszon, majd Ment\u00e9s.'),
        ('A men\u00fcben nem jelenik meg az almen\u00fc.', f'Ellen\u0151rizze, hogy a men\u00fcpont {q("Sz\u00fcl\u0151")} mez\u0151j\u00e9ben a helyes sz\u00fcl\u0151 men\u00fcpont van-e kiv\u00e1lasztva.'),
        ('Hogyan vehetek fel k\u00e9pgal\u00e9ri\u00e1t?', f'Hozzon l\u00e9tre egy {q("Gal\u00e9ria")} t\u00edpus\u00fa szekci\u00f3t, majd adjon hozz\u00e1 k\u00e9peket a {q("+ K\u00e9p hozz\u00e1ad\u00e1sa")} gombbal.'),
    ]
    for question, answer in faq:
        p_q = doc.add_paragraph()
        run_q = p_q.add_run(f'K: {question}')
        run_q.font.bold = True
        p_a = doc.add_paragraph(f'V: {answer}')
        p_a.paragraph_format.space_after = Pt(12)

    path = os.path.join(OUTPUT_DIR, 'ParkoloABC_Felhasznaloi_Kezikonyv.docx')
    doc.save(path)
    print(f'  \u2713 {path}')


# =====================================================================
# 2. DEVELOPER DOCUMENTATION
# =====================================================================
def generate_dev_docs():
    doc = Document()
    setup_styles(doc)
    add_cover_page(doc, 'Parkol\u00f3ABC.hu', 'Fejleszt\u0151i Dokument\u00e1ci\u00f3', '1.0')

    doc.add_heading('Tartalomjegyz\u00e9k', level=1)
    for item in [
        '1. Projekt \u00e1ttekint\u00e9s',
        '2. Architekt\u00fara',
        '3. F\u00e1jlstrukt\u00fara',
        '4. Adatb\u00e1zis s\u00e9ma',
        '5. Routing \u00e9s front controller',
        '6. Szekci\u00f3 rendszer',
        '7. Admin fel\u00fclet',
        '8. SEO implement\u00e1ci\u00f3',
        '9. E-mail rendszer',
        '10. Biztons\u00e1gi megold\u00e1sok',
        '11. Telep\u00edt\u00e9s (deployment)',
        '12. Karbantart\u00e1s \u00e9s b\u0151v\u00edt\u00e9s',
    ]:
        doc.add_paragraph(item)
    doc.add_page_break()

    # 1. Overview
    doc.add_heading('1. Projekt \u00e1ttekint\u00e9s', level=1)
    doc.add_paragraph('A Parkol\u00f3ABC.hu egy egyedi fejleszt\u00e9s\u0171 micro-CMS (tartalomkezel\u0151 rendszer), amely egy kor\u00e1bbi WordPress telep\u00edt\u00e9st v\u00e1lt ki. A rendszer PHP 8.x-en fut, MariaDB/MySQL adatb\u00e1zissal, shared hosting k\u00f6rnyezetben (rackhost.hu).')
    add_table(doc, ['Param\u00e9ter', '\u00c9rt\u00e9k'], [
        ['Nyelv', 'PHP 8.x (vanilla, framework n\u00e9lk\u00fcl)'],
        ['Adatb\u00e1zis', 'MariaDB/MySQL (utf8mb4)'],
        ['Hosting', 'rackhost.hu (shared hosting, FTP/SFTP)'],
        ['Domain', 'parkoloabc.hu / www.parkoloabc.hu'],
        ['Webroot', 'ftp.example.com (FTP)'],
        ['Admin fel\u00fclet', '/admin/ \u00fatvonal alatt'],
        ['E-mail', 'PHPMailer SMTP (opcion\u00e1lis)'],
    ])

    doc.add_heading('Tervez\u00e9si elvek', level=2)
    for item in [
        'Teljes szerver-oldali renderel\u00e9s (SSR) \u2014 SEO-first megk\u00f6zel\u00edt\u00e9s',
        'Egyetlen front controller (index.php) \u2014 egyszer\u0171 routing',
        'Modul\u00e1ris szekci\u00f3-rendszer \u2014 \u00faj t\u00edpus = \u00faj PHP f\u00e1jl',
        'Minim\u00e1lis JavaScript \u2014 csak interakt\u00edv elemekhez (drag & drop, hamburger men\u00fc)',
        'Nincsenek k\u00fcls\u0151 PHP f\u00fcgg\u0151s\u00e9gek (csak PHPMailer)',
        'Nincs build step \u2014 a k\u00f3d k\u00f6zvetlen\u00fcl futtathat\u00f3',
    ]:
        doc.add_paragraph(item, style='List Bullet')

    # 2. Architecture
    doc.add_heading('2. Architekt\u00fara', level=1)
    doc.add_heading('Request \u00e9letciklus', level=2)
    steps = [
        'A .htaccess minden k\u00e9r\u00e9st az index.php-ra ir\u00e1ny\u00edt (RewriteRule)',
        'Az index.php bet\u00f6lti a DB kapcsolatot (config/db.php)',
        'A Router::resolve() leszedi a slug-ot az URL-b\u0151l',
        'Speci\u00e1lis \u00fatvonalak (sitemap.xml, robots.txt, kulcsszo/) kezel\u00e9se',
        'A Renderer::getSettings() \u00e9s getMenus() bet\u00f6lti a glob\u00e1lis adatokat',
        'POST kezel\u00e9s (kapcsolatfelv\u00e9teli \u0171rlap, CSRF ellen\u0151rz\u00e9ssel)',
        'A Router::getPage() lek\u00e9ri az oldal adatait a slug alapj\u00e1n',
        'A Renderer::getSections() lek\u00e9ri az oldal szekci\u00f3it',
        'A Renderer::renderAllSections() rendereli a szekci\u00f3kat HTML-l\u00e9',
        'A SEO::buildMeta() \u00f6ssze\u00e1ll\u00edtja a meta tag-eket',
        'A templates/base.php \u00f6sszef\u0171zi a teljes HTML oldalt',
        'A b\u00f6ng\u00e9sz\u0151 megkapja a k\u00e9sz HTML-t',
    ]
    for i, step in enumerate(steps, 1):
        doc.add_paragraph(f'{i}. {step}')

    doc.add_heading('Admin fel\u00fclet \u00e9letciklusa', level=2)
    doc.add_paragraph('Az /admin/ alatt k\u00fcl\u00f6n PHP f\u00e1jlok kezelik az egyes funkci\u00f3kat. Minden admin f\u00e1jl az auth.php-t require-olja, ami session-alap\u00fa autentik\u00e1ci\u00f3t biztos\u00edt. Ha a felhaszn\u00e1l\u00f3 nincs bejelentkezve, \u00e1tir\u00e1ny\u00edt\u00e1s t\u00f6rt\u00e9nik a login oldalra.')

    # 3. File structure
    doc.add_heading('3. F\u00e1jlstrukt\u00fara', level=1)
    files = [
        ['index.php', 'Front controller \u2014 minden nyilv\u00e1nos k\u00e9r\u00e9s ide \u00e9rkezik'],
        ['.htaccess', 'Apache rewrite szab\u00e1lyok (clean URL-ek)'],
        ['.env', 'K\u00f6rnyezeti v\u00e1ltoz\u00f3k (DB hozz\u00e1f\u00e9r\u00e9s) \u2014 NEM COMMITOLHAT\u00d3'],
        ['config/db.php', 'Adatb\u00e1zis-kapcsolat be\u00e1ll\u00edt\u00e1sa, .env bet\u00f6lt\u00e9se'],
        ['config/templates.php', 'Szekci\u00f3 t\u00edpusok regisztr\u00e1ci\u00f3ja'],
        ['core/router.php', 'URL \u2192 oldal felold\u00e1s (slug keres\u00e9s)'],
        ['core/renderer.php', 'HTML renderel\u00e9s (szekci\u00f3k \u00f6ssze\u00e1ll\u00edt\u00e1sa)'],
        ['core/seo.php', 'Meta tag-ek, JSON-LD, sitemap.xml, robots.txt gener\u00e1l\u00e1s'],
        ['core/sanitize.php', 'Input sz\u0171r\u00e9s \u00e9s tiszt\u00edt\u00e1s'],
        ['templates/base.php', 'HTML v\u00e1z (doctype, head, header, nav, main, footer)'],
        ['templates/404.php', '404 hibaoldal sablon'],
        ['templates/keyword_results.php', 'Kulcssz\u00f3 keres\u00e9si eredm\u00e9nyek sablon'],
        ['templates/sections/*.php', 'Szekci\u00f3 sablonok (24 db, t\u00edpusonk\u00e9nt egy f\u00e1jl)'],
        ['admin/index.php', 'Admin ir\u00e1ny\u00edt\u00f3pult (dashboard)'],
        ['admin/auth.php', 'Bejelentkez\u00e9s \u00e9s session kezel\u00e9s'],
        ['admin/pages.php', 'Oldalak list\u00e1z\u00e1sa \u00e9s CRUD'],
        ['admin/page-edit.php', 'Oldal szerkeszt\u0151 (szekci\u00f3k kezel\u00e9s\u00e9vel)'],
        ['admin/settings.php', 'Glob\u00e1lis be\u00e1ll\u00edt\u00e1sok (+ SMTP konfigur\u00e1ci\u00f3)'],
        ['admin/api.php', 'AJAX API v\u00e9gpontok (szekci\u00f3 sorrend, SMTP teszt)'],
        ['admin/media.php', 'M\u00e9dia (k\u00e9p) kezel\u00e9s, felt\u00f6lt\u00e9s'],
        ['admin/menus.php', 'Navig\u00e1ci\u00f3s men\u00fc kezel\u00e9s (t\u00f6bbszint\u0171)'],
        ['admin/messages.php', 'Be\u00e9rkezett \u00fczenetek list\u00e1z\u00e1sa'],
        ['admin/password.php', 'Jelsz\u00f3v\u00e1ltoztat\u00e1s'],
        ['admin/components.php', 'Szekci\u00f3t\u00edpusok vizu\u00e1lis katal\u00f3gusa'],
        ['admin/includes/header.php', 'Admin fejl\u00e9c \u00e9s navig\u00e1ci\u00f3'],
        ['admin/includes/footer.php', 'Admin l\u00e1bl\u00e9c \u00e9s JS'],
        ['admin/includes/csrf.php', 'CSRF token gener\u00e1l\u00e1s \u00e9s ellen\u0151rz\u00e9s'],
        ['admin/assets/admin.css', 'Admin fel\u00fclet st\u00edlusok'],
        ['admin/assets/admin.js', 'Admin fel\u00fclet szkriptek'],
        ['assets/css/style.css', 'Nyilv\u00e1nos weboldal st\u00edlusok'],
        ['assets/uploads/', 'Felt\u00f6lt\u00f6tt m\u00e9diaf\u00e1jlok k\u00f6nyvt\u00e1ra'],
        ['lib/PHPMailer/', 'PHPMailer k\u00f6nyvt\u00e1r (SMTP e-mail k\u00fcld\u00e9s)'],
        ['database/*.sql', 'Adatb\u00e1zis s\u00e9ma \u00e9s migr\u00e1ci\u00f3s f\u00e1jlok'],
        ['deploy.sh', 'Linux deployment szkript (FTP felt\u00f6lt\u00e9s)'],
    ]
    add_table(doc, ['F\u00e1jl / Mappa', 'Funkci\u00f3'], files)

    # 4. Database
    doc.add_heading('4. Adatb\u00e1zis s\u00e9ma', level=1)
    doc.add_paragraph('Az adatb\u00e1zis 7 t\u00e1bl\u00e1t tartalmaz:')

    for tname, cols in [
        ('users', [
            ['id', 'INT PK AUTO_INCREMENT', 'Egyedi azonos\u00edt\u00f3'],
            ['username', 'VARCHAR(50) UNIQUE', 'Bejelentkez\u00e9si n\u00e9v'],
            ['password_hash', 'VARCHAR(255)', 'bcrypt hash'],
            ['display_name', 'VARCHAR(100)', 'Megjelen\u00edt\u0151 n\u00e9v'],
            ['created_at / updated_at', 'TIMESTAMP', 'D\u00e1tumok'],
        ]),
        ('site_settings', [
            ['id', 'INT PK', 'Egyedi azonos\u00edt\u00f3'],
            ['setting_key', 'VARCHAR(100) UNIQUE', 'Be\u00e1ll\u00edt\u00e1s kulcsa (pl. site_name, smtp_host)'],
            ['setting_value', 'TEXT', 'Be\u00e1ll\u00edt\u00e1s \u00e9rt\u00e9ke'],
        ]),
        ('pages', [
            ['id', 'INT PK', 'Egyedi azonos\u00edt\u00f3'],
            ['slug', 'VARCHAR(255) UNIQUE', 'URL slug (pl. szolgaltatasaink)'],
            ['title', 'VARCHAR(255)', 'Oldal c\u00edme'],
            ['meta_title / meta_description / meta_keywords', 'VARCHAR / TEXT', 'SEO mez\u0151k'],
            ['og_title / og_description / og_image', 'VARCHAR / TEXT', 'Open Graph mez\u0151k'],
            ['status', "ENUM('published','draft')", '\u00c1llapot'],
            ['sort_order', 'INT', 'Sorrend'],
        ]),
        ('sections', [
            ['id', 'INT PK', 'Egyedi azonos\u00edt\u00f3'],
            ['page_id', 'INT FK \u2192 pages.id', 'Melyik oldalhoz tartozik (CASCADE t\u00f6rl\u00e9s)'],
            ['type', 'VARCHAR(50)', 'Szekci\u00f3 t\u00edpusa (pl. hero, text, gallery)'],
            ['content', 'JSON', 'A szekci\u00f3 tartalma JSON form\u00e1tumban'],
            ['sort_order', 'INT', 'Sorrend az oldalon bel\u00fcl'],
        ]),
        ('media', [
            ['id', 'INT PK', 'Egyedi azonos\u00edt\u00f3'],
            ['filename', 'VARCHAR(255)', 'F\u00e1jln\u00e9v a szerveren'],
            ['original_name', 'VARCHAR(255)', 'Eredeti f\u00e1jln\u00e9v'],
            ['alt_text', 'VARCHAR(255)', 'SEO alt sz\u00f6veg'],
            ['is_featured', 'TINYINT(1)', 'Kiemelt-e (Hero diavet\u00edt\u00e9shez)'],
        ]),
        ('menus', [
            ['id', 'INT PK', 'Egyedi azonos\u00edt\u00f3'],
            ['label', 'VARCHAR(100)', 'Megjelen\u0151 sz\u00f6veg'],
            ['url', 'VARCHAR(500)', 'Egyedi URL'],
            ['page_id', 'INT FK \u2192 pages.id', 'Hivatkozott oldal'],
            ['parent_id', 'INT FK \u2192 menus.id', 'Sz\u00fcl\u0151 men\u00fcpont (ha almen\u00fc)'],
            ['sort_order', 'INT', 'Sorrend'],
        ]),
        ('contact_messages', [
            ['id', 'INT PK', 'Egyedi azonos\u00edt\u00f3'],
            ['sender_name / sender_email / sender_phone', 'VARCHAR', 'K\u00fcld\u0151 adatai'],
            ['message', 'TEXT', '\u00dczenet sz\u00f6veg'],
            ['page_slug', 'VARCHAR(255)', 'Melyik oldalr\u00f3l k\u00fcldt\u00e9k'],
            ['is_read', 'TINYINT(1)', 'Olvasott-e'],
            ['created_at', 'TIMESTAMP', '\u00c9rkez\u00e9s d\u00e1tuma'],
        ]),
    ]:
        doc.add_heading(tname, level=3)
        add_table(doc, ['Oszlop', 'T\u00edpus', 'Le\u00edr\u00e1s'], cols)

    # 5. Routing
    doc.add_heading('5. Routing \u00e9s front controller', level=1)
    doc.add_paragraph('A .htaccess az \u00f6sszes k\u00e9r\u00e9st az index.php-ra ir\u00e1ny\u00edtja, kiv\u00e9ve a l\u00e9tez\u0151 f\u00e1jlokat \u00e9s k\u00f6nyvt\u00e1rakat (assets/, admin/, uploads/).')
    doc.add_paragraph('A Router::resolve() lev\u00e1gja a query string-et \u00e9s a vezet\u0151/z\u00e1r\u00f3 perjelet, majd visszaadja a slug-ot. Ha a slug \u00fcres, a home slug-ot adja vissza.')
    doc.add_heading('Speci\u00e1lis \u00fatvonalak', level=2)
    add_table(doc, ['\u00datvonal', 'Kezel\u0151'], [
        ['sitemap.xml', 'SEO::generateSitemap() \u2014 dinamikusan gener\u00e1lt XML sitemap'],
        ['robots.txt', 'SEO::generateRobots() \u2014 dinamikus robots.txt'],
        ['kulcsszo/{keyword}', 'Kulcssz\u00f3 keres\u00e9s \u2014 list\u00e1zza az adott kulcssz\u00f3t tartalmaz\u00f3 oldalakat'],
    ])

    # 6. Section system
    doc.add_heading('6. Szekci\u00f3 rendszer', level=1)
    doc.add_paragraph('A szekci\u00f3 rendszer a CMS mag koncepci\u00f3ja. Minden oldal szekci\u00f3k sorozat\u00e1b\u00f3l \u00e1ll. Egy szekci\u00f3 egy t\u00edpussal (type) \u00e9s egy JSON tartalommal (content) rendelkezik.')
    doc.add_heading('\u00daj szekci\u00f3t\u00edpus hozz\u00e1ad\u00e1sa', level=2)
    doc.add_paragraph('H\u00e1rom helyen kell m\u00f3dos\u00edtani:')
    for item in [
        'templates/sections/{type}.php \u2014 a megjelen\u00edt\u0151 sablon l\u00e9trehoz\u00e1sa',
        'admin/page-edit.php \u2014 a szerkeszt\u0151 \u0171rlap mez\u0151inek hozz\u00e1ad\u00e1sa (switch-case blokk)',
        'admin/components.php \u2014 a t\u00edpus regisztr\u00e1l\u00e1sa a label, description \u00e9s fields t\u00f6mb\u00f6kben',
    ]:
        doc.add_paragraph(item, style='List Number')
    doc.add_paragraph('A szekci\u00f3 sablon megkapja a $content (JSON-b\u00f3l dek\u00f3dolt t\u00f6mb), $section (szekci\u00f3 sor) \u00e9s $pdo (adatb\u00e1zis kapcsolat) v\u00e1ltoz\u00f3kat.')

    # 7. Admin
    doc.add_heading('7. Admin fel\u00fclet', level=1)
    doc.add_paragraph('Az admin fel\u00fclet az /admin/ \u00fatvonal alatt \u00e9rhet\u0151 el. Session-alap\u00fa autentik\u00e1ci\u00f3t haszn\u00e1l (PHP nat\u00edv session, bcrypt jelsz\u00f3 hash).')
    doc.add_heading('CSRF v\u00e9delem', level=2)
    doc.add_paragraph('Minden POST form tartalmaz egy CSRF tokent (rejtett mez\u0151). A csrfField() f\u00fcggv\u00e9ny gener\u00e1lja, a csrfVerify() ellen\u0151rzi. A token a session-ben t\u00e1rol\u00f3dik.')
    doc.add_heading('API v\u00e9gpontok (admin/api.php)', level=2)
    add_table(doc, ['Action', 'Met\u00f3dus', 'Le\u00edr\u00e1s'], [
        ['reorder_sections', 'POST', 'Szekci\u00f3k sorrendj\u00e9nek friss\u00edt\u00e9se (drag & drop)'],
        ['test_smtp', 'POST (JSON)', 'Teszt e-mail k\u00fcld\u00e9se az SMTP be\u00e1ll\u00edt\u00e1sokkal'],
    ])

    # 8. SEO
    doc.add_heading('8. SEO implement\u00e1ci\u00f3', level=1)
    doc.add_paragraph('A rendszer az al\u00e1bbi SEO funkci\u00f3kat biztos\u00edtja automatikusan:')
    add_table(doc, ['Funkci\u00f3', 'Megval\u00f3s\u00edt\u00e1s'], [
        ['<title> tag', 'Oldal meta_title \u2192 oldal title \u2192 site_name fallback'],
        ['<meta description>', 'Oldal \u2192 glob\u00e1lis be\u00e1ll\u00edt\u00e1s fallback'],
        ['Open Graph tag-ek', 'og:title, og:description, og:image, og:url'],
        ['Twitter Card', 'summary_large_image t\u00edpus'],
        ['Canonical URL', 'Automatikus, az aktu\u00e1lis slug alapj\u00e1n'],
        ['JSON-LD', 'Organization + WebSite + WebPage struktur\u00e1lt adatok'],
        ['FAQ JSON-LD', 'Automatikus accordion szekci\u00f3kb\u00f3l'],
        ['sitemap.xml', 'Dinamikusan gener\u00e1lt, minden publik\u00e1lt oldallal'],
        ['robots.txt', 'Dinamikus, sitemap URL-lel'],
        ['Szemantikus HTML', '<header>, <main>, <section>, <article>, <footer>'],
        ['Kulcssz\u00f3 felh\u0151', 'Automatikus bels\u0151 linkel\u00e9s kulcsszavak alapj\u00e1n'],
    ])

    # 9. Email
    doc.add_heading('9. E-mail rendszer', level=1)
    doc.add_paragraph('Az e-mail k\u00fcld\u00e9s opcion\u00e1lis \u2014 a kapcsolatfelv\u00e9teli \u0171rlap \u00fczenetei mindig ment\u0151dnek az adatb\u00e1zisba. Ha az SMTP be\u00e1ll\u00edt\u00e1sok ki vannak t\u00f6ltve (site_settings t\u00e1bl\u00e1ban vagy .env-ben), a rendszer PHPMailer-rel SMTP-n kereszt\u00fcl k\u00fcld \u00e9rtes\u00edt\u00e9st.')
    doc.add_heading('Konfigur\u00e1ci\u00f3 priorit\u00e1s', level=2)
    doc.add_paragraph('1. site_settings t\u00e1bla (admin fel\u00fcleten szerkeszthet\u0151)')
    doc.add_paragraph('2. .env f\u00e1jl (fallback, ha a DB mez\u0151k \u00fcresek)')

    # 10. Security
    doc.add_heading('10. Biztons\u00e1gi megold\u00e1sok', level=1)
    add_table(doc, ['Ter\u00fclet', 'Megold\u00e1s'], [
        ['Jelsz\u00f3 t\u00e1rol\u00e1s', 'bcrypt hash (password_hash / password_verify)'],
        ['CSRF v\u00e9delem', 'Session-alap\u00fa token minden POST k\u00e9r\u00e9sn\u00e9l'],
        ['SQL injection', 'PDO prepared statements mindenhol'],
        ['XSS v\u00e9delem', 'htmlspecialchars() minden kimentn\u00e9l'],
        ['Session fixation', 'session_regenerate_id() bejelentkez\u00e9skor'],
        ['K\u00e9p felt\u00f6lt\u00e9s', 'MIME t\u00edpus ellen\u0151rz\u00e9s, egyedi f\u00e1jln\u00e9v (UUID), m\u00e9retkorl\u00e1t'],
        ['Admin hozz\u00e1f\u00e9r\u00e9s', 'requireLogin() middleware minden admin oldalon'],
        ['K\u00e9pek v\u00e9delme', 'Right-click/drag/long-press letilt\u00e1s CSS+JS-sel'],
        ['Input sz\u0171r\u00e9s', 'sanitize modul (trim, strip_tags ahol sz\u00fcks\u00e9ges)'],
    ])

    # 11. Deployment
    doc.add_heading('11. Telep\u00edt\u00e9s (deployment)', level=1)
    doc.add_paragraph('A telep\u00edt\u00e9s FTP-n kereszt\u00fcl t\u00f6rt\u00e9nik a rackhost.hu szerverre. K\u00e9t eszk\u00f6z \u00e1ll rendelkez\u00e9sre:')
    doc.add_heading('deploy.sh (Linux/Mac)', level=2)
    doc.add_paragraph('Bash szkript, amely curl-lel FTP-n felt\u00f6lti az \u00f6sszes f\u00e1jlt. Futtat\u00e1s: bash deploy.sh a projekt gy\u00f6ker\u00e9ben.')
    doc.add_heading('ParkoloABC_Telepito.exe (Windows)', level=2)
    doc.add_paragraph('Go-ban \u00edrt, Windows-ra cross-compil\u00e1lt exe. A weboldal mapp\u00e1j\u00e1ban kell elhelyezni, majd dupla kattint\u00e1ssal futtathat\u00f3. Automatikusan felt\u00f6lti a f\u00e1jlokat, \u00e9s magyar nyelv\u0171 \u00e1llapotjelz\u00e9st ad.')
    doc.add_heading('Adatb\u00e1zis migr\u00e1ci\u00f3', level=2)
    doc.add_paragraph('Az adatb\u00e1zis inicializ\u00e1l\u00e1s a database/setup.php szkripttel t\u00f6rt\u00e9nik. A k\u00e9s\u0151bbi s\u00e9ma m\u00f3dos\u00edt\u00e1sok migr\u00e1ci\u00f3s f\u00e1jlokkal kezelend\u0151k (database/XXX_migration_vN.sql + migrate-vN.php).')

    # 12. Maintenance
    doc.add_heading('12. Karbantart\u00e1s \u00e9s b\u0151v\u00edt\u00e9s', level=1)
    doc.add_heading('Cache', level=2)
    doc.add_paragraph('A CSS \u00e9s JS f\u00e1jlok query string verzi\u00f3sz\u00e1mmal vannak ell\u00e1tva (pl. style.css?v=2). \u00daj verzi\u00f3 deployol\u00e1skor n\u00f6velje a verzi\u00f3sz\u00e1mot a templates/base.php-ban.')
    doc.add_heading('Ment\u00e9s (backup)', level=2)
    doc.add_paragraph('Adatb\u00e1zis: mysqldump vagy a rackhost phpMyAdmin fel\u00fclet\u00e9n export\u00e1lhat\u00f3. F\u00e1jlok: az assets/uploads/ k\u00f6nyvt\u00e1rat \u00e9rdemes rendszeresen let\u00f6lteni FTP-n.')

    path = os.path.join(OUTPUT_DIR, 'ParkoloABC_Fejlesztoi_Dokumentacio.docx')
    doc.save(path)
    print(f'  \u2713 {path}')


# =====================================================================
# 3. FEATURE LIST
# =====================================================================
def generate_feature_list():
    doc = Document()
    setup_styles(doc)
    add_cover_page(doc, 'Parkol\u00f3ABC.hu', 'Funkci\u00f3lista', '1.0')

    doc.add_heading('A weboldal funkci\u00f3i', level=1)
    doc.add_paragraph('Az al\u00e1bbiakban a Parkol\u00f3ABC.hu weboldal \u00f6sszes funkci\u00f3j\u00e1nak \u00e1tfog\u00f3 list\u00e1ja olvashat\u00f3, kateg\u00f3ri\u00e1nk\u00e9nt csoportos\u00edtva.')

    # Public website
    doc.add_heading('1. Nyilv\u00e1nos weboldal', level=1)
    doc.add_heading('1.1 \u00c1ltal\u00e1nos', level=2)
    add_table(doc, ['Funkci\u00f3', 'Le\u00edr\u00e1s'], [
        ['Reszponz\u00edv design', 'A weboldal minden eszk\u00f6z\u00f6n (telefon, tablet, asztali g\u00e9p) j\u00f3l jelenik meg.'],
        ['Gyors bet\u00f6lt\u00e9s', 'Szerver-oldali renderel\u00e9s, nincs JavaScript framework, minim\u00e1lis HTTP k\u00e9r\u00e9s.'],
        ['Clean URL-ek', 'Olvashat\u00f3 webc\u00edmek (pl. /szolgaltatasaink).'],
        ['Automatikus 404 oldal', 'Ha egy URL nem l\u00e9tezik, egyedi hibaoldal jelenik meg.'],
        ['K\u00e9plet\u00f6lt\u00e9s-v\u00e9delem', 'A k\u00e9pek jobb-klikkel, h\u00faz\u00e1ssal \u00e9s hossz\u00fa nyom\u00e1ssal nem menthet\u0151k el.'],
        ['T\u00f6bbszint\u0171 men\u00fcrendszer', 'A navig\u00e1ci\u00f3s men\u00fc ak\u00e1r 5 szint m\u00e9lys\u00e9gig t\u00e1mogatja az almen\u00fcket.'],
        ['Mobil hamburger men\u00fc', 'Mobil eszk\u00f6z\u00f6n a men\u00fc hamburger ikonra kattintva ny\u00edlik le.'],
        ['L\u00e1bl\u00e9c oldalt\u00e9rk\u00e9p', 'Az oldal alj\u00e1n \u00f6sszecsukhat\u00f3 oldalt\u00e9rk\u00e9p a men\u00fcszerkezet alapj\u00e1n.'],
        ['Logo megjelen\u00edt\u00e9s', 'A fejl\u00e9cben v\u00e1laszthat\u00f3: csak n\u00e9v, csak logo, vagy mindk\u00e9t.'],
    ])

    doc.add_heading('1.2 SEO (Keres\u0151-optimaliz\u00e1l\u00e1s)', level=2)
    add_table(doc, ['Funkci\u00f3', 'Le\u00edr\u00e1s'], [
        ['Egyedi meta c\u00edmek', 'Minden oldalhoz saj\u00e1t <title> tag \u00e1ll\u00edthat\u00f3 be.'],
        ['Meta le\u00edr\u00e1s', 'Minden oldalhoz saj\u00e1t meta description a Google tal\u00e1latokhoz.'],
        ['Meta kulcsszavak', 'Kulcsszavak megad\u00e1sa oldalank\u00e9nt.'],
        ['Open Graph tag-ek', 'Facebook/LinkedIn megoszt\u00e1skor megjelen\u0151 c\u00edm, le\u00edr\u00e1s \u00e9s k\u00e9p.'],
        ['Twitter Card', 'Twitter megoszt\u00e1skor megjelen\u0151 el\u0151n\u00e9zeti k\u00e1rtya.'],
        ['Canonical URL', 'Automatikus kanonikus URL, hogy a Google ne l\u00e1sson duplik\u00e1lt tartalmat.'],
        ['JSON-LD struktur\u00e1lt adatok', 'Organization, WebSite, WebPage s\u00e9m\u00e1k \u2014 Google Rich Results.'],
        ['FAQ struktur\u00e1lt adatok', 'A harmonika szekci\u00f3kb\u00f3l automatikusan gener\u00e1lva.'],
        ['Automatikus sitemap.xml', 'Dinamikusan gener\u00e1lt XML sitemap a Google Search Console-hoz.'],
        ['Automatikus robots.txt', 'Keres\u0151robotok sz\u00e1m\u00e1ra \u00fatmutat\u00e1s + sitemap hivatkoz\u00e1s.'],
        ['Szemantikus HTML5', '<header>, <main>, <section>, <article>, <footer> c\u00edmk\u00e9k.'],
        ['Alt sz\u00f6veg minden k\u00e9pen', 'SEO \u00e9s akad\u00e1lymentess\u00e9g \u2014 a m\u00e9diakezel\u0151ben megadhat\u00f3.'],
        ['Kulcssz\u00f3 felh\u0151', 'Automatikus bels\u0151 linkel\u00e9s a leggyakoribb kulcsszavak alapj\u00e1n.'],
        ['Rejtett SEO sz\u00f6veg', 'Google indexeli, de a felhaszn\u00e1l\u00f3 csak gombra kattintva l\u00e1tja.'],
    ])

    doc.add_heading('1.3 Tartalomtipusok (szekci\u00f3k)', level=2)
    add_table(doc, ['Szekci\u00f3 t\u00edpus', 'Le\u00edr\u00e1s'], [
        ['Hero (fejl\u00e9ck\u00e9p)', 'Teljes sz\u00e9less\u00e9g\u0171 fejl\u00e9ck\u00e9p c\u00edmsorral, alc\u00edmmel \u00e9s CTA gombbal.'],
        ['Hero diavet\u00edt\u00e9s', 'Automatikus k\u00e9pv\u00e1lt\u00e1sos diavet\u00edt\u00e9s, navig\u00e1ci\u00f3s dobozokkal.'],
        ['Sz\u00f6veg', 'Gazdag sz\u00f6vegblokk c\u00edmsorral.'],
        ['K\u00e9p + sz\u00f6veg', 'K\u00e9p \u00e9s sz\u00f6veg egym\u00e1s mellett.'],
        ['K\u00e9t oszlop', 'K\u00e9t oszlopos sz\u00f6vegelrendez\u00e9s.'],
        ['K\u00e1rty\u00e1k', 'Szolg\u00e1ltat\u00e1s-k\u00e1rty\u00e1k r\u00e1csban.'],
        ['Gal\u00e9ria', 'K\u00e9pgal\u00e9ria r\u00e1cs elrendez\u00e9sben.'],
        ['Referencia gal\u00e9ria', 'Projektek alapj\u00e1n csoportos\u00edtott gal\u00e9ria.'],
        ['Term\u00e9kr\u00e1cs', 'Term\u00e9kk\u00e1rty\u00e1k k\u00e9ppel, hover-re le\u00edr\u00e1ssal.'],
        ['CTA s\u00e1v', 'Cselekv\u00e9sre \u00f6szt\u00f6nz\u0151 sz\u00ednes s\u00e1v gombbal.'],
        ['Links\u00e1v', 'Sz\u00e9les sz\u00ednes s\u00e1v hivatkoz\u00e1ssal.'],
        ['Fut\u00f3 sz\u00f6veg (ticker)', 'V\u00edzszintesen fut\u00f3 h\u00edrszalag, random sorrendben.'],
        ['Harmonika (GYIK)', 'Leny\u00edl\u00f3 k\u00e9rd\u00e9s-v\u00e1lasz blokkok.'],
        ['Vide\u00f3', 'YouTube vagy Vimeo vide\u00f3 be\u00e1gyaz\u00e1s.'],
        ['Elv\u00e1laszt\u00f3', 'Vizu\u00e1lis elv\u00e1laszt\u00f3.'],
        ['V\u00e9lem\u00e9nyek', '\u00dcgyf\u00e9lv\u00e9lem\u00e9nyek k\u00e1rty\u00e1kon.'],
        ['Sz\u00e1mok / Statisztika', 'Kiemelked\u0151 sz\u00e1madatok.'],
        ['Oldal/cikk lista', 'Automatikus oldal-list\u00e1z\u00e1s.'],
        ['T\u00e9rk\u00e9p', 'Google Maps be\u00e1gyaz\u00e1s.'],
        ['Kapcsolat \u0171rlap', '\u00dczenetk\u00fcld\u0151 \u0171rlap.'],
        ['Kulcssz\u00f3 felh\u0151', 'Automatikusan gener\u00e1lt kulcssz\u00f3-linkek.'],
        ['Rejtett SEO sz\u00f6veg', 'Keres\u0151 \u00e1ltal indexelt, de rejtett sz\u00f6veg.'],
        ['Oldalt\u00e9rk\u00e9p', 'Automatikus hierarchikus oldalt\u00e9rk\u00e9p.'],
        ['Tud\u00e1smorzsak', 'Kis tud\u00e1sdobozok random sorrendben.'],
    ])

    doc.add_heading('1.4 Kapcsolatfelv\u00e9tel', level=2)
    add_table(doc, ['Funkci\u00f3', 'Le\u00edr\u00e1s'], [
        ['Kapcsolat \u0171rlap', 'N\u00e9v, e-mail, telefon, \u00fczenet mez\u0151kkel.'],
        ['\u00dczenet ment\u00e9s adatb\u00e1zisba', 'Minden \u00fczenet az adatb\u00e1zisba ker\u00fcl, nem veszhet el.'],
        ['E-mail \u00e9rtes\u00edt\u00e9s', 'Opcion\u00e1lis \u2014 SMTP-n kereszt\u00fcl \u00e9rtes\u00edt\u0151 e-mail.'],
        ['Reply-To fejl\u00e9c', 'Az e-mail v\u00e1lasz gomb k\u00f6zvetlen\u00fcl a k\u00fcld\u0151nek \u00edr.'],
        ['Spam v\u00e9delem', 'CSRF token \u00e9s honeypot mez\u0151.'],
    ])

    # Admin panel
    doc.add_heading('2. Adminisztr\u00e1ci\u00f3s fel\u00fclet', level=1)
    doc.add_heading('2.1 Ir\u00e1ny\u00edt\u00f3pult', level=2)
    add_table(doc, ['Funkci\u00f3', 'Le\u00edr\u00e1s'], [
        ['\u00d6sszefoglal\u00f3 k\u00e1rty\u00e1k', 'Oldalak, szekci\u00f3k, m\u00e9dia, men\u00fcelemek \u00e9s \u00fczenetek sz\u00e1ma.'],
        ['Gyors navig\u00e1ci\u00f3', 'K\u00e1rty\u00e1kra kattintva a megfelel\u0151 kezel\u0151oldalra juthat.'],
    ])
    doc.add_heading('2.2 Oldalkezel\u00e9s', level=2)
    add_table(doc, ['Funkci\u00f3', 'Le\u00edr\u00e1s'], [
        ['Oldal lista', 'T\u00e1bl\u00e1zatos lista c\u00edmmel, slug-gal, \u00e1llapottal \u00e9s m\u0171veletekkel.'],
        ['Oldal l\u00e9trehoz\u00e1sa', 'C\u00edm, slug, \u00e1llapot, sorrend, SEO mez\u0151k.'],
        ['Oldal szerkeszt\u00e9se', 'Minden mez\u0151 m\u00f3dos\u00edthat\u00f3, szekci\u00f3k kezel\u00e9se.'],
        ['Oldal t\u00f6rl\u00e9se', 'Meger\u0151s\u00edt\u00e9ssel, kaszk\u00e1d szekci\u00f3 t\u00f6rl\u00e9s.'],
        ['Publik\u00e1lt / V\u00e1zlat \u00e1llapot', 'V\u00e1zlat oldalak nem jelennek meg a weboldalon.'],
        ['SEO mez\u0151k', 'Meta c\u00edm, le\u00edr\u00e1s, kulcsszavak, OG tag-ek oldalank\u00e9nt.'],
    ])
    doc.add_heading('2.3 Szekci\u00f3 kezel\u00e9s', level=2)
    add_table(doc, ['Funkci\u00f3', 'Le\u00edr\u00e1s'], [
        ['24 szekci\u00f3t\u00edpus', 'Sz\u00e9lesk\u00f6r\u0171 tartalomblokk-v\u00e1laszt\u00e9k.'],
        ['Szekci\u00f3 hozz\u00e1ad\u00e1sa', 'T\u00edpus kiv\u00e1laszt\u00e1sa leg\u00f6rd\u00fcl\u0151 list\u00e1b\u00f3l.'],
        ['Szekci\u00f3 szerkeszt\u00e9se', 'T\u00edpusnak megfelel\u0151 egyedi szerkeszt\u0151fel\u00fclet.'],
        ['Szekci\u00f3 t\u00f6rl\u00e9se', 'Meger\u0151s\u00edt\u00e9ssel.'],
        ['Drag & drop sorrend', 'H\u00fazd-\u00e9s-ejtsd m\u00f3dszerrel szekci\u00f3k \u00e1trendez\u00e9se.'],
        ['K\u00e9p tall\u00f3z\u00e1s', 'M\u00e9dia k\u00f6nyvt\u00e1rb\u00f3l v\u00e1laszthat\u00f3 a k\u00e9p.'],
        ['Oldal v\u00e1laszt\u00f3', 'URL mez\u0151k mellett leg\u00f6rd\u00fcl\u0151 oldal-v\u00e1laszt\u00f3.'],
        ['WYSIWYG szerkeszt\u0151', 'Gazdag sz\u00f6vegszerkeszt\u0151.'],
        ['Komponens katal\u00f3gus', 'Vizu\u00e1lis \u00e1ttekint\u00e9s az \u00f6sszes szekci\u00f3t\u00edpusr\u00f3l.'],
    ])
    doc.add_heading('2.4 M\u00e9diakezel\u00e9s', level=2)
    add_table(doc, ['Funkci\u00f3', 'Le\u00edr\u00e1s'], [
        ['K\u00e9p felt\u00f6lt\u00e9s', 'F\u00e1jl felt\u00f6lt\u00e9s (JPG, PNG, GIF, WebP, SVG).'],
        ['Alt sz\u00f6veg', 'SEO-bar\u00e1t sz\u00f6veges le\u00edr\u00e1s.'],
        ['Kiemelt jel\u00f6l\u00e9s', 'K\u00e9pek megjel\u00f6l\u00e9se a Hero diavet\u00edt\u00e9shez.'],
        ['K\u00e9p t\u00f6rl\u00e9se', 'A f\u00e1jl a szerverr\u0151l is elt\u00e1vol\u00edt\u00f3dik.'],
    ])
    doc.add_heading('2.5 Men\u00fckezel\u00e9s', level=2)
    add_table(doc, ['Funkci\u00f3', 'Le\u00edr\u00e1s'], [
        ['Men\u00fcpont hozz\u00e1ad\u00e1sa', 'N\u00e9v, hivatkozott oldal vagy egyedi URL, sz\u00fcl\u0151, sorrend.'],
        ['T\u00f6bbszint\u0171 men\u00fc', 'Ak\u00e1r 5 szint m\u00e9ly\u0171 almen\u00fc rendszer.'],
        ['Szerkeszt\u00e9s \u00e9s t\u00f6rl\u00e9s', 'Minden mez\u0151 m\u00f3dos\u00edthat\u00f3.'],
    ])
    doc.add_heading('2.6 Be\u00e1ll\u00edt\u00e1sok', level=2)
    add_table(doc, ['Funkci\u00f3', 'Le\u00edr\u00e1s'], [
        ['Weboldal neve \u00e9s szlogen', 'A fejl\u00e9cben \u00e9s keres\u0151ben megjelen\u0151 adatok.'],
        ['Kapcsolattart\u00e1si adatok', 'E-mail, telefon, c\u00edm.'],
        ['Sz\u00ednek', 'Els\u0151dleges \u00e9s m\u00e1sodlagos sz\u00edn.'],
        ['Logo', 'Logo felt\u00f6lt\u00e9s + 3 megjelen\u00edt\u00e9si m\u00f3d.'],
        ['Glob\u00e1lis SEO', 'Alap\u00e9rtelmezett meta le\u00edr\u00e1s, kulcsszavak, OG k\u00e9p.'],
        ['SMTP e-mail be\u00e1ll\u00edt\u00e1sok', 'Admin fel\u00fcletr\u0151l konfigur\u00e1lhat\u00f3 e-mail k\u00fcld\u00e9s.'],
        ['Teszt e-mail', 'Egy gombnyom\u00e1ssal ellen\u0151rizhet\u0151 az e-mail konfigur\u00e1ci\u00f3.'],
    ])
    doc.add_heading('2.7 Biztons\u00e1g', level=2)
    add_table(doc, ['Funkci\u00f3', 'Le\u00edr\u00e1s'], [
        ['Session-alap\u00fa bejelentkez\u00e9s', 'Biztons\u00e1gos munkamenet-kezel\u00e9s.'],
        ['Jelsz\u00f3v\u00e1ltoztat\u00e1s', 'Az admin fel\u00fcleten bel\u00fcl.'],
        ['bcrypt jelsz\u00f3 hash', 'Ipar\u00e1gi szabv\u00e1ny jelsz\u00f3 t\u00e1rol\u00e1s.'],
        ['CSRF v\u00e9delem', 'Minden \u0171rlap token-v\u00e9dett.'],
        ['SQL injection v\u00e9delem', 'Prepared statement-ek mindenhol.'],
        ['XSS v\u00e9delem', 'HTML escape minden kimentn\u00e9l.'],
    ])

    # Deployment
    doc.add_heading('3. Telep\u00edt\u00e9s \u00e9s karbantart\u00e1s', level=1)
    add_table(doc, ['Funkci\u00f3', 'Le\u00edr\u00e1s'], [
        ['Windows telep\u00edt\u0151 (.exe)', 'Egyszer\u0171 Windows program, dupla kattint\u00e1ssal futtathat\u00f3.'],
        ['Linux deploy szkript', 'Bash szkript fejleszt\u0151knek (deploy.sh).'],
        ['FTP alap\u00fa felt\u00f6lt\u00e9s', 'A f\u00e1jlok FTP-n kereszt\u00fcl ker\u00fclnek a szerverre.'],
        ['Adatb\u00e1zis migr\u00e1ci\u00f3k', 'SQL f\u00e1jlok a s\u00e9ma m\u00f3dos\u00edt\u00e1sokhoz.'],
        ['Cache busting', 'CSS/JS verzi\u00f3sz\u00e1mok a b\u00f6ng\u00e9sz\u0151 gyors\u00edt\u00f3t\u00e1r friss\u00edt\u00e9s\u00e9hez.'],
    ])

    path = os.path.join(OUTPUT_DIR, 'ParkoloABC_Funkciolista.docx')
    doc.save(path)
    print(f'  \u2713 {path}')


# =====================================================================
# MAIN
# =====================================================================
if __name__ == '__main__':
    print('Dokumentumok gener\u00e1l\u00e1sa...')
    generate_user_guide()
    generate_dev_docs()
    generate_feature_list()
    print('K\u00e9sz!')
