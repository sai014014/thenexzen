<?php

namespace App\Helpers;

use App\Models\Booking;
use App\Models\Business;
use Carbon\Carbon;

class BookingIdGenerator
{
    /**
     * Generate a unique Booking ID in format <ClientID>-<YYMMDD>-<XXXX>
     * Where:
     * - <ClientID> = client's ID (TNZ-LLN format)
     * - <YYMMDD> = booking date (e.g., 251024)
     * - <XXXX> = 4-digit sequence per client per day (0001 → 9999)
     */
    public static function generate(Business $business, Carbon $bookingDate = null): string
    {
        $bookingDate = $bookingDate ?? now();
        $clientId = $business->client_id;
        
        if (!$clientId) {
            throw new \Exception('Business does not have a Client ID');
        }
        
        // Format: YYMMDD (e.g., 251024 for 2025-10-24)
        $dateString = $bookingDate->format('ymd');
        
        // Get the next sequence number for this client on this date
        $sequenceNumber = self::getNextSequenceNumber($business->id, $bookingDate);
        
        // Format sequence as 4-digit string (0001-9999)
        $sequenceString = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
        
        return "{$clientId}-{$dateString}-{$sequenceString}";
    }
    
    /**
     * Get the next sequence number for a business on a specific date
     * This ensures each business gets sequential booking numbers starting from 0001 each day
     */
    private static function getNextSequenceNumber(int $businessId, Carbon $date): int
    {
        $dateString = $date->format('ymd'); // YYMMDD format
        
        // Get all existing booking numbers for this business on this date with new format
        $existingBookings = Booking::where('business_id', $businessId)
            ->where('booking_number', 'like', '%-' . $dateString . '-%')
            ->where('booking_number', 'not like', 'BK%') // Exclude old format
            ->pluck('booking_number')
            ->toArray();
        
        // Extract sequence numbers from existing booking IDs
        $sequenceNumbers = [];
        foreach ($existingBookings as $bookingNumber) {
            // Extract the last 4 digits (sequence number)
            if (preg_match('/-(\d{4})$/', $bookingNumber, $matches)) {
                $sequenceNumbers[] = (int) $matches[1];
            }
        }
        
        // If no existing bookings, start with 1
        if (empty($sequenceNumbers)) {
            return 1;
        }
        
        // Find the next available sequence number
        $maxSequence = max($sequenceNumbers);
        return $maxSequence + 1;
    }
    
    /**
     * Validate Booking ID format
     */
    public static function isValid(string $bookingId): bool
    {
        // Pattern: TNZ-LLN-YYMMDD-XXXX
        return preg_match('/^TNZ-[A-Z]{2}[0-9]-\d{6}-\d{4}$/', $bookingId) === 1;
    }
    
    /**
     * Parse a Booking ID to extract components
     */
    public static function parse(string $bookingId): array
    {
        if (!self::isValid($bookingId)) {
            throw new \InvalidArgumentException('Invalid Booking ID format');
        }
        
        $parts = explode('-', $bookingId);
        
        return [
            'client_id' => $parts[0] . '-' . $parts[1], // TNZ-LLN
            'date' => $parts[2], // YYMMDD
            'sequence' => (int) $parts[3], // XXXX
            'full_id' => $bookingId
        ];
    }
    
    /**
     * Get booking date from Booking ID
     */
    public static function getBookingDate(string $bookingId): Carbon
    {
        $parsed = self::parse($bookingId);
        $dateString = $parsed['date']; // YYMMDD
        
        // Convert YYMMDD to Carbon date
        $year = 2000 + (int) substr($dateString, 0, 2);
        $month = (int) substr($dateString, 2, 2);
        $day = (int) substr($dateString, 4, 2);
        
        return Carbon::create($year, $month, $day);
    }
    
    /**
     * Get business from Booking ID
     */
    public static function getBusiness(string $bookingId): ?Business
    {
        $parsed = self::parse($bookingId);
        return Business::where('client_id', $parsed['client_id'])->first();
    }
}
