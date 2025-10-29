<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanVehicleImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle:images:cleanup {--dry-run : Show what would be deleted without changing data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove non-image rows from vehicle_images and reset primary image per vehicle';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        // Find non-image rows (including null mime_type)
        $badRows = DB::table('vehicle_images')
            ->when(true, function ($q) {
                $q->whereNull('mime_type')
                  ->orWhere('mime_type', 'not like', 'image/%');
            })
            ->get(['id', 'vehicle_id', 'mime_type', 'image_path']);

        $this->info('Found ' . $badRows->count() . ' non-image rows in vehicle_images');
        if ($badRows->count() > 0) {
            $this->table(['id', 'vehicle_id', 'mime_type', 'image_path'], $badRows->toArray());
        }

        if (!$dryRun && $badRows->count() > 0) {
            DB::table('vehicle_images')
                ->whereIn('id', $badRows->pluck('id'))
                ->delete();
            $this->info('Deleted non-image rows');
        }

        // Reset primary: set first image per vehicle as primary, others not
        $vehicles = DB::table('vehicle_images')
            ->select('vehicle_id', DB::raw('MIN(id) as first_id'))
            ->groupBy('vehicle_id')
            ->get();

        if ($vehicles->count() > 0) {
            if (!$dryRun) {
                // Clear all primary flags
                DB::table('vehicle_images')->update(['is_primary' => 0]);
                // Set first image as primary per vehicle
                foreach ($vehicles as $v) {
                    DB::table('vehicle_images')
                        ->where('id', $v->first_id)
                        ->update(['is_primary' => 1]);
                }
            }
            $this->info(($dryRun ? '[Dry-run] ' : '') . 'Primary image reset prepared for ' . $vehicles->count() . ' vehicles');
        }

        $this->info('Done.');
        return Command::SUCCESS;
    }
}


