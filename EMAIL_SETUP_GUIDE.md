# 📧 Email Setup Guide for NexZen

## Quick Setup

### 1. Create .env file
Copy the configuration from `env-production-template.txt` and create a `.env` file in your project root.

### 2. Configure Email Settings
Update these values in your `.env` file:

```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="NexZen"
```

### 3. Gmail Setup (Recommended)
1. Enable 2-Factor Authentication on your Gmail account
2. Generate an App Password:
   - Go to Google Account settings
   - Security → 2-Step Verification → App passwords
   - Generate password for "Mail"
   - Use this password in `MAIL_PASSWORD`

### 4. Test Email Configuration
Visit: `http://your-domain.com/test-otp`

## Test OTP Page Features

### 🔧 Email Configuration Testing
- **Basic Email Test**: Tests Laravel's default mail configuration
- **OTP Email Test**: Tests the BusinessRegistrationOTP mail class
- **Custom SMTP Test**: Tests with custom SMTP settings
- **Configuration Check**: Shows current email configuration

### 📋 What to Test
1. **Check Configuration**: Click "Check Config" to see current settings
2. **Test Basic Email**: Send a simple text email
3. **Test OTP Email**: Send the actual OTP email template
4. **Test Custom SMTP**: Test with different SMTP providers

### 🚨 Common Issues & Solutions

#### Issue: "Connection could not be established"
**Solution**: Check SMTP credentials and firewall settings

#### Issue: "Authentication failed"
**Solution**: 
- For Gmail: Use App Password instead of regular password
- Check if 2FA is enabled
- Verify username is correct

#### Issue: "SSL/TLS connection failed"
**Solution**: Try different encryption settings (tls/ssl/none)

#### Issue: "SMTP server not responding"
**Solution**: Check if port is correct and server is accessible

### 🔄 Alternative SMTP Providers

#### Outlook/Hotmail
```env
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

#### Yahoo
```env
MAIL_HOST=smtp.mail.yahoo.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

#### Custom SMTP
```env
MAIL_HOST=your_smtp_host
MAIL_PORT=your_smtp_port
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

### 📝 Testing Steps

1. **Start with Configuration Check**
   - Visit `/test-otp`
   - Click "Check Config"
   - Verify all settings are correct

2. **Test Basic Email**
   - Enter your email address
   - Click "Test Basic Email"
   - Check your inbox

3. **Test OTP Email**
   - Enter your email address
   - Click "Test OTP Email"
   - Check for the formatted OTP email

4. **Test Custom SMTP** (if needed)
   - Configure custom SMTP settings
   - Click "Test Custom SMTP"
   - Verify email delivery

### 🐛 Debugging

Check Laravel logs: `storage/logs/laravel.log`

Common log entries to look for:
- `OTP sent successfully to...` - Success
- `Failed to send OTP email...` - Error with details
- `SMTP connection failed...` - Connection issues

### ✅ Success Indicators

- No errors in Laravel logs
- Emails appear in inbox
- Test page shows "success" messages
- OTP emails have proper formatting

## Security Notes

- Never commit `.env` file to version control
- Use App Passwords for Gmail
- Consider using environment-specific configurations
- Regularly rotate email credentials

## Production Deployment

1. Set `APP_ENV=production`
2. Set `APP_DEBUG=false`
3. Use production SMTP credentials
4. Remove test routes in production
5. Monitor email delivery rates
