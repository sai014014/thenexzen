

<?php $__env->startSection('title', 'Bug Tracking'); ?>

<?php $__env->startPush('styles'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/super-admin-bugs.css']); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="header-content">
            <h1 class="page-title">Bug Tracking</h1>
            <p class="page-subtitle">Manage and track bugs, feature requests, and improvements</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo e(route('super-admin.bugs.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add New Bug
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-bug"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo e($stats['total']); ?></h3>
                <p>Total Bugs</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon open">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo e($stats['open']); ?></h3>
                <p>Open</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon in-progress">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo e($stats['in_progress']); ?></h3>
                <p>In Progress</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon resolved">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo e($stats['resolved']); ?></h3>
                <p>Resolved</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <form method="GET" action="<?php echo e(route('super-admin.bugs.index')); ?>" class="filters-form">
            <div class="filter-group">
                <label for="search">Search</label>
                <input type="text" id="search" name="search" value="<?php echo e(request('search')); ?>" 
                       placeholder="Search by title, description, reporter...">
            </div>
            <div class="filter-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="open" <?php echo e(request('status') == 'open' ? 'selected' : ''); ?>>Open</option>
                    <option value="in_progress" <?php echo e(request('status') == 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                    <option value="testing" <?php echo e(request('status') == 'testing' ? 'selected' : ''); ?>>Testing</option>
                    <option value="resolved" <?php echo e(request('status') == 'resolved' ? 'selected' : ''); ?>>Resolved</option>
                    <option value="closed" <?php echo e(request('status') == 'closed' ? 'selected' : ''); ?>>Closed</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    <option value="">All Priorities</option>
                    <option value="low" <?php echo e(request('priority') == 'low' ? 'selected' : ''); ?>>Low</option>
                    <option value="medium" <?php echo e(request('priority') == 'medium' ? 'selected' : ''); ?>>Medium</option>
                    <option value="high" <?php echo e(request('priority') == 'high' ? 'selected' : ''); ?>>High</option>
                    <option value="critical" <?php echo e(request('priority') == 'critical' ? 'selected' : ''); ?>>Critical</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="type">Type</label>
                <select id="type" name="type">
                    <option value="">All Types</option>
                    <option value="bug" <?php echo e(request('type') == 'bug' ? 'selected' : ''); ?>>Bug</option>
                    <option value="feature_request" <?php echo e(request('type') == 'feature_request' ? 'selected' : ''); ?>>Feature Request</option>
                    <option value="improvement" <?php echo e(request('type') == 'improvement' ? 'selected' : ''); ?>>Improvement</option>
                    <option value="task" <?php echo e(request('type') == 'task' ? 'selected' : ''); ?>>Task</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>
                    Filter
                </button>
                <a href="<?php echo e(route('super-admin.bugs.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Bugs Table -->
    <div class="table-card">
        <div class="table-header">
            <h3>Bugs List</h3>
            <div class="table-actions">
                <span class="results-count"><?php echo e($bugs->total()); ?> results found</span>
            </div>
        </div>
        
        <?php if($bugs->count() > 0): ?>
            <div class="table-responsive">
                <table class="bugs-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Reporter</th>
                            <th>Assigned To</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $bugs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bug): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="bug-id">#<?php echo e($bug->id); ?></td>
                                <td class="bug-title">
                                    <a href="<?php echo e(route('super-admin.bugs.show', $bug)); ?>" class="title-link">
                                        <?php echo e(Str::limit($bug->title, 50)); ?>

                                    </a>
                                </td>
                                <td><?php echo $bug->type_badge; ?></td>
                                <td><?php echo $bug->priority_badge; ?></td>
                                <td>
                                    <form method="POST" action="<?php echo e(route('super-admin.bugs.update-status', $bug)); ?>" class="status-form">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <select name="status" class="status-select" onchange="updateBugStatus(this)">
                                            <option value="open" <?php echo e($bug->status == 'open' ? 'selected' : ''); ?>>Open</option>
                                            <option value="in_progress" <?php echo e($bug->status == 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                                            <option value="testing" <?php echo e($bug->status == 'testing' ? 'selected' : ''); ?>>Testing</option>
                                            <option value="resolved" <?php echo e($bug->status == 'resolved' ? 'selected' : ''); ?>>Resolved</option>
                                            <option value="closed" <?php echo e($bug->status == 'closed' ? 'selected' : ''); ?>>Closed</option>
                                        </select>
                                    </form>
                                </td>
                                <td><?php echo e($bug->reported_by ?? 'N/A'); ?></td>
                                <td><?php echo e($bug->assigned_to ?? 'Unassigned'); ?></td>
                                <td><?php echo e($bug->created_at->format('M d, Y')); ?></td>
                                <td class="actions">
                                    <div class="action-buttons">
                                        <a href="<?php echo e(route('super-admin.bugs.show', $bug)); ?>" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('super-admin.bugs.edit', $bug)); ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="<?php echo e(route('super-admin.bugs.destroy', $bug)); ?>" 
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this bug?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <?php echo e($bugs->appends(request()->query())->links()); ?>

            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-bug"></i>
                </div>
                <h3>No bugs found</h3>
                <p>There are no bugs matching your current filters.</p>
                <a href="<?php echo e(route('super-admin.bugs.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Add First Bug
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Updating status...</p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function updateBugStatus(selectElement) {
    const form = selectElement.closest('form');
    const originalValue = selectElement.dataset.originalValue || selectElement.value;
    
    // Show loading overlay
    document.getElementById('loadingOverlay').style.display = 'flex';
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: selectElement.value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('success', data.message);
            
            // Update the select element's original value
            selectElement.dataset.originalValue = selectElement.value;
        } else {
            // Revert to original value
            selectElement.value = originalValue;
            showAlert('error', data.message || 'An error occurred while updating status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert to original value
        selectElement.value = originalValue;
        showAlert('error', 'An error occurred while updating status');
    })
    .finally(() => {
        // Hide loading overlay
        document.getElementById('loadingOverlay').style.display = 'none';
    });
}

function showAlert(type, message) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of content
    const content = document.querySelector('.content-wrapper');
    content.insertBefore(alertDiv, content.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Set original values on page load
document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach(select => {
        select.dataset.originalValue = select.value;
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('super-admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/super-admin/bugs/index.blade.php ENDPATH**/ ?>