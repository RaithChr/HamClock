<?php
/**
 * Fetch current TLE data from CelesTrak API v1
 * Updates satellite tracking data for craith.cloud
 * Runs 3-4x daily via cron
 */

// Output file
$outputFile = '/var/www/html/satellite-data.json';

// Satellites we want to track
$satellites = [
    'ISS (ZARYA)',         // International Space Station
    'NOAA 20',             // Weather satellite (JPSS-1)
    'NOAA 21',             // Weather satellite (JPSS-2)
    'METEOR-M 2-3',        // Russian weather satellite
    'METEOR-M 2-4',        // Russian weather satellite
    'HUBBLE SPACE TELESCOPE' // Space telescope
];

// CelesTrak API endpoints (new JSON format)
$apiUrl = 'https://celestrak.com/api/v1/globalstars/tle.json';

// Fetch data
$json = @file_get_contents($apiUrl);

if ($json === false) {
    error_log('CelesTrak API fetch failed at ' . date('Y-m-d H:i:s'));
    http_response_code(503);
    echo json_encode(['error' => 'Failed to fetch TLE data from CelesTrak']);
    exit(1);
}

$data = json_decode($json, true);

if (!$data || !is_array($data)) {
    error_log('CelesTrak API returned invalid JSON at ' . date('Y-m-d H:i:s'));
    http_response_code(503);
    echo json_encode(['error' => 'Invalid JSON from CelesTrak']);
    exit(1);
}

// Extract our satellites from the API response
$satelliteData = [];
$colors = [
    'ISS (ZARYA)' => '#00ff88',
    'NOAA 20' => '#ffa502',
    'NOAA 21' => '#70a1ff',
    'METEOR-M 2-3' => '#ff6b88',
    'METEOR-M 2-4' => '#88ff88',
    'HUBBLE SPACE TELESCOPE' => '#ff88ff'
];

foreach ($data as $sat) {
    $name = isset($sat['OBJECT_NAME']) ? trim($sat['OBJECT_NAME']) : '';
    
    foreach ($satellites as $searchName) {
        if (stripos($name, $searchName) !== false) {
            // Found a match!
            $displayName = str_replace(' (ZARYA)', '', $name);
            if ($name === 'ISS (ZARYA)') $displayName = 'ISS';
            
            $satelliteData[$displayName] = [
                'norad_id' => $sat['NORAD_CAT_ID'] ?? null,
                'tle1' => $sat['TLE_LINE1'] ?? '',
                'tle2' => $sat['TLE_LINE2'] ?? '',
                'epoch' => $sat['EPOCH'] ?? '',
                'color' => $colors[$searchName] ?? '#ffffff',
                'updated_at' => date('Y-m-d H:i:s UTC')
            ];
            
            error_log("Updated TLE for: $displayName (NORAD: {$sat['NORAD_CAT_ID']})");
            break;
        }
    }
}

// Save to JSON file
$output = [
    'version' => '1.0',
    'source' => 'CelesTrak API v1',
    'updated_at' => date('Y-m-d H:i:s UTC'),
    'update_frequency' => '3-4 times daily',
    'observer' => [
        'location' => 'OE3LCR (JN87ct)',
        'latitude' => 47.8125,
        'longitude' => 16.2083,
        'elevation_m' => 200
    ],
    'satellites' => $satelliteData
];

$json_output = json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

if (file_put_contents($outputFile, $json_output) === false) {
    error_log("Failed to write satellite-data.json at " . date('Y-m-d H:i:s'));
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save data']);
    exit(1);
}

// Set permissions
chmod($outputFile, 0644);

// Log success
error_log("Successfully updated satellite-data.json with " . count($satelliteData) . " satellites at " . date('Y-m-d H:i:s UTC'));

// Return status
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'satellites_updated' => count($satelliteData),
    'timestamp' => date('Y-m-d H:i:s UTC'),
    'next_update' => 'Scheduled in 6 hours'
]);
exit(0);
?>
