

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
                                    <strong>Support Type:</strong> <?php echo e(ucfirst($subscriptionPackage->support_type)); ?>

                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Renewal Type:</strong> <?php echo e(ucfirst($subscriptionPackage->renewal_type)); ?>

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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Active Businesses Using This Package</h5>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="showAddBusinessModal()">
                            <i class="fas fa-plus"></i> Add Business
                        </button>
                        <span class="badge bg-primary"><?php echo e($subscriptionPackage->active_business_count); ?> Active</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Business Name</th>
                                    <th>Email</th>
                                    <th>Subscription Status</th>
                                    <th>Trial Ends</th>
                                    <th>Days Remaining</th>
                                    <th>Started Date</th>
                                    <th>Actions</th>
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
                                    <td><?php echo e($subscription->business->email ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($subscription->status === 'active' ? 'success' : ($subscription->status === 'trial' ? 'warning' : 'secondary')); ?>">
                                            <?php echo e(ucfirst($subscription->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php if($subscription->trial_ends_at): ?>
                                            <?php echo e($subscription->trial_ends_at->format('M d, Y')); ?>

                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($subscription->trial_ends_at): ?>
                                            <?php
                                                $daysRemaining = max(0, $subscription->trial_ends_at->diffInDays(now()));
                                                $isExpired = $subscription->trial_ends_at < now();
                                            ?>
                                            <?php if($isExpired): ?>
                                                <span class="badge bg-danger">Expired</span>
                                            <?php else: ?>
                                                <span class="badge bg-<?php echo e($daysRemaining <= 3 ? 'warning' : 'info'); ?>"><?php echo e($daysRemaining); ?> days</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($subscription->created_at->format('M d, Y')); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-info" onclick="viewBusinessDetails(<?php echo e($subscription->business->id); ?>)" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" onclick="extendTrial(<?php echo e($subscription->id); ?>)" title="Extend Trial">
                                                <i class="fas fa-clock"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="removeBusinessFromPackage(<?php echo e($subscription->id); ?>)" title="Remove from Package">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
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

<!-- Add Business Modal -->
<div class="modal fade" id="addBusinessModal" tabindex="-1" aria-labelledby="addBusinessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBusinessModalLabel">Add Business to Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="businessSearch" class="form-label">Search Business</label>
                    <input type="text" class="form-control" id="businessSearch" placeholder="Type business name or email...">
                    <div id="businessSearchResults" class="mt-2"></div>
                </div>
                <div class="mb-3">
                    <label for="trialDays" class="form-label">Trial Period (Days)</label>
                    <input type="number" class="form-control" id="trialDays" value="<?php echo e($subscriptionPackage->trial_period_days); ?>" min="1" max="365">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addBusinessToPackage()">Add Business</button>
            </div>
        </div>
    </div>
</div>

<!-- Extend Trial Modal -->
<div class="modal fade" id="extendTrialModal" tabindex="-1" aria-labelledby="extendTrialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="extendTrialModalLabel">Extend Trial Period</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="extensionDays" class="form-label">Additional Days</label>
                    <input type="number" class="form-control" id="extensionDays" min="1" max="365" value="7">
                </div>
                <div class="mb-3">
                    <label for="extensionReason" class="form-label">Reason (Optional)</label>
                    <textarea class="form-control" id="extensionReason" rows="3" placeholder="Reason for extending trial..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="confirmExtendTrial()">Extend Trial</button>
            </div>
        </div>
    </div>
</div>

<!-- Remove Business Modal -->
<div class="modal fade" id="removeBusinessModal" tabindex="-1" aria-labelledby="removeBusinessModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeBusinessModalLabel">Remove Business from Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove this business from the <strong><?php echo e($subscriptionPackage->package_name); ?></strong> package?</p>
                <div class="mb-3">
                    <label for="removalReason" class="form-label">Reason (Required)</label>
                    <textarea class="form-control" id="removalReason" rows="3" placeholder="Reason for removing business..." required></textarea>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This will cancel their subscription and they will lose access to the business portal.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmRemoveBusiness()">Remove Business</button>
            </div>
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

// Business management functions
let selectedBusinessId = null;
let selectedSubscriptionId = null;

function showAddBusinessModal() {
    const modal = new bootstrap.Modal(document.getElementById('addBusinessModal'));
    modal.show();
}

function viewBusinessDetails(businessId) {
    // Redirect to business details page or show in modal
    window.open(`/super-admin/businesses/${businessId}`, '_blank');
}

function extendTrial(subscriptionId) {
    selectedSubscriptionId = subscriptionId;
    const modal = new bootstrap.Modal(document.getElementById('extendTrialModal'));
    modal.show();
}

function removeBusinessFromPackage(subscriptionId) {
    selectedSubscriptionId = subscriptionId;
    const modal = new bootstrap.Modal(document.getElementById('removeBusinessModal'));
    modal.show();
}

// Business search functionality
let searchTimeout;
document.getElementById('businessSearch').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value;
    
    if (query.length < 2) {
        document.getElementById('businessSearchResults').innerHTML = '';
        return;
    }
    
    searchTimeout = setTimeout(() => {
        searchBusinesses(query);
    }, 300);
});

function searchBusinesses(query) {
    fetch(`<?php echo e(route('super-admin.businesses.search')); ?>?q=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        const resultsDiv = document.getElementById('businessSearchResults');
        if (data.success && data.businesses.length > 0) {
            resultsDiv.innerHTML = data.businesses.map(business => `
                <div class="card mb-2 p-2 business-option" onclick="selectBusiness(${business.id}, '${business.business_name}', '${business.email}')" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${business.business_name}</strong>
                            <br><small class="text-muted">${business.email}</small>
                        </div>
                        <i class="fas fa-plus text-primary"></i>
                    </div>
                </div>
            `).join('');
        } else {
            resultsDiv.innerHTML = '<div class="text-muted">No businesses found</div>';
        }
    })
    .catch(error => {
        console.error('Error searching businesses:', error);
        document.getElementById('businessSearchResults').innerHTML = '<div class="text-danger">Error searching businesses</div>';
    });
}

function selectBusiness(businessId, businessName, email) {
    selectedBusinessId = businessId;
    document.getElementById('businessSearch').value = `${businessName} (${email})`;
    document.getElementById('businessSearchResults').innerHTML = '';
}

function addBusinessToPackage() {
    if (!selectedBusinessId) {
        alert('Please select a business first');
        return;
    }
    
    const trialDays = document.getElementById('trialDays').value;
    
    fetch(`<?php echo e(route('super-admin.subscription-packages.add-business', $subscriptionPackage->id)); ?>`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            business_id: selectedBusinessId,
            trial_days: trialDays
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Business added to package successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the business');
    });
}

function confirmExtendTrial() {
    if (!selectedSubscriptionId) {
        alert('No subscription selected');
        return;
    }
    
    const extensionDays = document.getElementById('extensionDays').value;
    const reason = document.getElementById('extensionReason').value;
    
    fetch(`<?php echo e(route('super-admin.subscription-packages.extend-trial')); ?>`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            subscription_id: selectedSubscriptionId,
            extension_days: extensionDays,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Trial extended successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while extending the trial');
    });
}

function confirmRemoveBusiness() {
    if (!selectedSubscriptionId) {
        alert('No subscription selected');
        return;
    }
    
    const reason = document.getElementById('removalReason').value;
    if (!reason.trim()) {
        alert('Please provide a reason for removal');
        return;
    }
    
    fetch(`<?php echo e(route('super-admin.subscription-packages.remove-business')); ?>`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            subscription_id: selectedSubscriptionId,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Business removed from package successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while removing the business');
    });
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