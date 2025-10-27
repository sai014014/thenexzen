<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Business Registration - TheNexZen</title>
    <link rel="icon" href="<?php echo e(asset('homePage/assets/img/businesslogowhite.svg')); ?>" type="image/x-icon" />
    <link href="<?php echo e(asset('dist/bootstrap/bootstrap.min.css')); ?>" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Racing+Sans+One&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('dist/css/login.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('dist/css/common.css')); ?>">
    <script src="<?php echo e(asset('dist/jquery.min.js')); ?>"></script>
    
    <style>
        /* Register page specific styles */
        .register-card {
            max-height: 90vh;
            overflow-y: auto;
            padding: 20px;
        }
        
        .register-card::-webkit-scrollbar {
            width: 6px;
        }
        
        .register-card::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .register-card::-webkit-scrollbar-thumb {
            background: #6B6ADE;
            border-radius: 3px;
        }
        
        .register-card::-webkit-scrollbar-thumb:hover {
            background: #5a5ac7;
        }
        
        .form-container {
            max-height: 70vh;
            overflow-y: auto;
            padding-right: 10px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }
        
        .loginInput {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .loginInput:focus {
            outline: none;
            border-color: #6B6ADE;
            box-shadow: 0 0 0 2px rgba(107, 106, 222, 0.1);
        }
        
        .admin-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .admin-section h5 {
            color: #6B6ADE;
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 16px;
        }
        
        .welcom_text h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        
        .submit {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #6B6ADE;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .submit:hover {
            background: #5a5ac7;
        }
        
        .register {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }
        
        .register p {
            margin: 8px 0;
        }
        
        .register a {
            color: #6B6ADE;
            text-decoration: none;
            font-size: 14px;
        }
        
        .register a:hover {
            text-decoration: underline;
        }
        
        /* Success Modal Styles */
        .success-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }
        
        .success-modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .success-modal-content {
            background-color: #fff;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: #10B981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: bounceIn 0.6s ease;
        }
        
        .success-icon svg {
            width: 40px;
            height: 40px;
            color: white;
        }
        
        .success-title {
            font-size: 24px;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 10px;
        }
        
        .success-message {
            color: #6B7280;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        
        .success-button {
            background: #6B6ADE;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .success-button:hover {
            background: #5a5ac7;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideIn {
            from { 
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to { 
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes bounceIn {
            0% { transform: scale(0.3); }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); }
        }
        
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .register-card {
                max-height: 95vh;
                margin: 10px;
            }
            
            .form-container {
                max-height: 75vh;
            }
            
            .welcom_text h1 {
                font-size: 20px;
            }
            
            .success-modal-content {
                padding: 30px 20px;
                margin: 20px;
            }
            
            .success-title {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>
    <div id="loader" class="loader-container">  
        <div class="loader"></div>
    </div>
    
    <div class="login">
        <div class="row">
            <div class="col-md-6">
                <div class="logo">
                    <img src="<?php echo e(asset('images/login.png')); ?>" alt="Business Logo">
                </div>
            </div>
            <div class="col-md-6">
                <div class="fixed_position">
                    <div class="card register-card">
                        <div class="welcom_text">
                            <svg width="40" height="38" viewBox="0 0 46 44" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M23 0L23.823 3.36707C25.109 8.62855 25.752 11.2593 27.1233 13.3821C28.336 15.2593 29.9527 16.8418 31.8554 18.0139C34.0071 19.3395 36.651 19.926 41.9388 21.0991L46 22L41.9388 22.9009C36.651 24.074 34.0071 24.6605 31.8554 25.9861C29.9527 27.1582 28.336 28.7407 27.1233 30.6179C25.752 32.7407 25.109 35.3714 23.823 40.6329L23 44L22.177 40.6329C20.891 35.3714 20.248 32.7407 18.8767 30.6179C17.664 28.7407 16.0473 27.1582 14.1446 25.9861C11.9929 24.6605 9.34898 24.074 4.06116 22.9009L0 22L4.06116 21.0991C9.34897 19.926 11.9929 19.3395 14.1446 18.0139C16.0473 16.8418 17.664 15.2593 18.8767 13.3821C20.248 11.2593 20.891 8.62855 22.177 3.36707L23 0Z"
                                    fill="black" />
                            </svg>
                            <h1>Register Your Business</h1>
                        </div>
                        
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if(session('success')): ?>
                            <div class="alert alert-success">
                                <?php echo e(session('success')); ?>

                            </div>
                        <?php endif; ?>

                        <div class="form-container">
                            <!-- Step 1: Business Information Form -->
                            <form id="businessInfoForm">
                                <?php echo csrf_field(); ?>
                                
                                <!-- Business Information -->
                                <div class="form-group">
                                    <label for="business_name">Business Name *</label>
                                    <input class="loginInput" type="text" id="business_name" name="business_name" required>
                                </div>

                                <div class="form-group">
                                    <label for="business_type">Business Type *</label>
                                    <select class="loginInput" id="business_type" name="business_type" required>
                                        <option value="">Select Business Type</option>
                                        <option value="transportation">Transportation</option>
                                        <option value="logistics">Logistics</option>
                                        <option value="rental">Vehicle Rental</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="email">Business Email *</label>
                                    <input class="loginInput" type="email" id="email" name="email" required>
                                </div>

                                <div class="form-group">
                                    <label for="phone">Phone Number *</label>
                                    <input class="loginInput" type="tel" id="phone" name="phone" required>
                                </div>

                                <div class="form-group">
                                    <label for="address">Address *</label>
                                    <input class="loginInput" type="text" id="address" name="address" placeholder="Street Address, City, State" required>
                                </div>

                                <!-- Admin Information -->
                                <div class="admin-section">
                                    <h5>Admin Account</h5>
                                    
                                    <div class="form-group">
                                        <label for="admin_name">Admin Name *</label>
                                        <input class="loginInput" type="text" id="admin_name" name="admin_name" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="admin_email">Admin Email *</label>
                                        <input class="loginInput" type="email" id="admin_email" name="admin_email" required>
                                    </div>
                                </div>

                                <button class="submit loginSubmit" type="submit" id="sendOTPBtn">Send OTP</button>
                            </form>

                            <!-- Step 2: OTP Verification Form -->
                            <form id="otpVerificationForm" style="display: none;">
                                <div class="otp-info">
                                    <h5 style="color: #6B6ADE; margin-bottom: 15px; font-weight: 600;">Verify Email</h5>
                                    <p style="color: #666; margin-bottom: 20px;">
                                        We've sent a 6-digit OTP to <strong id="otpEmail"></strong>
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label for="otp">Enter OTP *</label>
                                    <input class="loginInput" type="text" id="otp" name="otp" maxlength="6" placeholder="123456" required>
                                </div>

                                <button class="submit loginSubmit" type="submit" id="verifyOTPBtn">Verify OTP</button>
                                <button type="button" class="btn btn-link" id="resendOTPBtn" style="margin-top: 10px; color: #6B6ADE;">Resend OTP</button>
                            </form>

                            <!-- Step 3: Password Creation Form -->
                            <form id="passwordForm" style="display: none;">
                                <div class="password-info">
                                    <h5 style="color: #6B6ADE; margin-bottom: 15px; font-weight: 600;">Create Password</h5>
                                    <p style="color: #666; margin-bottom: 20px;">
                                        Email verified! Now create your password to complete registration.
                                    </p>
                                </div>

                                <div class="form-group">
                                    <label for="password">Password *</label>
                                    <input class="loginInput" type="password" id="password" name="password" required>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password *</label>
                                    <input class="loginInput" type="password" id="password_confirmation" name="password_confirmation" required>
                                </div>

                                <button class="submit loginSubmit" type="submit" id="completeRegistrationBtn">Complete Registration</button>
                            </form>
                        </div>
                        
                        <div class="register" style="text-align:center ">
                            <p>
                                <a href="<?php echo e(route('business.login')); ?>">Already have an account? Login</a>
                            </p>
                            <p>
                                <a href="<?php echo e(route('super-admin.login')); ?>">Super Admin Login</a>
                            </p>
                            <p>
                                <a href="<?php echo e(url('/')); ?>">Back to Home</a>
                            </p>
                        </div>
                        <div id="errorMessage" style="color:red; display:none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Success Modal -->
    <div id="successModal" class="success-modal">
        <div class="success-modal-content">
            <div class="success-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="success-title">Registration Successful!</h2>
            <p class="success-message">
                Your business account has been created successfully and you have been automatically logged in. Welcome to The NexZen platform!
            </p>
            <button class="success-button" onclick="redirectToDashboard()">Go to Dashboard</button>
        </div>
    </div>
    
    <script>
    const baseUrl = '<?php echo e(url('/')); ?>';
    let currentSessionId = null;
    
    // Check if registration was successful
    <?php if(session('success')): ?>
        document.addEventListener('DOMContentLoaded', function() {
            showSuccessModal();
        });
    <?php endif; ?>
    
    function showSuccessModal() {
        const modal = document.getElementById('successModal');
        modal.classList.add('show');
    }
    
    function redirectToLogin() {
        window.location.href = '<?php echo e(route("business.login")); ?>';
    }
    
    function redirectToDashboard() {
        window.location.href = '<?php echo e(route("business.dashboard")); ?>';
    }
    
    // Close modal when clicking outside
    document.getElementById('successModal').addEventListener('click', function(e) {
        if (e.target === this) {
            redirectToDashboard();
        }
    });

    // OTP Verification Flow
    document.addEventListener('DOMContentLoaded', function() {
        // Step 1: Send OTP
        document.getElementById('businessInfoForm').addEventListener('submit', function(e) {
            e.preventDefault();
            sendOTP();
        });

        // Step 2: Verify OTP
        document.getElementById('otpVerificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            verifyOTP();
        });

        // Step 3: Complete Registration
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            completeRegistration();
        });

        // Resend OTP
        document.getElementById('resendOTPBtn').addEventListener('click', function() {
            resendOTP();
        });

        // OTP input formatting
        document.getElementById('otp').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').substring(0, 6);
        });
    });

    function sendOTP() {
        const formData = new FormData(document.getElementById('businessInfoForm'));
        const data = Object.fromEntries(formData);
        
        console.log('Sending OTP with data:', data);
        
        fetch('<?php echo e(route("business.register.send-otp")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            // Check if response is HTML (error page)
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('text/html')) {
                return response.text().then(html => {
                    console.error('Received HTML instead of JSON:', html.substring(0, 500));
                    throw new Error('Server returned HTML error page. Check console for details.');
                });
            }
            return response.json();
        })
        .then(result => {
            console.log('OTP response:', result);
            if (result.success) {
                currentSessionId = result.session_id;
                document.getElementById('otpEmail').textContent = result.email;
                showStep(2);
                showMessage('OTP sent successfully! Check your email.', 'success');
            } else {
                showMessage(result.message || 'Failed to send OTP', 'error');
            }
        })
        .catch(error => {
            console.error('Error sending OTP:', error);
            showMessage('Failed to send OTP. Please check your email and try again. ' + error.message, 'error');
        });
    }

    function verifyOTP() {
        const otp = document.getElementById('otp').value;
        
        if (otp.length !== 6) {
            showMessage('Please enter a valid 6-digit OTP.', 'error');
            return;
        }

        fetch('<?php echo e(route("business.register.verify-otp")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                otp: otp,
                session_id: currentSessionId
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showStep(3);
                showMessage('OTP verified successfully!', 'success');
            } else {
                showMessage(result.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Failed to verify OTP. Please try again.', 'error');
        });
    }

    function completeRegistration() {
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        
        if (password !== passwordConfirmation) {
            showMessage('Passwords do not match.', 'error');
            return;
        }

        if (password.length < 8) {
            showMessage('Password must be at least 8 characters long.', 'error');
            return;
        }

        fetch('<?php echo e(route("business.register.submit")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                password: password,
                password_confirmation: passwordConfirmation,
                session_id: currentSessionId
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showMessage('Registration successful! Redirecting to dashboard...', 'success');
                setTimeout(() => {
                    window.location.href = result.redirect_url;
                }, 2000);
            } else {
                showMessage(result.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Registration failed. Please try again.', 'error');
        });
    }

    function resendOTP() {
        fetch('<?php echo e(route("business.register.resend-otp")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                session_id: currentSessionId
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showMessage('New OTP sent successfully!', 'success');
            } else {
                showMessage(result.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Failed to resend OTP. Please try again.', 'error');
        });
    }

    function showStep(step) {
        // Hide all forms
        document.getElementById('businessInfoForm').style.display = 'none';
        document.getElementById('otpVerificationForm').style.display = 'none';
        document.getElementById('passwordForm').style.display = 'none';
        
        // Show the current step
        if (step === 1) {
            document.getElementById('businessInfoForm').style.display = 'block';
        } else if (step === 2) {
            document.getElementById('otpVerificationForm').style.display = 'block';
        } else if (step === 3) {
            document.getElementById('passwordForm').style.display = 'block';
        }
    }

    function showMessage(message, type) {
        const errorDiv = document.getElementById('errorMessage');
        errorDiv.textContent = message;
        errorDiv.style.color = type === 'success' ? 'green' : 'red';
        errorDiv.style.display = 'block';
        
        // Auto-hide success messages
        if (type === 'success') {
            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 5000);
        }
    }
    </script>
    <script src="<?php echo e(asset('dist/js/common.js')); ?>"></script>
</body>

</html>
<?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/business/auth/register.blade.php ENDPATH**/ ?>