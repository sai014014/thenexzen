

<?php $__env->startSection('title', 'Subscription Package Details'); ?>
<?php $__env->startSection('page-title', 'Package Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Package Overview -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Package Overview</h5>
                        <div class="btn-group">
                            <a href="<?php echo e(route('super-admin.subscription-packages.edit', $subscriptionPackage)); ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit Package
                            </a>
                            <a href="<?php echo e(route('super-admin.subscription-packages.index')); ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center mb-4">
                                <div class="package-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fas fa-box fa-3x"></i>
                                </div>
                            </div>
                            <h2 class="text-center mb-3"><?php echo e($subscriptionPackage->package_name); ?></h2>
                            <div class="text-center mb-3">
                                <h1 class="text-primary"><?php echo e($subscriptionPackage->formatted_price); ?></h1>
                                <small class="text-muted">per month</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="package-details">
                                <div class="detail-item mb-3">
                                    <strong>Trial Period:</strong> <?php echo e($subscriptionPackage->trial_period_days); ?> days
                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Onboarding Fee:</strong> <?php echo e($subscriptionPackage->formatted_onboarding_fee); ?>

                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Vehicle Capacity:</strong> 
                                    <span class="badge bg-info"><?php echo e($subscriptionPackage->vehicle_capacity_display); ?></span>
                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Status:</strong> 
                                    <span class="badge bg-<?php echo e($subscriptionPackage->status === 'active' ? 'success' : ($subscriptionPackage->status === 'inactive' ? 'danger' : 'secondary')); ?>">
                                        <?php echo e(ucfirst($subscriptionPackage->status)); ?>

                                    </span>
                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Support Type:</strong> <?php echo e(ucfirst($subscriptionPackage->status)); ?>

                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Renewal Type:</strong> <?php echo e(ucfirst($subscriptionPackage->status)); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if($subscriptionPackage->description): ?>
                        <div class="mt-4">
                            <h6>Description:</h6>
                            <p class="text-muted"><?php echo e($subscriptionPackage->description); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Module Management -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Module Management</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Enabled Modules:</h6>
                            <ul class="list-unstyled">
                                <?php
                                    $enabledModules = $subscriptionPackage->getEnabledModules();
                                    $availableModules = $subscriptionPackage->getAvailableModules();
                                ?>
                                <?php if(count($enabledModules) > 0): ?>
                                    <?php $__currentLoopData = $enabledModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><i class="fas fa-check text-success me-2"></i><?php echo e($availableModules[$module] ?? ucfirst(str_replace('_', ' ', $module))); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <li class="text-muted">No modules enabled</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Module Access Summary:</h6>
                            <div class="module-summary">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total Modules:</span>
                                    <strong><?php echo e(count($availableModules)); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Enabled Modules:</span>
                                    <strong><?php echo e(count($enabledModules)); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Access Level:</span>
                                    <strong><?php echo e(round((count($enabledModules) / count($availableModules)) * 100)); ?>%</strong>
                                </div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e((count($enabledModules) / count($availableModules)) * 100); ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Package Availability -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Package Availability</h5>
                </div>
                <div class="card-body">
                    <?php if($subscriptionPackage->features_summary): ?>
                        <div class="mt-3">
                            <h6>Features Summary:</h6>
                            <p class="text-muted"><?php echo e($subscriptionPackage->features_summary); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('super-admin.subscription-packages.edit', $subscriptionPackage)); ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Package
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Delete Package
                        </button>
                        <a href="<?php echo e(route('super-admin.subscription-packages.index')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-list"></i> View All Packages
                        </a>
                    </div>
                </div>
            </div>

            <!-- Package Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Package Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Created:</span>
                            <strong><?php echo e($subscriptionPackage->created_at->format('M d, Y')); ?></strong>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Last Updated:</span>
                            <strong><?php echo e($subscriptionPackage->updated_at->format('M d, Y')); ?></strong>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Active Businesses:</span>
                            <strong class="text-primary"><?php echo e($subscriptionPackage->active_business_count); ?></strong>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Enabled Modules:</span>
                            <strong><?php echo e(count($subscriptionPackage->enabled_modules ?? [])); ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature Comparison -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Feature Summary</h5>
                </div>
                <div class="card-body">
                    <div class="feature-summary">
                        <?php
                            $features = $subscriptionPackage->enabled_modules ?? [];
                            $totalFeatures = 11; // Total possible features
                            $percentage = count($features) / $totalFeatures * 100;
                        ?>
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo e($percentage); ?>%">
                                <?php echo e(round($percentage)); ?>%
                            </div>
                        </div>
                        <p class="text-muted small">
                            <?php echo e(count($features)); ?> of <?php echo e($totalFeatures); ?> features enabled
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Businesses Section -->
    <?php if($subscriptionPackage->active_business_count > 0): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Active Businesses Using This Package</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Business Name</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Subscription Status</th>
                                    <th>Started Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $subscriptionPackage->getActiveBusinesses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="business-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <strong><?php echo e($subscription->business->business_name ?? 'N/A'); ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo e($subscription->business->contact_person ?? 'N/A'); ?></td>
                                    <td><?php echo e($subscription->business->email ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($subscription->status === 'active' ? 'success' : 'warning'); ?>">
                                            <?php echo e(ucfirst($subscription->status)); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($subscription->created_at->format('M d, Y')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Active Businesses</h5>
                    <p class="text-muted">This package is not currently being used by any businesses.</p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the subscription package <strong><?php echo e($subscriptionPackage->package_name); ?></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="<?php echo e(route('super-admin.subscription-packages.destroy', $subscriptionPackage)); ?>" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete Package</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>

<style>
.package-icon {
    font-size: 32px;
}

.detail-item {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.detail-item:last-child {
    border-bottom: none;
}

.stat-item {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.stat-item:last-child {
    border-bottom: none;
}

.billing-cycles .badge,
.payment-methods .badge {
    font-size: 0.875rem;
}

.feature-summary .progress-bar {
    font-size: 0.875rem;
    font-weight: 600;
}

.card-header h5 {
    color: #495057;
    font-weight: 600;
}

.btn-group .btn {
    margin-right: 5px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('super-admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/super-admin/subscription-packages/show.blade.php ENDPATH**/ ?>