<?php
/**
 * fetch-space-data.php — Space Weather Aggregator
 * Quellen: N0NBH (hamqsl.com) + NOAA SWPC GOES-16/18
 * Cache: 10 Minuten in /tmp
 * 
 * Echte API-Endpunkte (verifiziert 2026-02-15):
 *   xrays-3-day.json      → energy='0.1-0.8nm', field 'flux'
 *   integral-protons-3-day.json → energy='>=10 MeV', field 'flux'
 *   integral-electrons-3-day.json → energy='>=2 MeV', field 'flux'
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Cache 10 Minuten
$cache = '/tmp/space_data_cache.json';
if (file_exists($cache) && time() - filemtime($cache) < 600) {
    readfile($cache);
    exit;
}

// --- Daten holen ---

// 1. N0NBH für K, A, SFI, MUF (bewährt, XML)
$n0nbh = @file_get_contents('https://www.hamqsl.com/solarxml.php');

// 2. NOAA X-Ray Flux (GOES, 0.1-0.8nm long channel)
$xray_json = @file_get_contents('https://services.swpc.noaa.gov/json/goes/primary/xrays-3-day.json');

// 3. NOAA Proton Flux (>=10 MeV integral channel)
$proton_json = @file_get_contents('https://services.swpc.noaa.gov/json/goes/primary/integral-protons-3-day.json');

// 4. NOAA Electron Flux (>=2 MeV integral channel)
$electron_json = @file_get_contents('https://services.swpc.noaa.gov/json/goes/primary/integral-electrons-3-day.json');

// --- N0NBH parsen ---
$kIndex = 2;
$aIndex = 7;
$sfi = 120;
$muf = '--';

if ($n0nbh) {
    preg_match('/<solarflux>(\d+)<\/solarflux>/', $n0nbh, $m);
    if ($m) $sfi = (int)$m[1];

    preg_match('/<kindex>(\d+)<\/kindex>/', $n0nbh, $m);
    if ($m) $kIndex = (int)$m[1];

    preg_match('/<aindex>(\d+)<\/aindex>/', $n0nbh, $m);
    if ($m) $aIndex = (int)$m[1];

    preg_match('/<muf>([\d.]+)<\/muf>/', $n0nbh, $m);
    if ($m) $muf = $m[1] . ' MHz';
}

// Aurora-Status basierend auf K-Index
$aurora = ($kIndex >= 5) ? 'Visible' : 'Quiet';
if ($kIndex >= 7) $aurora = 'Active!';

// Space Weather Status
if ($kIndex >= 7)      $spaceStatus = 'Severe Storm';
elseif ($kIndex >= 5)  $spaceStatus = 'Storm';
elseif ($kIndex >= 4)  $spaceStatus = 'Unsettled';
elseif ($kIndex >= 2)  $spaceStatus = 'Active';
else                   $spaceStatus = 'Quiet';

// --- X-Ray klassifizieren (W/m² → GOES-Klasse) ---
// Kanal: 0.1-0.8nm (Long wavelength, Standard für Flare-Klassen)
$xrayClass = 'A0.0';
$xrayFlux = 0.0;

if ($xray_json) {
    $xdata = json_decode($xray_json, true);
    if (is_array($xdata) && count($xdata) > 0) {
        // Von hinten iterieren → neuesten Wert für 0.1-0.8nm Kanal
        for ($i = count($xdata) - 1; $i >= 0; $i--) {
            $row = $xdata[$i];
            if (!isset($row['energy']) || $row['energy'] !== '0.1-0.8nm') continue;
            $flux = floatval($row['flux'] ?? 0);
            if ($flux <= 0) continue;
            $xrayFlux = $flux;
            if      ($flux >= 1e-4) $xrayClass = 'X' . number_format($flux / 1e-4, 1);
            elseif  ($flux >= 1e-5) $xrayClass = 'M' . number_format($flux / 1e-5, 1);
            elseif  ($flux >= 1e-6) $xrayClass = 'C' . number_format($flux / 1e-6, 1);
            elseif  ($flux >= 1e-7) $xrayClass = 'B' . number_format($flux / 1e-7, 1);
            else                    $xrayClass = 'A' . number_format($flux / 1e-8, 1);
            break;
        }
    }
}

// --- Proton Flux (>=10 MeV, pfu) ---
$protonFlux = 0.0;

if ($proton_json) {
    $pdata = json_decode($proton_json, true);
    if (is_array($pdata) && count($pdata) > 0) {
        for ($i = count($pdata) - 1; $i >= 0; $i--) {
            $row = $pdata[$i];
            if (!isset($row['energy']) || $row['energy'] !== '>=10 MeV') continue;
            $val = floatval($row['flux'] ?? 0);
            if ($val < 0) $val = 0;
            $protonFlux = $val;
            break;
        }
    }
}

// --- Electron Flux (>=2 MeV, pfu) ---
$electronFlux = 0.0;

if ($electron_json) {
    $edata = json_decode($electron_json, true);
    if (is_array($edata) && count($edata) > 0) {
        // Nur ein Kanal (>=2 MeV), letzter Eintrag
        $last = end($edata);
        $electronFlux = max(0.0, floatval($last['flux'] ?? 0));
    }
}

// --- Ergebnis ---
$result = [
    'success'      => true,
    'source'       => 'N0NBH (hamqsl.com) + NOAA SWPC (GOES)',
    'kIndex'       => $kIndex,
    'aIndex'       => $aIndex,
    'sfi'          => $sfi,
    'muf'          => $muf,
    'aurora'       => $aurora,
    'spaceStatus'  => $spaceStatus,
    'xray'         => $xrayClass,
    'xrayFlux'     => $xrayFlux,
    'protonFlux'   => round($protonFlux, 3),
    'electronFlux' => round($electronFlux, 1),
    'updated'      => gmdate('Y-m-d H:i') . ' UTC',
];

$json = json_encode($result, JSON_UNESCAPED_UNICODE);
file_put_contents($cache, $json);
echo $json;
