<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
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
        
        $allowedSortFields = ['vendor_name', 'created_at', 'gstin', 'pan_number'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $vendors = $query->paginate(15)->withQueryString();

        return view('business.vendors.index', compact('vendors'));
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

        return view('business.vendors.create');
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

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vendor registered successfully!',
                    'redirect_url' => route('business.vendors.show', $vendor)
                ]);
            }

            return redirect()->route('business.vendors.show', $vendor)
                ->with('success', 'Vendor registered successfully!');
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
                    'message' => 'An error occurred while registering the vendor: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'An error occurred while registering the vendor. Please try again.')
                ->withInput();
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

        return view('business.vendors.show', compact('vendor'));
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

        return view('business.vendors.edit', compact('vendor'));
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
     * Download vendor document
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

        $documentPath = $vendor->getDocumentPath($type);
        
        if (!$documentPath || !file_exists($documentPath)) {
            abort(404, 'Document not found.');
        }

        $filename = $this->getDocumentFilename($vendor, $type);
        
        return Storage::disk('public')->download($documentPath, $filename);
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
            'pan_number' => 'required|string|size:10|unique:vendors,pan_number,' . $vendorId,
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
            'commission_type' => 'required|in:fixed_amount,percentage_of_revenue',
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