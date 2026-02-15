<?php
/**
 * Get Real-Time System Statistics (CPU, RAM, Disk)
 * 
 * Returns JSON with:
 * - cpu_percent: CPU Auslastung %
 * - ram_percent: RAM Auslastung %
 * - disk_percent: Disk Auslastung %
 * - uptime: Server Uptime string
 * - timestamp: Zeitstempel UTC
 */

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// ============================================
// 1. CPU USAGE
// ============================================

function getCpuUsage() {
    $load = sys_getloadavg();
    $num_cpus = shell_exec('nproc') ? intval(shell_exec('nproc')) : 1;
    $cpu_percent = round(($load[0] / $num_cpus) * 100, 1);
    return min($cpu_percent, 100); // Cap at 100%
}

// ============================================
// 2. RAM USAGE
// ============================================

function getMemoryUsage() {
    $free = shell_exec('free | grep Mem | awk \'{print ($3/$2) * 100.0}\'');
    return round(floatval($free), 1);
}

// ============================================
// 3. DISK USAGE
// ============================================

function getDiskUsage($path = '/var/www/html') {
    $free = disk_free_space($path);
    $total = disk_total_space($path);
    
    if ($total == 0) return 0;
    return round((($total - $free) / $total) * 100, 1);
}

// ============================================
// 4. UPTIME
// ============================================

function getUptime() {
    $uptime = shell_exec('uptime -p 2>/dev/null');
    return trim($uptime) ?: 'Unknown';
}

// ============================================
// 5. CALLSIGN & QTH (FIXED)
// ============================================

$callsign = 'OE3LCR';
$qth = 'JN87ct';

// ============================================
// 6. BUILD RESPONSE
// ============================================

$response = [
    'timestamp' => date('Y-m-d\TH:i:s\Z'),
    'callsign' => $callsign,
    'qth' => $qth,
    'cpu_percent' => getCpuUsage(),
    'ram_percent' => getMemoryUsage(),
    'disk_percent' => getDiskUsage(),
    'uptime' => getUptime(),
    'status' => 'online'
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
