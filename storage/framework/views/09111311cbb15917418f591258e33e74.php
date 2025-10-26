<?php $__env->startSection('title', 'Vehicle Management'); ?>
<?php $__env->startSection('page-title', 'Vehicle Management'); ?>

<?php $__env->startPush('styles'); ?>


<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<!-- Main Content -->

<!-- Search, Filters and Add Vehicle Section -->
<div class="row mb-3 align-items-end filter-row">
    <div class="col-md-4">
        <div class="input-group vehicle-search-bar">
            <span class="input-group-text">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" id="vehicleSearch" class="form-control" placeholder="Search">
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
        <?php
            $businessAdmin = auth('business_admin')->user();
            $business = $businessAdmin ? $businessAdmin->business : null;
            $subscription = $business ? $business->subscriptions()->whereIn('status', ['active', 'trial'])->first() : null;
            $capacityStatus = $subscription ? $subscription->getVehicleCapacityStatus() : null;
        ?>
        <?php if($capacityStatus && $capacityStatus['can_add']): ?>
            <a href="<?php echo e(route('business.vehicles.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Vehicle
            </a>
        <?php elseif($capacityStatus && !$capacityStatus['can_add']): ?>
            <button class="btn btn-primary" onclick="showVehicleLimitModal()">
                <i class="fas fa-plus me-2"></i>Add New Vehicle
            </button>
        <?php else: ?>
            <a href="<?php echo e(route('business.vehicles.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Vehicle
            </a>
        <?php endif; ?>
    </div>
</div>

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
                    <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="vechicle_title">
                            <div class="d-flex align-items-center">
                                <?php
                                    // Generate a consistent color based on vehicle ID
                                    $colors = ['#6B6ADE', '#FF6B6B', '#4ECDC4', '#FFE66D', '#FF8C94', '#95E1D3', '#F38181', '#AA96DA', '#FCBAD3', '#A8E6CF'];
                                    $colorIndex = $vehicle->id % count($colors);
                                    $vehicleColor = $colors[$colorIndex];
                                    
                                    $brandIcon = 'images/vehicle-brands/' . strtolower(str_replace(' ', '-', $vehicle->vehicle_make)) . '.svg';
                                    $brandIconPath = public_path($brandIcon);
                                    $defaultIcon = 'images/vehicle-brands/default.svg';
                                ?>
                                <?php if(file_exists($brandIconPath)): ?>
                                    <img src="<?php echo e(asset($brandIcon)); ?>" alt="<?php echo e($vehicle->vehicle_make); ?>" class="me-2" style="width: 30px; height: 30px;">
                                <?php else: ?>
                                    <div class="brand-icon-placeholder me-2 d-flex align-items-center justify-content-center vehicle-color-badge" 
                                         style="width: 30px; height: 30px; background: <?php echo e($vehicleColor); ?>; color: white; border-radius: 4px; font-size: 12px; font-weight: bold;">
                                        <?php echo e(strtoupper(substr($vehicle->vehicle_make, 0, 2))); ?>

                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold"><?php echo e($vehicle->vehicle_make); ?> <?php echo e($vehicle->vehicle_model); ?></div>
                                    <small class="text-muted"><?php echo e($vehicle->registration_number); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                                $unitType = '';
                                if($vehicle->vehicle_type === 'car') {
                                    $unitType = 'Car - ' . ucfirst($vehicle->transmission_type);
                                } elseif($vehicle->vehicle_type === 'bike_scooter') {
                                    $unitType = 'Bike/Scooter - ' . ucfirst($vehicle->bike_transmission_type ?? $vehicle->transmission_type);
                                } elseif($vehicle->vehicle_type === 'heavy_vehicle') {
                                    $unitType = 'Heavy Vehicle - ' . ucfirst($vehicle->transmission_type);
                                }
                            ?>
                            <?php echo e($unitType); ?>

                        </td>
                        <td><?php echo e(ucfirst($vehicle->fuel_type)); ?></td>
                        <td>
                            <?php if($vehicle->vehicle_type === 'car'): ?>
                                <?php echo e($vehicle->seating_capacity); ?> Seats
                            <?php elseif($vehicle->vehicle_type === 'bike_scooter'): ?>
                                <?php echo e($vehicle->engine_capacity_cc); ?>cc Engine
                            <?php elseif($vehicle->vehicle_type === 'heavy_vehicle'): ?>
                                <?php if($vehicle->seating_capacity): ?>
                                    <?php echo e($vehicle->seating_capacity); ?> Seats
                                <?php elseif($vehicle->payload_capacity_tons): ?>
                                <?php echo e($vehicle->payload_capacity_tons); ?> Tons
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            ₹ <?php echo e(number_format($vehicle->rental_price_24h, 2)); ?>

                            <i class="fas fa-info-circle text-muted ms-1 rental-info-icon" 
                               style="cursor: pointer;" 
                               data-vehicle-id="<?php echo e($vehicle->id); ?>"
                               data-rental-price-24h="<?php echo e($vehicle->rental_price_24h); ?>"
                               data-km-limit="<?php echo e($vehicle->km_limit_per_booking); ?>"
                               data-extra-rental-price="<?php echo e($vehicle->extra_rental_price_per_hour); ?>"
                               data-extra-price-km="<?php echo e($vehicle->extra_price_per_km); ?>"></i>
                        </td>
                        <td>
                            <?php
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
                            ?>
                            <div>
                                <span class="badge rounded-pill bg-<?php echo e($badgeClass); ?> text-white fw-bold px-2 py-1">
                                    <?php echo e($displayStatus); ?>

                                </span>
                                <?php if($vehicle->vehicle_status === 'inactive' && $vehicle->unavailable_until): ?>
                                    <br><small class="text-muted mt-1 d-block">Until: <?php echo e(\Carbon\Carbon::parse($vehicle->unavailable_until)->format('M d, Y')); ?></small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <a href="<?php echo e(route('business.vehicles.show', $vehicle)); ?>" class="text-primary text-decoration-none" style="font-weight: 500;">View</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        
        <?php if($vehicles->total() > 20): ?>
        <!-- Simple Pagination - Only Previous/Next -->
        <div class="d-flex justify-content-center mt-3">
            <nav aria-label="Vehicle pagination">
                <ul class="pagination">
                    <?php if($vehicles->onFirstPage()): ?>
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="fas fa-chevron-left"></i> Previous
                            </span>
                        </li>
                    <?php else: ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($vehicles->previousPageUrl()); ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if($vehicles->hasMorePages()): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($vehicles->nextPageUrl()); ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">
                                Next <i class="fas fa-chevron-right"></i>
                            </span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
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
                        <?php if($capacityStatus): ?>
                            <?php echo e($capacityStatus['message']); ?>

                        <?php else: ?>
                            You have reached the maximum number of vehicles allowed for your subscription package.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="<?php echo e(route('business.subscription.index')); ?>" class="btn btn-primary btn-lg">
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
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('business.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/business/vehicles/index.blade.php ENDPATH**/ ?>