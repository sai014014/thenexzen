@extends('business.layouts.app')

@section('title', 'Vehicle Details - ' . $vehicle->vehicle_make . ' ' . $vehicle->vehicle_model)
@section('page-title', 'Vehicle Details')

@section('content')
<div class="container-fluid">
    <!-- Vehicle Card Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="vehicle-header-card">
                <div class="d-flex justify-content-between align-items-center">
                    <!-- Vehicle Name Section (Left) -->
                    <div class="d-flex align-items-center">
                <div>
                            <div class="d-flex align-items-center mb-1">
                                <div class="status-dot bg-success me-2"></div>
                                <span class="text-muted small">{{ $vehicle->vehicle_make }}</span>
                </div>
                            <h2 class="mb-0 fw-bold">{{ $vehicle->vehicle_model }}</h2>
            </div>
                            </div>
                    
                    <!-- Vehicle Booking Status and Actions (Middle & Right) -->
                    <div class="d-flex align-items-center gap-4">
                        <!-- Action Buttons -->
                        <div class="d-flex gap-3 align-items-center">
                            <!-- Status Dropdown -->
                            <div class="dropdown custom-status-dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle status-dropdown-btn" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="true">
                                    <span class="status-display-text">
                                        @if($vehicle->vehicle_status === 'active')
                                            <i class="fas fa-check-circle text-success me-1"></i>Active
                                        @elseif($vehicle->vehicle_status === 'inactive')
                                            <i class="fas fa-times-circle text-danger me-1"></i>Inactive
                                         @if($vehicle->unavailable_until)
                                                <span class="text-muted ms-2">Until: {{ \Carbon\Carbon::parse($vehicle->unavailable_until)->format('M d, Y') }}</span>
                                         @endif
                                        @elseif($vehicle->vehicle_status === 'under_maintenance')
                                            <i class="fas fa-tools text-warning me-1"></i>Under Maintenance
                                        @else
                                            <i class="fas fa-question-circle text-secondary me-1"></i>Unknown
                                 @endif
                                    </span>
                                    <i class="fas fa-chevron-down ms-2 chevron-icon"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end status-dropdown-menu p-3" style="min-width: 350px;" aria-labelledby="statusDropdown">
                                    <!-- Active Option -->
                                    <div class="status-option" data-status="active">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div>
                                                <span class="fw-bold">Active</span>
                                                <p class="text-muted small mb-0">Available for booking</p>
                    </div>
                                            <i class="fas fa-check-circle text-success"></i>
                             </div>
                        </div>
                        
                                    <!-- Inactive Option -->
                                    <div class="status-option" data-status="inactive">
                                        <div class="mb-2">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div>
                                                    <span class="fw-bold">Inactive</span>
                                                    <p class="text-muted small mb-0">Not available for booking</p>
                            </div>
                                                <i class="fas fa-times-circle text-danger"></i>
                            </div>
                        
                                            <!-- Inactive Options -->
                                            <div id="inactiveOptions" class="mt-3" style="display: none;">
                                                <div class="mb-3">
                                                    <label class="form-label small">Set until date</label>
                                                    <input type="date" class="form-control form-control-sm" id="inactiveUntilDate" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        </div>

                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="checkbox" id="inactiveUntilToggle">
                                                    <label class="form-check-label small" for="inactiveUntilToggle">
                                                        Until I switch it on
                                                    </label>
                        </div>

                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-primary w-100" onclick="saveInactiveStatus()">
                                                        <i class="fas fa-check me-1"></i>Done
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary w-100" onclick="cancelInactiveStatus()">
                                                        Cancel
                                                    </button>
                            </div>
                        </div>
                    </div>
                </div>

                                    <!-- Under Maintenance Option -->
                                    <div class="status-option" data-status="under_maintenance">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div>
                                                <span class="fw-bold">Under Maintenance</span>
                                                <p class="text-muted small mb-0">Not available for booking</p>
            </div>
                                            <i class="fas fa-tools text-warning"></i>
        </div>
            </div>
                                </ul>
                    </div>

                            
                            <a href="{{ route('business.vehicles.edit', $vehicle) }}" class="btn btn-link text-primary text-decoration-none px-2">
                                Modify
                            </a>
                            <button class="btn btn-link text-danger text-decoration-none px-2" onclick="confirmDelete({{ $vehicle->id }})">
                                Delete
                            </button>
                            <a href="{{ route('business.bookings.create', ['vehicle_id' => $vehicle->id]) }}" class="btn btn-primary px-4 py-2 rounded">
                                Book
                            </a>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="row">
        <!-- Left Column - Vehicle Images and Documents -->
        <div class="col-lg-6">
            <!-- Vehicle Images Section -->
        <div class="card mb-4">
            <div class="card-header">
                   
            </div>
                <div class="card-body p-0">
                    @if($vehicle->images && $vehicle->images->count() > 0)
                        <div class="row g-0">
                            <div class="col-8">
                                <div class="main-image-container" style="height: 400px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                    @php
                                        $primaryImage = $vehicle->primaryImage ?? $vehicle->firstImage;
                                    @endphp
                                    @if($primaryImage)
                                        <img src="{{ asset('storage/' . $primaryImage->image_path) }}" 
                                             alt="{{ $vehicle->vehicle_make }} {{ $vehicle->vehicle_model }}" 
                                             class="img-fluid" 
                                             style="max-height: 100%; max-width: 100%; object-fit: cover;"
                                             id="mainImage">
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-{{ $vehicle->vehicle_type === 'car' ? 'car' : ($vehicle->vehicle_type === 'bike_scooter' ? 'motorcycle' : 'truck') }} fa-5x mb-3"></i>
                                            <p>No Image Available</p>
                    </div>
                    @endif
                    </div>
                        </div>
                            <div class="col-4">
                                <div class="thumbnail-container p-3" style="height: 400px; overflow-y: auto;">
                                    @foreach($vehicle->images as $index => $image)
                                        <div class="thumbnail-item mb-2 {{ $index === 0 ? 'active' : '' }}" 
                                             onclick="changeMainImage(this, '{{ asset('storage/' . $image->image_path) }}')"
                                             data-image-src="{{ asset('storage/' . $image->image_path) }}">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                                 alt="Thumbnail {{ $index + 1 }}" 
                                                 class="img-fluid rounded" 
                                                 style="width: 100%; height: 60px; object-fit: cover; border: 2px solid {{ $index === 0 ? '#6f42c1' : 'transparent' }};">
                                            @if($image->is_primary)
                                                <div class="position-absolute top-0 start-0">
                                                    <span class="badge bg-success" style="font-size: 0.7em;">Primary</span>
                    </div>
                    @endif
                </div>
                                    @endforeach
            </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted p-5">
                            <i class="fas fa-{{ $vehicle->vehicle_type === 'car' ? 'car' : ($vehicle->vehicle_type === 'bike_scooter' ? 'motorcycle' : 'truck') }} fa-5x mb-3"></i>
                            <p>No Images Available</p>
        </div>
        @endif
            </div>
        </div>

            <!-- Documents Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Documents</h5>
                    <button class="btn btn-link p-0">
                        <i class="fas fa-chevron-down text-primary"></i>
                    </button>
            </div>
            <div class="card-body">
                <div class="row">
                        <!-- Insurance Document -->
                        <div class="col-6 col-md-4 mb-3 text-center">
                            <div class="document-item">
                                @if($vehicle->insurance_document_path && file_exists(public_path('storage/' . $vehicle->insurance_document_path)))
                                    <a href="{{ asset('storage/' . $vehicle->insurance_document_path) }}" target="_blank" class="text-decoration-none">
                                        <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                        <p class="mb-0 small text-primary">Insurance</p>
                                        <small class="text-muted">Click to view</small>
                                    </a>
                                @else
                                    <i class="fas fa-file-pdf fa-3x text-muted mb-2"></i>
                                    <p class="mb-0 small text-muted">Insurance</p>
                                    <small class="text-muted">Not uploaded</small>
                                @endif
                    </div>
                    </div>
                        
                        <!-- RC Document -->
                        <div class="col-6 col-md-4 mb-3 text-center">
                            <div class="document-item">
                                @if($vehicle->rc_document_path && file_exists(public_path('storage/' . $vehicle->rc_document_path)))
                                    <a href="{{ asset('storage/' . $vehicle->rc_document_path) }}" target="_blank" class="text-decoration-none">
                                        <i class="fas fa-file-image fa-3x text-success mb-2"></i>
                                        <p class="mb-0 small text-primary">RC Document</p>
                                        <small class="text-muted">Click to view</small>
                                    </a>
                                @else
                                    <i class="fas fa-file-image fa-3x text-muted mb-2"></i>
                                    <p class="mb-0 small text-muted">RC Document</p>
                                    <small class="text-muted">Not uploaded</small>
                    @endif
                </div>
                </div>

                        <!-- Additional Documents Placeholder -->
                        <div class="col-6 col-md-4 mb-3 text-center">
                            <div class="document-item">
                                <i class="fas fa-file-alt fa-3x text-muted mb-2"></i>
                                <p class="mb-0 small text-muted">Other Documents</p>
                                <small class="text-muted">Coming soon</small>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Vehicle Information -->
        <div class="col-lg-6">
            <!-- Vehicle Type & General Information -->
        <div class="card mb-4">
            <div class="card-header">
                    <h5 class="mb-0 text-primary">Vehicle Type & General Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Vehicle Type</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->vehicle_type_display }}</p>
                    </div>
                    </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Vehicle Make</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->vehicle_make }}</p>
                    </div>
                </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Vehicle Model</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->vehicle_model }}</p>
                    </div>
                </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Vehicle Year</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->vehicle_year }}</p>
                    </div>
                    </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">VIN / Chasis Number</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->vin_number ?? 'N/A' }}</p>
                </div>
            </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Registration Number</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->vehicle_number }}</p>
        </div>
            </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Vehicle Status</label>
                                <div class="mb-0">
                                    <span class="badge bg-{{ $vehicle->status_badge_class }}">{{ $vehicle->status_display }}</span>
                                    @if($vehicle->vehicle_status === 'inactive' && $vehicle->unavailable_until)
                                        <br><small class="text-muted mt-1 d-block">Inactive Until: {{ \Carbon\Carbon::parse($vehicle->unavailable_until)->format('M d, Y') }}</small>
                    @endif
                    </div>
                    </div>
                    </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Fuel Type</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->fuel_type_display }}</p>
                </div>
            </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Transmission Type</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->transmission_type_display }}</p>
        </div>
            </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Seating Capacity</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->seating_capacity ?? 'N/A' }} Seats</p>
            </div>
        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Mileage</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->mileage ?? 'N/A' }} KM/HR</p>
            </div>
        </div>
                </div>
            </div>
    </div>

            <!-- Vehicle Rental Information -->
        <div class="card mb-4">
            <div class="card-header">
                    <h5 class="mb-0 text-primary">Vehicle Rental Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">
                                    <i class="fas fa-info-circle me-1"></i>Rental Price for 24 hrs
                         </label>
                                <p class="mb-0 fw-bold text-primary">₹{{ number_format($vehicle->rental_price_24h ?? 0, 2) }}</p>
                     </div>
                     </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">
                                    <i class="fas fa-info-circle me-1"></i>Kilometer Limit per Booking
                                </label>
                                <p class="mb-0 fw-bold">{{ $vehicle->km_limit_per_booking ?? 'N/A' }}KM</p>
                 </div>
                                </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">
                                    <i class="fas fa-info-circle me-1"></i>Extra Rental Price per Hour
                                </label>
                                <p class="mb-0 fw-bold">₹{{ number_format($vehicle->extra_rental_price_per_hour ?? 0, 2) }}</p>
                            </div>
                </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">
                                    <i class="fas fa-info-circle me-1"></i>Extra Price per Kilometre
                                </label>
                                <p class="mb-0 fw-bold">₹{{ number_format($vehicle->extra_price_per_km ?? 0, 2) }}</p>
                    </div>
                    </div>
            </div>
            </div>
        </div>

            <!-- Ownership & Service Maintenance Details -->
            <div class="card">
            <div class="card-header">
                    <h5 class="mb-0 text-primary">Ownership & Service Maintenance Details</h5>
            </div>
            <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Ownership Type</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->ownership_type_display }}</p>
                    </div>
                </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Vendor Name</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->vendor_name ?? 'N/A' }}</p>
            </div>
        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Last Service Date</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->last_service_date ? $vehicle->last_service_date->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Meter Reading at Service Time</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->last_service_meter_reading ? number_format($vehicle->last_service_meter_reading) . ' Kilometers' : 'N/A' }}</p>
                    </div>
                    </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Next Service Date</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->next_service_due ? $vehicle->next_service_due->format('d/m/Y') : 'N/A' }}</p>
            </div>
        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Meter Reading for Next Service</label>
                                <p class="mb-0 fw-bold">{{ $vehicle->next_service_meter_reading ? number_format($vehicle->next_service_meter_reading) . ' Kilometers' : 'N/A' }}</p>
            </div>
                    </div>
                </div>

                    @if($vehicle->remarks_notes)
                    <div class="mt-3">
                        <label class="form-label small text-muted mb-1">Remarks / Notes</label>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-0">{{ $vehicle->remarks_notes }}</p>
            </div>
        </div>
        @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.thumbnail-item {
    cursor: pointer;
    transition: all 0.3s ease;
}

.thumbnail-item:hover {
    transform: scale(1.05);
}

.thumbnail-item.active img {
    border-color: #6f42c1 !important;
}

.info-item {
    padding: 0.5rem 0;
}

.document-item {
    padding: 1rem;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.document-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
}


.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 12px;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.card-header {
    background: none;
    border-bottom: 1px solid #e9ecef;
    border-radius: 12px 12px 0 0 !important;
}

.text-primary {
    color: #6f42c1 !important;
}

.btn-primary {
    background-color: #6f42c1;
    border-color: #6f42c1;
}

.btn-primary:hover {
    background-color: #5a359a;
    border-color: #5a359a;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.main-image-container {
    position: relative;
    overflow: hidden;
}

.main-image-container img {
    transition: all 0.3s ease;
}

.main-image-container:hover img {
    transform: scale(1.05);
}

.vehicle-header-card {
    background: white;
    border-radius: 12px;
    padding: 20px 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.vehicle-header-card .status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}

.vehicle-header-card h2 {
    font-size: 24px;
    color: #333;
    margin: 0;
}

.vehicle-header-card .text-muted {
    color: #6c757d;
    font-size: 14px;
}

.vehicle-header-card .btn-primary {
    background-color: #6B6ADE;
    border: none;
    color: white;
    font-weight: 500;
}

.vehicle-header-card .btn-primary:hover {
    background-color: #5a5ac5;
}

.vehicle-header-card .btn-link {
    font-weight: 500;
    text-decoration: none !important;
}

.vehicle-header-card .btn-link.text-primary {
    color: #6B6ADE !important;
}

.vehicle-header-card .btn-link.text-danger {
    color: #dc3545 !important;
}

.vehicle-header-card .btn-link:hover {
    text-decoration: underline !important;
}

/* Status Dropdown Styles */
.status-dropdown-menu {
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-radius: 8px;
}

.status-option {
    cursor: pointer;
    padding: 10px;
    border-radius: 6px;
    transition: background-color 0.2s;
    margin-bottom: 8px;
}

.status-option:hover {
    background-color: #f8f9fa;
}

.status-option:last-child {
    margin-bottom: 0;
}

.status-option i {
    font-size: 20px;
}

#inactiveOptions {
    border-top: 1px solid #dee2e6;
    padding-top: 15px;
    margin-top: 15px;
}

#inactiveOptions .form-control {
    border: 1px solid #dee2e6;
}

#inactiveOptions .form-check-label {
    color: #495057;
    cursor: pointer;
}

#inactiveOptions .form-check-input:checked {
    background-color: #6B6ADE;
    border-color: #6B6ADE;
}

/* Status Dropdown Button Styling */
.status-dropdown-btn {
    border-radius: 6px;
    padding: 8px 16px;
    font-weight: 500;
    background-color: white;
    color: #495057;
    transition: all 0.2s ease;
}

.status-dropdown-btn:hover {
    border-color:none;
    background-color:none;
}


.status-dropdown-btn:focus {
    border-color: #6B6ADE;
    box-shadow: 0 0 0 0.2rem rgba(107, 106, 222, 0.25);
    color: #6B6ADE;
}

.custom-status-dropdown {
    position: relative;
    display: block;
    border: none;
    box-shadow: none;
}

.status-display-text {
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
}

.status-dropdown-btn i.fas {
    margin: 0;
}

.inactive-until-info {
    font-size: 12px;
    color: #6c757d;
    text-align: left;
}

.inactive-until-info i {
    color: #6c757d;
}

/* Dropdown Menu Container */
.status-dropdown-menu {
    margin-top: 8px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    border: 1px solid #dee2e6;
}

/* Status Option Hover Effects */
.status-option {
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.status-option:hover {
    background-color: #f8f9fa;
    border-color: #e9ecef;
    transform: translateX(4px);
}

.status-option.active {
    background-color: #e7e7ff;
    border-color: #6B6ADE;
}

/* Button Icons */
.chevron-icon {
    transition: transform 0.2s ease;
    display: inline-block;
}

.status-dropdown-btn[aria-expanded="true"] .chevron-icon {
    transform: rotate(180deg);
}

/* Inactive Options Styling */
#inactiveOptions .form-control {
    transition: border-color 0.2s ease;
}

#inactiveOptions .form-control:focus {
    border-color: #6B6ADE;
    box-shadow: 0 0 0 0.2rem rgba(107, 106, 222, 0.15);
}

#inactiveOptions button {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
}

#inactiveOptions .btn-primary {
    background-color: #6B6ADE;
    border-color: #6B6ADE;
}

#inactiveOptions .btn-primary:hover {
    background-color: #5a5ac5;
    border-color: #5a5ac5;
}

#inactiveOptions .btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

#inactiveOptions .btn-secondary:hover {
    background-color: #5a6268;
    border-color: #5a6268;
 }
</style>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this vehicle?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone and will remove all associated data.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Vehicle</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function changeMainImage(thumbnail, imageSrc) {
    // Remove active class from all thumbnails
    document.querySelectorAll('.thumbnail-item').forEach(item => {
        item.classList.remove('active');
        item.querySelector('img').style.borderColor = 'transparent';
    });
    
    // Add active class to clicked thumbnail
    thumbnail.classList.add('active');
    thumbnail.querySelector('img').style.borderColor = '#6f42c1';
    
    // Change main image
    const mainImage = document.getElementById('mainImage');
    if (mainImage && imageSrc) {
        mainImage.src = imageSrc;
    }
}

// Handle status option clicks after DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for Bootstrap to initialize
    setTimeout(function() {
        const statusOptions = document.querySelectorAll('.status-option');
        
        statusOptions.forEach(option => {
            const status = option.getAttribute('data-status');
            
            if (status === 'active' || status === 'under_maintenance') {
                option.style.cursor = 'pointer';
                option.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    updateStatus(status);
                    return false;
                };
            } else if (status === 'inactive') {
                // Handle inactive click to show options
                const inactiveContainer = option.querySelector('.mb-2');
                const inactiveOptions = document.getElementById('inactiveOptions');
                
                if (inactiveContainer && inactiveOptions) {
                    option.style.cursor = 'pointer';
                    
                    // Get only the header part (the div with d-flex)
                    const inactiveHeader = inactiveContainer.querySelector('.d-flex.align-items-center.justify-content-between');
                    
                    if (inactiveHeader) {
                        inactiveHeader.onclick = function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            
                            const isHidden = inactiveOptions.style.display === 'none' || !inactiveOptions.style.display;
                            inactiveOptions.style.display = isHidden ? 'block' : 'none';
                            return false;
                        };
                    }
                }
                
                // Prevent clicks inside inactiveOptions from closing dropdown
                if (inactiveOptions) {
                    inactiveOptions.addEventListener('click', function(e) {
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                    });
                    
                    // Also prevent propagation from form controls
                    const dateInput = document.getElementById('inactiveUntilDate');
                    const toggleCheckbox = document.getElementById('inactiveUntilToggle');
                    
                    if (dateInput) {
                        dateInput.addEventListener('click', function(e) {
                            e.stopPropagation();
                        });
                    }
                    
                    if (toggleCheckbox) {
                        toggleCheckbox.addEventListener('click', function(e) {
                            e.stopPropagation();
                        });
                    }
                    
                    // Prevent propagation from buttons
                    const doneButton = inactiveOptions.querySelector('button[onclick="saveInactiveStatus()"]');
                    const cancelButton = inactiveOptions.querySelector('button[onclick="cancelInactiveStatus()"]');
                    
                    if (doneButton) {
                        doneButton.addEventListener('click', function(e) {
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                        });
                    }
                    
                    if (cancelButton) {
                        cancelButton.addEventListener('click', function(e) {
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                        });
                    }
                }
            }
        });
    }, 300);
});

function updateStatus(status, additionalData = null) {
    const button = document.getElementById('statusDropdown');
    if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    }
    
    const requestBody = { status: status };
    if (additionalData) {
        Object.assign(requestBody, additionalData);
    }
    
    console.log('Sending status update:', requestBody);
    
    fetch(`{{ route('business.vehicles.toggle-availability', $vehicle) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(requestBody)
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            console.log('Updated vehicle data:', data);
            
            // Update the dropdown button text with icon based on actual vehicle_status
            const actualStatus = data.vehicle_status;
            const statusText = actualStatus.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            let iconClass = '';
            let iconColor = '';
            
            if (actualStatus === 'active') {
                iconClass = 'fa-check-circle';
                iconColor = 'text-success';
            } else if (actualStatus === 'inactive') {
                iconClass = 'fa-times-circle';
                iconColor = 'text-danger';
            } else if (actualStatus === 'under_maintenance') {
                iconClass = 'fa-tools';
                iconColor = 'text-warning';
            }
            
            if (button) {
                button.disabled = false;
                button.innerHTML = '<span class="status-display-text"><i class="fas ' + iconClass + ' ' + iconColor + ' me-1"></i>' + statusText + '</span><i class="fas fa-chevron-down ms-2 chevron-icon"></i>';
            }
            showAlert('Status updated successfully!', 'success');
            
            // Collapse dropdown
            const dropdown = bootstrap.Dropdown.getInstance(document.getElementById('statusDropdown'));
            if (dropdown) {
                dropdown.hide();
            }
            
            // Reload page after 1 second to show updated status everywhere
            setTimeout(function() {
                location.reload();
            }, 1000);
        } else {
            if (button) {
                button.disabled = false;
            }
            showAlert(data.message || 'Failed to update status', 'danger');
        }
    })
    .catch(error => {
        console.error('Error updating status:', error);
        console.error('Error details:', error.message, error.stack);
        if (button) {
            button.disabled = false;
        }
        let errorMessage = 'An error occurred while updating status. ';
        if (error.message) {
            errorMessage += error.message;
        }
        showAlert(errorMessage, 'danger');
    });
}

function saveInactiveStatus() {
    const untilDate = document.getElementById('inactiveUntilDate').value;
    const untilToggle = document.getElementById('inactiveUntilToggle').checked;
    
    const additionalData = {};
    
    if (untilToggle) {
        additionalData.inactive_until_manual = true;
    } else if (untilDate) {
        additionalData.inactive_until_date = untilDate;
    }
    
    updateStatus('inactive', additionalData);
}

function cancelInactiveStatus() {
    const inactiveOptions = document.getElementById('inactiveOptions');
    inactiveOptions.style.display = 'none';
    
    // Reset form
    document.getElementById('inactiveUntilDate').value = '';
    document.getElementById('inactiveUntilToggle').checked = false;
}

// Handle "Until I switch it on" toggle
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('inactiveUntilToggle');
    const dateInput = document.getElementById('inactiveUntilDate');
    
    if (toggle && dateInput) {
        toggle.addEventListener('change', function() {
            if (this.checked) {
                dateInput.disabled = true;
                dateInput.value = '';
    } else {
                dateInput.disabled = false;
            }
        });
    }
});

function confirmDelete(vehicleId) {
    const form = document.getElementById('deleteForm');
    form.action = `/business/vehicles/${vehicleId}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Add new alert at the top of the content
    const content = document.querySelector('.container-fluid');
    if (content) {
        content.insertAdjacentHTML('afterbegin', alertHtml);
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Set initial active thumbnail
    const firstThumbnail = document.querySelector('.thumbnail-item');
    if (firstThumbnail) {
        firstThumbnail.classList.add('active');
    }
});
</script>
@endsection