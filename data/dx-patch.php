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

    return $html;
});
