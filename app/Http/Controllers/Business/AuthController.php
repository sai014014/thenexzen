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
                'host' => 'mail.zenvueservices.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'vinay@zenvueservices.com',
                'password' => 'Zenvue@2025',
                'from_address' => 'info@nexzen.com',
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
            
            // Send OTP via email using custom configuration
            Mail::to($request->admin_email)->send(new BusinessRegistrationOTP(
                $otp,
                $request->business_name,
                $request->admin_name
            ));
            
            \Log::info("OTP sent successfully to {$request->admin_email} for business: {$request->business_name}");
            
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your email!',
                'session_id' => $sessionId,
                'email' => $request->admin_email
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to send OTP email: ' . $e->getMessage(), [
                'email' => $request->admin_email,
                'business_name' => $request->business_name,
                'error' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please check your email configuration or try again later.'
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
}
