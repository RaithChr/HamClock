<?php
/**
 * N0NBH Band Conditions - HamQSL XML Parser
 * Fetches real band conditions from hamqsl.com/solarxml.php
 * Parses XML to JSON with caching
 */

header('Content-Type: application/json');
header('Cache-Control: public, max-age=3600'); // 1 hour cache

$cache_file = '/tmp/n0nbh_cache.json';
$cache_duration = 3600; // 1 hour (N0NBH updates every 3 hours)

// Check cache
if (file_exists($cache_file) && time() - filemtime($cache_file) < $cache_duration) {
    readfile($cache_file);
    exit;
}

// Fetch XML from hamqsl.com
try {
    $url = 'https://www.hamqsl.com/solarxml.php';
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 15,
            'user_agent' => 'craith.cloud/1.0'
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_host' => true
        ]
    ]);
    
    $xml_response = @file_get_contents($url, false, $context);
    
    if ($xml_response === false) {
        http_response_code(503);
        echo json_encode(['error' => 'HamQSL API unavailable', 'fallback' => 'fair']);
        exit;
    }
    
    // Parse XML to JSON
    $data = parseN0NBHxml($xml_response);
    
    if (!$data) {
        http_response_code(502);
        echo json_encode(['error' => 'Failed to parse N0NBH XML']);
        exit;
    }
    
    $json_response = json_encode($data);
    
    // Cache
    file_put_contents($cache_file, $json_response);
    
    echo $json_response;
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function parseN0NBHxml($xml) {
    $get = function($tag) use ($xml) {
        $m = [];
        if (preg_match("/<$tag>([^<]*)<\/$tag>/", $xml, $m)) {
            return trim($m[1]);
        }
        return null;
    };
    
    $bandConditions = [];
    $bandRegex = '/<band name="([^"]+)" time="([^"]+)">([^<]+)<\/band>/';
    preg_match_all($bandRegex, $xml, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $name = $match[1];
        // Only HF bands (80m-40m, 30m-20m, etc)
        if (preg_match('/m-|m\s/', $name)) {
            $bandConditions[] = [
                'name' => $name,
                'time' => $match[2],
                'condition' => trim($match[3])
            ];
        }
    }
    
    return [
        'bandConditions' => $bandConditions,
        'solarData' => [
            'aIndex' => $get('aindex'),
            'kIndex' => $get('kindex'),
            'sfi' => $get('solarflux'),
            'muf' => $get('muf'),
            'updated' => $get('updated')
        ],
        'updated' => $get('updated')
    ];
}
?>
