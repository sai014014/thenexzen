<?php

// Simple test file to check if the routes are working
// Access this via: http://localhost/nexzen/test-routes.php

echo "<h1>Route Test</h1>";

// Test if we can access the test-otp route
$baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);

echo "<h2>Testing Routes:</h2>";

// Test the main test-otp page
echo "<p><a href='{$baseUrl}/test-otp' target='_blank'>Test OTP Page</a></p>";

// Test the check-config endpoint
echo "<p><a href='{$baseUrl}/test-otp/check-config' target='_blank'>Check Config API</a></p>";

// Test the old test route
echo "<p><a href='{$baseUrl}/test-otp-email' target='_blank'>Old Test Route</a></p>";

echo "<h2>Expected Results:</h2>";
echo "<ul>";
echo "<li>Test OTP Page should show the email testing interface</li>";
echo "<li>Check Config API should return JSON with email configuration</li>";
echo "<li>Old Test Route should return JSON with mail config</li>";
echo "</ul>";

echo "<h2>If you see HTML errors instead of JSON:</h2>";
echo "<p>This means there's a PHP error. Check the Laravel logs at: storage/logs/laravel.log</p>";

echo "<h2>Quick PHP Info:</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Directory: " . getcwd() . "</p>";
echo "<p>Laravel App Path: " . (file_exists('artisan') ? 'Found' : 'Not Found') . "</p>";
