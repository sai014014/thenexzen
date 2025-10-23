<?php
// Direct migration status checker that reads .env directly
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Direct Migration Status Check ===\n";

try {
    // Read .env file directly
    $envFile = __DIR__ . '/../.env';
    if (!file_exists($envFile)) {
        throw new Exception(".env file not found");
    }
    
    $envContent = file_get_contents($envFile);
    $envVars = [];
    
    foreach (explode("\n", $envContent) as $line) {
        $line = trim($line);
        if (strpos($line, '=') !== false && !str_starts_with($line, '#')) {
            list($key, $value) = explode('=', $line, 2);
            $envVars[trim($key)] = trim($value);
        }
    }
    
    // Get database config
    $host = $envVars['DB_HOST'] ?? 'localhost';
    $port = $envVars['DB_PORT'] ?? '3306';
    $database = $envVars['DB_DATABASE'] ?? '';
    $username = $envVars['DB_USERNAME'] ?? '';
    $password = $envVars['DB_PASSWORD'] ?? '';
    
    echo "Database: $database@$host:$port\n";
    
    // Connect to database
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Database connection successful\n";
    
    // Check if migrations table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
    $migrationsTableExists = $stmt->rowCount() > 0;
    
    echo "Migrations table exists: " . ($migrationsTableExists ? 'YES' : 'NO') . "\n";
    
    if ($migrationsTableExists) {
        // Get ran migrations
        $stmt = $pdo->query("SELECT migration FROM migrations ORDER BY batch, migration");
        $ranMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $ranCount = count($ranMigrations);
        
        echo "Ran migrations: $ranCount\n";
        
        if ($ranCount > 0) {
            echo "Last 5 ran migrations:\n";
            $lastFive = array_slice($ranMigrations, -5);
            foreach ($lastFive as $migration) {
                echo "  - $migration\n";
            }
        }
    }
    
    // Get migration files
    $migrationPath = __DIR__ . '/../database/migrations';
    $files = glob($migrationPath . '/*.php');
    $migrationFiles = [];
    
    foreach ($files as $file) {
        $migrationFiles[] = basename($file, '.php');
    }
    
    sort($migrationFiles);
    echo "Total migration files: " . count($migrationFiles) . "\n";
    
    if ($migrationsTableExists) {
        $pendingMigrations = array_diff($migrationFiles, $ranMigrations);
        
        echo "Pending migrations: " . count($pendingMigrations) . "\n";
        
        if (count($pendingMigrations) > 0) {
            echo "Pending migration files:\n";
            foreach ($pendingMigrations as $migration) {
                echo "  - $migration\n";
            }
        }
    } else {
        echo "All migrations are pending (no migrations table)\n";
    }
    
    // Check existing tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "\nExisting tables (" . count($tables) . "):\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    
    // Check for specific tables that might cause issues
    $problematicTables = ['vehicle_makes', 'vehicle_models'];
    echo "\nChecking for problematic tables:\n";
    foreach ($problematicTables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->rowCount() > 0;
        echo "  - $table: " . ($exists ? 'EXISTS' : 'NOT FOUND') . "\n";
    }
    
    echo "\n=== RECOMMENDATIONS ===\n";
    if ($migrationsTableExists) {
        if (count($pendingMigrations) > 0) {
            echo "1. Use 'Sync Existing Tables' in super admin to mark existing tables as migrated\n";
            echo "2. Then use 'Run Migrations' to run any remaining new migrations\n";
        } else {
            echo "✓ All migrations are up to date\n";
        }
    } else {
        echo "1. Use 'Run Migrations' to create the migrations table and run all migrations\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>
