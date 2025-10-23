<?php
// Simple migration status checker that bypasses Livewire issues
require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    // Bootstrap Laravel
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "=== Migration Status Check ===\n";
    
    // Check if migrations table exists
    $migrationsTableExists = Schema::hasTable('migrations');
    echo "Migrations table exists: " . ($migrationsTableExists ? 'YES' : 'NO') . "\n";
    
    if ($migrationsTableExists) {
        // Get ran migrations
        $ranMigrations = DB::select('SELECT migration FROM migrations ORDER BY batch, migration');
        $ranCount = count($ranMigrations);
        echo "Ran migrations: $ranCount\n";
        
        if ($ranCount > 0) {
            echo "Last 5 ran migrations:\n";
            $lastFive = array_slice($ranMigrations, -5);
            foreach ($lastFive as $migration) {
                echo "  - " . $migration->migration . "\n";
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
        $ranMigrationNames = array_column($ranMigrations, 'migration');
        $pendingMigrations = array_diff($migrationFiles, $ranMigrationNames);
        
        echo "Pending migrations: " . count($pendingMigrations) . "\n";
        
        if (count($pendingMigrations) > 0) {
            echo "Pending migration files:\n";
            foreach ($pendingMigrations as $migration) {
                echo "  - " . $migration . "\n";
            }
        }
    } else {
        echo "All migrations are pending (no migrations table)\n";
    }
    
    echo "\n=== Database Connection Test ===\n";
    try {
        $tables = DB::select('SHOW TABLES');
        echo "Database connected successfully. Tables found: " . count($tables) . "\n";
        
        $tableNames = [];
        foreach ($tables as $table) {
            $tableArray = (array) $table;
            $tableNames[] = reset($tableArray);
        }
        
        echo "Existing tables:\n";
        foreach ($tableNames as $tableName) {
            echo "  - $tableName\n";
        }
        
    } catch (Exception $e) {
        echo "Database connection error: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
