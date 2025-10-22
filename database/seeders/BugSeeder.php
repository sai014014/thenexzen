<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bug;

class BugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bugs = [
            [
                'title' => 'Login page not loading on mobile devices',
                'description' => 'Users are unable to access the login page on mobile devices. The page shows a blank white screen.',
                'status' => 'open',
                'priority' => 'high',
                'type' => 'bug',
                'reported_by' => 'John Smith',
                'assigned_to' => 'Developer Team',
                'steps_to_reproduce' => "1. Open mobile browser\n2. Navigate to login page\n3. Page shows blank white screen",
                'expected_behavior' => 'Login page should load normally with form fields visible',
                'actual_behavior' => 'Page shows blank white screen',
            ],
            [
                'title' => 'Add dark mode toggle to dashboard',
                'description' => 'Users have requested a dark mode option for the dashboard to reduce eye strain during night usage.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'type' => 'feature_request',
                'reported_by' => 'Sarah Johnson',
                'assigned_to' => 'UI/UX Team',
                'steps_to_reproduce' => 'N/A - Feature request',
                'expected_behavior' => 'Dashboard should have a toggle to switch between light and dark themes',
                'actual_behavior' => 'No dark mode option available',
            ],
            [
                'title' => 'Database connection timeout error',
                'description' => 'Application occasionally shows database connection timeout errors during peak hours.',
                'status' => 'testing',
                'priority' => 'critical',
                'type' => 'bug',
                'reported_by' => 'System Admin',
                'assigned_to' => 'Backend Team',
                'steps_to_reproduce' => "1. Wait for peak hours (2-4 PM)\n2. Perform multiple database operations\n3. Error occurs randomly",
                'expected_behavior' => 'Database operations should complete successfully',
                'actual_behavior' => 'Connection timeout error appears',
                'resolution_notes' => 'Increased connection pool size and added retry logic. Testing in staging environment.',
            ],
            [
                'title' => 'Improve search functionality in business listings',
                'description' => 'Current search is too slow and doesn\'t return relevant results. Need to implement better search algorithm.',
                'status' => 'resolved',
                'priority' => 'medium',
                'type' => 'improvement',
                'reported_by' => 'Business Admin',
                'assigned_to' => 'Frontend Team',
                'steps_to_reproduce' => "1. Go to business listings\n2. Search for business name\n3. Results are slow and inaccurate",
                'expected_behavior' => 'Search should return relevant results quickly',
                'actual_behavior' => 'Slow search with irrelevant results',
                'resolution_notes' => 'Implemented Elasticsearch for better search performance and relevance. Search now returns results in under 200ms.',
                'resolved_at' => now()->subDays(3),
            ],
            [
                'title' => 'Add email notifications for booking confirmations',
                'description' => 'Users should receive email notifications when their bookings are confirmed or cancelled.',
                'status' => 'closed',
                'priority' => 'low',
                'type' => 'feature_request',
                'reported_by' => 'Customer Support',
                'assigned_to' => 'Backend Team',
                'steps_to_reproduce' => 'N/A - Feature request',
                'expected_behavior' => 'Users should receive email notifications for booking status changes',
                'actual_behavior' => 'No email notifications are sent',
                'resolution_notes' => 'Implemented email notification system with templates for booking confirmations, cancellations, and reminders.',
                'resolved_at' => now()->subDays(7),
            ],
            [
                'title' => 'Fix responsive layout on tablet devices',
                'description' => 'Dashboard layout breaks on tablet devices in landscape mode. Elements overlap and become unusable.',
                'status' => 'open',
                'priority' => 'medium',
                'type' => 'bug',
                'reported_by' => 'QA Team',
                'assigned_to' => 'Frontend Team',
                'steps_to_reproduce' => "1. Open dashboard on tablet\n2. Rotate to landscape mode\n3. Elements overlap and become unusable",
                'expected_behavior' => 'Dashboard should be fully functional in landscape mode on tablets',
                'actual_behavior' => 'Elements overlap and become unusable',
            ],
            [
                'title' => 'Implement user activity logging',
                'description' => 'Need to track user activities for security and audit purposes.',
                'status' => 'in_progress',
                'priority' => 'high',
                'type' => 'task',
                'reported_by' => 'Security Team',
                'assigned_to' => 'Backend Team',
                'steps_to_reproduce' => 'N/A - Development task',
                'expected_behavior' => 'All user activities should be logged with timestamps and IP addresses',
                'actual_behavior' => 'No activity logging implemented',
            ],
            [
                'title' => 'Optimize image loading performance',
                'description' => 'Vehicle images are taking too long to load, affecting user experience.',
                'status' => 'open',
                'priority' => 'medium',
                'type' => 'improvement',
                'reported_by' => 'Performance Team',
                'assigned_to' => 'Frontend Team',
                'steps_to_reproduce' => "1. Go to vehicle listings\n2. Images take 3-5 seconds to load\n3. Users complain about slow loading",
                'expected_behavior' => 'Images should load within 1 second',
                'actual_behavior' => 'Images take 3-5 seconds to load',
            ],
        ];

        foreach ($bugs as $bug) {
            Bug::create($bug);
        }
    }
}