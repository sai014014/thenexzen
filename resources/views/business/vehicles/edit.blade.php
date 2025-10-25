@extends('business.layouts.app')

@section('title', 'Edit Vehicle - ' . $vehicle->vehicle_make . ' ' . $vehicle->vehicle_model)
@section('page-title', 'Edit Vehicle')

@push('styles')
<style>
/* Vehicle Edit Page Specific Styles */
.vehicle-add-container {
    background-color: #f8f9fa;
    min-height: 100vh;
    padding: 20px 0;
}

.form-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 24px;
    padding: 24px;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 20px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e9ecef;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 6px;
    font-size: 14px;
}

.form-label.required::after {
    content: " *";
    color: #dc3545;
}

.form-control, .form-select {
    border: 1px solid #ced4da;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 12px;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #6B6ADE;
    box-shadow: 0 0 0 0.2rem rgba(107, 106, 222, 0.25);
}

.helper-text {
    font-size: 12px;
    color: #6c757d;
    margin-top: 10px;
}

.file-upload-area {
    border: 2px dashed #ced4da;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    background-color: #f8f9fa;
    transition: all 0.2s ease;
    cursor: pointer;
}

.file-upload-area:hover {
    border-color: #6B6ADE;
    background-color: #f0f0ff;
}

.file-upload-area.dragover {
    border-color: #6B6ADE;
    background-color: #f0f0ff;
}

.upload-icon {
    font-size: 24px;
    color: #6c757d;
    margin-bottom: 8px;
}

.upload-text {
    color: #6c757d;
    font-size: 12px;
    margin: 0;
}

.save-button {
    background: linear-gradient(135deg, #6B6ADE 0%, #3C3CE1 100%);
    border: none;
    border-radius: 8px;
    padding: 15px 60px;
    color: white;
    font-weight: 500;
    font-size: 16px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(107, 106, 222, 0.3);
}

.save-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(107, 106, 222, 0.4);
    color: white;
}

.cancel-button {
    background: #919191;
    border: none;
    text-decoration:none;
    border-radius: 8px;
    padding: 15px 24px;
    color: white;
    font-weight: 500;
    font-size: 16px;
    transition: all 0.3s ease;
    margin-right: 12px;
}

.cancel-button:hover {
    background: #5a6268;
    color: white;
}

.image-preview-container {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 12px;
}

.image-preview {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
    border: 2px solid #e9ecef;
}

.capacity-badge {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    display: inline-block;
    margin-bottom: 20px;
}

.capacity-badge.warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.capacity-badge.danger {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
}

/* Vendor Dropdown Styles */
.vendor-dropdown-container {
    position: relative;
    z-index: 1;
}

.vendor-dropdown-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.vendor-search-input {
    padding-right: 40px;
    cursor: pointer;
}

.vendor-dropdown-arrow {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #6c757d;
    transition: transform 0.2s ease;
}

.vendor-dropdown-container.active .vendor-dropdown-arrow {
    transform: translateY(-50%) rotate(180deg);
}

.vendor-dropdown-options {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1;
    background: white;
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    max-height: 250px;
    overflow: hidden;
}

.vendor-search-box {
    padding: 8px;
    border-bottom: 1px solid #e9ecef;
    background: #f8f9fa;
}

.vendor-search-filter {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 6px 10px;
    font-size: 12px;
}

.vendor-search-filter:focus {
    border-color: #6B6ADE;
    box-shadow: 0 0 0 0.2rem rgba(107, 106, 222, 0.25);
}

.vendor-options-list {
    max-height: 200px;
    overflow-y: auto;
}

.vendor-option {
    padding: 10px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
    font-size: 12px;
    transition: background-color 0.2s ease;
}

.vendor-option:hover {
    background-color: #f8f9fa;
}

.vendor-option.selected {
    background-color: #e3f2fd;
    color: #1976d2;
}

.vendor-option:last-child {
    border-bottom: none;
}

.vendor-no-results {
    padding: 10px 12px;
    color: #6c757d;
    font-style: italic;
    font-size: 12px;
    text-align: center;
}

.vendor-dropdown-footer {
    padding: 8px;
    border-top: 1px solid #e9ecef;
    background: #f8f9fa;
    text-align: center;
}

.vendor-add-btn {
    width: 100%;
    font-size: 11px;
    padding: 6px 12px;
    border-radius: 4px;
}

.vendor-add-btn:hover {
    background-color: #6B6ADE;
    border-color: #6B6ADE;
    color: white;
}

.vendor-no-results .fa-spinner {
    color: #6B6ADE;
}


/* Vendor Quick Add Modal Styles */
#vendorQuickAddModal .modal-header {
    background: linear-gradient(135deg, #6B6ADE 0%, #8B5CF6 100%);
    color: white;
    border-bottom: none;
}

#vendorQuickAddModal .modal-header .btn-close {
    filter: invert(1);
}

#vendorQuickAddModal .form-label.required::after {
    content: " *";
    color: #dc3545;
}

#vendorQuickAddModal .form-control,
#vendorQuickAddModal .form-select {
    border-radius: 8px;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

#vendorQuickAddModal .form-control:focus,
#vendorQuickAddModal .form-select:focus {
    border-color: #6B6ADE;
    box-shadow: 0 0 0 0.2rem rgba(107, 106, 222, 0.25);
}

#vendorQuickAddModal .btn-primary {
    background: linear-gradient(135deg, #6B6ADE 0%, #8B5CF6 100%);
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
}

#vendorQuickAddModal .btn-primary:hover {
    background: linear-gradient(135deg, #5A5AC8 0%, #7C3AED 100%);
    transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-section {
        padding: 16px;
        margin-bottom: 16px;
    }
    
    .section-title {
        font-size: 16px;
        margin-bottom: 16px;
    }
    
    .save-button, .cancel-button {
        width: 100%;
        margin-bottom: 8px;
    }
}
</style>
@endpush

@section('content')
<div class="vehicle-add-container">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <form method="POST" action="{{ route('business.vehicles.update', $vehicle) }}" enctype="multipart/form-data" id="vehicleForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Error Display Section -->
                    <div id="errorDisplay" class="alert alert-danger" style="display: none;">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:
                            </h6>
                        <div id="errorList"></div>
                    </div>
                    
                    <!-- Section 1: Vehicle Type & General Information -->
                    <div class="form-section">
                        <h3 class="section-title">Vehicle Type & General Information</h3>

                    <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vehicle_type" class="form-label required">Vehicle Type</label>
                            <select class="form-select @error('vehicle_type') is-invalid @enderror" 
                                    id="vehicle_type" 
                                    name="vehicle_type" 
                                    required>
                                <option value="">Select Vehicle Type</option>
                                <option value="car" {{ old('vehicle_type', $vehicle->vehicle_type) == 'car' ? 'selected' : '' }}>Car</option>
                                <option value="bike_scooter" {{ old('vehicle_type', $vehicle->vehicle_type) == 'bike_scooter' ? 'selected' : '' }}>Bike/Scooter</option>
                                <option value="heavy_vehicle" {{ old('vehicle_type', $vehicle->vehicle_type) == 'heavy_vehicle' ? 'selected' : '' }}>Heavy Vehicle</option>
                            </select>
                            @error('vehicle_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vehicle_make" class="form-label required">Vehicle Make</label>
                            <select class="form-select @error('vehicle_make') is-invalid @enderror" 
                                    id="vehicle_make" 
                                    name="vehicle_make" 
                                    required>
                                <option value="">Select Vehicle Make</option>
                                        @if($vehicle->vehicle_type)
                                            @php
                                                $vehicleData = [
                                                    'car' => [
                                                        'Maruti Suzuki' => ['Swift', 'Dzire', 'Baleno', 'Celerio', 'Wagon R', 'Ertiga', 'Vitara Brezza', 'S-Cross'],
                                                        'Hyundai' => ['i10', 'i20', 'Verna', 'Creta', 'Elantra', 'Tucson', 'Venue', 'Aura'],
                                                        'Tata' => ['Tiago', 'Tigor', 'Nexon', 'Harrier', 'Safari', 'Altroz', 'Punch', 'Punch EV'],
                                                        'Mahindra' => ['XUV300', 'XUV500', 'Scorpio', 'Bolero', 'Thar', 'Marazzo', 'KUV100', 'TUV300'],
                                                        'Honda' => ['City', 'Amaze', 'WR-V', 'Jazz', 'Civic', 'CR-V', 'BR-V', 'Brio'],
                                                        'Toyota' => ['Innova', 'Fortuner', 'Camry', 'Corolla', 'Etios', 'Glanza', 'Urban Cruiser', 'Vellfire'],
                                                        'Ford' => ['EcoSport', 'Figo', 'Aspire', 'Endeavour', 'Freestyle', 'Mustang'],
                                                        'Nissan' => ['Micra', 'Sunny', 'Terrano', 'Kicks', 'Magnite', 'GT-R'],
                                                        'Volkswagen' => ['Polo', 'Vento', 'Tiguan', 'Passat', 'Jetta', 'T-Cross'],
                                                        'Skoda' => ['Rapid', 'Octavia', 'Superb', 'Kodiaq', 'Kushaq', 'Slavia']
                                                    ],
                                                    'bike_scooter' => [
                                                        'Honda' => ['Activa', 'Dio', 'Shine', 'Unicorn', 'CB Hornet', 'CB Shine', 'Grazia', 'CB350'],
                                                        'Bajaj' => ['Pulsar', 'Discover', 'Platina', 'Avenger', 'Dominar', 'CT100', 'Pulsar NS', 'Pulsar RS'],
                                                        'TVS' => ['Apache', 'Jupiter', 'Scooty', 'Star City', 'Sport', 'NTorq', 'Raider', 'Ronin'],
                                                        'Hero' => ['Splendor', 'Passion', 'Glamour', 'Xtreme', 'Karizma', 'Duet', 'Maestro', 'Destini'],
                                                        'Yamaha' => ['FZ', 'R15', 'Fascino', 'Ray ZR', 'MT-15', 'R3', 'FZ25', 'Aerox'],
                                                        'Royal Enfield' => ['Classic', 'Bullet', 'Thunderbird', 'Himalayan', 'Interceptor', 'Continental GT', 'Meteor', 'Hunter'],
                                                        'KTM' => ['Duke', 'RC', 'Adventure', '390 Duke', '200 Duke', '125 Duke', 'RC 200', 'RC 390'],
                                                        'Suzuki' => ['Gixxer', 'Access', 'Burgman', 'Intruder', 'Hayabusa', 'V-Strom', 'GSX-R', 'Bandit']
                                                    ],
                                                    'heavy_vehicle' => [
                                                        'Tata' => ['Ace', 'Intra', 'Yodha', 'LPT', 'Prima', 'Signa', 'Ultra', 'Winger'],
                                                        'Mahindra' => ['Bolero Pickup', 'Supro', 'Jeeto', 'Alfa', 'Furio', 'Blazo', 'Cargo', 'Tourister'],
                                                        'Ashok Leyland' => ['Dost', 'Partner', 'Boss', 'Captain', 'Stag', 'Titan', 'Comet', 'Ecomet'],
                                                        'Eicher' => ['Pro', 'Skyline', 'Multix', 'Cruiser', 'Truck', 'Bus', 'Tractor', 'Pickup'],
                                                        'Force' => ['Traveller', 'Gurkha', 'Urbania', 'Trax', 'Tempo', 'Minibus', 'Pickup', 'Truck'],
                                                        'BharatBenz' => ['Truck', 'Bus', 'Tipper', 'Cargo', 'Haulage', 'Construction', 'Mining', 'Logistics']
                                                    ]
                                                ];
                                                $makes = $vehicleData[$vehicle->vehicle_type] ?? [];
                                            @endphp
                                            @foreach($makes as $make => $models)
                                                <option value="{{ $make }}" {{ old('vehicle_make', $vehicle->vehicle_make) == $make ? 'selected' : '' }}>{{ $make }}</option>
                                            @endforeach
                                            @if($vehicle->vehicle_make && !isset($makes[$vehicle->vehicle_make]))
                                                <option value="{{ $vehicle->vehicle_make }}" selected style="font-weight: bold;">{{ $vehicle->vehicle_make }}</option>
                                            @endif
                                        @endif
                            </select>
                            @error('vehicle_make')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                        </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vehicle_model" class="form-label required">Vehicle Model</label>
                            <select class="form-select @error('vehicle_model') is-invalid @enderror" 
                                    id="vehicle_model" 
                                    name="vehicle_model" 
                                    required 
                                            {{ !$vehicle->vehicle_make ? 'disabled' : '' }}>
                                <option value="">Select Vehicle Model</option>
                                        @if($vehicle->vehicle_type && $vehicle->vehicle_make)
                                            @php
                                                $vehicleData = [
                                                    'car' => [
                                                        'Maruti Suzuki' => ['Swift', 'Dzire', 'Baleno', 'Celerio', 'Wagon R', 'Ertiga', 'Vitara Brezza', 'S-Cross'],
                                                        'Hyundai' => ['i10', 'i20', 'Verna', 'Creta', 'Elantra', 'Tucson', 'Venue', 'Aura'],
                                                        'Tata' => ['Tiago', 'Tigor', 'Nexon', 'Harrier', 'Safari', 'Altroz', 'Punch', 'Punch EV'],
                                                        'Mahindra' => ['XUV300', 'XUV500', 'Scorpio', 'Bolero', 'Thar', 'Marazzo', 'KUV100', 'TUV300'],
                                                        'Honda' => ['City', 'Amaze', 'WR-V', 'Jazz', 'Civic', 'CR-V', 'BR-V', 'Brio'],
                                                        'Toyota' => ['Innova', 'Fortuner', 'Camry', 'Corolla', 'Etios', 'Glanza', 'Urban Cruiser', 'Vellfire'],
                                                        'Ford' => ['EcoSport', 'Figo', 'Aspire', 'Endeavour', 'Freestyle', 'Mustang'],
                                                        'Nissan' => ['Micra', 'Sunny', 'Terrano', 'Kicks', 'Magnite', 'GT-R'],
                                                        'Volkswagen' => ['Polo', 'Vento', 'Tiguan', 'Passat', 'Jetta', 'T-Cross'],
                                                        'Skoda' => ['Rapid', 'Octavia', 'Superb', 'Kodiaq', 'Kushaq', 'Slavia']
                                                    ],
                                                    'bike_scooter' => [
                                                        'Honda' => ['Activa', 'Dio', 'Shine', 'Unicorn', 'CB Hornet', 'CB Shine', 'Grazia', 'CB350'],
                                                        'Bajaj' => ['Pulsar', 'Discover', 'Platina', 'Avenger', 'Dominar', 'CT100', 'Pulsar NS', 'Pulsar RS'],
                                                        'TVS' => ['Apache', 'Jupiter', 'Scooty', 'Star City', 'Sport', 'NTorq', 'Raider', 'Ronin'],
                                                        'Hero' => ['Splendor', 'Passion', 'Glamour', 'Xtreme', 'Karizma', 'Duet', 'Maestro', 'Destini'],
                                                        'Yamaha' => ['FZ', 'R15', 'Fascino', 'Ray ZR', 'MT-15', 'R3', 'FZ25', 'Aerox'],
                                                        'Royal Enfield' => ['Classic', 'Bullet', 'Thunderbird', 'Himalayan', 'Interceptor', 'Continental GT', 'Meteor', 'Hunter'],
                                                        'KTM' => ['Duke', 'RC', 'Adventure', '390 Duke', '200 Duke', '125 Duke', 'RC 200', 'RC 390'],
                                                        'Suzuki' => ['Gixxer', 'Access', 'Burgman', 'Intruder', 'Hayabusa', 'V-Strom', 'GSX-R', 'Bandit']
                                                    ],
                                                    'heavy_vehicle' => [
                                                        'Tata' => ['Ace', 'Intra', 'Yodha', 'LPT', 'Prima', 'Signa', 'Ultra', 'Winger'],
                                                        'Mahindra' => ['Bolero Pickup', 'Supro', 'Jeeto', 'Alfa', 'Furio', 'Blazo', 'Cargo', 'Tourister'],
                                                        'Ashok Leyland' => ['Dost', 'Partner', 'Boss', 'Captain', 'Stag', 'Titan', 'Comet', 'Ecomet'],
                                                        'Eicher' => ['Pro', 'Skyline', 'Multix', 'Cruiser', 'Truck', 'Bus', 'Tractor', 'Pickup'],
                                                        'Force' => ['Traveller', 'Gurkha', 'Urbania', 'Trax', 'Tempo', 'Minibus', 'Pickup', 'Truck'],
                                                        'BharatBenz' => ['Truck', 'Bus', 'Tipper', 'Cargo', 'Haulage', 'Construction', 'Mining', 'Logistics']
                                                    ]
                                                ];
                                                $models = $vehicleData[$vehicle->vehicle_type][$vehicle->vehicle_make] ?? [];
                                            @endphp
                                            @foreach($models as $model)
                                                <option value="{{ $model }}" {{ old('vehicle_model', $vehicle->vehicle_model) == $model ? 'selected' : '' }}>{{ $model }}</option>
                                            @endforeach
                                            @if($vehicle->vehicle_model && !in_array($vehicle->vehicle_model, $models))
                                                <option value="{{ $vehicle->vehicle_model }}" selected style="font-weight: bold;">{{ $vehicle->vehicle_model }}</option>
                                            @endif
                                        @endif
                            </select>
                            @error('vehicle_model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                        </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vehicle_year" class="form-label required">Vehicle Year</label>
                                    <input type="number" 
                                           class="form-control @error('vehicle_year') is-invalid @enderror" 
                                    id="vehicle_year" 
                                    name="vehicle_year" 
                                           value="{{ old('vehicle_year', $vehicle->vehicle_year) }}" 
                                           min="1990" 
                                           max="{{ date('Y') + 1 }}"
                                           placeholder="2012"
                                    required>
                            @error('vehicle_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                        </div>
                    </div>

                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicle_number" class="form-label required">Registration Number</label>
                            <input type="text" 
                                   class="form-control @error('vehicle_number') is-invalid @enderror" 
                                   id="vehicle_number" 
                                   name="vehicle_number" 
                                   value="{{ old('vehicle_number', $vehicle->vehicle_number) }}" 
                                           placeholder="MH12AB1234"
                                   required>
                            @error('vehicle_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vin_number" class="form-label required">Vehicle Identification Number (VIN/Chassis Number)</label>
                            <input type="text" 
                                   class="form-control @error('vin_number') is-invalid @enderror" 
                                   id="vin_number" 
                                   name="vin_number" 
                                   value="{{ old('vin_number', $vehicle->vin_number ?? '') }}" 
                                           placeholder="Enter VIN/Chassis Number"
                                    required>
                            @error('vin_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fuel_type" class="form-label required">Fuel Type</label>
                            <select class="form-select @error('fuel_type') is-invalid @enderror" 
                                    id="fuel_type" 
                                    name="fuel_type" 
                                    required>
                                <option value="">Select Fuel Type</option>
                                <option value="petrol" {{ old('fuel_type', $vehicle->fuel_type) == 'petrol' ? 'selected' : '' }}>Petrol</option>
                                <option value="diesel" {{ old('fuel_type', $vehicle->fuel_type) == 'diesel' ? 'selected' : '' }}>Diesel</option>
                                <option value="cng" {{ old('fuel_type', $vehicle->fuel_type) == 'cng' ? 'selected' : '' }}>CNG</option>
                                <option value="electric" {{ old('fuel_type', $vehicle->fuel_type) == 'electric' ? 'selected' : '' }}>Electric</option>
                                <option value="hybrid" {{ old('fuel_type', $vehicle->fuel_type) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                            </select>
                            @error('fuel_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                        </div>
                    </div>

                    <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                            <label for="mileage" class="form-label">Mileage (km/l)</label>
                            <input type="number" 
                                   class="form-control @error('mileage') is-invalid @enderror" 
                                   id="mileage" 
                                   name="mileage" 
                                   value="{{ old('mileage', $vehicle->mileage) }}" 
                                   step="0.01" 
                                           min="0"
                                           placeholder="15.5">
                                    <div class="helper-text">Average fuel efficiency in kilometers per liter</div>
                            @error('mileage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                        </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="transmission_type" class="form-label required">Transmission Type</label>
                            <select class="form-select @error('transmission_type') is-invalid @enderror" 
                                    id="transmission_type" 
                                            name="transmission_type" 
                                            required>
                                        <option value="">Select Transmission Type</option>
                                        <!-- Options will be populated by JavaScript based on vehicle type -->
                            </select>
                            @error('transmission_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Vehicle Type Specific Fields -->
                    <div class="row" id="car-fields" style="display: none;">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="seating_capacity" class="form-label required">Seating Capacity</label>
                                <input type="number" 
                                       class="form-control @error('seating_capacity') is-invalid @enderror" 
                                        id="seating_capacity" 
                                       name="seating_capacity" 
                                       value="{{ old('seating_capacity', $vehicle->seating_capacity) }}" 
                                       min="1"
                                       placeholder="5">
                                <div class="helper-text">Number of seats (for cars)</div>
                                @error('seating_capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row" id="bike-scooter-fields" style="display: none;">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="engine_capacity_cc" class="form-label required">Engine Capacity (CC)</label>
                                <input type="number" 
                                       class="form-control @error('engine_capacity_cc') is-invalid @enderror" 
                                       id="engine_capacity_cc" 
                                       name="engine_capacity_cc" 
                                       value="{{ old('engine_capacity_cc', $vehicle->engine_capacity_cc) }}" 
                                       min="0"
                                       placeholder="150">
                                <div class="helper-text">Engine cubic capacity in CC (for bikes and scooters)</div>
                                @error('engine_capacity_cc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row" id="heavy-vehicle-fields" style="display: none;">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="seating_capacity_heavy" class="form-label">Seating Capacity</label>
                                <input type="number" 
                                       class="form-control @error('seating_capacity') is-invalid @enderror" 
                                        id="seating_capacity_heavy" 
                                       name="seating_capacity" 
                                       value="{{ old('seating_capacity', $vehicle->seating_capacity) }}" 
                                       min="1"
                                       placeholder="5">
                                <div class="helper-text">Number of seats (for heavy vehicles)</div>
                                @error('seating_capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="payload_capacity_tons" class="form-label">Payload Capacity (Tons)</label>
                                <input type="number" 
                                       class="form-control @error('payload_capacity_tons') is-invalid @enderror" 
                                       id="payload_capacity_tons" 
                                       name="payload_capacity_tons" 
                                       value="{{ old('payload_capacity_tons', $vehicle->payload_capacity_tons) }}" 
                                       step="0.01"
                                       min="0" 
                                       placeholder="2.5">
                                <div class="helper-text">Weight capacity in tons (for heavy vehicles)</div>
                                @error('payload_capacity_tons')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Vehicle Images -->
                    <div class="form-section">
                        <h3 class="section-title">Vehicle Images</h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">Vehicle Images</label>
                                    <div class="file-upload-area" onclick="document.getElementById('vehicle_images').click()">
                                        <div class="upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <div class="upload-text">
                                            <strong>Click to upload</strong> or drag and drop<br>
                                            <small>PNG, JPG, JPEG up to 10MB each (Max 5 images)</small>
                                        </div>
                                    </div>
                                    <input type="file" 
                                           id="vehicle_images" 
                                           name="vehicle_images[]" 
                                           multiple 
                                           accept="image/*" 
                                           style="display: none;"
                                           onchange="previewImages(this)">
                                    <div class="helper-text">Upload clear, high-quality images of your vehicle from different angles</div>
                                    @error('vehicle_images')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                        </div>
                    </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Current Images</label>
                                    <div class="image-preview-container" id="currentImages">
                                        @if($vehicle->images && count($vehicle->images) > 0)
                                            @foreach($vehicle->images as $image)
                                                <div class="image-preview-item">
                                                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                                                         alt="Vehicle Image" 
                                                         class="image-preview">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger mt-1" 
                                                            onclick="removeCurrentImage({{ $image->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted">No images uploaded yet</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Rental Information -->
                    <div class="form-section">
                        <h3 class="section-title">Rental Information</h3>

                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rental_price_24h" class="form-label required">Rental Price for 24 Hours (Base Price)</label>
                            <input type="number" 
                                   class="form-control @error('rental_price_24h') is-invalid @enderror" 
                                   id="rental_price_24h" 
                                   name="rental_price_24h" 
                                   value="{{ old('rental_price_24h', $vehicle->rental_price_24h) }}" 
                                   step="0.01" 
                                           min="0"
                                           placeholder="Enter Amount">
                                    <div class="helper-text">The default price for renting the vehicle for a 24-hour period</div>
                            @error('rental_price_24h')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                        </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="km_limit_per_booking" class="form-label required">Kilometre Limit per Booking (Base Limit)</label>
                            <input type="number" 
                                   class="form-control @error('km_limit_per_booking') is-invalid @enderror" 
                                   id="km_limit_per_booking" 
                                   name="km_limit_per_booking" 
                                   value="{{ old('km_limit_per_booking', $vehicle->km_limit_per_booking) }}" 
                                           min="0"
                                           placeholder="0">
                                    <div class="helper-text">The maximum number of kilometres included in the base price for a single booking (usually for 24 hours).</div>
                            @error('km_limit_per_booking')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="extra_rental_price_per_hour" class="form-label required">Extra Rental Price per Hour (if extended)</label>
                            <input type="number" 
                                   class="form-control @error('extra_rental_price_per_hour') is-invalid @enderror" 
                                   id="extra_rental_price_per_hour" 
                                   name="extra_rental_price_per_hour" 
                                   value="{{ old('extra_rental_price_per_hour', $vehicle->extra_rental_price_per_hour) }}" 
                                   step="0.01" 
                                           min="0"
                                           placeholder="Enter Amount">
                                    <div class="helper-text">The additional charge if the vehicle is kept beyond the booking period (beyond 24 hours or the specified rental time).</div>
                            @error('extra_rental_price_per_hour')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                        </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="extra_price_per_km" class="form-label required">Extra Price per Kilometre (after base km limit)</label>
                            <input type="number" 
                                   class="form-control @error('extra_price_per_km') is-invalid @enderror" 
                                   id="extra_price_per_km" 
                                   name="extra_price_per_km" 
                                   value="{{ old('extra_price_per_km', $vehicle->extra_price_per_km) }}" 
                                   step="0.01" 
                                           min="0"
                                           placeholder="Enter Amount">
                                    <div class="helper-text">The additional charge if the customer drives beyond the kilometer limit set for the booking.</div>
                            @error('extra_price_per_km')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                        </div>
                    </div>

                    <!-- Section 4: Vehicle Ownership & Documents -->
                    <div class="form-section">
                        <h3 class="section-title">Vehicle Ownership & Documents</h3>

                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ownership_type" class="form-label required">Ownership Type</label>
                            <select class="form-select @error('ownership_type') is-invalid @enderror" 
                                    id="ownership_type" 
                                    name="ownership_type" 
                                    required
                                    onchange="toggleVendorFields()">
                                <option value="">Select Ownership Type</option>
                                <option value="owned" {{ old('ownership_type', $vehicle->ownership_type) == 'owned' ? 'selected' : '' }}>Owned</option>
                                <option value="leased" {{ old('ownership_type', $vehicle->ownership_type) == 'leased' ? 'selected' : '' }}>Leased</option>
                                <option value="vendor_provided" {{ old('ownership_type', $vehicle->ownership_type) == 'vendor_provided' ? 'selected' : '' }}>Vendor Provided</option>
                            </select>
                            @error('ownership_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                        </div>

                            <div class="col-md-6 vendor-fields" style="{{ old('ownership_type', $vehicle->ownership_type) == 'vendor_provided' ? '' : 'display: none;' }}">
                                <div class="form-group">
                            <label for="vendor_name" class="form-label">Vendor Name</label>
                                    <div class="vendor-dropdown-container">
                                        <div class="vendor-dropdown-wrapper">
                                <input type="text" 
                                                   class="form-control vendor-search-input @error('vendor_name') is-invalid @enderror" 
                                   id="vendor_name" 
                                   name="vendor_name" 
                                                   value="{{ old('vendor_name', $vehicle->ownership_type == 'vendor_provided' ? $vehicle->vendor_name : '') }}"
                                                   placeholder="Search and select vendor" 
                                       autocomplete="off"
                                                   readonly>
                                            <div class="vendor-dropdown-arrow">
                                                <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                                        <div class="vendor-dropdown-options" style="display: none;">
                                            <div class="vendor-search-box">
                                                <input type="text" 
                                                       class="form-control vendor-search-filter" 
                                                       placeholder="Type to search vendors..." 
                                                       autocomplete="off">
                                            </div>
                                            <div class="vendor-options-list">
                                                <!-- Options will be populated dynamically -->
                                            </div>
                                            <div class="vendor-dropdown-footer">
                                                <button type="button" class="btn btn-sm btn-outline-primary vendor-add-btn" onclick="openAddVendorModal()">
                                                    <i class="fas fa-plus me-1"></i>Add New Vendor
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="helper-text">Required for vendor-provided vehicles</div>
                            @error('vendor_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row vendor-fields" style="{{ old('ownership_type', $vehicle->ownership_type) == 'vendor_provided' ? '' : 'display: none;' }}">
                            <div class="col-md-6">
                                <div class="form-group">
                            <label for="commission_type" class="form-label">Commission Type</label>
                            <select class="form-select @error('commission_type') is-invalid @enderror" 
                                    id="commission_type" 
                                    name="commission_type"
                                    {{ old('ownership_type', $vehicle->ownership_type) == 'vendor_provided' ? 'required' : '' }}>
                                        <option value="">Select Commission Type</option>
                                        <option value="fixed" {{ old('commission_type', $vehicle->commission_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                <option value="percentage" {{ old('commission_type', $vehicle->commission_type) == 'percentage' ? 'selected' : '' }}>Percentage</option>
                            </select>
                                    <div class="helper-text">Required for vendor-provided vehicles</div>
                            @error('commission_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                        </div>

                            <div class="col-md-6">
                                <div class="form-group">
                            <label for="commission_value" class="form-label">Commission Value</label>
                            <input type="number" 
                                   class="form-control @error('commission_value') is-invalid @enderror" 
                                   id="commission_value" 
                                   name="commission_value" 
                                   value="{{ old('commission_value', $vehicle->commission_value) }}" 
                                   step="0.01" 
                                   min="0"
                                   placeholder="Enter commission value"
                                   {{ old('ownership_type', $vehicle->ownership_type) == 'vendor_provided' ? 'required' : '' }}>
                                    <div class="helper-text">Commission amount (₹) or percentage (%) for vendor-provided vehicles</div>
                            @error('commission_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                        </div>
                    </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rc_number" class="form-label required">RC Number</label>
                                    <input type="text" 
                                           class="form-control @error('rc_number') is-invalid @enderror" 
                                           id="rc_number" 
                                           name="rc_number" 
                                           value="{{ old('rc_number', $vehicle->rc_number) }}" 
                                           placeholder="Enter RC number"
                                           required>
                                    @error('rc_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">RC Document</label>
                                    <div class="file-upload-area" onclick="document.getElementById('rc_document').click()">
                                        <div class="upload-icon">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="upload-text">
                                            <strong>Click to upload RC</strong><br>
                                            <small>PDF, JPG, JPEG up to 5MB</small>
                                        </div>
                                    </div>
                                    <input type="file" 
                                           id="rc_document" 
                                           name="rc_document" 
                                           accept=".pdf,.jpg,.jpeg,.png" 
                                           style="display: none;">
                                    <div class="helper-text">Upload the vehicle's Registration Certificate (RC)</div>
                                    @if($vehicle->rc_document_path)
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $vehicle->rc_document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>View Current RC
                                            </a>
                                        </div>
                                    @endif
                                    @error('rc_document')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                        </div>
                    </div>

                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="insurance_provider" class="form-label required">Insurance Provider</label>
                            <input type="text" 
                                   class="form-control @error('insurance_provider') is-invalid @enderror" 
                                   id="insurance_provider" 
                                   name="insurance_provider" 
                                   value="{{ old('insurance_provider', $vehicle->insurance_provider) }}" 
                                           placeholder="Enter insurance company name"
                                   required>
                            @error('insurance_provider')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                        </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="policy_number" class="form-label required">Policy Number</label>
                            <input type="text" 
                                   class="form-control @error('policy_number') is-invalid @enderror" 
                                   id="policy_number" 
                                   name="policy_number" 
                                   value="{{ old('policy_number', $vehicle->policy_number) }}" 
                                           placeholder="Enter policy number"
                                   required>
                            @error('policy_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                        </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rc_number" class="form-label required">RC Number</label>
                            <input type="text" 
                                   class="form-control @error('rc_number') is-invalid @enderror" 
                                   id="rc_number" 
                                   name="rc_number" 
                                   value="{{ old('rc_number', $vehicle->rc_number ?? '') }}" 
                                           placeholder="Enter RC Number"
                                   required>
                            @error('rc_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="insurance_expiry_date" class="form-label required">Insurance Expiry Date</label>
                            <input type="date" 
                                   class="form-control @error('insurance_expiry_date') is-invalid @enderror" 
                                   id="insurance_expiry_date" 
                                   name="insurance_expiry_date" 
                                           value="{{ old('insurance_expiry_date', $vehicle->insurance_expiry_date ? $vehicle->insurance_expiry_date->format('Y-m-d') : '') }}"
                                   required>
                            @error('insurance_expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">Insurance Document</label>
                                    <div class="file-upload-area" onclick="document.getElementById('insurance_document').click()">
                                        <div class="upload-icon">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="upload-text">
                                            <strong>Click to upload Insurance</strong><br>
                                            <small>PDF, JPG, JPEG up to 5MB</small>
                                        </div>
                                    </div>
                            <input type="file" 
                                   id="insurance_document" 
                                   name="insurance_document" 
                                           accept=".pdf,.jpg,.jpeg,.png" 
                                           style="display: none;">
                                    <div class="helper-text">Upload the vehicle's Insurance Certificate</div>
                            @if($vehicle->insurance_document_path)
                                <div class="mt-2">
                                            <a href="{{ asset('storage/' . $vehicle->insurance_document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>View Current Insurance
                                    </a>
                                </div>
                            @endif
                            @error('insurance_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                                </div>
                        </div>
                    </div>

                    <!-- Section 5: Vehicle Maintenance -->
                    <div class="form-section">
                        <h3 class="section-title">Vehicle Maintenance</h3>

                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                            <label for="last_service_date" class="form-label">Last Service Date</label>
                            <input type="date" 
                                   class="form-control @error('last_service_date') is-invalid @enderror" 
                                   id="last_service_date" 
                                   name="last_service_date" 
                                           value="{{ old('last_service_date', $vehicle->last_service_date ? $vehicle->last_service_date->format('Y-m-d') : '') }}">
                                    <div class="helper-text">Date when the vehicle was last serviced</div>
                            @error('last_service_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                        </div>

                            <div class="col-md-6">
                                <div class="form-group">
                            <label for="last_service_meter_reading" class="form-label">Last Service Meter Reading</label>
                            <input type="number" 
                                   class="form-control @error('last_service_meter_reading') is-invalid @enderror" 
                                   id="last_service_meter_reading" 
                                   name="last_service_meter_reading" 
                                   value="{{ old('last_service_meter_reading', $vehicle->last_service_meter_reading) }}" 
                                           min="0"
                                           placeholder="Enter meter reading">
                                    <div class="helper-text">Meter reading at the time of last service</div>
                            @error('last_service_meter_reading')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                            <label for="next_service_due" class="form-label">Next Service Due</label>
                            <input type="date" 
                                   class="form-control @error('next_service_due') is-invalid @enderror" 
                                   id="next_service_due" 
                                   name="next_service_due" 
                                           value="{{ old('next_service_due', $vehicle->next_service_due ? $vehicle->next_service_due->format('Y-m-d') : '') }}">
                                    <div class="helper-text">Expected date for the next service</div>
                            @error('next_service_due')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                                </div>
                        </div>

                            <div class="col-md-6">
                                <div class="form-group">
                            <label for="next_service_meter_reading" class="form-label">Next Service Meter Reading</label>
                            <input type="number" 
                                   class="form-control @error('next_service_meter_reading') is-invalid @enderror" 
                                   id="next_service_meter_reading" 
                                   name="next_service_meter_reading" 
                                   value="{{ old('next_service_meter_reading', $vehicle->next_service_meter_reading) }}" 
                                           min="0"
                                           placeholder="Enter expected meter reading">
                                    <div class="helper-text">Expected meter reading for next service</div>
                            @error('next_service_meter_reading')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        </div>
                    </div>

                    <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="remarks_notes" class="form-label">Remarks & Notes</label>
                            <textarea class="form-control @error('remarks_notes') is-invalid @enderror" 
                                      id="remarks_notes" 
                                      name="remarks_notes" 
                                              rows="4" 
                                              placeholder="Any specific remarks, notes, or instructions about the vehicle...">{{ old('remarks_notes', $vehicle->remarks_notes) }}</textarea>
                                    <div class="helper-text">Any additional notes about the vehicle's condition, special instructions, or remarks</div>
                            @error('remarks_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                        </div>
                    </div>

                    <!-- Section 6: Vehicle Availability & Status -->
                    <div class="form-section">
                        <h3 class="section-title">Vehicle Availability & Status</h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicle_status" class="form-label required">Vehicle Status</label>
                                    <select class="form-select @error('vehicle_status') is-invalid @enderror" 
                                            id="vehicle_status" 
                                            name="vehicle_status" 
                                            required>
                                        <option value="">Select Vehicle Status</option>
                                        <option value="active" {{ old('vehicle_status', $vehicle->vehicle_status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('vehicle_status', $vehicle->vehicle_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="under_maintenance" {{ old('vehicle_status', $vehicle->vehicle_status) == 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                        </select>
                                    @error('vehicle_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                    </div>
                    </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_available" 
                                               name="is_available" 
                                               value="1" 
                                               {{ old('is_available', $vehicle->is_available) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_available">
                                            Vehicle is Available for Booking
                                        </label>
                    </div>
                                    <div class="helper-text">Toggle vehicle availability for new bookings</div>
                                    @error('is_available')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                    </div>
                    </div>
                    </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unavailable_from" class="form-label">Unavailable From</label>
                                    <input type="datetime-local" 
                                           class="form-control @error('unavailable_from') is-invalid @enderror" 
                                           id="unavailable_from" 
                                           name="unavailable_from" 
                                           value="{{ old('unavailable_from', $vehicle->unavailable_from ? $vehicle->unavailable_from->format('Y-m-d\TH:i') : '') }}">
                                    <div class="helper-text">Start date/time when vehicle becomes unavailable</div>
                                    @error('unavailable_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                    </div>
                    </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unavailable_until" class="form-label">Unavailable Until</label>
                                    <input type="datetime-local" 
                                           class="form-control @error('unavailable_until') is-invalid @enderror" 
                                           id="unavailable_until" 
                                           name="unavailable_until" 
                                           value="{{ old('unavailable_until', $vehicle->unavailable_until ? $vehicle->unavailable_until->format('Y-m-d\TH:i') : '') }}">
                                    <div class="helper-text">End date/time when vehicle becomes available again</div>
                                    @error('unavailable_until')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                        </div>
                        </div>
                    </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-2 mb-4">
                        <a href="{{ route('business.vehicles.index') }}" class="cancel-button">
                            Cancel
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save me-2"></i>Update Vehicle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Vendor Quick Add Modal -->
<div class="modal fade" id="vendorQuickAddModal" tabindex="-1" aria-labelledby="vendorQuickAddModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vendorQuickAddModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Add New Vendor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="vendorQuickAddForm">
            <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="quick_vendor_name" class="form-label required">Vendor Name</label>
                                <input type="text" class="form-control" id="quick_vendor_name" name="vendor_name" required>
                    </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="quick_vendor_type" class="form-label required">Vendor Type</label>
                                <select class="form-select" id="quick_vendor_type" name="vendor_type" required>
                            <option value="">Select Type</option>
                                    <option value="vehicle_provider">Vehicle Provider</option>
                                    <option value="service_partner">Service Partner</option>
                                    <option value="other">Other</option>
                        </select>
                    </div>
                    </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="quick_primary_contact" class="form-label required">Primary Contact Person</label>
                                <input type="text" class="form-control" id="quick_primary_contact" name="primary_contact_person" required>
                    </div>
                    </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="quick_mobile_number" class="form-label required">Mobile Number</label>
                                <input type="text" class="form-control" id="quick_mobile_number" name="mobile_number" required>
                    </div>
                    </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="quick_email" class="form-label required">Email Address</label>
                                <input type="email" class="form-control" id="quick_email" name="email_address" required>
                        </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="quick_pan_number" class="form-label required">PAN Number</label>
                                <input type="text" class="form-control" id="quick_pan_number" name="pan_number" maxlength="10" required>
                    </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="quick_office_address" class="form-label required">Office Address</label>
                                <textarea class="form-control" id="quick_office_address" name="office_address" rows="2" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="quick_commission_type" class="form-label required">Commission Type</label>
                                <select class="form-select" id="quick_commission_type" name="commission_type" required>
                            <option value="">Select Type</option>
                                    <option value="fixed_amount">Fixed Amount</option>
                            <option value="percentage_of_revenue">Percentage of Revenue</option>
                        </select>
                    </div>
                    </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="quick_commission_rate" class="form-label required">Commission Rate</label>
                                <input type="number" class="form-control" id="quick_commission_rate" name="commission_rate" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Vendor
                    </button>
            </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Global variables for vehicle data
let currentVehicleType = '';
let vehicleMakes = [];
let vehicleModels = [];

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    const vehicleTypeSelect = document.getElementById('vehicle_type');
    const vehicleMakeSelect = document.getElementById('vehicle_make');
    const vehicleModelSelect = document.getElementById('vehicle_model');
    
    // Function to toggle vehicle type specific fields
    function toggleVehicleTypeFields() {
        const vehicleType = vehicleTypeSelect.value;
        
        // Hide all vehicle type specific fields
        document.getElementById('car-fields').style.display = 'none';
        document.getElementById('bike-scooter-fields').style.display = 'none';
        document.getElementById('heavy-vehicle-fields').style.display = 'none';
        
        // Show relevant fields based on vehicle type
        if (vehicleType === 'car') {
            document.getElementById('car-fields').style.display = 'block';
        } else if (vehicleType === 'bike_scooter') {
            document.getElementById('bike-scooter-fields').style.display = 'block';
        } else if (vehicleType === 'heavy_vehicle') {
            document.getElementById('heavy-vehicle-fields').style.display = 'block';
        }
    }
    
    // Initialize field visibility on page load
    toggleVehicleTypeFields();
    
    // Add event listener for vehicle type changes
    vehicleTypeSelect.addEventListener('change', toggleVehicleTypeFields);
    
    // Set current values
    const currentType = '{{ $vehicle->vehicle_type }}';
    const currentMake = '{{ $vehicle->vehicle_make }}';
    const currentModel = '{{ $vehicle->vehicle_model }}';
    
    // Initialize with current values
    if (currentType && currentType !== '') {
        currentVehicleType = currentType;
        loadVehicleMakes(currentMake, currentModel);
        populateTransmissionOptions();
    }
    
    // Vehicle type change handler
    vehicleTypeSelect.addEventListener('change', function() {
        currentVehicleType = this.value;
        loadVehicleMakes();
        toggleVehicleTypeFields();
        populateTransmissionOptions();
    });
    
    // Vehicle make change handler
    vehicleMakeSelect.addEventListener('change', function() {
        const selectedMake = this.value;
        loadVehicleModels(selectedMake);
    });
    
    // Load vehicle makes based on type
    function loadVehicleMakes(targetMake = null, targetModel = null) {
        if (!currentVehicleType) {
            vehicleMakeSelect.innerHTML = '<option value="">Select Vehicle Make</option>';
            vehicleModelSelect.innerHTML = '<option value="">Select Vehicle Model</option>';
            vehicleModelSelect.disabled = true;
            refreshCustomDropdowns();
        return;
    }

        vehicleMakeSelect.disabled = false;
        vehicleModelSelect.innerHTML = '<option value="">Select Vehicle Model</option>';
        vehicleModelSelect.disabled = true;

        $.ajax({
            url: '{{ route('business.api.vehicle-makes') }}',
            method: 'GET',
            data: { type: currentVehicleType },
            success: function(response) {
                vehicleMakes = response;
                vehicleMakeSelect.innerHTML = '<option value="">Select Vehicle Make</option>';
                
                response.forEach(function(make) {
                    const option = document.createElement('option');
                    option.value = make.name;
                    option.textContent = make.name;
                    if (targetMake && make.name === targetMake) {
                        option.selected = true;
                    }
                    vehicleMakeSelect.appendChild(option);
                });
                
                // If target make is not in the response, add it
                if (targetMake && !response.find(make => make.name === targetMake)) {
                    const option = document.createElement('option');
                    option.value = targetMake;
                    option.textContent = targetMake;
                    option.selected = true;
                    option.style.fontWeight = 'bold'; // Highlight custom make
                    vehicleMakeSelect.appendChild(option);
                }
                
                // Refresh custom dropdowns after updating options
                refreshCustomDropdowns();
                
                // If we have a target make, load models
                if (targetMake) {
                    loadVehicleModels(targetMake, targetModel);
                }
            },
            error: function(xhr, status, error) {
                console.error('Edit page - Error loading vehicle makes:', error);
                alert('Error loading vehicle makes. Please try again.');
            }
        });
    }

    // Load vehicle models based on make
    function loadVehicleModels(makeName, targetModel = null) {
        if (!makeName) {
            vehicleModelSelect.innerHTML = '<option value="">Select Vehicle Model</option>';
            vehicleModelSelect.disabled = true;
            refreshCustomDropdowns();
            return;
        }

        vehicleModelSelect.disabled = false;

        $.ajax({
            url: '{{ route('business.api.vehicle-models') }}',
            method: 'GET',
            data: { make_name: makeName },
            success: function(response) {
                vehicleModels = response;
                vehicleModelSelect.innerHTML = '<option value="">Select Vehicle Model</option>';
                
                response.forEach(function(model) {
                    const option = document.createElement('option');
                    option.value = model.name;
                    option.textContent = model.name;
                    if (targetModel && model.name === targetModel) {
                        option.selected = true;
                    }
                    vehicleModelSelect.appendChild(option);
                });
                
                // If target model is not in the response, add it
                if (targetModel && !response.find(model => model.name === targetModel)) {
                    const option = document.createElement('option');
                    option.value = targetModel;
                    option.textContent = targetModel;
                    option.selected = true;
                    option.style.fontWeight = 'bold'; // Highlight custom model
                    vehicleModelSelect.appendChild(option);
                }
                
                // Refresh custom dropdowns after updating options
                refreshCustomDropdowns();
            },
            error: function(xhr, status, error) {
                console.error('Edit page - Error loading vehicle models:', error);
                alert('Error loading vehicle models. Please try again.');
            }
        });
    }

    // Populate transmission type options based on vehicle type
    function populateTransmissionOptions() {
        const vehicleType = vehicleTypeSelect.value;
        const mainTransmission = document.getElementById('transmission_type');
        
        if (!mainTransmission) return;
        
        // Clear existing options
        mainTransmission.innerHTML = '<option value="">Select Transmission Type</option>';
        
        if (vehicleType === 'car' || vehicleType === 'heavy_vehicle') {
            // Cars and Heavy Vehicles: Manual, Automatic, Hybrid (Mandatory)
            mainTransmission.innerHTML += '<option value="manual">Manual</option>';
            mainTransmission.innerHTML += '<option value="automatic">Automatic</option>';
            mainTransmission.innerHTML += '<option value="hybrid">Hybrid</option>';
            mainTransmission.required = true;
            mainTransmission.closest('.form-group').querySelector('label').classList.add('required');
        } else if (vehicleType === 'bike_scooter') {
            // Bikes/Scooters: Gear, Gearless (Optional)
            mainTransmission.innerHTML += '<option value="gear">Gear</option>';
            mainTransmission.innerHTML += '<option value="gearless">Gearless</option>';
            mainTransmission.required = false;
            mainTransmission.closest('.form-group').querySelector('label').classList.remove('required');
        }
        
        // Set current value if exists
        const currentTransmission = '{{ $vehicle->transmission_type }}';
        if (currentTransmission) {
            mainTransmission.value = currentTransmission;
        }
        
        // Refresh custom dropdowns
        refreshCustomDropdowns();
    }
});

// Vendor dropdown search functionality
document.addEventListener('DOMContentLoaded', function() {
    const vendorInput = document.getElementById('vendor_name');
    const vendorContainer = document.querySelector('.vendor-dropdown-container');
    const vendorOptions = document.querySelector('.vendor-dropdown-options');
    const vendorSearchFilter = document.querySelector('.vendor-search-filter');
    const vendorOptionsList = document.querySelector('.vendor-options-list');
    
    // Vendor data - will be populated from database
    let vendorData = [];
    let filteredVendors = [];
    let selectedIndex = -1;
    let searchTimeout;
    
    if (vendorInput && vendorContainer && vendorOptions && vendorSearchFilter && vendorOptionsList) {
        // Initialize dropdown
        loadVendors();
        
        // Toggle dropdown
        vendorInput.addEventListener('click', function() {
            toggleDropdown();
        });
        
        // Handle search filter with debounce
        vendorSearchFilter.addEventListener('input', function() {
            const searchTerm = this.value.trim();
            
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            if (searchTerm.length === 0) {
                filteredVendors = [...vendorData];
                renderVendorOptions();
                return;
            }
            
            // Debounce search to avoid too many API calls
            searchTimeout = setTimeout(() => {
                searchVendors(searchTerm);
            }, 300);
        });
        
        // Handle keyboard navigation
        vendorSearchFilter.addEventListener('keydown', function(e) {
            switch(e.key) {
                case 'ArrowDown':
        e.preventDefault();
                    selectedIndex = Math.min(selectedIndex + 1, filteredVendors.length - 1);
                    updateSelection();
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    selectedIndex = Math.max(selectedIndex - 1, -1);
                    updateSelection();
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (selectedIndex >= 0 && selectedIndex < filteredVendors.length) {
                        selectVendor(filteredVendors[selectedIndex].vendor_name);
                    }
                    break;
                case 'Escape':
                    closeDropdown();
                    break;
            }
        });
        
        // Handle option clicks
        vendorOptionsList.addEventListener('click', function(e) {
            if (e.target.classList.contains('vendor-option')) {
                selectVendor(e.target.textContent);
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!vendorContainer.contains(e.target)) {
                closeDropdown();
            }
        });
        
        function toggleDropdown() {
            if (vendorOptions.style.display === 'none' || vendorOptions.style.display === '') {
                openDropdown();
        } else {
                closeDropdown();
            }
        }
        
        function openDropdown() {
            vendorOptions.style.display = 'block';
            vendorContainer.classList.add('active');
            vendorSearchFilter.focus();
            vendorSearchFilter.value = '';
            filteredVendors = [...vendorData];
            selectedIndex = -1;
            renderVendorOptions();
        }
        
        function closeDropdown() {
            vendorOptions.style.display = 'none';
            vendorContainer.classList.remove('active');
            selectedIndex = -1;
        }
        
        function renderVendorOptions(loading = false) {
            vendorOptionsList.innerHTML = '';
            
            if (loading) {
                vendorOptionsList.innerHTML = '<div class="vendor-no-results"><i class="fas fa-spinner fa-spin me-2"></i>Loading vendors...</div>';
                return;
            }
            
            if (filteredVendors.length === 0) {
                if (vendorData.length === 0) {
                    vendorOptionsList.innerHTML = '<div class="vendor-no-results">No vendors available. Contact admin to add vendors.</div>';
        } else {
                    vendorOptionsList.innerHTML = '<div class="vendor-no-results">No vendors found matching your search</div>';
                }
                return;
            }
            
            filteredVendors.forEach((vendor, index) => {
                const option = document.createElement('div');
                option.className = 'vendor-option';
                option.textContent = vendor.vendor_name; // Display vendor name
                option.dataset.vendorId = vendor.id; // Store vendor ID for selection
                option.dataset.vendorData = JSON.stringify(vendor); // Store full vendor data
                if (index === selectedIndex) {
                    option.classList.add('selected');
                }
                vendorOptionsList.appendChild(option);
            });
        }
        
        function updateSelection() {
            const options = vendorOptionsList.querySelectorAll('.vendor-option');
            options.forEach((option, index) => {
                option.classList.toggle('selected', index === selectedIndex);
            });
        }
        
        function selectVendor(vendorName) {
            vendorInput.value = vendorName;
            closeDropdown();
            
            // Find the selected vendor object
            const selectedVendor = filteredVendors.find(vendor => vendor.vendor_name === vendorName);
            if (selectedVendor) {
                // Populate commission fields
                const commissionTypeSelect = document.getElementById('commission_type');
                const commissionValueInput = document.getElementById('commission_value');
                
                if (commissionTypeSelect && commissionValueInput) {
                    // Map vendor commission type to form values
                    let commissionTypeValue = '';
                    if (selectedVendor.commission_type === 'fixed_amount') {
                        commissionTypeValue = 'fixed';
                    } else if (selectedVendor.commission_type === 'percentage_of_revenue') {
                        commissionTypeValue = 'percentage';
                    }
                    
                    commissionTypeSelect.value = commissionTypeValue;
                    commissionValueInput.value = selectedVendor.commission_rate || '';
                }
            }
        }
        
        // Load vendors from database
        function loadVendors() {
            renderVendorOptions(true); // Show loading state
            
            fetch('{{ route("business.vendors.search") }}', {
                method: 'GET',
            headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    vendorData = data.vendors; // Store full vendor objects instead of just names
                    filteredVendors = [...vendorData];
                    renderVendorOptions();
            } else {
                    console.error('Failed to load vendors:', data.message);
                    vendorData = [];
                    filteredVendors = [];
                    renderVendorOptions();
                }
            })
            .catch(error => {
                console.error('Error loading vendors:', error);
                vendorData = [];
                filteredVendors = [];
                renderVendorOptions();
            });
        }
        
        // Search vendors with API call
        function searchVendors(searchTerm) {
            renderVendorOptions(true); // Show loading state
            
            fetch('{{ route("business.vendors.search") }}?search=' + encodeURIComponent(searchTerm), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
        .then(data => {
            if (data.success) {
                    filteredVendors = data.vendors; // Store full vendor objects
                    selectedIndex = -1;
                    renderVendorOptions();
            } else {
                    console.error('Failed to search vendors:', data.message);
                    filteredVendors = [];
                    renderVendorOptions();
            }
        })
        .catch(error => {
                console.error('Error searching vendors:', error);
                filteredVendors = [];
                renderVendorOptions();
            });
        }
        
        // Fetch vendor commission details
        function fetchVendorCommissionDetails(vendorName) {
            // Ensure vendor fields are visible first
            const vendorFields = document.querySelectorAll('.vendor-fields');
            vendorFields.forEach(field => {
                field.style.display = '';
            });
            
            // Wait a moment for fields to be visible, then fetch
            setTimeout(() => {
                const commissionTypeSelect = document.getElementById('commission_type');
                const commissionValueInput = document.getElementById('commission_value');
                
                
                if (!commissionTypeSelect || !commissionValueInput) {
                    console.error('Commission fields not found');
            return;
        }

                fetchCommissionData(vendorName, commissionTypeSelect, commissionValueInput);
            }, 100);
        }
        
        // Separate function to fetch commission data
        function fetchCommissionData(vendorName, commissionTypeSelect, commissionValueInput) {
            // Show loading state
            commissionTypeSelect.disabled = true;
            commissionValueInput.disabled = true;
            commissionValueInput.placeholder = 'Loading commission details...';
            
            fetch('{{ route("business.vendors.search") }}?search=' + encodeURIComponent(vendorName), {
            method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.vendors.length > 0) {
                    const vendor = data.vendors.find(v => v.vendor_name === vendorName);
                    if (vendor) {
                        // Map commission type from database to form values
                        let commissionType = '';
                        if (vendor.commission_type === 'fixed_amount') {
                            commissionType = 'fixed';
                        } else if (vendor.commission_type === 'percentage_of_revenue') {
                            commissionType = 'percentage';
                        }
                        
                        // Set commission type
                        commissionTypeSelect.value = commissionType;
                        
                        // Set commission value
                        commissionValueInput.value = vendor.commission_rate || '';
                        
                        // Show success notification
                        showNotification(`Commission details loaded for ${vendorName}`, 'success');
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching vendor commission details:', error);
                showNotification('Failed to load commission details', 'error');
            })
            .finally(() => {
                // Re-enable fields
                commissionTypeSelect.disabled = false;
                commissionValueInput.disabled = false;
                commissionValueInput.placeholder = 'Enter commission value';
            });
        }
    }
});

// Toggle vendor fields based on ownership type
function toggleVendorFields() {
    const ownershipType = document.getElementById('ownership_type').value;
    const vendorFields = document.querySelectorAll('.vendor-fields');
    const vendorNameInput = document.getElementById('vendor_name');
    const commissionType = document.getElementById('commission_type');
    const commissionValue = document.getElementById('commission_value');
    
    if (ownershipType === 'vendor_provided') {
        // Show vendor fields
        vendorFields.forEach(field => {
            field.style.display = '';
        });
        // Make fields required
        if (vendorNameInput) {
            vendorNameInput.required = true;
        }
        if (commissionType) {
            commissionType.required = true;
        }
        if (commissionValue) {
            commissionValue.required = true;
        }
        
        // If a vendor is already selected, fetch their commission details
        if (vendorNameInput && vendorNameInput.value.trim() !== '') {
            fetchVendorCommissionDetails(vendorNameInput.value);
        }
    } else {
        // Hide vendor fields
        vendorFields.forEach(field => {
            field.style.display = 'none';
        });
        // Clear values and make fields not required
        if (vendorNameInput) {
            vendorNameInput.value = '';
            vendorNameInput.required = false;
        }
        if (commissionType) {
            commissionType.value = '';
            commissionType.required = false;
        }
        if (commissionValue) {
            commissionValue.value = '';
            commissionValue.required = false;
        }
    }
}

// Add New Vendor Modal Function
function openAddVendorModal() {
    // Close the dropdown first
    const vendorOptions = document.querySelector('.vendor-dropdown-options');
    const vendorContainer = document.querySelector('.vendor-dropdown-container');
    if (vendorOptions && vendorContainer) {
        vendorOptions.style.display = 'none';
        vendorContainer.classList.remove('active');
    }
    
    // Clear the form
    document.getElementById('vendorQuickAddForm').reset();
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('vendorQuickAddModal'));
    modal.show();
}

// Handle vendor quick add form submission
document.addEventListener('DOMContentLoaded', function() {
    const vendorForm = document.getElementById('vendorQuickAddForm');
    const vendorInput = document.getElementById('vendor_name');
    
    if (vendorForm) {
        vendorForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = vendorForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
            submitBtn.disabled = true;
            
            // Prepare form data
            const formData = new FormData(vendorForm);
            
            // Add CSRF token
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            // Submit the form
            fetch('{{ route("business.vendors.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('vendorQuickAddModal'));
                    modal.hide();
                    
                    // Set the vendor name in the input
                    if (vendorInput) {
                        vendorInput.value = data.vendor.vendor_name;
                    }
                    
                    // Reload vendors in dropdown
                    if (typeof loadVendors === 'function') {
                        loadVendors();
                    }
                    
                    // Show success message
                    showNotification('Vendor added successfully!', 'success');
                    } else {
                    // Show error message
                    showNotification(data.message || 'Failed to add vendor', 'error');
                }
            })
            .catch(error => {
                console.error('Error adding vendor:', error);
                showNotification('An error occurred while adding the vendor', 'error');
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});



// Simple notification function
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
                ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
        }, 5000);
    }

// Image preview functionality
function previewImages(input) {
    const container = document.getElementById('currentImages');
    const files = input.files;
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imagePreview = document.createElement('img');
                imagePreview.src = e.target.result;
                imagePreview.className = 'image-preview';
                imagePreview.style.marginRight = '10px';
                imagePreview.style.marginBottom = '10px';
                container.appendChild(imagePreview);
            };
            reader.readAsDataURL(file);
        }
    }
}

// Remove current image
function removeCurrentImage(imageId) {
    if (confirm('Are you sure you want to remove this image?')) {
        // Add hidden input to mark image for deletion
        const form = document.getElementById('vehicleForm');
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'deleted_images[]';
        hiddenInput.value = imageId;
        form.appendChild(hiddenInput);
        
        // Remove image from display
        event.target.closest('.image-preview-item').remove();
    }
}

// Form validation
document.getElementById('vehicleForm').addEventListener('submit', function(e) {
    const errors = [];
    
    // Check required fields
    const requiredFields = this.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            errors.push(`${field.previousElementSibling.textContent.replace(' *', '')} is required`);
        }
    });
    
    // Check file uploads
    const fileInputs = this.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        if (input.hasAttribute('required') && !input.files.length) {
            errors.push(`${input.previousElementSibling.previousElementSibling.textContent.replace(' *', '')} is required`);
        }
    });
    
    if (errors.length > 0) {
        e.preventDefault();
        showErrors(errors);
    }
});

function showErrors(errors) {
    const errorDisplay = document.getElementById('errorDisplay');
    const errorList = document.getElementById('errorList');
    
    errorList.innerHTML = errors.map(error => `<div>• ${error}</div>`).join('');
    errorDisplay.style.display = 'block';
    
    // Scroll to error display
    errorDisplay.scrollIntoView({ behavior: 'smooth' });
}
</script>
@endpush