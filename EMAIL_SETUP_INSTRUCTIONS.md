# 📧 Email Setup Instructions - Direct Code Configuration

## 🎯 **Where to Update Email Settings**

### **File: `app/Helpers/EmailConfig.php`**

Open this file and update the email configuration directly in the code:

```php
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

// Change this to select which email provider to use
'active' => 'gmail', // ← Options: 'gmail', 'outlook', 'yahoo', 'custom'
```

## 🔧 **Step-by-Step Setup**

### **1. For Gmail (Recommended)**
1. Open `app/Helpers/EmailConfig.php`
2. Find the `'gmail'` section
3. Replace `'your_email@gmail.com'` with your actual Gmail address
4. Replace `'your_app_password'` with your Gmail App Password
5. Make sure `'active' => 'gmail'` is set

### **2. For Outlook/Hotmail**
1. Open `app/Helpers/EmailConfig.php`
2. Find the `'outlook'` section
3. Update the credentials
4. Change `'active' => 'outlook'`

### **3. For Yahoo**
1. Open `app/Helpers/EmailConfig.php`
2. Find the `'yahoo'` section
3. Update the credentials
4. Change `'active' => 'yahoo'`

### **4. For Custom SMTP**
1. Open `app/Helpers/EmailConfig.php`
2. Find the `'custom'` section
3. Update with your SMTP details
4. Change `'active' => 'custom'`

## 📱 **Gmail App Password Setup**

1. **Enable 2-Factor Authentication** on your Gmail account
2. **Generate App Password:**
   - Go to [Google Account Settings](https://myaccount.google.com/)
   - Security → 2-Step Verification → App passwords
   - Select "Mail" and generate password
   - Copy the 16-character password

## 🧪 **Test Your Configuration**

1. **Visit:** `http://localhost/nexzen/test-otp`
2. **Click "Check Config"** to see your current settings
3. **Test Basic Email** to verify it works
4. **Test OTP Email** to test the actual OTP functionality

## 🔄 **Switch Email Providers**

To switch between email providers, just change the `'active'` value:

```php
'active' => 'gmail',    // Use Gmail
'active' => 'outlook',  // Use Outlook
'active' => 'yahoo',    // Use Yahoo
'active' => 'custom',   // Use Custom SMTP
```

## ✅ **Success Indicators**

- Test page shows "Configuration retrieved successfully"
- "Check Config" shows your actual email address (not placeholder)
- "Test Basic Email" sends successfully
- "Test OTP Email" sends the formatted OTP email

## 🚨 **Common Issues**

### **"Authentication failed"**
- For Gmail: Use App Password, not regular password
- Check if 2FA is enabled
- Verify username is correct

### **"Connection could not be established"**
- Check SMTP host and port
- Verify firewall settings
- Try different encryption (tls/ssl)

### **"SMTP server not responding"**
- Check if port is correct
- Verify server is accessible
- Try different SMTP provider

## 📝 **Quick Test Checklist**

- [ ] Updated `app/Helpers/EmailConfig.php` with your credentials
- [ ] Set correct `'active'` provider
- [ ] Visited `/test-otp` page
- [ ] Clicked "Check Config" - shows your email
- [ ] Tested "Basic Email" - received email
- [ ] Tested "OTP Email" - received formatted OTP email

Once all tests pass, your business registration OTP will work perfectly!
