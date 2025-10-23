    <?php
    /**
     * Laravel Deployment Diagnostic Tool
     * This file helps diagnose common issues when deploying Laravel to a web server
     * 
     * Instructions:
     * 1. Upload this file to your public directory
     * 2. Access it via: https://yourdomain.com/laravel-check.php
     * 3. Review the results and fix any issues shown
     * 4. DELETE this file after checking for security reasons
     */

// Security check - allow access with force parameter or from common hosting IPs
$allowed_ips = ['127.0.0.1', '::1', 'localhost'];
$force_access = isset($_GET['force']) && $_GET['force'] == '1';
$is_common_hosting = isset($_SERVER['HTTP_HOST']) && (
    strpos($_SERVER['HTTP_HOST'], 'cpanel') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'plesk') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'hostinger') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'godaddy') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'bluehost') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'siteground') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'a2hosting') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'inmotion') !== false
);

if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips) && !$force_access && !$is_common_hosting) {
    die('Access denied. Add ?force=1 to URL to bypass IP check.<br><br>Example: https://yourdomain.com/laravel-check.php?force=1');
}

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Laravel Deployment Check</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .header { background: #2c3e50; color: white; padding: 20px; margin: -20px -20px 20px -20px; border-radius: 8px 8px 0 0; }
            .check-item { margin: 15px 0; padding: 15px; border-left: 4px solid #ddd; background: #f9f9f9; }
            .success { border-left-color: #27ae60; background: #d5f4e6; }
            .warning { border-left-color: #f39c12; background: #fef9e7; }
            .error { border-left-color: #e74c3c; background: #fadbd8; }
            .info { border-left-color: #3498db; background: #e8f4fd; }
            .code { background: #2c3e50; color: #ecf0f1; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; }
            .section { margin: 30px 0; }
            h2 { color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; }
            h3 { color: #34495e; margin-top: 25px; }
            .status { font-weight: bold; padding: 5px 10px; border-radius: 4px; }
            .status.success { background: #27ae60; color: white; }
            .status.warning { background: #f39c12; color: white; }
            .status.error { background: #e74c3c; color: white; }
            .status.info { background: #3498db; color: white; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🚀 Laravel Deployment Diagnostic Tool</h1>
                <p>Checking your Laravel application for common deployment issues...</p>
            </div>

            <?php
            $issues = [];
            $warnings = [];
            $successes = [];

            // Function to check if a file exists and is readable
            function checkFile($path, $description) {
                global $issues, $warnings, $successes;
                
                if (file_exists($path)) {
                    if (is_readable($path)) {
                        $successes[] = "✅ $description exists and is readable";
                        return true;
                    } else {
                        $issues[] = "❌ $description exists but is not readable";
                        return false;
                    }
                } else {
                    $issues[] = "❌ $description not found at: $path";
                    return false;
                }
            }

            // Function to check directory permissions
            function checkDirectory($path, $description, $required = true) {
                global $issues, $warnings, $successes;
                
                if (is_dir($path)) {
                    if (is_readable($path) && is_writable($path)) {
                        $successes[] = "✅ $description directory exists and is writable";
                        return true;
                    } elseif (is_readable($path)) {
                        $warnings[] = "⚠️ $description directory exists and is readable but not writable";
                        return false;
                    } else {
                        $issues[] = "❌ $description directory exists but is not readable";
                        return false;
                    }
                } else {
                    if ($required) {
                        $issues[] = "❌ $description directory not found at: $path";
                    } else {
                        $warnings[] = "⚠️ $description directory not found at: $path";
                    }
                    return false;
                }
            }

            // Function to check PHP extensions
            function checkExtension($extension, $description) {
                global $issues, $successes;
                
                if (extension_loaded($extension)) {
                    $successes[] = "✅ $description extension is loaded";
                    return true;
                } else {
                    $issues[] = "❌ $description extension is not loaded";
                    return false;
                }
            }

            // Function to check PHP configuration
            function checkPhpConfig($setting, $expected, $description) {
                global $issues, $warnings, $successes;
                
                $current = ini_get($setting);
                if ($current >= $expected) {
                    $successes[] = "✅ $description: $current (required: $expected)";
                    return true;
                } else {
                    $warnings[] = "⚠️ $description: $current (recommended: $expected)";
                    return false;
                }
            }
            ?>

            <!-- Basic Environment Check -->
            <div class="section">
                <h2>🔍 Basic Environment Check</h2>
                
                <div class="check-item info">
                    <h3>Server Information</h3>
                    <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
                    <p><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
                    <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></p>
                    <p><strong>Current Directory:</strong> <?php echo getcwd(); ?></p>
                    <p><strong>Script Path:</strong> <?php echo __FILE__; ?></p>
                </div>

                <div class="check-item <?php echo version_compare(PHP_VERSION, '8.0.0', '>=') ? 'success' : 'error'; ?>">
                    <h3>PHP Version Check</h3>
                    <?php if (version_compare(PHP_VERSION, '8.0.0', '>=')): ?>
                        <p>✅ PHP version <?php echo PHP_VERSION; ?> is compatible with Laravel 8+</p>
                    <?php else: ?>
                        <p>❌ PHP version <?php echo PHP_VERSION; ?> is too old. Laravel 8+ requires PHP 8.0+</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- File Structure Check -->
            <div class="section">
                <h2>📁 File Structure Check</h2>
                
                <?php
                // Check essential Laravel files
                checkFile('../artisan', 'Artisan command file');
                checkFile('../composer.json', 'Composer configuration file');
                checkFile('../.env', 'Environment configuration file');
                checkFile('../.env.example', 'Environment example file');
                checkFile('index.php', 'Laravel entry point');
                checkFile('.htaccess', 'Apache configuration file');
                
                // Check directories
                checkDirectory('../storage', 'Storage', true);
                checkDirectory('../bootstrap/cache', 'Bootstrap cache', true);
                checkDirectory('../vendor', 'Vendor', true);
                checkDirectory('../app', 'App', true);
                checkDirectory('../config', 'Config', true);
                checkDirectory('../database', 'Database', true);
                checkDirectory('../resources', 'Resources', true);
                checkDirectory('../routes', 'Routes', true);
                ?>
            </div>

            <!-- PHP Extensions Check -->
            <div class="section">
                <h2>🔧 PHP Extensions Check</h2>
                
                <?php
                $required_extensions = [
                    'openssl' => 'OpenSSL',
                    'pdo' => 'PDO',
                    'mbstring' => 'Multibyte String',
                    'tokenizer' => 'Tokenizer',
                    'xml' => 'XML',
                    'ctype' => 'Ctype',
                    'json' => 'JSON',
                    'bcmath' => 'BCMath',
                    'fileinfo' => 'Fileinfo',
                    'curl' => 'cURL'
                ];

                foreach ($required_extensions as $ext => $name) {
                    checkExtension($ext, $name);
                }
                ?>
            </div>

            <!-- PHP Configuration Check -->
            <div class="section">
                <h2>⚙️ PHP Configuration Check</h2>
                
                <?php
                checkPhpConfig('memory_limit', 128, 'Memory Limit (MB)');
                checkPhpConfig('max_execution_time', 30, 'Max Execution Time (seconds)');
                checkPhpConfig('upload_max_filesize', 2, 'Upload Max Filesize (MB)');
                checkPhpConfig('post_max_size', 8, 'Post Max Size (MB)');
                checkPhpConfig('max_input_vars', 1000, 'Max Input Variables');
                ?>
            </div>

            <!-- Permission Check -->
            <div class="section">
                <h2>🔐 Permission Check</h2>
                
                <?php
                // Check if we can write to storage
                if (is_dir('../storage')) {
                    $test_file = '../storage/test_write.txt';
                    if (file_put_contents($test_file, 'test')) {
                        unlink($test_file);
                        $successes[] = "✅ Storage directory is writable";
                    } else {
                        $issues[] = "❌ Storage directory is not writable";
                    }
                }

                // Check if we can write to bootstrap/cache
                if (is_dir('../bootstrap/cache')) {
                    $test_file = '../bootstrap/cache/test_write.txt';
                    if (file_put_contents($test_file, 'test')) {
                        unlink($test_file);
                        $successes[] = "✅ Bootstrap cache directory is writable";
                    } else {
                        $issues[] = "❌ Bootstrap cache directory is not writable";
                    }
                }

                // Check .env file permissions
                if (file_exists('../.env')) {
                    $env_perms = fileperms('../.env');
                    if ($env_perms & 0x0004) { // Check if readable by others
                        $warnings[] = "⚠️ .env file is readable by others (security risk)";
                    } else {
                        $successes[] = "✅ .env file has secure permissions";
                    }
                }
                ?>
            </div>

            <!-- Laravel Specific Checks -->
            <div class="section">
                <h2>🎯 Laravel Specific Checks</h2>
                
                <?php
                // Check if we can run artisan commands
                if (file_exists('../artisan')) {
                    $output = [];
                    $return_var = 0;
                    exec('cd .. && php artisan --version 2>&1', $output, $return_var);
                    
                    if ($return_var === 0) {
                        $successes[] = "✅ Artisan commands work: " . implode(' ', $output);
                    } else {
                        $issues[] = "❌ Artisan commands failed: " . implode(' ', $output);
                    }
                }

                // Check if vendor autoload exists
                if (file_exists('../vendor/autoload.php')) {
                    $successes[] = "✅ Composer autoload file exists";
                } else {
                    $issues[] = "❌ Composer autoload file not found - run 'composer install'";
                }

                // Check if application key is set
                if (file_exists('../.env')) {
                    $env_content = file_get_contents('../.env');
                    if (strpos($env_content, 'APP_KEY=') !== false && strpos($env_content, 'APP_KEY=base64:') !== false) {
                        $successes[] = "✅ Application key is set";
                    } else {
                        $issues[] = "❌ Application key is not set - run 'php artisan key:generate'";
                    }
                }
                ?>
            </div>

            <!-- Database Check -->
            <div class="section">
                <h2>🗄️ Database Check</h2>
                
                <?php
                if (file_exists('../.env')) {
                    $env_content = file_get_contents('../.env');
                    
                    // Check database configuration
                    if (strpos($env_content, 'DB_CONNECTION=') !== false) {
                        $successes[] = "✅ Database connection is configured";
                    } else {
                        $warnings[] = "⚠️ Database connection not configured";
                    }
                    
                    if (strpos($env_content, 'DB_DATABASE=') !== false) {
                        $successes[] = "✅ Database name is configured";
                    } else {
                        $warnings[] = "⚠️ Database name not configured";
                    }
                }
                ?>
            </div>

            <!-- Summary -->
            <div class="section">
                <h2>📊 Summary</h2>
                
                <div class="check-item <?php echo empty($issues) ? 'success' : 'error'; ?>">
                    <h3>Issues Found: <?php echo count($issues); ?></h3>
                    <?php if (!empty($issues)): ?>
                        <ul>
                            <?php foreach ($issues as $issue): ?>
                                <li><?php echo $issue; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>🎉 No critical issues found!</p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($warnings)): ?>
                <div class="check-item warning">
                    <h3>Warnings: <?php echo count($warnings); ?></h3>
                    <ul>
                        <?php foreach ($warnings as $warning): ?>
                            <li><?php echo $warning; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($successes)): ?>
                <div class="check-item success">
                    <h3>Successes: <?php echo count($successes); ?></h3>
                    <ul>
                        <?php foreach ($successes as $success): ?>
                            <li><?php echo $success; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <!-- Quick Fixes -->
            <div class="section">
                <h2>🔧 Quick Fixes</h2>
                
                <div class="check-item info">
                    <h3>Common Commands to Fix Issues</h3>
                    
                    <h4>Set File Permissions:</h4>
                    <div class="code">
    # Set correct permissions for files and directories<br>
    find . -type f -exec chmod 644 {} \;<br>
    find . -type d -exec chmod 755 {} \;<br><br>
    # Set Laravel-specific permissions<br>
    chmod -R 775 storage/<br>
    chmod -R 775 bootstrap/cache/<br>
    chmod 644 .env
                    </div>

                    <h4>Install Dependencies:</h4>
                    <div class="code">
    composer install --no-dev --optimize-autoloader
                    </div>

                    <h4>Generate Application Key:</h4>
                    <div class="code">
    php artisan key:generate
                    </div>

                    <h4>Clear and Cache:</h4>
                    <div class="code">
    php artisan config:cache<br>
    php artisan route:cache<br>
    php artisan view:cache
                    </div>

                    <h4>Run Migrations:</h4>
                    <div class="code">
    php artisan migrate
                    </div>
                </div>
            </div>

            <div class="check-item error">
                <h3>⚠️ Security Warning</h3>
                <p><strong>IMPORTANT:</strong> Delete this file (laravel-check.php) after checking your Laravel application for security reasons!</p>
            </div>
        </div>
    </body>
    </html>
