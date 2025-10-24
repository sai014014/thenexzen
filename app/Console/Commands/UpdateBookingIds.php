<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Business;
use App\Helpers\BookingIdGenerator;

class UpdateBookingIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:update-ids {--force : Force update even if bookings already have new format IDs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all existing bookings with new Client ID-based booking ID format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Booking ID update...');
        
        $force = $this->option('force');
        
        // Get bookings to update
        if ($force) {
            $bookings = Booking::with('business')->get();
            $this->warn('Force mode: Updating ALL bookings (including those with new format IDs)');
        } else {
            // Only update bookings with old format (starting with 'BK')
            $bookings = Booking::with('business')
                ->where('booking_number', 'like', 'BK%')
                ->get();
        }
        
        if ($bookings->isEmpty()) {
            $this->info('No bookings found that need ID updates.');
            return;
        }
        
        $this->info("Found {$bookings->count()} bookings to update.");
        
        $bar = $this->output->createProgressBar($bookings->count());
        $bar->start();
        
        $updated = 0;
        $errors = 0;
        $skipped = 0;
        
        foreach ($bookings as $booking) {
            try {
                // Skip if already in new format and not forcing
                if (!$force && BookingIdGenerator::isValid($booking->booking_number)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }
                
                // Skip if no business
                if (!$booking->business) {
                    $this->warn("\nSkipping booking {$booking->id}: No associated business");
                    $skipped++;
                    $bar->advance();
                    continue;
                }
                
                // Skip if business has no client_id
                if (!$booking->business->client_id) {
                    $this->warn("\nSkipping booking {$booking->id}: Business '{$booking->business->business_name}' has no Client ID");
                    $skipped++;
                    $bar->advance();
                    continue;
                }
                
                // Generate new booking ID
                $newBookingId = BookingIdGenerator::generate(
                    $booking->business, 
                    $booking->start_date_time ?? $booking->created_at
                );
                
                // Update the booking
                $booking->update(['booking_number' => $newBookingId]);
                $updated++;
                
                $this->line("\nUpdated booking {$booking->id}: {$booking->booking_number}");
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("\nFailed to update booking {$booking->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        
        $this->newLine(2);
        $this->info("Booking ID update completed!");
        $this->info("Successfully updated: {$updated} bookings");
        $this->info("Skipped: {$skipped} bookings");
        
        if ($errors > 0) {
            $this->warn("Errors encountered: {$errors} bookings");
        }
        
        // Show some examples
        $this->newLine();
        $this->info("Sample updated Booking IDs:");
        $sampleBookings = Booking::whereNotNull('booking_number')
            ->where('booking_number', 'not like', 'BK%')
            ->take(5)
            ->get();
            
        foreach ($sampleBookings as $booking) {
            $this->line("  Booking {$booking->id}: {$booking->booking_number}");
        }
    }
}