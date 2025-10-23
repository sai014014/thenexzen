

<?php $__env->startSection('title', 'Customer Management - ' . $business->business_name); ?>
<?php $__env->startSection('page-title', 'Customer Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>Customer Management
                        </h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-plus me-2"></i>Add New Customer
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo e(route('business.customers.create', ['type' => 'individual'])); ?>">
                                    <i class="fas fa-user me-2"></i>Individual Customer
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('business.customers.create', ['type' => 'corporate'])); ?>">
                                    <i class="fas fa-building me-2"></i>Corporate Customer
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Search by name, mobile, or GSTIN..." value="<?php echo e(request('search')); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="customerTypeFilter">
                            <option value="">All Types</option>
                            <option value="individual" <?php echo e(request('customer_type') == 'individual' ? 'selected' : ''); ?>>Individual</option>
                            <option value="corporate" <?php echo e(request('customer_type') == 'corporate' ? 'selected' : ''); ?>>Corporate</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                            <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                            <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="licenseStatusFilter">
                            <option value="">All Licenses</option>
                            <option value="valid" <?php echo e(request('license_status') == 'valid' ? 'selected' : ''); ?>>Valid</option>
                            <option value="near_expiry" <?php echo e(request('license_status') == 'near_expiry' ? 'selected' : ''); ?>>Near Expiry</option>
                            <option value="expired" <?php echo e(request('license_status') == 'expired' ? 'selected' : ''); ?>>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                            <i class="fas fa-times me-2"></i>Clear
                        </button>
                    </div>
                </div>

                <!-- Customer List Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Customer ID</th>
                                <th>Type</th>
                                <th>Name / Company</th>
                                <th>Primary Contact</th>
                                <th>Registration Date</th>
                                <th>Status</th>
                                <th>License Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#<?php echo e(str_pad($customer->id, 6, '0', STR_PAD_LEFT)); ?></span>
                                </td>
                                <td>
                                    <?php if($customer->customer_type === 'individual'): ?>
                                        <span class="badge bg-info">
                                            <i class="fas fa-user me-1"></i>Individual
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-building me-1"></i>Corporate
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo e($customer->display_name); ?></strong>
                                        <?php if($customer->customer_type === 'corporate' && $customer->contact_person_name): ?>
                                            <br><small class="text-muted">Contact: <?php echo e($customer->contact_person_name); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <i class="fas fa-phone me-1"></i><?php echo e($customer->primary_contact); ?>

                                        <?php if($customer->primary_email): ?>
                                            <br><small class="text-muted"><i class="fas fa-envelope me-1"></i><?php echo e($customer->primary_email); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?php echo e($customer->created_at->format('M d, Y')); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="form-check form-switch me-2">
                                            <input class="form-check-input status-toggle" type="checkbox" 
                                                   id="statusToggle<?php echo e($customer->id); ?>" 
                                                   data-customer-id="<?php echo e($customer->id); ?>"
                                                   <?php echo e($customer->status === 'active' ? 'checked' : ''); ?>>
                                        </div>
                                        <div class="status-badge">
                                            <?php switch($customer->status):
                                                case ('active'): ?>
                                                    <span class="badge bg-success" id="statusBadge<?php echo e($customer->id); ?>">Active</span>
                                                    <?php break; ?>
                                                <?php case ('inactive'): ?>
                                                    <span class="badge bg-danger" id="statusBadge<?php echo e($customer->id); ?>">Inactive</span>
                                                    <?php break; ?>
                                                <?php case ('pending'): ?>
                                                    <span class="badge bg-warning" id="statusBadge<?php echo e($customer->id); ?>">Pending</span>
                                                    <?php break; ?>
                                            <?php endswitch; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if($customer->license_expiry_date): ?>
                                        <?php switch($customer->license_status):
                                            case ('valid'): ?>
                                                <span class="badge bg-success">Valid</span>
                                                <?php break; ?>
                                            <?php case ('near_expiry'): ?>
                                                <span class="badge bg-warning">Near Expiry</span>
                                                <?php break; ?>
                                            <?php case ('expired'): ?>
                                                <span class="badge bg-danger">Expired</span>
                                                <?php break; ?>
                                        <?php endswitch; ?>
                                        <br><small class="text-muted"><?php echo e($customer->license_expiry_date->format('M d, Y')); ?></small>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('business.customers.show', $customer)); ?>" class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('business.customers.edit', $customer)); ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?php echo e($customer->id); ?>)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <p>No customers found</p>
                                        <a href="<?php echo e(route('business.customers.create')); ?>" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Add First Customer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if($customers->hasPages()): ?>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        Showing <?php echo e($customers->firstItem()); ?> to <?php echo e($customers->lastItem()); ?> of <?php echo e($customers->total()); ?> results
                    </div>
                    <div>
                        <?php echo e($customers->appends(request()->query())->links()); ?>

                    </div>
                </div>
                <?php endif; ?>
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
                <p>Are you sure you want to delete this customer? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This will also delete all associated corporate drivers and documents.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete Customer</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value;
    if (searchTerm.length >= 3 || searchTerm.length === 0) {
        applyFilters();
    }
});

// Filter functionality
document.getElementById('customerTypeFilter').addEventListener('change', applyFilters);
document.getElementById('statusFilter').addEventListener('change', applyFilters);
document.getElementById('licenseStatusFilter').addEventListener('change', applyFilters);

function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const customerType = document.getElementById('customerTypeFilter').value;
    const status = document.getElementById('statusFilter').value;
    const licenseStatus = document.getElementById('licenseStatusFilter').value;
    
    const url = new URL(window.location);
    url.searchParams.set('search', search);
    url.searchParams.set('customer_type', customerType);
    url.searchParams.set('status', status);
    url.searchParams.set('license_status', licenseStatus);
    
    window.location.href = url.toString();
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('customerTypeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('licenseStatusFilter').value = '';
    applyFilters();
}

function confirmDelete(customerId) {
    const form = document.getElementById('deleteForm');
    form.action = `/business/customers/${customerId}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Status toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const statusToggles = document.querySelectorAll('.status-toggle');
    
    statusToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const customerId = this.getAttribute('data-customer-id');
            const isActive = this.checked;
            const newStatus = isActive ? 'active' : 'inactive';
            
            // Show loading state
            const originalChecked = this.checked;
            this.disabled = true;
            
            // Update status via AJAX
            const formData = new FormData();
            formData.append('status', newStatus);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('_method', 'PATCH');
            
            fetch(`/business/customers/${customerId}/status`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    // Try to parse as JSON, fallback to text if it fails
                    return response.text().then(text => {
                        try {
                            const data = JSON.parse(text);
                            throw new Error(data.message || 'Failed to update status');
                        } catch (e) {
                            throw new Error('Server error: ' + response.status + ' - ' + text.substring(0, 100));
                        }
                    });
                }
            })
            .then(data => {
                if (data.success) {
                    // Update the status badge
                    const statusBadge = document.getElementById(`statusBadge${customerId}`);
                    if (statusBadge) {
                        const statusClass = data.status === 'active' ? 'bg-success' : 
                                          data.status === 'inactive' ? 'bg-danger' : 'bg-warning';
                        statusBadge.className = `badge ${statusClass}`;
                        statusBadge.textContent = data.status_label;
                    }
                    
                    // Show success message
                    showAlert('success', data.message);
                } else {
                    throw new Error(data.message || 'Failed to update status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert toggle state
                this.checked = !originalChecked;
                showAlert('danger', error.message || 'Failed to update customer status. Please try again.');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });
});

// Alert function
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('business.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/business/customers/index.blade.php ENDPATH**/ ?>