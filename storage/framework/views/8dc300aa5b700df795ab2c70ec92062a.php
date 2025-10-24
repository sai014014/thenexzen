

<?php $__env->startSection('title', 'Create Subscription Package'); ?>
<?php $__env->startSection('page-title', 'Create New Package'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="<?php echo e(route('super-admin.subscription-packages.store')); ?>">
                <?php echo csrf_field(); ?>

                <!-- Package Details Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">A. Package Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="package_name" class="form-label">1️⃣ Package Name</label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['package_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="package_name" name="package_name" 
                                           value="<?php echo e(old('package_name', '')); ?>" 
                                           placeholder="e.g., Starter, Pro, Max" required>
                                    <?php $__errorArgs = ['package_name'];
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
                                <div class="mb-3">
                                    <label for="subscription_fee" class="form-label">2️⃣ Package Price</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control <?php $__errorArgs = ['subscription_fee'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               id="subscription_fee" name="subscription_fee" 
                                               value="<?php echo e(old('subscription_fee', '')); ?>" 
                                               step="0.01" min="0" required>
                                        <select class="form-select <?php $__errorArgs = ['currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="currency" style="max-width: 100px;">
                                            <option value="INR" <?php echo e(old('currency', 'INR') == 'INR' ? 'selected' : ''); ?>>₹</option>
                                            <option value="USD" <?php echo e(old('currency', '') == 'USD' ? 'selected' : ''); ?>>$</option>
                                            <option value="EUR" <?php echo e(old('currency', '') == 'EUR' ? 'selected' : ''); ?>>€</option>
                                        </select>
                                    </div>
                                    <small class="form-text text-muted">per month / year</small>
                                    <?php $__errorArgs = ['subscription_fee'];
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="trial_period_days" class="form-label">3️⃣ Trial Period (Days)</label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['trial_period_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="trial_period_days" name="trial_period_days" 
                                           value="<?php echo e(old('trial_period_days', 14)); ?>" 
                                           min="0" max="365" required>
                                    <small class="form-text text-muted">Default: 14 days</small>
                                    <?php $__errorArgs = ['trial_period_days'];
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="onboarding_fee" class="form-label">4️⃣ Onboarding Fee</label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['onboarding_fee'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="onboarding_fee" name="onboarding_fee" 
                                           value="<?php echo e(old('onboarding_fee', 6000)); ?>" 
                                           step="0.01" min="0" required>
                                    <small class="form-text text-muted">Default: 6000</small>
                                    <?php $__errorArgs = ['onboarding_fee'];
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="vehicle_capacity" class="form-label">5️⃣ Vehicle Capacity</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control <?php $__errorArgs = ['vehicle_capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                               id="vehicle_capacity" name="vehicle_capacity" 
                                               value="<?php echo e(old('vehicle_capacity', '')); ?>" 
                                               min="1" <?php echo e(old('is_unlimited_vehicles', false) ? 'disabled' : ''); ?>>
                                        <div class="form-check form-switch ms-3">
                                            <input class="form-check-input" type="checkbox" id="is_unlimited_vehicles" 
                                                   name="is_unlimited_vehicles" value="1" 
                                                   <?php echo e(old('is_unlimited_vehicles', false) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="is_unlimited_vehicles">
                                                Unlimited
                                            </label>
                                        </div>
                                    </div>
                                    <?php $__errorArgs = ['vehicle_capacity'];
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
                </div>

                <!-- Module Management Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">B. Module Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h6>Enable Modules for this Package</h6>
                                <p class="text-muted">Select which modules businesses can access with this subscription package.</p>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="module_vehicles" 
                                                   name="enabled_modules[]" value="vehicles" 
                                                   <?php echo e(in_array('vehicles', old('enabled_modules', [])) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="module_vehicles">
                                                🚗 Vehicle Management
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="module_bookings" 
                                                   name="enabled_modules[]" value="bookings" 
                                                   <?php echo e(in_array('bookings', old('enabled_modules', [])) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="module_bookings">
                                                📅 Booking Management
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="module_customers" 
                                                   name="enabled_modules[]" value="customers" 
                                                   <?php echo e(in_array('customers', old('enabled_modules', [])) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="module_customers">
                                                👥 Customer Management
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="module_reports" 
                                                   name="enabled_modules[]" value="reports" 
                                                   <?php echo e(in_array('reports', old('enabled_modules', [])) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="module_reports">
                                                📊 Reports & Analytics
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="module_notifications" 
                                                   name="enabled_modules[]" value="notifications" 
                                                   <?php echo e(in_array('notifications', old('enabled_modules', [])) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="module_notifications">
                                                🔔 Notifications
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="module_vendors" 
                                                   name="enabled_modules[]" value="vendors" 
                                                   <?php echo e(in_array('vendors', old('enabled_modules', [])) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="module_vendors">
                                                🏢 Vendor Management
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="module_subscription" 
                                                   name="enabled_modules[]" value="subscription" 
                                                   <?php echo e(in_array('subscription', old('enabled_modules', [])) ? 'checked' : ''); ?>>
                                            <label class="form-check-label" for="module_subscription">
                                                💳 Subscription Management
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Package Availability Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">C. Package Availability</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="status" name="status" required>
                                        <option value="draft" <?php echo e(old('status', 'draft') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                                        <option value="active" <?php echo e(old('status', '') == 'active' ? 'selected' : ''); ?>>Active</option>
                                        <option value="inactive" <?php echo e(old('status', '') == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                    </select>
                                    <?php $__errorArgs = ['status'];
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
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Brief description of the package..."><?php echo e(old('description', '')); ?></textarea>
                                    <?php $__errorArgs = ['description'];
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
                                <div class="mb-3">
                                    <label for="features_summary" class="form-label">Features Summary</label>
                                    <textarea class="form-control <?php $__errorArgs = ['features_summary'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                              id="features_summary" name="features_summary" rows="3" 
                                              placeholder="Summary of key features..."><?php echo e(old('features_summary', '')); ?></textarea>
                                    <?php $__errorArgs = ['features_summary'];
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
                </div>

                <!-- Submit Button -->
                <div class="card">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-save"></i> Save Package
                        </button>
                        <a href="<?php echo e(route('super-admin.subscription-packages.index')); ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Preview Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Package Preview</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="package-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                    <h4 id="preview-name" class="text-center mb-3">Package Name</h4>
                    <div class="text-center mb-3">
                        <h3 id="preview-price" class="text-primary">₹ 0.00</h3>
                        <small class="text-muted">per month</small>
                    </div>
                    <div class="mb-3">
                        <h6>Features:</h6>
                        <ul id="preview-features" class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Basic Features</li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <h6>Vehicle Capacity:</h6>
                        <span id="preview-capacity" class="badge bg-info">Unlimited</span>
                    </div>
                    <div class="mb-3">
                        <h6>Status:</h6>
                        <span id="preview-status" class="badge bg-secondary">Draft</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle unlimited vehicles checkbox
    const unlimitedCheckbox = document.getElementById('is_unlimited_vehicles');
    const vehicleCapacityInput = document.getElementById('vehicle_capacity');
    
    unlimitedCheckbox.addEventListener('change', function() {
        if (this.checked) {
            vehicleCapacityInput.disabled = true;
            vehicleCapacityInput.value = '';
        } else {
            vehicleCapacityInput.disabled = false;
        }
        updatePreview();
    });

    // Update preview on form changes
    const formInputs = document.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        input.addEventListener('change', updatePreview);
        input.addEventListener('input', updatePreview);
    });

    function updatePreview() {
        // Update package name
        const packageName = document.getElementById('package_name').value || 'Package Name';
        document.getElementById('preview-name').textContent = packageName;

        // Update price
        const price = document.getElementById('subscription_fee').value || '0';
        const currency = document.querySelector('select[name="currency"]').value;
        const currencySymbol = currency === 'INR' ? '₹' : currency === 'USD' ? '$' : '€';
        document.getElementById('preview-price').textContent = currencySymbol + ' ' + parseFloat(price).toFixed(2);

        // Update vehicle capacity
        const isUnlimited = document.getElementById('is_unlimited_vehicles').checked;
        const capacity = document.getElementById('vehicle_capacity').value;
        const capacityText = isUnlimited ? 'Unlimited' : (capacity || '0');
        document.getElementById('preview-capacity').textContent = capacityText;

        // Update status
        const status = document.getElementById('status').value;
        const statusElement = document.getElementById('preview-status');
        statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        statusElement.className = `badge bg-${status === 'active' ? 'success' : status === 'inactive' ? 'danger' : 'secondary'}`;

        // Update features
        const features = [];
        const featureCheckboxes = document.querySelectorAll('input[type="checkbox"][name$="_management"], input[type="checkbox"][name$="_reporting"], input[type="checkbox"][name$="_reminders"], input[type="checkbox"][name$="_options"], input[type="checkbox"][name$="_access"], input[type="checkbox"][name$="_manager"]');
        
        featureCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const label = checkbox.nextElementSibling.textContent.replace('✅ ', '');
                features.push(label);
            }
        });

        const featuresList = document.getElementById('preview-features');
        featuresList.innerHTML = '';
        if (features.length > 0) {
            features.forEach(feature => {
                const li = document.createElement('li');
                li.innerHTML = `<i class="fas fa-check text-success me-2"></i>${feature}`;
                featuresList.appendChild(li);
            });
        } else {
            featuresList.innerHTML = '<li class="text-muted">No features selected</li>';
        }
    }

    // Initial preview update
    updatePreview();
});
</script>

<style>
.package-icon {
    font-size: 24px;
}

.form-check-label {
    font-weight: 500;
}

.card-header h5 {
    color: #495057;
    font-weight: 600;
}

.form-label {
    font-weight: 500;
    color: #495057;
}

.btn-lg {
    padding: 12px 30px;
    font-size: 16px;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('super-admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/super-admin/subscription-packages/create.blade.php ENDPATH**/ ?>