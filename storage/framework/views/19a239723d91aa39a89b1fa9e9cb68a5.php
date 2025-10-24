<?php $__env->startSection('title', 'Vehicle Details - ' . $vehicle->vehicle_make . ' ' . $vehicle->vehicle_model); ?>
<?php $__env->startSection('page-title', 'Vehicle Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <a href="<?php echo e(route('business.vehicles.index')); ?>" class="btn btn-link text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>Back to list
                </a>
            </div>
        </div>
    </div>

    <!-- Vehicle Name and Actions Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Vehicle Name Section -->
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <div class="d-flex align-items-center mb-1">
                            <div class="status-dot bg-success me-2"></div>
                            <span class="text-muted small"><?php echo e($vehicle->vehicle_make); ?></span>
                        </div>
                        <h2 class="mb-0 fw-bold"><?php echo e($vehicle->vehicle_model); ?></h2>
                    </div>
                </div>
                
                <!-- Vehicle Booking Status and Actions -->
                <div class="d-flex align-items-center">
                    <!-- Vehicle Booking Status -->
                    <div class="me-4">
                        <label class="form-label mb-1 small text-muted">Vehicle Booking Status</label>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <?php echo e(ucfirst($vehicle->vehicle_status)); ?>

                                <i class="fas fa-chevron-down ms-1"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="updateStatus('active')">Active</a></li>
                                <li><a class="dropdown-item" href="#" onclick="updateStatus('inactive')">Inactive</a></li>
                                <li><a class="dropdown-item" href="#" onclick="updateStatus('under_maintenance')">Under Maintenance</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-3">
                        <a href="<?php echo e(route('business.vehicles.edit', $vehicle)); ?>" class="btn btn-link text-muted text-decoration-none">
                            Edit
                        </a>
                        <button class="btn btn-link text-danger text-decoration-none" onclick="confirmDelete(<?php echo e($vehicle->id); ?>)">
                            Delete
                        </button>
                        <a href="<?php echo e(route('business.bookings.create', ['vehicle_id' => $vehicle->id])); ?>" class="btn btn-primary px-4">
                            Book
                        </a>
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
                    <h6 class="mb-0">
                        <i class="fas fa-images me-2"></i>Vehicle Images
                        <?php if($vehicle->images && $vehicle->images->count() > 0): ?>
                            <span class="badge bg-primary ms-2"><?php echo e($vehicle->images->count()); ?> image(s)</span>
                        <?php endif; ?>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <?php if($vehicle->images && $vehicle->images->count() > 0): ?>
                        <div class="row g-0">
                            <div class="col-8">
                                <div class="main-image-container" style="height: 400px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                    <?php
                                        $primaryImage = $vehicle->primaryImage ?? $vehicle->firstImage;
                                    ?>
                                    <?php if($primaryImage): ?>
                                        <img src="<?php echo e(asset('storage/' . $primaryImage->image_path)); ?>" 
                                             alt="<?php echo e($vehicle->vehicle_make); ?> <?php echo e($vehicle->vehicle_model); ?>" 
                                             class="img-fluid" 
                                             style="max-height: 100%; max-width: 100%; object-fit: cover;"
                                             id="mainImage">
                                    <?php else: ?>
                                        <div class="text-center text-muted">
                                            <i class="fas fa-<?php echo e($vehicle->vehicle_type === 'car' ? 'car' : ($vehicle->vehicle_type === 'bike_scooter' ? 'motorcycle' : 'truck')); ?> fa-5x mb-3"></i>
                                            <p>No Image Available</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="thumbnail-container p-3" style="height: 400px; overflow-y: auto;">
                                    <?php $__currentLoopData = $vehicle->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="thumbnail-item mb-2 <?php echo e($index === 0 ? 'active' : ''); ?>" 
                                             onclick="changeMainImage(this, '<?php echo e(asset('storage/' . $image->image_path)); ?>')"
                                             data-image-src="<?php echo e(asset('storage/' . $image->image_path)); ?>">
                                            <img src="<?php echo e(asset('storage/' . $image->image_path)); ?>" 
                                                 alt="Thumbnail <?php echo e($index + 1); ?>" 
                                                 class="img-fluid rounded" 
                                                 style="width: 100%; height: 60px; object-fit: cover; border: 2px solid <?php echo e($index === 0 ? '#6f42c1' : 'transparent'); ?>;">
                                            <?php if($image->is_primary): ?>
                                                <div class="position-absolute top-0 start-0">
                                                    <span class="badge bg-success" style="font-size: 0.7em;">Primary</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted p-5">
                            <i class="fas fa-<?php echo e($vehicle->vehicle_type === 'car' ? 'car' : ($vehicle->vehicle_type === 'bike_scooter' ? 'motorcycle' : 'truck')); ?> fa-5x mb-3"></i>
                            <p>No Images Available</p>
                        </div>
                    <?php endif; ?>
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
                                <?php if($vehicle->insurance_document_path && file_exists(public_path('storage/' . $vehicle->insurance_document_path))): ?>
                                    <a href="<?php echo e(asset('storage/' . $vehicle->insurance_document_path)); ?>" target="_blank" class="text-decoration-none">
                                        <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                        <p class="mb-0 small text-primary">Insurance</p>
                                        <small class="text-muted">Click to view</small>
                                    </a>
                                <?php else: ?>
                                    <i class="fas fa-file-pdf fa-3x text-muted mb-2"></i>
                                    <p class="mb-0 small text-muted">Insurance</p>
                                    <small class="text-muted">Not uploaded</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- RC Document -->
                        <div class="col-6 col-md-4 mb-3 text-center">
                            <div class="document-item">
                                <?php if($vehicle->rc_document_path && file_exists(public_path('storage/' . $vehicle->rc_document_path))): ?>
                                    <a href="<?php echo e(asset('storage/' . $vehicle->rc_document_path)); ?>" target="_blank" class="text-decoration-none">
                                        <i class="fas fa-file-image fa-3x text-success mb-2"></i>
                                        <p class="mb-0 small text-primary">RC Document</p>
                                        <small class="text-muted">Click to view</small>
                                    </a>
                                <?php else: ?>
                                    <i class="fas fa-file-image fa-3x text-muted mb-2"></i>
                                    <p class="mb-0 small text-muted">RC Document</p>
                                    <small class="text-muted">Not uploaded</small>
                                <?php endif; ?>
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
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->vehicle_type_display); ?></p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Vehicle Make</label>
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->vehicle_make); ?></p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Vehicle Model</label>
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->vehicle_model); ?></p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Vehicle Year</label>
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->vehicle_year); ?></p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">VIN / Chasis Number</label>
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->vin_number ?? 'N/A'); ?></p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Registration Number</label>
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->vehicle_number); ?></p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Vehicle Status</label>
                                <p class="mb-0 fw-bold">
                                    <span class="badge bg-<?php echo e($vehicle->status_badge_class); ?>"><?php echo e($vehicle->status_display); ?></span>
                                </p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Fuel Type</label>
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->fuel_type_display); ?></p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Transmission Type</label>
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->transmission_type_display); ?></p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Seating Capacity</label>
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->seating_capacity ?? 'N/A'); ?> Seats</p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Mileage</label>
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->mileage ?? 'N/A'); ?> KM/HR</p>
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
                                <p class="mb-0 fw-bold text-primary">₹<?php echo e(number_format($vehicle->rental_price_24h ?? 0, 2)); ?></p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">
                                    <i class="fas fa-info-circle me-1"></i>Kilometer Limit per Booking
                                </label>
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->km_limit_per_booking ?? 'N/A'); ?>KM</p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">
                                    <i class="fas fa-info-circle me-1"></i>Extra Rental Price per Hour
                                </label>
                                <p class="mb-0 fw-bold">₹<?php echo e(number_format($vehicle->extra_rental_price_per_hour ?? 0, 2)); ?></p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">
                                    <i class="fas fa-info-circle me-1"></i>Extra Price per Kilometre
                                </label>
                                <p class="mb-0 fw-bold">₹<?php echo e(number_format($vehicle->extra_price_per_km ?? 0, 2)); ?></p>
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
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->ownership_type_display); ?></p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Vendor Name</label>
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->vendor_name ?? 'N/A'); ?></p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Last Service Date</label>
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->last_service_date ? $vehicle->last_service_date->format('d/m/Y') : 'N/A'); ?></p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Meter Reading at Service Time</label>
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->last_service_meter_reading ? number_format($vehicle->last_service_meter_reading) . ' Kilometers' : 'N/A'); ?></p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Next Service Date</label>
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->next_service_due ? $vehicle->next_service_due->format('d/m/Y') : 'N/A'); ?></p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="info-item">
                                <label class="form-label small text-muted mb-1">Meter Reading for Next Service</label>
                                <p class="mb-0 fw-bold"><?php echo e($vehicle->next_service_meter_reading ? number_format($vehicle->next_service_meter_reading) . ' Kilometers' : 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <?php if($vehicle->remarks_notes): ?>
                    <div class="mt-3">
                        <label class="form-label small text-muted mb-1">Remarks / Notes</label>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-0"><?php echo e($vehicle->remarks_notes); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
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
    background: #f8f9fa;
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
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
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

function updateStatus(status) {
    
    fetch(`/business/vehicles/<?php echo e($vehicle->id); ?>/toggle-availability`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the dropdown button text
            const dropdownButton = document.querySelector('.dropdown-toggle');
            if (dropdownButton) {
                dropdownButton.textContent = status.charAt(0).toUpperCase() + status.slice(1);
            }
            showAlert('Status updated successfully!', 'success');
        } else {
            showAlert(data.message || 'Failed to update status', 'danger');
        }
    })
    .catch(error => {
        showAlert('An error occurred while updating status', 'danger');
    });
}

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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('business.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/business/vehicles/show.blade.php ENDPATH**/ ?>