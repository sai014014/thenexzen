<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VendorController extends Controller
{
    /**
     * Search vendors for AJAX requests.
     */
    public function search(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.'
            ], 401);
        }
        
        $business = $businessAdmin->business;
        $search = $request->get('search', '');
        
        $query = $business->vendors();
        
        // If search term is provided, filter results
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('vendor_name', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%")
                  ->orWhere('gstin', 'like', "%{$search}%")
                  ->orWhere('pan_number', 'like', "%{$search}%");
            });
        }
        
        $vendors = $query->select('id', 'vendor_name', 'mobile_number', 'vendor_type', 'gstin', 'pan_number', 'commission_type', 'commission_rate')
            ->orderBy('vendor_name', 'asc')
            ->limit(20)
            ->get();
        
        return response()->json([
            'success' => true,
            'vendors' => $vendors
        ]);
    }

    /**
     * Display a listing of vendors.
     */
    public function index(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;
        $query = $business->vendors();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('vendor_name', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%")
                  ->orWhere('gstin', 'like', "%{$search}%")
                  ->orWhere('pan_number', 'like', "%{$search}%");
            });
        }

        // Filter by vendor type
        if ($request->filled('vendor_type')) {
            $query->where('vendor_type', $request->vendor_type);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedSortFields = ['vendor_name', 'vendor_type', 'mobile_number', 'email_address', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $vendors = $query->paginate(15)->withQueryString();

        return view('business.vendors.index', compact('vendors', 'business', 'businessAdmin'));
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }

        $business = $businessAdmin->business;

        return view('business.vendors.create', compact('business', 'businessAdmin'));
    }

    /**
     * Store a newly created vendor.
     */
    public function store(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required. Please log in again.'
                ], 401);
            }
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        try {
            $rules = $this->getValidationRules();
            $validatedData = $request->validate($rules);

            // Handle file uploads
            $filePaths = $this->handleFileUploads($request);
            $validatedData = array_merge($validatedData, $filePaths);

            // Handle additional branches
            if ($request->filled('additional_branches')) {
                $validatedData['additional_branches'] = $this->processAdditionalBranches($request->additional_branches);
            }

            // Handle additional certificates
            if ($request->hasFile('additional_certificates')) {
                $validatedData['additional_certificates'] = $this->processAdditionalCertificates($request->file('additional_certificates'));
            }

            $validatedData['business_id'] = $business->id;
            $vendor = Vendor::create($validatedData);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vendor registered successfully!',
                    'vendor' => [
                        'id' => $vendor->id,
                        'vendor_name' => $vendor->vendor_name,
                        'mobile_number' => $vendor->mobile_number,
                        'vendor_type' => $vendor->vendor_type,
                        'gstin' => $vendor->gstin,
                        'pan_number' => $vendor->pan_number,
                        'commission_type' => $vendor->commission_type,
                        'commission_rate' => $vendor->commission_rate
                    ],
                    'redirect_url' => route('business.vendors.index')
                ]);
            }

            return redirect()->route('business.vendors.index')
                ->with('success', 'Vendor registered successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while registering the vendor: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'An error occurred while registering the vendor. Please try again.')
                ->withInput();
        }
    }

    /**
     * Quick store a vendor with minimal fields for the vehicle quick-add flow.
     */
    public function quickStore(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.'
            ], 401);
        }
        
        $business = $businessAdmin->business;
        
        try {
            $validated = $request->validate([
                'vendor_name' => 'required|string|max:255',
                'mobile_number' => 'required|string|max:15', // quick-add: allow existing numbers
                'office_address' => 'required|string',
                'commission_type' => 'required|in:fixed_amount,percentage_of_revenue,per_booking_per_day,lease_to_rent',
                'commission_rate' => 'required|numeric|min:0',
            ]);
            
            // Defaults for required columns not provided by quick-add
            $defaults = [
                'vendor_type' => 'vehicle_provider',
                'primary_contact_person' => $validated['vendor_name'],
                'email_address' => $request->input('email_address') ?: ('vendor+' . now()->timestamp . '@temp.local'),
                'pan_number' => $request->input('pan_number') ?: ('TEMP' . strtoupper(substr(md5(uniqid('', true)), 0, 6))),
                'payout_method' => 'other',
                'other_payout_method' => 'Quick Add',
                'status' => 'active',
            ];
            
            $data = array_merge($validated, $defaults);
            $data['business_id'] = $business->id;
            
            $vendor = Vendor::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Vendor added successfully',
                'vendor' => [
                    'id' => $vendor->id,
                    'vendor_name' => $vendor->vendor_name,
                    'commission_type' => $vendor->commission_type,
                    'commission_rate' => $vendor->commission_rate,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add vendor: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified vendor.
     */
    public function show(Vendor $vendor)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the vendor belongs to this business
        if ($vendor->business_id !== $business->id) {
            abort(403, 'Unauthorized access to vendor.');
        }

        // Get vehicles associated with this vendor (by name), without excluding by ownership_type
        $vehicles = $business->vehicles()
            ->where('vendor_name', $vendor->vendor_name)
            ->orderByRaw("CASE WHEN ownership_type='vendor_provided' THEN 0 ELSE 1 END")
            ->orderBy('vehicle_make')
            ->orderBy('vehicle_model')
            ->get();

        // Calculate monthly earnings history (last 12 months)
        $monthlyEarnings = $this->calculateMonthlyEarnings($vehicles, $vendor);
        
        // Reverse array to show current/latest month first
        $monthlyEarnings = array_reverse($monthlyEarnings);
        
        // Calculate current month earnings
        $currentMonth = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        $currentMonthEarnings = $this->calculateMonthEarnings($vehicles, $currentMonth, $currentMonthEnd);

        return view('business.vendors.show', compact(
            'vendor', 
            'business', 
            'businessAdmin', 
            'vehicles',
            'monthlyEarnings',
            'currentMonthEarnings'
        ));
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit(Vendor $vendor)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the vendor belongs to this business
        if ($vendor->business_id !== $business->id) {
            abort(403, 'Unauthorized access to vendor.');
        }

        return view('business.vendors.edit', compact('vendor', 'business', 'businessAdmin'));
    }

    /**
     * Update the specified vendor.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required. Please log in again.'
                ], 401);
            }
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the vendor belongs to this business
        if ($vendor->business_id !== $business->id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to vendor.'
                ], 403);
            }
            abort(403, 'Unauthorized access to vendor.');
        }

        try {
            $rules = $this->getValidationRules($vendor->id);
            $validatedData = $request->validate($rules);

            // Handle file uploads
            $filePaths = $this->handleFileUploads($request, $vendor);
            $validatedData = array_merge($validatedData, $filePaths);

            // Handle additional branches
            if ($request->filled('additional_branches')) {
                $validatedData['additional_branches'] = $this->processAdditionalBranches($request->additional_branches);
            }

            // Handle additional certificates
            if ($request->hasFile('additional_certificates')) {
                $validatedData['additional_certificates'] = $this->processAdditionalCertificates($request->file('additional_certificates'));
            }

            $vendor->update($validatedData);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vendor updated successfully!',
                    'redirect_url' => route('business.vendors.show', $vendor)
                ]);
            }

            return redirect()->route('business.vendors.show', $vendor)
                ->with('success', 'Vendor updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the vendor: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'An error occurred while updating the vendor. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified vendor.
     */
    public function destroy(Vendor $vendor)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the vendor belongs to this business
        if ($vendor->business_id !== $business->id) {
            abort(403, 'Unauthorized access to vendor.');
        }

        // Delete associated files
        $this->deleteVendorFiles($vendor);

        $vendor->delete();

        return redirect()->route('business.vendors.index')
            ->with('success', 'Vendor deleted successfully!');
    }

    /**
     * View vendor document inline (PDF/Image) in the browser
     */
    public function viewDocument(Vendor $vendor, $type)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the vendor belongs to this business
        if ($vendor->business_id !== $business->id) {
            abort(403, 'Unauthorized access to vendor.');
        }

        $documentPath = $vendor->getDocumentPath($type);
        
        if (!$documentPath || !file_exists($documentPath)) {
            abort(404, 'Document not found.');
        }

        $mime = mime_content_type($documentPath);

        // Display inline in new tab
        return response()->file($documentPath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($documentPath) . '"'
        ]);
    }

    /**
     * Download vendor document (uses original filename)
     */
    public function downloadDocument(Vendor $vendor, $type)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the vendor belongs to this business
        if ($vendor->business_id !== $business->id) {
            abort(403, 'Unauthorized access to vendor.');
        }

        // Get the relative path from vendor model
        $relativePath = match($type) {
            'vendor_agreement' => $vendor->vendor_agreement_path,
            'gstin_certificate' => $vendor->gstin_certificate_path,
            'pan_card' => $vendor->pan_card_path,
            default => null
        };
        
        if (!$relativePath || !Storage::disk('public')->exists($relativePath)) {
            abort(404, 'Document not found.');
        }

        // Use original filename from path instead of generic name
        $filename = basename($relativePath);
        
        return Storage::disk('public')->download($relativePath, $filename);
    }

    /**
     * Get validation rules for vendor
     */
    private function getValidationRules($vendorId = null)
    {
        return [
            'vendor_name' => 'required|string|max:255',
            'vendor_type' => 'required|in:vehicle_provider,service_partner,other',
            'gstin' => 'nullable|string|size:15|unique:vendors,gstin,' . $vendorId,
            'pan_number' => 'nullable|string|size:10|unique:vendors,pan_number,' . $vendorId,
            'primary_contact_person' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:15|unique:vendors,mobile_number,' . $vendorId,
            'alternate_contact_number' => 'nullable|string|max:15',
            'email_address' => 'required|email|max:255|unique:vendors,email_address,' . $vendorId,
            'office_address' => 'required|string',
            'payout_method' => 'required|in:bank_transfer,upi_payment,cheque,other',
            'other_payout_method' => 'required_if:payout_method,other|nullable|string|max:255',
            'bank_name' => 'required_if:payout_method,bank_transfer|nullable|string|max:255',
            'account_holder_name' => 'required_if:payout_method,bank_transfer|nullable|string|max:255',
            'account_number' => 'required_if:payout_method,bank_transfer|nullable|string|max:50',
            'ifsc_code' => 'required_if:payout_method,bank_transfer|nullable|string|size:11',
            'bank_branch_name' => 'nullable|string|max:255',
            'upi_id' => 'required_if:payout_method,upi_payment|nullable|string|max:255',
            'payout_frequency' => 'required|in:weekly,bi_weekly,monthly,quarterly,after_every_booking',
            'payout_day_of_week' => 'required_if:payout_frequency,weekly,bi_weekly|nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'payout_day_of_month' => 'required_if:payout_frequency,monthly,quarterly|nullable|integer|min:1|max:31',
            'payout_terms' => 'nullable|string',
            'commission_type' => 'required|in:fixed_amount,percentage_of_revenue,per_booking_per_day,lease_to_rent',
            'commission_rate' => 'required|numeric|min:0',
            'vendor_agreement' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'gstin_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'pan_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'additional_certificates.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }

    /**
     * Handle file uploads for vendor
     */
    private function handleFileUploads(Request $request, Vendor $vendor = null)
    {
        $filePaths = [];

        $fileFields = [
            'vendor_agreement' => 'vendor_agreement_path',
            'gstin_certificate' => 'gstin_certificate_path',
            'pan_card' => 'pan_card_path'
        ];

        foreach ($fileFields as $requestField => $dbField) {
            if ($request->hasFile($requestField)) {
                // Delete old file if exists
                if ($vendor && $vendor->$dbField) {
                    Storage::disk('public')->delete($vendor->$dbField);
                }

                $file = $request->file($requestField);
                $filename = time() . '_' . $requestField . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('vendor_documents', $filename, 'public');
                $filePaths[$dbField] = $path;
            }
        }

        return $filePaths;
    }

    /**
     * Process additional branches
     */
    private function processAdditionalBranches($branches)
    {
        if (is_string($branches)) {
            $branches = json_decode($branches, true);
        }
        
        return is_array($branches) ? $branches : [];
    }

    /**
     * Process additional certificates
     */
    private function processAdditionalCertificates($files)
    {
        $paths = [];
        
        foreach ($files as $file) {
            $filename = time() . '_additional_cert_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('vendor_documents', $filename, 'public');
            $paths[] = $path;
        }
        
        return $paths;
    }

    /**
     * Delete vendor files
     */
    private function deleteVendorFiles(Vendor $vendor)
    {
        $fileFields = [
            'vendor_agreement_path',
            'gstin_certificate_path',
            'pan_card_path'
        ];

        foreach ($fileFields as $field) {
            if ($vendor->$field) {
                Storage::disk('public')->delete($vendor->$field);
            }
        }

        if ($vendor->additional_certificates) {
            foreach ($vendor->additional_certificates as $path) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * Get document filename for download
     */
    /**
     * Calculate monthly earnings history for vendor (last 12 months)
     */
    private function calculateMonthlyEarnings(Collection $vehicles, Vendor $vendor): array
    {
        $monthlyData = [];
        $now = Carbon::now();
        
        // Get last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = $now->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $now->copy()->subMonths($i)->endOfMonth();
            
            $monthEarnings = $this->calculateMonthEarnings($vehicles, $monthStart, $monthEnd);
            $monthEarnings['month'] = $monthStart->format('M Y');
            $monthEarnings['month_key'] = $monthStart->format('Y-m');
            
            $monthlyData[] = $monthEarnings;
        }
        
        return $monthlyData;
    }
    
    /**
     * Calculate earnings for a specific month based on vehicle commission settings
     */
    private function calculateMonthEarnings(Collection $vehicles, Carbon $monthStart, Carbon $monthEnd): array
    {
        $totalEarnings = 0;
        $totalBookings = 0;
        $totalBusinessRevenue = 0;
        $bookingDetails = [];
        
        foreach ($vehicles as $vehicle) {
            // Get completed bookings for this month
            $bookings = $vehicle->bookings()
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->whereBetween('completed_at', [$monthStart, $monthEnd])
                ->get();
            
            foreach ($bookings as $booking) {
                $businessRevenue = $booking->total_amount;
                $totalBusinessRevenue += $businessRevenue;
                $totalBookings++;
                
                // Calculate vendor earnings based on VEHICLE-SPECIFIC commission settings
                $vendorEarning = $this->calculateVendorEarning($vehicle, $booking);
                $totalEarnings += $vendorEarning;
                
                $bookingDetails[] = [
                    'booking_id' => $booking->booking_number ?? $booking->id,
                    'vehicle' => "{$vehicle->vehicle_make} {$vehicle->vehicle_model}",
                    'business_revenue' => $businessRevenue,
                    'vendor_earning' => $vendorEarning,
                    'completed_at' => $booking->completed_at->format('d M Y'),
                ];
            }
        }
        
        return [
            'total_earnings' => $totalEarnings,
            'total_bookings' => $totalBookings,
            'total_business_revenue' => $totalBusinessRevenue,
            'net_business_profit' => $totalBusinessRevenue - $totalEarnings,
            'bookings' => $bookingDetails,
        ];
    }
    
    /**
     * Calculate vendor earning for a single booking based on vehicle commission settings
     */
    private function calculateVendorEarning(Vehicle $vehicle, $booking): float
    {
        $commissionType = $vehicle->commission_type ?? 'fixed';
        $commissionRate = $vehicle->commission_rate ?? $vehicle->commission_value ?? 0;
        
        // Normalize commission type
        if ($commissionType === 'fixed_amount') {
            $commissionType = 'fixed';
        } elseif ($commissionType === 'percentage_of_revenue') {
            $commissionType = 'percentage';
        }
        
        $businessRevenue = $booking->total_amount;
        
        switch ($commissionType) {
            case 'fixed':
                // Fixed amount per booking
                return (float)$commissionRate;
                
            case 'percentage':
                // Percentage of booking amount
                return $businessRevenue * ((float)$commissionRate / 100);
                
            case 'per_booking_per_day':
                // Per day commission
                $days = max(1, (int)ceil($booking->start_date_time->diffInHours($booking->end_date_time) / 24));
                return (float)$commissionRate * $days;
                
            case 'lease_to_rent':
                // Lease to rent - fixed monthly amount (if booking in month, pay full month)
                return (float)$commissionRate;
                
            default:
                return 0;
        }
    }

    private function getDocumentFilename(Vendor $vendor, $type)
    {
        $filename = $vendor->vendor_name . '_';
        
        return match($type) {
            'vendor_agreement' => $filename . 'Vendor_Agreement.pdf',
            'gstin_certificate' => $filename . 'GSTIN_Certificate.pdf',
            'pan_card' => $filename . 'PAN_Card.pdf',
            default => $filename . 'Document.pdf'
        };
    }
}