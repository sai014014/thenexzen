

<?php $__env->startSection('title', 'Edit Vendor - ' . $vendor->vendor_name); ?>
<?php $__env->startSection('page-title', 'Edit Vendor'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Vendor
                </h5>
                <small class="text-muted">Update vendor information and settings</small>
            </div>
            <div class="card-body">
                <form id="vendorForm" method="POST" action="<?php echo e(route('business.vendors.update', $vendor)); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <!-- Vendor Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-building me-2"></i>Vendor Information
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vendor_name" class="form-label">Vendor Name *</label>
                            <input type="text" class="form-control" id="vendor_name" name="vendor_name" value="<?php echo e(old('vendor_name', $vendor->vendor_name)); ?>" required>
                            <?php $__errorArgs = ['vendor_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vendor_type" class="form-label">Vendor Type *</label>
                            <select class="form-select" id="vendor_type" name="vendor_type" required>
                                <option value="">Select Vendor Type</option>
                                <option value="vehicle_provider" <?php echo e(old('vendor_type', $vendor->vendor_type) == 'vehicle_provider' ? 'selected' : ''); ?>>Vehicle Provider</option>
                                <option value="service_partner" <?php echo e(old('vendor_type', $vendor->vendor_type) == 'service_partner' ? 'selected' : ''); ?>>Service Partner</option>
                                <option value="other" <?php echo e(old('vendor_type', $vendor->vendor_type) == 'other' ? 'selected' : ''); ?>>Other</option>
                            </select>
                            <?php $__errorArgs = ['vendor_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gstin" class="form-label">GSTIN</label>
                            <input type="text" class="form-control" id="gstin" name="gstin" value="<?php echo e(old('gstin', $vendor->gstin)); ?>" maxlength="15">
                            <div class="form-text">15-digit GST Identification Number</div>
                            <?php $__errorArgs = ['gstin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pan_number" class="form-label">PAN Number *</label>
                            <input type="text" class="form-control" id="pan_number" name="pan_number" value="<?php echo e(old('pan_number', $vendor->pan_number)); ?>" maxlength="10" required>
                            <div class="form-text">10-digit PAN number</div>
                            <?php $__errorArgs = ['pan_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="primary_contact_person" class="form-label">Primary Contact Person *</label>
                            <input type="text" class="form-control" id="primary_contact_person" name="primary_contact_person" value="<?php echo e(old('primary_contact_person', $vendor->primary_contact_person)); ?>" required>
                            <?php $__errorArgs = ['primary_contact_person'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-phone me-2"></i>Contact Information
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mobile_number" class="form-label">Mobile Number *</label>
                            <input type="tel" class="form-control" id="mobile_number" name="mobile_number" value="<?php echo e(old('mobile_number', $vendor->mobile_number)); ?>" required>
                            <?php $__errorArgs = ['mobile_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="alternate_contact_number" class="form-label">Alternate Contact Number</label>
                            <input type="tel" class="form-control" id="alternate_contact_number" name="alternate_contact_number" value="<?php echo e(old('alternate_contact_number', $vendor->alternate_contact_number)); ?>">
                            <?php $__errorArgs = ['alternate_contact_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email_address" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email_address" name="email_address" value="<?php echo e(old('email_address', $vendor->email_address)); ?>" required>
                            <?php $__errorArgs = ['email_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-map-marker-alt me-2"></i>Address Information
                            </h6>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="office_address" class="form-label">Office Address *</label>
                            <textarea class="form-control" id="office_address" name="office_address" rows="3" required><?php echo e(old('office_address', $vendor->office_address)); ?></textarea>
                            <?php $__errorArgs = ['office_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="additional_branches" class="form-label">Additional Branches</label>
                            <div id="additionalBranchesContainer">
                                <?php if($vendor->additional_branches && count($vendor->additional_branches) > 0): ?>
                                    <?php $__currentLoopData = $vendor->additional_branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="additional_branches[]" value="<?php echo e($branch); ?>" placeholder="Enter branch address">
                                        <button type="button" class="btn btn-outline-danger" onclick="removeBranch(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="additional_branches[]" placeholder="Enter branch address">
                                        <button type="button" class="btn btn-outline-danger" onclick="removeBranch(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addBranch()">
                                <i class="fas fa-plus me-1"></i>Add Branch
                            </button>
                        </div>
                    </div>

                    <!-- Vendor Payout Settings -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-credit-card me-2"></i>Vendor Payout Settings
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="payout_method" class="form-label">Payout Method *</label>
                            <select class="form-select" id="payout_method" name="payout_method" required onchange="togglePayoutFields()">
                                <option value="">Select Payout Method</option>
                                <option value="bank_transfer" <?php echo e(old('payout_method', $vendor->payout_method) == 'bank_transfer' ? 'selected' : ''); ?>>Bank Transfer (NEFT/RTGS)</option>
                                <option value="upi_payment" <?php echo e(old('payout_method', $vendor->payout_method) == 'upi_payment' ? 'selected' : ''); ?>>UPI Payment</option>
                                <option value="cheque" <?php echo e(old('payout_method', $vendor->payout_method) == 'cheque' ? 'selected' : ''); ?>>Cheque</option>
                                <option value="other" <?php echo e(old('payout_method', $vendor->payout_method) == 'other' ? 'selected' : ''); ?>>Other</option>
                            </select>
                            <?php $__errorArgs = ['payout_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3" id="otherPayoutMethodDiv" style="display: none;">
                            <label for="other_payout_method" class="form-label">Specify Other Method</label>
                            <input type="text" class="form-control" id="other_payout_method" name="other_payout_method" value="<?php echo e(old('other_payout_method', $vendor->other_payout_method)); ?>">
                            <?php $__errorArgs = ['other_payout_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Bank Details (if Bank Transfer selected) -->
                    <div id="bankDetailsDiv" style="display: none;">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-university me-2"></i>Bank Details
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bank_name" class="form-label">Bank Name *</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?php echo e(old('bank_name', $vendor->bank_name)); ?>">
                                <?php $__errorArgs = ['bank_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger small"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="account_holder_name" class="form-label">Account Holder Name *</label>
                                <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" value="<?php echo e(old('account_holder_name', $vendor->account_holder_name)); ?>">
                                <?php $__errorArgs = ['account_holder_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger small"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="account_number" class="form-label">Account Number *</label>
                                <input type="text" class="form-control" id="account_number" name="account_number" value="<?php echo e(old('account_number', $vendor->account_number)); ?>">
                                <?php $__errorArgs = ['account_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger small"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ifsc_code" class="form-label">IFSC Code *</label>
                                <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="<?php echo e(old('ifsc_code', $vendor->ifsc_code)); ?>" maxlength="11">
                                <?php $__errorArgs = ['ifsc_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger small"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bank_branch_name" class="form-label">Bank Branch Name</label>
                                <input type="text" class="form-control" id="bank_branch_name" name="bank_branch_name" value="<?php echo e(old('bank_branch_name', $vendor->bank_branch_name)); ?>">
                                <?php $__errorArgs = ['bank_branch_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger small"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <!-- UPI Payment Details (if UPI Payment selected) -->
                    <div id="upiDetailsDiv" style="display: none;">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-mobile-alt me-2"></i>UPI Payment Details
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="upi_id" class="form-label">UPI ID *</label>
                                <input type="text" class="form-control" id="upi_id" name="upi_id" value="<?php echo e(old('upi_id', $vendor->upi_id)); ?>" placeholder="example@upi">
                                <?php $__errorArgs = ['upi_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="text-danger small"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Payout Frequency -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-calendar-alt me-2"></i>Payout Frequency
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="payout_frequency" class="form-label">Payout Frequency *</label>
                            <select class="form-select" id="payout_frequency" name="payout_frequency" required onchange="togglePayoutSchedule()">
                                <option value="">Select Frequency</option>
                                <option value="weekly" <?php echo e(old('payout_frequency', $vendor->payout_frequency) == 'weekly' ? 'selected' : ''); ?>>Weekly</option>
                                <option value="bi_weekly" <?php echo e(old('payout_frequency', $vendor->payout_frequency) == 'bi_weekly' ? 'selected' : ''); ?>>Bi-Weekly</option>
                                <option value="monthly" <?php echo e(old('payout_frequency', $vendor->payout_frequency) == 'monthly' ? 'selected' : ''); ?>>Monthly</option>
                                <option value="quarterly" <?php echo e(old('payout_frequency', $vendor->payout_frequency) == 'quarterly' ? 'selected' : ''); ?>>Quarterly</option>
                                <option value="after_every_booking" <?php echo e(old('payout_frequency', $vendor->payout_frequency) == 'after_every_booking' ? 'selected' : ''); ?>>After Every Booking</option>
                            </select>
                            <?php $__errorArgs = ['payout_frequency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3" id="payoutDayOfWeekDiv" style="display: none;">
                            <label for="payout_day_of_week" class="form-label">Day of Week *</label>
                            <select class="form-select" id="payout_day_of_week" name="payout_day_of_week">
                                <option value="">Select Day</option>
                                <option value="monday" <?php echo e(old('payout_day_of_week', $vendor->payout_day_of_week) == 'monday' ? 'selected' : ''); ?>>Monday</option>
                                <option value="tuesday" <?php echo e(old('payout_day_of_week', $vendor->payout_day_of_week) == 'tuesday' ? 'selected' : ''); ?>>Tuesday</option>
                                <option value="wednesday" <?php echo e(old('payout_day_of_week', $vendor->payout_day_of_week) == 'wednesday' ? 'selected' : ''); ?>>Wednesday</option>
                                <option value="thursday" <?php echo e(old('payout_day_of_week', $vendor->payout_day_of_week) == 'thursday' ? 'selected' : ''); ?>>Thursday</option>
                                <option value="friday" <?php echo e(old('payout_day_of_week', $vendor->payout_day_of_week) == 'friday' ? 'selected' : ''); ?>>Friday</option>
                                <option value="saturday" <?php echo e(old('payout_day_of_week', $vendor->payout_day_of_week) == 'saturday' ? 'selected' : ''); ?>>Saturday</option>
                                <option value="sunday" <?php echo e(old('payout_day_of_week', $vendor->payout_day_of_week) == 'sunday' ? 'selected' : ''); ?>>Sunday</option>
                            </select>
                            <?php $__errorArgs = ['payout_day_of_week'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3" id="payoutDayOfMonthDiv" style="display: none;">
                            <label for="payout_day_of_month" class="form-label">Day of Month *</label>
                            <input type="number" class="form-control" id="payout_day_of_month" name="payout_day_of_month" value="<?php echo e(old('payout_day_of_month', $vendor->payout_day_of_month)); ?>" min="1" max="31">
                            <?php $__errorArgs = ['payout_day_of_month'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="payout_terms" class="form-label">Payout Terms</label>
                            <textarea class="form-control" id="payout_terms" name="payout_terms" rows="3" placeholder="Any special payout terms, thresholds, or conditions"><?php echo e(old('payout_terms', $vendor->payout_terms)); ?></textarea>
                            <?php $__errorArgs = ['payout_terms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Commission Settings -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-percentage me-2"></i>Commission Settings
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="commission_type" class="form-label">Commission Type *</label>
                            <select class="form-select" id="commission_type" name="commission_type" required onchange="toggleCommissionFields()">
                                <option value="">Select Commission Type</option>
                                <option value="fixed_amount" <?php echo e(old('commission_type', $vendor->commission_type) == 'fixed_amount' ? 'selected' : ''); ?>>Fixed Amount</option>
                                <option value="percentage_of_revenue" <?php echo e(old('commission_type', $vendor->commission_type) == 'percentage_of_revenue' ? 'selected' : ''); ?>>Percentage of Revenue</option>
                            </select>
                            <?php $__errorArgs = ['commission_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="commission_rate" class="form-label">Commission Rate *</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="commission_rate" name="commission_rate" value="<?php echo e(old('commission_rate', $vendor->commission_rate)); ?>" step="0.01" min="0" required>
                                <span class="input-group-text" id="commissionUnit">₹</span>
                            </div>
                            <?php $__errorArgs = ['commission_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Document Upload -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-file-upload me-2"></i>Document Upload
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vendor_agreement" class="form-label">Vendor Agreement Document</label>
                            <input type="file" class="form-control" id="vendor_agreement" name="vendor_agreement" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF, JPG, PNG files only (Max 10MB)</div>
                            <?php if($vendor->vendor_agreement_path): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Current: </small>
                                    <a href="<?php echo e(route('business.vendors.download-document', [$vendor, 'vendor_agreement'])); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>Download Current
                                    </a>
                                </div>
                            <?php endif; ?>
                            <?php $__errorArgs = ['vendor_agreement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gstin_certificate" class="form-label">GSTIN Certificate</label>
                            <input type="file" class="form-control" id="gstin_certificate" name="gstin_certificate" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF, JPG, PNG files only (Max 10MB)</div>
                            <?php if($vendor->gstin_certificate_path): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Current: </small>
                                    <a href="<?php echo e(route('business.vendors.download-document', [$vendor, 'gstin_certificate'])); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>Download Current
                                    </a>
                                </div>
                            <?php endif; ?>
                            <?php $__errorArgs = ['gstin_certificate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pan_card" class="form-label">PAN Card</label>
                            <input type="file" class="form-control" id="pan_card" name="pan_card" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF, JPG, PNG files only (Max 10MB)</div>
                            <?php if($vendor->pan_card_path): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Current: </small>
                                    <a href="<?php echo e(route('business.vendors.download-document', [$vendor, 'pan_card'])); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>Download Current
                                    </a>
                                </div>
                            <?php endif; ?>
                            <?php $__errorArgs = ['pan_card'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="additional_certificates" class="form-label">Additional Certificates</label>
                            <input type="file" class="form-control" id="additional_certificates" name="additional_certificates[]" accept=".pdf,.jpg,.jpeg,.png" multiple>
                            <div class="form-text">PDF, JPG, PNG files only (Max 10MB each)</div>
                            <?php if($vendor->additional_certificates && count($vendor->additional_certificates) > 0): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Current certificates: <?php echo e(count($vendor->additional_certificates)); ?></small>
                                </div>
                            <?php endif; ?>
                            <?php $__errorArgs = ['additional_certificates.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo e(route('business.vendors.show', $vendor)); ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Vendor
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Toggle payout method fields
function togglePayoutFields() {
    const payoutMethod = document.getElementById('payout_method').value;
    const bankDetailsDiv = document.getElementById('bankDetailsDiv');
    const upiDetailsDiv = document.getElementById('upiDetailsDiv');
    const otherPayoutMethodDiv = document.getElementById('otherPayoutMethodDiv');
    
    // Hide all sections
    bankDetailsDiv.style.display = 'none';
    upiDetailsDiv.style.display = 'none';
    otherPayoutMethodDiv.style.display = 'none';
    
    // Show relevant section
    if (payoutMethod === 'bank_transfer') {
        bankDetailsDiv.style.display = 'block';
    } else if (payoutMethod === 'upi_payment') {
        upiDetailsDiv.style.display = 'block';
    } else if (payoutMethod === 'other') {
        otherPayoutMethodDiv.style.display = 'block';
    }
}

// Toggle payout schedule fields
function togglePayoutSchedule() {
    const payoutFrequency = document.getElementById('payout_frequency').value;
    const payoutDayOfWeekDiv = document.getElementById('payoutDayOfWeekDiv');
    const payoutDayOfMonthDiv = document.getElementById('payoutDayOfMonthDiv');
    
    // Hide all sections
    payoutDayOfWeekDiv.style.display = 'none';
    payoutDayOfMonthDiv.style.display = 'none';
    
    // Show relevant section
    if (payoutFrequency === 'weekly' || payoutFrequency === 'bi_weekly') {
        payoutDayOfWeekDiv.style.display = 'block';
    } else if (payoutFrequency === 'monthly' || payoutFrequency === 'quarterly') {
        payoutDayOfMonthDiv.style.display = 'block';
    }
}

// Toggle commission fields
function toggleCommissionFields() {
    const commissionType = document.getElementById('commission_type').value;
    const commissionUnit = document.getElementById('commissionUnit');
    
    if (commissionType === 'percentage_of_revenue') {
        commissionUnit.textContent = '%';
    } else {
        commissionUnit.textContent = '₹';
    }
}

// Add additional branch
function addBranch() {
    const container = document.getElementById('additionalBranchesContainer');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" class="form-control" name="additional_branches[]" placeholder="Enter branch address">
        <button type="button" class="btn btn-outline-danger" onclick="removeBranch(this)">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

// Remove additional branch
function removeBranch(button) {
    button.parentElement.remove();
}

// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
    togglePayoutFields();
    togglePayoutSchedule();
    toggleCommissionFields();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('business.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/business/vendors/edit.blade.php ENDPATH**/ ?>