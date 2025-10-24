<?php $__env->startSection('title', 'Vehicle Management'); ?>
<?php $__env->startSection('page-title', 'Vehicle Management'); ?>

<?php $__env->startSection('content'); ?>
<!-- Main Content -->
<link rel="stylesheet" href="<?php echo e(asset('dist/css/VehicleManagement/vehicleManagement_view.css')); ?>">

<!-- Vehicle Statistics Cards -->
<div class="vehicle-stats-container">
    <div class="vehicle-stat-card">
        <div class="stat-icon">
            <i class="fas fa-car"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo e($vehicles->total()); ?></div>
            <div class="stat-label">All Vehicles</div>
        </div>
    </div>
    <div class="vehicle-stat-card">
        <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo e($vehicles->where('vehicle_status', 'active')->count()); ?></div>
            <div class="stat-label">Available Vehicles</div>
        </div>
    </div>
    <div class="vehicle-stat-card">
        <div class="stat-icon">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number"><?php echo e($vehicles->where('vehicle_status', 'booked')->count()); ?></div>
            <div class="stat-label">Booked Vehicles</div>
        </div>
    </div>
</div>
<style>
    /* Vehicle Statistics Cards */
    .vehicle-stats-container {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        padding: 0 1rem;
    }
    
    .vehicle-stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .vehicle-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .vehicle-stat-card:nth-child(1) .stat-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .vehicle-stat-card:nth-child(2) .stat-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .vehicle-stat-card:nth-child(3) .stat-icon {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .stat-content {
        flex: 1;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        line-height: 1;
        margin-bottom: 0.25rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #718096;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    @media (max-width: 768px) {
        .vehicle-stats-container {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .vehicle-stat-card {
            padding: 1rem;
        }
        
        .stat-number {
            font-size: 1.5rem;
        }
    }

    .price-tooltip {
        position: absolute;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        z-index: 100;
        width: 200px;
        display: none;
        font-size: 13px;
    }

    .price-tooltip h4 {
        margin: 0 0 8px;
        padding-bottom: 5px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
    }

    .price-tooltip p {
        margin: 5px 0;
        display: flex;
        justify-content: space-between;
    }

    .fas.fa-info-circle {
        cursor: pointer;
        position: relative;
    }

    /* Add different colors for each row to differentiate */
    tr:nth-child(4n+1) .price-tooltip {
        border-left: 4px solid #4285f4;
    }

    tr:nth-child(4n+2) .price-tooltip {
        border-left: 4px solid #ea4335;
    }

    tr:nth-child(4n+3) .price-tooltip {
        border-left: 4px solid #fbbc05;
    }

    tr:nth-child(4n+4) .price-tooltip {
        border-left: 4px solid #34a853;
    }

    /* Search and Add Vehicle Section Styling */
    .search-container .input-group {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .search-container .input-group-text {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #6c757d;
    }

    .search-container .form-control {
        border: 1px solid #dee2e6;
        border-left: none;
    }

    .search-container .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    .add-vehicle-container .btn {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    /* Filter Container Styling */
    .filter-container {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        margin-bottom: 1rem;
    }

    .filter-container .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .filter-container .form-select {
        border-radius: 6px;
        border: 1px solid #ced4da;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .filter-container .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    .filter-container .btn {
        border-radius: 6px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .filter-container .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
    }

    .filter-container .btn-outline-secondary:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }

    .add-vehicle-container .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    /* Responsive design for search and add section */
    @media (max-width: 768px) {
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }
        
        .search-container .input-group {
            width: 100% !important;
        }
        
        .add-vehicle-container {
            width: 100%;
        }
        
        .add-vehicle-container .btn {
            width: 100%;
        }
    }
</style>
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
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" id="clearFilters" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Clear Filters
                </button>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="search-container">
            <div class="input-group" style="width: 300px;">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" id="vehicleSearch" class="form-control" placeholder="Search vehicles...">
            </div>
        </div>
        <div class="add-vehicle-container">
            <a href="<?php echo e(route('business.vehicles.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Vehicle
            </a>
        </div>
    </div>
    
    <div class="record-count"><?php echo e($vehicles->total()); ?> Records Found, Page <?php echo e($vehicles->currentPage()); ?> of <?php echo e($vehicles->lastPage()); ?></div>
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
                                    $brandIcon = 'images/vehicle-brands/' . strtolower(str_replace(' ', '-', $vehicle->vehicle_make)) . '.svg';
                                    $brandIconPath = public_path($brandIcon);
                                    $defaultIcon = 'images/vehicle-brands/default.svg';
                                ?>
                                <?php if(file_exists($brandIconPath)): ?>
                                    <img src="<?php echo e(asset($brandIcon)); ?>" alt="<?php echo e($vehicle->vehicle_make); ?>" class="me-2" style="width: 30px; height: 30px;">
                                <?php else: ?>
                                    <div class="brand-icon-placeholder me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background: #f8f9fa; border-radius: 4px; font-size: 12px; font-weight: bold; color: #6c757d;">
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

                            <i class="fas fa-info-circle text-muted ms-1" style="cursor: pointer;"></i>
                        </td>
                        <td>
                            <?php
                                $displayStatus = 'Available';
                                $badgeClass = 'success';

                                if ($vehicle->vehicle_status === 'under_maintenance') {
                                    $displayStatus = 'Maintenance';
                                    $badgeClass = 'danger';
                                } elseif ($vehicle->bookings()->whereIn('status', ['ongoing', 'upcoming'])->where('end_date_time', '>=', \Carbon\Carbon::now())->exists()) {
                                    $displayStatus = 'Booked';
                                    $badgeClass = 'warning';
                                }
                            ?>
                            <span class="badge rounded-pill bg-<?php echo e($badgeClass); ?> text-white fw-bold px-2 py-1">
                                <?php echo e($displayStatus); ?>

                            </span>
                        </td>
                        <td class="text-end">
                            <a href="<?php echo e(route('business.vehicles.show', $vehicle)); ?>" class="text-primary text-decoration-none">View</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination controls -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="pagination-info">
                <span class="text-muted">Rows per page: 10</span>
                <span class="text-muted ms-3"><?php echo e($vehicles->firstItem()); ?>-<?php echo e($vehicles->lastItem()); ?> of <?php echo e($vehicles->total()); ?></span>
            </div>
            <nav aria-label="Vehicle pagination">
                <ul class="pagination mb-0">
                    <?php if($vehicles->onFirstPage()): ?>
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        </li>
                    <?php else: ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($vehicles->previousPageUrl()); ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if($vehicles->hasMorePages()): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($vehicles->nextPageUrl()); ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<script>
    const RECORDS_PER_PAGE = 10;
    const RECORDS_PER_PAGE_OPTIONS = [10, 25, 50, 100];
    const VEHICLE_STATUS_ARRAY = ['Active', 'Inactive', 'Under Maintenance'];

    // Live search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('vehicleSearch');
        const tableBody = document.getElementById('vehicleTableBody');
        const rows = tableBody.querySelectorAll('tr');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update record count
            const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
            const recordCount = document.querySelector('.record-count');
            if (recordCount) {
                recordCount.textContent = `${visibleRows.length} Records Found (filtered from ${rows.length} total)`;
            }
        });
        
        // Clear search functionality
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                this.dispatchEvent(new Event('input'));
            }
        });

        // Filter functionality
        const vehicleTypeFilter = document.getElementById('vehicleTypeFilter');
        const statusFilter = document.getElementById('statusFilter');
        const fuelTypeFilter = document.getElementById('fuelTypeFilter');
        const clearFiltersBtn = document.getElementById('clearFilters');

        function applyFilters() {
            const vehicleType = vehicleTypeFilter.value.toLowerCase();
            const status = statusFilter.value.toLowerCase();
            const fuelType = fuelTypeFilter.value.toLowerCase();
            const searchTerm = searchInput.value.toLowerCase().trim();

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
                    recordCount.textContent = `${totalRows} Records Found, Page <?php echo e($vehicles->currentPage()); ?> of <?php echo e($vehicles->lastPage()); ?>`;
                }
            }
        }

        // Event listeners for filters
        clearFiltersBtn.addEventListener('click', function() {
            vehicleTypeFilter.value = '';
            statusFilter.value = '';
            fuelTypeFilter.value = '';
            searchInput.value = '';
            applyFilters();
        });

        // Live filters - apply automatically on change
        vehicleTypeFilter.addEventListener('change', applyFilters);
        statusFilter.addEventListener('change', applyFilters);
        fuelTypeFilter.addEventListener('change', applyFilters);
    });

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('business.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/business/vehicles/index.blade.php ENDPATH**/ ?>