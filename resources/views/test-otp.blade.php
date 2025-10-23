<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test OTP Email - TheNexZen</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('dist/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .test-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #6B6ADE 0%, #5a5ac7 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 40px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: #6B6ADE;
            box-shadow: 0 0 0 3px rgba(107, 106, 222, 0.1);
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 5px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6B6ADE 0%, #5a5ac7 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(107, 106, 222, 0.4);
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: white;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid;
        }
        .alert-success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .alert-danger {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .alert-info {
            background: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }
        .config-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .config-section h4 {
            color: #6B6ADE;
            margin-bottom: 15px;
        }
        .test-results {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            max-height: 300px;
            overflow-y: auto;
        }
        .log-entry {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
        .log-entry:last-child {
            border-bottom: none;
        }
        .log-success { color: #28a745; }
        .log-error { color: #dc3545; }
        .log-info { color: #17a2b8; }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="header">
            <h1>🔧 OTP Email Testing Tool</h1>
            <p>Test email configuration and OTP sending functionality</p>
        </div>
        
        <div class="content">
            <!-- Email Configuration Section -->
            <div class="config-section">
                <h4>📧 Email Configuration</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>SMTP Host</label>
                            <input type="text" class="form-control" id="smtp_host" value="smtp.gmail.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>SMTP Port</label>
                            <input type="number" class="form-control" id="smtp_port" value="587">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>SMTP Username</label>
                            <input type="email" class="form-control" id="smtp_username" placeholder="your-email@gmail.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>SMTP Password</label>
                            <input type="password" class="form-control" id="smtp_password" placeholder="App Password">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Encryption</label>
                            <select class="form-control" id="smtp_encryption">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                                <option value="">None</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>From Name</label>
                            <input type="text" class="form-control" id="from_name" value="TheNexZen">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Email Section -->
            <div class="config-section">
                <h4>📨 Test Email Details</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Recipient Email</label>
                            <input type="email" class="form-control" id="recipient_email" placeholder="test@example.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Test OTP Code</label>
                            <input type="text" class="form-control" id="test_otp" value="123456" maxlength="6">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="text-align: center; margin: 30px 0;">
                <button class="btn btn-primary" onclick="testBasicEmail()">📧 Test Basic Email</button>
                <button class="btn btn-success" onclick="testOTPEmail()">🔐 Test OTP Email</button>
                <button class="btn btn-warning" onclick="testCustomSMTP()">⚙️ Test Custom SMTP</button>
                <button class="btn btn-primary" onclick="checkConfiguration()">🔍 Check Config</button>
            </div>

            <!-- Results Section -->
            <div id="results"></div>
            
            <!-- Test Results Log -->
            <div class="test-results" id="testLog" style="display: none;">
                <h5>📋 Test Log</h5>
                <div id="logContent"></div>
            </div>
        </div>
    </div>

    <script src="{{ asset('dist/jquery.min.js') }}"></script>
    <script>
        function addLog(message, type = 'info') {
            const logContent = document.getElementById('logContent');
            const logEntry = document.createElement('div');
            logEntry.className = `log-entry log-${type}`;
            logEntry.innerHTML = `[${new Date().toLocaleTimeString()}] ${message}`;
            logContent.appendChild(logEntry);
            logContent.scrollTop = logContent.scrollHeight;
        }

        function showResult(message, type = 'info') {
            const results = document.getElementById('results');
            const alertClass = type === 'success' ? 'alert-success' : 
                             type === 'error' ? 'alert-danger' : 'alert-info';
            
            results.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
            document.getElementById('testLog').style.display = 'block';
        }

        function testBasicEmail() {
            addLog('Starting basic email test...', 'info');
            
            const recipient = document.getElementById('recipient_email').value;
            if (!recipient) {
                showResult('Please enter a recipient email address.', 'error');
                return;
            }

            fetch('/test-otp/send-basic', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    email: recipient,
                    subject: 'Test Email from TheNexZen',
                    message: 'This is a test email to verify email functionality.'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addLog('Basic email sent successfully!', 'success');
                    showResult('✅ Basic email sent successfully! Check your inbox.', 'success');
                } else {
                    addLog(`Basic email failed: ${data.message}`, 'error');
                    showResult(`❌ Basic email failed: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                addLog(`Basic email error: ${error.message}`, 'error');
                showResult(`❌ Error: ${error.message}`, 'error');
            });
        }

        function testOTPEmail() {
            addLog('Starting OTP email test...', 'info');
            
            const recipient = document.getElementById('recipient_email').value;
            const otp = document.getElementById('test_otp').value;
            
            if (!recipient || !otp) {
                showResult('Please enter recipient email and OTP code.', 'error');
                return;
            }

            fetch('/test-otp/send-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    email: recipient,
                    otp: otp,
                    business_name: 'Test Business',
                    admin_name: 'Test Admin'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addLog('OTP email sent successfully!', 'success');
                    showResult('✅ OTP email sent successfully! Check your inbox.', 'success');
                } else {
                    addLog(`OTP email failed: ${data.message}`, 'error');
                    showResult(`❌ OTP email failed: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                addLog(`OTP email error: ${error.message}`, 'error');
                showResult(`❌ Error: ${error.message}`, 'error');
            });
        }

        function testCustomSMTP() {
            addLog('Starting custom SMTP test...', 'info');
            
            const smtpConfig = {
                host: document.getElementById('smtp_host').value,
                port: document.getElementById('smtp_port').value,
                username: document.getElementById('smtp_username').value,
                password: document.getElementById('smtp_password').value,
                encryption: document.getElementById('smtp_encryption').value,
                from_name: document.getElementById('from_name').value
            };

            const recipient = document.getElementById('recipient_email').value;
            if (!recipient) {
                showResult('Please enter a recipient email address.', 'error');
                return;
            }

            fetch('/test-otp/send-custom-smtp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    smtp_config: smtpConfig,
                    email: recipient,
                    subject: 'Custom SMTP Test Email',
                    message: 'This email was sent using custom SMTP configuration.'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addLog('Custom SMTP email sent successfully!', 'success');
                    showResult('✅ Custom SMTP email sent successfully! Check your inbox.', 'success');
                } else {
                    addLog(`Custom SMTP email failed: ${data.message}`, 'error');
                    showResult(`❌ Custom SMTP email failed: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                addLog(`Custom SMTP email error: ${error.message}`, 'error');
                showResult(`❌ Error: ${error.message}`, 'error');
            });
        }

        function checkConfiguration() {
            addLog('Checking email configuration...', 'info');
            
            // Try the main route first, then fallback to backup route
            fetch('/test-otp/check-config', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                addLog('Configuration check completed', 'info');
                showResult(`📋 Configuration Status:<br><pre>${JSON.stringify(data, null, 2)}</pre>`, 'info');
            })
            .catch(error => {
                addLog(`Main route failed, trying backup route: ${error.message}`, 'info');
                // Try backup route
                return fetch('/test-otp-check-config', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
            })
            .then(response => {
                if (response && response.ok) {
                    return response.json();
                }
                throw new Error('Both routes failed');
            })
            .then(data => {
                addLog('Configuration check completed via backup route', 'info');
                showResult(`📋 Configuration Status (Backup Route):<br><pre>${JSON.stringify(data, null, 2)}</pre>`, 'info');
            })
            .catch(error => {
                addLog(`Configuration check error: ${error.message}`, 'error');
                showResult(`❌ Error checking configuration: ${error.message}`, 'error');
            });
        }

        // Auto-generate random OTP
        document.getElementById('test_otp').addEventListener('click', function() {
            this.value = Math.floor(100000 + Math.random() * 900000);
        });
    </script>
</body>
</html>
