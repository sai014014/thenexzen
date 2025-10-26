@extends('business.layouts.app')

@section('title', 'Customer Management - ' . $business->business_name)
@section('page-title', 'Customer Management')

@section('content')
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
                        <!-- Add button moved to header -->
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Add Customer Section -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="customerSearch" class="form-control" placeholder="Search customers...">
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('business.customers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>ADD NEW CUSTOMER
                        </a>
                    </div>
                </div>

                <div class="record-count">{{ $customers->total() }} Records Found, Page {{ $customers->currentPage() }} of {{ $customers->lastPage() }}</div>

                <!-- Customer List Table -->
                <div class="table-responsive">
                    <table id="customerTable" class="table table-hover">
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
                        <tbody id="customerTableBody">
                            @forelse($customers as $customer)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#{{ str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    @if($customer->customer_type === 'individual')
                                        <span class="badge bg-info">
                                            <i class="fas fa-user me-1"></i>Individual
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-building me-1"></i>Corporate
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $customer->display_name }}</strong>
                                        @if($customer->customer_type === 'corporate' && $customer->contact_person_name)
                                            <br><small class="text-muted">Contact: {{ $customer->contact_person_name }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <i class="fas fa-phone me-1"></i>{{ $customer->primary_contact }}
                                        @if($customer->primary_email)
                                            <br><small class="text-muted"><i class="fas fa-envelope me-1"></i>{{ $customer->primary_email }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="form-check form-switch me-2">
                                            <input class="form-check-input status-toggle" type="checkbox" 
                                                   id="statusToggle{{ $customer->id }}" 
                                                   data-customer-id="{{ $customer->id }}"
                                                   {{ $customer->status === 'active' ? 'checked' : '' }}>
                                        </div>
                                        <div class="status-badge">
                                            @switch($customer->status)
                                                @case('active')
                                                    <span class="badge bg-success" id="statusBadge{{ $customer->id }}">Active</span>
                                                    @break
                                                @case('inactive')
                                                    <span class="badge bg-danger" id="statusBadge{{ $customer->id }}">Inactive</span>
                                                    @break
                                                @case('pending')
                                                    <span class="badge bg-warning" id="statusBadge{{ $customer->id }}">Pending</span>
                                                    @break
                                            @endswitch
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($customer->license_expiry_date)
                                        @switch($customer->license_status)
                                            @case('valid')
                                                <span class="badge bg-success">Valid</span>
                                                @break
                                            @case('near_expiry')
                                                <span class="badge bg-warning">Near Expiry</span>
                                                @break
                                            @case('expired')
                                                <span class="badge bg-danger">Expired</span>
                                                @break
                                        @endswitch
                                        <br><small class="text-muted">{{ $customer->license_expiry_date->format('M d, Y') }}</small>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('business.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('business.customers.edit', $customer) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $customer->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <p>No customers found</p>
                                        <a href="{{ route('business.customers.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Add First Customer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($customers->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} results
                    </div>
                    <div>
                        {{ $customers->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
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
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Customer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Search functionality
document.getElementById('customerSearch').addEventListener('input', function() {
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
    const search = document.getElementById('customerSearch').value;
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
    document.getElementById('customerSearch').value = '';
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
@endpush
