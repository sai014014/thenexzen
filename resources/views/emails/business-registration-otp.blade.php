<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Registration OTP - The NexZen</title>
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #6B6ADE;
            margin-bottom: 10px;
        }
        .otp-box {
            background-color: #f8f9fa;
            border: 2px solid #6B6ADE;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin: 25px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #6B6ADE;
            letter-spacing: 8px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #6c757d;
            text-align: center;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">The NexZen</div>
            <h2>Business Registration Verification</h2>
        </div>

        <p>Hello <strong>{{ $adminName }}</strong>,</p>

        <p>Thank you for registering your business <strong>"{{ $businessName }}"</strong> with The NexZen platform.</p>

        <p>To complete your registration, please use the following One-Time Password (OTP):</p>

        <div class="otp-box">
            <p style="margin: 0 0 15px 0; font-weight: 600; font-size: 16px;">Your Verification Code:</p>
            <div class="otp-code">{{ $otp }}</div>
            <p style="margin: 15px 0 0 0; font-size: 14px; color: #6c757d;">Valid for 10 minutes</p>
        </div>

        <div class="warning">
            <strong>⚠️ Important Security Notice:</strong><br>
            This OTP is valid for 10 minutes only. If you don't use it within this time, you'll need to request a new one. Never share this code with anyone.
        </div>

        <div class="success">
            <strong>✅ Next Steps:</strong><br>
            1. Enter this OTP in the registration form<br>
            2. Create your password<br>
            3. Complete your business profile<br>
            4. Start managing your fleet!
        </div>

        <p>If you didn't request this registration, please ignore this email or contact our support team immediately.</p>

        <div class="footer">
            <p><strong>The NexZen Team</strong><br>
            Your trusted fleet management partner</p>
            
            <p style="font-size: 12px; margin-top: 20px; color: #999;">
                This is an automated message. Please do not reply to this email.<br>
                For support, contact us at support@thenexzen.com
            </p>
        </div>
    </div>
</body>
</html>