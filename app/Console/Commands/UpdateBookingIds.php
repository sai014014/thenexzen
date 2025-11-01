<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Business;
use App\Helpers\BookingIdGenerator;
use Carbon\Carbon;

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
                
                // Generate new booking ID using the booking's creation date (created_at)
                // This matches the new behavior where IDs use creation date, not start_date_time
                $bookingDate = $booking->created_at;
                $newBookingId = BookingIdGenerator::generate(
                    $booking->business, 
                    $bookingDate
                );
                
                // Check if the generated ID already exists (excluding current booking)
                $existingBooking = Booking::where('booking_number', $newBookingId)
                    ->where('id', '!=', $booking->id)
                    ->first();
                
                if ($existingBooking) {
                    $this->warn("\nBooking {$booking->id}: Generated ID {$newBookingId} already exists. Trying alternative sequence...");
                    // Try to find next available sequence for this date
                    $dateString = Carbon::parse($bookingDate)->format('ymd');
                    $clientId = $booking->business->client_id;
                    $sequence = self::findNextAvailableSequence($booking->business->id, $clientId, $dateString, $booking->id);
                    $newBookingId = "{$clientId}-{$dateString}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
                }
                
                // Update the booking
                $oldBookingId = $booking->booking_number;
                $booking->update(['booking_number' => $newBookingId]);
                $updated++;
                
                $this->line("\nUpdated booking {$booking->id}: {$oldBookingId} → {$newBookingId}");
                
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
    
    /**
     * Find the next available sequence number for a booking update
     * Excludes the current booking being updated to avoid conflicts
     */
    private function findNextAvailableSequence(int $businessId, string $clientId, string $dateString, int $excludeBookingId): int
    {
        // Build pattern to match bookings for this client on this date
        $pattern = $clientId . '-' . $dateString . '-%';
        
        // Get all existing booking numbers for this business/client on this date
        // Exclude the booking we're currently updating
        $existingBookings = Booking::where('business_id', $businessId)
            ->where('id', '!=', $excludeBookingId) // Exclude current booking
            ->where('booking_number', 'like', $pattern)
            ->where('booking_number', 'not like', 'BK%') // Exclude old format
            ->pluck('booking_number')
            ->toArray();
        
        // Extract sequence numbers from existing booking IDs
        $sequenceNumbers = [];
        foreach ($existingBookings as $bookingNumber) {
            if (preg_match('/-(\d{4})$/', $bookingNumber, $matches)) {
                $sequenceNumbers[] = (int) $matches[1];
            }
        }
        
        // If no existing bookings for this client on this date, start with 0001
        if (empty($sequenceNumbers)) {
            return 1;
        }
        
        // Find the next available sequence number
        $maxSequence = max($sequenceNumbers);
        $nextSequence = $maxSequence + 1;
        
        // Safety check: ensure we don't exceed 9999
        if ($nextSequence > 9999) {
            throw new \Exception("Maximum booking sequence (9999) reached for client {$clientId} on date {$dateString}");
        }
        
        return $nextSequence;
    }
}