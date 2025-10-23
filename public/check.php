<?php
/**
 * Laravel Deployment Diagnostic Tool
 * This file helps diagnose common issues when deploying Laravel to a web server
 * 
 * Instructions:
 * 1. Upload this file to your public directory
 * 2. Access it via: https://yourdomain.com/check.php
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
    die('Access denied. Add ?force=1 to URL to bypass IP check.<br><br>Example: https://yourdomain.com/check.php?force=1');
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

            <div class="check-item <?php echo (strpos(getcwd(), 'public') !== false) ? 'success' : 'error'; ?>">
                <h3>Directory Structure Check</h3>
                <?php if (strpos(getcwd(), 'public') !== false): ?>
                    <p>✅ You're in the correct public directory</p>
                <?php else: ?>
                    <p>❌ You're not in the public directory. Current path: <?php echo getcwd(); ?></p>
                    <p><strong>Fix:</strong> Make sure your web server's document root points to the 'public' directory of your Laravel application.</p>
                <?php endif; ?>
            </div>

            <div class="check-item <?php echo (file_exists('../artisan') && file_exists('index.php')) ? 'success' : 'error'; ?>">
                <h3>Laravel Structure Check</h3>
                <?php if (file_exists('../artisan') && file_exists('index.php')): ?>
                    <p>✅ Laravel structure is correct - artisan file and index.php found</p>
                <?php else: ?>
                    <p>❌ Laravel structure issue detected</p>
                    <ul>
                        <li>Artisan file exists: <?php echo file_exists('../artisan') ? 'Yes' : 'No'; ?></li>
                        <li>Index.php exists: <?php echo file_exists('index.php') ? 'Yes' : 'No'; ?></li>
                    </ul>
                <?php endif; ?>
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
            checkFile('index.php', 'Laravel entry point (current directory)');
            checkFile('.htaccess', 'Apache configuration file');
            
            // Check if we're in the right directory structure
            if (file_exists('../artisan') && file_exists('index.php')) {
                $successes[] = "✅ Correct Laravel directory structure detected";
            } else {
                $issues[] = "❌ Incorrect directory structure - make sure you're in the public directory";
            }
            
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

            // Check Laravel bootstrap files
            if (file_exists('../bootstrap/app.php')) {
                $successes[] = "✅ Laravel bootstrap file exists";
            } else {
                $issues[] = "❌ Laravel bootstrap file missing";
            }

            // Check if we can include Laravel files
            try {
                if (file_exists('../vendor/autoload.php')) {
                    require_once '../vendor/autoload.php';
                    $successes[] = "✅ Composer autoload can be included";
                }
            } catch (Exception $e) {
                $issues[] = "❌ Composer autoload error: " . $e->getMessage();
            }

            // Check Laravel configuration files
            $config_files = [
                '../config/app.php' => 'App configuration',
                '../config/database.php' => 'Database configuration',
                '../config/mail.php' => 'Mail configuration',
                '../config/session.php' => 'Session configuration'
            ];

            foreach ($config_files as $file => $description) {
                if (file_exists($file)) {
                    $successes[] = "✅ $description file exists";
                } else {
                    $issues[] = "❌ $description file missing";
                }
            }
            ?>
        </div>

        <!-- HTTP 500 Specific Checks -->
        <div class="section">
            <h2>🚨 HTTP 500 Error Specific Checks</h2>
            
            <?php
            // Check if index.php can be executed
            if (file_exists('index.php')) {
                $index_content = file_get_contents('index.php');
                if (strpos($index_content, 'Laravel') !== false || strpos($index_content, 'artisan') !== false) {
                    $successes[] = "✅ index.php appears to be a valid Laravel entry point";
                } else {
                    $issues[] = "❌ index.php doesn't appear to be a Laravel entry point";
                }
            }

            // Check .htaccess file content
            if (file_exists('.htaccess')) {
                $htaccess_content = file_get_contents('.htaccess');
                if (strpos($htaccess_content, 'RewriteEngine On') !== false) {
                    $successes[] = "✅ .htaccess has RewriteEngine enabled";
                } else {
                    $issues[] = "❌ .htaccess missing RewriteEngine directive";
                }
                
                if (strpos($htaccess_content, 'index.php') !== false) {
                    $successes[] = "✅ .htaccess has index.php rewrite rule";
                } else {
                    $issues[] = "❌ .htaccess missing index.php rewrite rule";
                }
            } else {
                $issues[] = "❌ .htaccess file missing - this will cause 500 errors";
            }

            // Check if mod_rewrite is enabled
            if (function_exists('apache_get_modules')) {
                if (in_array('mod_rewrite', apache_get_modules())) {
                    $successes[] = "✅ mod_rewrite is enabled";
                } else {
                    $issues[] = "❌ mod_rewrite is not enabled - required for Laravel";
                }
            } else {
                $warnings[] = "⚠️ Cannot check if mod_rewrite is enabled";
            }

            // Check if we can access Laravel routes
            try {
                if (file_exists('../vendor/autoload.php')) {
                    require_once '../vendor/autoload.php';
                    
                    // Try to create a minimal Laravel app instance
                    if (file_exists('../bootstrap/app.php')) {
                        $app = require_once '../bootstrap/app.php';
                        $successes[] = "✅ Laravel application can be bootstrapped";
                    }
                }
            } catch (Exception $e) {
                $issues[] = "❌ Laravel bootstrap error: " . $e->getMessage();
            } catch (Error $e) {
                $issues[] = "❌ Laravel fatal error: " . $e->getMessage();
            }

            // Check for common Laravel error patterns
            $error_patterns = [
                'Class not found' => 'Missing class files',
                'Call to undefined function' => 'Missing PHP functions',
                'Parse error' => 'Syntax errors in PHP files',
                'Fatal error' => 'Critical PHP errors',
                'Permission denied' => 'File permission issues'
            ];

            // Check if there are any error logs
            $log_files = [
                '../storage/logs/laravel.log',
                '../storage/logs/laravel-' . date('Y-m-d') . '.log',
                '../storage/logs/error.log'
            ];

            $found_logs = false;
            foreach ($log_files as $log_file) {
                if (file_exists($log_file) && filesize($log_file) > 0) {
                    $found_logs = true;
                    $log_content = file_get_contents($log_file);
                    $log_size = filesize($log_file);
                    
                    if ($log_size > 0) {
                        $warnings[] = "⚠️ Log file found: " . basename($log_file) . " ($log_size bytes)";
                        
                        // Check for recent errors
                        $recent_errors = substr($log_content, -2000); // Last 2000 characters
                        foreach ($error_patterns as $pattern => $description) {
                            if (stripos($recent_errors, $pattern) !== false) {
                                $issues[] = "❌ Recent error found: $description";
                            }
                        }
                    }
                }
            }

            if (!$found_logs) {
                $warnings[] = "⚠️ No log files found - errors might not be logged";
            }

            // Check if we can write to storage/logs
            if (is_dir('../storage/logs')) {
                $test_log = '../storage/logs/test_' . time() . '.log';
                if (file_put_contents($test_log, 'test log entry')) {
                    unlink($test_log);
                    $successes[] = "✅ Can write to storage/logs directory";
                } else {
                    $issues[] = "❌ Cannot write to storage/logs directory";
                }
            }

            // Check if cache directories are writable
            $cache_dirs = [
                '../storage/framework/cache' => 'Framework cache',
                '../storage/framework/sessions' => 'Session storage',
                '../storage/framework/views' => 'View cache',
                '../bootstrap/cache' => 'Bootstrap cache'
            ];

            foreach ($cache_dirs as $dir => $description) {
                if (is_dir($dir)) {
                    $test_file = $dir . '/test_' . time() . '.txt';
                    if (file_put_contents($test_file, 'test')) {
                        unlink($test_file);
                        $successes[] = "✅ $description directory is writable";
                    } else {
                        $issues[] = "❌ $description directory is not writable";
                    }
                } else {
                    $issues[] = "❌ $description directory does not exist";
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

        <!-- Live Laravel Test -->
        <div class="section">
            <h2>🧪 Live Laravel Application Test</h2>
            
            <?php
            // Test if we can actually run the Laravel application
            try {
                // Capture any output that might be generated
                ob_start();
                
                // Try to include the Laravel bootstrap
                if (file_exists('../vendor/autoload.php') && file_exists('../bootstrap/app.php')) {
                    require_once '../vendor/autoload.php';
                    
                    // Try to create the application
                    $app = require_once '../bootstrap/app.php';
                    
                    // Try to make a simple request
                    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
                    $request = Illuminate\Http\Request::capture();
                    
                    // This will actually try to process the request
                    $response = $kernel->handle($request);
                    
                    $output = ob_get_clean();
                    
                    if ($response instanceof Illuminate\Http\Response) {
                        $successes[] = "✅ Laravel application can process requests successfully";
                        $successes[] = "✅ HTTP Status: " . $response->getStatusCode();
                    } else {
                        $issues[] = "❌ Laravel application failed to process requests";
                    }
                } else {
                    $issues[] = "❌ Cannot test Laravel application - missing core files";
                }
            } catch (Exception $e) {
                $output = ob_get_clean();
                $issues[] = "❌ Laravel Exception: " . $e->getMessage();
                $issues[] = "❌ File: " . $e->getFile() . " Line: " . $e->getLine();
            } catch (Error $e) {
                $output = ob_get_clean();
                $issues[] = "❌ PHP Fatal Error: " . $e->getMessage();
                $issues[] = "❌ File: " . $e->getFile() . " Line: " . $e->getLine();
            } catch (Throwable $e) {
                $output = ob_get_clean();
                $issues[] = "❌ Throwable Error: " . $e->getMessage();
                $issues[] = "❌ File: " . $e->getFile() . " Line: " . $e->getLine();
            }
            
            // Show any captured output
            if (!empty($output)) {
                echo '<div class="check-item warning">';
                echo '<h3>Captured Output:</h3>';
                echo '<div class="code">' . htmlspecialchars($output) . '</div>';
                echo '</div>';
            }
            ?>
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

                <h4>Check Laravel Logs:</h4>
                <div class="code">
tail -f storage/logs/laravel.log
                </div>

                <h4>Test Laravel Routes:</h4>
                <div class="code">
php artisan route:list
                </div>
            </div>
        </div>

        <div class="check-item error">
            <h3>⚠️ Security Warning</h3>
            <p><strong>IMPORTANT:</strong> Delete this file (check.php) after checking your Laravel application for security reasons!</p>
        </div>
    </div>
</body>
</html>
