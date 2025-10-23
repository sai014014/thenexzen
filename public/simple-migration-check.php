<?php
// Simple migration status checker that bypasses Livewire entirely
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Simple Migration Status Check ===\n";

try {
    // Check if we can connect to database directly
    $config = include __DIR__ . '/../config/database.php';
    $mysqlConfig = $config['connections']['mysql'];
    
    $dsn = "mysql:host={$mysqlConfig['host']};port={$mysqlConfig['port']};dbname={$mysqlConfig['database']};charset={$mysqlConfig['charset']}";
    
    $pdo = new PDO($dsn, $mysqlConfig['username'], $mysqlConfig['password']);
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
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>
