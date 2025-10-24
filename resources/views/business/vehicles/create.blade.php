@extends('business.layouts.app')

@section('title', 'Add New Vehicle - ' . $business->business_name)
@section('page-title', 'Add New Vehicle')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Vehicle Registration Form
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('business.vehicles.store') }}" enctype="multipart/form-data" id="vehicleForm">
                    @csrf
                    
                    <!-- Error Display Section -->
                    <div id="errorDisplay" class="mb-4" style="display: none;">
                        <div class="alert alert-danger" role="alert">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:
                            </h6>
                            <div id="errorList"></div>
                        </div>
                    </div>
                    
                    <!-- Vehicle Type Selection -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-car me-2"></i>Vehicle Type Selection
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="vehicle_type" class="form-label">Vehicle Type *</label>
                            <select class="form-select @error('vehicle_type') is-invalid @enderror" 
                                    id="vehicle_type" 
                                    name="vehicle_type" 
                                    required>
                                <option value="">Select Vehicle Type</option>
                                <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>Car</option>
                                <option value="bike_scooter" {{ old('vehicle_type') == 'bike_scooter' ? 'selected' : '' }}>Bike/Scooter</option>
                                <option value="heavy_vehicle" {{ old('vehicle_type') == 'heavy_vehicle' ? 'selected' : '' }}>Heavy Vehicle</option>
                            </select>
                            @error('vehicle_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Common Vehicle Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Vehicle Information
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="vehicle_make" class="form-label">Vehicle Make *</label>
                            <select class="form-select @error('vehicle_make') is-invalid @enderror" 
                                    id="vehicle_make" 
                                    name="vehicle_make" 
                                    required>
                                <option value="">Select Vehicle Make</option>
                            </select>
                            @error('vehicle_make')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="vehicle_images" class="form-label">Vehicle Images</label>
                            <input type="file" 
                                   class="form-control @error('vehicle_images') is-invalid @enderror" 
                                   id="vehicle_images" 
                                   name="vehicle_images[]" 
                                   accept="image/*"
                                   multiple
                                   onchange="previewMultipleImages(this)">
                            <div class="form-text">Upload multiple images of the vehicle (JPG, PNG, max 5MB each)</div>
                            @error('vehicle_images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="imagePreviewContainer" class="mt-2" style="display: none;">
                                <div class="row" id="imagePreviews"></div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="vehicle_model" class="form-label">Vehicle Model *</label>
                            <select class="form-select @error('vehicle_model') is-invalid @enderror" 
                                    id="vehicle_model" 
                                    name="vehicle_model" 
                                    required 
                                    disabled>
                                <option value="">Select Vehicle Model</option>
                            </select>
                            @error('vehicle_model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="vehicle_year" class="form-label">Vehicle Year *</label>
                            <select class="form-select @error('vehicle_year') is-invalid @enderror" 
                                    id="vehicle_year" 
                                    name="vehicle_year" 
                                    required>
                                <option value="">Select Year</option>
                                @for($year = date('Y'); $year >= 1990; $year--)
                                    <option value="{{ $year }}" {{ old('vehicle_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                            @error('vehicle_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="vehicle_number" class="form-label">Vehicle Number *</label>
                            <input type="text" 
                                   class="form-control @error('vehicle_number') is-invalid @enderror" 
                                   id="vehicle_number" 
                                   name="vehicle_number" 
                                   value="{{ old('vehicle_number') }}" 
                                   placeholder="e.g., MH12AB1234"
                                   required>
                            @error('vehicle_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="vehicle_status" class="form-label">Vehicle Status *</label>
                            <select class="form-select @error('vehicle_status') is-invalid @enderror" 
                                    id="vehicle_status" 
                                    name="vehicle_status" 
                                    required>
                                <option value="active" {{ old('vehicle_status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('vehicle_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="under_maintenance" {{ old('vehicle_status') == 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                            </select>
                            @error('vehicle_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="fuel_type" class="form-label">Fuel Type *</label>
                            <select class="form-select @error('fuel_type') is-invalid @enderror" 
                                    id="fuel_type" 
                                    name="fuel_type" 
                                    required>
                                <option value="">Select Fuel Type</option>
                                <option value="petrol" {{ old('fuel_type') == 'petrol' ? 'selected' : '' }}>Petrol</option>
                                <option value="diesel" {{ old('fuel_type') == 'diesel' ? 'selected' : '' }}>Diesel</option>
                                <option value="cng" {{ old('fuel_type') == 'cng' ? 'selected' : '' }}>CNG</option>
                                <option value="electric" {{ old('fuel_type') == 'electric' ? 'selected' : '' }}>Electric</option>
                                <option value="hybrid" {{ old('fuel_type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                            </select>
                            @error('fuel_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="mileage" class="form-label">Mileage (km/l)</label>
                            <input type="number" 
                                   class="form-control @error('mileage') is-invalid @enderror" 
                                   id="mileage" 
                                   name="mileage" 
                                   value="{{ old('mileage') }}" 
                                   step="0.01" 
                                   min="0">
                            @error('mileage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Transmission Type - Dynamic based on vehicle type -->
                        <div class="col-md-4 mb-3" id="transmission_car_heavy" style="display: none;">
                            <label for="transmission_type_car_heavy" class="form-label">Transmission Type *</label>
                            <select class="form-select @error('transmission_type') is-invalid @enderror" 
                                    id="transmission_type_car_heavy" 
                                    name="transmission_type">
                                <option value="">Select Transmission</option>
                                <option value="manual" {{ old('transmission_type') == 'manual' ? 'selected' : '' }}>Manual</option>
                                <option value="automatic" {{ old('transmission_type') == 'automatic' ? 'selected' : '' }}>Automatic</option>
                            </select>
                            @error('transmission_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3" id="transmission_bike" style="display: none;">
                            <label for="bike_transmission_type" class="form-label">Transmission Type *</label>
                            <select class="form-select @error('bike_transmission_type') is-invalid @enderror" 
                                    id="bike_transmission_type" 
                                    name="bike_transmission_type">
                                <option value="">Select Transmission</option>
                                <option value="gear" {{ old('bike_transmission_type') == 'gear' ? 'selected' : '' }}>Gear</option>
                                <option value="gearless" {{ old('bike_transmission_type') == 'gearless' ? 'selected' : '' }}>Gearless</option>
                            </select>
                            @error('bike_transmission_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Category-Specific Fields -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-cogs me-2"></i>Vehicle Specifications
                            </h6>
                        </div>
                    </div>

                    <!-- For Cars -->
                    <div id="car_specs" style="display: none;">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="seating_capacity" class="form-label">Seating Capacity *</label>
                                <select class="form-select @error('seating_capacity') is-invalid @enderror" 
                                        id="seating_capacity" 
                                        name="seating_capacity">
                                    <option value="">Select Seating Capacity</option>
                                    @for($i = 2; $i <= 8; $i++)
                                        <option value="{{ $i }}" {{ old('seating_capacity') == $i ? 'selected' : '' }}>{{ $i }} Seater</option>
                                    @endfor
                                </select>
                                @error('seating_capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- For Bikes and Scooters -->
                    <div id="bike_specs" style="display: none;">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="engine_capacity_cc" class="form-label">Engine Capacity (CC) *</label>
                                <input type="number" 
                                       class="form-control @error('engine_capacity_cc') is-invalid @enderror" 
                                       id="engine_capacity_cc" 
                                       name="engine_capacity_cc" 
                                       value="{{ old('engine_capacity_cc') }}" 
                                       min="50" 
                                       max="2000">
                                @error('engine_capacity_cc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- For Heavy Vehicles -->
                    <div id="heavy_vehicle_specs" style="display: none;">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="seating_capacity_heavy" class="form-label">Seating Capacity (for Buses)</label>
                                <select class="form-select @error('seating_capacity') is-invalid @enderror" 
                                        id="seating_capacity_heavy" 
                                        name="seating_capacity_heavy">
                                    <option value="">Select Seating Capacity</option>
                                    @for($i = 10; $i <= 100; $i += 5)
                                        <option value="{{ $i }}" {{ old('seating_capacity') == $i ? 'selected' : '' }}>{{ $i }} Seater</option>
                                    @endfor
                                </select>
                                @error('seating_capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="payload_capacity_tons" class="form-label">Payload Capacity (Tons) - for Trucks</label>
                                <input type="number" 
                                       class="form-control @error('payload_capacity_tons') is-invalid @enderror" 
                                       id="payload_capacity_tons" 
                                       name="payload_capacity_tons" 
                                       value="{{ old('payload_capacity_tons') }}" 
                                       step="0.1" 
                                       min="0" 
                                       max="100">
                                @error('payload_capacity_tons')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Rental Pricing and Usage Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-dollar-sign me-2"></i>Rental Pricing Information
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="rental_price_24h" class="form-label">24-Hour Base Price (₹)</label>
                            <input type="number" 
                                   class="form-control @error('rental_price_24h') is-invalid @enderror" 
                                   id="rental_price_24h" 
                                   name="rental_price_24h" 
                                   value="{{ old('rental_price_24h') }}" 
                                   step="0.01" 
                                   min="0">
                            @error('rental_price_24h')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="km_limit_per_booking" class="form-label">KM Limit per Booking</label>
                            <input type="number" 
                                   class="form-control @error('km_limit_per_booking') is-invalid @enderror" 
                                   id="km_limit_per_booking" 
                                   name="km_limit_per_booking" 
                                   value="{{ old('km_limit_per_booking') }}" 
                                   min="0">
                            @error('km_limit_per_booking')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="extra_rental_price_per_hour" class="form-label">Extra Price per Hour (₹)</label>
                            <input type="number" 
                                   class="form-control @error('extra_rental_price_per_hour') is-invalid @enderror" 
                                   id="extra_rental_price_per_hour" 
                                   name="extra_rental_price_per_hour" 
                                   value="{{ old('extra_rental_price_per_hour') }}" 
                                   step="0.01" 
                                   min="0">
                            @error('extra_rental_price_per_hour')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="extra_price_per_km" class="form-label">Extra Price per KM (₹)</label>
                            <input type="number" 
                                   class="form-control @error('extra_price_per_km') is-invalid @enderror" 
                                   id="extra_price_per_km" 
                                   name="extra_price_per_km" 
                                   value="{{ old('extra_price_per_km') }}" 
                                   step="0.01" 
                                   min="0">
                            @error('extra_price_per_km')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Ownership and Vendor Details -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-handshake me-2"></i>Ownership and Vendor Details
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="ownership_type" class="form-label">Ownership Type *</label>
                            <select class="form-select @error('ownership_type') is-invalid @enderror" 
                                    id="ownership_type" 
                                    name="ownership_type" 
                                    required>
                                <option value="">Select Ownership Type</option>
                                <option value="owned" {{ old('ownership_type') == 'owned' ? 'selected' : '' }}>Owned</option>
                                <option value="leased" {{ old('ownership_type') == 'leased' ? 'selected' : '' }}>Leased</option>
                                <option value="vendor_provided" {{ old('ownership_type') == 'vendor_provided' ? 'selected' : '' }}>Vendor Provided</option>
                            </select>
                            @error('ownership_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3" id="vendor_fields" style="display: none;">
                            <label for="vendor_search" class="form-label">Search Vendor</label>
                            <div class="position-relative">
                                <input type="text" 
                                       class="form-control @error('vendor_name') is-invalid @enderror" 
                                       id="vendor_search" 
                                       placeholder="Type to search vendors..."
                                       autocomplete="off">
                                <input type="hidden" id="vendor_id" name="vendor_id" value="{{ old('vendor_id') }}">
                                <input type="hidden" id="vendor_name" name="vendor_name" value="{{ old('vendor_name') }}">
                                <div id="vendor_dropdown" class="dropdown-menu w-100" style="display: none; max-height: 200px; overflow-y: auto;">
                                    <!-- Vendor options will be populated here -->
                                </div>
                            </div>
                            @error('vendor_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2 mb-3" id="commission_type_field" style="display: none;">
                            <label for="commission_type" class="form-label">Commission Type</label>
                            <select class="form-select @error('commission_type') is-invalid @enderror" 
                                    id="commission_type" 
                                    name="commission_type">
                                <option value="">Select Type</option>
                                <option value="fixed" {{ old('commission_type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                <option value="percentage" {{ old('commission_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                            </select>
                            @error('commission_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2 mb-3" id="commission_value_field" style="display: none;">
                            <label for="commission_value" class="form-label">Commission Value</label>
                            <input type="number" 
                                   class="form-control @error('commission_value') is-invalid @enderror" 
                                   id="commission_value" 
                                   name="commission_value" 
                                   value="{{ old('commission_value') }}" 
                                   step="0.01" 
                                   min="0">
                            @error('commission_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Insurance and Legal Documents -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-shield-alt me-2"></i>Insurance and Legal Documents
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="insurance_provider" class="form-label">Insurance Provider *</label>
                            <input type="text" 
                                   class="form-control @error('insurance_provider') is-invalid @enderror" 
                                   id="insurance_provider" 
                                   name="insurance_provider" 
                                   value="{{ old('insurance_provider') }}" 
                                   required>
                            @error('insurance_provider')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="policy_number" class="form-label">Policy Number *</label>
                            <input type="text" 
                                   class="form-control @error('policy_number') is-invalid @enderror" 
                                   id="policy_number" 
                                   name="policy_number" 
                                   value="{{ old('policy_number') }}" 
                                   required>
                            @error('policy_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="insurance_expiry_date" class="form-label">Insurance Expiry Date *</label>
                            <input type="date" 
                                   class="form-control @error('insurance_expiry_date') is-invalid @enderror" 
                                   id="insurance_expiry_date" 
                                   name="insurance_expiry_date" 
                                   value="{{ old('insurance_expiry_date') }}" 
                                   min="{{ date('Y-m-d') }}" 
                                   required>
                            @error('insurance_expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="insurance_document" class="form-label">Upload Insurance Document</label>
                            <input type="file" 
                                   class="form-control @error('insurance_document') is-invalid @enderror" 
                                   id="insurance_document" 
                                   name="insurance_document" 
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF, JPG, PNG files only. Max size: 10MB</div>
                            @error('insurance_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="rc_document" class="form-label">Upload RC Document</label>
                            <input type="file" 
                                   class="form-control @error('rc_document') is-invalid @enderror" 
                                   id="rc_document" 
                                   name="rc_document" 
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF, JPG, PNG files only. Max size: 10MB</div>
                            @error('rc_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rc_number" class="form-label">RC Number *</label>
                            <input type="text" 
                                   class="form-control @error('rc_number') is-invalid @enderror" 
                                   id="rc_number" 
                                   name="rc_number" 
                                   value="{{ old('rc_number') }}" 
                                   required>
                            @error('rc_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Maintenance and Service -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-wrench me-2"></i>Maintenance and Service
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="last_service_date" class="form-label">Last Service Date</label>
                            <input type="date" 
                                   class="form-control @error('last_service_date') is-invalid @enderror" 
                                   id="last_service_date" 
                                   name="last_service_date" 
                                   value="{{ old('last_service_date') }}">
                            @error('last_service_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="last_service_meter_reading" class="form-label">Last Service Meter Reading</label>
                            <input type="number" 
                                   class="form-control @error('last_service_meter_reading') is-invalid @enderror" 
                                   id="last_service_meter_reading" 
                                   name="last_service_meter_reading" 
                                   value="{{ old('last_service_meter_reading') }}" 
                                   min="0">
                            @error('last_service_meter_reading')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="next_service_due" class="form-label">Next Service Due</label>
                            <input type="date" 
                                   class="form-control @error('next_service_due') is-invalid @enderror" 
                                   id="next_service_due" 
                                   name="next_service_due" 
                                   value="{{ old('next_service_due') }}">
                            @error('next_service_due')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="next_service_meter_reading" class="form-label">Next Service Meter Reading</label>
                            <input type="number" 
                                   class="form-control @error('next_service_meter_reading') is-invalid @enderror" 
                                   id="next_service_meter_reading" 
                                   name="next_service_meter_reading" 
                                   value="{{ old('next_service_meter_reading') }}" 
                                   min="0">
                            @error('next_service_meter_reading')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-sticky-note me-2"></i>Additional Information
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="remarks_notes" class="form-label">Remarks/Notes</label>
                            <textarea class="form-control @error('remarks_notes') is-invalid @enderror" 
                                      id="remarks_notes" 
                                      name="remarks_notes" 
                                      rows="3" 
                                      placeholder="Any special remarks, notes, or instructions...">{{ old('remarks_notes') }}</textarea>
                            @error('remarks_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('business.vehicles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Register Vehicle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Vendor Modal -->
<div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="addVendorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVendorModalLabel">Add New Vendor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addVendorForm">
                    <div class="mb-3">
                        <label for="new_vendor_name" class="form-label">Vendor Name *</label>
                        <input type="text" class="form-control" id="new_vendor_name" name="vendor_name" value="Test Vendor" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_vendor_type" class="form-label">Vendor Type *</label>
                        <select class="form-select" id="new_vendor_type" name="vendor_type" required>
                            <option value="">Select Type</option>
                            <option value="vehicle_provider" selected>Vehicle Provider</option>
                            <option value="service_partner">Service Partner</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="new_vendor_pan" class="form-label">PAN Number *</label>
                        <input type="text" class="form-control" id="new_vendor_pan" name="pan_number" value="ABCDE1234F" maxlength="10" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_vendor_contact" class="form-label">Primary Contact Person *</label>
                        <input type="text" class="form-control" id="new_vendor_contact" name="primary_contact_person" value="John Doe" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_vendor_mobile" class="form-label">Mobile Number *</label>
                        <input type="tel" class="form-control" id="new_vendor_mobile" name="mobile_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_vendor_email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="new_vendor_email" name="email_address" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_vendor_address" class="form-label">Office Address *</label>
                        <textarea class="form-control" id="new_vendor_address" name="office_address" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="new_vendor_payout" class="form-label">Payout Method *</label>
                        <select class="form-select" id="new_vendor_payout" name="payout_method" required>
                            <option value="">Select Method</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="upi_payment">UPI Payment</option>
                            <option value="cheque">Cheque</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="new_vendor_frequency" class="form-label">Payout Frequency *</label>
                        <select class="form-select" id="new_vendor_frequency" name="payout_frequency" required onchange="toggleFrequencyFields()">
                            <option value="">Select Frequency</option>
                            <option value="weekly">Weekly</option>
                            <option value="bi_weekly">Bi-weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="after_every_booking">After Every Booking</option>
                        </select>
                    </div>
                    
                    <!-- Conditional fields for weekly/bi-weekly -->
                    <div id="weekly_frequency_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="new_vendor_day_of_week" class="form-label">Day of Week *</label>
                            <select class="form-select" id="new_vendor_day_of_week" name="payout_day_of_week">
                                <option value="">Select Day</option>
                                <option value="monday" selected>Monday</option>
                                <option value="tuesday">Tuesday</option>
                                <option value="wednesday">Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                                <option value="saturday">Saturday</option>
                                <option value="sunday">Sunday</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Conditional fields for monthly/quarterly -->
                    <div id="monthly_frequency_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="new_vendor_day_of_month" class="form-label">Day of Month *</label>
                            <input type="number" class="form-control" id="new_vendor_day_of_month" name="payout_day_of_month" min="1" max="31">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="new_vendor_commission_type" class="form-label">Commission Type *</label>
                        <select class="form-select" id="new_vendor_commission_type" name="commission_type" required>
                            <option value="">Select Type</option>
                            <option value="fixed_amount">Fixed Amount</option>
                            <option value="percentage_of_revenue">Percentage of Revenue</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="new_vendor_commission_rate" class="form-label">Commission Rate *</label>
                        <input type="number" class="form-control" id="new_vendor_commission_rate" name="commission_rate" step="0.01" min="0" required>
                    </div>
                    
                    <!-- Conditional fields for bank transfer -->
                    <div id="bank_transfer_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="new_vendor_bank_name" class="form-label">Bank Name *</label>
                            <input type="text" class="form-control" id="new_vendor_bank_name" name="bank_name" value="State Bank of India">
                        </div>
                        <div class="mb-3">
                            <label for="new_vendor_account_holder" class="form-label">Account Holder Name *</label>
                            <input type="text" class="form-control" id="new_vendor_account_holder" name="account_holder_name" value="John Doe">
                        </div>
                        <div class="mb-3">
                            <label for="new_vendor_account_number" class="form-label">Account Number *</label>
                            <input type="text" class="form-control" id="new_vendor_account_number" name="account_number" value="1234567890">
                        </div>
                        <div class="mb-3">
                            <label for="new_vendor_ifsc" class="form-label">IFSC Code *</label>
                            <input type="text" class="form-control" id="new_vendor_ifsc" name="ifsc_code" value="SBIN0001234" maxlength="11">
                        </div>
                    </div>
                    
                    <!-- Conditional fields for UPI payment -->
                    <div id="upi_payment_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="new_vendor_upi_id" class="form-label">UPI ID *</label>
                            <input type="text" class="form-control" id="new_vendor_upi_id" name="upi_id">
                        </div>
                    </div>
                    
                    <!-- Conditional fields for other payout method -->
                    <div id="other_payout_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="new_vendor_other_payout" class="form-label">Other Payout Method *</label>
                            <input type="text" class="form-control" id="new_vendor_other_payout" name="other_payout_method">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveNewVendor()">
                    <i class="fas fa-save me-1"></i>Save Vendor
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let vehicleTypeSelect, carSpecs, bikeSpecs, heavyVehicleSpecs, transmissionCarHeavy, transmissionBike;
let ownershipTypeSelect, vendorFields, commissionTypeField, commissionValueField;
let vendorSearchTimeout, vendorSearchInput, vendorDropdown, vendorIdInput, vendorNameInput;

// Custom fetch function to avoid common.js conflicts
function customFetch(url, options = {}) {
    // Use XMLHttpRequest instead of fetch to avoid common.js interception
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(options.method || 'GET', url);
        
        // Set headers
        if (options.headers) {
            Object.keys(options.headers).forEach(key => {
                xhr.setRequestHeader(key, options.headers[key]);
            });
        }
        
        xhr.onload = function() {
            const response = {
                ok: xhr.status >= 200 && xhr.status < 300,
                status: xhr.status,
                statusText: xhr.statusText,
                headers: {
                    get: function(name) {
                        return xhr.getResponseHeader(name);
                    }
                },
                json: () => {
                    try {
                        return Promise.resolve(JSON.parse(xhr.responseText));
                    } catch (e) {
                        return Promise.reject(new Error('Invalid JSON response'));
                    }
                },
                text: () => Promise.resolve(xhr.responseText)
            };
            resolve(response);
        };
        
        xhr.onerror = function() {
            reject(new Error('Network error'));
        };
        
        // Send request
        if (options.body) {
            xhr.send(options.body);
        } else {
            xhr.send();
        }
    });
}

// Multiple image preview function
function previewMultipleImages(input) {
    const previewContainer = document.getElementById('imagePreviewContainer');
    const imagePreviews = document.getElementById('imagePreviews');
    
    // Clear previous previews
    imagePreviews.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        previewContainer.style.display = 'block';
        
        Array.from(input.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const colDiv = document.createElement('div');
                    colDiv.className = 'col-md-3 mb-2';
                    colDiv.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" 
                                 alt="Preview ${index + 1}" 
                                 class="img-thumbnail" 
                                 style="width: 100%; height: 120px; object-fit: cover;">
                            <div class="position-absolute top-0 end-0">
                                <span class="badge bg-primary">${index + 1}</span>
                            </div>
                        </div>
                    `;
                    imagePreviews.appendChild(colDiv);
                };
                reader.readAsDataURL(file);
            }
        });
    } else {
        previewContainer.style.display = 'none';
    }
}

// Add vendor modal functions
function openAddVendorModal() {
    const modal = new bootstrap.Modal(document.getElementById('addVendorModal'));
    modal.show();
    
    // Add event listener for payout method change
    const payoutMethodSelect = document.getElementById('new_vendor_payout');
    if (payoutMethodSelect) {
        payoutMethodSelect.addEventListener('change', togglePayoutFields);
        // Show conditional fields based on default selection
        togglePayoutFields();
    }
    
    // Add event listener for payout frequency change
    const frequencySelect = document.getElementById('new_vendor_frequency');
    if (frequencySelect) {
        frequencySelect.addEventListener('change', toggleFrequencyFields);
        // Show conditional fields based on default selection
        toggleFrequencyFields();
    }
}

function togglePayoutFields() {
    const payoutMethod = document.getElementById('new_vendor_payout').value;
    const bankFields = document.getElementById('bank_transfer_fields');
    const upiFields = document.getElementById('upi_payment_fields');
    const otherFields = document.getElementById('other_payout_fields');
    
    // Hide all conditional fields
    if (bankFields) bankFields.style.display = 'none';
    if (upiFields) upiFields.style.display = 'none';
    if (otherFields) otherFields.style.display = 'none';
    
    // Show relevant fields based on selection
    if (payoutMethod === 'bank_transfer' && bankFields) {
        bankFields.style.display = 'block';
    } else if (payoutMethod === 'upi_payment' && upiFields) {
        upiFields.style.display = 'block';
    } else if (payoutMethod === 'other' && otherFields) {
        otherFields.style.display = 'block';
    }
}

function toggleFrequencyFields() {
    const frequency = document.getElementById('new_vendor_frequency').value;
    const weeklyFields = document.getElementById('weekly_frequency_fields');
    const monthlyFields = document.getElementById('monthly_frequency_fields');
    
    // Hide all conditional fields
    if (weeklyFields) weeklyFields.style.display = 'none';
    if (monthlyFields) monthlyFields.style.display = 'none';
    
    // Show relevant fields based on selection
    if ((frequency === 'weekly' || frequency === 'bi_weekly') && weeklyFields) {
        weeklyFields.style.display = 'block';
    } else if ((frequency === 'monthly' || frequency === 'quarterly') && monthlyFields) {
        monthlyFields.style.display = 'block';
    }
}

function saveNewVendor() {
    const form = document.getElementById('addVendorForm');
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Debug: Log form data
    console.log('Form data being sent:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    // Add timestamp to make mobile number unique
    const timestamp = Date.now();
    formData.set('mobile_number', '888888888' + (timestamp % 1000));
    formData.set('email_address', 'test' + timestamp + '@vendor.com');
    formData.set('pan_number', 'ABCDE' + (timestamp % 10000));
    
    console.log('Updated form data with unique values:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    customFetch('{{ url("/business/vendors") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Vendor creation response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // Try to get response text first to see what we're actually getting
        return response.text().then(text => {
            console.log('Raw response text:', text);
            try {
                const data = JSON.parse(text);
                console.log('Parsed response data:', data);
                
                if (!response.ok) {
                    // Handle validation errors
                    if (response.status === 422) {
                        console.error('Validation errors:', data.errors || data);
                        console.error('Full response data:', data);
                        if (data.errors) {
                            showValidationErrors(data.errors);
                        } else {
                            showAlert('Validation failed: ' + JSON.stringify(data), 'danger');
                        }
                        return;
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return data;
            } catch (parseError) {
                console.error('Error parsing JSON:', parseError);
                console.error('Raw response was:', text);
                showAlert('Server returned invalid JSON: ' + text.substring(0, 200), 'danger');
                return;
            }
        });
    })
    .then(data => {
        if (!data) return; // Skip if we handled validation errors above
        console.log('Vendor creation response:', data);
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addVendorModal'));
            modal.hide();
            
            // Clear form
            form.reset();
            
            // Select the new vendor
            selectVendor(data.vendor);
            
            // Show success message
            showAlert('Vendor added successfully!', 'success');
        } else {
            showAlert(data.message || 'Error adding vendor', 'danger');
        }
    })
    .catch(error => {
        console.error('Error adding vendor:', error);
        showAlert(`Error adding vendor: ${error.message}`, 'danger');
    });
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

function showValidationErrors(errors) {
    // Clear previous error messages
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    
    // Show new error messages
    Object.keys(errors).forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = errors[field][0];
            input.parentNode.appendChild(errorDiv);
        }
    });
    
    // Show general error message
    showAlert('Please fix the validation errors above.', 'danger');
}

// Vendor search functions
function searchVendors(query) {
    if (!vendorDropdown) {
        console.error('Vendor dropdown element not found');
        return;
    }
    
    const url = `{{ url("/business/vendors/search") }}?q=${encodeURIComponent(query)}`;
    
    customFetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.vendors && data.vendors.length > 0) {
            displayVendorOptions(data.vendors);
        } else {
            displayNoVendorsFound();
        }
    })
    .catch(error => {
        console.error('Error searching vendors:', error);
        displayNoVendorsFound();
    });
}

function displayVendorOptions(vendors) {
    vendorDropdown.innerHTML = '';
    
    // Add vendor options
    vendors.forEach(vendor => {
        const option = document.createElement('div');
        option.className = 'dropdown-item';
        option.style.cursor = 'pointer';
        option.innerHTML = `
            <div class="fw-bold">${vendor.vendor_name}</div>
            <small class="text-muted">${vendor.mobile_number} • ${vendor.vendor_type}</small>
            ${vendor.commission_type && vendor.commission_rate ? 
                `<small class="text-info d-block">Commission: ${vendor.commission_type} - ${vendor.commission_rate}</small>` : 
                ''
            }
        `;
        option.addEventListener('click', () => selectVendor(vendor));
        vendorDropdown.appendChild(option);
    });
    
    // Add "Add New Vendor" button at the bottom
    const addButton = document.createElement('div');
    addButton.className = 'dropdown-item border-top';
    addButton.style.cursor = 'pointer';
    addButton.innerHTML = `
        <div class="d-flex align-items-center text-primary">
            <i class="fas fa-plus me-2"></i>
            <span>Add New Vendor</span>
        </div>
    `;
    addButton.onclick = () => openAddVendorModal();
    vendorDropdown.appendChild(addButton);
    
    vendorDropdown.style.display = 'block';
}

function displayNoVendorsFound() {
    vendorDropdown.innerHTML = `
        <div class="dropdown-item text-muted text-center py-3">
            <div class="mb-2">No vendors found</div>
            <div class="dropdown-item border-top" style="cursor: pointer;" onclick="openAddVendorModal()">
                <div class="d-flex align-items-center text-primary">
                    <i class="fas fa-plus me-2"></i>
                    <span>Add New Vendor</span>
                </div>
            </div>
        </div>
    `;
    vendorDropdown.style.display = 'block';
}

function selectVendor(vendor) {
    vendorSearchInput.value = vendor.vendor_name;
    vendorIdInput.value = vendor.id;
    vendorNameInput.value = vendor.vendor_name;
    vendorDropdown.style.display = 'none';
    
    // Update commission fields if vendor has commission data
    if (vendor.commission_type && vendor.commission_rate) {
        const commissionTypeSelect = document.getElementById('commission_type');
        const commissionValueInput = document.getElementById('commission_value');
        
        if (commissionTypeSelect) {
            // Map vendor commission_type to form values
            let formCommissionType = '';
            if (vendor.commission_type === 'fixed_amount') {
                formCommissionType = 'fixed';
            } else if (vendor.commission_type === 'percentage_of_revenue') {
                formCommissionType = 'percentage';
            }
            
            commissionTypeSelect.value = formCommissionType;
            console.log('Updated commission type to:', formCommissionType);
        }
        
        if (commissionValueInput) {
            commissionValueInput.value = vendor.commission_rate;
            console.log('Updated commission value to:', vendor.commission_rate);
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables
    vehicleTypeSelect = document.getElementById('vehicle_type');
    carSpecs = document.getElementById('car_specs');
    bikeSpecs = document.getElementById('bike_specs');
    heavyVehicleSpecs = document.getElementById('heavy_vehicle_specs');
    transmissionCarHeavy = document.getElementById('transmission_car_heavy');
    transmissionBike = document.getElementById('transmission_bike');
    ownershipTypeSelect = document.getElementById('ownership_type');
    vendorFields = document.getElementById('vendor_fields');
    commissionTypeField = document.getElementById('commission_type_field');
    commissionValueField = document.getElementById('commission_value_field');
    
    // Initialize vendor search elements
    vendorSearchInput = document.getElementById('vendor_search');
    vendorDropdown = document.getElementById('vendor_dropdown');
    vendorIdInput = document.getElementById('vendor_id');
    vendorNameInput = document.getElementById('vendor_name');
    
    console.log('Vendor elements initialized:');
    console.log('vendorSearchInput:', vendorSearchInput);
    console.log('vendorDropdown:', vendorDropdown);
    console.log('vendorIdInput:', vendorIdInput);
    console.log('vendorNameInput:', vendorNameInput);

    function toggleVehicleSpecs() {
        const vehicleType = vehicleTypeSelect.value;
        
        // Hide all specs
        carSpecs.style.display = 'none';
        bikeSpecs.style.display = 'none';
        heavyVehicleSpecs.style.display = 'none';
        transmissionCarHeavy.style.display = 'none';
        transmissionBike.style.display = 'none';

        // Show relevant specs
        if (vehicleType === 'car') {
            carSpecs.style.display = 'block';
            transmissionCarHeavy.style.display = 'block';
        } else if (vehicleType === 'bike_scooter') {
            bikeSpecs.style.display = 'block';
            transmissionBike.style.display = 'block';
        } else if (vehicleType === 'heavy_vehicle') {
            heavyVehicleSpecs.style.display = 'block';
            transmissionCarHeavy.style.display = 'block';
        }
    }

    function toggleVendorFields() {
        const ownershipType = ownershipTypeSelect.value;
        
        if (ownershipType === 'vendor_provided') {
            vendorFields.style.display = 'block';
            commissionTypeField.style.display = 'block';
            commissionValueField.style.display = 'block';
        } else {
            vendorFields.style.display = 'none';
            commissionTypeField.style.display = 'none';
            commissionValueField.style.display = 'none';
        }
    }

    vehicleTypeSelect.addEventListener('change', toggleVehicleSpecs);
    ownershipTypeSelect.addEventListener('change', toggleVendorFields);

    // Initialize on page load
    toggleVehicleSpecs();
    toggleVendorFields();
    
    // Add event listeners to clear error styling
    const formFields = document.querySelectorAll('.form-control, .form-select');
    formFields.forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            this.style.borderColor = '';
        });
        
        field.addEventListener('change', function() {
            this.classList.remove('is-invalid');
            this.style.borderColor = '';
        });
    });

    // Vendor search event listener
    if (vendorSearchInput) {
        console.log('Vendor search input found, adding event listener');
        vendorSearchInput.addEventListener('input', function() {
            console.log('Vendor search input changed:', this.value);
            clearTimeout(vendorSearchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                if (vendorDropdown) {
                    vendorDropdown.style.display = 'none';
                }
                return;
            }
            
            vendorSearchTimeout = setTimeout(() => {
                searchVendors(query);
            }, 300);
        });
    } else {
        console.error('Vendor search input not found');
    }

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#vendor_fields')) {
            if (vendorDropdown) {
                vendorDropdown.style.display = 'none';
            }
        }
    });

    // Form submission with validation
    document.getElementById('vehicleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Clear previous alerts
        clearAlerts();
        
        // Validate form
        if (!validateForm()) {
            return;
        }
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registering...';
        submitBtn.disabled = true;
        
        // Submit form via AJAX
        const formData = new FormData(this);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Ensure seating capacity and transmission type are included based on vehicle type
        const vehicleType = document.getElementById('vehicle_type').value;
        if (vehicleType === 'car') {
            const seatingCapacity = document.getElementById('seating_capacity').value;
            if (seatingCapacity) {
                formData.set('seating_capacity', seatingCapacity);
            }
            const transmissionType = document.getElementById('transmission_type_car_heavy').value;
            if (transmissionType) {
                formData.set('transmission_type', transmissionType);
            }
        } else if (vehicleType === 'heavy_vehicle') {
            const seatingCapacityHeavy = document.getElementById('seating_capacity_heavy').value;
            if (seatingCapacityHeavy) {
                formData.set('seating_capacity', seatingCapacityHeavy);
            }
            const transmissionType = document.getElementById('transmission_type_car_heavy').value;
            if (transmissionType) {
                formData.set('transmission_type', transmissionType);
            }
        } else if (vehicleType === 'bike_scooter') {
            const bikeTransmissionType = document.getElementById('bike_transmission_type').value;
            if (bikeTransmissionType) {
                formData.set('bike_transmission_type', bikeTransmissionType);
            }
        }
        
        console.log('Form action:', this.action);
        console.log('CSRF Token:', csrfToken);
        console.log('Form data:', Object.fromEntries(formData));
        console.log('Vehicle type:', document.getElementById('vehicle_type').value);
        console.log('Seating capacity:', document.getElementById('seating_capacity').value);
        console.log('Transmission type (car/heavy):', document.getElementById('transmission_type_car_heavy').value);
        console.log('Bike transmission type:', document.getElementById('bike_transmission_type').value);
        console.log('Car specs visible:', document.getElementById('car_specs').style.display);
        
        // Debug all form fields
        console.log('=== FORM FIELD DEBUG ===');
        const allInputs = this.querySelectorAll('input, select, textarea');
        allInputs.forEach(input => {
            if (input.name && input.value) {
                console.log(`${input.name}: ${input.value}`);
            }
        });
        console.log('=== END FORM FIELD DEBUG ===');
        
        customFetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (response.ok) {
                return response.json();
            } else {
                // Always try to get the response text first
                return response.text().then(text => {
                    console.error('=== SERVER RESPONSE DEBUG ===');
                    console.error('Response status:', response.status);
                    console.error('Response statusText:', response.statusText);
                    console.error('Response headers:', response.headers);
                    console.error('Raw server response text:', text);
                    console.error('Response text length:', text.length);
                    console.error('Response text type:', typeof text);
                    console.error('=== END SERVER RESPONSE DEBUG ===');
                    
                    try {
                        const errorData = JSON.parse(text);
                        console.error('Successfully parsed JSON:', errorData);
                        
                        if (errorData.errors) {
                            // Handle validation errors
                            console.error('Validation errors:', errorData.errors);
                            showValidationErrors(errorData.errors);
                            resetSubmitButton(submitBtn, originalText);
                            return; // Don't throw error, just show validation errors
                        }
                        
                        if (errorData.message) {
                            console.error('Error message:', errorData.message);
                            throw new Error(errorData.message);
                        }
                        
                        throw new Error('Server error: ' + response.status + ' - ' + text);
                    } catch (parseError) {
                        console.error('JSON parse error:', parseError);
                        console.error('Raw response text:', text);
                        // If not JSON, show raw text
                        throw new Error('Server error: ' + response.status + ' - ' + text);
                    }
                });
            }
        })
        .then(data => {
            if (!data) return; // Skip if we handled validation errors above
            
            if (data.success) {
                showSuccessAlert('Vehicle registered successfully! Redirecting...');
                setTimeout(() => {
                    window.location.href = data.redirect_url || '{{ route("business.vehicles.index") }}';
                }, 2000);
            } else {
                let errorMessage = data.message || 'Registration failed. Please try again.';
                
                // Handle validation errors
                if (data.errors) {
                    const errorList = Object.values(data.errors).flat().join('<br>');
                    errorMessage = errorList;
                }
                
                showErrorAlert(errorMessage);
                resetSubmitButton(submitBtn, originalText);
            }
        })
        .catch(error => {
            console.error('Error details:', error);
            console.error('Error message:', error.message);
            console.error('Error stack:', error.stack);
            showErrorAlert('An error occurred while registering the vehicle: ' + error.message);
            resetSubmitButton(submitBtn, originalText);
        });
    });
    
    // Form validation
    function validateForm() {
        let isValid = true;
        const errors = [];
        const errorFields = [];
        
        // Clear previous error styling
        clearFieldErrors();
        
        // Check vehicle type
        if (!document.getElementById('vehicle_type').value) {
            errors.push('Please select a vehicle type');
            errorFields.push('vehicle_type');
            isValid = false;
        }
        
        // Check vehicle make
        if (!document.getElementById('vehicle_make').value) {
            errors.push('Please select a vehicle make');
            errorFields.push('vehicle_make');
            isValid = false;
        }
        
        // Check vehicle model
        if (!document.getElementById('vehicle_model').value) {
            errors.push('Please select a vehicle model');
            errorFields.push('vehicle_model');
            isValid = false;
        }
        
        // Check vehicle number
        if (!document.getElementById('vehicle_number').value.trim()) {
            errors.push('Please enter vehicle number');
            errorFields.push('vehicle_number');
            isValid = false;
        }
        
        // Check year
        if (!document.getElementById('vehicle_year').value) {
            errors.push('Please select vehicle year');
            errorFields.push('vehicle_year');
            isValid = false;
        }
        
        // Check fuel type
        if (!document.getElementById('fuel_type').value) {
            errors.push('Please select fuel type');
            errorFields.push('fuel_type');
            isValid = false;
        }
        
        // Check ownership type
        if (!document.getElementById('ownership_type').value) {
            errors.push('Please select ownership type');
            errorFields.push('ownership_type');
            isValid = false;
        }
        
        // Check vehicle type specific required fields
        const vehicleType = document.getElementById('vehicle_type').value;
        if (vehicleType === 'car') {
            if (!document.getElementById('seating_capacity').value) {
                errors.push('Please select seating capacity for car');
                errorFields.push('seating_capacity');
                isValid = false;
            }
            if (!document.getElementById('transmission_type_car_heavy').value) {
                errors.push('Please select transmission type for car');
                errorFields.push('transmission_type_car_heavy');
                isValid = false;
            }
        } else if (vehicleType === 'bike_scooter') {
            if (!document.getElementById('engine_capacity_cc').value) {
                errors.push('Please enter engine capacity for bike/scooter');
                errorFields.push('engine_capacity_cc');
                isValid = false;
            }
            if (!document.getElementById('bike_transmission_type').value) {
                errors.push('Please select transmission type for bike/scooter');
                errorFields.push('bike_transmission_type');
                isValid = false;
            }
        } else if (vehicleType === 'heavy_vehicle') {
            if (!document.getElementById('transmission_type_car_heavy').value) {
                errors.push('Please select transmission type for heavy vehicle');
                errorFields.push('transmission_type_car_heavy');
                isValid = false;
            }
        }
        
        // Check insurance provider
        if (!document.getElementById('insurance_provider').value.trim()) {
            errors.push('Please enter insurance provider');
            errorFields.push('insurance_provider');
            isValid = false;
        }
        
        // Check policy number
        if (!document.getElementById('policy_number').value.trim()) {
            errors.push('Please enter policy number');
            errorFields.push('policy_number');
            isValid = false;
        }
        
        // Check insurance expiry date
        if (!document.getElementById('insurance_expiry_date').value) {
            errors.push('Please select insurance expiry date');
            errorFields.push('insurance_expiry_date');
            isValid = false;
        }
        
        // Check RC number
        if (!document.getElementById('rc_number').value.trim()) {
            errors.push('Please enter RC number');
            errorFields.push('rc_number');
            isValid = false;
        }
        
        // Highlight error fields
        errorFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.classList.add('is-invalid');
                field.style.borderColor = '#dc3545';
            }
        });
        
        if (!isValid) {
            showErrorAlert(errors.join('<br>'));
        }
        
        return isValid;
    }
    
    // Clear field error styling
    function clearFieldErrors() {
        const fields = document.querySelectorAll('.form-control, .form-select');
        fields.forEach(field => {
            field.classList.remove('is-invalid');
            field.style.borderColor = '';
        });
    }
    
    // Reset submit button
    function resetSubmitButton(btn, originalText) {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
    
    // Clear all alerts
    function clearAlerts() {
        $('.alert').remove();
        
        // Hide error display section
        const errorDisplay = document.getElementById('errorDisplay');
        if (errorDisplay) {
            errorDisplay.style.display = 'none';
        }
    }
    
    // Show success alert
    function showSuccessAlert(message) {
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('.card-body').prepend(alertHtml);
    }
    
    // Show error alert
    function showErrorAlert(message) {
        // Clear previous errors
        clearAlerts();
        
        // Show error display section
        const errorDisplay = document.getElementById('errorDisplay');
        const errorList = document.getElementById('errorList');
        
        if (errorDisplay && errorList) {
            errorList.innerHTML = message;
            errorDisplay.style.display = 'block';
            
            // Scroll to error section
            errorDisplay.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            // Fallback to old method
            const alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><strong>Please fix the following errors:</strong><br>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('.card-body').prepend(alertHtml);
            
            // Scroll to top to show the error
            $('html, body').animate({
                scrollTop: 0
            }, 500);
        }
    }
    
    // Show validation errors in a structured format
    function showValidationErrors(errors) {
        clearAlerts();
        
        const errorDisplay = document.getElementById('errorDisplay');
        const errorList = document.getElementById('errorList');
        
        if (errorDisplay && errorList) {
            let errorHtml = '<ul class="mb-0">';
            
            if (typeof errors === 'object') {
                Object.keys(errors).forEach(field => {
                    const fieldErrors = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
                    fieldErrors.forEach(error => {
                        errorHtml += `<li><strong>${field}:</strong> ${error}</li>`;
                    });
                });
            } else {
                errorHtml += `<li>${errors}</li>`;
            }
            
            errorHtml += '</ul>';
            errorList.innerHTML = errorHtml;
            errorDisplay.style.display = 'block';
            
            // Scroll to error section
            errorDisplay.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            // Fallback to showErrorAlert
            showErrorAlert(errors);
        }
    }
});
</script>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let vehicleMakes = [];
    let vehicleModels = [];
    let currentVehicleType = '';

    // Initialize regular select elements
    // Note: Select2 removed to avoid conflicts

    // Load vehicle makes when vehicle type changes
    $('#vehicle_type').on('change', function() {
        currentVehicleType = $(this).val();
        loadVehicleMakes();
    });

    // Load vehicle models when make changes
    $('#vehicle_make').on('change', function() {
        const makeId = $(this).val();
        loadVehicleModels(makeId);
    });

    // Load vehicle makes based on type
    function loadVehicleMakes() {
        if (!currentVehicleType) {
            $('#vehicle_make').empty().append('<option value="">Select Vehicle Make</option>').prop('disabled', true);
            $('#vehicle_model').empty().append('<option value="">Select Vehicle Model</option>').prop('disabled', true);
            return;
        }

        $('#vehicle_make').prop('disabled', false);
        $('#vehicle_model').empty().append('<option value="">Select Vehicle Model</option>').prop('disabled', true);

        $.ajax({
            url: '{{ route('business.api.vehicle-makes') }}',
            method: 'GET',
            data: { type: currentVehicleType },
            success: function(response) {
                vehicleMakes = response;
                $('#vehicle_make').empty().append('<option value="">Select Vehicle Make</option>');
                
                response.forEach(function(make) {
                    $('#vehicle_make').append(`<option value="${make.name}">${make.name}</option>`);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading vehicle makes:', error);
                showAlert('Error loading vehicle makes. Please try again.', 'danger');
            }
        });
    }

    // Load vehicle models based on make
    function loadVehicleModels(makeName) {
        if (!makeName) {
            $('#vehicle_model').empty().append('<option value="">Select Vehicle Model</option>').prop('disabled', true);
            return;
        }

        $('#vehicle_model').prop('disabled', false);

        $.ajax({
            url: '{{ route('business.api.vehicle-models') }}',
            method: 'GET',
            data: { make_name: makeName },
            success: function(response) {
                vehicleModels = response;
                $('#vehicle_model').empty().append('<option value="">Select Vehicle Model</option>');
                
                response.forEach(function(model) {
                    $('#vehicle_model').append(`<option value="${model.name}">${model.name}</option>`);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading vehicle models:', error);
                showAlert('Error loading vehicle models. Please try again.', 'danger');
            }
        });
    }

    // Show alert function
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('.card-body').prepend(alertHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Initialize on page load if vehicle type is already selected
    if ($('#vehicle_type').val()) {
        currentVehicleType = $('#vehicle_type').val();
        loadVehicleMakes();
    }
});
</script>
@endpush
