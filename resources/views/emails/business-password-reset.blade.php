<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - The NexZen</title>
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
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #6B6ADE;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            background-color: #5a5ac8;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">The NexZen</div>
            <h2>Reset Your Password</h2>
        </div>

        <p>Hello {{ $businessAdmin->name }},</p>

        <p>We received a request to reset your password for your NexZen business account.</p>

        <p>Click the button below to reset your password:</p>

        <div style="text-align: center;">
            <a href="{{ url('/business/password/reset/' . $token) }}" class="button">Reset Password</a>
        </div>

        <p>Or copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #6B6ADE;">{{ url('/business/password/reset/' . $token) }}</p>

        <div class="warning">
            <strong>⚠️ Important Security Notice:</strong><br>
            This password reset link will expire in 60 minutes.<br>
            If you didn't request a password reset, please ignore this email or contact support immediately.
        </div>

        <p>If you didn't request a password reset, please contact our support team or simply ignore this email.</p>

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

