<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Business;
use App\Models\BusinessAdmin;
use App\Mail\BusinessRegistrationOTP;
use App\Mail\BusinessPasswordReset;

class AuthController extends Controller
{
    /**
     * Get email configuration (same as TestOTPController)
     */
    private function getEmailConfig()
    {
        return [
            // Custom SMTP Configuration (Working configuration)
            'custom' => [
                'host' => 'mail.hecown.com',
                'port' => 465,
                'encryption' => 'tls',
                'username' => 'arqam@hecown.com',
                'password' => 'Hecown@2025',
                'from_address' => 'info@hecown.com',
                'from_name' => 'NexZen',
            ],
            'active' => 'custom',
        ];
    }

    /**
     * Get active email configuration
     */
    private function getActiveEmailConfig()
    {
        $config = $this->getEmailConfig();
        $active = $config['active'] ?? 'custom';
        return $config[$active] ?? $config['custom'];
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

    public function showLoginForm()
    {
        return view('business.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([    
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::guard('business_admin')->attempt($credentials, $remember)) {
            $businessAdmin = Auth::guard('business_admin')->user();
            
            // Check if the business is active
            $business = $businessAdmin->business;
            if (!$business || $business->status !== 'active') {
                // Log out the user immediately
                Auth::guard('business_admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'email' => 'Your business account is currently inactive. Please contact support for assistance.',
                ])->onlyInput('email');
            }
            
            // Check if the business admin is active
            if (!$businessAdmin->is_active) {
                // Log out the user immediately
                Auth::guard('business_admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'email' => 'Your admin account is currently inactive. Please contact support for assistance.',
                ])->onlyInput('email');
            }
            
            $request->session()->regenerate();
            
            // Update last login info
            $businessAdmin->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Log login activity
            \App\Traits\LogsActivity::logActivity(
                'login',
                'Logged into the system',
                get_class($businessAdmin),
                $businessAdmin->id
            );

            return redirect()->intended(route('business.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('business.auth.register');
    }

    public function sendOTP(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255|unique:businesses,business_name',
            'business_type' => 'required|string|in:transportation,logistics,rental,other',
            'email' => 'required|email|max:255|unique:businesses,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:business_admins,email',
        ]);

        // Check for duplicate emails
        $existingBusiness = Business::where('email', $request->email)->first();
        $existingAdmin = BusinessAdmin::where('email', $request->admin_email)->first();
        
        if ($existingBusiness) {
            return response()->json([
                'success' => false,
                'message' => 'A business with this email already exists.'
            ], 400);
        }
        
        if ($existingAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'An admin with this email already exists.'
            ], 400);
        }

        // Generate OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $sessionId = Str::uuid();
        
        // Store OTP data in cache for 10 minutes
        Cache::put("otp_registration_{$sessionId}", [
            'otp' => $otp,
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'admin_name' => $request->admin_name,
            'admin_email' => $request->admin_email,
            'created_at' => now(),
        ], 600);

        try {
            // Apply custom email configuration
            $this->applyEmailConfig();
            
            // Log email attempt
            \Log::info("Attempting to send OTP email to {$request->admin_email} for business: {$request->business_name}");
            \Log::info("Email config: " . json_encode([
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'username' => config('mail.mailers.smtp.username'),
                'from' => config('mail.from.address')
            ]));
            
            // Send OTP via email using custom configuration
            Mail::to($request->admin_email)->send(new BusinessRegistrationOTP(
                $otp,
                $request->business_name,
                $request->admin_name
            ));
            
            \Log::info("OTP sent successfully to {$request->admin_email} for business: {$request->business_name}");
            
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your email! Please check your inbox (and spam folder).',
                'session_id' => $sessionId,
                'email' => $request->admin_email
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP email: ' . $e->getMessage(), [
                'email' => $request->admin_email,
                'business_name' => $request->business_name,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage() . '. Please check your email configuration.'
            ], 500);
        }
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
            'session_id' => 'required|string',
        ]);

        $otpData = Cache::get("otp_registration_{$request->session_id}");
        
        if (!$otpData) {
            return response()->json([
                'success' => false,
                'message' => 'OTP session expired. Please start registration again.'
            ], 400);
        }

        if ($otpData['otp'] !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP. Please try again.'
            ], 400);
        }

        // Mark OTP as verified
        $otpData['verified'] = true;
        Cache::put("otp_registration_{$request->session_id}", $otpData, 600);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully!'
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Verify OTP session
            $otpData = Cache::get("otp_registration_{$request->session_id}");
            
            if (!$otpData) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP session expired. Please start registration again.'
                ], 400);
            }

            if (!isset($otpData['verified']) || !$otpData['verified']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your OTP first.'
                ], 400);
            }

            DB::beginTransaction();

            // Create Business
            $business = Business::create([
                'business_name' => $otpData['business_name'],
                'business_slug' => Str::slug($otpData['business_name']),
                'business_type' => $otpData['business_type'],
                'description' => null,
                'email' => $otpData['email'],
                'phone' => $otpData['phone'],
                'address' => $otpData['address'],
                'city' => 'Not specified',
                'state' => 'Not specified',
                'country' => 'India',
                'postal_code' => '000000',
                'website' => null,
                'status' => 'active', // Auto-approve after OTP verification
                'subscription_plan' => 'basic',
                'subscription_expires_at' => now()->addDays(30),
                'is_verified' => true,
            ]);

            // Create Business Admin
            $businessAdmin = BusinessAdmin::create([
                'business_id' => $business->id,
                'name' => $otpData['admin_name'],
                'email' => $otpData['admin_email'],
                'password' => Hash::make($request->password),
                'phone' => $otpData['phone'],
                'role' => 'admin',
                'permissions' => json_encode(['all']),
                'is_active' => true,
            ]);

            // Clear OTP data from cache
            Cache::forget("otp_registration_{$request->session_id}");

            // Auto-login the user
            Auth::guard('business_admin')->login($businessAdmin);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Business registration successful! You have been logged in.',
                'redirect_url' => route('business.dashboard')
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            \Log::error('Business registration database error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Registration failed due to database error. Please try again.'
            ], 500);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Business registration error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again later.'
            ], 500);
        }
    }

    public function resendOTP(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        $otpData = Cache::get("otp_registration_{$request->session_id}");
        
        if (!$otpData) {
            return response()->json([
                'success' => false,
                'message' => 'OTP session expired. Please start registration again.'
            ], 400);
        }

        // Generate new OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $otpData['otp'] = $otp;
        $otpData['created_at'] = now();
        
        Cache::put("otp_registration_{$request->session_id}", $otpData, 600);

        try {
            // Apply custom email configuration
            $this->applyEmailConfig();
            
            // Resend OTP email using custom configuration
            Mail::to($otpData['admin_email'])->send(new BusinessRegistrationOTP(
                $otp,
                $otpData['business_name'],
                $otpData['admin_name']
            ));
            
            \Log::info("OTP resent successfully to {$otpData['admin_email']} for business: {$otpData['business_name']}");
            
            return response()->json([
                'success' => true,
                'message' => 'New OTP sent successfully to your email!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to resend OTP email: ' . $e->getMessage(), [
                'email' => $otpData['admin_email'],
                'business_name' => $otpData['business_name'],
                'error' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP. Please try again later.'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        // Log logout activity before logging out
        if ($businessAdmin) {
            \App\Traits\LogsActivity::logActivity(
                'logout',
                'Logged out of the system',
                get_class($businessAdmin),
                $businessAdmin->id
            );
        }
        
        Auth::guard('business_admin')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('business.login');
    }

    // Password Reset Methods
    public function showLinkRequestForm()
    {
        return view('business.auth.password.forgot');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $businessAdmin = BusinessAdmin::where('email', $request->email)->first();

        if (!$businessAdmin) {
            // Don't reveal if email exists or not for security
            return back()->with('status', 'If that email address exists in our system, we will send you a password reset link.');
        }

        // Generate password reset token
        $token = Str::random(60);
        
        // Store in cache for 60 minutes
        Cache::put("password_reset_token_{$token}", [
            'email' => $businessAdmin->email,
            'user_id' => $businessAdmin->id,
            'created_at' => now()
        ], 3600);

        try {
            // Apply email configuration
            $this->applyEmailConfig();
            
            // Send password reset email
            Mail::to($businessAdmin->email)->send(new BusinessPasswordReset($token, $businessAdmin));
            
            \Log::info("Password reset email sent to {$businessAdmin->email}");
            
            return back()->with('status', 'Password reset link sent successfully! Please check your email.');
            
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
            
            return back()->withErrors(['email' => 'Failed to send password reset email. Please try again later.']);
        }
    }

    public function showResetForm(Request $request, $token)
    {
        $resetData = Cache::get("password_reset_token_{$token}");
        
        if (!$resetData) {
            return redirect()->route('business.password.request')
                ->withErrors(['token' => 'This password reset token is invalid or has expired.']);
        }

        return view('business.auth.password.reset', ['token' => $token, 'email' => $resetData['email']]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $resetData = Cache::get("password_reset_token_{$request->token}");
        
        if (!$resetData) {
            return back()->withErrors(['token' => 'This password reset token is invalid or has expired.']);
        }

        if ($resetData['email'] !== $request->email) {
            return back()->withErrors(['email' => 'Email does not match.']);
        }

        // Find the user
        $businessAdmin = BusinessAdmin::where('email', $request->email)->first();
        
        if (!$businessAdmin) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        // Update password
        $businessAdmin->password = Hash::make($request->password);
        $businessAdmin->save();

        // Delete the token
        Cache::forget("password_reset_token_{$request->token}");

        \Log::info("Password reset successfully for user: {$businessAdmin->email}");

        return redirect()->route('business.login')
            ->with('status', 'Password has been reset successfully. You can now login with your new password.');
    }
}
