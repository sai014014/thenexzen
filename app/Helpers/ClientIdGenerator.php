<?php

namespace App\Helpers;

class ClientIdGenerator
{
    /**
     * Generate a unique Client ID in format TNZ-LLN
     * Where LL = A-Z (2 letters), N = 0-9 (1 digit)
     * Total combinations: 26 * 26 * 10 = 6,760
     * Sequential order: TNZ-AA0, TNZ-AA1, ..., TNZ-AA9, TNZ-AB0, etc.
     */
    public static function generate(): string
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        
        // Generate all possible combinations in sequential order
        $combinations = [];
        for ($i = 0; $i < 26; $i++) {
            for ($j = 0; $j < 26; $j++) {
                for ($k = 0; $k < 10; $k++) {
                    $combinations[] = 'TNZ-' . $letters[$i] . $letters[$j] . $numbers[$k];
                }
            }
        }
        
        // Return the first available ID (sequential order)
        return $combinations[0];
    }
    
    /**
     * Generate a unique Client ID that doesn't exist in the database
     * Returns the next sequential ID that's not already used
     */
    public static function generateUnique(): string
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        
        // Get all existing client IDs
        $existingIds = \App\Models\Business::whereNotNull('client_id')
            ->where('client_id', '!=', '')
            ->pluck('client_id')
            ->toArray();
        
        // Generate all possible combinations in sequential order
        for ($i = 0; $i < 26; $i++) {
            for ($j = 0; $j < 26; $j++) {
                for ($k = 0; $k < 10; $k++) {
                    $clientId = 'TNZ-' . $letters[$i] . $letters[$j] . $numbers[$k];
                    
                    // Check if this client ID is not already used
                    if (!in_array($clientId, $existingIds)) {
                        return $clientId;
                    }
                }
            }
        }
        
        // If we've exhausted all combinations, throw an exception
        throw new \Exception('All 6,760 Client IDs have been used. No more unique IDs available.');
    }
    
    /**
     * Validate Client ID format
     */
    public static function isValid(string $clientId): bool
    {
        return preg_match('/^TNZ-[A-Z]{2}[0-9]$/', $clientId) === 1;
    }
    
    /**
     * Get all possible Client IDs (for reference)
     */
    public static function getAllPossibleIds(): array
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $combinations = [];
        
        for ($i = 0; $i < 26; $i++) {
            for ($j = 0; $j < 26; $j++) {
                for ($k = 0; $k < 10; $k++) {
                    $combinations[] = 'TNZ-' . $letters[$i] . $letters[$j] . $numbers[$k];
                }
            }
        }
        
        return $combinations;
    }
}
