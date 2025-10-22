<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CorporateDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;
        $query = $business->customers();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%")
                  ->orWhere('contact_person_mobile', 'like', "%{$search}%")
                  ->orWhere('gstin', 'like', "%{$search}%");
            });
        }

        // Filter by customer type
        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by license status
        if ($request->filled('license_status')) {
            switch ($request->license_status) {
                case 'valid':
                    $query->where('license_expiry_date', '>', now()->addDays(30));
                    break;
                case 'near_expiry':
                    $query->where('license_expiry_date', '<=', now()->addDays(30))
                          ->where('license_expiry_date', '>', now());
                    break;
                case 'expired':
                    $query->where('license_expiry_date', '<', now());
                    break;
            }
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        switch ($sortBy) {
            case 'name':
                $query->orderBy('full_name', $sortOrder);
                break;
            case 'registration_date':
                $query->orderBy('created_at', $sortOrder);
                break;
            case 'license_expiry':
                $query->orderBy('license_expiry_date', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        $customers = $query->paginate(15);

        return view('business.customers.index', compact('customers', 'business'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;
        $customerType = $request->get('type', 'individual');

        return view('business.customers.create', compact('business', 'customerType'));
    }

    /**
     * Store a newly created resource in storage.
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
            $customerType = $request->customer_type;
            $rules = $this->getValidationRules($customerType);
            $request->validate($rules);

            $data = $request->all();
            $data['business_id'] = $business->id;

            // Handle file uploads
            if ($request->hasFile('driving_license')) {
                $data['driving_license_path'] = $this->uploadDocument($request->file('driving_license'), 'driving_license');
            }

            // Handle current address
            if ($request->filled('same_as_permanent')) {
                $data['current_address'] = $data['permanent_address'];
                $data['same_as_permanent'] = true;
            }

            $customer = Customer::create($data);

            // Handle corporate drivers
            if ($customerType === 'corporate' && $request->has('drivers')) {
                foreach ($request->drivers as $driverData) {
                    if (!empty($driverData['driver_name']) && !empty($driverData['driving_license_number'])) {
                        $driverData['customer_id'] = $customer->id;
                        
                        if (isset($driverData['driving_license_file']) && $driverData['driving_license_file']) {
                            $driverData['driving_license_path'] = $this->uploadDocument($driverData['driving_license_file'], 'corporate_driving_license');
                        }
                        
                        CorporateDriver::create($driverData);
                    }
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer registered successfully!',
                    'redirect_url' => route('business.customers.show', $customer)
                ]);
            }

            return redirect()->route('business.customers.show', $customer)
                ->with('success', 'Customer registered successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while registering the customer: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the customer belongs to this business
        if ($customer->business_id !== $business->id) {
            abort(403, 'Unauthorized access to customer.');
        }

        // Load corporate drivers if it's a corporate customer
        if ($customer->isCorporate()) {
            $customer->load('corporateDrivers');
        }

        return view('business.customers.show', compact('customer', 'business'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the customer belongs to this business
        if ($customer->business_id !== $business->id) {
            abort(403, 'Unauthorized access to customer.');
        }

        // Load corporate drivers if it's a corporate customer
        if ($customer->isCorporate()) {
            $customer->load('corporateDrivers');
        }

        return view('business.customers.edit', compact('customer', 'business'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
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

        // Ensure the customer belongs to this business
        if ($customer->business_id !== $business->id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to customer.'
                ], 403);
            }
            abort(403, 'Unauthorized access to customer.');
        }

        try {
            $rules = $this->getValidationRules($customer->customer_type, $customer->id);
            $request->validate($rules);

            $data = $request->all();

            // Handle file uploads
            if ($request->hasFile('driving_license')) {
                // Delete old file if exists
                if ($customer->driving_license_path) {
                    Storage::disk('public')->delete($customer->driving_license_path);
                }
                $data['driving_license_path'] = $this->uploadDocument($request->file('driving_license'), 'driving_license');
            }

            // Handle current address
            if ($request->filled('same_as_permanent')) {
                $data['current_address'] = $data['permanent_address'];
                $data['same_as_permanent'] = true;
            }

            $customer->update($data);

            // Handle corporate drivers
            if ($customer->customer_type === 'corporate' && $request->has('drivers')) {
                // Delete existing drivers
                $customer->corporateDrivers()->delete();
                
                // Add new drivers
                foreach ($request->drivers as $driverData) {
                    if (!empty($driverData['driver_name']) && !empty($driverData['driving_license_number'])) {
                        $driverData['customer_id'] = $customer->id;
                        
                        if (isset($driverData['driving_license_file']) && $driverData['driving_license_file']) {
                            $driverData['driving_license_path'] = $this->uploadDocument($driverData['driving_license_file'], 'corporate_driving_license');
                        }
                        
                        CorporateDriver::create($driverData);
                    }
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer updated successfully!',
                    'redirect_url' => route('business.customers.show', $customer)
                ]);
            }

            return redirect()->route('business.customers.show', $customer)
                ->with('success', 'Customer updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the customer: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the customer belongs to this business
        if ($customer->business_id !== $business->id) {
            abort(403, 'Unauthorized access to customer.');
        }

        // Delete associated files
        if ($customer->driving_license_path) {
            Storage::disk('public')->delete($customer->driving_license_path);
        }

        // Delete corporate drivers and their files
        foreach ($customer->corporateDrivers as $driver) {
            if ($driver->driving_license_path) {
                Storage::disk('public')->delete($driver->driving_license_path);
            }
        }

        $customer->delete();

        return redirect()->route('business.customers.index')
            ->with('success', 'Customer deleted successfully!');
    }

    /**
     * Download customer document
     */
    public function downloadDocument(Customer $customer, $type)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the customer belongs to this business
        if ($customer->business_id !== $business->id) {
            abort(403, 'Unauthorized access to customer.');
        }

        $documentPath = null;
        $filename = '';

        switch ($type) {
            case 'driving_license':
                $documentPath = $customer->driving_license_path;
                $filename = 'driving_license_' . $customer->id . '.pdf';
                break;
            default:
                abort(404, 'Document not found.');
        }
        
        if (!$documentPath || !Storage::disk('public')->exists($documentPath)) {
            abort(404, 'Document not found.');
        }

        return Storage::disk('public')->download($documentPath, $filename);
    }

    /**
     * Update customer status
     */
    public function updateStatus(Request $request, Customer $customer)
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

        // Ensure the customer belongs to this business
        if ($customer->business_id !== $business->id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to customer.'
                ], 403);
            }
            abort(403, 'Unauthorized access to customer.');
        }

        try {
            $request->validate([
                'status' => 'required|in:active,inactive,pending'
            ]);

            $customer->update([
                'status' => $request->status
            ]);

            $statusLabels = [
                'active' => 'Active',
                'inactive' => 'Inactive', 
                'pending' => 'Pending'
            ];

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Customer status updated to {$statusLabels[$request->status]} successfully!",
                    'status' => $customer->status,
                    'status_label' => $statusLabels[$request->status]
                ]);
            }

            return redirect()->back()
                ->with('success', "Customer status updated to {$statusLabels[$request->status]} successfully!");
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating status: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Download corporate driver document
     */
    public function downloadDriverDocument(Customer $customer, CorporateDriver $driver, $type)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the customer belongs to this business
        if ($customer->business_id !== $business->id) {
            abort(403, 'Unauthorized access to customer.');
        }

        // Ensure the driver belongs to this customer
        if ($driver->customer_id !== $customer->id) {
            abort(403, 'Unauthorized access to driver.');
        }

        $documentPath = null;
        $filename = '';

        switch ($type) {
            case 'driving_license':
                $documentPath = $driver->driving_license_path;
                $filename = 'driver_license_' . $driver->id . '.pdf';
                break;
            default:
                abort(404, 'Document not found.');
        }
        
        if (!$documentPath || !Storage::disk('public')->exists($documentPath)) {
            abort(404, 'Document not found.');
        }

        return Storage::disk('public')->download($documentPath, $filename);
    }

    /**
     * Get validation rules based on customer type
     */
    private function getValidationRules($customerType, $customerId = null)
    {
        $rules = [
            'customer_type' => 'required|in:individual,corporate',
        ];

        if ($customerType === 'individual') {
            $rules = array_merge($rules, [
                'full_name' => 'required|string|max:255',
                'mobile_number' => 'required|string|max:15|unique:customers,mobile_number,' . $customerId,
                'alternate_contact_number' => 'nullable|string|max:15',
                'email_address' => 'nullable|email|max:255',
                'date_of_birth' => 'required|date|before:today|before:' . now()->subYears(18)->format('Y-m-d'),
                'permanent_address' => 'required|string',
                'current_address' => 'nullable|string',
                'government_id_type' => 'required|in:aadhar_card,passport,pan_card,voter_id',
                'government_id_number' => 'required|string|max:255',
                'driving_license_number' => 'required|string|max:255',
                'driving_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'license_expiry_date' => 'nullable|date|after:today',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_number' => 'nullable|string|max:15',
                'additional_information' => 'nullable|string',
            ]);
        } else {
            $rules = array_merge($rules, [
                'company_name' => 'required|string|max:255',
                'company_type' => 'required|in:private_limited,public_limited,partnership,proprietorship,llp,ngo,other',
                'gstin' => 'nullable|string|size:15|unique:customers,gstin,' . $customerId,
                'company_address' => 'required|string',
                'pan_number' => 'nullable|string|size:10',
                'contact_person_name' => 'required|string|max:255',
                'designation' => 'required|string|max:255',
                'official_email' => 'required|email|max:255',
                'contact_person_mobile' => 'required|string|max:15',
                'contact_person_alternate' => 'nullable|string|max:15',
                'billing_name' => 'required|string|max:255',
                'billing_email' => 'required|email|max:255',
                'billing_address' => 'required|string',
                'preferred_payment_method' => 'required|in:bank_transfer,upi,corporate_credit_card,cheque_payment,cash,card',
                'invoice_frequency' => 'required|in:weekly,monthly',
                'additional_information' => 'nullable|string',
                'drivers.*.driver_name' => 'required_with:drivers|string|max:255',
                'drivers.*.driving_license_number' => 'required_with:drivers|string|max:255',
                'drivers.*.license_expiry_date' => 'required_with:drivers|date|after:today',
                'drivers.*.driving_license_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            ]);
        }

        return $rules;
    }

    /**
     * Upload document file
     */
    private function uploadDocument($file, $type)
    {
        $filename = $type . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('customer_documents', $filename, 'public');
        return $path;
    }
}