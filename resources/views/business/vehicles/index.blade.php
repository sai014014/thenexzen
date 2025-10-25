@extends('business.layouts.app')

@section('title', 'Vehicle Management')
@section('page-title', 'Vehicle Management')

@push('styles')


@endpush

@section('content')
<!-- Main Content -->
<link rel="stylesheet" href="{{ asset('dist/css/VehicleManagement/vehicleManagement_view.css') }}">

<!-- Search and Add Vehicle Section -->
    <!-- Filter Options -->
    <div class="filter-container mb-3">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="vehicleTypeFilter" class="form-label">Vehicle Type</label>
                <select id="vehicleTypeFilter" class="form-select">
                    <option value="">All Types</option>
                    <option value="car">Car</option>
                    <option value="bike_scooter">Bike/Scooter</option>
                    <option value="heavy_vehicle">Heavy Vehicle</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="statusFilter" class="form-label">Status</label>
                <select id="statusFilter" class="form-select">
                    <option value="">All Status</option>
                    <option value="active">Active (Available)</option>
                    <option value="booked">Booked</option>
                    <option value="under_maintenance">Under Maintenance</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="fuelTypeFilter" class="form-label">Fuel Type</label>
                <select id="fuelTypeFilter" class="form-select">
                    <option value="">All Fuel Types</option>
                    <option value="petrol">Petrol</option>
                    <option value="diesel">Diesel</option>
                    <option value="electric">Electric</option>
                    <option value="hybrid">Hybrid</option>
                </select>
            </div>
        </div>
    </div>

    
    <div class="record-count">{{ $vehicles->total() }} Records Found, Page {{ $vehicles->currentPage() }} of {{ $vehicles->lastPage() }}</div>
    <div class="filter-section">
        <div class="table-responsive">
            <table id="vehicleTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="vechicle_title">Vehicle</th>
                        <th>Unit Type</th>
                        <th>Fuel</th>
                        <th>Capacity</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="vehicleTableBody">
                    @foreach($vehicles as $vehicle)
                    <tr>
                        <td class="vechicle_title">
                            <div class="d-flex align-items-center">
                                @php
                                    $brandIcon = 'images/vehicle-brands/' . strtolower(str_replace(' ', '-', $vehicle->vehicle_make)) . '.svg';
                                    $brandIconPath = public_path($brandIcon);
                                    $defaultIcon = 'images/vehicle-brands/default.svg';
                                @endphp
                                @if(file_exists($brandIconPath))
                                    <img src="{{ asset($brandIcon) }}" alt="{{ $vehicle->vehicle_make }}" class="me-2" style="width: 30px; height: 30px;">
                                @else
                                    <div class="brand-icon-placeholder me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background: #f8f9fa; border-radius: 4px; font-size: 12px; font-weight: bold; color: #6c757d;">
                                        {{ strtoupper(substr($vehicle->vehicle_make, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold">{{ $vehicle->vehicle_make }} {{ $vehicle->vehicle_model }}</div>
                                    <small class="text-muted">{{ $vehicle->registration_number }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $unitType = '';
                                if($vehicle->vehicle_type === 'car') {
                                    $unitType = 'Car - ' . ucfirst($vehicle->transmission_type);
                                } elseif($vehicle->vehicle_type === 'bike_scooter') {
                                    $unitType = 'Bike/Scooter - ' . ucfirst($vehicle->bike_transmission_type ?? $vehicle->transmission_type);
                                } elseif($vehicle->vehicle_type === 'heavy_vehicle') {
                                    $unitType = 'Heavy Vehicle - ' . ucfirst($vehicle->transmission_type);
                                }
                            @endphp
                            {{ $unitType }}
                        </td>
                        <td>{{ ucfirst($vehicle->fuel_type) }}</td>
                        <td>
                            @if($vehicle->vehicle_type === 'car')
                                {{ $vehicle->seating_capacity }} Seats
                            @elseif($vehicle->vehicle_type === 'bike_scooter')
                                {{ $vehicle->engine_capacity_cc }}cc Engine
                            @elseif($vehicle->vehicle_type === 'heavy_vehicle')
                                @if($vehicle->seating_capacity)
                                    {{ $vehicle->seating_capacity }} Seats
                                @elseif($vehicle->payload_capacity_tons)
                                {{ $vehicle->payload_capacity_tons }} Tons
                                @endif
                            @endif
                        </td>
                        <td>
                            ₹ {{ number_format($vehicle->rental_price_24h, 2) }}
                            <i class="fas fa-info-circle text-muted ms-1" style="cursor: pointer;"></i>
                        </td>
                        <td>
                            @php
                                $displayStatus = 'Available';
                                $badgeClass = 'success';

                                if ($vehicle->vehicle_status === 'under_maintenance') {
                                    $displayStatus = 'Maintenance';
                                    $badgeClass = 'danger';
                                } elseif ($vehicle->bookings()->whereIn('status', ['ongoing', 'upcoming'])->where('end_date_time', '>=', \Carbon\Carbon::now())->exists()) {
                                    $displayStatus = 'Booked';
                                    $badgeClass = 'warning';
                                }
                            @endphp
                            <span class="badge rounded-pill bg-{{ $badgeClass }} text-white fw-bold px-2 py-1">
                                {{ $displayStatus }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('business.vehicles.show', $vehicle) }}" class="text-primary text-decoration-none">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pagination controls -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="pagination-info">
                <span class="text-muted">Rows per page: 10</span>
                <span class="text-muted ms-3">{{ $vehicles->firstItem() }}-{{ $vehicles->lastItem() }} of {{ $vehicles->total() }}</span>
            </div>
            <nav aria-label="Vehicle pagination">
                <ul class="pagination mb-0">
                    @if ($vehicles->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $vehicles->previousPageUrl() }}">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    @endif

                    @if ($vehicles->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $vehicles->nextPageUrl() }}">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Vehicle Limit Modal -->
<div class="modal fade" id="vehicleLimitModal" tabindex="-1" aria-labelledby="vehicleLimitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="vehicleLimitModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Vehicle Limit Reached
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-4">
                    <i class="fas fa-car fa-4x text-warning mb-3"></i>
                    <h4 class="text-warning mb-3">Vehicle Capacity Limit Reached</h4>
                    <p class="text-muted mb-4">
                        @if($capacityStatus)
                            {{ $capacityStatus['message'] }}
                        @else
                            You have reached the maximum number of vehicles allowed for your subscription package.
                        @endif
                    </p>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="{{ route('business.subscription.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-up me-2"></i>Upgrade Package
                    </a>
                    <button type="button" class="btn btn-outline-secondary btn-lg" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const RECORDS_PER_PAGE = 10;
    const RECORDS_PER_PAGE_OPTIONS = [10, 25, 50, 100];
    const VEHICLE_STATUS_ARRAY = ['Active', 'Inactive', 'Under Maintenance'];

        // Live search functionality (now handled by common search)
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality is now handled by the common search in the header

        // Filter functionality
        const vehicleTypeFilter = document.getElementById('vehicleTypeFilter');
        const statusFilter = document.getElementById('statusFilter');
        const fuelTypeFilter = document.getElementById('fuelTypeFilter');
        
        // Get table rows and search input
        const rows = document.querySelectorAll('#vehicleTableBody tr');
        const searchInput = document.querySelector('input[type="search"]') || document.querySelector('.search-input');

        function applyFilters() {
            const vehicleType = vehicleTypeFilter.value.toLowerCase();
            const status = statusFilter.value.toLowerCase();
            const fuelType = fuelTypeFilter.value.toLowerCase();
            const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

            let visibleCount = 0;

            rows.forEach(row => {
                let showRow = true;
                const text = row.textContent.toLowerCase();

                // Apply search filter
                if (searchTerm && !text.includes(searchTerm)) {
                    showRow = false;
                }

                // Apply vehicle type filter
                if (vehicleType && showRow) {
                    if (vehicleType === 'car' && !text.includes('car')) {
                        showRow = false;
                    } else if (vehicleType === 'bike_scooter' && !text.includes('bike/scooter')) {
                        showRow = false;
                    } else if (vehicleType === 'heavy_vehicle' && !text.includes('heavy vehicle')) {
                        showRow = false;
                    }
                }

                // Apply status filter
                if (status && showRow) {
                    if (status === 'active' && !text.includes('available')) {
                        showRow = false;
                    } else if (status === 'booked' && !text.includes('booked')) {
                        showRow = false;
                    } else if (status === 'under_maintenance' && !text.includes('maintenance')) {
                        showRow = false;
                    } else if (status === 'inactive' && !text.includes('inactive')) {
                        showRow = false;
                    }
                }

                // Apply fuel type filter
                if (fuelType && showRow) {
                    if (!text.includes(fuelType)) {
                        showRow = false;
                    }
                }

                if (showRow) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Update record count
            const recordCount = document.querySelector('.record-count');
            if (recordCount) {
                const totalRows = rows.length;
                if (vehicleType || status || fuelType || searchTerm) {
                    recordCount.textContent = `${visibleCount} Records Found (filtered from ${totalRows} total)`;
                } else {
                    recordCount.textContent = `${totalRows} Records Found, Page {{ $vehicles->currentPage() }} of {{ $vehicles->lastPage() }}`;
                }
            }
        }

        // Live filters - apply automatically on change
        vehicleTypeFilter.addEventListener('change', applyFilters);
        statusFilter.addEventListener('change', applyFilters);
        fuelTypeFilter.addEventListener('change', applyFilters);
        
        // Add search input listener if search input exists
        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }
    });

    // Function to show vehicle limit modal
    function showVehicleLimitModal() {
        const modal = new bootstrap.Modal(document.getElementById('vehicleLimitModal'));
        modal.show();
    }

</script>
@endsection