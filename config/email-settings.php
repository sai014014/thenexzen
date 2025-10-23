<?php

/**
 * Email Configuration Settings
 * Update these values with your actual email credentials
 */

return [
    // Gmail Configuration (Recommended)
    'gmail' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls',
        'username' => 'your_email@gmail.com', // Replace with your Gmail
        'password' => 'your_app_password',     // Replace with your Gmail App Password
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

    // Active Configuration (change this to 'gmail', 'outlook', 'yahoo', or 'custom')
    'active' => 'gmail',
];
