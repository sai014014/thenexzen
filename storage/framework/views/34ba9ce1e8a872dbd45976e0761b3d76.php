

<?php $__env->startSection('title', 'View Business - The NexZen Super Admin'); ?>
<?php $__env->startSection('page-title', 'View Business'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8">
        <!-- Business Information Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-building me-2"></i>Business Information
                </h5>
                <div>
                    <a href="<?php echo e(route('super-admin.businesses.edit', $business)); ?>" class="btn btn-warning btn-sm me-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="<?php echo e(route('super-admin.businesses.index')); ?>" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <?php if($business->logo): ?>
                            <img src="<?php echo e($business->logo); ?>" alt="<?php echo e($business->business_name); ?>" class="img-fluid rounded" style="max-height: 200px;">
                        <?php else: ?>
                            <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-building fa-4x"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <h4 class="mb-3"><?php echo e($business->business_name); ?></h4>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <strong>Business Type:</strong><br>
                                <span class="badge bg-info"><?php echo e(ucfirst(str_replace('_', ' ', $business->business_type))); ?></span>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <strong>Status:</strong><br>
                                <?php if($business->status === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php elseif($business->status === 'inactive'): ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Suspended</span>
                                <?php endif; ?>
                                <?php if($business->is_verified): ?>
                                    <span class="badge bg-primary ms-2">Verified</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if($business->description): ?>
                        <div class="mb-3">
                            <strong>Description:</strong><br>
                            <p class="text-muted"><?php echo e($business->description); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-address-book me-2"></i>Contact Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Email:</strong><br>
                            <a href="mailto:<?php echo e($business->email); ?>" class="text-decoration-none"><?php echo e($business->email); ?></a>
                        </div>
                        <div class="mb-3">
                            <strong>Phone:</strong><br>
                            <a href="tel:<?php echo e($business->phone); ?>" class="text-decoration-none"><?php echo e($business->phone); ?></a>
                        </div>
                        <?php if($business->website): ?>
                        <div class="mb-3">
                            <strong>Website:</strong><br>
                            <a href="<?php echo e($business->website); ?>" target="_blank" class="text-decoration-none"><?php echo e($business->website); ?></a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Address:</strong><br>
                            <span class="text-muted">
                                <?php echo e($business->address); ?><br>
                                <?php echo e($business->city); ?>, <?php echo e($business->state); ?> <?php echo e($business->postal_code); ?><br>
                                <?php echo e($business->country); ?>

                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>Subscription Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Plan:</strong><br>
                            <span class="badge bg-primary"><?php echo e(ucfirst($business->subscription_plan)); ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Expires:</strong><br>
                            <?php if($business->subscription_expires_at): ?>
                                <span class="text-muted"><?php echo e($business->subscription_expires_at->format('M d, Y')); ?></span>
                            <?php else: ?>
                                <span class="text-muted">No expiration date</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Business Admins Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>Business Admins (<?php echo e($business->businessAdmins->count()); ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if($business->businessAdmins->count() > 0): ?>
                    <?php $__currentLoopData = $business->businessAdmins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0"><?php echo e($admin->name); ?></h6>
                            <small class="text-muted"><?php echo e($admin->email); ?></small>
                        </div>
                        <div>
                            <span class="badge bg-<?php echo e($admin->role === 'admin' ? 'success' : ($admin->role === 'manager' ? 'warning' : 'info')); ?>">
                                <?php echo e(ucfirst($admin->role)); ?>

                            </span>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="fas fa-users fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No admins found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo e(route('super-admin.businesses.edit', $business)); ?>" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Business
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete(<?php echo e($business->id); ?>)">
                        <i class="fas fa-trash me-2"></i>Delete Business
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this business? This action cannot be undone.</p>
                <p class="text-danger"><strong>Warning:</strong> This will also delete all associated business admins and data.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete Business</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(businessId) {
    const form = document.getElementById('deleteForm');
    form.action = `/super-admin/businesses/${businessId}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('super-admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/super-admin/businesses/show.blade.php ENDPATH**/ ?>