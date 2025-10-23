<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MigrationController extends Controller
{
    /**
     * Get migration status
     */
    public function getStatus()
    {
        try {
            // Check if migrations table exists first
            $migrationsTableExists = Schema::hasTable('migrations');
            
            if (!$migrationsTableExists) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'migrations_table_exists' => false,
                        'total_migration_files' => 0,
                        'ran_migrations' => 0,
                        'pending_migrations' => 0,
                        'pending_list' => [],
                        'ran_list' => [],
                        'last_migration' => null,
                        'message' => 'Migrations table does not exist. Run migrations to create it.'
                    ]
                ]);
            }
            
            // Get migration files
            $migrationFiles = $this->getMigrationFiles();
            
            // Get ran migrations using direct database query to avoid Artisan issues
            $ranMigrations = $this->getRanMigrationsDirect();
            
            // Calculate pending migrations
            $pendingMigrations = array_diff($migrationFiles, $ranMigrations);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'migrations_table_exists' => $migrationsTableExists,
                    'total_migration_files' => count($migrationFiles),
                    'ran_migrations' => count($ranMigrations),
                    'pending_migrations' => count($pendingMigrations),
                    'pending_list' => array_values($pendingMigrations),
                    'ran_list' => $ranMigrations,
                    'last_migration' => !empty($ranMigrations) ? end($ranMigrations) : null
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get migration status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_id' => auth()->id()
            ]);
            
            // Try fallback method
            try {
                $migrationFiles = $this->getMigrationFiles();
                return response()->json([
                    'success' => true,
                    'data' => [
                        'migrations_table_exists' => false,
                        'total_migration_files' => count($migrationFiles),
                        'ran_migrations' => 0,
                        'pending_migrations' => count($migrationFiles),
                        'pending_list' => $migrationFiles,
                        'ran_list' => [],
                        'last_migration' => null,
                        'message' => 'Using fallback method. Migrations table may not exist or be accessible.'
                    ]
                ]);
            } catch (\Exception $fallbackError) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get migration status: ' . $e->getMessage(),
                    'fallback_error' => $fallbackError->getMessage()
                ], 500);
            }
        }
    }
    
    /**
     * Run pending migrations
     */
    public function runMigrations(Request $request)
    {
        try {
            // Get pending migrations before running
            $pendingBefore = $this->getPendingMigrations();
            
            if (empty($pendingBefore)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No pending migrations to run.',
                    'data' => [
                        'migrations_run' => 0,
                        'pending_before' => 0,
                        'pending_after' => 0
                    ]
                ]);
            }
            
            // Check if migrations table exists, if not create it
            if (!Schema::hasTable('migrations')) {
                Artisan::call('migrate:install');
            }
            
            // Run migrations with --force flag to avoid interactive prompts
            Artisan::call('migrate', [
                '--force' => true,
                '--pretend' => false
            ]);
            $output = Artisan::output();
            
            // Get pending migrations after running
            $pendingAfter = $this->getPendingMigrations();
            $migrationsRun = count($pendingBefore) - count($pendingAfter);
            
            // If no migrations were actually run but we had pending ones, 
            // it might be because tables already exist but aren't tracked
            if ($migrationsRun === 0 && !empty($pendingBefore)) {
                // Try to mark existing migrations as run without actually running them
                $this->markExistingMigrationsAsRun($pendingBefore);
                $pendingAfter = $this->getPendingMigrations();
                $migrationsRun = count($pendingBefore) - count($pendingAfter);
                
                if ($migrationsRun > 0) {
                    $output .= "\nMarked existing migrations as run without executing them.";
                }
            }
            
            // Log the migration run
            Log::info('Super Admin ran migrations', [
                'admin_id' => auth()->id(),
                'migrations_run' => $migrationsRun,
                'pending_before' => count($pendingBefore),
                'pending_after' => count($pendingAfter),
                'output' => $output,
                'timestamp' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $migrationsRun > 0 ? "Successfully ran {$migrationsRun} migration(s)." : "No new migrations were executed. All migrations are up to date.",
                'data' => [
                    'migrations_run' => $migrationsRun,
                    'pending_before' => count($pendingBefore),
                    'pending_after' => count($pendingAfter),
                    'output' => $output,
                    'ran_migrations' => array_slice($pendingBefore, 0, $migrationsRun)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to run migrations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_id' => auth()->id()
            ]);
            
            // Check if it's a table already exists error
            if (strpos($e->getMessage(), 'already exists') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some tables already exist. Try using "Check Status" first, or use "Reset All Migrations" if you need to start fresh.',
                    'error_details' => $e->getMessage(),
                    'suggestion' => 'Use the migration status check to see which migrations are not tracked properly.'
                ], 500);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to run migrations: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Sync migrations - mark existing tables as migrated without running them
     */
    public function syncMigrations(Request $request)
    {
        try {
            // Get pending migrations
            $pendingMigrations = $this->getPendingMigrations();
            
            if (empty($pendingMigrations)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No pending migrations to sync.',
                    'data' => [
                        'migrations_synced' => 0
                    ]
                ]);
            }
            
            // Check if migrations table exists, if not create it
            if (!Schema::hasTable('migrations')) {
                Artisan::call('migrate:install');
            }
            
            $syncedMigrations = [];
            $currentBatch = DB::table('migrations')->max('batch') ?? 0;
            $newBatch = $currentBatch + 1;
            
            foreach ($pendingMigrations as $migration) {
                // Check if this migration creates a table that already exists
                if ($this->migrationCreatesExistingTable($migration)) {
                    // Mark as run without executing
                    DB::table('migrations')->insert([
                        'migration' => $migration,
                        'batch' => $newBatch
                    ]);
                    $syncedMigrations[] = $migration;
                }
            }
            
            // Log the sync operation
            Log::info('Super Admin synced migrations', [
                'admin_id' => auth()->id(),
                'migrations_synced' => count($syncedMigrations),
                'synced_migrations' => $syncedMigrations,
                'timestamp' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => count($syncedMigrations) > 0 ? 
                    "Successfully synced " . count($syncedMigrations) . " migration(s) that were already applied." : 
                    "No migrations needed syncing. All pending migrations require actual execution.",
                'data' => [
                    'migrations_synced' => count($syncedMigrations),
                    'synced_migrations' => $syncedMigrations,
                    'remaining_pending' => array_diff($pendingMigrations, $syncedMigrations)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to sync migrations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync migrations: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Rollback last migration batch
     */
    public function rollbackMigrations(Request $request)
    {
        try {
            // Get current migration status
            $ranMigrations = $this->getRanMigrations();
            
            if (empty($ranMigrations)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No migrations to rollback.',
                    'data' => [
                        'migrations_rolled_back' => 0
                    ]
                ]);
            }
            
            // Get the last batch
            $lastBatch = DB::table('migrations')->max('batch');
            $lastBatchMigrations = DB::table('migrations')
                ->where('batch', $lastBatch)
                ->pluck('migration')
                ->toArray();
            
            // Rollback migrations
            Artisan::call('migrate:rollback', ['--force' => true]);
            $output = Artisan::output();
            
            // Log the rollback
            Log::info('Super Admin rolled back migrations', [
                'admin_id' => auth()->id(),
                'batch_rolled_back' => $lastBatch,
                'migrations_rolled_back' => $lastBatchMigrations,
                'output' => $output,
                'timestamp' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Successfully rolled back " . count($lastBatchMigrations) . " migration(s) from batch {$lastBatch}.",
                'data' => [
                    'migrations_rolled_back' => count($lastBatchMigrations),
                    'batch_rolled_back' => $lastBatch,
                    'rolled_back_migrations' => $lastBatchMigrations,
                    'output' => $output
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to rollback migrations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to rollback migrations: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Reset all migrations (rollback all and re-run)
     */
    public function resetMigrations(Request $request)
    {
        try {
            // Get current status
            $ranMigrations = $this->getRanMigrations();
            
            if (empty($ranMigrations)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No migrations to reset.',
                    'data' => [
                        'migrations_reset' => 0
                    ]
                ]);
            }
            
            // Reset migrations
            Artisan::call('migrate:reset', ['--force' => true]);
            $resetOutput = Artisan::output();
            
            // Run migrations again
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = Artisan::output();
            
            // Log the reset
            Log::info('Super Admin reset migrations', [
                'admin_id' => auth()->id(),
                'migrations_reset' => count($ranMigrations),
                'reset_output' => $resetOutput,
                'migrate_output' => $migrateOutput,
                'timestamp' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Successfully reset " . count($ranMigrations) . " migration(s) and re-ran them.",
                'data' => [
                    'migrations_reset' => count($ranMigrations),
                    'reset_output' => $resetOutput,
                    'migrate_output' => $migrateOutput
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to reset migrations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset migrations: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get pending migrations
     */
    private function getPendingMigrations()
    {
        try {
            if (!Schema::hasTable('migrations')) {
                return [];
            }
            
            $ranMigrations = DB::table('migrations')->pluck('migration')->toArray();
            $migrationFiles = $this->getMigrationFiles();
            
            return array_diff($migrationFiles, $ranMigrations);
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Get ran migrations
     */
    private function getRanMigrations()
    {
        try {
            if (!Schema::hasTable('migrations')) {
                return [];
            }
            
            return DB::table('migrations')
                ->orderBy('batch')
                ->orderBy('migration')
                ->pluck('migration')
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Get ran migrations using direct database query (fallback method)
     */
    private function getRanMigrationsDirect()
    {
        try {
            if (!Schema::hasTable('migrations')) {
                return [];
            }
            
            $migrations = DB::select('SELECT migration FROM migrations ORDER BY batch, migration');
            return array_column($migrations, 'migration');
        } catch (\Exception $e) {
            Log::warning('Failed to get ran migrations directly', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
    
    /**
     * Get migration files
     */
    private function getMigrationFiles()
    {
        $migrationPath = database_path('migrations');
        $files = File::files($migrationPath);
        
        $migrations = [];
        foreach ($files as $file) {
            $migrations[] = str_replace('.php', '', $file->getFilename());
        }
        
        sort($migrations);
        return $migrations;
    }
    
    /**
     * Mark existing migrations as run without executing them
     */
    private function markExistingMigrationsAsRun($pendingMigrations)
    {
        try {
            if (!Schema::hasTable('migrations')) {
                return;
            }
            
            $currentBatch = DB::table('migrations')->max('batch') ?? 0;
            $newBatch = $currentBatch + 1;
            
            foreach ($pendingMigrations as $migration) {
                // Check if this migration creates a table that already exists
                if ($this->migrationCreatesExistingTable($migration)) {
                    // Mark as run without executing
                    DB::table('migrations')->insert([
                        'migration' => $migration,
                        'batch' => $newBatch
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to mark existing migrations as run', [
                'error' => $e->getMessage(),
                'migrations' => $pendingMigrations
            ]);
        }
    }
    
    /**
     * Check if a migration creates a table that already exists
     */
    private function migrationCreatesExistingTable($migration)
    {
        try {
            // Common table names that might already exist
            $commonTables = [
                'vehicle_makes' => 'create_vehicle_makes_table',
                'vehicle_models' => 'create_vehicle_models_table',
                'users' => 'create_users_table',
                'businesses' => 'create_businesses_table',
                'business_admins' => 'create_business_admins_table',
                'vehicles' => 'create_vehicles_table',
                'bookings' => 'create_bookings_table',
                'customers' => 'create_customers_table',
                'vendors' => 'create_vendors_table',
                'bugs' => 'create_bugs_table',
                'bug_attachments' => 'create_bug_attachments_table',
                'notifications' => 'create_notifications_table'
            ];
            
            foreach ($commonTables as $tableName => $migrationPattern) {
                if (strpos($migration, $migrationPattern) !== false) {
                    return Schema::hasTable($tableName);
                }
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
