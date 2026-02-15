<?php
// PHP-Proxy für Sonnenbild — hohe Auflösung (1024px)
$cache_file = '/tmp/sdo_cache_hq.jpg';
$cache_time = 300; // 5 Minuten

if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
    header('Content-Type: image/jpeg');
    header('Cache-Control: public, max-age=300');
    readfile($cache_file);
    exit;
}

$urls = [
    // SDO NASA direkt (1024px, manchmal erreichbar)
    'https://sdo.gsfc.nasa.gov/assets/img/latest/f_211_193_171_1024.jpg',
    // SOHO EIT 304 — 1024px, hochauflösend, orange/rot ✅
    'https://soho.nascom.nasa.gov/data/realtime/eit_304/1024/latest.jpg',
    // SOHO HMI — 1024px Magnetogramm ✅
    'https://soho.nascom.nasa.gov/data/realtime/hmi_igr/1024/latest.jpg',
    // Fallback 512px
    'https://soho.nascom.nasa.gov/data/realtime/eit_304/512/latest.jpg',
];

$img = false;
foreach ($urls as $url) {
    $ctx = stream_context_create(['http' => [
        'timeout' => 8,
        'header' => "User-Agent: Mozilla/5.0 (craith.cloud Ham Dashboard)\r\n",
    ]]);
    $data = @file_get_contents($url, false, $ctx);
    if ($data && strlen($data) > 50000) {
        $img = $data;
        break;
    }
}

if ($img) {
    file_put_contents($cache_file, $img);
    header('Content-Type: image/jpeg');
    header('Cache-Control: public, max-age=300');
    echo $img;
} else {
    http_response_code(503);
}
