<?php
// Daily notification generation script
// Run this via cron: 0 9 * * * php /path/to/your/project/generate_notifications.php

// Log file path
$logFile = __DIR__ . '/storage/logs/cron_notifications.log';

// Function to log messages
function logMessage($message, $logFile) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    echo $logEntry;
}

// Start logging
logMessage("=== CRON JOB STARTED ===", $logFile);

try {
    require_once __DIR__ . '/vendor/autoload.php';
    logMessage("✅ Laravel autoloader loaded", $logFile);

    // Bootstrap Laravel
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    logMessage("✅ Laravel application bootstrapped", $logFile);

    $notificationService = new NotificationService();
    logMessage("✅ NotificationService instantiated", $logFile);
    
    // Generate all notifications
    $notificationService->generateAllNotifications();
    logMessage("✅ Notifications generated successfully", $logFile);
    
    // Clean up old notifications (older than 30 days)
    $deletedCount = $notificationService->cleanupOldNotifications(30);
    logMessage("🗑️ Cleaned up {$deletedCount} old notifications", $logFile);
    
    logMessage("=== CRON JOB COMPLETED SUCCESSFULLY ===", $logFile);
    
} catch (Exception $e) {
    logMessage("❌ ERROR: " . $e->getMessage(), $logFile);
    logMessage("❌ Stack trace: " . $e->getTraceAsString(), $logFile);
    logMessage("=== CRON JOB FAILED ===", $logFile);
    exit(1);
}
?>
