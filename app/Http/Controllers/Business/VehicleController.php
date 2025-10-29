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
                  ->orWhere('vehicle_number', 'like', "%{$search}%");
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

        $vehicles = $query->latest()->paginate(20);
        
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
            // Server-side fallback: map commission_value -> commission_rate before validation
            if ($request->input('ownership_type') === 'vendor_provided') {
                if ($request->filled('commission_value') && !$request->filled('commission_rate')) {
                    $request->merge(['commission_rate' => $request->input('commission_value')]);
                }
            }

            $request->validate($this->getValidationRules($request));

            $data = $request->all();
            $data['business_id'] = $business->id;

            // Handle file uploads (old method - fallback)
            if ($request->hasFile('vehicle_image')) {
                $data['vehicle_image_path'] = $this->uploadDocument($request->file('vehicle_image'), 'vehicle_images');
            }

            // Handle RC and Insurance documents from live upload
            if ($request->has('rc_document_path') && !empty($request->rc_document_path)) {
                $data['rc_document_path'] = $request->rc_document_path;
            } elseif ($request->hasFile('rc_document')) {
                $data['rc_document_path'] = $this->uploadDocument($request->file('rc_document'), 'rc');
            }

            if ($request->has('insurance_document_path') && !empty($request->insurance_document_path)) {
                $data['insurance_document_path'] = $request->insurance_document_path;
            } elseif ($request->hasFile('insurance_document')) {
                $data['insurance_document_path'] = $this->uploadDocument($request->file('insurance_document'), 'insurance');
            }

            // Set transmission type based on vehicle type
            if ($data['vehicle_type'] === 'bike_scooter') {
                // Accept either transmission_type (new) or bike_transmission_type (legacy)
                $data['transmission_type'] = $data['transmission_type']
                    ?? ($data['bike_transmission_type'] ?? null);
            }
            
            // Handle seating capacity for heavy vehicles
            if ($data['vehicle_type'] === 'heavy_vehicle' && isset($data['seating_capacity_heavy'])) {
                $data['seating_capacity'] = $data['seating_capacity_heavy'];
                unset($data['seating_capacity_heavy']);
            }

            // Map vehicle_number to rc_number for database
            if (isset($data['vehicle_number'])) {
                $data['rc_number'] = $data['vehicle_number'];
            }
            
            // Handle vendor default commission override
            if (($data['ownership_type'] ?? null) === 'vendor_provided' && $request->boolean('use_vendor_default_commission')) {
                $vendorName = $data['vendor_name'] ?? null;
                if ($vendorName) {
                    $vendor = \App\Models\Vendor::where('business_id', $business->id)
                        ->where('vendor_name', $vendorName)
                        ->first();
                    if ($vendor) {
                        // Map vendor commission type to vehicle enum
                        $vendorToVehicleType = [
                            'fixed_amount' => 'fixed',
                            'percentage_of_revenue' => 'percentage',
                            'per_booking_per_day' => 'per_booking_per_day',
                            'lease_to_rent' => 'lease_to_rent',
                            // Also handle already-normalized values
                            'fixed' => 'fixed',
                            'percentage' => 'percentage',
                        ];
                        $data['commission_type'] = $vendorToVehicleType[$vendor->commission_type] ?? $vendor->commission_type;
                        $data['commission_rate'] = $vendor->commission_rate;
                        $data['lease_commitment_months'] = ($data['commission_type'] === 'lease_to_rent')
                            ? ($vendor->lease_commitment_months ?? null)
                            : null;
                    }
                }
            } else {
                // Map commission_type form values to DB values
                if (isset($data['commission_type'])) {
                    $commissionTypeMapping = [
                        'fixed' => 'fixed',
                        'percentage' => 'percentage',
                        'per_booking_per_day' => 'per_booking_per_day',
                        'lease_to_rent' => 'lease_to_rent'
                    ];
                    $data['commission_type'] = $commissionTypeMapping[$data['commission_type']] ?? $data['commission_type'];
                }
            }
            
            // Map commission_value to commission_rate if provided
            if (isset($data['commission_value']) && !isset($data['commission_rate'])) {
                $data['commission_rate'] = $data['commission_value'];
            }
            unset($data['commission_value']); // Remove the old key

            $vehicle = Vehicle::create($data);

            // Handle multiple vehicle images from live upload
            if ($request->has('uploaded_image_ids') && !empty($request->uploaded_image_ids)) {
                $imageIds = explode(',', $request->uploaded_image_ids);
                $imageIds = array_filter($imageIds);
                
                if (!empty($imageIds)) {
                    // Update vehicle_id for uploaded images
                    VehicleImage::whereIn('id', $imageIds)
                        ->update([
                            'vehicle_id' => $vehicle->id,
                            'sort_order' => \DB::raw('id')
                        ]);
                    
                    // Handle primary image selection
                    if ($request->filled('primary_image_id')) {
                        // User selected a primary image
                        $primaryImageId = $request->primary_image_id;
                        // Remove primary status from all images
                        $vehicle->images()->update(['is_primary' => false]);
                        // Set selected image as primary
                        VehicleImage::where('id', $primaryImageId)
                            ->where('vehicle_id', $vehicle->id)
                            ->update(['is_primary' => true]);
                    } else {
                        // Set first image as primary if none exists
                        $hasPrimary = $vehicle->images()->where('is_primary', true)->exists();
                        if (!$hasPrimary && count($imageIds) > 0) {
                            VehicleImage::where('id', $imageIds[0])
                                ->update(['is_primary' => true]);
                        }
                    }
                }
            } elseif ($request->hasFile('vehicle_images')) {
                // Fallback to old method if files are uploaded via form
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
            // Server-side fallback: map commission_value -> commission_rate before validation
            if ($request->input('ownership_type') === 'vendor_provided') {
                if ($request->filled('commission_value') && !$request->filled('commission_rate')) {
                    $request->merge(['commission_rate' => $request->input('commission_value')]);
                }
            }

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

            // Handle RC and Insurance documents from live upload
            // Only update if a new document is uploaded
            if ($request->filled('insurance_document_path') && !empty($request->insurance_document_path)) {
                // Delete old file if exists and path is different
                if ($vehicle->insurance_document_path && $vehicle->insurance_document_path !== $request->insurance_document_path) {
                    Storage::disk('public')->delete($vehicle->insurance_document_path);
                }
                $data['insurance_document_path'] = $request->insurance_document_path;
            } elseif ($request->hasFile('insurance_document')) {
                // Delete old file if exists
                if ($vehicle->insurance_document_path) {
                    Storage::disk('public')->delete($vehicle->insurance_document_path);
                }
                $data['insurance_document_path'] = $this->uploadDocument($request->file('insurance_document'), 'insurance');
            } else {
                // Keep existing document - don't update the path at all
                unset($data['insurance_document_path']);
            }

            // Handle RC documents from live upload
            if ($request->filled('rc_document_path') && !empty($request->rc_document_path)) {
                // Delete old file if exists and path is different
                if ($vehicle->rc_document_path && $vehicle->rc_document_path !== $request->rc_document_path) {
                    Storage::disk('public')->delete($vehicle->rc_document_path);
                }
                $data['rc_document_path'] = $request->rc_document_path;
            } elseif ($request->hasFile('rc_document')) {
                // Delete old file if exists
                if ($vehicle->rc_document_path) {
                    Storage::disk('public')->delete($vehicle->rc_document_path);
                }
                $data['rc_document_path'] = $this->uploadDocument($request->file('rc_document'), 'rc');
            } else {
                // Keep existing document - don't update the path at all
                unset($data['rc_document_path']);
            }

            // Set transmission type based on vehicle type
            if ($data['vehicle_type'] === 'bike_scooter') {
                $data['transmission_type'] = $data['transmission_type']
                    ?? ($data['bike_transmission_type'] ?? null);
            }
            
            // Handle seating capacity for heavy vehicles
            if ($data['vehicle_type'] === 'heavy_vehicle' && isset($data['seating_capacity_heavy'])) {
                $data['seating_capacity'] = $data['seating_capacity_heavy'];
                unset($data['seating_capacity_heavy']);
            }
            
            // Handle vendor default commission override
            if (($data['ownership_type'] ?? null) === 'vendor_provided' && $request->boolean('use_vendor_default_commission')) {
                $vendorName = $data['vendor_name'] ?? null;
                if ($vendorName) {
                    $vendor = \App\Models\Vendor::where('business_id', $business->id)
                        ->where('vendor_name', $vendorName)
                        ->first();
                    if ($vendor) {
                        // Map vendor commission type to vehicle enum
                        $vendorToVehicleType = [
                            'fixed_amount' => 'fixed',
                            'percentage_of_revenue' => 'percentage',
                            'per_booking_per_day' => 'per_booking_per_day',
                            'lease_to_rent' => 'lease_to_rent',
                            // Also handle already-normalized values
                            'fixed' => 'fixed',
                            'percentage' => 'percentage',
                        ];
                        $data['commission_type'] = $vendorToVehicleType[$vendor->commission_type] ?? $vendor->commission_type;
                        $data['commission_rate'] = $vendor->commission_rate;
                        $data['lease_commitment_months'] = ($data['commission_type'] === 'lease_to_rent')
                            ? ($vendor->lease_commitment_months ?? $vehicle->lease_commitment_months)
                            : null;
                    }
                }
            } else {
                // Map commission_type form values to DB values
                if (isset($data['commission_type'])) {
                    $commissionTypeMapping = [
                        'fixed' => 'fixed',
                        'percentage' => 'percentage',
                        'per_booking_per_day' => 'per_booking_per_day',
                        'lease_to_rent' => 'lease_to_rent'
                    ];
                    $data['commission_type'] = $commissionTypeMapping[$data['commission_type']] ?? $data['commission_type'];
                }
            }
            
            // Map commission_value to commission_rate - prioritize commission_value from form
            if (isset($data['commission_value'])) {
                // Check if commission_value is not empty (including '0' as valid)
                $commissionValue = trim($data['commission_value']);
                if ($commissionValue !== '' && $commissionValue !== null) {
                    // Always use commission_value from form as it's what user submitted
                    $data['commission_rate'] = $commissionValue;
                }
            }
            // Always remove commission_value as we use commission_rate in DB
            unset($data['commission_value']);
            
            // Handle lease_commitment_months
            if (isset($data['commission_type']) && $data['commission_type'] !== 'lease_to_rent') {
                // Clear lease commitment if commission type is not lease_to_rent
                $data['lease_commitment_months'] = null;
            } elseif (isset($data['lease_commitment_months'])) {
                // Commission type is lease_to_rent
                $commitmentValue = trim($data['lease_commitment_months']);
                if ($commitmentValue !== '' && $commitmentValue !== null) {
                    // Save the provided value
                    $data['lease_commitment_months'] = (int)$commitmentValue;
                } else {
                    // Empty value - preserve existing commitment if vehicle already has one
                    unset($data['lease_commitment_months']);
                }
            }
            // If lease_commitment_months not in $data and commission_type is lease_to_rent, 
            // existing value will be preserved (not updated)

            $vehicle->update($data);

            // Handle image deletions
            if ($request->has('deleted_image_ids') && !empty($request->deleted_image_ids)) {
                $deletedIds = explode(',', $request->deleted_image_ids);
                foreach ($deletedIds as $imageId) {
                    $image = VehicleImage::find($imageId);
                    if ($image && $image->vehicle_id == $vehicle->id) {
                        if ($image->image_path) {
                            Storage::disk('public')->delete($image->image_path);
                        }
                        $image->delete();
                    }
                }
            }

            // Handle new uploaded images from live upload
            if ($request->has('uploaded_image_ids') && !empty($request->uploaded_image_ids)) {
                $uploadedIds = explode(',', $request->uploaded_image_ids);
                foreach ($uploadedIds as $imageId) {
                    $image = VehicleImage::find($imageId);
                    if ($image && !$image->vehicle_id) {
                        $image->vehicle_id = $vehicle->id;
                        $image->save();
                    }
                }
            }
            
            // Handle primary image selection
            if ($request->filled('primary_image_id')) {
                $primaryImageIdValue = $request->primary_image_id;
                
                // Check if it's an existing image (prefixed with 'existing_')
                if (str_starts_with($primaryImageIdValue, 'existing_')) {
                    $actualImageId = (int) str_replace('existing_', '', $primaryImageIdValue);
                    // Remove primary status from all images
                    $vehicle->images()->update(['is_primary' => false]);
                    // Set existing image as primary
                    VehicleImage::where('id', $actualImageId)
                        ->where('vehicle_id', $vehicle->id)
                        ->update(['is_primary' => true]);
                } else {
                    // New uploaded image
                    $actualImageId = (int) $primaryImageIdValue;
                    // Remove primary status from all images
                    $vehicle->images()->update(['is_primary' => false]);
                    // Set new image as primary
                    VehicleImage::where('id', $actualImageId)
                        ->where('vehicle_id', $vehicle->id)
                        ->update(['is_primary' => true]);
                }
            } else {
                // If no primary selected and none exists, set first image as primary
                $hasPrimary = $vehicle->images()->where('is_primary', true)->exists();
                if (!$hasPrimary) {
                    $firstImage = $vehicle->images()->first();
                    if ($firstImage) {
                        $firstImage->update(['is_primary' => true]);
                    }
                }
            }

            // Handle multiple vehicle images from file input (legacy)
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
        
        // Delete legacy vehicle_image_path if it exists
        if ($vehicle->vehicle_image_path) {
            Storage::disk('public')->delete($vehicle->vehicle_image_path);
        }

        // Delete all vehicle images and their files
        foreach ($vehicle->images as $image) {
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
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
            // Handle new status-based approach
            if ($request->has('status')) {
                $status = $request->status;
                $updateData = ['vehicle_status' => $status];
                
                // Handle inactive with date or manual toggle
                if ($status === 'inactive') {
                    $updateData['is_available'] = false;
                    
                    if ($request->has('inactive_until_manual') && $request->inactive_until_manual) {
                        // Set until manual toggle - leave unavailable_until as null or set to very far future
                        // Don't set unavailable_until, will remain null or current value
                        $updateData['unavailable_until'] = null;
                    } elseif ($request->has('inactive_until_date') && $request->inactive_until_date) {
                        // Set until specific date
                        $updateData['unavailable_until'] = $request->inactive_until_date;
                    }
                } elseif ($status === 'under_maintenance') {
                    // Mark as under maintenance
                    $updateData['is_available'] = false;
                } else {
                    // Active - mark as available
                    $updateData['is_available'] = true;
                    $updateData['unavailable_until'] = null;
                }
            } else {
                // Old method - backward compatibility
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
            }

            $vehicle->update($updateData);
            
            // Log the update for debugging
            \Log::info('Vehicle status updated', [
                'vehicle_id' => $vehicle->id,
                'vehicle_status' => $vehicle->fresh()->vehicle_status,
                'is_available' => $vehicle->fresh()->is_available,
                'unavailable_until' => $vehicle->fresh()->unavailable_until,
                'update_data' => $updateData
            ]);

            $statusMessage = $request->has('status') ? ucfirst(str_replace('_', ' ', $request->status)) : ($updateData['is_available'] ? 'available' : 'unavailable');
            
            // Always return JSON for API requests
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Vehicle status updated to {$statusMessage} successfully!",
                    'vehicle_status' => $vehicle->vehicle_status,
                    'is_available' => $vehicle->is_available,
                    'unavailable_from' => $vehicle->unavailable_from,
                    'unavailable_until' => $vehicle->unavailable_until,
                ]);
            }

            return redirect()->back()
                ->with('success', "Vehicle status updated to {$statusMessage} successfully!");
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
     * View vehicle document inline (PDF/Image) in the browser
     */
    public function viewDocument(Vehicle $vehicle, $type)
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

        $fullPath = Storage::disk('public')->path($documentPath);
        $mime = mime_content_type($fullPath);

        // Display inline
        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"'
        ]);
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
            'vin_number' => 'required|string|max:50|unique:vehicles,vin_number,' . $vehicleId,
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
            'commission_type' => 'nullable|in:fixed,percentage,per_booking_per_day,lease_to_rent',
            'commission_value' => 'nullable|numeric|min:0',
            'lease_commitment_months' => 'nullable|integer|in:3,6,12,24',
            'insurance_provider' => 'required|string|max:100',
            'policy_number' => 'required|string|max:100',
            'insurance_expiry_date' => 'required|date|after_or_equal:today',
            'vehicle_image' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            // Multiple vehicle images (gallery)
            'vehicle_images' => 'nullable|array',
            'vehicle_images.*' => 'image|mimes:jpg,jpeg,png|max:5120',
            // Allow PDF and image formats for insurance and RC documents
            'insurance_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
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
            $rules['transmission_type'] = 'required|in:manual,automatic,hybrid';
        } elseif ($request->vehicle_type === 'bike_scooter') {
            $rules['engine_capacity_cc'] = 'required|integer|min:50|max:2000';
            // Accept transmission_type directly for bikes; keep legacy key for backward compatibility
            $rules['transmission_type'] = 'required|in:gear,gearless';
        } elseif ($request->vehicle_type === 'heavy_vehicle') {
            $rules['seating_capacity_heavy'] = 'nullable|integer|min:1|max:100';
            $rules['payload_capacity_tons'] = 'nullable|numeric|min:0|max:100';
            $rules['transmission_type'] = 'required|in:manual,automatic,hybrid';
        }
        
        // Vendor-provided vehicle commission requirements
        if ($request->ownership_type === 'vendor_provided') {
            if ($request->boolean('use_vendor_default_commission')) {
                // Using vendor defaults; form commission fields not required
                $rules['commission_type'] = 'nullable|in:fixed,percentage,per_booking_per_day,lease_to_rent';
                $rules['commission_rate'] = 'nullable|numeric|min:0';
                $rules['lease_commitment_months'] = 'nullable|integer|in:3,6,12,24';
            } else {
                $rules['commission_type'] = 'required|in:fixed,percentage,per_booking_per_day,lease_to_rent';
                // Cap percentage type at 100
                $rules['commission_rate'] = $request->commission_type === 'percentage'
                    ? 'required|numeric|min:0|max:100'
                    : 'required|numeric|min:0';
                
                // Lease commitment required for lease-to-rent type
                if ($request->commission_type === 'lease_to_rent') {
                    $rules['lease_commitment_months'] = 'required|integer|in:3,6,12,24';
                }
            }
        }

        return $rules;
    }

    /**
     * Upload document file
     */
    private function uploadDocument($file, $type)
    {
        $filename = $type . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        // Store official documents (rc/insurance) under vehicle_documents; images under vehicle_images
        $directory = in_array($type, ['rc', 'insurance']) ? 'vehicle_documents' : 'vehicle_images';
        $path = $file->storeAs($directory, $filename, 'public');
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
                // Defensive check: ensure only images are accepted here
                $mime = $file->getMimeType();
                if (!str_starts_with($mime, 'image/')) {
                    // Skip non-image files (RC/PDF/etc.) from gallery upload
                    continue;
                }
                $filename = 'vehicle_images_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('vehicle_images', $filename, 'public');

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

    /**
     * Upload vehicle image via AJAX (for live upload)
     */
    public function uploadVehicleImage(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.'
            ], 401);
        }

        try {
            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            ]);

            $file = $request->file('image');
            
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file.'
                ], 422);
            }

            $filename = 'vehicle_images_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('vehicle_images', $filename, 'public');

            // Create a temporary vehicle image record (without vehicle_id)
            $vehicleImage = VehicleImage::create([
                'vehicle_id' => null, // Will be linked when vehicle is created
                'image_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'is_primary' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully.',
                'image_id' => $vehicleImage->id,
                'image_path' => $path,
                'preview_url' => asset('storage/' . $path)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload RC or Insurance document via AJAX (for live upload)
     */
    public function uploadDocumentAjax(Request $request)
    {
        $businessAdmin = Auth::guard('business_admin')->user();
        
        if (!$businessAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.'
            ], 401);
        }

        try {
            $request->validate([
                'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'type' => 'required|in:rc,insurance',
            ]);

            $file = $request->file('document');
            
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file.'
                ], 422);
            }

            $type = $request->type;
            $filename = $type . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            // Always store RC/Insurance under vehicle_documents
            $path = $file->storeAs('vehicle_documents', $filename, 'public');

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully.',
                'document_path' => $path,
                'preview_url' => asset('storage/' . $path),
                'type' => $type
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading document: ' . $e->getMessage()
            ], 500);
        }
    }
}
