<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Vendor;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class ReportsController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        $business = $businessAdmin->business;
        return view('business.reports.index', compact('business'));
    }

    /**
     * Customer Data Report (7.01)
     */
    public function customerReport(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        $business = $businessAdmin->business;
        $query = $business->customers()->with(['bookings']);

        // Date filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $dateTo = Carbon::parse($request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }

        // Customer type filter
        if ($request->filled('customer_type') && $request->customer_type !== 'both') {
            $query->where('customer_type', $request->customer_type);
        }

        // Sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $customers = $query->get();

        // Calculate additional data for each customer
        $customerData = $customers->map(function ($customer) {
            $bookings = $customer->bookings()->where('status', 'completed')->get();
            $totalBookings = $bookings->count();
            $netBill = $bookings->sum('total_amount');
            $lastBooking = $customer->bookings()->where('status', 'completed')->latest('completed_at')->first();

            return [
                'id' => $customer->id,
                'name' => $customer->display_name,
                'type' => $customer->customer_type,
                'location' => $customer->permanent_address,
                'contact_number' => $customer->mobile_number,
                'registered_on' => $customer->created_at->format('d/m/Y'),
                'license_expiry' => $customer->driving_license_expiry_date ? $customer->driving_license_expiry_date->format('d/m/Y') : 'N/A',
                'total_bookings' => $totalBookings,
                'net_bill' => $netBill,
                'last_booking_date' => $lastBooking ? $lastBooking->completed_at->format('d/m/Y') : 'N/A',
            ];
        });

        if ($request->has('export')) {
            return $this->exportCustomerReport($customerData);
        }

        return view('business.reports.customer', compact('customerData'));
    }

    /**
     * Vehicle Data Report (7.02)
     */
    public function vehicleReport(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        $business = $businessAdmin->business;
        $query = $business->vehicles()->with(['bookings']);

        // Date filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $dateTo = Carbon::parse($request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }

        // Ownership filter
        if ($request->filled('ownership_type') && $request->ownership_type !== 'both') {
            $query->where('ownership_type', $request->ownership_type);
        }

        // Sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $vehicles = $query->get();

        // Calculate additional data for each vehicle
        $vehicleData = $vehicles->map(function ($vehicle) {
            $bookings = $vehicle->bookings()->where('status', 'completed')->get();
            $totalRentals = $bookings->count();
            $netRevenue = $bookings->sum('total_amount');
            $lastRental = $vehicle->bookings()->where('status', 'completed')->latest('completed_at')->first();

            return [
                'id' => $vehicle->id,
                'name' => $vehicle->vehicle_make . ' ' . $vehicle->vehicle_model,
                'vehicle_number' => $vehicle->vehicle_number,
                'ownership' => $vehicle->ownership_type === 'owned' ? 'Owned' : 'Vendor Provided',
                'vendor_name' => $vehicle->vendor_name ?? 'N/A',
                'registered_on' => $vehicle->created_at->format('d/m/Y'),
                'insurance_expiry' => $vehicle->insurance_expiry_date ? $vehicle->insurance_expiry_date->format('d/m/Y') : 'N/A',
                'total_rentals' => $totalRentals,
                'total_kilometers' => 'N/A', // This would need to be tracked separately
                'net_revenue' => $netRevenue,
                'last_rental_date' => $lastRental ? $lastRental->completed_at->format('d/m/Y') : 'N/A',
            ];
        });

        if ($request->has('export')) {
            return $this->exportVehicleReport($vehicleData);
        }

        return view('business.reports.vehicle', compact('vehicleData'));
    }

    /**
     * Vendor Data Report (7.03)
     */
    public function vendorReport(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        $business = $businessAdmin->business;
        $query = $business->vendors();

        // Date filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $dateTo = Carbon::parse($request->date_to)->endOfDay();
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }

        // Vendor status filter
        if ($request->filled('vendor_status') && $request->vendor_status !== 'all') {
            // Note: We don't have a status field in vendors table, so we'll skip this for now
        }

        // Sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $vendors = $query->get();

        // Calculate additional data for each vendor
        $vendorData = $vendors->map(function ($vendor) {
            $vendorVehicles = Vehicle::where('business_id', $vendor->business_id)
                ->where('vendor_name', $vendor->vendor_name)
                ->get();
            
            $totalVehicles = $vendorVehicles->count();
            $totalBookings = 0;
            $netRevenue = 0;
            
            foreach ($vendorVehicles as $vehicle) {
                $vehicleBookings = $vehicle->bookings()->where('status', 'completed')->get();
                $totalBookings += $vehicleBookings->count();
                $netRevenue += $vehicleBookings->sum('total_amount');
            }

            // Calculate commission
            $totalCommission = 0;
            if ($vendor->commission_type === 'fixed_amount') {
                $totalCommission = $vendor->commission_rate;
            } elseif ($vendor->commission_type === 'percentage_of_revenue') {
                $totalCommission = $netRevenue * ($vendor->commission_rate / 100);
            }

            return [
                'id' => $vendor->id,
                'name' => $vendor->vendor_name,
                'contact_number' => $vendor->mobile_number,
                'active_status' => 'Active', // We'll assume all are active for now
                'registered_on' => $vendor->created_at->format('d/m/Y'),
                'total_vehicles' => $totalVehicles,
                'total_bookings' => $totalBookings,
                'commission_type' => ucfirst(str_replace('_', ' ', $vendor->commission_type)),
                'net_revenue' => $netRevenue,
                'total_commission' => $totalCommission,
            ];
        });

        if ($request->has('export')) {
            return $this->exportVendorReport($vendorData);
        }

        return view('business.reports.vendor', compact('vendorData'));
    }

    /**
     * Booking Data Report (7.04)
     */
    public function bookingReport(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        $business = $businessAdmin->business;
        $query = $business->bookings()->with(['customer', 'vehicle']);

        // Date filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $dateTo = Carbon::parse($request->date_to)->endOfDay();
            $query->whereBetween('start_date_time', [$dateFrom, $dateTo]);
        }

        // Group by date and calculate summary data
        $bookings = $query->get();
        
        $bookingData = $bookings->groupBy(function ($booking) {
            return $booking->start_date_time->format('Y-m-d');
        })->map(function ($dayBookings, $date) {
            $completedBookings = $dayBookings->where('status', 'completed');
            $cancelledBookings = $dayBookings->where('status', 'cancelled');
            $uniqueCustomers = $dayBookings->pluck('customer_id')->unique()->count();
            
            // Calculate returning customers (customers who have more than one booking on this day)
            $customerCounts = $dayBookings->pluck('customer_id')->countBy();
            $returningCustomers = $customerCounts->filter(function ($count) {
                return $count > 1;
            })->count();
            
            $totalRevenue = $completedBookings->sum('total_amount');
            $averageBookingValue = $completedBookings->count() > 0 ? $totalRevenue / $completedBookings->count() : 0;
            $cancellationRate = $dayBookings->count() > 0 ? ($cancelledBookings->count() / $dayBookings->count()) * 100 : 0;

            return [
                'date' => Carbon::parse($date)->format('d/m/Y'),
                'completed_bookings' => $completedBookings->count(),
                'vehicles_booked' => $dayBookings->pluck('vehicle_id')->unique()->count(),
                'unique_customers' => $uniqueCustomers,
                'returning_customers' => $returningCustomers,
                'cancelled_bookings' => $cancelledBookings->count(),
                'cancellation_rate' => round($cancellationRate, 2),
                'total_revenue' => $totalRevenue,
                'average_booking_value' => round($averageBookingValue, 2),
            ];
        })->sortBy('date');

        // Calculate totals
        $totalCompletedBookings = $bookingData->sum('completed_bookings');
        $totalCancelledBookings = $bookingData->sum('cancelled_bookings');
        $totalRevenue = $bookingData->sum('total_revenue');
        
        $totals = [
            'completed_bookings' => $totalCompletedBookings,
            'vehicles_booked' => $bookingData->sum('vehicles_booked'),
            'unique_customers' => $bookingData->sum('unique_customers'),
            'returning_customers' => $bookingData->sum('returning_customers'),
            'cancelled_bookings' => $totalCancelledBookings,
            'total_revenue' => $totalRevenue,
            'average_booking_value' => $totalCompletedBookings > 0 ? round($totalRevenue / $totalCompletedBookings, 2) : 0,
        ];

        $overallCancellationRate = $totals['completed_bookings'] + $totals['cancelled_bookings'] > 0 
            ? round(($totals['cancelled_bookings'] / ($totals['completed_bookings'] + $totals['cancelled_bookings'])) * 100, 2) 
            : 0;
        $totals['cancellation_rate'] = $overallCancellationRate;

        if ($request->has('export')) {
            return $this->exportBookingReport($bookingData, $totals);
        }

        return view('business.reports.booking', compact('bookingData', 'totals'));
    }

    /**
     * Export Customer Report
     */
    private function exportCustomerReport($data)
    {
        $filename = 'customer_report_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'S No', 'Customer Name', 'Type', 'Location', 'Contact Number', 
                'Registered On', 'License Expiry', 'Total Bookings', 'Net Bill', 'Last Booking Date'
            ]);

            // Data
            foreach ($data as $index => $customer) {
                fputcsv($file, [
                    $index + 1,
                    $customer['name'],
                    ucfirst($customer['type']),
                    $customer['location'],
                    $customer['contact_number'],
                    $customer['registered_on'],
                    $customer['license_expiry'],
                    $customer['total_bookings'],
                    '₹' . number_format($customer['net_bill'], 2),
                    $customer['last_booking_date'],
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export Vehicle Report
     */
    private function exportVehicleReport($data)
    {
        $filename = 'vehicle_report_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'S No', 'Vehicle Name', 'Vehicle Number', 'Owned/Vendor', 'Registered On', 
                'Insurance Expiry', 'Total Rentals', 'Total Kilometers', 'Net Revenue', 'Last Rental Date'
            ]);

            // Data
            foreach ($data as $index => $vehicle) {
                fputcsv($file, [
                    $index + 1,
                    $vehicle['name'],
                    $vehicle['vehicle_number'],
                    $vehicle['ownership'],
                    $vehicle['registered_on'],
                    $vehicle['insurance_expiry'],
                    $vehicle['total_rentals'],
                    $vehicle['total_kilometers'],
                    '₹' . number_format($vehicle['net_revenue'], 2),
                    $vehicle['last_rental_date'],
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export Vendor Report
     */
    private function exportVendorReport($data)
    {
        $filename = 'vendor_report_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'S No', 'Vendor Name', 'Contact Number', 'Active Status', 'Registered On', 
                'Total Vehicles', 'Total Bookings', 'Commission Type', 'Net Revenue', 'Total Commission'
            ]);

            // Data
            foreach ($data as $index => $vendor) {
                fputcsv($file, [
                    $index + 1,
                    $vendor['name'],
                    $vendor['contact_number'],
                    $vendor['active_status'],
                    $vendor['registered_on'],
                    $vendor['total_vehicles'],
                    $vendor['total_bookings'],
                    $vendor['commission_type'],
                    '₹' . number_format($vendor['net_revenue'], 2),
                    '₹' . number_format($vendor['total_commission'], 2),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export Booking Report
     */
    private function exportBookingReport($data, $totals)
    {
        $filename = 'booking_report_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data, $totals) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'S No', 'Date', 'Completed Bookings', 'Vehicles Booked', 'Unique Customers', 
                'Returning Customers', 'Cancelled Bookings', 'Cancellation Rate', 'Total Revenue', 'Average Booking Value'
            ]);

            // Data
            $index = 1;
            foreach ($data as $booking) {
                fputcsv($file, [
                    $index,
                    $booking['date'],
                    $booking['completed_bookings'],
                    $booking['vehicles_booked'],
                    $booking['unique_customers'],
                    $booking['returning_customers'],
                    $booking['cancelled_bookings'],
                    $booking['cancellation_rate'] . '%',
                    '₹' . number_format($booking['total_revenue'], 2),
                    '₹' . number_format($booking['average_booking_value'], 2),
                ]);
                $index++;
            }

            // Totals row
            fputcsv($file, [
                'Total',
                '',
                $totals['completed_bookings'],
                $totals['vehicles_booked'],
                $totals['unique_customers'],
                $totals['returning_customers'],
                $totals['cancelled_bookings'],
                $totals['cancellation_rate'] . '%',
                '₹' . number_format($totals['total_revenue'], 2),
                '₹' . number_format($totals['average_booking_value'], 2),
            ]);

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}