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

        // Calculate stats
        $totalVehicles = $business->vehicles()->count();
        $totalCustomers = $business->customers()->count();
        $totalVendors = $business->vendors()->count();
        $totalBookings = $business->bookings()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        // Calculate total earnings from completed bookings
        $totalEarnings = $business->bookings()
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        // Calculate vehicle status counts (dynamic against active bookings)
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

        // Calculate changes from yesterday (simplified - you can implement more complex logic)
        $earningsChange = 0; // Implement your change calculation logic
        $bookingsChange = 0;
        $vendorsChange = 0;
        $vehiclesChange = 0;
        $customersChange = 0;

        // Get ongoing bookings sorted by end date & time (ascending)
        $recentBookings = $business->bookings()
            ->with(['vehicle', 'customer'])
            ->where('status', 'ongoing')
            ->orderBy('end_date_time', 'asc')
            ->limit(10)
            ->get();

        // Prepare chart data across selected range (max 30 points)
        $chartLabels = [];
        $chartData = [];
        $daysInRange = $startDate->diffInDays($endDate) + 1; // inclusive
        $periodDays = min(30, $daysInRange);

        // If range is larger than 30 days, step by interval chunks
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

            // Sum earnings in this chunk
            $chunkEarnings = $business->bookings()
                ->where('status', 'completed')
                ->whereBetween('created_at', [$segmentStart, $segmentEnd])
                ->sum('total_amount');

            $chartData[] = $chunkEarnings;
        }

        return view('business.dashboard', compact(
            'business',
            'businessAdmin',
            'currentRangeLabel',
            'range',
            'totalVehicles',
            'totalCustomers',
            'totalVendors',
            'totalBookings',
            'totalEarnings',
            'availableVehicles',
            'bookedVehicles',
            'maintenanceVehicles',
            'earningsChange',
            'bookingsChange',
            'vendorsChange',
            'vehiclesChange',
            'customersChange',
            'recentBookings',
            'chartLabels',
            'chartData'
        ));
    }

    private function resolveRange(string $range): array
    {
        $now = Carbon::now();
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
                return [Carbon::create(2000,1,1,0,0,0), $now->copy()->endOfDay(), 'All Time'];
            case 'last7':
            default:
                return [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay(), 'Last 7 days'];
        }
    }
}