<?php $__env->startSection('title', 'Edit Vehicle - ' . $business->business_name); ?>
<?php $__env->startSection('page-title', 'Edit Vehicle'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Vehicle Add Page Specific Styles */
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


/* Document upload progress */
.upload-progress {
    margin-top: 10px;
    padding: 8px;
    background: #f8f9fa;
    border-radius: 4px;
    display: none;
}

.upload-progress.active {
    display: block;
}

.upload-progress .progress-bar-container {
    background: #e9ecef;
    border-radius: 4px;
    height: 8px;
    overflow: hidden;
}

.upload-progress .progress-bar-fill {
    background: linear-gradient(135deg, #6B6ADE 0%, #3C3CE1 100%);
    height: 100%;
    transition: width 0.3s ease;
}

.upload-progress .progress-text {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}

/* Loading button state */
.save-button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="vehicle-add-container">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form method="POST" action="<?php echo e(route('business.vehicles.update', $vehicle)); ?>" enctype="multipart/form-data" id="vehicleForm">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
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
                            <select class="form-select <?php $__errorArgs = ['vehicle_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="vehicle_type" 
                                    name="vehicle_type" 
                                    required>
                                <option value="">Select Vehicle Type</option>
                                <option value="car" <?php echo e(old('vehicle_type', $vehicle->vehicle_type) == 'car' ? 'selected' : ''); ?>>Car</option>
                                <option value="bike_scooter" <?php echo e(old('vehicle_type', $vehicle->vehicle_type) == 'bike_scooter' ? 'selected' : ''); ?>>Bike/Scooter</option>
                                <option value="heavy_vehicle" <?php echo e(old('vehicle_type', $vehicle->vehicle_type) == 'heavy_vehicle' ? 'selected' : ''); ?>>Heavy Vehicle</option>
                            </select>
                            <?php $__errorArgs = ['vehicle_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vehicle_make" class="form-label required">Vehicle Make</label>
                            <select class="form-select <?php $__errorArgs = ['vehicle_make'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="vehicle_make" 
                                    name="vehicle_make" 
                                    required>
                                <option value="">Select Vehicle Make</option>
                            </select>
                            <?php $__errorArgs = ['vehicle_make'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vehicle_model" class="form-label required">Vehicle Model</label>
                            <select class="form-select <?php $__errorArgs = ['vehicle_model'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="vehicle_model" 
                                    name="vehicle_model" 
                                    required 
                                    disabled>
                                <option value="">Select Vehicle Model</option>
                            </select>
                            <?php $__errorArgs = ['vehicle_model'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vehicle_year" class="form-label required">Vehicle Year</label>
                                    <input type="number" 
                                           class="form-control <?php $__errorArgs = ['vehicle_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="vehicle_year" 
                                    name="vehicle_year" 
                                           value="<?php echo e(old('vehicle_year', $vehicle->vehicle_year)); ?>" 
                                           min="1990" 
                                           max="<?php echo e(date('Y') + 1); ?>"
                                           placeholder="Enter Year"
                                    required>
                            <?php $__errorArgs = ['vehicle_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>
                    </div>

                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vin_number" class="form-label required">Vehicle Identification Number (VIN/Chassis Number)</label>
                                    <input type="text" 
                                           class="form-control <?php $__errorArgs = ['vin_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="vin_number" 
                                           name="vin_number" 
                                           value="<?php echo e(old('vin_number', $vehicle->vin_number)); ?>" 
                                           placeholder="Enter VIN/Chassis Number"
                                           required>
                                    <?php $__errorArgs = ['vin_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicle_number" class="form-label required">Registration Number</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['vehicle_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="vehicle_number" 
                                   name="vehicle_number" 
                                   value="<?php echo e(old('vehicle_number', $vehicle->vehicle_number)); ?>" 
                                           placeholder="Enter Registration Number"
                                   required>
                            <?php $__errorArgs = ['vehicle_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="vehicle_status" class="form-label required">Vehicle Status</label>
                            <select class="form-select <?php $__errorArgs = ['vehicle_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="vehicle_status" 
                                    name="vehicle_status" 
                                    required>
                                <option value="active" <?php echo e(old('vehicle_status', $vehicle->vehicle_status) == 'active' ? 'selected' : ''); ?>>Active</option>
                                <option value="inactive" <?php echo e(old('vehicle_status', $vehicle->vehicle_status) == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                <option value="under_maintenance" <?php echo e(old('vehicle_status', $vehicle->vehicle_status) == 'under_maintenance' ? 'selected' : ''); ?>>Under Maintenance</option>
                            </select>
                            <?php $__errorArgs = ['vehicle_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fuel_type" class="form-label required">Fuel Type</label>
                            <select class="form-select <?php $__errorArgs = ['fuel_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="fuel_type" 
                                    name="fuel_type" 
                                    required>
                                <option value="">Select Fuel Type</option>
                                <option value="petrol" <?php echo e(old('fuel_type', $vehicle->fuel_type) == 'petrol' ? 'selected' : ''); ?>>Petrol</option>
                                <option value="diesel" <?php echo e(old('fuel_type', $vehicle->fuel_type) == 'diesel' ? 'selected' : ''); ?>>Diesel</option>
                                <option value="cng" <?php echo e(old('fuel_type', $vehicle->fuel_type) == 'cng' ? 'selected' : ''); ?>>CNG</option>
                                <option value="electric" <?php echo e(old('fuel_type', $vehicle->fuel_type) == 'electric' ? 'selected' : ''); ?>>Electric</option>
                                <option value="hybrid" <?php echo e(old('fuel_type', $vehicle->fuel_type) == 'hybrid' ? 'selected' : ''); ?>>Hybrid</option>
                            </select>
                            <?php $__errorArgs = ['fuel_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                            <div class="col-md-3" id="default-transmission-field" style="display: none;">
                                <div class="form-group">
                                    <label for="transmission_type" class="form-label required">Transmission Type</label>
                            <select class="form-select <?php $__errorArgs = ['transmission_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="transmission_type" 
                                            name="transmission_type" 
                                            required>
                                        <option value="">Select Transmission Type</option>
                                        <!-- Options will be populated by JavaScript based on vehicle type -->
                            </select>
                            <?php $__errorArgs = ['transmission_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mileage" class="form-label">Mileage</label>
                                    <input type="text" 
                                   class="form-control <?php $__errorArgs = ['mileage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="mileage" 
                                   name="mileage" 
                                   value="<?php echo e(old('mileage', $vehicle->mileage)); ?>" 
                                           placeholder="">
                            <?php $__errorArgs = ['mileage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        </div>
                    </div>

                    <!-- Vehicle Type Specific Fields -->
                    <div class="row" id="car-fields" style="display: none;">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="seating_capacity" class="form-label required">Seating Capacity</label>
                                <select class="form-select <?php $__errorArgs = ['seating_capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        id="seating_capacity" 
                                       name="seating_capacity" 
                                        required>
                                    <option value="">Select Seating Capacity</option>
                                    <?php for($i = 1; $i <= 20; $i++): ?>
                                        <option value="<?php echo e($i); ?>" <?php echo e(old('seating_capacity', $vehicle->seating_capacity) == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <div class="helper-text">Number of seats (for cars)</div>
                                <?php $__errorArgs = ['seating_capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="bike-scooter-fields" style="display: none;">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="engine_capacity_cc" class="form-label required">Engine Capacity (CC)</label>
                                <input type="number" 
                                       class="form-control <?php $__errorArgs = ['engine_capacity_cc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="engine_capacity_cc" 
                                       name="engine_capacity_cc" 
                                       value="<?php echo e(old('engine_capacity_cc', $vehicle->engine_capacity_cc)); ?>" 
                                       min="0"
                                       placeholder="150">
                                <div class="helper-text">Engine cubic capacity in CC (for bikes and scooters)</div>
                                <?php $__errorArgs = ['engine_capacity_cc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                        <div class="row" id="heavy-vehicle-fields" style="display: none;">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="seating_capacity_heavy" class="form-label">Seating Capacity</label>
                                <select class="form-select <?php $__errorArgs = ['seating_capacity_heavy'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        id="seating_capacity_heavy" 
                                        name="seating_capacity_heavy">
                                    <option value="">Select Seating Capacity</option>
                                    <?php for($i = 1; $i <= 100; $i++): ?>
                                        <option value="<?php echo e($i); ?>" <?php echo e(old('seating_capacity_heavy', $vehicle->seating_capacity_heavy) == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <div class="helper-text">Number of seats (for heavy vehicles)</div>
                                <?php $__errorArgs = ['seating_capacity_heavy'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="payload_capacity_tons" class="form-label">Payload Capacity (Tons)</label>
                                <input type="number" 
                                       class="form-control <?php $__errorArgs = ['payload_capacity_tons'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="payload_capacity_tons" 
                                       name="payload_capacity_tons" 
                                       value="<?php echo e(old('payload_capacity_tons', $vehicle->payload_capacity_tons)); ?>" 
                                       step="0.01"
                                       min="0" 
                                       placeholder="2.5">
                                <div class="helper-text">Weight capacity in tons (for heavy vehicles)</div>
                                <?php $__errorArgs = ['payload_capacity_tons'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Upload Vehicle Images</label>
                                    
                                    <?php if(isset($vehicle->images) && $vehicle->images->count() > 0): ?>
                                    <div class="existing-images mb-3">
                                        <p class="text-muted mb-2"><strong>Existing Images:</strong></p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php $__currentLoopData = $vehicle->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="position-relative existing-image-item" data-image-id="<?php echo e($image->id); ?>" style="width: 100px; height: 100px; border: 2px solid #ced4da; border-radius: 8px; overflow: hidden;">
                                                <img src="<?php echo e(asset('storage/' . $image->image_path)); ?>" alt="Vehicle Image" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='<?php echo e(asset('images/car.png')); ?>';">
                                                <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 2px; right: 2px; padding: 2px 6px; font-size: 10px;" onclick="removeExistingImage(<?php echo e($image->id); ?>, this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <?php if($image->is_primary): ?>
                                                <span class="badge bg-success position-absolute" style="bottom: 2px; left: 2px; font-size: 8px;">Primary</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="file-upload-area" onclick="document.getElementById('vehicle_images').click()">
                                        <div class="upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <p class="upload-text">Click to upload or drag and drop the file here. Supported format JPG, PNG</p>
                                    </div>
                                    <input type="file" 
                                           class="form-control <?php $__errorArgs = ['vehicle_images'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="vehicle_images" 
                                           name="vehicle_images[]" 
                                           accept="image/*" 
                                           multiple
                                           style="display: none;">
                                    <input type="hidden" name="uploaded_image_ids" id="uploaded_image_ids" value="">
                                    <input type="hidden" name="deleted_image_ids" id="deleted_image_ids" value="">
                                    <div id="vehicleImagesContainer" class="d-flex flex-wrap gap-2 mt-2" style="display: none;"></div>
                                    <?php $__errorArgs = ['vehicle_images'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <div class="helper-text">Upload clear, high-quality images of your vehicle from different angles</div>
                        </div>
                    </div>
                        </div>
                    </div>

                    <!-- Section 2: Vehicle Rental Information -->
                    <div class="form-section">
                        <h3 class="section-title">Vehicle Rental Information</h3>

                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rental_price_24h" class="form-label required">Rental Price for 24 Hours (Base Price)</label>
                            <input type="number" 
                                   class="form-control <?php $__errorArgs = ['rental_price_24h'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="rental_price_24h" 
                                   name="rental_price_24h" 
                                   value="<?php echo e(old('rental_price_24h', $vehicle->rental_price_24h)); ?>" 
                                   step="0.01" 
                                           min="0"
                                           placeholder="Enter Amount">
                                    <div class="helper-text">The default price for renting the vehicle for a 24-hour period</div>
                            <?php $__errorArgs = ['rental_price_24h'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="km_limit_per_booking" class="form-label required">Kilometre Limit per Booking (Base Limit)</label>
                            <input type="number" 
                                   class="form-control <?php $__errorArgs = ['km_limit_per_booking'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="km_limit_per_booking" 
                                   name="km_limit_per_booking" 
                                   value="<?php echo e(old('km_limit_per_booking', $vehicle->km_limit_per_booking)); ?>" 
                                           min="0"
                                           placeholder="0">
                                    <div class="helper-text">The maximum number of kilometres included in the base price for a single booking (usually for 24 hours).</div>
                            <?php $__errorArgs = ['km_limit_per_booking'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="extra_rental_price_per_hour" class="form-label required">Extra Rental Price per Hour (if extended)</label>
                            <input type="number" 
                                   class="form-control <?php $__errorArgs = ['extra_rental_price_per_hour'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="extra_rental_price_per_hour" 
                                   name="extra_rental_price_per_hour" 
                                   value="<?php echo e(old('extra_rental_price_per_hour', $vehicle->extra_rental_price_per_hour)); ?>" 
                                   step="0.01" 
                                           min="0"
                                           placeholder="Enter Amount">
                                    <div class="helper-text">The additional charge if the vehicle is kept beyond the booking period (beyond 24 hours or the specified rental time).</div>
                            <?php $__errorArgs = ['extra_rental_price_per_hour'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="extra_price_per_km" class="form-label required">Extra Price per Kilometre (after base km limit)</label>
                            <input type="number" 
                                   class="form-control <?php $__errorArgs = ['extra_price_per_km'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="extra_price_per_km" 
                                   name="extra_price_per_km" 
                                   value="<?php echo e(old('extra_price_per_km', $vehicle->extra_price_per_km)); ?>" 
                                   step="0.01" 
                                           min="0"
                                           placeholder="Enter Amount">
                                    <div class="helper-text">The additional charge if the customer drives beyond the kilometer limit set for the booking.</div>
                            <?php $__errorArgs = ['extra_price_per_km'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                        </div>
                    </div>

                    <!-- Section 3: Ownership and Vendor Details -->
                    <div class="form-section">
                        <h3 class="section-title">Ownership and Vendor Details</h3>

                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ownership_type" class="form-label required">Ownership Type</label>
                            <select class="form-select <?php $__errorArgs = ['ownership_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="ownership_type" 
                                    name="ownership_type" 
                                            required
                                            onchange="toggleVendorFields()">
                                <option value="">Select Ownership Type</option>
                                <option value="owned" <?php echo e(old('ownership_type', $vehicle->ownership_type) == 'owned' ? 'selected' : ''); ?>>Owned</option>
                                <option value="leased" <?php echo e(old('ownership_type', $vehicle->ownership_type) == 'leased' ? 'selected' : ''); ?>>Leased</option>
                                <option value="vendor_provided" <?php echo e(old('ownership_type', $vehicle->ownership_type) == 'vendor_provided' ? 'selected' : ''); ?>>Vendor Provided</option>
                            </select>
                            <?php $__errorArgs = ['ownership_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>

                            <div class="col-md-6 vendor-fields" style="display: none;">
                                <div class="form-group">
                            <label for="vendor_name" class="form-label">Vendor Name</label>
                                    <div class="vendor-dropdown-container">
                                        <div class="vendor-dropdown-wrapper">
                                <input type="text" 
                                                   class="form-control vendor-search-input <?php $__errorArgs = ['vendor_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="vendor_name" 
                                   name="vendor_name" 
                                                   value="<?php echo e(old('vendor_name', $vehicle->vendor_name)); ?>"
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
                            <?php $__errorArgs = ['vendor_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Commission Fields (only for vendor-provided vehicles) -->
                        <div class="row vendor-fields" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                            <label for="commission_type" class="form-label">Commission Type</label>
                            <select class="form-select <?php $__errorArgs = ['commission_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="commission_type" 
                                    name="commission_type">
                                        <option value="">Select Commission Type</option>
                                        <?php
                                            $commissionValue = '';
                                            if($vehicle->commission_type) {
                                                if($vehicle->commission_type == 'fixed_amount' || $vehicle->commission_type == 'fixed') {
                                                    $commissionValue = 'fixed';
                                                } elseif($vehicle->commission_type == 'percentage_of_revenue' || $vehicle->commission_type == 'percentage') {
                                                    $commissionValue = 'percentage';
                                                }
                                            }
                                        ?>
                                        <option value="fixed" <?php echo e(old('commission_type', $commissionValue) == 'fixed' ? 'selected' : ''); ?>>Fixed Amount</option>
                                        <option value="percentage" <?php echo e(old('commission_type', $commissionValue) == 'percentage' ? 'selected' : ''); ?>>Percentage</option>
                            </select>
                                    <div class="helper-text">Required for vendor-provided vehicles</div>
                            <?php $__errorArgs = ['commission_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>

                            <div class="col-md-6">
                                <div class="form-group">
                            <label for="commission_value" class="form-label">Commission Value</label>
                            <input type="number" 
                                   class="form-control <?php $__errorArgs = ['commission_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="commission_value" 
                                   name="commission_value" 
                                   value="<?php echo e(old('commission_value', $vehicle->commission_rate ?? $vehicle->commission_value)); ?>" 
                                   step="0.01" 
                                   min="0"
                                           placeholder="Enter commission value">
                                    <div class="helper-text">Commission amount (₹) or percentage (%) for vendor-provided vehicles</div>
                            <?php $__errorArgs = ['commission_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>
                    </div>
                    </div>

                    <!-- Section 4: Insurance and Legal Documents -->
                    <div class="form-section">
                        <h3 class="section-title">Insurance and Legal Documents</h3>

                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="insurance_provider" class="form-label required">Insurance Provider</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['insurance_provider'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="insurance_provider" 
                                   name="insurance_provider" 
                                   value="<?php echo e(old('insurance_provider', $vehicle->insurance_provider)); ?>" 
                                           placeholder=""
                                   required>
                            <?php $__errorArgs = ['insurance_provider'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="policy_number" class="form-label required">Policy Number</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['policy_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="policy_number" 
                                   name="policy_number" 
                                   value="<?php echo e(old('policy_number', $vehicle->policy_number)); ?>" 
                                           placeholder=""
                                   required>
                            <?php $__errorArgs = ['policy_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="insurance_expiry_date" class="form-label required">Insurance Expiry Date</label>
                            <input type="date" 
                                   class="form-control <?php $__errorArgs = ['insurance_expiry_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="insurance_expiry_date" 
                                   name="insurance_expiry_date" 
                                   value="<?php echo e(old('insurance_expiry_date', $vehicle->insurance_expiry_date ? Carbon\Carbon::parse($vehicle->insurance_expiry_date)->format('Y-m-d') : '')); ?>" 
                                   min="<?php echo e(date('Y-m-d')); ?>" 
                                   required>
                            <?php $__errorArgs = ['insurance_expiry_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>
                    </div>

                    <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="insurance_document" class="form-label required">Upload Insurance Certificate</label>
                                    
                                    <?php if($vehicle->insurance_document_path): ?>
                                    <div class="mb-2">
                                        <a href="<?php echo e(route('business.vehicles.view-document', [$vehicle, 'insurance'])); ?>" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye me-1"></i>View Current Insurance Document
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="file-upload-area" onclick="document.getElementById('insurance_document').click()">
                                        <div class="upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <p class="upload-text">Click to upload or drag and drop the file here. Supported format PDF, JPG</p>
                                    </div>
                            <input type="file" 
                                   class="form-control <?php $__errorArgs = ['insurance_document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="insurance_document" 
                                   name="insurance_document" 
                                           accept=".pdf,.jpg,.jpeg,.png"
                                       style="display: none;">
                                <input type="hidden" name="insurance_document_path" id="insurance_document_path" value="<?php echo e($vehicle->insurance_document_path ?? ''); ?>">
                                    <div id="insuranceFileName" class="mt-1 text-muted" style="font-size: 12px;"></div>
                                <div class="upload-progress" id="insuranceProgress">
                                    <div class="progress-bar-container">
                                        <div class="progress-bar-fill" id="insuranceProgressFill" style="width: 0%"></div>
                                    </div>
                                    <div class="progress-text"><span id="insuranceProgressText">0%</span></div>
                                </div>
                            <?php $__errorArgs = ['insurance_document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rc_document" class="form-label required">Upload Registration Certificate</label>
                                    
                                    <?php if($vehicle->rc_document_path): ?>
                                    <div class="mb-2">
                                        <a href="<?php echo e(route('business.vehicles.view-document', [$vehicle, 'rc'])); ?>" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye me-1"></i>View Current RC Document
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="file-upload-area" onclick="document.getElementById('rc_document').click()">
                                        <div class="upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <p class="upload-text">Click to upload or drag and drop the file here. Supported format PDF, JPG</p>
                                    </div>
                            <input type="file" 
                                   class="form-control <?php $__errorArgs = ['rc_document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="rc_document" 
                                   name="rc_document" 
                                           accept=".pdf,.jpg,.jpeg,.png"
                                       style="display: none;">
                                <input type="hidden" name="rc_document_path" id="rc_document_path" value="<?php echo e($vehicle->rc_document_path ?? ''); ?>">
                                    <div id="rcFileName" class="mt-1 text-muted" style="font-size: 12px;"></div>
                                <div class="upload-progress" id="rcProgress">
                                    <div class="progress-bar-container">
                                        <div class="progress-bar-fill" id="rcProgressFill" style="width: 0%"></div>
                                    </div>
                                    <div class="progress-text"><span id="rcProgressText">0%</span></div>
                                </div>
                            <?php $__errorArgs = ['rc_document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                        </div>
                    </div>

                    <!-- Section 5: Maintenance and Service Information -->
                    <div class="form-section">
                        <h3 class="section-title">Maintenance and Service Information</h3>

                    <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                            <label for="last_service_date" class="form-label">Last Service Date</label>
                            <input type="date" 
                                   class="form-control <?php $__errorArgs = ['last_service_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="last_service_date" 
                                   name="last_service_date" 
                                   value="<?php echo e(old('last_service_date', $vehicle->last_service_date)); ?>">
                            <?php $__errorArgs = ['last_service_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="last_service_meter_reading" class="form-label">Meter Reading (KMs)</label>
                            <input type="number" 
                                   class="form-control <?php $__errorArgs = ['last_service_meter_reading'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="last_service_meter_reading" 
                                   name="last_service_meter_reading" 
                                   value="<?php echo e(old('last_service_meter_reading', $vehicle->last_service_meter_reading)); ?>" 
                                           min="0"
                                           placeholder="0">
                            <?php $__errorArgs = ['last_service_meter_reading'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>

                            <div class="col-md-3">
                                <div class="form-group">
                            <label for="next_service_due" class="form-label">Next Service Due</label>
                            <input type="date" 
                                   class="form-control <?php $__errorArgs = ['next_service_due'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="next_service_due" 
                                   name="next_service_due" 
                                   value="<?php echo e(old('next_service_due', $vehicle->next_service_due)); ?>">
                            <?php $__errorArgs = ['next_service_due'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                        </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="next_service_meter_reading" class="form-label">Meter Reading (KMs)</label>
                            <input type="number" 
                                   class="form-control <?php $__errorArgs = ['next_service_meter_reading'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="next_service_meter_reading" 
                                   name="next_service_meter_reading" 
                                   value="<?php echo e(old('next_service_meter_reading', $vehicle->next_service_meter_reading)); ?>" 
                                           min="0"
                                           placeholder="0">
                            <?php $__errorArgs = ['next_service_meter_reading'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                            <label for="remarks_notes" class="form-label">Remarks/Notes</label>
                            <textarea class="form-control <?php $__errorArgs = ['remarks_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="remarks_notes" 
                                      name="remarks_notes" 
                                      rows="3" 
                                              placeholder="Input your notes"><?php echo e(old('remarks_notes')); ?></textarea>
                            <?php $__errorArgs = ['remarks_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 mb-4">
                        <a href="<?php echo e(route('business.vehicles.index')); ?>" class="cancel-button">
                            Cancel
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save me-2"></i>Save
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
let vehicleTypeSelect, vendorFields, vendorSearchInput, vendorDropdown, vendorIdInput, vendorNameInput;
let vendorSearchTimeout;

// Multiple image preview function
function previewMultipleImages(input) {
    const previewContainer = document.getElementById('imagePreviewContainer');
    
    // Clear previous previews
    previewContainer.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        previewContainer.style.display = 'flex';
        
        Array.from(input.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = `Preview ${index + 1}`;
                    img.className = 'image-preview';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
        } else {
        previewContainer.style.display = 'none';
    }
}

// Vendor search functions
function searchVendors(query) {
    if (!vendorDropdown) {
        console.error('Vendor dropdown element not found');
        return;
    }
    
    const url = `<?php echo e(url("/business/vendors/search")); ?>?q=${encodeURIComponent(query)}`;
    
    fetch(url, {
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
}

function openAddVendorModal() {
    const modal = new bootstrap.Modal(document.getElementById('addVendorModal'));
    modal.show();
}

function saveNewVendor() {
    const form = document.getElementById('addVendorForm');
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Add timestamp to make mobile number unique
    const timestamp = Date.now();
    formData.set('mobile_number', '888888888' + (timestamp % 1000));
    formData.set('email_address', 'test' + timestamp + '@vendor.com');
    formData.set('pan_number', 'ABCDE' + (timestamp % 10000));
    
    fetch('<?php echo e(url("/business/vendors")); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
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
    document.querySelector('.vehicle-add-container').insertBefore(alertDiv, document.querySelector('.container-fluid'));
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables
    vehicleTypeSelect = document.getElementById('vehicle_type');
    vendorFields = document.getElementById('vendor_fields');
    vendorSearchInput = document.getElementById('vendor_search');
    vendorDropdown = document.getElementById('vendor_dropdown');
    vendorIdInput = document.getElementById('vendor_id');
    vendorNameInput = document.getElementById('vendor_name');

    function toggleVendorFields() {
        const ownershipType = document.getElementById('ownership_type')?.value;
        if (!ownershipType) return;
        
        const vendorFieldsElements = document.querySelectorAll('.vendor-fields');
        if (!vendorFieldsElements.length) return;
        
        if (ownershipType === 'vendor_provided') {
            vendorFieldsElements.forEach(field => field.style.display = 'block');
        } else {
            vendorFieldsElements.forEach(field => field.style.display = 'none');
        }
    }

    // Event listeners
    const ownershipTypeSelect = document.getElementById('ownership_type');
    if (ownershipTypeSelect) {
        ownershipTypeSelect.addEventListener('change', toggleVendorFields);
    }

    // Initialize on page load
    toggleVendorFields();

    // Vendor search event listener
    if (vendorSearchInput) {
        vendorSearchInput.addEventListener('input', function() {
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
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating Vehicle...';
        submitBtn.disabled = true;
        
        // Submit form via AJAX
        const formData = new FormData(this);
        
        // Ensure seating capacity is included if vehicle type is car
        const vehicleType = formData.get('vehicle_type');
        if (vehicleType === 'car') {
            const seatingCapacitySelect = document.getElementById('seating_capacity');
            if (seatingCapacitySelect) {
                const selectedValue = seatingCapacitySelect.value;
                if (selectedValue) {
                    formData.set('seating_capacity', selectedValue);
                }
            }
        }
        
        // Remove the old file input data
        formData.delete('vehicle_images[]');
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async (response) => {
            if (response.ok) {
                const data = await response.json();
                if (!data.success) throw { status: 400, data };
                showAlert('Vehicle registered successfully! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = data.redirect_url || '<?php echo e(route("business.vehicles.index")); ?>';
                }, 2000);
                return;
            }
            let data;
            try { data = await response.json(); } catch (_) { data = {}; }
            throw { status: response.status, data };
        })
        .catch(err => {
            const errorDisplay = document.getElementById('errorDisplay');
            const errorListDiv = document.getElementById('errorList');
            if (errorDisplay && errorListDiv) {
                let messages = [];
                const labelMap = {
                    vehicle_number: 'Registration Number',
                    vehicle_type: 'Vehicle Type',
                    vehicle_make: 'Vehicle Make',
                    vehicle_model: 'Vehicle Model',
                    insurance_provider: 'Insurance Provider',
                    policy_number: 'Policy Number',
                    insurance_expiry_date: 'Insurance Expiry Date',
                    rc_document: 'Registration Certificate',
                    insurance_document: 'Insurance Certificate'
                };
                if (err && err.data && err.data.errors) {
                    Object.entries(err.data.errors).forEach(([field, arr]) => {
                        const label = labelMap[field] || field.replace(/_/g, ' ');
                        arr.forEach(msg => messages.push(`${label}: ${msg}`));
                    });
                } else if (err && err.data && err.data.message) {
                    messages.push(err.data.message);
                } else {
                    messages.push('Registration failed. Please try again.');
                }
                errorListDiv.innerHTML = '<ul class="mb-0">' + messages.map(m => `<li>${m}</li>`).join('') + '</ul>';
                errorDisplay.style.display = '';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                showAlert('Registration failed. Please try again.', 'danger');
            }
            resetSubmitButton(submitBtn, originalText);
        });
    });
    
    // Reset submit button
    function resetSubmitButton(btn, originalText) {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
});

// Toggle vendor fields based on ownership type
function toggleVendorFields() {
    const ownershipType = document.getElementById('ownership_type')?.value;
    if (!ownershipType) return;
    
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
            
            // Validate required fields
            const commissionTypeSelect = document.getElementById('quick_commission_type');
            const commissionTypeValue = commissionTypeSelect.value;
            
            if (!commissionTypeValue || commissionTypeValue === '') {
                showNotification('Please select a commission type', 'error');
            return;
        }

            const submitBtn = vendorForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
            submitBtn.disabled = true;
            
            // Prepare form data
            const formData = new FormData(vendorForm);
            
            // Map form values to backend expectations
            const commissionRateValue = formData.get('commission_value');
            
            // Remove old values
            formData.delete('commission_type');
            formData.delete('commission_value');
            
            // Add correct field names and values
            formData.append('commission_type', commissionTypeValue === 'fixed' ? 'fixed_amount' : 'percentage_of_revenue');
            formData.append('commission_rate', commissionRateValue);
            
            // Add CSRF token
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            // Submit the form
            // Use quick-store endpoint expecting minimal fields
            fetch('<?php echo e(route("business.vendors.quick-store")); ?>', {
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
                    
                    // Auto-fill vendor details on main form
                    if (vendorInput) {
                        vendorInput.value = data.vendor.vendor_name;
                    }
                    
                    // Use setTimeout to ensure DOM is ready
                    setTimeout(() => {
                        const commissionTypeSelect = document.getElementById('commission_type');
                        const commissionValueInput = document.getElementById('commission_value');
                        
                        if (commissionTypeSelect) {
                            const mappedType = (data.vendor.commission_type === 'fixed_amount' ? 'fixed' : (data.vendor.commission_type === 'percentage_of_revenue' ? 'percentage' : (data.vendor.commission_type || '')));
                            
                            // Set the native select value first
                            commissionTypeSelect.value = mappedType;
                            
                            // Trigger a change event to notify the custom dropdown
                            const changeEvent = new Event('change', { bubbles: true });
                            commissionTypeSelect.dispatchEvent(changeEvent);
                            
                            // Also trigger input event for better compatibility
                            const inputEvent = new Event('input', { bubbles: true });
                            commissionTypeSelect.dispatchEvent(inputEvent);
                            
                            // Find the custom dropdown wrapper and update it
                            let customDropdownWrapper = null;
                            let sibling = commissionTypeSelect.nextSibling;
                            while (sibling) {
                                if (sibling.classList && sibling.classList.contains('form-select-wrapper')) {
                                    customDropdownWrapper = sibling;
                                    break;
                                }
                                sibling = sibling.nextSibling;
                            }
                            
                            if (customDropdownWrapper) {
                                const selectedOption = commissionTypeSelect.options[commissionTypeSelect.selectedIndex];
                                const displayElement = customDropdownWrapper.querySelector('.selected-text');
                                const toggle = customDropdownWrapper.querySelector('.dropdown-toggle');
                                
                                if (displayElement && selectedOption) {
                                    displayElement.textContent = selectedOption.textContent;
                                    if (toggle) {
                                        toggle.classList.add('has-selection');
                                    }
                                }
                                
                                // Update selected state in menu
                                customDropdownWrapper.querySelectorAll('.dropdown-option').forEach(option => {
                                    option.classList.remove('selected');
                                    if (option.dataset.value === mappedType) {
                                        option.classList.add('selected');
                                    }
                                });
                            }
                        }
                        
                        if (commissionValueInput) {
                            commissionValueInput.value = data.vendor.commission_value || data.vendor.commission_rate || '';
                        }
                    }, 150); // Small delay to ensure custom dropdown is initialized
                    
                    // Show success message
                    showNotification('Vendor added successfully!', 'success');
                } else {
                    // Show error message
                    const msg = data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Failed to add vendor');
                    showNotification(msg, 'error');
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
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 1; min-width: 300px;';
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

// Vendor Dropdown Functionality
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
                        selectVendor(filteredVendors[selectedIndex]);
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
                option.textContent = vendor;
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
        
        function selectVendor(vendor) {
            vendorInput.value = vendor;
            closeDropdown();
            
            // Fetch vendor commission details
            fetchVendorCommissionDetails(vendor);
        }
        
        // Load vendors from database
        function loadVendors() {
            renderVendorOptions(true); // Show loading state
            
            fetch('<?php echo e(route("business.vendors.search")); ?>', {
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
                    vendorData = data.vendors.map(vendor => vendor.vendor_name);
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
            
            fetch('<?php echo e(route("business.vendors.search")); ?>?search=' + encodeURIComponent(searchTerm), {
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
                    filteredVendors = data.vendors.map(vendor => vendor.vendor_name);
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
            return;
        }

            // Show loading state
            commissionTypeSelect.disabled = true;
            commissionValueInput.disabled = true;
            commissionValueInput.placeholder = 'Loading commission details...';
            
            fetch('<?php echo e(route("business.vendors.search")); ?>?search=' + encodeURIComponent(vendorName), {
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
                            
                            // Refresh custom dropdowns to show the selected value
                            refreshCustomDropdowns();
                        
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
            }, 100); // Small delay to ensure fields are visible
        }
    }
});
</script>
<?php $__env->stopSection(); ?>

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
                                <label for="quick_mobile_number" class="form-label required">Mobile Number</label>
                                <input type="text" class="form-control" id="quick_mobile_number" name="mobile_number" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="quick_office_address" class="form-label required">Address</label>
                                <textarea class="form-control" id="quick_office_address" name="office_address" rows="2" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="quick_commission_type" class="form-label required">Commission Type</label>
                                <select class="form-select" id="quick_commission_type" name="commission_type" required data-skip-custom-dropdown="true">
                                    <option value="">Select Type</option>
                                    <option value="fixed">Fixed Amount</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="quick_commission_rate" class="form-label required">Commission Value</label>
                                <input type="number" class="form-control" id="quick_commission_rate" name="commission_value" step="0.01" min="0" required>
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

<?php $__env->startPush('scripts'); ?>
<script>
// Uploaded documents tracker
let uploadedDocuments = {
    rc: null,
    insurance: null
};

// Vehicle images upload function
function uploadVehicleImages(files) {
    const container = document.getElementById('vehicleImagesContainer');
    if (!container) return;
    
    // Track uploaded image IDs globally
    if (!window.uploadedVehicleImageIds) {
        window.uploadedVehicleImageIds = [];
    }
    
    // Upload each file
    Array.from(files).forEach((file, index) => {
        // Create preview element immediately
        const previewDiv = document.createElement('div');
        previewDiv.className = 'position-relative';
        previewDiv.style.cssText = 'width: 100px; height: 100px; border: 1px solid #ced4da; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f8f9fa;';
            
            // Show loading state
        previewDiv.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>';
            
        container.appendChild(previewDiv);
        container.style.display = 'flex';
            
        const formData = new FormData();
        formData.append('image', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
        fetch('<?php echo e(route("business.vehicles.upload-image")); ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                window.uploadedVehicleImageIds.push({
                    id: data.image_id,
                    path: data.image_path,
                    preview_url: data.preview_url
                });
                
                // Update preview with actual image
                previewDiv.innerHTML = `
                    <img src="${data.preview_url}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 2px; right: 2px; padding: 2px 6px; font-size: 10px;" onclick="removeVehicleImage(${data.image_id}, this)">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                // Update hidden input
                document.getElementById('uploaded_image_ids').value = window.uploadedVehicleImageIds.map(img => img.id).join(',');
    } else {
                alert('Failed to upload file: ' + data.message);
                previewDiv.remove();
                }
            })
            .catch(error => {
            console.error('Error uploading file:', error);
            alert('Failed to upload file: ' + file.name);
            previewDiv.remove();
            });
        });
    }

// Remove vehicle image
function removeVehicleImage(imageId, buttonElement) {
    // Remove from tracking array
    window.uploadedVehicleImageIds = window.uploadedVehicleImageIds.filter(img => img.id !== imageId);
    
    // Update hidden input
    document.getElementById('uploaded_image_ids').value = window.uploadedVehicleImageIds.map(img => img.id).join(',');
    
    // Remove preview
    buttonElement.closest('.position-relative').remove();
    
    // Check if container is empty
    const container = document.getElementById('vehicleImagesContainer');
    if (container && container.children.length === 0) {
        container.style.display = 'none';
    }
}

function removeExistingImage(imageId, buttonElement) {
    // Get or initialize deleted images array
    if (!window.deletedImageIds) {
        window.deletedImageIds = [];
    }
    
    // Add to deleted array
    window.deletedImageIds.push(imageId);
    
    // Update hidden input
    document.getElementById('deleted_image_ids').value = window.deletedImageIds.join(',');
    
    // Remove the image element
    buttonElement.closest('.existing-image-item').remove();
    
    console.log('Image marked for deletion:', imageId);
}

// Document upload functions
function uploadDocument(file, type) {
    const formData = new FormData();
    formData.append('document', file);
    formData.append('type', type);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    const progressContainer = document.getElementById(type + 'Progress');
    const progressFill = document.getElementById(type + 'ProgressFill');
    const progressText = document.getElementById(type + 'ProgressText');
    
    progressContainer.classList.add('active');
    
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            progressFill.style.width = percentComplete + '%';
            progressText.textContent = Math.round(percentComplete) + '%';
        }
    });
    
    xhr.addEventListener('load', function() {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    uploadedDocuments[type] = response.document_path;
                    
                    // Set the hidden input field value
                    const pathInput = document.getElementById(type + '_document_path');
                    if (pathInput) {
                        pathInput.value = response.document_path;
                    }
                    
                    // Show filename feedback (non-intrusive)
                    const nameEl = document.getElementById(type + 'FileName');
                    if (nameEl) {
                        nameEl.textContent = 'Selected file: ' + (file?.name || response.document_path.split('/').pop());
                    }
                    progressText.textContent = 'Upload complete!';
    setTimeout(() => {
                        progressContainer.classList.remove('active');
                    }, 2000);
                    // Reset the file input to allow re-uploading the same file if needed
                    const inputEl = document.getElementById(type + '_document');
                    if (inputEl) inputEl.value = '';
                } else {
                    alert('Upload failed: ' + response.message);
                    progressContainer.classList.remove('active');
                }
            } catch (e) {
                alert('Failed to parse response');
                progressContainer.classList.remove('active');
            }
        } else {
            alert('Upload failed with status: ' + xhr.status);
            progressContainer.classList.remove('active');
        }
    });
    
    xhr.addEventListener('error', function() {
        alert('An error occurred during upload');
        progressContainer.classList.remove('active');
    });
    
    xhr.open('POST', '<?php echo e(route("business.vehicles.upload-document")); ?>');
    xhr.send(formData);
}

// Handle vehicle images file input
document.getElementById('vehicle_images').addEventListener('change', function() {
    if (this.files && this.files.length > 0) {
        uploadVehicleImages(this.files);
    }
    // Allow selecting the same file again immediately
    this.value = '';
});

// Handle document file inputs
document.getElementById('rc_document').addEventListener('change', function() {
    if (this.files && this.files[0]) {
        uploadDocument(this.files[0], 'rc');
    }
});

document.getElementById('insurance_document').addEventListener('change', function() {
    if (this.files && this.files[0]) {
        uploadDocument(this.files[0], 'insurance');
    }
});

$(document).ready(function() {

    let vehicleMakes = [];
    let vehicleModels = [];
    let currentVehicleType = '';

    // Initialize regular select elements
    // Note: Select2 removed to avoid conflicts

    // Load vehicle makes when vehicle type changes
    // Listen for changes on vehicle_type select
    $('#vehicle_type').on('change', function() {
        currentVehicleType = $(this).val();
        loadVehicleMakes();
        toggleVehicleTypeFields();
        populateTransmissionOptions();
    });

    // Listen for changes on vehicle_make select
    $('#vehicle_make').on('change', function() {
        const makeName = $(this).val();
        loadVehicleModels(makeName);
    });

    // Function to toggle vehicle type specific fields
    function toggleVehicleTypeFields() {
        const vehicleType = $('#vehicle_type').val();
        
        // Hide all vehicle type specific fields
        $('#car-fields, #bike-scooter-fields, #heavy-vehicle-fields').hide();
        
        // Show relevant fields based on vehicle type
        const carSeating = document.getElementById('seating_capacity');
        const heavySeating = document.getElementById('seating_capacity_heavy');

        if (vehicleType === 'car') {
            $('#car-fields').show();
            $('#default-transmission-field').show();
            if (carSeating) carSeating.required = true;
            if (heavySeating) heavySeating.required = false;
        } else if (vehicleType === 'bike_scooter') {
            $('#bike-scooter-fields').show();
            $('#default-transmission-field').show();
            if (carSeating) carSeating.required = false;
            if (heavySeating) heavySeating.required = false;
        } else if (vehicleType === 'heavy_vehicle') {
            $('#heavy-vehicle-fields').show();
            $('#default-transmission-field').show();
            if (carSeating) carSeating.required = false;
            if (heavySeating) heavySeating.required = false; // server rule is nullable
        } else {
            // Hide transmission field if no vehicle type selected
            $('#default-transmission-field').hide();
            if (carSeating) carSeating.required = false;
            if (heavySeating) heavySeating.required = false;
        }
    }

    // Initialize field visibility on page load
    toggleVehicleTypeFields();
    populateTransmissionOptions();
    
    // Set commission type on page load
    const commissionValue = '<?php echo e($commissionValue ?? ""); ?>';
    if (commissionValue) {
        $('#commission_type').val(commissionValue);
        // Update custom dropdown display
        setTimeout(() => {
            const commissionSelect = document.getElementById('commission_type');
            const customDropdownWrapper = commissionSelect.nextElementSibling;
            if (customDropdownWrapper && customDropdownWrapper.classList.contains('form-select-wrapper')) {
                const selectedOption = commissionSelect.options[commissionSelect.selectedIndex];
                const displayElement = customDropdownWrapper.querySelector('.selected-text');
                if (displayElement && selectedOption) {
                    displayElement.textContent = selectedOption.textContent;
                    customDropdownWrapper.querySelector('.dropdown-toggle').classList.add('has-selection');
                }
            }
        }, 100);
    }

    // Populate transmission type options based on vehicle type
    function populateTransmissionOptions() {
        const vehicleType = $('#vehicle_type').val();
        const mainTransmission = $('#transmission_type');
        
        // Store current value before clearing
        const currentValue = mainTransmission.val() || '<?php echo e($vehicle->transmission_type ?? ""); ?>';
        
        // Clear existing options
        mainTransmission.empty().append('<option value="">Select Transmission Type</option>');
        
        if (vehicleType === 'car' || vehicleType === 'heavy_vehicle') {
            // Cars and Heavy Vehicles: Manual, Automatic, Hybrid (Mandatory)
            mainTransmission.append('<option value="manual">Manual</option>');
            mainTransmission.append('<option value="automatic">Automatic</option>');
            mainTransmission.append('<option value="hybrid">Hybrid</option>');
            mainTransmission.prop('required', true);
            mainTransmission.closest('.form-group').find('label').addClass('required');
        } else if (vehicleType === 'bike_scooter') {
            // Bikes/Scooters: Gear, Gearless (Optional)
            mainTransmission.append('<option value="gear">Gear</option>');
            mainTransmission.append('<option value="gearless">Gearless</option>');
            mainTransmission.prop('required', false);
            mainTransmission.closest('.form-group').find('label').removeClass('required');
        }
        
        // Set the value back after populating options
        if (currentValue) {
            mainTransmission.val(currentValue);
        }
        
        // Refresh custom dropdowns
        refreshCustomDropdowns();
    }

    // Load vehicle models when make changes

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
            url: '<?php echo e(route('business.api.vehicle-makes')); ?>',
            method: 'GET',
            data: { type: currentVehicleType },
            success: function(response) {
                vehicleMakes = response;
                $('#vehicle_make').empty().append('<option value="">Select Vehicle Make</option>');
                
                response.forEach(function(make) {
                    $('#vehicle_make').append(`<option value="${make.name}">${make.name}</option>`);
                });
                
                // Set the current vehicle's make if available
                const currentMake = '<?php echo e($vehicle->vehicle_make ?? ""); ?>';
                if (currentMake) {
                    $('#vehicle_make').val(currentMake).trigger('change');
                }
                
                // Refresh custom dropdowns after updating options
                refreshCustomDropdowns();
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
            url: '<?php echo e(route('business.api.vehicle-models')); ?>',
            method: 'GET',
            data: { make_name: makeName },
            success: function(response) {
                vehicleModels = response;
                $('#vehicle_model').empty().append('<option value="">Select Vehicle Model</option>');
                
                response.forEach(function(model) {
                    $('#vehicle_model').append(`<option value="${model.name}">${model.name}</option>`);
                });
                
                // Set the current vehicle's model if available
                const currentModel = '<?php echo e($vehicle->vehicle_model ?? ""); ?>';
                if (currentModel) {
                    $('#vehicle_model').val(currentModel);
                }
                
                // Refresh custom dropdowns after updating options
                refreshCustomDropdowns();
            },
            error: function(xhr, status, error) {
                console.error('Error loading vehicle models:', error);
                showAlert('Error loading vehicle models. Please try again.', 'danger');
            }
        });
    }

    // Initialize on page load if vehicle type is already selected
    if ($('#vehicle_type').val()) {
        currentVehicleType = $('#vehicle_type').val();
        loadVehicleMakes();
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('business.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/business/vehicles/edit.blade.php ENDPATH**/ ?>