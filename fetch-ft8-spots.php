<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$cache_file = '/tmp/ft8_spots_cache.json';
$cache_time = 300; // 5 minutes

// Try cache first
if (file_exists($cache_file) && time() - filemtime($cache_file) < $cache_time) {
    echo file_get_contents($cache_file);
    exit;
}

$spots = [];

// Try PSK Reporter API
$psk_url = 'https://www.pskreporter.info/pskmap.php?type=dxjson&noactive=1&limit=100';
$ctx = stream_context_create([
    'http' => ['timeout' => 10, 'header' => "User-Agent: HamClock/1.0\r\n"],
    'ssl' => ['verify_peer' => false]
]);

$response = @file_get_contents($psk_url, false, $ctx);

if ($response) {
    $data = json_decode($response, true);
    
    // Parse PSK Reporter response
    if (isset($data['receptions'])) {
        foreach ($data['receptions'] as $spot) {
            if (count($spots) >= 20) break;
            
            $freq = $spot['frequency'] ?? 14074000;
            $band = getBandFromFreq($freq);
            
            $spots[] = [
                'call' => $spot['call'] ?? 'N/A',
                'band' => $band,
                'freq' => number_format($freq / 1000, 0) . ' kHz',
                'snr' => $spot['sn'] ?? '+00',
                'mode' => 'FT8',
            ];
        }
    }
}

// Fallback: Demo data if API fails
if (empty($spots)) {
    $spots = [
        ['call' => 'OE1A', 'band' => '20m', 'freq' => '14074 kHz', 'snr' => '+12', 'mode' => 'FT8'],
        ['call' => 'N1MM', 'band' => '15m', 'freq' => '21074 kHz', 'snr' => '+08', 'mode' => 'FT8'],
        ['call' => 'EA5RBA', 'band' => '10m', 'freq' => '10136 kHz', 'snr' => '+18', 'mode' => 'FT8'],
        ['call' => 'ZL2ABC', 'band' => '40m', 'freq' => '7074 kHz', 'snr' => '+14', 'mode' => 'FT8'],
        ['call' => 'VK4XYZ', 'band' => '20m', 'freq' => '14074 kHz', 'snr' => '+06', 'mode' => 'FT8'],
    ];
}

// Count by band for activity - 13 BANDS FROM BAND CONDITIONS
$bandActivity = [
    '160m' => 0,
    '80m' => 0,
    '60m' => 0,
    '40m' => 0,
    '30m' => 0,
    '20m' => 0,
    '17m' => 0,
    '15m' => 0,
    '12m' => 0,
    '11m' => 0,
    '10m' => 0,
    '6m' => 0,
    '2m' => 0,
];

foreach ($spots as $spot) {
    $band = $spot['band'];
    if (isset($bandActivity[$band])) {
        $bandActivity[$band]++;
    }
}

$result = [
    'spots' => $spots,
    'band_activity' => $bandActivity,
    'total_spots' => count($spots),
    'timestamp' => time(),
    'next_update' => time() + 300,
];

file_put_contents($cache_file, json_encode($result));
echo json_encode($result);

function getBandFromFreq($freq) {
    // Freq in Hz, convert to kHz
    $freqKHz = $freq / 1000;
    
    // ITU Band definitions (MHz converted to kHz)
    if ($freqKHz >= 1800 && $freqKHz < 2000) return '160m';
    if ($freqKHz >= 3500 && $freqKHz < 4000) return '80m';
    if ($freqKHz >= 5330 && $freqKHz < 5405) return '60m';  // 5 MHz band
    if ($freqKHz >= 7000 && $freqKHz < 7300) return '40m';
    if ($freqKHz >= 10100 && $freqKHz < 10150) return '30m';
    if ($freqKHz >= 14000 && $freqKHz < 14350) return '20m';
    if ($freqKHz >= 18068 && $freqKHz < 18168) return '17m';
    if ($freqKHz >= 21000 && $freqKHz < 21450) return '15m';
    if ($freqKHz >= 24890 && $freqKHz < 24990) return '12m';
    if ($freqKHz >= 28000 && $freqKHz < 29700) return '10m';
    if ($freqKHz >= 50000 && $freqKHz < 54000) return '6m';
    if ($freqKHz >= 144000 && $freqKHz < 148000) return '2m';
    if ($freqKHz >= 220000 && $freqKHz < 225000) return '1.25m';
    if ($freqKHz >= 430000 && $freqKHz < 450000) return '70cm';
    
    return 'Other';
}
