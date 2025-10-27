# cPanel Security Implementation Guide - Nexzen Application

## Quick Setup Checklist

Your application is now using **database sessions** which is production-ready. Follow these steps to implement security in cPanel:

## ✅ STEP 1: Configure Environment Variables

### In cPanel File Manager:

1. **Navigate to**: `public_html/nexzen/` (or your app directory)
2. **Edit**: `.env` file
3. **Add/Update these lines**:

```env
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Session Configuration (Already Configured ✓)
SESSION_DRIVER=database
SESSION_LIFETIME=480
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
SESSION_PREFIX=nexzen_session_

# Database (Your existing credentials)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourmailserver.com
MAIL_PORT=587
MAIL_USERNAME=your_email@domain.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Nexzen"
```

## 🔒 STEP 2: Force HTTPS (SSL/HTTPS)

### In cPanel SSL/TLS Settings:

1. **Go to**: cPanel → SSL/TLS
2. **Install an SSL Certificate** (or use Let's Encrypt - free)
3. **Force HTTPS**:
   - Add to `.htaccess` in `public_html/nexzen/public/`:

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## 🛡️ STEP 3: Secure File Permissions

### In cPanel File Manager:

Set permissions:
```bash
Folders: 755
Files: 644
.env: 600 (restrictive)
storage/: 775
bootstrap/cache/: 775
```

### Or use Terminal in cPanel:

```bash
cd ~/public_html/nexzen
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod 600 .env
chmod -R 775 storage bootstrap/cache
```

## 🔐 STEP 4: Implement Security Headers

### Add to `public_html/nexzen/public/.htaccess`:

```apache
# Security Headers
<IfModule mod_headers.c>
    Header set X-Frame-Options "DENY"
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
    Header set Permissions-Policy "geolocation=(), microphone=(), camera=()"
    Header set Strict-Transport-Security "max-age=31536000; includeSubDomains"
</IfModule>

# Disable Directory Listing
Options -Indexes

# Block Sensitive Files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Protect .env file
<Files ".env">
    Order allow,deny
    Deny from all
</Files>
```

## 🚫 STEP 5: Block Unwanted Access

### In cPanel IP Blocker:

1. **Go to**: cPanel → IP Blocker
2. **Add IP addresses** to block if you see suspicious activity

### In cPanel.htaccess Editor:

Add to `public_html/nexzen/public/.htaccess`:

```apache
# Block Bad Bots
RewriteCond %{HTTP_USER_AGENT} (bot|crawler|spider|scraper) [NC]
RewriteCond %{HTTP_USER_AGENT} !(googlebot|bingbot) [NC]
RewriteRule .* - [F,L]

# Block SQL Injection Attempts
RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2})
RewriteRule .* - [F,L]

# Block File Injection Attempts
RewriteCond %{QUERY_STRING} (\.\./|etc/passwd|boot\.ini) [NC]
RewriteRule .* - [F,L]
```

## 🔍 STEP 6: Update Application Configuration

### Run these commands in Terminal (cPanel):

```bash
# Navigate to your application
cd ~/public_html/nexzen

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Generate application key if not set
php artisan key:generate --force

# Link storage (if needed)
php artisan storage:link

# Run migrations
php artisan migrate --force
```

## 📊 STEP 7: Monitor Security

### Check Application Logs:

```bash
tail -f storage/logs/laravel.log
```

### Common Issues to Watch:

1. **500 Errors**: Check permissions and .env configuration
2. **Database Errors**: Verify DB credentials in .env
3. **CSRF Errors**: Check HTTPS is enabled
4. **Session Issues**: Verify session table exists

## 🔥 STEP 8: Rate Limiting (Optional but Recommended)

### In routes/web.php, add to sensitive endpoints:

```php
// Login route with rate limiting
Route::post('/business/login', [AuthController::class, 'login'])
    ->middleware(['throttle:5,1']); // 5 attempts per minute
```

## ✅ STEP 9: Final Verification

### Test Your Security:

1. **HTTPS**: Visit https://yourdomain.com - should work
2. **HTTP Redirect**: Visit http://yourdomain.com - should redirect to HTTPS
3. **Session**: Login and check session works
4. **Cookies**: Check browser dev tools - cookies should have:
   - `Secure` flag
   - `HttpOnly` flag
   - `SameSite=Strict`

### Use Security Headers Check:

Visit: https://securityheaders.com/?q=https://yourdomain.com

## 📝 STEP 10: Backup Configuration

### Before Going Live:

1. **Backup Database**: cPanel → phpMyAdmin → Export
2. **Backup Files**: cPanel → File Manager → Compress entire application
3. **Store backups**: Download to local machine

## 🎯 Priority Actions (Do First!)

1. ✅ **Update .env file** with production values
2. ✅ **Enable HTTPS** (SSL certificate)
3. ✅ **Force HTTPS** in .htaccess
4. ✅ **Set file permissions** correctly
5. ✅ **Add security headers** in .htaccess
6. ✅ **Run Laravel optimization** commands
7. ✅ **Test the application** thoroughly

## Current Security Status

### ✅ Already Configured:
- Database sessions (secure)
- Session encryption enabled
- HTTPS-only cookies
- HTTP-only cookies
- SameSite Strict protection
- 8-hour session lifetime
- CSRF protection enabled

### 🔧 Need to Configure in cPanel:
- SSL certificate
- .htaccess security rules
- File permissions
- Environment variables
- Security headers

## Support

If you encounter issues:
1. Check `storage/logs/laravel.log` for errors
2. Verify `.env` configuration
3. Test database connection: `php artisan tinker` then `DB::connection()->getPdo();`
4. Check file permissions
5. Verify SSL certificate is installed

## 🎉 After Implementation

Your application will have:
- ✅ Industry-standard security
- ✅ HTTPS encryption
- ✅ Secure session management
- ✅ Protection against common attacks
- ✅ Production-ready configuration

