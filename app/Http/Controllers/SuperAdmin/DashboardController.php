<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\BusinessAdmin;
use App\Models\SuperAdmin;
use App\Models\User;
use App\Models\Booking;
use App\Models\Bug;
use App\Models\BusinessSubscription;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $dateRange = $request->get('date_range', 'weekly');
        $businessType = $request->get('business_type', 'all');
        $package = $request->get('package', 'all');
        $region = $request->get('region', 'all');

        // Calculate date range
        $dateFilter = $this->getDateFilter($dateRange);
        
        // Business statistics
        $totalBusinesses = Business::count();
        $activeBusinesses = Business::where('status', 'active')->count();
        $inactiveBusinesses = Business::where('status', 'inactive')->count();
        $suspendedBusinesses = Business::where('status', 'suspended')->count();
        
        // Calculate growth rates (mock data for now)
        $businessGrowthRate = 12.5; // This would be calculated from actual data
        
        // Revenue calculations (no revenue system implemented yet)
        $arr = 0; // Annual Recurring Revenue
        $mrr = 0; // Monthly Recurring Revenue
        $arrGrowth = 0; // ARR growth percentage
        $mrrGrowth = 0; // MRR growth percentage
        
        // Business statistics
        $newBusinesses = Business::where('created_at', '>=', $dateFilter['start'])->count();
        $suspensionRate = 2.3; // Suspension rate percentage
        $newBusinessesGrowth = 15.2; // New businesses growth percentage
        
        // Total revenue (no revenue system implemented yet)
        $totalRevenue = 0; // This would be calculated from actual bookings
        $revenueGrowth = 0; // Revenue growth percentage
        
        // Bug tracking statistics
        $totalBugs = Bug::count();
        $openBugs = Bug::open()->count();
        $inProgressBugs = Bug::inProgress()->count();
        $resolvedBugs = Bug::resolved()->count();
        $closedBugs = Bug::closed()->count();
        $criticalBugs = Bug::byPriority('critical')->count();
        $highPriorityBugs = Bug::byPriority('high')->count();
        
        // Calculate bug resolution rate
        $resolvedBugsCount = Bug::resolved()->count() + Bug::closed()->count();
        $bugResolutionRate = $totalBugs > 0 ? round(($resolvedBugsCount / $totalBugs) * 100, 1) : 0;
        
        // Calculate average days to resolve (for resolved bugs)
        $avgDaysToResolve = 0;
        $resolvedBugsWithDate = Bug::whereNotNull('resolved_at')->get();
        if ($resolvedBugsWithDate->count() > 0) {
            $totalDays = $resolvedBugsWithDate->sum(function($bug) {
                return $bug->created_at->diffInDays($bug->resolved_at);
            });
            $avgDaysToResolve = round($totalDays / $resolvedBugsWithDate->count(), 1);
        }
        
        $stats = [
            'total_businesses' => $totalBusinesses,
            'active_businesses' => $activeBusinesses,
            'inactive_businesses' => $inactiveBusinesses,
            'suspended_businesses' => $suspendedBusinesses,
            'business_growth_rate' => $businessGrowthRate,
            'arr' => $arr,
            'arr_growth' => $arrGrowth,
            'mrr' => $mrr,
            'mrr_growth' => $mrrGrowth,
            'new_businesses' => $newBusinesses,
            'new_businesses_growth' => $newBusinessesGrowth,
            'suspension_rate' => $suspensionRate,
            'total_revenue' => $totalRevenue,
            'revenue_growth' => $revenueGrowth,
            // Bug tracking stats
            'total_bugs' => $totalBugs,
            'open_bugs' => $openBugs,
            'in_progress_bugs' => $inProgressBugs,
            'resolved_bugs' => $resolvedBugs,
            'closed_bugs' => $closedBugs,
            'critical_bugs' => $criticalBugs,
            'high_priority_bugs' => $highPriorityBugs,
            'bug_resolution_rate' => $bugResolutionRate,
            'avg_days_to_resolve' => $avgDaysToResolve,
        ];

        // Revenue breakdown by package (empty - no package system implemented yet)
        $revenue_by_package = collect();

        // Revenue breakdown by region (empty - no region tracking implemented yet)
        $revenue_by_region = collect();

        // Recent businesses
        $recent_businesses = Business::latest()->take(5)->get();

        // Recent bookings (if available)
        $recent_bookings = collect(); // This would be populated from actual booking data

        // Pending subscriptions
        $pending_subscriptions = BusinessSubscription::where('status', 'pending')
            ->with(['business', 'subscriptionPackage'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('super-admin.dashboard', compact(
            'stats', 
            'revenue_by_package', 
            'revenue_by_region', 
            'recent_businesses', 
            'recent_bookings',
            'pending_subscriptions'
        ));
    }

    private function getDateFilter($dateRange)
    {
        $now = Carbon::now();
        
        switch ($dateRange) {
            case 'daily':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
            case 'weekly':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
            case 'monthly':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
            case 'quarterly':
                return [
                    'start' => $now->copy()->startOfQuarter(),
                    'end' => $now->copy()->endOfQuarter()
                ];
            default:
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
        }
    }
}
