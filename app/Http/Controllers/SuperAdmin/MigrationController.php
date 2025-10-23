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
            // Get pending migrations
            $pendingMigrations = $this->getPendingMigrations();
            
            // Get ran migrations
            $ranMigrations = $this->getRanMigrations();
            
            // Get migration files
            $migrationFiles = $this->getMigrationFiles();
            
            // Check if migrations table exists
            $migrationsTableExists = Schema::hasTable('migrations');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'migrations_table_exists' => $migrationsTableExists,
                    'total_migration_files' => count($migrationFiles),
                    'ran_migrations' => count($ranMigrations),
                    'pending_migrations' => count($pendingMigrations),
                    'pending_list' => $pendingMigrations,
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
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get migration status: ' . $e->getMessage()
            ], 500);
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
            
            // Run migrations
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            
            // Get pending migrations after running
            $pendingAfter = $this->getPendingMigrations();
            $migrationsRun = count($pendingBefore) - count($pendingAfter);
            
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
                'message' => "Successfully ran {$migrationsRun} migration(s).",
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
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to run migrations: ' . $e->getMessage()
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
}
