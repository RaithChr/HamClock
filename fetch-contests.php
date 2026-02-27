<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$cache_file = '/tmp/contests_cache.json';
$cache_time = 3600; // 1 hour

// Check cache
if (file_exists($cache_file) && time() - filemtime($cache_file) < $cache_time) {
    echo file_get_contents($cache_file);
    exit;
}

// Fetch ARRL contests
$contests = [];

// ARRL Calendar (static fallback)
$arrl = [
    ['name' => 'CQ WW DX SSB', 'date' => '2026-03-01', 'time' => '12:00', 'mode' => 'SSB', 'band' => 'All HF'],
    ['name' => 'ARRL Rookie Roundup', 'date' => '2026-03-07', 'time' => '20:00', 'mode' => 'CW', 'band' => '40/80m'],
    ['name' => 'ARRL VHF Contest', 'date' => '2026-03-07', 'time' => '19:00', 'mode' => 'All', 'band' => 'VHF+'],
];

// OEVSV (Austrian)
$oevsv = [
    ['name' => 'OEVSV Open Sprint', 'date' => '2026-03-01', 'time' => '18:00', 'mode' => 'CW', 'band' => '20m', 'country' => 'AT'],
    ['name' => 'OEVSV SSB Aktivität', 'date' => '2026-03-07', 'time' => '09:00', 'mode' => 'SSB', 'band' => '40/80m', 'country' => 'AT'],
    ['name' => 'OE DX Convention', 'date' => '2026-03-14', 'time' => '00:00', 'mode' => 'SSB/CW', 'band' => 'All', 'country' => 'AT'],
];

$result = [
    'arrl' => $arrl,
    'oevsv' => $oevsv,
    'timestamp' => time(),
    'next_update' => time() + 3600,
];

file_put_contents($cache_file, json_encode($result));
echo json_encode($result);
