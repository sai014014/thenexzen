<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\SuperAdmin\AuthController as SuperAdminAuthController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\BusinessController as SuperAdminBusinessController;
use App\Http\Controllers\SuperAdmin\BugController as SuperAdminBugController;
use App\Http\Controllers\Business\AuthController as BusinessAuthController;
use App\Http\Controllers\Business\DashboardController as BusinessDashboardController;

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
        
        // Subscription Package Management Routes
        Route::resource('subscription-packages', \App\Http\Controllers\SuperAdmin\SubscriptionPackageController::class);
        Route::post('/subscription-packages/{subscriptionPackage}/toggle-status', [\App\Http\Controllers\SuperAdmin\SubscriptionPackageController::class, 'toggleStatus'])->name('subscription-packages.toggle-status');
        Route::post('/subscription-packages/{subscriptionPackage}/add-business', [\App\Http\Controllers\SuperAdmin\SubscriptionPackageController::class, 'addBusiness'])->name('subscription-packages.add-business');
        Route::post('/subscription-packages/extend-trial', [\App\Http\Controllers\SuperAdmin\SubscriptionPackageController::class, 'extendTrial'])->name('subscription-packages.extend-trial');
        Route::post('/subscription-packages/remove-business', [\App\Http\Controllers\SuperAdmin\SubscriptionPackageController::class, 'removeBusiness'])->name('subscription-packages.remove-business');
        Route::post('/subscription-packages/approve-subscription', [\App\Http\Controllers\SuperAdmin\SubscriptionPackageController::class, 'approveSubscription'])->name('subscription-packages.approve-subscription');
        Route::post('/subscription-packages/reject-subscription', [\App\Http\Controllers\SuperAdmin\SubscriptionPackageController::class, 'rejectSubscription'])->name('subscription-packages.reject-subscription');
        Route::get('/businesses/search', [\App\Http\Controllers\SuperAdmin\SubscriptionPackageController::class, 'searchBusinesses'])->name('businesses.search');
        
        // Bug Tracking Routes
        Route::patch('/bugs/{bug}/update-status', [SuperAdminBugController::class, 'updateStatus'])->name('bugs.update-status');
        Route::delete('/bug-attachments/{attachment}', [SuperAdminBugController::class, 'deleteAttachment'])->name('bug-attachments.destroy');
        Route::resource('bugs', SuperAdminBugController::class);
        
        // Notification Management Routes
        Route::resource('notifications', \App\Http\Controllers\SuperAdmin\NotificationController::class);
        Route::post('/notifications/bulk-send', [\App\Http\Controllers\SuperAdmin\NotificationController::class, 'bulkSend'])->name('notifications.bulk-send');
        Route::post('/notifications/send-to-all', [\App\Http\Controllers\SuperAdmin\NotificationController::class, 'sendToAll'])->name('notifications.send-to-all');
        
        // Cache Management Routes
        Route::post('/cache/clear', [\App\Http\Controllers\SuperAdmin\CacheController::class, 'clearAllCache'])->name('cache.clear');
        Route::get('/cache/status', [\App\Http\Controllers\SuperAdmin\CacheController::class, 'getCacheStatus'])->name('cache.status');
        
        // Migration Management Routes
        Route::get('/migrations/status', [\App\Http\Controllers\SuperAdmin\MigrationController::class, 'getStatus'])->name('migrations.status');
        Route::post('/migrations/run', [\App\Http\Controllers\SuperAdmin\MigrationController::class, 'runMigrations'])->name('migrations.run');
        Route::post('/migrations/sync', [\App\Http\Controllers\SuperAdmin\MigrationController::class, 'syncMigrations'])->name('migrations.sync');
        Route::post('/migrations/rollback', [\App\Http\Controllers\SuperAdmin\MigrationController::class, 'rollbackMigrations'])->name('migrations.rollback');
        Route::post('/migrations/reset', [\App\Http\Controllers\SuperAdmin\MigrationController::class, 'resetMigrations'])->name('migrations.reset');
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
        
        // Password Reset Routes
        Route::get('/password/reset', [BusinessAuthController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('/password/email', [BusinessAuthController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('/password/reset/{token}', [BusinessAuthController::class, 'showResetForm'])->name('password.reset');
        Route::post('/password/reset', [BusinessAuthController::class, 'resetPassword'])->name('password.update');
    });

    // Vehicle API Routes for dropdowns (accessible without authentication)
    Route::get('/api/vehicle-makes', [\App\Http\Controllers\Api\VehicleMakeModelController::class, 'getMakes'])->name('api.vehicle-makes');
    Route::get('/api/vehicle-models', [\App\Http\Controllers\Api\VehicleMakeModelController::class, 'getModels'])->name('api.vehicle-models');
    Route::get('/api/vehicle-makes-with-models', [\App\Http\Controllers\Api\VehicleApiController::class, 'getMakesWithModels'])->name('api.vehicle-makes-with-models');
    

    // Subscription Management Routes (accessible without subscription check)
    Route::middleware(['business_admin', 'check.business.status', 'check.subscription.changes'])->group(function () {
        Route::post('/logout', [BusinessAuthController::class, 'logout'])->name('logout');
        Route::resource('subscription', \App\Http\Controllers\Business\BusinessSubscriptionController::class);
        Route::post('/subscription/{subscription}/cancel', [\App\Http\Controllers\Business\BusinessSubscriptionController::class, 'cancel'])->name('subscription.cancel');
        Route::post('/subscription/upgrade', [\App\Http\Controllers\Business\BusinessSubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
        Route::post('/subscription/start-trial', [\App\Http\Controllers\Business\BusinessSubscriptionController::class, 'startTrial'])->name('subscription.start-trial');
        Route::post('/subscription/{subscription}/renew', [\App\Http\Controllers\Business\BusinessSubscriptionController::class, 'renew'])->name('subscription.renew');
        Route::post('/subscription/pause', [\App\Http\Controllers\Business\BusinessSubscriptionController::class, 'pause'])->name('subscription.pause');
        Route::post('/subscription/resume', [\App\Http\Controllers\Business\BusinessSubscriptionController::class, 'resume'])->name('subscription.resume');
        Route::get('/subscription/packages/available', [\App\Http\Controllers\Business\BusinessSubscriptionController::class, 'getAvailablePackages'])->name('subscription.packages.available');
    });

    // Manage Account Routes (outside subscription middleware)
    Route::middleware(['business_admin', 'check.business.status', 'check.subscription.changes'])->group(function () {
        Route::get('/manage-account', [\App\Http\Controllers\Business\ManageAccountController::class, 'index'])->name('manage-account.index');
        Route::post('/manage-account/update-business', [\App\Http\Controllers\Business\ManageAccountController::class, 'updateBusinessDetails'])->name('manage-account.update-business');
        Route::post('/manage-account/update-logo', [\App\Http\Controllers\Business\ManageAccountController::class, 'updateLogo'])->name('manage-account.update-logo');
        Route::post('/manage-account/add-user', [\App\Http\Controllers\Business\ManageAccountController::class, 'addUser'])->name('manage-account.add-user');
        Route::put('/manage-account/update-user/{id}', [\App\Http\Controllers\Business\ManageAccountController::class, 'updateUser'])->name('manage-account.update-user');
        Route::delete('/manage-account/delete-user/{id}', [\App\Http\Controllers\Business\ManageAccountController::class, 'deleteUser'])->name('manage-account.delete-user');
        Route::post('/manage-account/change-password', [\App\Http\Controllers\Business\ManageAccountController::class, 'changePassword'])->name('manage-account.change-password');
        
        // Activity Log Routes
        Route::get('/activity-log', [\App\Http\Controllers\Business\ActivityLogController::class, 'index'])->name('activity-log.index');
        Route::get('/activity-log/{activityLog}', [\App\Http\Controllers\Business\ActivityLogController::class, 'show'])->name('activity-log.show');
    });

    // Main business routes (require active subscription)
    Route::middleware(['business_admin', 'check.business.status', 'check.subscription.changes', 'check.business.subscription'])->group(function () {
        Route::get('/dashboard', [BusinessDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/get-vehicles-data', [BusinessDashboardController::class, 'getVehiclesData'])->name('dashboard.vehicles-data');
        
        // Vehicle Management Routes
        Route::resource('vehicles', \App\Http\Controllers\Business\VehicleController::class);
        Route::post('/vehicles/{vehicle}/toggle-availability', [\App\Http\Controllers\Business\VehicleController::class, 'toggleAvailability'])->name('vehicles.toggle-availability');
        Route::get('/vehicles/{vehicle}/download/{type}', [\App\Http\Controllers\Business\VehicleController::class, 'downloadDocument'])->name('vehicles.download-document');
        Route::get('/vehicles/{vehicle}/view/{type}', [\App\Http\Controllers\Business\VehicleController::class, 'viewDocument'])->name('vehicles.view-document');
        
        // Vehicle Creation Routes with Capacity Check
        Route::middleware('check.vehicle.capacity')->group(function () {
            Route::get('/vehicles/create', [\App\Http\Controllers\Business\VehicleController::class, 'create'])->name('vehicles.create');
            Route::post('/vehicles', [\App\Http\Controllers\Business\VehicleController::class, 'store'])->name('vehicles.store');
        });
        
        // Vehicle Image Management Routes
        Route::delete('/vehicles/{vehicleId}/images/{imageId}', [\App\Http\Controllers\Business\VehicleController::class, 'deleteImage'])->name('vehicles.images.delete');
        Route::post('/vehicles/{vehicleId}/images/{imageId}/set-primary', [\App\Http\Controllers\Business\VehicleController::class, 'setPrimaryImage'])->name('vehicles.images.set-primary');
        
        // Vehicle Upload Routes (for live uploads)
        Route::post('/vehicles/upload-image', [\App\Http\Controllers\Business\VehicleController::class, 'uploadVehicleImage'])->name('vehicles.upload-image');
        Route::post('/vehicles/upload-document', [\App\Http\Controllers\Business\VehicleController::class, 'uploadDocumentAjax'])->name('vehicles.upload-document');
        
        // Notifications Management Routes
        Route::resource('notifications', \App\Http\Controllers\Business\NotificationsController::class);
        Route::post('/notifications/{notification}/snooze', [\App\Http\Controllers\Business\NotificationsController::class, 'snooze'])->name('notifications.snooze');
        Route::post('/notifications/{notification}/complete', [\App\Http\Controllers\Business\NotificationsController::class, 'markCompleted'])->name('notifications.complete');
        Route::delete('/notifications/{notification}', [\App\Http\Controllers\Business\NotificationsController::class, 'delete'])->name('notifications.delete');
        Route::get('/notifications/count', [\App\Http\Controllers\Business\NotificationsController::class, 'getNotificationCount'])->name('notifications.count');
        Route::get('notificationManagement/get-dashboardNotifications', [\App\Http\Controllers\Business\NotificationsController::class, 'getDashboardNotifications'])->name('notifications.dashboard');
        
        // Customer Management Routes
        Route::resource('customers', \App\Http\Controllers\Business\CustomerController::class);
        Route::patch('/customers/{customer}/status', [\App\Http\Controllers\Business\CustomerController::class, 'updateStatus'])->name('customers.update-status');
        Route::post('/customers/{customer}/status', [\App\Http\Controllers\Business\CustomerController::class, 'updateStatus'])->name('customers.update-status-post');
        Route::get('/customers/{customer}/download/{type}', [\App\Http\Controllers\Business\CustomerController::class, 'downloadDocument'])->name('customers.download-document');
        Route::get('/customers/{customer}/drivers/{driver}/download/{type}', [\App\Http\Controllers\Business\CustomerController::class, 'downloadDriverDocument'])->name('customers.download-driver-document');
        Route::post('/customers/quick-create', [\App\Http\Controllers\Business\CustomerController::class, 'quickStore'])->name('customers.quick-create');
        
        // Vendor Management Routes
        Route::get('/vendors/search', [\App\Http\Controllers\Business\VendorController::class, 'search'])->name('vendors.search');
        // Quick add vendor (minimal fields) - JSON endpoint
        Route::post('/vendors/quick-add', [\App\Http\Controllers\Business\VendorController::class, 'quickStore'])->name('vendors.quick-store');
        Route::resource('vendors', \App\Http\Controllers\Business\VendorController::class);
        Route::get('/vendors/{vendor}/download/{type}', [\App\Http\Controllers\Business\VendorController::class, 'downloadDocument'])->name('vendors.download-document');
        
        // Booking Management Routes
        // New Single-page Booking Flow (must be BEFORE resource route to avoid conflicts)
        Route::get('/bookings/flow', [\App\Http\Controllers\Business\BookingController::class, 'createFlow'])->name('bookings.flow.create');
        Route::post('/bookings/flow/save-step', [\App\Http\Controllers\Business\BookingController::class, 'saveFlowStep'])->name('bookings.flow.save_step');
        Route::post('/bookings/flow/clear-draft', [\App\Http\Controllers\Business\BookingController::class, 'clearFlowDraft'])->name('bookings.flow.clear_draft');
        Route::get('/bookings/flow/vehicles/list', [\App\Http\Controllers\Business\BookingController::class, 'listFlowVehicles'])->name('bookings.flow.vehicles');
        Route::post('/bookings/flow/billing/summary', [\App\Http\Controllers\Business\BookingController::class, 'billingSummary'])->name('bookings.flow.billing_summary');
        Route::post('/bookings/flow/store', [\App\Http\Controllers\Business\BookingController::class, 'storeFromFlow'])->name('bookings.flow.store');
        Route::get('/bookings/flow/vehicle/{vehicleId}/billing', [\App\Http\Controllers\Business\BookingController::class, 'getVehicleForBilling'])->name('bookings.flow.vehicle.billing');
        
        Route::get('/bookings/quick-create', [\App\Http\Controllers\Business\BookingController::class, 'quickCreate'])->name('bookings.quick-create');
        Route::resource('bookings', \App\Http\Controllers\Business\BookingController::class);
        Route::post('/bookings/{booking}/start', [\App\Http\Controllers\Business\BookingController::class, 'start'])->name('bookings.start');
        Route::post('/bookings/{booking}/complete', [\App\Http\Controllers\Business\BookingController::class, 'complete'])->name('bookings.complete');
        Route::post('/bookings/{booking}/cancel', [\App\Http\Controllers\Business\BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::get('/bookings/available-vehicles', [\App\Http\Controllers\Business\BookingController::class, 'getAvailableVehicles'])->name('bookings.available-vehicles');
        Route::post('/bookings/calculate-pricing', [\App\Http\Controllers\Business\BookingController::class, 'calculatePricing'])->name('bookings.calculate-pricing');
        
        // API Routes for Single-Page Booking Flow
        Route::get('/api/vehicles/available', [\App\Http\Controllers\Business\BookingController::class, 'getAvailableVehicles'])->name('api.vehicles.available');
        Route::get('/api/customers/search', [\App\Http\Controllers\Business\BookingController::class, 'searchCustomers'])->name('api.customers.search');
        
        // 5-Stage Booking Flow Routes (old multi-page flow - kept for backward compatibility)
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
    });
});

// Laravel Breeze routes (for user authentication)
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Current system time route
Route::get('/current-time', function () {
    $now = \Carbon\Carbon::now('Asia/Kolkata');
    
    return response()->json([
        'current_system_time' => $now->format('Y-m-d H:i:s'),
        'timezone' => $now->timezone->getName(),
        'day_name' => $now->format('l'),
        'date_formatted' => $now->format('d M Y'),
        'time_formatted' => $now->format('h:i:s A'),
        'timestamp' => $now->timestamp,
        'iso_format' => $now->toISOString(),
        'app_timezone' => config('app.timezone'),
        'php_timezone' => date_default_timezone_get()
    ]);
});

require __DIR__.'/auth.php';