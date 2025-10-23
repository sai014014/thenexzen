<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Mail\BusinessRegistrationOTP;
use Exception;

class TestOTPController extends Controller
{
    /**
     * Get email configuration
     * Update these values with your actual email credentials
     */
    private function getEmailConfig()
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
                'host' => 'mail.zenvueservices.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'vinay@zenvueservices.com',
                'password' => 'Zenvue@2025',
                'from_address' => 'info@nexzen.com',
                'from_name' => 'NexZen',
            ],

            // ===========================================
            // CHANGE THIS TO SELECT WHICH CONFIG TO USE
            // Options: 'gmail', 'outlook', 'yahoo', 'custom'
            // ===========================================
            'active' => 'custom', // ← Using custom SMTP configuration
        ];
    }

    /**
     * Get active email configuration
     */
    private function getActiveEmailConfig()
    {
        $config = $this->getEmailConfig();
        $active = $config['active'] ?? 'gmail';
        return $config[$active] ?? $config['gmail'];
    }

    /**
     * Apply email configuration to Laravel config
     */
    private function applyEmailConfig()
    {
        $emailConfig = $this->getActiveEmailConfig();
        
        // Update mail configuration
        Config::set([
            'mail.mailers.smtp.host' => $emailConfig['host'],
            'mail.mailers.smtp.port' => $emailConfig['port'],
            'mail.mailers.smtp.encryption' => $emailConfig['encryption'],
            'mail.mailers.smtp.username' => $emailConfig['username'],
            'mail.mailers.smtp.password' => $emailConfig['password'],
            'mail.from.address' => $emailConfig['from_address'],
            'mail.from.name' => $emailConfig['from_name'],
        ]);
    }

    /**
     * Show the test OTP page
     */
    public function index()
    {
        return view('test-otp');
    }

    /**
     * Send a basic test email
     */
    public function sendBasic(Request $request)
    {
        try {
            // Apply email configuration
            $this->applyEmailConfig();
            
            $request->validate([
                'email' => 'required|email',
                'subject' => 'required|string',
                'message' => 'required|string'
            ]);

            $email = $request->email;
            $subject = $request->subject;
            $message = $request->message;

            // Send basic email using Laravel's mail
            Mail::raw($message, function ($mail) use ($email, $subject) {
                $mail->to($email)
                     ->subject($subject)
                     ->from(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info("Basic test email sent successfully to: {$email}");

            return response()->json([
                'success' => true,
                'message' => 'Basic email sent successfully!'
            ]);

        } catch (Exception $e) {
            Log::error('Basic email test failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send basic email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send OTP test email using the BusinessRegistrationOTP mail class
     */
    public function sendOTP(Request $request)
    {
        try {
            // Apply email configuration
            $this->applyEmailConfig();
            
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|string|size:6',
                'business_name' => 'required|string',
                'admin_name' => 'required|string'
            ]);

            $email = $request->email;
            $otp = $request->otp;
            $businessName = $request->business_name;
            $adminName = $request->admin_name;

            // Send OTP email using the existing mail class
            Mail::to($email)->send(new BusinessRegistrationOTP($otp, $businessName, $adminName));

            Log::info("OTP test email sent successfully to: {$email} for business: {$businessName}");

            return response()->json([
                'success' => true,
                'message' => 'OTP email sent successfully!'
            ]);

        } catch (Exception $e) {
            Log::error('OTP email test failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send email using custom SMTP configuration
     */
    public function sendCustomSMTP(Request $request)
    {
        try {
            $request->validate([
                'smtp_config' => 'required|array',
                'smtp_config.host' => 'required|string',
                'smtp_config.port' => 'required|integer',
                'smtp_config.username' => 'required|string',
                'smtp_config.password' => 'required|string',
                'smtp_config.encryption' => 'nullable|string',
                'smtp_config.from_name' => 'required|string',
                'email' => 'required|email',
                'subject' => 'required|string',
                'message' => 'required|string'
            ]);

            $smtpConfig = $request->smtp_config;
            $email = $request->email;
            $subject = $request->subject;
            $message = $request->message;

            // Temporarily change mail configuration
            $originalConfig = [
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'username' => config('mail.mailers.smtp.username'),
                'password' => config('mail.mailers.smtp.password'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
            ];

            // Set custom SMTP configuration
            Config::set('mail.mailers.smtp.host', $smtpConfig['host']);
            Config::set('mail.mailers.smtp.port', $smtpConfig['port']);
            Config::set('mail.mailers.smtp.username', $smtpConfig['username']);
            Config::set('mail.mailers.smtp.password', $smtpConfig['password']);
            Config::set('mail.mailers.smtp.encryption', $smtpConfig['encryption'] ?? 'tls');
            Config::set('mail.from.address', $smtpConfig['username']);
            Config::set('mail.from.name', $smtpConfig['from_name']);

            // Send email with custom configuration
            Mail::raw($message, function ($mail) use ($email, $subject, $smtpConfig) {
                $mail->to($email)
                     ->subject($subject)
                     ->from($smtpConfig['username'], $smtpConfig['from_name']);
            });

            // Restore original configuration
            Config::set('mail.mailers.smtp.host', $originalConfig['host']);
            Config::set('mail.mailers.smtp.port', $originalConfig['port']);
            Config::set('mail.mailers.smtp.username', $originalConfig['username']);
            Config::set('mail.mailers.smtp.password', $originalConfig['password']);
            Config::set('mail.mailers.smtp.encryption', $originalConfig['encryption']);
            Config::set('mail.from.address', $originalConfig['from_address']);
            Config::set('mail.from.name', $originalConfig['from_name']);

            Log::info("Custom SMTP email sent successfully to: {$email} using host: {$smtpConfig['host']}");

            return response()->json([
                'success' => true,
                'message' => 'Custom SMTP email sent successfully!'
            ]);

        } catch (Exception $e) {
            Log::error('Custom SMTP email test failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send custom SMTP email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check current email configuration
     */
    public function checkConfig()
    {
        try {
            // Clear any previous output
            if (ob_get_level()) {
                ob_clean();
            }
            
            $emailConfig = $this->getEmailConfig();
            $activeConfig = $this->getActiveEmailConfig();
            
            $config = [
                'active_provider' => $emailConfig['active'],
                'smtp_host' => $activeConfig['host'],
                'smtp_port' => $activeConfig['port'],
                'smtp_username' => $activeConfig['username'],
                'smtp_password' => $activeConfig['password'] ? '***configured***' : 'not_set',
                'smtp_encryption' => $activeConfig['encryption'],
                'from_address' => $activeConfig['from_address'],
                'from_name' => $activeConfig['from_name'],
                'app_env' => config('app.env'),
                'app_debug' => config('app.debug'),
                'log_level' => config('logging.level'),
            ];

            // Check if mail configuration is complete
            $config['mail_configured'] = !empty($activeConfig['username']) && 
                                       !empty($activeConfig['password']) &&
                                       $activeConfig['username'] !== 'your_email@gmail.com' &&
                                       $activeConfig['password'] !== 'your_app_password';

            // Show available providers
            $config['available_providers'] = array_keys(array_filter($emailConfig, function($key) {
                return $key !== 'active';
            }, ARRAY_FILTER_USE_KEY));

            return response()->json([
                'success' => true,
                'config' => $config,
                'message' => 'Configuration retrieved successfully'
            ]);

        } catch (Exception $e) {
            Log::error('Configuration check failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to check configuration: ' . $e->getMessage(),
                'error_details' => $e->getTraceAsString()
            ], 500);
        } catch (Error $e) {
            Log::error('Configuration check PHP Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'PHP Error: ' . $e->getMessage(),
                'error_details' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Test SMTP connection without sending email
     */
    public function testSMTPConnection(Request $request)
    {
        try {
            $request->validate([
                'smtp_config' => 'required|array',
                'smtp_config.host' => 'required|string',
                'smtp_config.port' => 'required|integer',
                'smtp_config.username' => 'required|string',
                'smtp_config.password' => 'required|string',
                'smtp_config.encryption' => 'nullable|string',
            ]);

            $smtpConfig = $request->smtp_config;

            // Test SMTP connection
            $connection = fsockopen($smtpConfig['host'], $smtpConfig['port'], $errno, $errstr, 30);
            
            if (!$connection) {
                return response()->json([
                    'success' => false,
                    'message' => "SMTP connection failed: {$errstr} ({$errno})"
                ]);
            }

            fclose($connection);

            return response()->json([
                'success' => true,
                'message' => 'SMTP connection successful!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SMTP connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
