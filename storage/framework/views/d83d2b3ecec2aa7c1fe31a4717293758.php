<?php $__env->startSection('title', 'Edit Vehicle - ' . $vehicle->vehicle_make . ' ' . $vehicle->vehicle_model); ?>
<?php $__env->startSection('page-title', 'Edit Vehicle'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Vehicle - <?php echo e($vehicle->vehicle_make); ?> <?php echo e($vehicle->vehicle_model); ?>

                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('business.vehicles.update', $vehicle)); ?>" enctype="multipart/form-data" id="vehicleForm">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
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

                        <div class="col-md-4 mb-3">
                            <label for="vehicle_model" class="form-label">Vehicle Model *</label>
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

                        <div class="col-md-4 mb-3">
                            <label for="vehicle_year" class="form-label">Vehicle Year *</label>
                            <select class="form-select <?php $__errorArgs = ['vehicle_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="vehicle_year" 
                                    name="vehicle_year" 
                                    required>
                                <option value="">Select Year</option>
                                <?php for($year = date('Y') + 1; $year >= 1990; $year--): ?>
                                    <option value="<?php echo e($year); ?>" <?php echo e(old('vehicle_year', $vehicle->vehicle_year) == $year ? 'selected' : ''); ?>><?php echo e($year); ?></option>
                                <?php endfor; ?>
                            </select>
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

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="vehicle_number" class="form-label">Vehicle Number *</label>
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
                                   placeholder="e.g., MH12AB1234"
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

                        <div class="col-md-4 mb-3">
                            <label for="vehicle_status" class="form-label">Vehicle Status *</label>
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

                        <div class="col-md-4 mb-3">
                            <label for="fuel_type" class="form-label">Fuel Type *</label>
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

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="mileage" class="form-label">Mileage (km/l)</label>
                            <input type="number" 
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
                                   step="0.01" 
                                   min="0">
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

                        <!-- Transmission Type - Dynamic based on vehicle type -->
                        <div class="col-md-4 mb-3" id="transmission_car_heavy" style="display: none;">
                            <label for="transmission_type" class="form-label">Transmission Type *</label>
                            <select class="form-select <?php $__errorArgs = ['transmission_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="transmission_type" 
                                    name="transmission_type">
                                <option value="">Select Transmission</option>
                                <option value="manual" <?php echo e(old('transmission_type', $vehicle->transmission_type) == 'manual' ? 'selected' : ''); ?>>Manual</option>
                                <option value="automatic" <?php echo e(old('transmission_type', $vehicle->transmission_type) == 'automatic' ? 'selected' : ''); ?>>Automatic</option>
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

                        <div class="col-md-4 mb-3" id="transmission_bike" style="display: none;">
                            <label for="bike_transmission_type" class="form-label">Transmission Type *</label>
                            <select class="form-select <?php $__errorArgs = ['bike_transmission_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="bike_transmission_type" 
                                    name="bike_transmission_type">
                                <option value="">Select Transmission</option>
                                <option value="gear" <?php echo e(old('bike_transmission_type', $vehicle->transmission_type) == 'gear' ? 'selected' : ''); ?>>Gear</option>
                                <option value="gearless" <?php echo e(old('bike_transmission_type', $vehicle->transmission_type) == 'gearless' ? 'selected' : ''); ?>>Gearless</option>
                            </select>
                            <?php $__errorArgs = ['bike_transmission_type'];
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

                    <!-- Vehicle Images -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-images me-2"></i>Vehicle Images
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_images" class="form-label">Add New Images</label>
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
                                   onchange="previewMultipleImages(this)">
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
                            <div class="form-text">Upload multiple images of the vehicle (JPG, PNG, max 5MB each)</div>
                            <div id="imagePreviewContainer" class="mt-2" style="display: none;">
                                <div class="row" id="imagePreviews"></div>
                            </div>
                            
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Images</label>
                            <div id="currentImages" class="border rounded p-2" style="min-height: 150px; background-color: #f8f9fa;">
                                <?php if($vehicle->images && $vehicle->images->count() > 0): ?>
                                    <div class="row" id="currentImagesList">
                                        <?php $__currentLoopData = $vehicle->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-md-6 mb-2" data-image-id="<?php echo e($image->id); ?>">
                                                <div class="position-relative">
                                                    <img src="<?php echo e(asset('storage/' . $image->image_path)); ?>" 
                                                         alt="Vehicle Image" 
                                                         class="img-thumbnail" 
                                                         style="width: 100%; height: 100px; object-fit: cover;">
                                                    <?php if($image->is_primary): ?>
                                                        <span class="position-absolute top-0 start-0 badge bg-success">Primary</span>
                                                    <?php endif; ?>
                                                    <div class="position-absolute top-0 end-0">
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger" 
                                                                onclick="deleteImage(<?php echo e($vehicle->id); ?>, <?php echo e($image->id); ?>)"
                                                                title="Delete Image">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div class="position-absolute bottom-0 start-0 w-100">
                                                        <button type="button" 
                                                                class="btn btn-sm btn-primary w-100" 
                                                                onclick="setPrimaryImage(<?php echo e($vehicle->id); ?>, <?php echo e($image->id); ?>)"
                                                                <?php echo e($image->is_primary ? 'disabled' : ''); ?>

                                                                title="Set as Primary">
                                                            <?php echo e($image->is_primary ? 'Primary' : 'Set Primary'); ?>

                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted text-center">
                                        <i class="fas fa-images fa-2x mb-2"></i><br>
                                        No images uploaded
                                    </div>
                                <?php endif; ?>
                            </div>
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
                                <select class="form-select <?php $__errorArgs = ['seating_capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        id="seating_capacity" 
                                        name="seating_capacity">
                                    <option value="">Select Seating Capacity</option>
                                    <?php for($i = 2; $i <= 8; $i++): ?>
                                        <option value="<?php echo e($i); ?>" <?php echo e(old('seating_capacity', $vehicle->seating_capacity) == $i ? 'selected' : ''); ?>><?php echo e($i); ?> Seater</option>
                                    <?php endfor; ?>
                                </select>
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

                    <!-- For Bikes and Scooters -->
                    <div id="bike_specs" style="display: none;">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="engine_capacity_cc" class="form-label">Engine Capacity (CC) *</label>
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
                                       min="50" 
                                       max="2000">
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

                    <!-- For Heavy Vehicles -->
                    <div id="heavy_vehicle_specs" style="display: none;">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="seating_capacity_heavy" class="form-label">Seating Capacity (for Buses)</label>
                                <select class="form-select <?php $__errorArgs = ['seating_capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        id="seating_capacity_heavy" 
                                        name="seating_capacity">
                                    <option value="">Select Seating Capacity</option>
                                    <?php for($i = 10; $i <= 100; $i += 5): ?>
                                        <option value="<?php echo e($i); ?>" <?php echo e(old('seating_capacity', $vehicle->seating_capacity) == $i ? 'selected' : ''); ?>><?php echo e($i); ?> Seater</option>
                                    <?php endfor; ?>
                                </select>
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
                            <div class="col-md-4 mb-3">
                                <label for="payload_capacity_tons" class="form-label">Payload Capacity (Tons) - for Trucks</label>
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
                                       step="0.1" 
                                       min="0" 
                                       max="100">
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
                                   min="0">
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

                        <div class="col-md-3 mb-3">
                            <label for="km_limit_per_booking" class="form-label">KM Limit per Booking</label>
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
                                   min="0">
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

                        <div class="col-md-3 mb-3">
                            <label for="extra_rental_price_per_hour" class="form-label">Extra Price per Hour (₹)</label>
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
                                   min="0">
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

                        <div class="col-md-3 mb-3">
                            <label for="extra_price_per_km" class="form-label">Extra Price per KM (₹)</label>
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
                                   min="0">
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
                                    required>
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

                        <div class="col-md-4 mb-3" id="vendor_fields" style="display: none;">
                            <label for="vendor_search" class="form-label">Vendor Name</label>
                            <div class="position-relative">
                                <input type="text" 
                                       class="form-control <?php $__errorArgs = ['vendor_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="vendor_search" 
                                       placeholder="Type to search vendors..."
                                       autocomplete="off"
                                       value="<?php echo e(old('vendor_name', $vehicle->vendor_name)); ?>">
                                <input type="hidden" id="vendor_id" name="vendor_id" value="<?php echo e(old('vendor_id', $vehicle->vendor_id)); ?>">
                                <input type="hidden" id="vendor_name" name="vendor_name" value="<?php echo e(old('vendor_name', $vehicle->vendor_name)); ?>">
                                <div id="vendor_dropdown" class="dropdown-menu w-100" style="display: none; max-height: 200px; overflow-y: auto;">
                                    <!-- Vendor options will be populated here -->
                                </div>
                            </div>
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

                        <div class="col-md-2 mb-3" id="commission_type_field" style="display: none;">
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
                                <option value="">Select Type</option>
                                <option value="fixed" <?php echo e(old('commission_type', $vehicle->commission_type) == 'fixed' ? 'selected' : ''); ?>>Fixed</option>
                                <option value="percentage" <?php echo e(old('commission_type', $vehicle->commission_type) == 'percentage' ? 'selected' : ''); ?>>Percentage</option>
                            </select>
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

                        <div class="col-md-2 mb-3" id="commission_value_field" style="display: none;">
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
                                   value="<?php echo e(old('commission_value', $vehicle->commission_value)); ?>" 
                                   step="0.01" 
                                   min="0">
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

                        <div class="col-md-4 mb-3">
                            <label for="policy_number" class="form-label">Policy Number *</label>
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

                        <div class="col-md-4 mb-3">
                            <label for="insurance_expiry_date" class="form-label">Insurance Expiry Date *</label>
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
                                   value="<?php echo e(old('insurance_expiry_date', $vehicle->insurance_expiry_date?->format('Y-m-d'))); ?>" 
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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="insurance_document" class="form-label">Upload Insurance Document</label>
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
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF, JPG, PNG files only. Max size: 10MB</div>
                            <?php if($vehicle->insurance_document_path): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Current: </small>
                                    <a href="<?php echo e(route('business.vehicles.download-document', [$vehicle, 'insurance'])); ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>Download Current
                                    </a>
                                </div>
                            <?php endif; ?>
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

                        <div class="col-md-6 mb-3">
                            <label for="rc_document" class="form-label">Upload RC Document</label>
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
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF, JPG, PNG files only. Max size: 10MB</div>
                            <?php if($vehicle->rc_document_path): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Current: </small>
                                    <a href="<?php echo e(route('business.vehicles.download-document', [$vehicle, 'rc'])); ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>Download Current
                                    </a>
                                </div>
                            <?php endif; ?>
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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rc_number" class="form-label">RC Number *</label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['rc_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="rc_number" 
                                   name="rc_number" 
                                   value="<?php echo e(old('rc_number', $vehicle->rc_number)); ?>" 
                                   required>
                            <?php $__errorArgs = ['rc_number'];
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
                                   value="<?php echo e(old('last_service_date', $vehicle->last_service_date?->format('Y-m-d'))); ?>">
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

                        <div class="col-md-3 mb-3">
                            <label for="last_service_meter_reading" class="form-label">Last Service Meter Reading</label>
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
                                   min="0">
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

                        <div class="col-md-3 mb-3">
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
                                   value="<?php echo e(old('next_service_due', $vehicle->next_service_due?->format('Y-m-d'))); ?>">
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

                        <div class="col-md-3 mb-3">
                            <label for="next_service_meter_reading" class="form-label">Next Service Meter Reading</label>
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
                                   min="0">
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
                                      placeholder="Any special remarks, notes, or instructions..."><?php echo e(old('remarks_notes', $vehicle->remarks_notes)); ?></textarea>
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

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?php echo e(route('business.vehicles.show', $vehicle)); ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Vehicle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Vendor Modal -->
<div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="addVendorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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
                            <option value="individual" selected>Individual</option>
                            <option value="company">Company</option>
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
                    <div class="mb-3">
                        <label for="new_vendor_payout" class="form-label">Payout Method *</label>
                        <select class="form-select" id="new_vendor_payout" name="payout_method" required>
                            <option value="">Select Method</option>
                            <option value="bank_transfer" selected>Bank Transfer</option>
                            <option value="upi">UPI</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="new_vendor_frequency" class="form-label">Payout Frequency *</label>
                        <select class="form-select" id="new_vendor_frequency" name="payout_frequency" required onchange="toggleFrequencyFields()">
                            <option value="">Select Frequency</option>
                            <option value="weekly" selected>Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    <div id="frequency_fields">
                        <div class="mb-3" id="day_of_week_field">
                            <label for="new_vendor_day_of_week" class="form-label">Day of Week *</label>
                            <select class="form-select" id="new_vendor_day_of_week" name="payout_day_of_week">
                                <option value="">Select Day</option>
                                <option value="monday" selected>Monday</option>
                                <option value="tuesday">Tuesday</option>
                                <option value="wednesday">Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                                <option value="saturday">Saturday</option>
                                <option value="sunday">Sunday</option>
                            </select>
                        </div>
                        <div class="mb-3" id="day_of_month_field" style="display: none;">
                            <label for="new_vendor_day_of_month" class="form-label">Day of Month *</label>
                            <select class="form-select" id="new_vendor_day_of_month" name="payout_day_of_month">
                                <option value="">Select Day</option>
                                <?php for($i = 1; $i <= 31; $i++): ?>
                                    <option value="<?php echo e($i); ?>" <?php echo e($i == 1 ? 'selected' : ''); ?>><?php echo e($i); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="new_vendor_commission_type" class="form-label">Commission Type *</label>
                        <select class="form-select" id="new_vendor_commission_type" name="commission_type" required>
                            <option value="">Select Type</option>
                            <option value="fixed_amount" selected>Fixed Amount</option>
                            <option value="percentage_of_revenue">Percentage of Revenue</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="new_vendor_commission_rate" class="form-label">Commission Rate *</label>
                        <input type="number" class="form-control" id="new_vendor_commission_rate" name="commission_rate" value="100" step="0.01" min="0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveNewVendor()">Add Vendor</button>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview function
// Multiple image preview function
function previewMultipleImages(input) {
    const previewContainer = document.getElementById('imagePreviewContainer');
    const imagePreviews = document.getElementById('imagePreviews');
    
    // Clear previous previews
    imagePreviews.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        previewContainer.style.display = 'block';
        
        Array.from(input.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const colDiv = document.createElement('div');
                    colDiv.className = 'col-md-3 mb-2';
                    colDiv.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" 
                                 alt="Preview ${index + 1}" 
                                 class="img-thumbnail" 
                                 style="width: 100%; height: 120px; object-fit: cover;">
                            <div class="position-absolute top-0 end-0">
                                <span class="badge bg-primary">New ${index + 1}</span>
                            </div>
                        </div>
                    `;
                    imagePreviews.appendChild(colDiv);
                };
                reader.readAsDataURL(file);
            }
        });
    } else {
        previewContainer.style.display = 'none';
    }
}

// Delete image function
function deleteImage(vehicleId, imageId) {
    console.log('deleteImage called with vehicleId:', vehicleId, 'imageId:', imageId);
    
    if (confirm('Are you sure you want to delete this image?')) {
        console.log('User confirmed deletion');
        
        fetch(`<?php echo e(url('/business/vehicles')); ?>/${vehicleId}/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => {
            console.log('Delete response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Delete response data:', data);
            if (data.success) {
                // Remove the image element from DOM
                const imageElement = document.querySelector(`[data-image-id="${imageId}"]`);
                if (imageElement) {
                    imageElement.remove();
                }
                
                // Show success message
                showAlert('Image deleted successfully!', 'success');
                
                // Check if no images left
                const currentImagesList = document.getElementById('currentImagesList');
                if (currentImagesList && currentImagesList.children.length === 0) {
                    currentImagesList.innerHTML = `
                        <div class="text-muted text-center">
                            <i class="fas fa-images fa-2x mb-2"></i><br>
                            No images uploaded
                        </div>
                    `;
                }
            } else {
                showAlert(data.message || 'Failed to delete image', 'danger');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showAlert('An error occurred while deleting the image', 'danger');
        });
    }
}

// Set primary image function
function setPrimaryImage(vehicleId, imageId) {
    console.log('setPrimaryImage called with vehicleId:', vehicleId, 'imageId:', imageId);
    
    fetch(`<?php echo e(url('/business/vehicles')); ?>/${vehicleId}/images/${imageId}/set-primary`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => {
        console.log('Set primary response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Set primary response data:', data);
        if (data.success) {
            // Remove primary badge from all images
            document.querySelectorAll('.badge.bg-success').forEach(badge => {
                badge.textContent = 'Set Primary';
                badge.className = 'position-absolute top-0 start-0 badge bg-primary';
            });
            
            // Update all buttons
            document.querySelectorAll('button[onclick*="setPrimaryImage"]').forEach(btn => {
                btn.textContent = 'Set Primary';
                btn.disabled = false;
                btn.className = 'btn btn-sm btn-primary w-100';
            });
            
            // Set the selected image as primary
            const imageElement = document.querySelector(`[data-image-id="${imageId}"]`);
            if (imageElement) {
                const badge = imageElement.querySelector('.badge');
                const button = imageElement.querySelector('button[onclick*="setPrimaryImage"]');
                
                if (badge) {
                    badge.textContent = 'Primary';
                    badge.className = 'position-absolute top-0 start-0 badge bg-success';
                }
                
                if (button) {
                    button.textContent = 'Primary';
                    button.disabled = true;
                    button.className = 'btn btn-sm btn-success w-100';
                }
            }
            
            showAlert('Primary image updated successfully!', 'success');
        } else {
            showAlert(data.message || 'Failed to update primary image', 'danger');
        }
    })
    .catch(error => {
        console.error('Set primary error:', error);
        showAlert('An error occurred while updating the primary image', 'danger');
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
    
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Add new alert at the top of the form
    const form = document.getElementById('vehicleForm');
    if (form) {
        form.insertAdjacentHTML('afterbegin', alertHtml);
    }
}


// Vendor search functionality
let vendorSearchTimeout;
let vendorSearchInput, vendorDropdown, vendorIdInput, vendorNameInput;

// Custom fetch function to avoid common.js conflicts
function customFetch(url, options = {}) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(options.method || 'GET', url);
        
        if (options.headers) {
            Object.keys(options.headers).forEach(key => {
                xhr.setRequestHeader(key, options.headers[key]);
            });
        }
        
        xhr.onload = function() {
            const response = {
                ok: xhr.status >= 200 && xhr.status < 300,
                status: xhr.status,
                statusText: xhr.statusText,
                headers: {
                    get: function(name) {
                        return xhr.getResponseHeader(name);
                    }
                },
                json: () => {
                    try {
                        return Promise.resolve(JSON.parse(xhr.responseText));
                    } catch (e) {
                        return Promise.reject(new Error('Invalid JSON response'));
                    }
                },
                text: () => Promise.resolve(xhr.responseText)
            };
            resolve(response);
        };
        
        xhr.onerror = function() {
            reject(new Error('Network error'));
        };
        
        if (options.body) {
            xhr.send(options.body);
        } else {
            xhr.send();
        }
    });
}

function searchVendors(query) {
    if (query.length < 2) {
        vendorDropdown.style.display = 'none';
        return;
    }

    if (vendorSearchTimeout) {
        clearTimeout(vendorSearchTimeout);
    }

    vendorSearchTimeout = setTimeout(() => {
        const url = `<?php echo e(route('business.vendors.search')); ?>?q=${encodeURIComponent(query)}`;
        
        customFetch(url)
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
    }, 300);
}

function displayVendorOptions(vendors) {
    vendorDropdown.innerHTML = '';
    
    vendors.forEach(vendor => {
        const option = document.createElement('div');
        option.className = 'dropdown-item';
        option.style.cursor = 'pointer';
        option.innerHTML = `
            <div class="fw-bold">${vendor.vendor_name}</div>
            <small class="text-muted">${vendor.mobile_number} • ${vendor.vendor_type}</small>
            ${vendor.commission_type && vendor.commission_rate ? 
                `<small class="text-info d-block">Commission: ${vendor.commission_type} - ${vendor.commission_rate}</small>` : 
                ''
            }
        `;
        option.addEventListener('click', () => selectVendor(vendor));
        vendorDropdown.appendChild(option);
    });
    
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
        <div class="dropdown-item text-muted">
            <i class="fas fa-search me-2"></i>No vendors found
        </div>
        <div class="dropdown-item border-top text-primary" style="cursor: pointer;" onclick="openAddVendorModal()">
            <i class="fas fa-plus me-2"></i>Add New Vendor
        </div>
    `;
    vendorDropdown.style.display = 'block';
}

function selectVendor(vendor) {
    vendorSearchInput.value = vendor.vendor_name;
    vendorIdInput.value = vendor.id;
    vendorNameInput.value = vendor.vendor_name;
    vendorDropdown.style.display = 'none';
    
    // Update commission fields if vendor has commission data
    if (vendor.commission_type && vendor.commission_rate) {
        const commissionTypeSelect = document.getElementById('commission_type');
        const commissionValueInput = document.getElementById('commission_value');
        
        if (commissionTypeSelect) {
            let formCommissionType = '';
            if (vendor.commission_type === 'fixed_amount') {
                formCommissionType = 'fixed';
            } else if (vendor.commission_type === 'percentage_of_revenue') {
                formCommissionType = 'percentage';
            }
            commissionTypeSelect.value = formCommissionType;
        }
        
        if (commissionValueInput) {
            commissionValueInput.value = vendor.commission_rate;
        }
    }
}

function openAddVendorModal() {
    const modal = new bootstrap.Modal(document.getElementById('addVendorModal'));
    modal.show();
}

function toggleFrequencyFields() {
    const frequency = document.getElementById('new_vendor_frequency').value;
    const dayOfWeekField = document.getElementById('day_of_week_field');
    const dayOfMonthField = document.getElementById('day_of_month_field');
    
    if (frequency === 'weekly') {
        dayOfWeekField.style.display = 'block';
        dayOfMonthField.style.display = 'none';
    } else if (frequency === 'monthly') {
        dayOfWeekField.style.display = 'none';
        dayOfMonthField.style.display = 'block';
    } else {
        dayOfWeekField.style.display = 'none';
        dayOfMonthField.style.display = 'none';
    }
}

function saveNewVendor() {
    const form = document.getElementById('addVendorForm');
    const formData = new FormData(form);
    
    // Generate unique values to avoid conflicts
    const timestamp = Date.now();
    formData.set('mobile_number', '9' + Math.floor(Math.random() * 9000000000) + 1000000000);
    formData.set('email_address', 'vendor' + timestamp + '@example.com');
    formData.set('pan_number', 'ABCDE' + Math.floor(Math.random() * 10000) + 'F');
    
    customFetch('<?php echo e(route("business.vendors.store")); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.vendor) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addVendorModal'));
            modal.hide();
            
            // Clear form
            form.reset();
            
            // Auto-select the new vendor
            selectVendor(data.vendor);
            
            // Show success message
            alert('Vendor added successfully!');
        } else {
            alert('Error adding vendor: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error adding vendor:', error);
        alert('Error adding vendor: ' + error.message);
    });
}

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

    // Initialize vendor search elements
    vendorSearchInput = document.getElementById('vendor_search');
    vendorDropdown = document.getElementById('vendor_dropdown');
    vendorIdInput = document.getElementById('vendor_id');
    vendorNameInput = document.getElementById('vendor_name');

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

    // Initialize vendor search
    if (vendorSearchInput) {
        vendorSearchInput.addEventListener('input', function() {
            searchVendors(this.value);
        });
        
        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!vendorSearchInput.contains(e.target) && !vendorDropdown.contains(e.target)) {
                vendorDropdown.style.display = 'none';
            }
        });
    }

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
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
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
        
        
        fetch(this.action, {
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
                    console.error('Server error response:', text);
                    throw new Error('Server error: ' + response.status + ' - ' + text);
                });
            }
        })
        .then(data => {
            if (data.success) {
                showSuccessAlert('Vehicle updated successfully! Redirecting...');
                setTimeout(() => {
                    window.location.href = data.redirect_url || '<?php echo e(route("business.vehicles.index")); ?>';
                }, 2000);
            } else {
                let errorMessage = data.message || 'Update failed. Please try again.';
                
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
            showErrorAlert('An error occurred while updating the vehicle: ' + error.message);
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

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    let vehicleMakes = [];
    let vehicleModels = [];
    let currentVehicleType = '<?php echo e($vehicle->vehicle_type); ?>';
    let currentMake = '<?php echo e($vehicle->vehicle_make); ?>';
    let currentModel = '<?php echo e($vehicle->vehicle_model); ?>';
    

    // Load vehicle makes when vehicle type changes
    $('#vehicle_type').on('change', function() {
        currentVehicleType = $(this).val();
        loadVehicleMakes();
    });

    // Load vehicle models when make changes
    $('#vehicle_make').on('change', function() {
        const makeName = $(this).val();
        loadVehicleModels(makeName);
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
            url: '<?php echo e(route('business.api.vehicle-makes')); ?>',
            method: 'GET',
            data: { type: currentVehicleType },
            success: function(response) {
                vehicleMakes = response;
                $('#vehicle_make').empty().append('<option value="">Select Vehicle Make</option>');
                
                response.forEach(function(make) {
                    // Try exact match first
                    let selected = make.name === currentMake ? 'selected' : '';
                    
                    // If no exact match, try partial match (current make contains API make name)
                    if (!selected && currentMake && currentMake.includes(make.name)) {
                        selected = 'selected';
                    }
                    
                    // If still no match, try reverse partial match (API make name contains current make)
                    if (!selected && currentMake && make.name.includes(currentMake)) {
                        selected = 'selected';
                    }
                    
                    $('#vehicle_make').append(`<option value="${make.name}" ${selected}>${make.name}</option>`);
                });

                // Manually set the selected value as a fallback
                if (currentMake) {
                    // Try to find the best match for the current make
                    let bestMatch = null;
                    for (let make of response) {
                        if (make.name === currentMake) {
                            bestMatch = make.name;
                            break;
                        } else if (currentMake.includes(make.name) || make.name.includes(currentMake)) {
                            bestMatch = make.name;
                            break;
                        }
                    }
                    
                    if (bestMatch) {
                        $('#vehicle_make').val(bestMatch);
                    } else {
                    }
                }
                
                // Trigger change to load models if make is already selected
                if (currentMake) {
                    loadVehicleModels(currentMake);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading vehicle makes:', error);
                console.error('Response:', xhr.responseText);
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
                    // Try exact match first
                    let selected = model.name === currentModel ? 'selected' : '';
                    
                    // If no exact match, try partial match (current model contains API model name)
                    if (!selected && currentModel && currentModel.includes(model.name)) {
                        selected = 'selected';
                    }
                    
                    // If still no match, try reverse partial match (API model name contains current model)
                    if (!selected && currentModel && model.name.includes(currentModel)) {
                        selected = 'selected';
                    }
                    
                    $('#vehicle_model').append(`<option value="${model.name}" ${selected}>${model.name}</option>`);
                });
                
                // Manually set the selected value as a fallback
                if (currentModel) {
                    // Try to find the best match for the current model
                    let bestMatch = null;
                    for (let model of response) {
                        if (model.name === currentModel) {
                            bestMatch = model.name;
                            break;
                        } else if (currentModel.includes(model.name) || model.name.includes(currentModel)) {
                            bestMatch = model.name;
                            break;
                        }
                    }
                    
                    if (bestMatch) {
                        $('#vehicle_model').val(bestMatch);
                    } else {
                    }
                }
                
                // Force trigger change event to ensure selection is visible
                $('#vehicle_model').trigger('change');
            },
            error: function(xhr, status, error) {
                console.error('Error loading vehicle models:', error);
                console.error('Response:', xhr.responseText);
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

    // Initialize on page load
    loadVehicleMakes();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('business.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/business/vehicles/edit.blade.php ENDPATH**/ ?>