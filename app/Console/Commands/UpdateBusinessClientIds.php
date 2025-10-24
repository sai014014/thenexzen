<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;
use App\Helpers\ClientIdGenerator;

class UpdateBusinessClientIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'business:update-client-ids {--force : Force update even if some businesses already have client IDs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all businesses with unique Client IDs in TNZ-LLN format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Client ID update for businesses...');
        
        $force = $this->option('force');
        
        // Get businesses without client IDs or all businesses if force is used
        if ($force) {
            $businesses = Business::all();
            $this->warn('Force mode: Updating ALL businesses (including those with existing Client IDs)');
        } else {
            $businesses = Business::where(function($query) {
                $query->whereNull('client_id')
                      ->orWhere('client_id', '')
                      ->orWhere('client_id', 'NULL');
            })->get();
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
        $this->info("Client ID update completed!");
        $this->info("Successfully updated: {$updated} businesses");
        
        if ($errors > 0) {
            $this->warn("Errors encountered: {$errors} businesses");
        }
        
        // Show some examples
        $this->newLine();
        $this->info("Sample Client IDs generated:");
        $sampleBusinesses = Business::whereNotNull('client_id')->take(5)->get();
        foreach ($sampleBusinesses as $business) {
            $this->line("  {$business->business_name}: {$business->client_id}");
        }
    }
}