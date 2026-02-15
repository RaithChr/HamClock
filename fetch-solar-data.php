<?php
/**
 * Fetch Real Solar Data from N0NBH (hamqsl.com) + NOAA SWPC
 * Replaces bogus rand()-based calculations with real data.
 * 
 * Primary source: N0NBH HamQSL XML (K-Index, A-Index, SFI, MUF)
 * Fallback: Static safe defaults (no random values)
 */

header('Content-Type: application/json');
header('Cache-Control: public, max-age=600');

// 10-minute cache
$cache_file = '/tmp/solar_data_v2_cache.json';
$cache_duration = 600;

if (file_exists($cache_file) && time() - filemtime($cache_file) < $cache_duration) {
    readfile($cache_file);
    exit;
}

$solarData = fetchN0NBHData();

// Save to file for homepage to use
$outputFile = '/var/www/html/data/solar-data.json';
@mkdir('/var/www/html/data', 0755, true);
file_put_contents($outputFile, json_encode($solarData, JSON_PRETTY_PRINT));
@chmod($outputFile, 0644);

$json = json_encode($solarData, JSON_PRETTY_PRINT);
file_put_contents($cache_file, $json);
echo $json;

// ─────────────────────────────────────────────────────────────────────────────

function fetchN0NBHData() {
    $url = 'https://www.hamqsl.com/solarxml.php';
    $context = stream_context_create([
        'http' => ['timeout' => 15, 'user_agent' => 'craith.cloud/2.0'],
        'ssl'  => ['verify_peer' => true]
    ]);

    $xml = @file_get_contents($url, false, $context);

    if (!$xml) {
        return fallbackData('N0NBH HamQSL unreachable');
    }

    // Helper: extract single XML tag value
    $get = function($tag) use ($xml) {
        if (preg_match("/<$tag>([^<]*)<\/$tag>/i", $xml, $m)) return trim($m[1]);
        return null;
    };

    $kIndex = intval($get('kindex') ?? 2);
    $aIndex = intval($get('aindex') ?? 7);
    $sfi    = intval($get('solarflux') ?? 100);
    $muf    = $get('muf');
    $ssn    = intval($get('sunspots') ?? 0);
    $updated = $get('updated') ?? gmdate('Y-m-d H:i') . ' UTC';

    // Sanity-check ranges (no fake values injected)
    $kIndex = max(0, min(9,   $kIndex));
    $aIndex = max(0, min(400, $aIndex));
    $sfi    = max(65, min(300, $sfi));

    return [
        'success'    => true,
        'source'     => 'N0NBH HamQSL',
        'timestamp'  => gmdate('Y-m-d H:i:s') . ' UTC',
        'kIndex'     => $kIndex,
        'gIndex'     => kToG($kIndex),
        'gText'      => kToGText($kIndex),
        'sfi'        => $sfi,
        'aIndex'     => $aIndex,
        'muf'        => $muf,
        'ssn'        => $ssn,
        'updated_at' => $updated,
        'conditions' => getConditionsFromIndex($kIndex)
    ];
}

/** Convert K-Index (0–9) to approximate G-Scale (0–5) */
function kToG($k) {
    if ($k >= 9) return 5;
    if ($k >= 8) return 4;
    if ($k >= 7) return 3;
    if ($k >= 6) return 2;
    if ($k >= 5) return 1;
    return 0;
}

function kToGText($k) {
    if ($k >= 9) return 'Extreme';
    if ($k >= 8) return 'Severe';
    if ($k >= 7) return 'Strong';
    if ($k >= 6) return 'Moderate';
    if ($k >= 5) return 'Minor';
    return 'None';
}

/** Static safe fallback — no random values */
function fallbackData($reason = '') {
    return [
        'success'    => false,
        'source'     => 'Static fallback',
        'timestamp'  => gmdate('Y-m-d H:i:s') . ' UTC',
        'kIndex'     => 2,
        'gIndex'     => 0,
        'gText'      => 'None',
        'sfi'        => 100,
        'aIndex'     => 7,
        'muf'        => null,
        'ssn'        => 0,
        'updated_at' => gmdate('Y-m-d'),
        'error'      => $reason ?: 'Primary API unavailable',
        'conditions' => getConditionsFromIndex(2)
    ];
}

function getConditionsFromIndex($kIndex) {
    // Base: everything good
    $bands = array_fill_keys(
        ['160','80','60','40','30','20','17','15','12','11','10','6','2'],
        'good'
    );

    if ($kIndex >= 7) {
        // Severe storm — HF badly disrupted
        foreach (['160','80','60','40','30','20'] as $b) $bands[$b] = 'poor';
        foreach (['17','15','12','11','10']       as $b) $bands[$b] = 'fair';
    } elseif ($kIndex >= 5) {
        // Moderate storm
        foreach (['160','80','60']      as $b) $bands[$b] = 'poor';
        foreach (['40','30','20','17']  as $b) $bands[$b] = 'fair';
    } elseif ($kIndex >= 3) {
        // Unsettled
        $bands['160'] = 'poor';
        foreach (['80','60','40']       as $b) $bands[$b] = 'fair';
    } else {
        // Quiet — 160m always needs night to open
        $bands['160'] = 'fair';
    }

    return $bands;
}
?>
