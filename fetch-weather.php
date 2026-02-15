<?php
// fetch-weather.php — OpenWeatherMap One Call 3.0 proxy with 10-min cache
// API key is loaded from .env (never hardcoded / never in git)

// Load .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            [$k, $v] = explode('=', $line, 2);
            $_ENV[trim($k)] = trim($v);
        }
    }
}

$apiKey = $_ENV['OWM_API_KEY'] ?? '';
if (!$apiKey) {
    http_response_code(500);
    echo json_encode(['error' => 'OWM_API_KEY not configured in .env']);
    exit;
}

$lat = floatval($_GET['lat'] ?? 47.8125);
$lon = floatval($_GET['lon'] ?? 16.2083);

if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid coordinates']);
    exit;
}

$cacheFile = sys_get_temp_dir() . '/owm_' . md5($lat . '_' . $lon) . '.json';
$cacheTTL  = 600; // 10 minutes

if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTTL) {
    header('Content-Type: application/json');
    readfile($cacheFile);
    exit;
}

$url = "https://api.openweathermap.org/data/3.0/onecall?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric&exclude=minutely,hourly,daily,alerts";

$ctx = stream_context_create(['http' => [
    'timeout'    => 10,
    'user_agent' => 'HamDashboard/1.0 OE3LCR',
]]);

$raw = @file_get_contents($url, false, $ctx);

if ($raw === false) {
    http_response_code(502);
    echo json_encode(['error' => 'upstream fetch failed']);
    exit;
}

file_put_contents($cacheFile, $raw);
header('Content-Type: application/json');
echo $raw;
