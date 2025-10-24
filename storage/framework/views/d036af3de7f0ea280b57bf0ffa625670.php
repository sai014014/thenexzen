

<?php $__env->startSection('title', 'Subscription Management'); ?>
<?php $__env->startSection('page-title', 'My Subscription'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- No Subscription / Trial Ended Message -->
    <?php if(!$currentSubscription): ?>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php if(!$hasSubscriptionHistory): ?>
                                Welcome! Start Your Free Trial
                            <?php else: ?>
                                Subscription Required
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body text-center py-5">
                        <?php if(!$hasSubscriptionHistory): ?>
                            <i class="fas fa-rocket fa-4x text-primary mb-4"></i>
                            <h4 class="text-primary mb-3">Get Started with Your Free Trial</h4>
                            <p class="text-muted mb-4">
                                Experience the full power of our vehicle management system with a free trial. 
                                No credit card required!
                            </p>
                        <?php else: ?>
                            <i class="fas fa-lock fa-4x text-warning mb-4"></i>
                            <h4 class="text-warning mb-3">Subscription Required</h4>
                            <p class="text-muted mb-4">
                                Your trial period has ended. Please choose a subscription plan to continue using our services.
                            </p>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <?php if(!$hasSubscriptionHistory): ?>
                                <button class="btn btn-primary btn-lg me-md-2" onclick="startTrial()">
                                    <i class="fas fa-play me-2"></i>Start Free Trial
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-outline-primary btn-lg" onclick="showPackages()">
                                <i class="fas fa-list me-2"></i>View All Packages
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Current Subscription Card -->
    <?php if($currentSubscription): ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Current Subscription</h5>
                    <span class="badge bg-light text-primary fs-6"><?php echo e($currentSubscription->status_display); ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="text-primary"><?php echo e($currentSubscription->subscriptionPackage->package_name); ?></h4>
                        <p class="text-muted mb-3"><?php echo e($currentSubscription->subscriptionPackage->description); ?></p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="subscription-detail">
                                    <strong>Price:</strong> <?php echo e($currentSubscription->subscriptionPackage->formatted_price); ?>/month
                                </div>
                                <div class="subscription-detail">
                                    <strong>Vehicle Capacity:</strong> <?php echo e($currentSubscription->subscriptionPackage->vehicle_capacity_display); ?>

                                </div>
                                <div class="subscription-detail">
                                    <strong>Support:</strong> <?php echo e(ucfirst($currentSubscription->subscriptionPackage->support_type)); ?>

                                </div>
                                <?php if($currentSubscription->is_trial): ?>
                                <div class="subscription-detail">
                                    <strong>Trial Period:</strong> <?php echo e($currentSubscription->subscriptionPackage->trial_period_days); ?> days
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <div class="subscription-detail">
                                    <strong>Status:</strong> 
                                    <span class="badge bg-<?php echo e($currentSubscription->is_paused ? 'warning' : ($currentSubscription->is_active ? 'success' : ($currentSubscription->is_trial ? 'warning' : 'secondary'))); ?>">
                                        <?php echo e($currentSubscription->is_paused ? 'Paused' : $currentSubscription->status_display); ?>

                                    </span>
                                    <?php if($currentSubscription->is_paused): ?>
                                        <small class="text-muted d-block">Paused on <?php echo e($currentSubscription->paused_at->format('M d, Y')); ?></small>
                                    <?php endif; ?>
                                </div>
                                <div class="subscription-detail">
                                    <strong>Expires:</strong> <?php echo e($currentSubscription->expires_at->format('M d, Y')); ?>

                                </div>
                                <div class="subscription-detail">
                                    <strong>Days Remaining:</strong> <?php echo e($currentSubscription->days_remaining); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="subscription-actions">
                            <?php if($currentSubscription->is_active): ?>
                                <?php if($currentSubscription->is_paused): ?>
                                    <button class="btn btn-success me-2" onclick="resumeSubscription()">
                                        <i class="fas fa-play"></i> Resume
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-warning me-2" onclick="pauseSubscription()">
                                        <i class="fas fa-pause"></i> Pause
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-outline-danger me-2" onclick="cancelSubscription(<?php echo e($currentSubscription->id); ?>)">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button class="btn btn-success" onclick="renewSubscription(<?php echo e($currentSubscription->id); ?>)">
                                    <i class="fas fa-sync"></i> Renew
                                </button>
                            <?php elseif($currentSubscription->is_trial): ?>
                                <button class="btn btn-primary" onclick="upgradeSubscription()">
                                    <i class="fas fa-arrow-up"></i> Upgrade Now
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card mb-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                <h4>No Active Subscription</h4>
                <p class="text-muted">You don't have an active subscription. Please choose a plan to continue using our services.</p>
                <button class="btn btn-primary btn-lg" onclick="showAvailablePackages()">
                    <i class="fas fa-plus"></i> Choose a Plan
                </button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Available Packages -->
    <div class="card mb-4" id="available-packages">
        <div class="card-header">
            <h5 class="mb-0">Available Packages</h5>
        </div>
        <div class="card-body">
            <?php if($availablePackages->count() > 0): ?>
                <div class="row">
                    <?php $__currentLoopData = $availablePackages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-4 mb-3">
                            <div class="package-card h-100">
                                <div class="card h-100">
                                    <div class="card-header text-center">
                                        <h6 class="mb-0"><?php echo e($package->package_name); ?></h6>
                                        <h4 class="text-primary mt-2"><?php echo e($package->formatted_price); ?>/month</h4>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted small"><?php echo e($package->description); ?></p>
                                        <ul class="list-unstyled small">
                                            <li><i class="fas fa-check text-success me-2"></i><?php echo e($package->vehicle_capacity_display); ?> vehicles</li>
                                            <li><i class="fas fa-check text-success me-2"></i><?php echo e(ucfirst($package->status)); ?> support</li>
                                            <li><i class="fas fa-check text-success me-2"></i><?php echo e($package->trial_period_days); ?> days trial</li>
                                        </ul>
                                    </div>
                                    <div class="card-footer text-center">
                                        <button class="btn btn-outline-primary btn-sm" onclick="upgradeToPackage(<?php echo e($package->id); ?>)">
                                            <i class="fas fa-arrow-up"></i> Upgrade
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Packages Available</h5>
                    <p class="text-muted">Please contact support for available subscription options.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Subscription History -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Subscription History</h5>
        </div>
        <div class="card-body">
            <?php if($subscriptionHistory->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Package</th>
                                <th>Status</th>
                                <th>Started</th>
                                <th>Expires</th>
                                <th>Amount Paid</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $subscriptionHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($subscription->subscriptionPackage->package_name); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($subscription->status === 'active' ? 'success' : ($subscription->status === 'trial' ? 'warning' : 'secondary')); ?>">
                                            <?php echo e($subscription->status_display); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($subscription->starts_at->format('M d, Y')); ?></td>
                                    <td><?php echo e($subscription->expires_at->format('M d, Y')); ?></td>
                                    <td><?php echo e($subscription->subscriptionPackage->currency); ?> <?php echo e(number_format($subscription->amount_paid, 2)); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('business.subscription.show', $subscription)); ?>" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php echo e($subscriptionHistory->links()); ?>

            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No subscription history found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Cancel Subscription Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Cancel Subscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="cancelForm">
                    <input type="hidden" id="subscriptionId">
                    <div class="mb-3">
                        <label for="cancellationReason" class="form-label">Reason for Cancellation</label>
                        <textarea class="form-control" id="cancellationReason" rows="3" required placeholder="Please tell us why you're cancelling..."></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Note:</strong> Your subscription will remain active until the end of your current billing period.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Subscription</button>
                <button type="button" class="btn btn-danger" onclick="confirmCancel()">Cancel Subscription</button>
            </div>
        </div>
    </div>
</div>

<script>
function cancelSubscription(subscriptionId) {
    document.getElementById('subscriptionId').value = subscriptionId;
    const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
    cancelModal.show();
}

function confirmCancel() {
    const subscriptionId = document.getElementById('subscriptionId').value;
    const reason = document.getElementById('cancellationReason').value;
    
    if (!reason.trim()) {
        alert('Please provide a reason for cancellation.');
        return;
    }
    
    fetch(`<?php echo e(url('/business/subscription')); ?>/${subscriptionId}/cancel`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cancellation_reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('An error occurred while cancelling the subscription.');
    });
}

function renewSubscription(subscriptionId) {
    if (confirm('Are you sure you want to renew your subscription for another month?')) {
        fetch(`<?php echo e(url('/business/subscription')); ?>/${subscriptionId}/renew`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred while renewing the subscription.');
        });
    }
}

function upgradeToPackage(packageId) {
    if (confirm('Are you sure you want to upgrade to this package? Your current subscription will be cancelled and replaced with the new one.')) {
        fetch('<?php echo e(url("/business/subscription/upgrade")); ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                package_id: packageId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred while upgrading the subscription.');
        });
    }
}

function upgradeSubscription() {
    // Scroll to available packages section
    document.querySelector('.card:has(.card-header h5:contains("Available Packages"))')?.scrollIntoView({ behavior: 'smooth' });
}

function showAvailablePackages() {
    // Scroll to available packages section
    document.querySelector('.card:has(.card-header h5:contains("Available Packages"))')?.scrollIntoView({ behavior: 'smooth' });
}

function startTrial() {
    // Find the first available package (usually the starter package)
    const firstPackage = document.querySelector('.package-card');
    if (firstPackage) {
        const packageId = firstPackage.dataset.packageId;
        
        fetch('<?php echo e(route("business.subscription.start-trial")); ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                package_id: packageId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('🎉 ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            alert('❌ An error occurred while starting the trial.');
        });
    } else {
        alert('❌ No packages available for trial.');
    }
}

function showPackages() {
    // Scroll to available packages section
    const packagesSection = document.querySelector('#available-packages');
    if (packagesSection) {
        packagesSection.scrollIntoView({ behavior: 'smooth' });
    } else {
        // If packages section doesn't exist, show it
        document.querySelector('.card:has(.card-header h5:contains("Available Packages"))')?.scrollIntoView({ behavior: 'smooth' });
    }
}

function pauseSubscription() {
    const reason = prompt('Please provide a reason for pausing your subscription (optional):');
    
    if (reason === null) {
        return; // User cancelled
    }

    fetch('<?php echo e(route("business.subscription.pause")); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ An error occurred while pausing the subscription');
    });
}

function resumeSubscription() {
    if (confirm('Are you sure you want to resume your subscription?')) {
        fetch('<?php echo e(route("business.subscription.resume")); ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ An error occurred while resuming the subscription');
        });
    }
}
</script>

<style>
.subscription-detail {
    margin-bottom: 8px;
}

.package-card .card {
    transition: transform 0.2s ease;
}

.package-card .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.subscription-actions .btn {
    margin-bottom: 5px;
}

.card-header h5 {
    color: #495057;
    font-weight: 600;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('business.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/business/subscription/index.blade.php ENDPATH**/ ?>