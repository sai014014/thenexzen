<?php

namespace App\Helpers;

/**
 * Email Configuration Helper
 * Update your email settings here directly in the code
 */
class EmailConfig
{
    /**
     * Get email configuration
     * Update these values with your actual email credentials
     */
    public static function getConfig()
    {
        return [
            // ===========================================
            // UPDATE THESE VALUES WITH YOUR EMAIL DETAILS
            // ===========================================
            
            // Gmail Configuration (Recommended)
            'gmail' => [
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'your_email@gmail.com', // ← Replace with your Gmail
                'password' => 'your_app_password',     // ← Replace with your Gmail App Password
                'from_address' => 'your_email@gmail.com',
                'from_name' => 'NexZen',
            ],

            // Outlook/Hotmail Configuration
            'outlook' => [
                'host' => 'smtp-mail.outlook.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'your_email@outlook.com',
                'password' => 'your_password',
                'from_address' => 'your_email@outlook.com',
                'from_name' => 'NexZen',
            ],

            // Yahoo Configuration
            'yahoo' => [
                'host' => 'smtp.mail.yahoo.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'your_email@yahoo.com',
                'password' => 'your_app_password',
                'from_address' => 'your_email@yahoo.com',
                'from_name' => 'NexZen',
            ],

            // Custom SMTP Configuration
            'custom' => [
                'host' => 'your_smtp_host.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'your_username',
                'password' => 'your_password',
                'from_address' => 'your_email@yourdomain.com',
                'from_name' => 'NexZen',
            ],

            // ===========================================
            // CHANGE THIS TO SELECT WHICH CONFIG TO USE
            // Options: 'gmail', 'outlook', 'yahoo', 'custom'
            // ===========================================
            'active' => 'gmail', // ← Change this to switch email providers
        ];
    }

    /**
     * Get active email configuration
     */
    public static function getActiveConfig()
    {
        $config = self::getConfig();
        $active = $config['active'] ?? 'gmail';
        return $config[$active] ?? $config['gmail'];
    }

    /**
     * Apply email configuration to Laravel config
     */
    public static function applyConfig()
    {
        $emailConfig = self::getActiveConfig();
        
        // Update mail configuration
        config([
            'mail.mailers.smtp.host' => $emailConfig['host'],
            'mail.mailers.smtp.port' => $emailConfig['port'],
            'mail.mailers.smtp.encryption' => $emailConfig['encryption'],
            'mail.mailers.smtp.username' => $emailConfig['username'],
            'mail.mailers.smtp.password' => $emailConfig['password'],
            'mail.from.address' => $emailConfig['from_address'],
            'mail.from.name' => $emailConfig['from_name'],
        ]);
    }
}
