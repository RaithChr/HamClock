<?php
/**
 * Email Sender via Brevo (Sendinblue) API
 * Uses environment variables from ~/.env
 */

// Load .env file
$env_file = '/home/chris-admin/.env';
if (file_exists($env_file)) {
    $env_vars = parse_ini_file($env_file);
    foreach ($env_vars as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Get API Key
$api_key = $_ENV['BREVO_API_KEY'] ?? getenv('BREVO_API_KEY');
if (!$api_key) {
    die(json_encode(['error' => 'BREVO_API_KEY not found in .env']));
}

/**
 * Send email via Brevo API
 */
function sendEmailViaBrevo($to, $subject, $html_content, $text_content = null) {
    $api_key = $_ENV['BREVO_API_KEY'] ?? getenv('BREVO_API_KEY');
    $sender_email = $_ENV['BREVO_EMAIL'] ?? 'myhoney@craith.cloud';
    $sender_name = $_ENV['BREVO_SENDER_NAME'] ?? 'Gwen';
    
    // Brevo API v3 endpoint
    $url = 'https://api.brevo.com/v3/smtp/email';
    
    $payload = [
        'sender' => [
            'name' => $sender_name,
            'email' => $sender_email
        ],
        'to' => [
            [
                'email' => $to,
                'name' => $to
            ]
        ],
        'subject' => $subject,
        'htmlContent' => $html_content,
        'textContent' => $text_content ?? strip_tags($html_content)
    ];
    
    // cURL request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'api-key: ' . $api_key,
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    return [
        'status' => $http_code,
        'success' => ($http_code === 201),
        'response' => $result,
        'timestamp' => date('Y-m-d H:i:s UTC')
    ];
}

// Example: Send test email if called directly
if (php_sapi_name() === 'cli') {
    echo "Testing Brevo Email...\n";
    
    $result = sendEmailViaBrevo(
        'chris-admin@craith.cloud',
        'Test Email from Gwen',
        '<h1>Hello!</h1><p>This is a test email from Gwen (myhoney) ✨</p>',
        'Hello! This is a test email from Gwen.'
    );
    
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    echo "\n";
    
    if ($result['success']) {
        echo "✅ Email sent successfully!\n";
        exit(0);
    } else {
        echo "❌ Failed to send email\n";
        exit(1);
    }
}
?>
