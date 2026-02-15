<?php
/**
 * DX Cluster Proxy — vermeidet Browser forbidden-header Problem
 * Cached 60 Sekunden
 */
header('Content-Type: text/plain; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache');

$cache_file = __DIR__ . '/dx-cache.txt';
$cache_ttl  = 60; // Sekunden

// Cache check
if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_ttl)) {
    readfile($cache_file);
    exit;
}

$ctx = stream_context_create(['http' => [
    'timeout' => 10,
    'header'  => "User-Agent: Mozilla/5.0 (compatible; OE3LCR-Dashboard/1.0)\r\n",
]]);
$data = @file_get_contents('https://www.hamqth.com/dxc_csv.php?limit=20', false, $ctx);

if ($data === false) {
    http_response_code(502);
    echo 'ERROR: upstream fetch failed';
    exit;
}

file_put_contents($cache_file, $data);
echo $data;
