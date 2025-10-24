@extends('super-admin.layouts.app')

@section('title', 'Subscription Packages')
@section('page-title', 'Software Packages Management')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">Subscription Packages</h2>
            <p class="text-muted">Manage software subscription packages and pricing</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('super-admin.subscription-packages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Package
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('super-admin.subscription-packages.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Packages</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Search by package name...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort_by" class="form-label">Sort By</label>
                    <select class="form-select" id="sort_by" name="sort_by">
                        <option value="package_name" {{ request('sort_by') == 'package_name' ? 'selected' : '' }}>Package Name</option>
                        <option value="subscription_fee" {{ request('sort_by') == 'subscription_fee' ? 'selected' : '' }}>Subscription Fee</option>
                        <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sort_order" class="form-label">Order</label>
                    <select class="form-select" id="sort_order" name="sort_order">
                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                    <a href="{{ route('super-admin.subscription-packages.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
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
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $package)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="package-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-box"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $package->package_name }}</h6>
                                            <small class="text-muted">{{ $package->vehicle_capacity_display }} vehicles</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $package->formatted_price }}</strong>
                                        <small class="text-muted d-block">{{ $package->getBillingCyclesDisplay() }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="features-preview" data-bs-toggle="tooltip" data-bs-placement="top" 
                                         title="{{ implode(', ', $package->getFeaturesList()) }}">
                                        <span class="badge bg-info me-1">{{ count($package->getFeaturesList()) }} Features</span>
                                        <small class="text-muted">Hover to view</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" type="checkbox" 
                                               id="status-{{ $package->id }}" 
                                               data-package-id="{{ $package->id }}"
                                               {{ $package->status === 'active' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status-{{ $package->id }}">
                                            <span class="badge bg-{{ $package->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($package->status) }}
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('super-admin.subscription-packages.show', $package) }}" 
                                           class="btn btn-sm btn-outline-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('super-admin.subscription-packages.edit', $package) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-package" 
                                                data-package-id="{{ $package->id }}" 
                                                data-package-name="{{ $package->package_name }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-box fa-3x mb-3"></i>
                                        <p>No subscription packages found.</p>
                                        <a href="{{ route('super-admin.subscription-packages.create') }}" class="btn btn-primary">
                                            Create First Package
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $packages->links() }}
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
                    @csrf
                    @method('DELETE')
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
            const packageId = this.dataset.packageId;
            const isActive = this.checked;
            
            fetch(`{{ url('/super-admin/subscription-packages') }}/${packageId}/toggle-status`, {
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
                    alert('Error: ' + data.message);
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
            document.getElementById('delete-form').action = `{{ url('/super-admin/subscription-packages') }}/${packageId}`;
            
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
@endsection
