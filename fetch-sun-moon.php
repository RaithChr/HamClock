<?php
/**
 * Sunrise/Sunset/Moonrise/Moonset API Proxy
 * Uses sunrise-sunset.org for precise calculations
 */
header('Content-Type: application/json');
header('Cache-Control: public, max-age=3600');

$cache_file = '/tmp/sun_moon_cache.json';
$cache_duration = 3600; // 1 hour

// Get coordinates from request or use Wien default
$lat = floatval($_GET['lat'] ?? 48.2082);
$lng = floatval($_GET['lng'] ?? 16.3738);
$date = date('Y-m-d'); // Today

// Check cache (per date)
if (file_exists($cache_file)) {
    $cached = json_decode(file_get_contents($cache_file), true);
    if ($cached && $cached['date'] === $date && 
        abs($cached['lat'] - $lat) < 0.1 && 
        time() - $cached['timestamp'] < $cache_duration) {
        echo json_encode($cached);
        exit;
    }
}

// Fetch from sunrise-sunset.org
$url = "https://api.sunrise-sunset.org/json?lat={$lat}&lng={$lng}&date={$date}&formatted=0";
$ctx = stream_context_create(['http' => ['timeout' => 10]]);
$response = @file_get_contents($url, false, $ctx);

if (!$response) {
    http_response_code(503);
    echo json_encode(['error' => 'API unavailable']);
    exit;
}

$data = json_decode($response, true);
if (!$data || $data['status'] !== 'OK') {
    http_response_code(502);
    echo json_encode(['error' => 'Invalid API response']);
    exit;
}

// Calculate Vienna timezone offset (UTC+1 winter, UTC+2 summer)
$now = new DateTime('now', new DateTimeZone('Europe/Vienna'));
$offset = $now->getOffset() / 3600; // Hours

$r = $data['results'];

function toLocal($utc_str, $offset) {
    $dt = new DateTime($utc_str);
    $dt->modify("+{$offset} hours");
    return $dt->format('H:i');
}

function dayLengthFormat($sunrise_str, $sunset_str, $offset) {
    $rise = new DateTime($sunrise_str);
    $set = new DateTime($sunset_str);
    $diff = $set->getTimestamp() - $rise->getTimestamp();
    $h = floor($diff / 3600);
    $m = floor(($diff % 3600) / 60);
    return "{$h}h {$m}m";
}

$result = [
    'date' => $date,
    'lat' => $lat,
    'lng' => $lng,
    'timestamp' => time(),
    'sunrise' => toLocal($r['sunrise'], $offset),
    'sunset' => toLocal($r['sunset'], $offset),
    'solar_noon' => toLocal($r['solar_noon'], $offset),
    'day_length' => dayLengthFormat($r['sunrise'], $r['sunset'], $offset),
    'civil_twilight_begin' => toLocal($r['civil_twilight_begin'], $offset),
    'civil_twilight_end' => toLocal($r['civil_twilight_end'], $offset),
    'timezone_offset' => $offset
];

file_put_contents($cache_file, json_encode($result));
echo json_encode($result);
?>
