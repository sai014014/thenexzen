<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        // Range handling (default last 7 days)
        $range = $request->query('range', 'last7');
        [$startDate, $endDate, $currentRangeLabel] = $this->resolveRange($range);
        
        // Debug logging for date range
        \Log::info("Date range: {$startDate->format('Y-m-d H:i:s')} to {$endDate->format('Y-m-d H:i:s')} (Range: {$range})");

        // Calculate new dashboard metrics
        
        // 1. Total Revenue: total revenue from all completed bookings within the selected filter range
        $totalRevenue = $business->bookings()
            ->where('status', 'completed')
            ->whereNotNull('completed_at') // Only bookings that have been completed
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->sum('total_amount');
        
        // 2. Completed Bookings: total count of bookings marked as "completed" within the selected range
        $completedBookings = $business->bookings()
            ->where('status', 'completed')
            ->whereNotNull('completed_at') // Only bookings that have been completed
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->count();
        
        // 3. Ongoing Bookings: total count of currently active bookings (not filtered by date range)
        $ongoingBookings = $business->bookings()
            ->where('status', 'ongoing')
            ->count();
        
        // 4. Fleet Utilization: percentage of total vehicles booked or in use within the selected filter range
        $totalVehicles = $business->vehicles()->count();
        
        // Calculate vehicles that had bookings within the selected date range
        $vehiclesWithBookingsInRange = $business->vehicles()
            ->where('vehicle_status', 'active')
            ->whereHas('bookings', function($query) use ($startDate, $endDate) {
                $query->where(function($q) use ($startDate, $endDate) {
                    // Bookings that were active during the selected period
                    $q->where(function($bookingQuery) use ($startDate, $endDate) {
                        // Booking started before or during the period and ended after or during the period
                        $bookingQuery->where('start_date_time', '<=', $endDate)
                                   ->where('end_date_time', '>=', $startDate);
                    })
                    // Or bookings that were completed within the period
                    ->orWhere(function($completedQuery) use ($startDate, $endDate) {
                        $completedQuery->where('status', 'completed')
                                      ->where(function($dateQuery) use ($startDate, $endDate) {
                                          $dateQuery->whereBetween('completed_at', [$startDate, $endDate])
                                                  ->orWhere(function($fallbackQuery) use ($startDate, $endDate) {
                                                      $fallbackQuery->whereNull('completed_at')
                                                                  ->whereBetween('created_at', [$startDate, $endDate]);
                                                  });
                                      });
                    });
                });
            })
            ->count();
        
        $fleetUtilization = $totalVehicles > 0 ? round(($vehiclesWithBookingsInRange / $totalVehicles) * 100, 1) : 0;
        
        // 5. Outstanding Payments: total amount pending/receivable from ongoing and advance bookings
        $outstandingPayments = $business->bookings()
            ->whereIn('status', ['ongoing', 'upcoming'])
            ->where('amount_due', '>', 0) // Only bookings with outstanding amounts
            ->sum('amount_due');

        // Calculate vehicle status counts (LIVE DATA - not filtered by date range)
        $bookedVehicles = $business->vehicles()
            ->where('vehicle_status', 'active')
            ->whereHas('bookings', function($q){
                $q->where('status', 'ongoing')
                  ->where('end_date_time', '>=', Carbon::now());
            })
            ->count();

        $availableVehicles = $business->vehicles()
            ->where('vehicle_status', 'active')
            ->where('is_available', true)
            ->whereDoesntHave('bookings', function($q){
                $q->where('status', 'ongoing')
                  ->where('end_date_time', '>=', Carbon::now());
            })
            ->count();

        $maintenanceVehicles = $business->vehicles()
            ->where('vehicle_status', 'under_maintenance')
            ->count();

        // Calculate changes from yesterday for the new metrics
        $yesterdayStart = Carbon::yesterday('Asia/Kolkata')->startOfDay();
        $yesterdayEnd = Carbon::yesterday('Asia/Kolkata')->endOfDay();
        
        // Calculate yesterday's revenue
        $yesterdayRevenue = $business->bookings()
            ->where('status', 'completed')
            ->whereNotNull('completed_at') // Only bookings that have been completed
            ->whereBetween('completed_at', [$yesterdayStart, $yesterdayEnd])
            ->sum('total_amount');
        
        // Calculate revenue change percentage
        $revenueChange = 0;
        if ($yesterdayRevenue > 0) {
            $revenueChange = round((($totalRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100);
        } elseif ($totalRevenue > 0) {
            $revenueChange = 100; // 100% increase if yesterday was 0 and today has revenue
        }
        
        // Calculate yesterday's completed bookings
        $yesterdayCompletedBookings = $business->bookings()
            ->where('status', 'completed')
            ->whereNotNull('completed_at') // Only bookings that have been completed
            ->whereBetween('completed_at', [$yesterdayStart, $yesterdayEnd])
            ->count();
        
        // Calculate completed bookings change percentage
        $completedBookingsChange = 0;
        if ($yesterdayCompletedBookings > 0) {
            $completedBookingsChange = round((($completedBookings - $yesterdayCompletedBookings) / $yesterdayCompletedBookings) * 100);
        } elseif ($completedBookings > 0) {
            $completedBookingsChange = 100; // 100% increase if yesterday was 0 and today has completed bookings
        }
        
        // Calculate yesterday's outstanding payments (ongoing and upcoming bookings)
        $yesterdayOutstandingPayments = $business->bookings()
            ->whereIn('status', ['ongoing', 'upcoming'])
            ->where('amount_due', '>', 0) // Only bookings with outstanding amounts
            ->where('created_at', '<=', $yesterdayEnd) // Bookings created by end of yesterday
            ->sum('amount_due');
        
        // Calculate outstanding payments change percentage
        $outstandingPaymentsChange = 0;
        if ($yesterdayOutstandingPayments > 0) {
            $outstandingPaymentsChange = round((($outstandingPayments - $yesterdayOutstandingPayments) / $yesterdayOutstandingPayments) * 100);
        } elseif ($outstandingPayments > 0) {
            $outstandingPaymentsChange = 100; // 100% increase if yesterday was 0 and today has outstanding payments
        }

        // Calculate yesterday's ongoing bookings (LIVE DATA - not filtered by date range)
        $yesterdayOngoingBookings = $business->bookings()
            ->where('status', 'ongoing')
            ->where('created_at', '<=', $yesterdayEnd) // Bookings created by end of yesterday
            ->count();
        
        // Calculate ongoing bookings change percentage
        $ongoingBookingsChange = 0;
        if ($yesterdayOngoingBookings > 0) {
            $ongoingBookingsChange = round((($ongoingBookings - $yesterdayOngoingBookings) / $yesterdayOngoingBookings) * 100);
        } elseif ($ongoingBookings > 0) {
            $ongoingBookingsChange = 100; // 100% increase if yesterday was 0 and today has ongoing bookings
        }
        
        // For fleet utilization, set change to 0 as it's complex to calculate day-over-day changes
        $fleetUtilizationChange = 0;

        // Get ongoing bookings sorted by end date & time (LIVE DATA - not filtered by date range)
        $recentBookings = $business->bookings()
            ->with(['vehicle', 'customer'])
            ->where('status', 'ongoing')
            ->orderBy('end_date_time', 'asc')
            ->limit(10)
            ->get();

        // Prepare chart data across selected range
        $chartLabels = [];
        $chartData = [];
        $daysInRange = $startDate->diffInDays($endDate) + 1; // inclusive

        // For single day ranges (today, yesterday), show hourly data
        if ($daysInRange == 1) {
            // Always show 24 hours for single day, even if no data
            for ($hour = 0; $hour < 24; $hour++) {
                $hourStart = $startDate->copy()->addHours($hour);
                $hourEnd = $hourStart->copy()->addHour()->subSecond();
                
                // Ensure we don't go beyond the end date
                if ($hourEnd->gt($endDate)) { $hourEnd = $endDate->copy(); }
                
                // Always show hourly labels (00:00, 01:00, 02:00, etc.)
                $chartLabels[] = $hourStart->format('H:i');
                
                // Sum earnings for this hour - only completed bookings
                $hourEarnings = $business->bookings()
                    ->where('status', 'completed')
                    ->whereNotNull('completed_at') // Only bookings that have been completed
                    ->whereBetween('completed_at', [$hourStart, $hourEnd])
                    ->sum('total_amount');
                
                // Always add data point (0 if no earnings)
                $chartData[] = $hourEarnings;
            }
        } else {
            // For multi-day ranges, chunk into segments (max 30 points)
            $periodDays = min(30, $daysInRange);
            $step = max(1, (int) ceil($daysInRange / $periodDays));

            for ($i = ($periodDays - 1) * $step; $i >= 0; $i -= $step) {
                $segmentStart = $endDate->copy()->subDays($i + ($step - 1))->startOfDay();
                if ($segmentStart->lt($startDate)) { $segmentStart = $startDate->copy()->startOfDay(); }
                $segmentEnd = $endDate->copy()->subDays($i)->endOfDay();
                if ($segmentEnd->gt($endDate)) { $segmentEnd = $endDate->copy()->endOfDay(); }

                // Label by day or by range when chunked
                $chartLabels[] = $step > 1
                    ? $segmentStart->format('d M') . ' - ' . $segmentEnd->format('d M')
                    : $segmentEnd->format('d M');

                // Sum earnings in this chunk - only completed bookings
                $chunkEarnings = $business->bookings()
                    ->where('status', 'completed')
                    ->whereNotNull('completed_at') // Only bookings that have been completed
                    ->whereBetween('completed_at', [$segmentStart, $segmentEnd])
                    ->sum('total_amount');

                $chartData[] = $chunkEarnings;
            }
        }

        return view('business.dashboard', compact(
            'business',
            'businessAdmin',
            'currentRangeLabel',
            'range',
            'totalRevenue',
            'completedBookings',
            'ongoingBookings',
            'fleetUtilization',
            'outstandingPayments',
            'revenueChange',
            'completedBookingsChange',
            'ongoingBookingsChange',
            'fleetUtilizationChange',
            'outstandingPaymentsChange',
            'totalVehicles',
            'availableVehicles',
            'bookedVehicles',
            'maintenanceVehicles',
            'recentBookings',
            'chartLabels',
            'chartData'
        ));
    }

    public function getVehiclesData()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        // Calculate live vehicle status counts (not filtered by date range)
        $bookedVehicles = $business->vehicles()
            ->where('vehicle_status', 'active')
            ->whereHas('bookings', function($q){
                $q->where('status', 'ongoing')
                  ->where('end_date_time', '>=', Carbon::now());
            })
            ->count();

        $availableVehicles = $business->vehicles()
            ->where('vehicle_status', 'active')
            ->where('is_available', true)
            ->whereDoesntHave('bookings', function($q){
                $q->where('status', 'ongoing')
                  ->where('end_date_time', '>=', Carbon::now());
            })
            ->count();

        $maintenanceVehicles = $business->vehicles()
            ->where('vehicle_status', 'under_maintenance')
            ->count();

        return response()->json([
            'status' => 'success',
            'data' => [[
                'available_count' => $availableVehicles,
                'booked_count' => $bookedVehicles,
                'maintenance_count' => $maintenanceVehicles,
            ]]
        ]);
    }

    private function resolveRange(string $range): array
    {
        $now = Carbon::now('Asia/Kolkata');
        switch ($range) {
            case 'today':
                return [$now->copy()->startOfDay(), $now->copy()->endOfDay(), 'Today'];
            case 'yesterday':
                $y = $now->copy()->subDay();
                return [$y->copy()->startOfDay(), $y->copy()->endOfDay(), 'Yesterday'];
            case 'last30':
                return [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay(), 'Last 30 days'];
            case 'this_month':
                return [$now->copy()->startOfMonth(), $now->copy()->endOfDay(), 'This Month'];
            case 'this_year':
                return [$now->copy()->startOfYear(), $now->copy()->endOfDay(), 'This Year'];
            case 'all':
                return [Carbon::create(2000,1,1,0,0,0, 'Asia/Kolkata'), $now->copy()->endOfDay(), 'All Time'];
            case 'last7':
            default:
                return [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay(), 'Last 7 days'];
        }
    }
}