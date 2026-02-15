<?php
/**
 * DX Patch — auto_prepend_file
 * Ersetzt im Output den kaputten HamQTH-fetch durch den lokalen Proxy
 * und entfernt den forbidden User-Agent Header
 */

// Nur für index.php (Hauptseite) patchen
$uri = $_SERVER['REQUEST_URI'] ?? '';
if (!preg_match('#^/?($|\?|index\.php)#', $uri)) {
    return; // andere Requests unverändert lassen
}

ob_start(function($html) {
    // Fix 1: direkten HamQTH fetch → lokaler Proxy (forbidden User-Agent Header)
    $html = str_replace(
        "fetch('https://www.hamqth.com/dxc_csv.php?limit=20', {\n                headers: { 'User-Agent': 'HamClockDashboard/1.0 OE3LCR' }\n            })",
        "fetch('/data/fetch-dx.php')",
        $html
    );
    $html = preg_replace(
        "#fetch\('https://www\.hamqth\.com/dxc_csv\.php\?limit=20',[^)]*\)#s",
        "fetch('/data/fetch-dx.php')",
        $html
    );

    // Fix 2: LIVE-Badge zum DX Cluster card-header hinzufügen
    $html = str_replace(
        '<span class="card-title" data-i18n="card_dx">DX Cluster Spots</span>',
        '<span class="card-title" data-i18n="card_dx">DX Cluster Spots</span><span class="demo-badge">LIVE</span>',
        $html
    );

    // Fix 3: Aurora CSS für VHF-Bänder vor </head> injizieren
    $html = str_replace(
        '</head>',
        '<style>
.band-box.aurora {
    background: rgba(100, 200, 255, 0.15);
    border: 1.5px solid #64c8ff;
    color: #64c8ff;
}
</style>
</head>',
        $html
    );

    // Fix 4: "Letzte Aktualisierung" Timestamp-Zeile ins Band-Widget injizieren
    // Wir fügen den Span nach dem letzten Band-Box (band2-box) innerhalb der Grid-Zeile ein
    $html = str_replace(
        'id="band2-box" class="band-box good"><div class="band-name">2m</div><div class="band-condition">GOOD</div></div>',
        'id="band2-box" class="band-box good"><div class="band-name">2m</div><div class="band-condition">GOOD</div></div>' .
        '<div style="grid-column:1/-1;text-align:center;font-size:0.72em;color:#888;padding:3px 0 2px;letter-spacing:0.03em;">' .
        '&#8635; <span id="band-updated">--</span></div>',
        $html
    );

    // Fix 5: band-patch.js vor dem ERSTEN </body> injizieren (Band Conditions v2 Override)
    // Nutze strrpos → letztes </body> finden (echter HTML-Abschluss)
    $bodyPos = strrpos($html, '</body>');
    if ($bodyPos !== false) {
        $inject = '<script src="/data/band-patch.js"></script>' . "\n";
        $html = substr_replace($html, $inject . '</body>', $bodyPos, strlen('</body>'));
    }

    // Fix 6+7: Neue Space-Weather-Zeilen + Legende-Button nach spaceWeatherCombined
    // Suche das Ende der Status-Zeile (letzte Zeile im Widget-Grid) und injiziere 3 neue Rows + Button
    $spaceNewRows =
        "\n        " . '<div style="display:flex; justify-content:space-between; align-items:center;">' .
        "\n            " . '<span style="color:#aaa;">X-Ray:</span>' .
        "\n            " . '<span style="color:#fff; font-weight:600;" id="xrayCombined">--</span>' .
        "\n        " . '</div>' .
        "\n        " . '<div style="display:flex; justify-content:space-between; align-items:center;">' .
        "\n            " . '<span style="color:#aaa;">Proton Flux:</span>' .
        "\n            " . '<span style="color:#fff; font-weight:600;" id="protonFluxCombined">-- pfu</span>' .
        "\n        " . '</div>' .
        "\n        " . '<div style="display:flex; justify-content:space-between; align-items:center;">' .
        "\n            " . '<span style="color:#aaa;">Electron Flux:</span>' .
        "\n            " . '<span style="color:#fff; font-weight:600;" id="electronFluxCombined">--</span>' .
        "\n        " . '</div>' .
        "\n        " . '<div style="text-align:center; margin-top:8px;">' .
        "\n            " . '<button onclick="document.getElementById(\'legend-modal\').style.display=\'flex\'"' .
        "\n                    " . 'style="background:transparent;border:1px solid #333;color:#aaa;padding:3px 10px;border-radius:4px;cursor:pointer;font-size:11px;">' .
        "\n                " . '&#x2139;&#xFE0F; Legende / Legend' .
        "\n            " . '</button>' .
        "\n        " . '</div>';

    // Injiziere nach dem schließenden </div> der Status-Zeile, VOR dem Grid-</div>
    $html = str_replace(
        'id="spaceWeatherCombined">--</span>' . "\n        </div>\n    </div>\n</div>",
        'id="spaceWeatherCombined">--</span>' . "\n        </div>" . $spaceNewRows . "\n    </div>\n</div>",
        $html
    );

    // Fix 8+9: Legende-Modal + space-patch.js vor </body> injizieren
    $legendModal = '
<div id="legend-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:9999; align-items:center; justify-content:center; padding:20px;">
  <div style="background:#1a1a1a; border:1px solid #333; border-radius:10px; max-width:700px; width:100%; max-height:90vh; overflow-y:auto; padding:24px; color:#eee; font-size:13px; line-height:1.7;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h2 style="color:#00ff88; margin:0; font-size:16px;">&#x1F4E1; Propagation Parameter &mdash; Legende / Legend</h2>
      <button onclick="document.getElementById(\'legend-modal\').style.display=\'none\'" style="background:transparent;border:none;color:#aaa;font-size:20px;cursor:pointer;">&#x2715;</button>
    </div>
    <table style="width:100%; border-collapse:collapse;">
      <tr style="border-bottom:1px solid #333;">
        <th style="color:#00ff88; text-align:left; padding:6px 8px; width:130px;">Parameter</th>
        <th style="color:#00ff88; text-align:left; padding:6px 8px;">&#x1F1E9;&#x1F1EA; Deutsch</th>
        <th style="color:#00ff88; text-align:left; padding:6px 8px;">&#x1F1EC;&#x1F1E7; English</th>
      </tr>
      <tr style="border-bottom:1px solid #222;">
        <td style="padding:8px; color:#ffd700; font-weight:600; vertical-align:top;">K-Index<br><span style="color:#aaa; font-size:11px;">Skala 0&ndash;9</span></td>
        <td style="padding:8px; vertical-align:top;">Geomagnetischer Index. Misst Schwankungen des Erdmagnetfelds &uuml;ber 3 Stunden. Logarithmische Skala. K&ge;5 = Geomagnetischer Sturm &rarr; HF-Ausbreitung gest&ouml;rt, Polarlicht m&ouml;glich.</td>
        <td style="padding:8px; vertical-align:top;">Geomagnetic Index. Measures variations in Earth&apos;s magnetic field over 3-hour intervals. Logarithmic scale. K&ge;5 = Geomagnetic storm &rarr; HF propagation disrupted, aurora possible.</td>
      </tr>
      <tr style="border-bottom:1px solid #222;">
        <td style="padding:8px; color:#ffd700; font-weight:600; vertical-align:top;">A-Index<br><span style="color:#aaa; font-size:11px;">Skala 0&ndash;400</span></td>
        <td style="padding:8px; vertical-align:top;">&Auml;quivalentamplitude. Tages-Durchschnitt aus 8 K-Messungen (linear umgewandelt). A&lt;15 = ruhig, A&gt;100 = Sturm. Lineare Skala, besser f&uuml;r Vergleiche geeignet als K.</td>
        <td style="padding:8px; vertical-align:top;">Equivalent amplitude. Daily average derived from 8 K-index measurements (linearly converted). A&lt;15 = quiet, A&gt;100 = storm. Linear scale, better for comparisons than K.</td>
      </tr>
      <tr style="border-bottom:1px solid #222;">
        <td style="padding:8px; color:#ffd700; font-weight:600; vertical-align:top;">SFI / F10.7<br><span style="color:#aaa; font-size:11px;">70&ndash;300 sfu</span></td>
        <td style="padding:8px; vertical-align:top;">Solar Flux Index bei 10,7 cm Wellenl&auml;nge (2800 MHz). Ma&szlig; f&uuml;r solare UV/EUV-Strahlung die die F2-Schicht ionisiert. SFI&gt;150 = hohe Aktivit&auml;t &rarr; h&ouml;here MUF, bessere DX-Bedingungen auf 10&ndash;20 m.</td>
        <td style="padding:8px; vertical-align:top;">Solar Flux Index at 10.7 cm wavelength (2800 MHz). Proxy for solar UV/EUV radiation ionizing the F2 layer. SFI&gt;150 = high activity &rarr; higher MUF, better DX conditions on 10&ndash;20 m.</td>
      </tr>
      <tr style="border-bottom:1px solid #222;">
        <td style="padding:8px; color:#ffd700; font-weight:600; vertical-align:top;">X-Ray<br><span style="color:#aaa; font-size:11px;">A B C M X</span></td>
        <td style="padding:8px; vertical-align:top;">GOES-Satellit misst R&ouml;ntgenstrahlung (0.1&ndash;0.8 nm). Logarithmische Klassen: A&rarr;B&rarr;C&rarr;M&rarr;X (je 10&times; st&auml;rker). M/X-Flares &rarr; Dellinger-Effekt: HF-Blackout auf Tagseite Erde (Kurzwellen-Totalausfall bis Stunden).</td>
        <td style="padding:8px; vertical-align:top;">GOES satellite measures X-ray flux (0.1&ndash;0.8 nm). Logarithmic classes: A&rarr;B&rarr;C&rarr;M&rarr;X (each 10&times; stronger). M/X flares &rarr; Dellinger effect: HF blackout on sunlit side of Earth (shortwave blackout lasting minutes to hours).</td>
      </tr>
      <tr style="border-bottom:1px solid #222;">
        <td style="padding:8px; color:#ffd700; font-weight:600; vertical-align:top;">Proton Flux<br><span style="color:#aaa; font-size:11px;">&gt;10 MeV, pfu</span></td>
        <td style="padding:8px; vertical-align:top;">Energiereiche Protonen im Sonnenwind (&gt;10 MeV). Einheit: pfu (particles/cm&sup2;/s/sr). &gt;10 pfu = Strahlungssturm S1 &rarr; Polar Cap Absorption (PCA): HF-Totalausfall auf Polrouten. Tritt nach starken Flares auf.</td>
        <td style="padding:8px; vertical-align:top;">High-energy protons in the solar wind (&gt;10 MeV). Unit: pfu (particles/cm&sup2;/s/sr). &gt;10 pfu = Radiation Storm S1 &rarr; Polar Cap Absorption (PCA): HF blackout on polar routes. Occurs after major flares.</td>
      </tr>
      <tr style="border-bottom:1px solid #222;">
        <td style="padding:8px; color:#ffd700; font-weight:600; vertical-align:top;">Electron Flux<br><span style="color:#aaa; font-size:11px;">&gt;2 MeV, pfu</span></td>
        <td style="padding:8px; vertical-align:top;">Relativistische Elektronen im Sonnenwind. Hohe Werte zeigen Aktivit&auml;t im Strahlungsg&uuml;rtel an &rarr; Aurora-Vorl&auml;ufer. St&ouml;rt Satelliten-Navigation (GPS). Korreliert mit Polarlicht-Aktivit&auml;t.</td>
        <td style="padding:8px; vertical-align:top;">Relativistic electrons in the solar wind. High values indicate radiation belt activity &rarr; aurora precursor. Disturbs satellite navigation (GPS). Correlates with aurora activity.</td>
      </tr>
      <tr style="border-bottom:1px solid #222;">
        <td style="padding:8px; color:#ffd700; font-weight:600; vertical-align:top;">MUF<br><span style="color:#aaa; font-size:11px;">MHz</span></td>
        <td style="padding:8px; vertical-align:top;">Maximum Usable Frequency. H&ouml;chste Frequenz die von der F2-Ionosph&auml;renschicht noch reflektiert wird. Bestimmt das obere Limit f&uuml;r Kurzwellenverbindungen. Abh&auml;ngig von SFI, Tageszeit und Jahreszeit.</td>
        <td style="padding:8px; vertical-align:top;">Maximum Usable Frequency. Highest frequency still reflected by the F2 ionospheric layer. Determines the upper limit for shortwave links. Depends on SFI, time of day, and season.</td>
      </tr>
      <tr>
        <td style="padding:8px; color:#ffd700; font-weight:600; vertical-align:top;">Aurora<br><span style="color:#aaa; font-size:11px;">K&ge;5</span></td>
        <td style="padding:8px; vertical-align:top;">Polarlicht-Aktivit&auml;t. Bei K&ge;5 sichtbar bis ~50&deg;N (&Ouml;sterreich JN87). F&uuml;r VHF-Funkamateure: Aurora-Scatter erm&ouml;glicht 2m/6m-DX &uuml;ber reflektierte Signale an der Ionisation. Typisches QSB-Muster: &quot;raspelig&quot;, breite Bandbreite.</td>
        <td style="padding:8px; vertical-align:top;">Aurora activity. K&ge;5 visible to ~50&deg;N (Austria JN87). For VHF operators: Aurora scatter enables 2m/6m DX via signals reflected from ionized aurora curtains. Characteristic sound: &quot;raspy&quot;, wide bandwidth.</td>
      </tr>
    </table>
    <div style="margin-top:16px; padding:10px; background:#0d1a0d; border-radius:6px; font-size:11px; color:#666;">
      Datenquellen / Data Sources: N0NBH (hamqsl.com) &middot; NOAA SWPC (GOES-16/18) &middot; Aktualisierung alle 10 Min / Updated every 10 min
    </div>
  </div>
</div>
<script src="/data/space-patch.js"></script>';

    $bodyPos2 = strrpos($html, '</body>');
    if ($bodyPos2 !== false) {
        $html = substr_replace($html, $legendModal . "\n</body>", $bodyPos2, strlen('</body>'));
    }

    return $html;
});
