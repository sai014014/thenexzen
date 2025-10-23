<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\SuperAdmin\AuthController as SuperAdminAuthController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\BusinessController as SuperAdminBusinessController;
use App\Http\Controllers\SuperAdmin\BugController as SuperAdminBugController;
use App\Http\Controllers\Business\AuthController as BusinessAuthController;
use App\Http\Controllers\Business\DashboardController as BusinessDashboardController;
use App\Http\Controllers\TestOTPController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Test OTP email route (remove in production)
Route::get('/test-otp-email', function () {
    try {
        $otp = '123456';
        $businessName = 'Test Business';
        $adminName = 'Test Admin';
        $email = 'test@example.com';
        
        \Mail::to($email)->send(new \App\Mail\BusinessRegistrationOTP($otp, $businessName, $adminName));
        
        return response()->json([
            'success' => true,
            'message' => 'Test OTP email sent successfully!',
            'mail_config' => [
                'driver' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'from' => config('mail.from.address'),
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to send test email: ' . $e->getMessage(),
            'error' => $e->getTraceAsString()
        ], 500);
    }
})->name('test.otp.email');

// Advanced OTP Testing Routes
Route::prefix('test-otp')->group(function () {
    Route::get('/', [TestOTPController::class, 'index'])->name('test.otp.index');
    Route::post('/send-basic', [TestOTPController::class, 'sendBasic'])->name('test.otp.send.basic');
    Route::post('/send-otp', [TestOTPController::class, 'sendOTP'])->name('test.otp.send.otp');
    Route::post('/send-custom-smtp', [TestOTPController::class, 'sendCustomSMTP'])->name('test.otp.send.custom.smtp');
    Route::get('/check-config', [TestOTPController::class, 'checkConfig'])->name('test.otp.check.config');
    Route::post('/test-smtp-connection', [TestOTPController::class, 'testSMTPConnection'])->name('test.otp.test.smtp.connection');
});

// Alternative routes without prefix (backup) - These should work
Route::get('/test-otp-check-config', [TestOTPController::class, 'checkConfig']);
Route::post('/test-otp-send-basic', [TestOTPController::class, 'sendBasic']);
Route::post('/test-otp-send-otp', [TestOTPController::class, 'sendOTP']);
Route::post('/test-otp-send-custom-smtp', [TestOTPController::class, 'sendCustomSMTP']);

// Test business registration OTP directly
Route::post('/test-business-otp', function(\Illuminate\Http\Request $request) {
    try {
        $controller = new \App\Http\Controllers\Business\AuthController();
        return $controller->sendOTP($request);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Exception: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    } catch (Error $e) {
        return response()->json([
            'success' => false,
            'message' => 'PHP Error: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Simple working routes for testing
Route::get('/email-config', function() {
    $controller = new \App\Http\Controllers\TestOTPController();
    return $controller->checkConfig();
});

Route::post('/email-test-basic', function(\Illuminate\Http\Request $request) {
    $controller = new \App\Http\Controllers\TestOTPController();
    return $controller->sendBasic($request);
});

Route::post('/email-test-otp', function(\Illuminate\Http\Request $request) {
    $controller = new \App\Http\Controllers\TestOTPController();
    return $controller->sendOTP($request);
});

Route::get('/', function () {
    return view('welcome');
});

// Contact page route
Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Contact form submission route
Route::post('/request-form-submit', function (Illuminate\Http\Request $request) {
    // Handle contact form submission
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'company' => 'required|string|max:255',
        'message' => 'nullable|string|max:1000',
    ]);
    
    // Here you can save to database or send email
    // For now, just return success
    return response()->json([
        'status' => 'success', 
        'message' => 'Message sent successfully'
    ]);
})->name('contact.submit');

// Redirect /home to business dashboard for authenticated users
Route::get('/home', function () {
    return redirect()->route('business.dashboard');
})->middleware(['business_admin', 'check.business.status']);

// Super Admin Routes
Route::prefix('super-admin')->name('super-admin.')->group(function () {
    // Authentication Routes
    Route::middleware('guest:super_admin')->group(function () {
        Route::get('/login', [SuperAdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [SuperAdminAuthController::class, 'login']);
    });

    Route::middleware('super_admin')->group(function () {
        Route::post('/logout', [SuperAdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        
        // Business Management Routes
        Route::resource('businesses', SuperAdminBusinessController::class);
        Route::patch('/businesses/{business}/status', [SuperAdminBusinessController::class, 'updateStatus'])->name('businesses.update-status');
        
        // Bug Tracking Routes
        Route::patch('/bugs/{bug}/update-status', [SuperAdminBugController::class, 'updateStatus'])->name('bugs.update-status');
        Route::delete('/bug-attachments/{attachment}', [SuperAdminBugController::class, 'deleteAttachment'])->name('bug-attachments.destroy');
        Route::resource('bugs', SuperAdminBugController::class);
        
        // Cache Management Routes
        Route::post('/cache/clear', [\App\Http\Controllers\SuperAdmin\CacheController::class, 'clearAllCache'])->name('cache.clear');
        Route::get('/cache/status', [\App\Http\Controllers\SuperAdmin\CacheController::class, 'getCacheStatus'])->name('cache.status');
    });
});


Route::prefix('business/api')->name('business.api.')->group(function () {
    Route::get('/vehicle-makes', [\App\Http\Controllers\Api\VehicleMakeModelController::class, 'getMakes'])->name('vehicle-makes');
    Route::get('/vehicle-models', [\App\Http\Controllers\Api\VehicleMakeModelController::class, 'getModels'])->name('vehicle-models');
});

// Business Admin Routes
Route::prefix('business')->name('business.')->group(function () {
    // Authentication Routes
    Route::middleware('guest:business_admin')->group(function () {
        Route::get('/login', [BusinessAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [BusinessAuthController::class, 'login']);
        Route::get('/register', [BusinessAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register/send-otp', [BusinessAuthController::class, 'sendOTP'])->name('register.send-otp');
        Route::post('/register/verify-otp', [BusinessAuthController::class, 'verifyOTP'])->name('register.verify-otp');
        Route::post('/register/resend-otp', [BusinessAuthController::class, 'resendOTP'])->name('register.resend-otp');
        Route::post('/register', [BusinessAuthController::class, 'register'])->name('register.submit');
    });

    // Vehicle API Routes for dropdowns (accessible without authentication)
    Route::get('/api/vehicle-makes', [\App\Http\Controllers\Api\VehicleMakeModelController::class, 'getMakes'])->name('api.vehicle-makes');
    Route::get('/api/vehicle-models', [\App\Http\Controllers\Api\VehicleMakeModelController::class, 'getModels'])->name('api.vehicle-models');
    Route::get('/api/vehicle-makes-with-models', [\App\Http\Controllers\Api\VehicleApiController::class, 'getMakesWithModels'])->name('api.vehicle-makes-with-models');
    

    Route::middleware(['business_admin', 'check.business.status'])->group(function () {
        Route::post('/logout', [BusinessAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [BusinessDashboardController::class, 'index'])->name('dashboard');
        
        
        // Vehicle Management Routes
        Route::resource('vehicles', \App\Http\Controllers\Business\VehicleController::class);
        Route::post('/vehicles/{vehicle}/toggle-availability', [\App\Http\Controllers\Business\VehicleController::class, 'toggleAvailability'])->name('vehicles.toggle-availability');
        Route::get('/vehicles/{vehicle}/download/{type}', [\App\Http\Controllers\Business\VehicleController::class, 'downloadDocument'])->name('vehicles.download-document');
        
        // Customer Management Routes
        Route::resource('customers', \App\Http\Controllers\Business\CustomerController::class);
        Route::patch('/customers/{customer}/status', [\App\Http\Controllers\Business\CustomerController::class, 'updateStatus'])->name('customers.update-status');
        Route::post('/customers/{customer}/status', [\App\Http\Controllers\Business\CustomerController::class, 'updateStatus'])->name('customers.update-status-post');
        Route::get('/customers/{customer}/download/{type}', [\App\Http\Controllers\Business\CustomerController::class, 'downloadDocument'])->name('customers.download-document');
        Route::get('/customers/{customer}/drivers/{driver}/download/{type}', [\App\Http\Controllers\Business\CustomerController::class, 'downloadDriverDocument'])->name('customers.download-driver-document');
        
        // Vendor Management Routes
        Route::get('/vendors/search', [\App\Http\Controllers\Business\VendorController::class, 'search'])->name('vendors.search');
        Route::resource('vendors', \App\Http\Controllers\Business\VendorController::class);
        Route::get('/vendors/{vendor}/download/{type}', [\App\Http\Controllers\Business\VendorController::class, 'downloadDocument'])->name('vendors.download-document');
        
        // Booking Management Routes
        Route::get('/bookings/quick-create', [\App\Http\Controllers\Business\BookingController::class, 'quickCreate'])->name('bookings.quick-create');
        Route::resource('bookings', \App\Http\Controllers\Business\BookingController::class);
        Route::post('/bookings/{booking}/start', [\App\Http\Controllers\Business\BookingController::class, 'start'])->name('bookings.start');
        Route::post('/bookings/{booking}/complete', [\App\Http\Controllers\Business\BookingController::class, 'complete'])->name('bookings.complete');
        Route::post('/bookings/{booking}/cancel', [\App\Http\Controllers\Business\BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::get('/bookings/available-vehicles', [\App\Http\Controllers\Business\BookingController::class, 'getAvailableVehicles'])->name('bookings.available-vehicles');
        Route::post('/bookings/calculate-pricing', [\App\Http\Controllers\Business\BookingController::class, 'calculatePricing'])->name('bookings.calculate-pricing');
        
        // 5-Stage Booking Flow Routes
        Route::prefix('bookings/flow')->name('bookings.flow.')->group(function () {
            Route::get('/step1', [\App\Http\Controllers\Business\BookingController::class, 'createStep1'])->name('step1');
            Route::post('/step1', [\App\Http\Controllers\Business\BookingController::class, 'processStep1'])->name('process-step1');
            Route::get('/step2', [\App\Http\Controllers\Business\BookingController::class, 'createStep2'])->name('step2');
            Route::post('/step2', [\App\Http\Controllers\Business\BookingController::class, 'processStep2'])->name('process-step2');
            Route::get('/step3', [\App\Http\Controllers\Business\BookingController::class, 'createStep3'])->name('step3');
            Route::post('/step3', [\App\Http\Controllers\Business\BookingController::class, 'processStep3'])->name('process-step3');
            Route::get('/step4', [\App\Http\Controllers\Business\BookingController::class, 'createStep4'])->name('step4');
            Route::post('/step4', [\App\Http\Controllers\Business\BookingController::class, 'processStep4'])->name('process-step4');
            Route::get('/step5', [\App\Http\Controllers\Business\BookingController::class, 'createStep5'])->name('step5');
            Route::post('/step5', [\App\Http\Controllers\Business\BookingController::class, 'processStep5'])->name('process-step5');
        });
        
        // Reports Routes
        Route::get('/reports', [\App\Http\Controllers\Business\ReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/customer', [\App\Http\Controllers\Business\ReportsController::class, 'customerReport'])->name('reports.customer');
        Route::get('/reports/vehicle', [\App\Http\Controllers\Business\ReportsController::class, 'vehicleReport'])->name('reports.vehicle');
        Route::get('/reports/vendor', [\App\Http\Controllers\Business\ReportsController::class, 'vendorReport'])->name('reports.vendor');
        Route::get('/reports/booking', [\App\Http\Controllers\Business\ReportsController::class, 'bookingReport'])->name('reports.booking');
        
        // API Routes for Single-Page Booking Flow
        Route::get('/api/vehicles/available', [\App\Http\Controllers\Business\BookingController::class, 'getAvailableVehicles'])->name('api.vehicles.available');
        Route::get('/api/customers/search', [\App\Http\Controllers\Business\BookingController::class, 'searchCustomers'])->name('api.customers.search');
    });
});

// Test route
Route::get('/test', function () {
    return 'Hello World - Laravel 12 is working!';
});

// Simple test route
Route::get('/test-simple-route', function () {
    return response()->json([
        'success' => true,
        'message' => 'Simple route is working',
        'time' => now(),
        'laravel_version' => app()->version()
    ]);
});

// Direct test for checkConfig method
Route::get('/test-check-config', function () {
    try {
        $controller = new \App\Http\Controllers\TestOTPController();
        return $controller->checkConfig();
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Exception: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    } catch (Error $e) {
        return response()->json([
            'success' => false,
            'message' => 'PHP Error: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Debug route for TestOTPController
Route::get('/test-otp-debug', function () {
    try {
        $controller = new \App\Http\Controllers\TestOTPController();
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('getEmailConfig');
        $method->setAccessible(true);
        $config = $method->invoke($controller);
        
        return response()->json([
            'success' => true,
            'message' => 'TestOTPController is working',
            'config' => $config
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Laravel Breeze routes (for user authentication)
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';