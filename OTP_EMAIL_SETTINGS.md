# OTP Email Settings - Business Registration

## Current Email Configuration

The OTP system uses the following SMTP settings for business registration:

### Email Settings (Hardcoded in Code)
```php
Location: app/Http/Controllers/Business/AuthController.php (Lines 24-38)
```

**Configuration:**
- **SMTP Host:** mail.zenvueservices.com
- **Port:** 587
- **Encryption:** TLS
- **Username:** vinay@zenvueservices.com
- **Password:** Zenvue@2025
- **From Address:** info@nexzen.com
- **From Name:** NexZen

## How OTP Works

### 1. Registration Flow
1. User fills registration form
2. System generates 6-digit OTP (random_int)
3. OTP stored in cache for 10 minutes with session ID
4. Email sent via `BusinessRegistrationOTP` Mailable
5. User enters OTP to verify
6. Business and admin accounts created

### 2. Email Template
- **File:** `resources/views/emails/business-registration-otp.blade.php`
- **Subject:** "Business Registration OTP - The NexZen"
- **From:** info@nexzen.com
- **Valid For:** 10 minutes

### 3. OTP Sending Methods
The system has TWO locations with email configurations:

**Location 1:** `app/Http/Controllers/Business/AuthController.php`
```php
// Currently ACTIVE - Used for Business Registration
'custom' => [
    'host' => 'mail.zenvueservices.com',
    'port' => 587,
    'encryption' => 'tls',
    'username' => 'vinay@zenvueservices.com',
    'password' => 'Zenvue@2025',
    'from_address' => 'info@nexzen.com',
    'from_name' => 'NexZen',
]
```

**Location 2:** `app/Helpers/EmailConfig.php`
```php
// Currently NOT used for registration
// Has placeholder values for Gmail, Outlook, Yahoo, Custom
'active' => 'gmail' // Has dummy values
```

## Key Methods

### sendOTP()
- **Location:** `AuthController@sendOTP` (Line 142)
- **Generates:** 6-digit OTP (100000-999999)
- **Stores:** In cache with session ID for 10 minutes
- **Sends:** Via `Mail::to()->send()`
- **Logs:** Success/Error in Laravel logs

### verifyOTP()
- **Location:** `AuthController@verifyOTP` (Line 223)
- **Validates:** OTP from cache
- **Creates:** Business & BusinessAdmin records
- **Returns:** JSON response

### resendOTP()
- **Location:** `AuthController@resendOTP` (Line 351)
- **Regenerates:** New OTP
- **Resends:** Email with new code

## Testing OTP

### To Test if OTP is Working:
1. Visit: `/business/register`
2. Fill registration form
3. Check console/logs for:
   - "OTP sent successfully to {email}"
   - Or "Failed to send OTP email: {error}"
4. Check email inbox for OTP

### Common Issues:
1. **OTP not received:**
   - Check spam folder
   - Verify SMTP credentials are correct
   - Check Laravel logs: `storage/logs/laravel.log`
   - Test SMTP connection manually

2. **"Failed to send OTP" error:**
   - SMTP credentials might be wrong
   - Firewall blocking port 587
   - Email server might be down

## Database Storage
- **OTP is NOT stored in database**
- **Stored in:** Laravel Cache (Redis/File based)
- **Key Format:** `otp_registration_{session_id}`
- **TTL:** 10 minutes (600 seconds)
- **Data Stored:**
  - OTP code
  - Business details (name, type, email, phone, address)
  - Admin details (name, email)
  - Created timestamp

## Security Notes
1. **OTP is 6-digit numeric** (100000-999999)
2. **Valid for 10 minutes only**
3. **Generated using:** `random_int()` (cryptographically secure)
4. **Session ID:** UUID for tracking
5. **One-time use:** OTP deleted after successful verification

## Logging
All OTP events are logged:
- Success: `storage/logs/laravel.log`
- Error: `storage/logs/laravel.log`
- Look for: "OTP sent successfully" or "Failed to send OTP email"

## Files Involved
- `app/Http/Controllers/Business/AuthController.php` - Main controller
- `app/Mail/BusinessRegistrationOTP.php` - Mailable class
- `resources/views/emails/business-registration-otp.blade.php` - Email template
- `resources/views/business/auth/register.blade.php` - Registration form

