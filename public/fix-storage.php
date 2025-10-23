<?php
/**
 * Laravel Storage Directory Fix
 * This script creates all required Laravel storage directories with correct permissions
 */

// Security check
$allowed_ips = ['127.0.0.1', '::1', 'localhost'];
$force_access = isset($_GET['force']) && $_GET['force'] == '1';

if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips) && !$force_access) {
    die('Access denied. Add ?force=1 to URL to bypass IP check.<br><br>Example: https://yourdomain.com/fix-storage.php?force=1');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Laravel Storage Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #2c3e50; color: white; padding: 20px; margin: -20px -20px 20px -20px; border-radius: 8px 8px 0 0; }
        .success { color: #27ae60; background: #d5f4e6; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { color: #e74c3c; background: #fadbd8; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .info { color: #3498db; background: #e8f4fd; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .code { background: #2c3e50; color: #ecf0f1; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Laravel Storage Directory Fix</h1>
            <p>Creating required Laravel storage directories...</p>
        </div>

        <?php
        $successes = [];
        $errors = [];
        $warnings = [];

        // Function to create directory with permissions
        function createDirectory($path, $description) {
            global $successes, $errors;
            
            if (!is_dir($path)) {
                if (mkdir($path, 0755, true)) {
                    $successes[] = "✅ Created directory: $description ($path)";
                    return true;
                } else {
                    $errors[] = "❌ Failed to create directory: $description ($path)";
                    return false;
                }
            } else {
                $successes[] = "✅ Directory already exists: $description ($path)";
                return true;
            }
        }

        // Function to create file with content
        function createFile($path, $content, $description) {
            global $successes, $errors;
            
            if (file_put_contents($path, $content)) {
                $successes[] = "✅ Created file: $description ($path)";
                return true;
            } else {
                $errors[] = "❌ Failed to create file: $description ($path)";
                return false;
            }
        }

        // Check if we're in the right location
        if (!file_exists('../artisan')) {
            echo '<div class="error">❌ Error: This script must be run from the public directory of a Laravel application.</div>';
            exit;
        }

        echo '<h2>📁 Creating Required Directories</h2>';

        // Create storage directories
        createDirectory('../storage/app', 'Storage App');
        createDirectory('../storage/framework', 'Storage Framework');
        createDirectory('../storage/framework/cache', 'Storage Framework Cache');
        createDirectory('../storage/framework/sessions', 'Storage Framework Sessions');
        createDirectory('../storage/framework/views', 'Storage Framework Views');
        createDirectory('../storage/logs', 'Storage Logs');

        // Create bootstrap/cache directory
        createDirectory('../bootstrap/cache', 'Bootstrap Cache');

        echo '<h2>📄 Creating Required Files</h2>';

        // Create .gitignore files
        createFile('../storage/.gitignore', "*\n!.gitignore\n", 'Storage .gitignore');
        createFile('../storage/app/.gitignore', "*\n!.gitignore\n", 'Storage App .gitignore');
        createFile('../storage/framework/.gitignore', "*\n!.gitignore\n", 'Storage Framework .gitignore');
        createFile('../storage/framework/cache/.gitignore', "*\n!.gitignore\n", 'Storage Framework Cache .gitignore');
        createFile('../storage/framework/sessions/.gitignore', "*\n!.gitignore\n", 'Storage Framework Sessions .gitignore');
        createFile('../storage/framework/views/.gitignore', "*\n!.gitignore\n", 'Storage Framework Views .gitignore');
        createFile('../storage/logs/.gitignore', "*\n!.gitignore\n", 'Storage Logs .gitignore');

        // Create bootstrap/cache .gitignore
        createFile('../bootstrap/cache/.gitignore', "*\n!.gitignore\n", 'Bootstrap Cache .gitignore');

        echo '<h2>🔐 Setting Permissions</h2>';

        // Set permissions
        $directories_to_fix = [
            '../storage' => 'Storage directory',
            '../storage/app' => 'Storage app directory',
            '../storage/framework' => 'Storage framework directory',
            '../storage/framework/cache' => 'Storage framework cache directory',
            '../storage/framework/sessions' => 'Storage framework sessions directory',
            '../storage/framework/views' => 'Storage framework views directory',
            '../storage/logs' => 'Storage logs directory',
            '../bootstrap/cache' => 'Bootstrap cache directory'
        ];

        foreach ($directories_to_fix as $dir => $description) {
            if (is_dir($dir)) {
                if (chmod($dir, 0755)) {
                    $successes[] = "✅ Set permissions for: $description";
                } else {
                    $warnings[] = "⚠️ Could not set permissions for: $description";
                }
            }
        }

        echo '<h2>📊 Summary</h2>';

        if (!empty($successes)) {
            echo '<div class="success">';
            echo '<h3>✅ Successes (' . count($successes) . ')</h3>';
            echo '<ul>';
            foreach ($successes as $success) {
                echo '<li>' . $success . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        if (!empty($warnings)) {
            echo '<div class="info">';
            echo '<h3>⚠️ Warnings (' . count($warnings) . ')</h3>';
            echo '<ul>';
            foreach ($warnings as $warning) {
                echo '<li>' . $warning . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        if (!empty($errors)) {
            echo '<div class="error">';
            echo '<h3>❌ Errors (' . count($errors) . ')</h3>';
            echo '<ul>';
            foreach ($errors as $error) {
                echo '<li>' . $error . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        if (empty($errors)) {
            echo '<div class="success">';
            echo '<h3>🎉 Storage Fix Complete!</h3>';
            echo '<p>All required Laravel storage directories have been created. Your Laravel application should now work properly.</p>';
            echo '<p><strong>Next steps:</strong></p>';
            echo '<ul>';
            echo '<li>Try accessing your Laravel application again</li>';
            echo '<li>Run: <code>php artisan config:cache</code></li>';
            echo '<li>Run: <code>php artisan route:cache</code></li>';
            echo '<li>Delete this fix-storage.php file for security</li>';
            echo '</ul>';
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<h3>❌ Some errors occurred</h3>';
            echo '<p>Please check the errors above and try to fix them manually, or contact your hosting provider for assistance.</p>';
            echo '</div>';
        }
        ?>

        <div class="info">
            <h3>🔧 Manual Commands (if needed)</h3>
            <p>If the automatic fix didn't work, you can run these commands manually via SSH:</p>
            <div class="code">
# Create storage directories<br>
mkdir -p storage/framework/{cache,sessions,views}<br>
mkdir -p storage/{app,logs}<br>
mkdir -p bootstrap/cache<br><br>
# Set permissions<br>
chmod -R 755 storage/<br>
chmod -R 755 bootstrap/cache/<br><br>
# Create .gitignore files<br>
touch storage/.gitignore storage/app/.gitignore storage/framework/.gitignore<br>
touch storage/framework/cache/.gitignore storage/framework/sessions/.gitignore<br>
touch storage/framework/views/.gitignore storage/logs/.gitignore<br>
touch bootstrap/cache/.gitignore
            </div>
        </div>

        <div class="info">
            <h3>⚠️ Security Warning</h3>
            <p><strong>IMPORTANT:</strong> Delete this file (fix-storage.php) after fixing your storage directories for security reasons!</p>
        </div>
    </div>
</body>
</html>
