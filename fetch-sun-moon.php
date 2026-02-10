<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
$cache_file = '/tmp/sun_moon_cache_v2.json';
$lat = floatval($_GET['lat'] ?? 48.2082);
$lng = floatval($_GET['lng'] ?? 16.3738);
$date = date('Y-m-d');
$viennaDT = new DateTime('now', new DateTimeZone('Europe/Vienna'));
$tz_offset = $viennaDT->getOffset() / 3600;
if (file_exists($cache_file)) {
    $cached = json_decode(file_get_contents($cache_file), true);
    if ($cached && $cached['date'] === $date && abs($cached['lat']-$lat)<0.01 && time()-($cached['timestamp']??0)<3600) {
        echo json_encode($cached); exit;
    }
}
$url = "https://aa.usno.navy.mil/api/rstt/oneday?date={$date}&coords={$lat},{$lng}&tz={$tz_offset}";
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
$result=['date'=>$date,'lat'=>$lat,'lng'=>$lng,'timestamp'=>time(),'timezone_offset'=>$tz_offset,
    'sunrise'=>$sun['rise'],'sunset'=>$sun['set'],'solar_noon'=>$sun['transit'],
    'civil_dawn'=>$sun['civil_dawn'],'civil_dusk'=>$sun['civil_dusk'],
    'day_length'=>timeDiff($sun['rise'],$sun['set']),
    'moonrise'=>$moon['rise'],'moonset'=>$moon['set'],'moon_transit'=>$moon['transit'],
    'moon_phase'=>$props['curphase']??'','moon_illum'=>$props['fracillum']??''];
file_put_contents($cache_file, json_encode($result));
echo json_encode($result);
?>