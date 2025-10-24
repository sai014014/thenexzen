<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class GenerateNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:generate {--cleanup : Clean up old notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate service and insurance renewal notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting notification generation...');
        
        try {
            $notificationService = new NotificationService();
            
            // Generate all notifications
            $notificationService->generateAllNotifications();
            $this->info('✅ Notifications generated successfully');
            
            // Clean up old notifications if requested
            if ($this->option('cleanup')) {
                $deletedCount = $notificationService->cleanupOldNotifications(30);
                $this->info("🗑️ Cleaned up {$deletedCount} old notifications");
            }
            
            $this->info('Notification generation completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
