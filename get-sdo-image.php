<?php
/**
 * SDO/AIA Live Image Proxy
 * Maps view modes to actual NASA composite/single wavelength images
 */

$mode = isset($_GET['mode']) ? preg_replace('/[^a-z]/', '', strtolower($_GET['mode'])) : 'chromosphere';

// Map modes to actual NASA URLs
$modes = [
    'visible'       => 'https://sdo.gsfc.nasa.gov/assets/img/latest/latest_1024_HMIIF.jpg',
    'corona'        => 'https://sdo.gsfc.nasa.gov/assets/img/latest/f_094_335_193_1024.jpg',
    'chromosphere'  => 'https://sdo.gsfc.nasa.gov/assets/img/latest/f_304_211_171_1024.jpg',
    'quietcorona'   => 'https://sdo.gsfc.nasa.gov/assets/img/latest/f_304_211_171_1024.jpg',
    'flairing'      => 'https://sdo.gsfc.nasa.gov/assets/img/latest/f_094_335_193_1024.jpg',
];

if (!isset($modes[$mode])) {
    $mode = 'chromosphere';
}

$url = $modes[$mode];
$cache_file = "/tmp/sdo_cache_{$mode}.jpg";
$cache_time = 300; // 5 Minuten

// Cache prüfen
if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
    header('Content-Type: image/jpeg');
    header('Cache-Control: public, max-age=300');
    readfile($cache_file);
    exit;
}

// Von NASA holen
$ctx = stream_context_create([
    'http' => [
        'timeout' => 15,
        'header' => "User-Agent: Mozilla/5.0 (craith.cloud Ham Dashboard)\r\n",
    ],
    'ssl' => ['verify_peer' => false]
]);

$data = @file_get_contents($url, false, $ctx);

if ($data && strlen($data) > 50000) {
    file_put_contents($cache_file, $data);
    header('Content-Type: image/jpeg');
    header('Cache-Control: public, max-age=300');
    echo $data;
} else {
    http_response_code(503);
    echo "Unable to fetch SDO image";
}
