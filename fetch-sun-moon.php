<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$cache_file = '/tmp/sun_moon_cache_v7.json';
$lat = floatval($_GET['lat'] ?? 48.2082);
$lng = floatval($_GET['lng'] ?? 16.3738);
$dateStr = date('Y-m-d');
list($year, $month, $day) = explode('-', $dateStr);

if (isset($_GET['tz'])) {
    $tz_offset = floatval($_GET['tz']);
} else {
    $tz_offset = round($lng / 15);
}

$cache_key = md5("{$dateStr}_{$lat}_{$lng}_{$tz_offset}");
if (file_exists($cache_file)) {
    $cached = json_decode(file_get_contents($cache_file), true);
    if ($cached && ($cached['cache_key']??'') === $cache_key &&
        time() - ($cached['timestamp']??0) < 3600) {
        echo json_encode($cached); exit;
    }
}

// Get USNO sun/moon data
$url = "https://aa.usno.navy.mil/api/rstt/oneday?date={$dateStr}&coords={$lat},{$lng}&tz={$tz_offset}";
$ctx = stream_context_create(['http'=>['timeout'=>10]]);
$response = @file_get_contents($url, false, $ctx);
if (!$response) { http_response_code(503); echo json_encode(['error'=>'unavailable']); exit; }
$data = json_decode($response, true);
$props = $data['properties']['data'] ?? null;
if (!$props) { http_response_code(502); echo json_encode(['error'=>'bad response']); exit; }

$sun=['rise'=>'--:--','set'=>'--:--','transit'=>'--:--','civil_dawn'=>'--:--','civil_dusk'=>'--:--'];
foreach ($props['sundata']??[] as $i) {
    if($i['phen']==='Rise') $sun['rise']=$i['time'];
    if($i['phen']==='Set') $sun['set']=$i['time'];
    if($i['phen']==='Upper Transit') $sun['transit']=$i['time'];
    if($i['phen']==='Begin Civil Twilight') $sun['civil_dawn']=$i['time'];
    if($i['phen']==='End Civil Twilight') $sun['civil_dusk']=$i['time'];
}
$moon=['rise'=>'--:--','set'=>'--:--','transit'=>'--:--'];
foreach ($props['moondata']??[] as $i) {
    if($i['phen']==='Rise') $moon['rise']=$i['time'];
    if($i['phen']==='Set') $moon['set']=$i['time'];
    if($i['phen']==='Upper Transit') $moon['transit']=$i['time'];
}

function timeDiff($t1,$t2){if($t1==='--:--'||$t2==='--:--')return '--';list($h1,$m1)=array_map('intval',explode(':',$t1));list($h2,$m2)=array_map('intval',explode(':',$t2));$m=($h2*60+$m2)-($h1*60+$m1);if($m<0)$m+=1440;return floor($m/60).'h '.($m%60).'m';}

function getNextMoonPhases($year, $month, $day) {
    // Full Moons (timeanddate.de)
    $fullMoons = [
        '2026-01-10', '2026-02-09', '2026-03-03', '2026-04-02', '2026-05-02',
        '2026-05-31', '2026-06-30', '2026-07-29', '2026-08-28', '2026-09-27', '2026-10-26', '2026-11-25',
        '2026-12-24',
        '2027-01-24', '2027-02-23', '2027-03-24', '2027-04-23', '2027-05-23',
        '2027-06-21', '2027-07-21', '2027-08-19', '2027-09-18', '2027-10-18', '2027-11-17', '2027-12-16',
        '2028-01-15', '2028-02-14', '2028-03-14', '2028-04-13', '2028-05-12',
        '2028-06-11', '2028-07-10', '2028-08-09', '2028-09-07', '2028-10-07', '2028-11-05', '2028-12-05',
        '2029-01-04', '2029-02-02', '2029-03-04', '2029-04-03', '2029-05-02',
        '2029-06-01', '2029-07-01', '2029-07-30', '2029-08-29', '2029-09-28', '2029-10-27', '2029-11-26',
        '2029-12-26',
    ];
    
    // New Moons (timeanddate.de)
    $newMoons = [
        '2026-01-03', '2026-02-01', '2026-03-19', '2026-04-10', '2026-05-10',
        '2026-06-08', '2026-07-08', '2026-08-06', '2026-09-05', '2026-10-04', '2026-11-03', '2026-12-02',
        '2026-12-31',
        '2027-01-30', '2027-02-28', '2027-04-01', '2027-04-30', '2027-05-30',
        '2027-06-28', '2027-07-27', '2027-08-26', '2027-09-24', '2027-10-24', '2027-11-23', '2027-12-22',
        '2028-01-20', '2028-02-19', '2028-03-20', '2028-04-19', '2028-05-18',
        '2028-06-17', '2028-07-17', '2028-08-15', '2028-09-14', '2028-10-13', '2028-11-12', '2028-12-11',
        '2029-01-10', '2029-02-08', '2029-03-10', '2029-04-08', '2029-05-08',
        '2029-06-06', '2029-07-06', '2029-08-04', '2029-09-03', '2029-10-02', '2029-11-01', '2029-11-30',
        '2029-12-30',
    ];
    
    $checkDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
    $nextFullMoon = null;
    $nextNewMoon = null;
    
    // Find next Full Moon
    foreach ($fullMoons as $fm) {
        if ($fm > $checkDate) {
            $nextFullMoon = $fm;
            break;
        }
    }
    
    // Find next New Moon
    foreach ($newMoons as $nm) {
        if ($nm > $checkDate) {
            $nextNewMoon = $nm;
            break;
        }
    }
    
    // Format
    if ($nextNewMoon) {
        $dt_new = new DateTime($nextNewMoon);
        $newMoonDE = $dt_new->format('d. M.');
    } else {
        $newMoonDE = '—';
    }
    
    if ($nextFullMoon) {
        $dt_full = new DateTime($nextFullMoon);
        $fullMoonDE = $dt_full->format('d. M.');
    } else {
        $fullMoonDE = '—';
    }
    
    return [
        'new_moon_date' => $nextNewMoon ?? '',
        'new_moon_de' => $newMoonDE,
        'full_moon_date' => $nextFullMoon ?? '',
        'full_moon_de' => $fullMoonDE,
    ];
}

$nextPhases = getNextMoonPhases($year, $month, $day);

$result=[
    'cache_key'=>$cache_key, 'date'=>$dateStr, 'lat'=>$lat, 'lng'=>$lng, 'timestamp'=>time(),
    'timezone_offset'=>$tz_offset,
    'sunrise'=>$sun['rise'], 'sunset'=>$sun['set'], 'solar_noon'=>$sun['transit'],
    'civil_dawn'=>$sun['civil_dawn'], 'civil_dusk'=>$sun['civil_dusk'],
    'day_length'=>timeDiff($sun['rise'],$sun['set']),
    'moonrise'=>$moon['rise'], 'moonset'=>$moon['set'], 'moon_transit'=>$moon['transit'],
    'moon_phase'=>$props['curphase']??'', 'moon_illum'=>$props['fracillum']??'',
    'next_new_moon_date' => $nextPhases['new_moon_date'],
    'next_new_moon_de' => $nextPhases['new_moon_de'],
    'next_full_moon_date' => $nextPhases['full_moon_date'],
    'next_full_moon_de' => $nextPhases['full_moon_de'],
];

file_put_contents($cache_file, json_encode($result));
echo json_encode($result);
