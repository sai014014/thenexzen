<?php
// Simple test to check what's happening
header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'message' => 'Simple test is working',
    'time' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'laravel_exists' => file_exists('../artisan'),
    'test_otp_exists' => file_exists('../app/Http/Controllers/TestOTPController.php')
]);
?>
