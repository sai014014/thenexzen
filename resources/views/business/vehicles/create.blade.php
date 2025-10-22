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
                                @for($year = date('Y') + 1; $year >= 1990; $year--)
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
                            <label for="transmission_type" class="form-label">Transmission Type *</label>
                            <select class="form-select @error('transmission_type') is-invalid @enderror" 
                                    id="transmission_type" 
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
                                        name="seating_capacity">
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
                            <label for="vendor_name" class="form-label">Vendor Name</label>
                            <input type="text" 
                                   class="form-control @error('vendor_name') is-invalid @enderror" 
                                   id="vendor_name" 
                                   name="vendor_name" 
                                   value="{{ old('vendor_name') }}">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const vehicleTypeSelect = document.getElementById('vehicle_type');
    const carSpecs = document.getElementById('car_specs');
    const bikeSpecs = document.getElementById('bike_specs');
    const heavyVehicleSpecs = document.getElementById('heavy_vehicle_specs');
    const transmissionCarHeavy = document.getElementById('transmission_car_heavy');
    const transmissionBike = document.getElementById('transmission_bike');
    const ownershipTypeSelect = document.getElementById('ownership_type');
    const vendorFields = document.getElementById('vendor_fields');
    const commissionTypeField = document.getElementById('commission_type_field');
    const commissionValueField = document.getElementById('commission_value_field');

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
        
        // Ensure seating capacity is included if vehicle type is car
        const vehicleType = document.getElementById('vehicle_type').value;
        if (vehicleType === 'car') {
            const seatingCapacity = document.getElementById('seating_capacity').value;
            if (seatingCapacity) {
                formData.set('seating_capacity', seatingCapacity);
            }
        }
        
        console.log('Form action:', this.action);
        console.log('CSRF Token:', csrfToken);
        console.log('Form data:', Object.fromEntries(formData));
        console.log('Vehicle type:', document.getElementById('vehicle_type').value);
        console.log('Seating capacity:', document.getElementById('seating_capacity').value);
        console.log('Car specs visible:', document.getElementById('car_specs').style.display);
        
        fetch(this.action, {
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
                return response.text().then(text => {
                    console.error('Server error response:', text);
                    throw new Error('Server error: ' + response.status + ' - ' + text);
                });
            }
        })
        .then(data => {
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
});
</script>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let vehicleMakes = [];
    let vehicleModels = [];
    let currentVehicleType = '';

    // Initialize Select2 for better search experience
    $('#vehicle_make').select2({
        placeholder: 'Search and select vehicle make...',
        allowClear: true,
        width: '100%'
    });

    $('#vehicle_model').select2({
        placeholder: 'Search and select vehicle model...',
        allowClear: true,
        width: '100%'
    });

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
            url: '{{ url("business/api/vehicle-makes") }}',
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

        // Find the make ID
        const make = vehicleMakes.find(m => m.name === makeName);
        if (!make) {
            $('#vehicle_model').empty().append('<option value="">Select Vehicle Model</option>').prop('disabled', true);
            return;
        }

        $('#vehicle_model').prop('disabled', false);

        $.ajax({
            url: '{{ url("business/api/vehicle-models") }}',
            method: 'GET',
            data: { make_id: make.id },
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
