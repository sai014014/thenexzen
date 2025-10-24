<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;
use App\Helpers\ClientIdGenerator;

class UpdateBusinessClientIdsSequential extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'business:update-client-ids-sequential {--force : Force update all businesses}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all businesses with sequential Client IDs (TNZ-AA0, TNZ-AA1, etc.)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sequential Client ID update for businesses...');
        
        $force = $this->option('force');
        
        if ($force) {
            $businesses = Business::orderBy('id')->get();
            $this->warn('Force mode: Updating ALL businesses with sequential Client IDs');
        } else {
            $businesses = Business::where(function($query) {
                $query->whereNull('client_id')
                      ->orWhere('client_id', '')
                      ->orWhere('client_id', 'NULL');
            })->orderBy('id')->get();
        }
        
        if ($businesses->isEmpty()) {
            $this->info('No businesses found that need Client ID updates.');
            return;
        }
        
        $this->info("Found {$businesses->count()} businesses to update.");
        
        $bar = $this->output->createProgressBar($businesses->count());
        $bar->start();
        
        $updated = 0;
        $errors = 0;
        
        // Clear all existing client IDs first if force mode
        if ($force) {
            $this->info("\nClearing existing Client IDs...");
            Business::query()->update(['client_id' => null]);
        }
        
        foreach ($businesses as $business) {
            try {
                $clientId = ClientIdGenerator::generateUnique();
                $business->update(['client_id' => $clientId]);
                $updated++;
                
                $this->line("\nUpdated {$business->business_name} with Client ID: {$clientId}");
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("\nFailed to update {$business->business_name}: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        
        $this->newLine(2);
        $this->info("Sequential Client ID update completed!");
        $this->info("Successfully updated: {$updated} businesses");
        
        if ($errors > 0) {
            $this->warn("Errors encountered: {$errors} businesses");
        }
        
        // Show the sequential order
        $this->newLine();
        $this->info("Sequential Client IDs assigned:");
        $sampleBusinesses = Business::whereNotNull('client_id')
            ->orderBy('client_id')
            ->take(10)
            ->get();
            
        foreach ($sampleBusinesses as $business) {
            $this->line("  {$business->business_name}: {$business->client_id}");
        }
        
        if ($sampleBusinesses->count() > 10) {
            $this->line("  ... and " . ($updated - 10) . " more");
        }
    }
}