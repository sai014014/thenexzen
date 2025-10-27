# Redis Security Checklist for Production

## ✅ Current Security Measures Implemented

### 1. Application Level Security
- ✅ Session encryption enabled (`encrypt => true`)
- ✅ HTTPS-only cookies (`secure => true`)
- ✅ HTTP-only cookies (`http_only => true`)
- ✅ Same-Site Strict protection (`same_site => 'strict'`)
- ✅ JSON serialization for sessions (more secure than PHP serialization)
- ✅ Session prefix to prevent key collisions
- ✅ Long session lifetime (8 hours) with auto-expiration

### 2. Redis Configuration
- ✅ Dedicated session database (DB 2)
- ✅ Connection timeout (30s)
- ✅ Read timeout (60s)
- ✅ Persistent connections disabled
- ✅ Password authentication support

## 🔒 Critical Security Steps - ACTION REQUIRED

### 1. Redis Server Configuration
Edit `/etc/redis/redis.conf` and set:

```bash
# CRITICAL: Set strong password
requirepass YourStrongPassword123!@#

# CRITICAL: Bind to localhost only
bind 127.0.0.1

# CRITICAL: Enable protected mode
protected-mode yes

# CRITICAL: Disable dangerous commands
rename-command FLUSHDB ""
rename-command FLUSHALL ""
rename-command CONFIG ""
rename-command EVAL ""
rename-command DEBUG ""

# RECOMMENDED: Set max memory and policy
maxmemory 256mb
maxmemory-policy allkeys-lru

# RECOMMENDED: Enable AOF persistence
appendonly yes
appendfsync everysec
```

### 2. Update Environment Variables
In your `.env` file, add:

```env
# Session Configuration
SESSION_DRIVER=redis
SESSION_LIFETIME=480
SESSION_STORE=redis
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
SESSION_PREFIX=nexzen_session_

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=YourStrongPassword123!@#
REDIS_SESSION_DB=2

# Security Headers
APP_ENV=production
APP_DEBUG=false
```

### 3. Firewall Configuration
Block Redis from internet access:

```bash
# UFW (Ubuntu)
sudo ufw deny 6379
sudo ufw allow 127.0.0.1:6379

# Firewalld (CentOS/RHEL)
sudo firewall-cmd --permanent --add-rich-rule='rule family="ipv4" source address="127.0.0.1" port protocol="tcp" port="6379" accept'
sudo firewall-cmd --permanent --add-rich-rule='rule family="ipv4" port protocol="tcp" port="6379" reject'
sudo firewall-cmd --reload

# iptables
sudo iptables -A INPUT -p tcp -s 127.0.0.1 --dport 6379 -j ACCEPT
sudo iptables -A INPUT -p tcp --dport 6379 -j REJECT
```

### 4. Redis Service Security
```bash
# Run Redis with limited privileges
sudo useradd -r -s /bin/false redisuser
sudo chown -R redisuser:redisuser /var/lib/redis

# Limit file permissions
sudo chmod 750 /etc/redis
sudo chmod 640 /etc/redis/redis.conf
```

### 5. Enable TLS for Redis (Recommended)
If Redis must be accessible externally:

```bash
# Install Redis with TLS support
cd /etc/redis
openssl req -x509 -newkey rsa:4096 -keyout key.pem -out cert.pem -days 365 -nodes

# Edit redis.conf
tls-port 6380
tls-cert-file cert.pem
tls-key-file key.pem
tls-ca-cert-file ca.pem
```

Update Laravel config:
```env
REDIS_PORT=6380
REDIS_URL=rediss://127.0.0.1:6380
```

## 🛡️ Additional Security Recommendations

### 1. Application Security Headers
Update `app/Http/Middleware/VerifyCsrfToken.php`:
```php
public function handle($request, Closure $next)
{
    $response = $next($request);
    
    $response->headers->set('X-Frame-Options', 'DENY');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
    
    return $response;
}
```

### 2. Session Fixation Protection
Already enabled by default in Laravel.

### 3. Session Regeneration
Add to login/logout:
```php
// In AuthController
Auth::login($user);
request()->session()->regenerate(); // Prevent fixation

// In logout
Auth::logout();
request()->session()->invalidate();
request()->session()->regenerateToken();
```

### 4. Rate Limiting
Protect login endpoints:
```php
// In routes/web.php
Route::post('/business/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute
```

### 5. Monitor Redis
```bash
# Check active connections
redis-cli CLIENT LIST

# Monitor commands in real-time
redis-cli MONITOR

# Check memory usage
redis-cli INFO memory

# Check slow queries
redis-cli SLOWLOG GET 10
```

### 6. Regular Security Audits
```bash
# Check Redis configuration
redis-cli CONFIG GET requirepass

# Verify protected mode
redis-cli CONFIG GET protected-mode

# Check for weak settings
redis-cli CONFIG GET "*" | grep -E "save|dir|logfile"
```

## ⚠️ Security Concerns Addressed

### 1. ✅ Session Hijacking
- **Protection**: HTTPS-only cookies, SameSite Strict, HTTP-only cookies
- **Mitigation**: Session encryption, secure session IDs

### 2. ✅ Session Fixation
- **Protection**: Session regeneration on login
- **Mitigation**: Unique session IDs per authentication

### 3. ✅ Session Replay
- **Protection**: Session timeout (8 hours)
- **Mitigation**: Auto-expiration of old sessions

### 4. ✅ CSRF Attacks
- **Protection**: SameSite cookies, CSRF tokens
- **Mitigation**: Laravel's built-in CSRF protection

### 5. ✅ XSS Attacks
- **Protection**: HTTP-only cookies
- **Mitigation**: Content Security Policy (add to response headers)

### 6. ✅ Redis Injection
- **Protection**: Laravel's Redis wrapper, proper escaping
- **Mitigation**: Use Laravel's Redis methods, not raw commands

### 7. ✅ Unauthorized Redis Access
- **Protection**: Password authentication, firewall rules
- **Mitigation**: Bind to localhost, disable dangerous commands

## 📊 Security Score

### Current Status: 🟡 MEDIUM-HIGH SECURITY
- ✅ Application layer: Highly secure
- 🟡 Redis layer: Requires configuration
- 🟡 Infrastructure: Requires hardening

### After Hardening: 🟢 HIGH SECURITY
- ✅ All layers properly secured
- ✅ Industry best practices applied
- ✅ Production-ready configuration

## 🚀 Deployment Checklist

Before going live, ensure:
- [ ] Redis password is set and complex
- [ ] Redis is bound to localhost only
- [ ] Firewall blocks external access to port 6379
- [ ] SESSION_SECURE_COOKIE=true in .env
- [ ] APP_DEBUG=false in .env
- [ ] HTTPS is properly configured
- [ ] Session encryption is enabled
- [ ] Rate limiting is active on auth endpoints
- [ ] Monitoring is set up
- [ ] Backups are configured

## 📞 Support

If Redis connection fails, check:
1. Redis service is running: `sudo systemctl status redis`
2. Password is correct in `.env`
3. Firewall is not blocking connections
4. Laravel logs: `storage/logs/laravel.log`

