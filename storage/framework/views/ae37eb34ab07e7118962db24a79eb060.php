

<?php $__env->startSection('title', 'Subscription Packages'); ?>
<?php $__env->startSection('page-title', 'Software Packages Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">Subscription Packages</h2>
            <p class="text-muted">Manage software subscription packages and pricing</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?php echo e(route('super-admin.subscription-packages.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Package
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('super-admin.subscription-packages.index')); ?>" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Packages</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo e(request('search')); ?>" placeholder="Search by package name...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                        <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                        <option value="draft" <?php echo e(request('status') == 'draft' ? 'selected' : ''); ?>>Draft</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort_by" class="form-label">Sort By</label>
                    <select class="form-select" id="sort_by" name="sort_by">
                        <option value="package_name" <?php echo e(request('sort_by') == 'package_name' ? 'selected' : ''); ?>>Package Name</option>
                        <option value="subscription_fee" <?php echo e(request('sort_by') == 'subscription_fee' ? 'selected' : ''); ?>>Subscription Fee</option>
                        <option value="status" <?php echo e(request('sort_by') == 'status' ? 'selected' : ''); ?>>Status</option>
                        <option value="created_at" <?php echo e(request('sort_by') == 'created_at' ? 'selected' : ''); ?>>Created Date</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sort_order" class="form-label">Order</label>
                    <select class="form-select" id="sort_order" name="sort_order">
                        <option value="asc" <?php echo e(request('sort_order') == 'asc' ? 'selected' : ''); ?>>Ascending</option>
                        <option value="desc" <?php echo e(request('sort_order') == 'desc' ? 'selected' : ''); ?>>Descending</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                    <a href="<?php echo e(route('super-admin.subscription-packages.index')); ?>" class="btn btn-outline-secondary">Clear Filters</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Packages Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Package List</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Package Name</th>
                            <th>Subscription Fee</th>
                            <th>Features</th>
                            <th>Businesses Using</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="package-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-box"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?php echo e($package->package_name); ?></h6>
                                            <small class="text-muted"><?php echo e($package->vehicle_capacity_display); ?> vehicles</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo e($package->formatted_price); ?></strong>
                                        <small class="text-muted d-block"><?php echo e($package->status); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div class="features-preview" data-bs-toggle="tooltip" data-bs-placement="top" 
                                         title="<?php echo e(implode(', ', $package->enabled_modules ?? [])); ?>">
                                        <span class="badge bg-info me-1"><?php echo e(count($package->enabled_modules ?? [])); ?> Modules</span>
                                        <i class="fas fa-info-circle text-muted" style="font-size: 12px;"></i>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <span class="badge bg-primary fs-6"><?php echo e($package->active_business_subscriptions_count); ?></span>
                                        <small class="text-muted d-block">Active</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" type="checkbox" 
                                               id="status-<?php echo e($package->id); ?>" 
                                               data-package-id="<?php echo e($package->id); ?>"
                                               data-business-count="<?php echo e($package->active_business_subscriptions_count); ?>"
                                               <?php echo e($package->status === 'active' ? 'checked' : ''); ?>

                                               <?php if($package->status === 'active' && $package->active_business_subscriptions_count > 0): ?>
                                                   disabled
                                                   title="Cannot deactivate: <?php echo e($package->active_business_subscriptions_count); ?> active business(es) using this package"
                                               <?php endif; ?>>
                                        <label class="form-check-label" for="status-<?php echo e($package->id); ?>">
                                            <span class="badge bg-<?php echo e($package->status === 'active' ? 'success' : 'secondary'); ?>">
                                                <?php echo e(ucfirst($package->status)); ?>

                                            </span>
                                            <?php if($package->status === 'active' && $package->active_business_subscriptions_count > 0): ?>
                                                <small class="text-muted d-block">🔒 Locked</small>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('super-admin.subscription-packages.show', $package)); ?>" 
                                           class="btn btn-sm btn-outline-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('super-admin.subscription-packages.edit', $package)); ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-package" 
                                                data-package-id="<?php echo e($package->id); ?>" 
                                                data-package-name="<?php echo e($package->package_name); ?>" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-box fa-3x mb-3"></i>
                                        <p>No subscription packages found.</p>
                                        <a href="<?php echo e(route('super-admin.subscription-packages.create')); ?>" class="btn btn-primary">
                                            Create First Package
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <?php echo e($packages->links()); ?>

        </div>
    </div>
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
                <p>Are you sure you want to delete the subscription package <strong id="package-name"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete Package</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Status toggle functionality
    document.querySelectorAll('.status-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            // Check if toggle is disabled
            if (this.disabled) {
                this.checked = true; // Keep it checked
                const businessCount = this.dataset.businessCount;
                alert('❌ Cannot deactivate this package. It is currently being used by ' + businessCount + ' active business(es). Please contact the businesses to upgrade or cancel their subscriptions first.');
                return;
            }
            
            const packageId = this.dataset.packageId;
            const isActive = this.checked;
            
            fetch(`<?php echo e(url('/super-admin/subscription-packages')); ?>/${packageId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the badge
                    const badge = this.nextElementSibling.querySelector('.badge');
                    badge.textContent = data.new_status.charAt(0).toUpperCase() + data.new_status.slice(1);
                    badge.className = `badge bg-${data.new_status === 'active' ? 'success' : 'secondary'}`;
                } else {
                    // Revert the toggle
                    this.checked = !isActive;
                    // Show a more user-friendly error message
                    const errorMessage = data.message || 'An error occurred while updating the status.';
                    alert('❌ ' + errorMessage);
                }
            })
            .catch(error => {
                // Revert the toggle
                this.checked = !isActive;
                alert('An error occurred while updating the status.');
            });
        });
    });

    // Delete package functionality
    document.querySelectorAll('.delete-package').forEach(function(button) {
        button.addEventListener('click', function() {
            const packageId = this.dataset.packageId;
            const packageName = this.dataset.packageName;
            
            document.getElementById('package-name').textContent = packageName;
            document.getElementById('delete-form').action = `<?php echo e(url('/super-admin/subscription-packages')); ?>/${packageId}`;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        });
    });
});
</script>

<style>
.package-icon {
    font-size: 16px;
}

.features-preview {
    cursor: help;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('super-admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/super-admin/subscription-packages/index.blade.php ENDPATH**/ ?>