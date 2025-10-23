<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CacheController extends Controller
{
    /**
     * Clear all application caches
     */
    public function clearAllCache(Request $request)
    {
        try {
            // Clear Laravel application caches
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('optimize:clear');
            
            // Clear compiled views
            $compiledViewsPath = storage_path('framework/views');
            if (File::exists($compiledViewsPath)) {
                File::cleanDirectory($compiledViewsPath);
            }
            
            // Clear application cache
            Cache::flush();
            
            // Clear session cache (if using file/database sessions)
            if (config('session.driver') === 'file') {
                $sessionPath = storage_path('framework/sessions');
                if (File::exists($sessionPath)) {
                    $files = File::files($sessionPath);
                    foreach ($files as $file) {
                        if ($file->getExtension() === 'php') {
                            File::delete($file->getPathname());
                        }
                    }
                }
            }
            
            // Clear temporary files
            $tempPath = storage_path('app/temp');
            if (File::exists($tempPath)) {
                File::cleanDirectory($tempPath);
            }
            
            // Log the cache clearing action
            Log::info('Super Admin cleared all application caches', [
                'admin_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'All caches cleared successfully!',
                'cleared_caches' => [
                    'Application Cache',
                    'Configuration Cache',
                    'Route Cache',
                    'View Cache',
                    'Compiled Views',
                    'Session Cache',
                    'Temporary Files'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to clear application caches', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'admin_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear caches: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get cache status information
     */
    public function getCacheStatus()
    {
        try {
            $status = [
                'application_cache' => Cache::getStore() ? 'Active' : 'Inactive',
                'config_cached' => File::exists(base_path('bootstrap/cache/config.php')),
                'routes_cached' => File::exists(base_path('bootstrap/cache/routes-v7.php')),
                'views_cached' => File::exists(base_path('bootstrap/cache/packages.php')),
                'compiled_views_count' => count(File::files(storage_path('framework/views'))),
                'session_driver' => config('session.driver'),
                'cache_driver' => config('cache.default'),
                'last_cleared' => Cache::get('last_cache_clear', 'Never')
            ];
            
            return response()->json([
                'success' => true,
                'status' => $status
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get cache status: ' . $e->getMessage()
            ], 500);
        }
    }
}
