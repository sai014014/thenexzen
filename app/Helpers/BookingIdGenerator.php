<?php

namespace App\Helpers;

use App\Models\Booking;
use App\Models\Business;
use Carbon\Carbon;

class BookingIdGenerator
{
    /**
     * Generate a unique Booking ID in format <Business/clientID>-<YYMMDD>-<XXXX>
     * Where:
     * - <Business/clientID> = client's ID (e.g., TNZ-AA0)
     * - <YYMMDD> = booking creation date (current date when booking is created, e.g., 251024 for Oct 24, 2025)
     * - <XXXX> = 4-digit sequence per client per day (0001 → 9999)
     * 
     * Example: TNZ-AA0-251024-0001, TNZ-AA0-251024-0002, etc.
     * Maximum: 9999 bookings per business/client per day
     */
    public static function generate(Business $business, Carbon $bookingDate = null): string
    {
        if (!$bookingDate) {
            $bookingDate = now();
        }
        
        $clientId = $business->client_id;
        
        if (!$clientId) {
            throw new \Exception('Business does not have a Client ID. Please ensure the business has a client_id set.');
        }
        
        // Format: YYMMDD (e.g., 251024 for 2025-10-24)
        $dateString = $bookingDate->format('ymd');
        
        // Get the next sequence number for this client on this date
        $sequenceNumber = self::getNextSequenceNumber($business->id, $clientId, $dateString);
        
        // Format sequence as 4-digit string (0001-9999)
        $sequenceString = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
        
        return "{$clientId}-{$dateString}-{$sequenceString}";
    }
    
    /**
     * Get the next sequence number for a business on a specific date
     * This ensures each business gets sequential booking numbers starting from 0001 each day
     * Sequence resets daily per business/client
     */
    private static function getNextSequenceNumber(int $businessId, string $clientId, string $dateString): int
    {
        // Build pattern to match bookings for this client on this date
        // Format: <ClientID>-<YYMMDD>-<XXXX>
        $pattern = $clientId . '-' . $dateString . '-%';
        
        // Get all existing booking numbers for this business/client on this date with new format
        $existingBookings = Booking::where('business_id', $businessId)
            ->where('booking_number', 'like', $pattern)
            ->where('booking_number', 'not like', 'BK%') // Exclude old format (BK...)
            ->pluck('booking_number')
            ->toArray();
        
        // Extract sequence numbers from existing booking IDs
        $sequenceNumbers = [];
        foreach ($existingBookings as $bookingNumber) {
            // Extract the last 4 digits (sequence number)
            // Pattern: <ClientID>-<YYMMDD>-<XXXX>
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
    
    /**
     * Validate Booking ID format
     * Format: <ClientID>-<YYMMDD>-<XXXX>
     * Where ClientID can be any format (e.g., TNZ-AA0, ABC-XY1, etc.)
     */
    public static function isValid(string $bookingId): bool
    {
        // Pattern: <ClientID>-<YYMMDD>-<XXXX>
        // ClientID can contain alphanumeric characters and hyphens
        // YYMMDD = 6 digits
        // XXXX = 4 digits
        return preg_match('/^[A-Z0-9-]+-\d{6}-\d{4}$/i', $bookingId) === 1;
    }
    
    /**
     * Parse a Booking ID to extract components
     * Format: <ClientID>-<YYMMDD>-<XXXX>
     */
    public static function parse(string $bookingId): array
    {
        if (!self::isValid($bookingId)) {
            throw new \InvalidArgumentException('Invalid Booking ID format: ' . $bookingId);
        }
        
        // Split by last two hyphens to separate ClientID, Date, and Sequence
        $parts = explode('-', $bookingId);
        
        // Last part is sequence (4 digits)
        $sequence = array_pop($parts);
        // Second to last is date (6 digits)
        $date = array_pop($parts);
        // Everything else is client_id (may contain hyphens)
        $clientId = implode('-', $parts);
        
        return [
            'client_id' => $clientId,
            'date' => $date, // YYMMDD
            'sequence' => (int) $sequence, // XXXX
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
