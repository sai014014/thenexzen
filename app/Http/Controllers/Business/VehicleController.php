<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        $query = $business->vehicles()->with(['business', 'bookings' => function($q) {
            $q->whereIn('status', ['ongoing', 'upcoming'])
              ->where('end_date_time', '>=', now());
        }]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('vehicle_make', 'like', "%{$search}%")
                  ->orWhere('vehicle_model', 'like', "%{$search}%")
                  ->orWhere('vehicle_number', 'like', "%{$search}%")
                  ->orWhere('rc_number', 'like', "%{$search}%");
            });
        }

        // Filter by vehicle type
        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->vehicle_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('vehicle_status', $request->status);
        }

        // Filter by availability
        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->where('is_available', true)->where('vehicle_status', 'active');
            } elseif ($request->availability === 'unavailable') {
                $query->where(function($q) {
                    $q->where('is_available', false)
                      ->orWhere('vehicle_status', '!=', 'active');
                });
            }
        }

        $vehicles = $query->latest()->paginate(15);
        
        // Get subscription information for capacity display
        $subscription = $business->subscriptions()->whereIn('status', ['active', 'trial'])->first();
        $capacityStatus = $subscription ? $subscription->getVehicleCapacityStatus() : null;

        return view('business.vehicles.index', compact('vehicles', 'business', 'subscription', 'capacityStatus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;
        
        // Check vehicle capacity limits
        $subscription = $business->subscriptions()->whereIn('status', ['active', 'trial'])->first();
        if ($subscription) {
            $capacityStatus = $subscription->getVehicleCapacityStatus();
            if (!$capacityStatus['can_add']) {
                return redirect()->route('business.vehicles.index')
                    ->with('error', $capacityStatus['message']);
            }
        }

        return view('business.vehicles.create', compact('business', 'subscription'));
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

        // Check vehicle capacity limits before processing
        $subscription = $business->subscriptions()->whereIn('status', ['active', 'trial'])->first();
        if ($subscription && !$subscription->canAddVehicle()) {
            $capacityStatus = $subscription->getVehicleCapacityStatus();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $capacityStatus['message']
                ], 403);
            }
            return redirect()->route('business.vehicles.index')
                ->with('error', $capacityStatus['message']);
        }

        try {
            $request->validate($this->getValidationRules($request));

            $data = $request->all();
            $data['business_id'] = $business->id;

            // Handle file uploads
            if ($request->hasFile('vehicle_image')) {
                $data['vehicle_image_path'] = $this->uploadDocument($request->file('vehicle_image'), 'vehicle_images');
            }

            if ($request->hasFile('insurance_document')) {
                $data['insurance_document_path'] = $this->uploadDocument($request->file('insurance_document'), 'insurance');
            }

            if ($request->hasFile('rc_document')) {
                $data['rc_document_path'] = $this->uploadDocument($request->file('rc_document'), 'rc');
            }

            // Set transmission type based on vehicle type
            if ($data['vehicle_type'] === 'bike_scooter') {
                $data['transmission_type'] = $data['bike_transmission_type'] ?? null;
            }
            
            // Handle seating capacity for heavy vehicles
            if ($data['vehicle_type'] === 'heavy_vehicle' && isset($data['seating_capacity_heavy'])) {
                $data['seating_capacity'] = $data['seating_capacity_heavy'];
                unset($data['seating_capacity_heavy']);
            }

            $vehicle = Vehicle::create($data);

            // Handle multiple vehicle images
            if ($request->hasFile('vehicle_images')) {
                $this->handleMultipleImageUploads($request->file('vehicle_images'), $vehicle);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vehicle registered successfully!',
                    'redirect_url' => route('business.vehicles.index')
                ]);
            }

            return redirect()->route('business.vehicles.index')
                ->with('success', 'Vehicle registered successfully!');
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
                    'message' => 'An error occurred while registering the vehicle: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        // Ensure the vehicle belongs to this business
        if ($vehicle->business_id !== $business->id) {
            abort(403, 'Unauthorized access to vehicle.');
        }

        // Load vehicle with images
        $vehicle->load('images');

        // Get current and upcoming bookings for this vehicle
        $currentBookings = $vehicle->bookings()
            ->whereIn('status', ['ongoing', 'upcoming'])
            ->where('end_date_time', '>=', now())
            ->orderBy('start_date_time')
            ->get();

        // Get recent completed bookings
        $recentBookings = $vehicle->bookings()
            ->where('status', 'completed')
            ->orderBy('end_date_time', 'desc')
            ->limit(5)
            ->get();

        return view('business.vehicles.show', compact('vehicle', 'business', 'currentBookings', 'recentBookings'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return redirect()->route('business.login')->with('error', 'Please log in to continue.');
        }
        
        $business = $businessAdmin->business;

        // Ensure the vehicle belongs to this business
        if ($vehicle->business_id !== $business->id) {
            abort(403, 'Unauthorized access to vehicle.');
        }

        // Load vehicle with images
        $vehicle->load('images');

        return view('business.vehicles.edit', compact('vehicle', 'business'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
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

        // Ensure the vehicle belongs to this business
        if ($vehicle->business_id !== $business->id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to vehicle.'
                ], 403);
            }
            abort(403, 'Unauthorized access to vehicle.');
        }

        try {
            $request->validate($this->getValidationRules($request, $vehicle->id));

            $data = $request->all();

            // Handle file uploads
            if ($request->hasFile('vehicle_image')) {
                // Delete old file if exists
                if ($vehicle->vehicle_image_path) {
                    Storage::disk('public')->delete($vehicle->vehicle_image_path);
                }
                $data['vehicle_image_path'] = $this->uploadDocument($request->file('vehicle_image'), 'vehicle_images');
            }

            if ($request->hasFile('insurance_document')) {
                // Delete old file if exists
                if ($vehicle->insurance_document_path) {
                    Storage::disk('public')->delete($vehicle->insurance_document_path);
                }
                $data['insurance_document_path'] = $this->uploadDocument($request->file('insurance_document'), 'insurance');
            }

            if ($request->hasFile('rc_document')) {
                // Delete old file if exists
                if ($vehicle->rc_document_path) {
                    Storage::disk('public')->delete($vehicle->rc_document_path);
                }
                $data['rc_document_path'] = $this->uploadDocument($request->file('rc_document'), 'rc');
            }

            // Set transmission type based on vehicle type
            if ($data['vehicle_type'] === 'bike_scooter') {
                $data['transmission_type'] = $data['bike_transmission_type'] ?? null;
            }
            
            // Handle seating capacity for heavy vehicles
            if ($data['vehicle_type'] === 'heavy_vehicle' && isset($data['seating_capacity_heavy'])) {
                $data['seating_capacity'] = $data['seating_capacity_heavy'];
                unset($data['seating_capacity_heavy']);
            }

            $vehicle->update($data);

            // Handle multiple vehicle images
            if ($request->hasFile('vehicle_images')) {
                $this->handleMultipleImageUploads($request->file('vehicle_images'), $vehicle);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vehicle updated successfully!',
                    'redirect_url' => route('business.vehicles.show', $vehicle)
                ]);
            }

            return redirect()->route('business.vehicles.show', $vehicle)
                ->with('success', 'Vehicle updated successfully!');
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
                    'message' => 'An error occurred while updating the vehicle: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        // Ensure the vehicle belongs to this business
        if ($vehicle->business_id !== $business->id) {
            abort(403, 'Unauthorized access to vehicle.');
        }

        // Delete associated files
        if ($vehicle->insurance_document_path) {
            Storage::disk('public')->delete($vehicle->insurance_document_path);
        }
        if ($vehicle->rc_document_path) {
            Storage::disk('public')->delete($vehicle->rc_document_path);
        }

        $vehicle->delete();

        return redirect()->route('business.vehicles.index')
            ->with('success', 'Vehicle deleted successfully!');
    }

    /**
     * Toggle vehicle availability
     */
    public function toggleAvailability(Request $request, Vehicle $vehicle)
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

        // Ensure the vehicle belongs to this business
        if ($vehicle->business_id !== $business->id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to vehicle.'
                ], 403);
            }
            abort(403, 'Unauthorized access to vehicle.');
        }

        try {
            $request->validate([
                'is_available' => 'required|boolean',
                'unavailable_from' => 'nullable|date',
                'unavailable_until' => 'nullable|date|after_or_equal:unavailable_from',
            ]);

            $updateData = [
                'is_available' => $request->is_available,
            ];

            // Only update unavailable dates if vehicle is being marked as unavailable
            if (!$request->is_available) {
                $updateData['unavailable_from'] = $request->unavailable_from;
                $updateData['unavailable_until'] = $request->unavailable_until;
            } else {
                // Clear unavailable dates when marking as available
                $updateData['unavailable_from'] = null;
                $updateData['unavailable_until'] = null;
            }

            $vehicle->update($updateData);

            $status = $request->is_available ? 'available' : 'unavailable';
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Vehicle marked as {$status} successfully!",
                    'is_available' => $vehicle->is_available,
                    'unavailable_from' => $vehicle->unavailable_from,
                    'unavailable_until' => $vehicle->unavailable_until,
                ]);
            }

            return redirect()->back()
                ->with('success', "Vehicle marked as {$status} successfully!");
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
                    'message' => 'An error occurred while updating availability: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Download vehicle document
     */
    public function downloadDocument(Vehicle $vehicle, $type)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        // Ensure the vehicle belongs to this business
        if ($vehicle->business_id !== $business->id) {
            abort(403, 'Unauthorized access to vehicle.');
        }

        $documentPath = $type === 'insurance' ? $vehicle->insurance_document_path : $vehicle->rc_document_path;
        
        if (!$documentPath || !Storage::disk('public')->exists($documentPath)) {
            abort(404, 'Document not found.');
        }

        return Storage::disk('public')->download($documentPath);
    }

    /**
     * Get validation rules based on vehicle type
     */
    private function getValidationRules(Request $request, $vehicleId = null)
    {
        $rules = [
            'vehicle_type' => 'required|in:car,bike_scooter,heavy_vehicle',
            'vehicle_make' => 'required|string|max:100',
            'vehicle_model' => 'required|string|max:100',
            'vehicle_year' => 'required|integer|min:1900|max:' . date('Y'),
            'vehicle_number' => 'required|string|max:20|unique:vehicles,vehicle_number,' . $vehicleId,
            'vehicle_status' => 'required|in:active,inactive,under_maintenance',
            'fuel_type' => 'required|in:petrol,diesel,cng,electric,hybrid',
            'mileage' => 'nullable|numeric|min:0',
            'rental_price_24h' => 'nullable|numeric|min:0',
            'km_limit_per_booking' => 'nullable|integer|min:0',
            'extra_rental_price_per_hour' => 'nullable|numeric|min:0',
            'extra_price_per_km' => 'nullable|numeric|min:0',
            'ownership_type' => 'required|in:owned,leased,vendor_provided',
            'vendor_name' => 'nullable|string|max:100',
            'commission_type' => 'nullable|in:fixed,percentage',
            'commission_value' => 'nullable|numeric|min:0',
            'insurance_provider' => 'required|string|max:100',
            'policy_number' => 'required|string|max:100',
            'insurance_expiry_date' => 'required|date|after_or_equal:today',
            'vehicle_image' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'insurance_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'rc_number' => 'required|string|max:100',
            'rc_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'last_service_date' => 'nullable|date',
            'last_service_meter_reading' => 'nullable|integer|min:0',
            'next_service_due' => 'nullable|date|after_or_equal:last_service_date',
            'next_service_meter_reading' => 'nullable|integer|min:0',
            'remarks_notes' => 'nullable|string|max:1000',
        ];

        // Add type-specific validation
        if ($request->vehicle_type === 'car') {
            $rules['seating_capacity'] = 'required|integer|min:1|max:50';
            $rules['transmission_type'] = 'required|in:manual,automatic';
        } elseif ($request->vehicle_type === 'bike_scooter') {
            $rules['engine_capacity_cc'] = 'required|integer|min:50|max:2000';
            $rules['bike_transmission_type'] = 'required|in:gear,gearless';
        } elseif ($request->vehicle_type === 'heavy_vehicle') {
            $rules['seating_capacity_heavy'] = 'nullable|integer|min:1|max:100';
            $rules['payload_capacity_tons'] = 'nullable|numeric|min:0|max:100';
            $rules['transmission_type'] = 'required|in:manual,automatic';
        }

        return $rules;
    }

    /**
     * Upload document file
     */
    private function uploadDocument($file, $type)
    {
        $filename = $type . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('vehicle_documents', $filename, 'public');
        return $path;
    }

    /**
     * Handle multiple image uploads for vehicles
     */
    private function handleMultipleImageUploads($files, Vehicle $vehicle)
    {
        $sortOrder = $vehicle->images()->max('sort_order') ?? 0;
        $hasPrimary = $vehicle->images()->where('is_primary', true)->exists();

        foreach ($files as $index => $file) {
            if ($file->isValid()) {
                $filename = 'vehicle_images_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('vehicle_documents', $filename, 'public');

                VehicleImage::create([
                    'vehicle_id' => $vehicle->id,
                    'image_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'sort_order' => $sortOrder + $index + 1,
                    'is_primary' => !$hasPrimary && $index === 0, // First image is primary if none exists
                ]);
            }
        }
    }

    /**
     * Delete a vehicle image
     */
    public function deleteImage($vehicleId, $imageId)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        // Find the vehicle
        $vehicle = Vehicle::findOrFail($vehicleId);
        
        // Ensure the vehicle belongs to this business
        if ($vehicle->business_id !== $business->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Find the image
        $image = VehicleImage::findOrFail($imageId);
        
        // Ensure the image belongs to this vehicle
        if ($image->vehicle_id !== $vehicle->id) {
            return response()->json(['success' => false, 'message' => 'Image not found'], 404);
        }

        try {
            // Delete file from storage
            Storage::disk('public')->delete($image->image_path);
            
            // Delete from database
            $image->delete();

            return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete image'], 500);
        }
    }

    /**
     * Set primary image for vehicle
     */
    public function setPrimaryImage($vehicleId, $imageId)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        $business = $businessAdmin->business;

        // Find the vehicle
        $vehicle = Vehicle::findOrFail($vehicleId);
        
        // Ensure the vehicle belongs to this business
        if ($vehicle->business_id !== $business->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Find the image
        $image = VehicleImage::findOrFail($imageId);
        
        // Ensure the image belongs to this vehicle
        if ($image->vehicle_id !== $vehicle->id) {
            return response()->json(['success' => false, 'message' => 'Image not found'], 404);
        }

        try {
            // Remove primary status from all images
            $vehicle->images()->update(['is_primary' => false]);
            
            // Set this image as primary
            $image->update(['is_primary' => true]);

            return response()->json(['success' => true, 'message' => 'Primary image updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update primary image'], 500);
        }
    }
}
