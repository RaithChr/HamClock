<?php
/**
 * Daily Status Email - FINAL FIX
 */

// Load .env
$env_file = '/var/www/html/.env';
if (!file_exists($env_file)) $env_file = '/home/chris-admin/.env';

if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

error_log("✅ Daily email started: " . date('Y-m-d H:i:s'));

$brevo_api_key = $_ENV['BREVO_API_KEY'] ?? '';
$sender_email = $_ENV['BREVO_EMAIL'] ?? 'noreply@craith.cloud';
$sender_name = $_ENV['BREVO_SENDER_NAME'] ?? 'Gwen';

if (!$brevo_api_key) {
    error_log("❌ No BREVO_API_KEY found");
    exit(1);
}

// Get System Stats
$cpu = trim(shell_exec("top -bn1 | grep 'Cpu(s)' | awk '{print $2}' | cut -d'%' -f1")) ?: '0';
$mem = trim(shell_exec("free -h | grep Mem | awk '{print $3, $2}'")) ?: 'N/A';
$disk = trim(shell_exec("df -h / | tail -1 | awk '{print $3, $2}'")) ?: 'N/A';
$uptime = trim(shell_exec("uptime | awk -F'up' '{print $2}'")) ?: 'N/A';

$body = "📡 OE3LCR Daily Status\n\n";
$body .= "Time: " . date('Y-m-d H:i:s UTC') . "\n\n";
$body .= "System Stats:\n";
$body .= "• CPU: " . $cpu . "%\n";
$body .= "• RAM: " . $mem . "\n";
$body .= "• Disk: " . $disk . "\n";
$body .= "• Uptime: " . $uptime . "\n";

$data = [
    "to" => [["email" => "raith.mobile@gmail.com"]],
    "sender" => ["name" => $sender_name, "email" => $sender_email],
    "subject" => "[OE3LCR] Daily Status - " . date('Y-m-d'),
    "htmlContent" => "<pre style='font-family:monospace'>" . htmlspecialchars($body) . "</pre>",
    "textContent" => $body,
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.brevo.com/v3/smtp/email",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "api-key: $brevo_api_key",
        "content-type: application/json",
    ],
    CURLOPT_POSTFIELDS => json_encode($data),
]);

$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($http_code >= 200 && $http_code < 300) {
    error_log("✅ Email sent successfully!");
    echo "✅ Status email sent!\n";
} else {
    error_log("❌ Email failed HTTP $http_code: $response");
    echo "❌ Failed: $http_code\n$response\n";
}
?>
