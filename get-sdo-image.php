<?php
// PHP-Proxy für NASA/SOHO Sonnenbild (umgeht Browser-Blocks)
$cache_file = '/tmp/sdo_cache.jpg';
$cache_time = 300; // 5 Minuten

if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
    header('Content-Type: image/jpeg');
    header('Cache-Control: public, max-age=300');
    readfile($cache_file);
    exit;
}

// SDO direkt versuchen, dann SOHO als Fallback
$urls = [
    'https://sdo.gsfc.nasa.gov/assets/img/latest/latest_1024_211193171.jpg',
    'https://soho.nascom.nasa.gov/data/realtime/eit_304/512/latest.jpg',
    'https://soho.nascom.nasa.gov/data/realtime/hmi_igr/512/latest.jpg',
];

$img = false;
foreach ($urls as $url) {
    $ctx = stream_context_create(['http' => [
        'timeout' => 8,
        'header' => "User-Agent: Mozilla/5.0 (craith.cloud Ham Dashboard)\r\n",
    ]]);
    $data = @file_get_contents($url, false, $ctx);
    if ($data && strlen($data) > 10000) {
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
    echo 'Image unavailable';
}
