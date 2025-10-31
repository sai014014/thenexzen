@extends('business.layouts.app')

@section('title', 'Vehicle Management')
@section('page-title', 'Vehicle Management')

@push('styles')


@endpush

@section('content')
<!-- Main Content -->

<!-- Search, Filters and Add Vehicle Section -->
<div class="row mb-3 align-items-end filter-row">
    <div class="col-md-4">
        <div class="vehicle-search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="vehicleSearch" class="form-control search-input" placeholder="Search">
        </div>
    </div>
    <div class="col-md-2">
        <select id="vehicleTypeFilter" class="form-select" data-title="Vehicle Type">
            <option value="">Vehicle Type</option>
            <option value="car">Car</option>
            <option value="bike_scooter">Bike/Scooter</option>
            <option value="heavy_vehicle">Heavy Vehicle</option>
        </select>
    </div>
    <div class="col-md-2">
        <select id="statusFilter" class="form-select" data-title="Status">
            <option value="">Status</option>
            <option value="active">Active (Available)</option>
            <option value="booked">Booked</option>
            <option value="under_maintenance">Under Maintenance</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>
    <div class="col-md-2">
        <select id="fuelTypeFilter" class="form-select" data-title="Fuel Type">
            <option value="">Fuel Type</option>
            <option value="petrol">Petrol</option>
            <option value="diesel">Diesel</option>
            <option value="electric">Electric</option>
            <option value="hybrid">Hybrid</option>
        </select>
    </div>
    <div class="col-md-2 text-end">
        @php
            $businessAdmin = auth('business_admin')->user();
            $business = $businessAdmin ? $businessAdmin->business : null;
            $subscription = $business ? $business->subscriptions()->whereIn('status', ['active', 'trial'])->first() : null;
            $capacityStatus = $subscription ? $subscription->getVehicleCapacityStatus() : null;
        @endphp
        @if($capacityStatus && $capacityStatus['can_add'])
            <a href="{{ route('business.vehicles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Vehicle
            </a>
        @elseif($capacityStatus && !$capacityStatus['can_add'])
            <button class="btn btn-primary" onclick="showVehicleLimitModal()">
                <i class="fas fa-plus me-2"></i>Add New Vehicle
            </button>
        @else
            <a href="{{ route('business.vehicles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Vehicle
            </a>
        @endif
    </div>
</div>

    <div class="filter-section">
        <div class="table-responsive">
            <table id="vehicleTable" class="table table-striped table-bordered">
                <thead>
                    @php
                        $currentSort = request('sort');
                        $currentDir = strtolower(request('dir', 'asc')) === 'desc' ? 'desc' : 'asc';
                        $nextDir = $currentDir === 'asc' ? 'desc' : 'asc';
                        $sortLink = function ($key, $label) use ($currentSort, $currentDir, $nextDir) {
                            $params = array_merge(request()->query(), [
                                'sort' => $key,
                                'dir' => $currentSort === $key ? $nextDir : 'asc',
                            ]);
                            // Always show a sort icon; neutral when not active
                            $icon = '↕';
                            if ($currentSort === $key) {
                                $icon = $currentDir === 'asc' ? '▲' : '▼';
                            }
                            $url = request()->url() . '?' . http_build_query($params);
                            $activeClass = $currentSort === $key ? 'text-primary fw-semibold' : 'text-muted';
                            $iconStyle = 'font-size:11px;';
                            if ($currentSort !== $key) { $iconStyle .= 'opacity:0.6;'; }
                            return '<a href="' . e($url) . '" class="text-decoration-none">' . e($label) . ' <span class="ms-1 ' . $activeClass . '" style="' . $iconStyle . '">' . e($icon) . '</span></a>';
                        };
                    @endphp
                    <tr>
                        <th class="vechicle_title">{!! $sortLink('vehicle', 'Vehicle') !!}</th>
                        <th>{!! $sortLink('unit_type', 'Unit Type') !!}</th>
                        <th>{!! $sortLink('fuel', 'Fuel') !!}</th>
                        <th>{!! $sortLink('capacity', 'Capacity') !!}</th>
                        <th>{!! $sortLink('price', 'Price') !!}</th>
                        <th>{!! $sortLink('status', 'Status') !!}</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="vehicleTableBody">
                    @foreach($vehicles as $vehicle)
                    <tr>
                        <td class="vechicle_title">
                            <div class="d-flex align-items-center">
                                @php
                                    // Generate a consistent color based on vehicle ID
                                    $colors = ['#6B6ADE', '#FF6B6B', '#4ECDC4', '#FFE66D', '#FF8C94', '#95E1D3', '#F38181', '#AA96DA', '#FCBAD3', '#A8E6CF'];
                                    $colorIndex = $vehicle->id % count($colors);
                                    $vehicleColor = $colors[$colorIndex];
                                    
                                    $brandIcon = 'images/vehicle-brands/' . strtolower(str_replace(' ', '-', $vehicle->vehicle_make)) . '.svg';
                                    $brandIconPath = public_path($brandIcon);
                                    $defaultIcon = 'images/vehicle-brands/default.svg';
                                @endphp
                                @if(file_exists($brandIconPath))
                                    <img src="{{ asset($brandIcon) }}" alt="{{ $vehicle->vehicle_make }}" class="me-2" style="width: 30px; height: 30px;">
                                @else
                                    <div class="brand-icon-placeholder me-2 d-flex align-items-center justify-content-center vehicle-color-badge" 
                                         style="width: 30px; height: 30px; background: {{ $vehicleColor }}; color: white; border-radius: 4px; font-size: 12px; font-weight: bold;">
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
                                {{ $vehicle->seating_capacity ? $vehicle->seating_capacity . ' Seats' : 'N/A' }}
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
                            <i class="fas fa-info-circle text-muted ms-1 rental-info-icon" 
                               style="cursor: pointer;" 
                               data-vehicle-id="{{ $vehicle->id }}"
                               data-rental-price-24h="{{ $vehicle->rental_price_24h }}"
                               data-km-limit="{{ $vehicle->km_limit_per_booking }}"
                               data-extra-rental-price="{{ $vehicle->extra_rental_price_per_hour }}"
                               data-extra-price-km="{{ $vehicle->extra_price_per_km }}"></i>
                        </td>
                        <td>
                            @php
                                $displayStatus = 'Available';
                                $badgeClass = 'success';

                                if ($vehicle->vehicle_status === 'inactive') {
                                    $displayStatus = 'Inactive';
                                    $badgeClass = 'secondary';
                                } elseif ($vehicle->vehicle_status === 'under_maintenance') {
                                    $displayStatus = 'Maintenance';
                                    $badgeClass = 'danger';
                                } elseif ($vehicle->bookings()->whereIn('status', ['ongoing', 'upcoming'])->where('end_date_time', '>=', \Carbon\Carbon::now())->exists()) {
                                    $displayStatus = 'Booked';
                                    $badgeClass = 'warning';
                                }
                            @endphp
                            <div>
                                <span class="badge rounded-pill bg-{{ $badgeClass }} text-white fw-bold px-2 py-1">
                                    {{ $displayStatus }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('business.vehicles.show', $vehicle) }}" class="text-primary text-decoration-none" style="font-weight: 500;">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($vehicles->total() > 20)
        <!-- Simple Pagination - Only Previous/Next -->
        <div class="d-flex justify-content-center mt-3">
            <nav aria-label="Vehicle pagination">
                <ul class="pagination">
                    @if ($vehicles->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="fas fa-chevron-left"></i> Previous
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $vehicles->previousPageUrl() }}">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        </li>
                    @endif

                    @if ($vehicles->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $vehicles->nextPageUrl() }}">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">
                                Next <i class="fas fa-chevron-right"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>

<!-- Vehicle Limit Modal -->
<div class="modal fade vehicle-limit-modal" id="vehicleLimitModal" tabindex="-1" aria-labelledby="vehicleLimitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vehicleLimitModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>Vehicle Limit Reached
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="modal-icon-wrapper">
                        <i class="fas fa-lock fa-3x"></i>
                    </div>
                    <h4 class="modal-title-text">Vehicle Capacity Limit Reached</h4>
                    <p class="modal-message">
                        @if($capacityStatus)
                            {{ $capacityStatus['message'] }}
                        @else
                            You have reached the maximum number of vehicles allowed for your subscription package.
                        @endif
                    </p>
                </div>
                <div class="modal-alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <span>Upgrade your subscription to add more vehicles to your fleet.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <a href="{{ route('business.subscription.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-up me-2"></i>Upgrade Package
                </a>
            </div>
        </div>
    </div>
</div>


<script>
    const RECORDS_PER_PAGE = 20;
    const RECORDS_PER_PAGE_OPTIONS = [20, 50, 100];
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
        const searchInput = document.getElementById('vehicleSearch');

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
        }

        // Live filters - apply automatically on change
        vehicleTypeFilter.addEventListener('change', applyFilters);
        statusFilter.addEventListener('change', applyFilters);
        fuelTypeFilter.addEventListener('change', applyFilters);
        
        // Add search input listener
        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }
    });

    // Function to show vehicle limit modal
    function showVehicleLimitModal() {
        const modal = new bootstrap.Modal(document.getElementById('vehicleLimitModal'));
        modal.show();
    }

    // Rental Info Popover functionality
    document.addEventListener('DOMContentLoaded', function() {
        const infoIcons = document.querySelectorAll('.rental-info-icon');
        
        infoIcons.forEach(icon => {
            icon.addEventListener('click', function(e) {
                e.stopPropagation();
                
                // Close all other popovers first
                document.querySelectorAll('.rental-info-popover').forEach(popover => {
                    if (popover !== this.nextElementSibling) {
                        popover.remove();
                    }
                });
                
                // Check if popover already exists
                let popover = this.nextElementSibling;
                if (popover && popover.classList.contains('rental-info-popover')) {
                    popover.remove();
                    return;
                }
                
                // Get data attributes
                const rentalPrice24h = this.getAttribute('data-rental-price-24h');
                const kmLimit = this.getAttribute('data-km-limit');
                const extraRentalPrice = this.getAttribute('data-extra-rental-price');
                const extraPriceKm = this.getAttribute('data-extra-price-km');
                
                // Create popover element
                popover = document.createElement('div');
                popover.className = 'rental-info-popover';
                popover.innerHTML = `
                    <div class="rental-info-header">
                        <strong>Rental Information</strong>
                        <i class="fas fa-times close-popover"></i>
                    </div>
                    <div class="rental-info-content">
                        <div class="rental-info-item">
                            <span class="rental-info-label">Rental Price for 24 hrs:</span>
                            <span class="rental-info-value">₹ ${parseFloat(rentalPrice24h).toFixed(2)}</span>
                        </div>
                        <div class="rental-info-item">
                            <span class="rental-info-label">Kilometer Limit per Booking:</span>
                            <span class="rental-info-value">${kmLimit}KM</span>
                        </div>
                        <div class="rental-info-item">
                            <span class="rental-info-label">Extra Rental Price per Hour:</span>
                            <span class="rental-info-value">₹ ${parseFloat(extraRentalPrice).toFixed(2)}</span>
                        </div>
                        <div class="rental-info-item">
                            <span class="rental-info-label">Extra Price per Kilometre:</span>
                            <span class="rental-info-value">₹ ${parseFloat(extraPriceKm).toFixed(2)}</span>
                        </div>
                    </div>
                `;
                
                // Insert after the icon
                this.parentElement.insertBefore(popover, this.nextSibling);
                
                // Add close functionality
                const closeBtn = popover.querySelector('.close-popover');
                closeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    popover.remove();
                });
            });
        });
        
        // Close popover when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.rental-info-icon') && !e.target.closest('.rental-info-popover')) {
                document.querySelectorAll('.rental-info-popover').forEach(popover => {
                    popover.remove();
                });
            }
        });
    });

</script>

<style>
.rental-info-popover {
    position: absolute;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 15px 20px;
    min-width: 300px;
    max-width: 350px;
    z-index: 1050;
    margin-top: 8px;
    margin-left: -250px;
}

.rental-info-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e9ecef;
}

.rental-info-header strong {
    color: #333;
    font-size: 14px;
}

.close-popover {
    cursor: pointer;
    color: #6c757d;
    font-size: 16px;
}

.close-popover:hover {
    color: #495057;
}

.rental-info-content {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.rental-info-item {
    display: flex;
    flex-direction: column;
    padding: 8px 0;
}

.rental-info-label {
    font-style: italic;
    color: #6c757d;
    font-size: 13px;
    margin-bottom: 4px;
}

.rental-info-value {
    font-weight: bold;
    color: #333;
    font-size: 14px;
}

.rental-info-icon:hover {
    color: #6B6ADE !important;
}

/* Modern Modal Styles */
.vehicle-limit-modal .modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
}

.vehicle-limit-modal .modal-header {
    border-bottom: 1px solid #e9ecef;
    padding: 20px 30px;
    background: #f8f9fa;
    border-radius: 16px 16px 0 0;
}

.vehicle-limit-modal .modal-header .modal-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
}

.vehicle-limit-modal .modal-body {
    padding: 30px;
}

.modal-icon-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin-bottom: 20px;
    box-shadow: 0 8px 20px rgba(107, 106, 222, 0.3);
}

.modal-title-text {
    font-size: 22px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 15px;
}

.modal-message {
    font-size: 15px;
    color: #4a5568;
    line-height: 1.6;
    margin-bottom: 0;
}

.modal-alert {
    display: flex;
    align-items: flex-start;
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 8px;
    padding: 12px 16px;
    color: #856404;
    font-size: 14px;
}

.modal-footer {
    padding: 20px 30px;
    border-top: 1px solid #e9ecef;
    background: #f8f9fa;
    border-radius: 0 0 16px 16px;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.modal-footer .btn {
    padding: 10px 20px;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.modal-footer .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
}

.modal-footer .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(107, 106, 222, 0.4);
}

.modal-footer .btn-danger {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border: none;
    color: white;
}

.modal-footer .btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(245, 87, 108, 0.4);
}

.modal-footer .btn-outline-secondary {
    border: 2px solid #6c757d;
    color: #6c757d;
}

.modal-footer .btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
    transform: translateY(-2px);
}

.btn-close {
    background: none;
    border: none;
    font-size: 18px;
    color: #6c757d;
    transition: all 0.3s ease;
}

.btn-close:hover {
    color: #333;
    transform: rotate(90deg);
}
</style>
@endsection