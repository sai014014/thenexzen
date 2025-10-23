<?php $__env->startSection('title', 'Vehicle Details - ' . $vehicle->vehicle_make . ' ' . $vehicle->vehicle_model); ?>
<?php $__env->startSection('page-title', 'Vehicle Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8">
        <!-- Vehicle Information Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-car me-2"></i>Vehicle Information
                </h5>
                <div>
                    <a href="<?php echo e(route('business.vehicles.edit', $vehicle)); ?>" class="btn btn-warning btn-sm me-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="<?php echo e(route('business.vehicles.index')); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <?php if($vehicle->vehicle_image_path && file_exists(public_path('storage/' . $vehicle->vehicle_image_path))): ?>
                            <img src="<?php echo e(asset('storage/' . $vehicle->vehicle_image_path)); ?>" 
                                 alt="<?php echo e($vehicle->vehicle_make); ?> <?php echo e($vehicle->vehicle_model); ?>" 
                                 class="img-fluid rounded" 
                                 style="height: 200px; width: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-<?php echo e($vehicle->vehicle_type === 'car' ? 'car' : ($vehicle->vehicle_type === 'bike_scooter' ? 'motorcycle' : 'truck')); ?> fa-4x"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <h4 class="mb-3"><?php echo e($vehicle->vehicle_make); ?> <?php echo e($vehicle->vehicle_model); ?></h4>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <strong>Vehicle Type:</strong><br>
                                <span class="badge bg-info"><?php echo e($vehicle->vehicle_type_display); ?></span>
                            </div>
                             <div class="col-sm-6 mb-3">
                                 <strong>Status:</strong><br>
                                 <span class="badge bg-<?php echo e($vehicle->status_badge_class); ?>"><?php echo e($vehicle->status_display); ?></span>
                                 <br><span class="badge bg-<?php echo e($vehicle->availability_status_badge_class); ?>"><?php echo e($vehicle->availability_status); ?></span>
                                 <?php if($currentBookings->count() > 0): ?>
                                     <br><small class="text-dark" style="opacity: 1 !important;">
                                         <strong style="opacity: 1 !important;">Booked Periods:</strong><br>
                                         <?php $__currentLoopData = $currentBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                             <span class="badge bg-info me-1 mb-1" style="opacity: 1 !important;">
                                                 <?php echo e(\Carbon\Carbon::parse($booking->start_date_time)->format('M d, Y H:i')); ?> - 
                                                 <?php echo e(\Carbon\Carbon::parse($booking->end_date_time)->format('M d, Y H:i')); ?>

                                             </span><br>
                                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                     </small>
                                 <?php elseif($vehicle->unavailable_from || $vehicle->unavailable_until): ?>
                                     <br><small class="text-muted">
                                         <strong>Manual Unavailability:</strong><br>
                                         <?php if($vehicle->unavailable_from): ?>
                                             From: <?php echo e(\Carbon\Carbon::parse($vehicle->unavailable_from)->format('M d, Y H:i')); ?>

                                         <?php endif; ?>
                                         <?php if($vehicle->unavailable_from && $vehicle->unavailable_until): ?>
                                             <br>
                                         <?php endif; ?>
                                         <?php if($vehicle->unavailable_until): ?>
                                             Until: <?php echo e(\Carbon\Carbon::parse($vehicle->unavailable_until)->format('M d, Y H:i')); ?>

                                         <?php endif; ?>
                                     </small>
                                 <?php endif; ?>
                             </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <strong>Year:</strong><br>
                                <span class="text-muted"><?php echo e($vehicle->vehicle_year); ?></span>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <strong>Fuel Type:</strong><br>
                                <span class="badge bg-secondary"><?php echo e($vehicle->fuel_type_display); ?></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <strong>Vehicle Number:</strong><br>
                                <span class="text-muted"><?php echo e($vehicle->vehicle_number); ?></span>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <strong>Transmission:</strong><br>
                                <span class="text-muted"><?php echo e($vehicle->transmission_type_display); ?></span>
                            </div>
                        </div>

                        <?php if($vehicle->mileage): ?>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <strong>Mileage:</strong><br>
                                <span class="text-muted"><?php echo e($vehicle->mileage); ?> km/l</span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle Specifications Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>Vehicle Specifications
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if($vehicle->vehicle_type === 'car' && $vehicle->seating_capacity): ?>
                    <div class="col-md-6 mb-3">
                        <strong>Seating Capacity:</strong><br>
                        <span class="text-muted"><?php echo e($vehicle->seating_capacity); ?> Seater</span>
                    </div>
                    <?php endif; ?>

                    <?php if($vehicle->vehicle_type === 'bike_scooter' && $vehicle->engine_capacity_cc): ?>
                    <div class="col-md-6 mb-3">
                        <strong>Engine Capacity:</strong><br>
                        <span class="text-muted"><?php echo e($vehicle->engine_capacity_cc); ?>cc</span>
                    </div>
                    <?php endif; ?>

                    <?php if($vehicle->vehicle_type === 'heavy_vehicle'): ?>
                        <?php if($vehicle->seating_capacity): ?>
                        <div class="col-md-6 mb-3">
                            <strong>Seating Capacity:</strong><br>
                            <span class="text-muted"><?php echo e($vehicle->seating_capacity); ?> Seater</span>
                        </div>
                        <?php endif; ?>
                        <?php if($vehicle->payload_capacity_tons): ?>
                        <div class="col-md-6 mb-3">
                            <strong>Payload Capacity:</strong><br>
                            <span class="text-muted"><?php echo e($vehicle->payload_capacity_tons); ?> Tons</span>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Rental Pricing Information Card -->
        <?php if($vehicle->rental_price_24h): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-dollar-sign me-2"></i>Rental Pricing Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <strong>24-Hour Base Price:</strong><br>
                        <span class="text-primary h5">₹<?php echo e(number_format($vehicle->rental_price_24h)); ?></span>
                    </div>
                    <?php if($vehicle->km_limit_per_booking): ?>
                    <div class="col-md-3 mb-3">
                        <strong>KM Limit per Booking:</strong><br>
                        <span class="text-muted"><?php echo e($vehicle->km_limit_per_booking); ?> km</span>
                    </div>
                    <?php endif; ?>
                    <?php if($vehicle->extra_rental_price_per_hour): ?>
                    <div class="col-md-3 mb-3">
                        <strong>Extra Price per Hour:</strong><br>
                        <span class="text-muted">₹<?php echo e(number_format($vehicle->extra_rental_price_per_hour)); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($vehicle->extra_price_per_km): ?>
                    <div class="col-md-3 mb-3">
                        <strong>Extra Price per KM:</strong><br>
                        <span class="text-muted">₹<?php echo e(number_format($vehicle->extra_price_per_km)); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Ownership and Vendor Details Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-handshake me-2"></i>Ownership and Vendor Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Ownership Type:</strong><br>
                        <span class="badge bg-info"><?php echo e($vehicle->ownership_type_display); ?></span>
                    </div>
                    <?php if($vehicle->isVendorProvided() && $vehicle->vendor_name): ?>
                    <div class="col-md-6 mb-3">
                        <strong>Vendor Name:</strong><br>
                        <span class="text-muted"><?php echo e($vehicle->vendor_name); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if($vehicle->isVendorProvided() && $vehicle->commission_type): ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Commission Type:</strong><br>
                        <span class="text-muted"><?php echo e(ucfirst($vehicle->commission_type)); ?></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Commission Value:</strong><br>
                        <span class="text-muted">
                            ₹<?php echo e(number_format($vehicle->commission_value)); ?>

                            <?php if($vehicle->commission_type === 'percentage'): ?>
                                %
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Insurance and Legal Documents Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-shield-alt me-2"></i>Insurance and Legal Documents
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong>Insurance Provider:</strong><br>
                        <span class="text-muted"><?php echo e($vehicle->insurance_provider); ?></span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Policy Number:</strong><br>
                        <span class="text-muted"><?php echo e($vehicle->policy_number); ?></span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>Expiry Date:</strong><br>
                        <span class="text-muted"><?php echo e($vehicle->insurance_expiry_date->format('M d, Y')); ?></span>
                        <?php if($vehicle->insurance_expiry_date->isPast()): ?>
                            <span class="badge bg-danger ms-2">Expired</span>
                        <?php elseif($vehicle->insurance_expiry_date->diffInDays() <= 30): ?>
                            <span class="badge bg-warning ms-2">Expiring Soon</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>RC Number:</strong><br>
                        <span class="text-muted"><?php echo e($vehicle->rc_number); ?></span>
                    </div>
                </div>

                <div class="row">
                    <?php if($vehicle->insurance_document_path): ?>
                    <div class="col-md-6 mb-3">
                        <strong>Insurance Document:</strong><br>
                        <a href="<?php echo e(route('business.vehicles.download-document', [$vehicle, 'insurance'])); ?>" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-1"></i>Download Insurance
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if($vehicle->rc_document_path): ?>
                    <div class="col-md-6 mb-3">
                        <strong>RC Document:</strong><br>
                        <a href="<?php echo e(route('business.vehicles.download-document', [$vehicle, 'rc'])); ?>" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-1"></i>Download RC
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Maintenance Information Card -->
        <?php if($vehicle->last_service_date || $vehicle->next_service_due): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-wrench me-2"></i>Maintenance Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if($vehicle->last_service_date): ?>
                    <div class="col-md-3 mb-3">
                        <strong>Last Service Date:</strong><br>
                        <span class="text-muted"><?php echo e($vehicle->last_service_date->format('M d, Y')); ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if($vehicle->last_service_meter_reading): ?>
                    <div class="col-md-3 mb-3">
                        <strong>Last Service Meter:</strong><br>
                        <span class="text-muted"><?php echo e(number_format($vehicle->last_service_meter_reading)); ?> km</span>
                    </div>
                    <?php endif; ?>

                    <?php if($vehicle->next_service_due): ?>
                    <div class="col-md-3 mb-3">
                        <strong>Next Service Due:</strong><br>
                        <span class="text-muted"><?php echo e($vehicle->next_service_due->format('M d, Y')); ?></span>
                        <?php if($vehicle->next_service_due->isPast()): ?>
                            <span class="badge bg-danger ms-2">Overdue</span>
                        <?php elseif($vehicle->next_service_due->diffInDays() <= 30): ?>
                            <span class="badge bg-warning ms-2">Due Soon</span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if($vehicle->next_service_meter_reading): ?>
                    <div class="col-md-3 mb-3">
                        <strong>Next Service Meter:</strong><br>
                        <span class="text-muted"><?php echo e(number_format($vehicle->next_service_meter_reading)); ?> km</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Additional Information Card -->
        <?php if($vehicle->remarks_notes): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-sticky-note me-2"></i>Additional Information
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted"><?php echo e($vehicle->remarks_notes); ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <!-- Vehicle Availability Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-toggle-on me-2"></i>Vehicle Availability
                </h5>
            </div>
            <div class="card-body">
                 <div class="text-center mb-3">
                     <div class="form-check form-switch d-inline-block">
                         <input class="form-check-input" 
                                type="checkbox" 
                                id="availabilityToggle" 
                                <?php echo e($vehicle->is_available ? 'checked' : ''); ?>

                                <?php echo e($currentBookings->count() > 0 ? 'disabled' : ''); ?>

                                onchange="toggleAvailability()">
                         <label class="form-check-label" for="availabilityToggle">
                             <?php echo e($vehicle->is_available ? 'Available' : 'Unavailable'); ?>

                         </label>
                     </div>
                     <?php if($currentBookings->count() > 0): ?>
                     <div class="mt-2">
                         <small class="text-muted">
                             <i class="fas fa-info-circle me-1"></i>
                             Toggle disabled due to active bookings
                         </small>
                     </div>
                     <?php endif; ?>
                 </div>

                <?php if(!$vehicle->is_available || $currentBookings->count() > 0): ?>
                <div class="alert alert-warning booking-details-alert" style="opacity: 1 !important; transition: none !important;">
                    <?php if($currentBookings->count() > 0): ?>
                        <strong class="text-dark" style="opacity: 1 !important;">Booked Periods:</strong><br>
                        <?php $__currentLoopData = $currentBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mb-2" style="opacity: 1 !important;">
                                <strong class="text-dark" style="opacity: 1 !important;">Booking #<?php echo e($booking->booking_number); ?>:</strong><br>
                                <div class="text-dark" style="opacity: 1 !important;">
                                    From: <?php echo e(\Carbon\Carbon::parse($booking->start_date_time)->format('M d, Y H:i')); ?><br>
                                    Until: <?php echo e(\Carbon\Carbon::parse($booking->end_date_time)->format('M d, Y H:i')); ?>

                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php elseif($vehicle->unavailable_from || $vehicle->unavailable_until): ?>
                        <strong>Manual Unavailability:</strong><br>
                        <?php if($vehicle->unavailable_from): ?>
                            From: <?php echo e($vehicle->unavailable_from->format('M d, Y')); ?><br>
                        <?php endif; ?>
                        <?php if($vehicle->unavailable_until): ?>
                            Until: <?php echo e($vehicle->unavailable_until->format('M d, Y')); ?>

                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <form id="availabilityForm" method="POST" action="<?php echo e(route('business.vehicles.toggle-availability', $vehicle)); ?>" style="display: none;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="is_available" id="is_available_input">
                    <div class="mb-3">
                        <label for="unavailable_from" class="form-label">Unavailable From</label>
                        <input type="date" class="form-control" id="unavailable_from" name="unavailable_from">
                    </div>
                    <div class="mb-3">
                        <label for="unavailable_until" class="form-label">Unavailable Until</label>
                        <input type="date" class="form-control" id="unavailable_until" name="unavailable_until">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Update Availability</button>
                </form>
            </div>
        </div>

        <!-- Recent Bookings Card -->
        <?php if($recentBookings->count() > 0): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Recent Bookings
                </h5>
            </div>
            <div class="card-body">
                <?php $__currentLoopData = $recentBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border-bottom pb-2 mb-2">
                    <div class="d-flex justify-content-between">
                        <strong>#<?php echo e($booking->booking_number); ?></strong>
                        <span class="badge bg-<?php echo e($booking->status === 'completed' ? 'success' : 'warning'); ?>">
                            <?php echo e(ucfirst($booking->status)); ?>

                        </span>
                    </div>
                    <small class="text-muted">
                        <?php echo e(\Carbon\Carbon::parse($booking->start_date_time)->format('M d, Y H:i')); ?> - 
                        <?php echo e(\Carbon\Carbon::parse($booking->end_date_time)->format('M d, Y H:i')); ?>

                    </small>
                    <br>
                    <small>Customer: <?php echo e($booking->customer->full_name ?? $booking->customer->company_name); ?></small>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Actions Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo e(route('business.vehicles.edit', $vehicle)); ?>" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Vehicle
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete(<?php echo e($vehicle->id); ?>)">
                        <i class="fas fa-trash me-2"></i>Delete Vehicle
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Ensure booking details never fade */
.booking-details-alert {
    opacity: 1 !important;
    transition: none !important;
    animation: none !important;
}

.booking-details-alert * {
    opacity: 1 !important;
    transition: none !important;
    animation: none !important;
}

 /* Prevent any fade effects on booking details */
 .booking-details-alert .text-dark,
 .booking-details-alert strong,
 .booking-details-alert small,
 .booking-details-alert .badge {
     opacity: 1 !important;
     transition: none !important;
     animation: none !important;
 }

 /* Disabled toggle styling */
 .form-check-input:disabled {
     opacity: 0.5;
     cursor: not-allowed;
 }

 .form-check-input:disabled + .form-check-label {
     opacity: 0.7;
     cursor: not-allowed;
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
function confirmDelete(vehicleId) {
    const form = document.getElementById('deleteForm');
    form.action = `/business/vehicles/${vehicleId}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

 function toggleAvailability() {
     const toggle = document.getElementById('availabilityToggle');
     
     // Don't proceed if toggle is disabled (due to active bookings)
     if (toggle.disabled) {
         return;
     }
     
     const form = document.getElementById('availabilityForm');
     const isAvailableInput = document.getElementById('is_available_input');
     
     isAvailableInput.value = toggle.checked ? '1' : '0';
     
     if (toggle.checked) {
         form.style.display = 'none';
         // Automatically submit form when marking as available
         submitAvailabilityForm();
     } else {
         form.style.display = 'block';
     }
 }

function submitAvailabilityForm() {
    const form = document.getElementById('availabilityForm');
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
    submitBtn.disabled = true;
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        } else {
            return response.text().then(text => {
                throw new Error('Server error: ' + response.status + ' - ' + text);
            });
        }
    })
    .then(data => {
        if (data.success) {
            showSuccessAlert(data.message);
            // Update the toggle label
            const toggle = document.getElementById('availabilityToggle');
            const label = document.querySelector('label[for="availabilityToggle"]');
            label.textContent = data.is_available ? 'Available' : 'Unavailable';
            
            // Update the unavailable period display
            updateUnavailablePeriodDisplay(data);
        } else {
            showErrorAlert(data.message || 'Failed to update availability');
            // Revert toggle state
            const toggle = document.getElementById('availabilityToggle');
            toggle.checked = !toggle.checked;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorAlert('An error occurred while updating availability: ' + error.message);
        // Revert toggle state
        const toggle = document.getElementById('availabilityToggle');
        toggle.checked = !toggle.checked;
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function updateUnavailablePeriodDisplay(data) {
    const unavailablePeriod = document.querySelector('.alert-warning');
    if (data.is_available) {
        // Hide unavailable period display
        if (unavailablePeriod) {
            unavailablePeriod.style.display = 'none';
        }
    } else {
        // Show or update unavailable period display
        if (unavailablePeriod) {
            let content = '<strong>Unavailable Period:</strong><br>';
            if (data.unavailable_from) {
                const fromDate = new Date(data.unavailable_from).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
                content += `From: ${fromDate}<br>`;
            }
            if (data.unavailable_until) {
                const untilDate = new Date(data.unavailable_until).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
                content += `Until: ${untilDate}`;
            }
            unavailablePeriod.innerHTML = content;
            unavailablePeriod.style.display = 'block';
        }
    }
}

function showSuccessAlert(message) {
    const alertHtml = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.querySelector('.card-body').insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert-success.alert-dismissible');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}

function showErrorAlert(message) {
    const alertHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.querySelector('.card-body').insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert-danger.alert-dismissible');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}

// Initialize form visibility and form submission
document.addEventListener('DOMContentLoaded', function() {
    toggleAvailability();
    
    // Handle manual form submission
    const availabilityForm = document.getElementById('availabilityForm');
    if (availabilityForm) {
        availabilityForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitAvailabilityForm();
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('business.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/business/vehicles/show.blade.php ENDPATH**/ ?>