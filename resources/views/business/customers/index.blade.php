@extends('business.layouts.app')

@section('title', 'Customer Management - ' . $business->business_name)
@section('page-title', 'Customer Management')

@section('content')
<div class="content-wrapper">
                <!-- Search, Filters and Add Customer Section -->
                <div class="row mb-3 align-items-end filter-row">
                    <div class="col-md-4">
                        <div class="customer-search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="customerSearch" class="form-control search-input" placeholder="Search customers...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select id="customerTypeFilter" class="form-select" data-title="Customer Type">
                            <option value="">Customer Type</option>
                            <option value="individual">Individual</option>
                            <option value="corporate">Corporate</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="statusFilter" class="form-select" data-title="Status">
                            <option value="">Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2 text-end">
                        <a href="{{ route('business.customers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>ADD NEW CUSTOMER
                        </a>
                    </div>
                </div>

<!-- Customer List Table -->
<div class="table-responsive">
    <table id="customerTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th class="customer_title">Customer</th>
                <th>Type</th>
                <th>Contact</th>
                <th>Registration Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="customerTableBody">
            @forelse($customers as $customer)
            <tr>
                <td class="customer_title">
                    <div class="d-flex align-items-center">
                        @php
                            $colors = ['#6B6ADE', '#FF6B6B', '#4ECDC4', '#FFE66D', '#FF8C94', '#95E1D3', '#F38181', '#AA96DA', '#FCBAD3', '#A8E6CF'];
                            $colorIndex = $customer->id % count($colors);
                            $customerColor = $colors[$colorIndex];
                        @endphp
                        <div class="brand-icon-placeholder me-2 d-flex align-items-center justify-content-center customer-color-badge" 
                             style="width: 30px; height: 30px; background: {{ $customerColor }}; color: white; border-radius: 4px; font-size: 12px; font-weight: bold;">
                            {{ strtoupper(substr($customer->display_name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ $customer->display_name }}</div>
                        </div>
                    </div>
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
                        {{ $customer->primary_contact }}
                    </div>
                </td>
                <td>{{ $customer->created_at->format('M d, Y') }}</td>
                <td>
                    @php
                        $displayStatus = $customer->status;
                        $badgeClass = $customer->status === 'active' ? 'success' : ($customer->status === 'inactive' ? 'secondary' : 'warning');
                    @endphp
                    <div>
                        <span class="badge rounded-pill bg-{{ $badgeClass }} text-white fw-bold px-2 py-1">
                            {{ ucfirst($displayStatus) }}
                        </span>
                    </div>
                </td>
                
                <td>
                    <a href="{{ route('business.customers.show', $customer) }}" class="text-primary text-decoration-none" style="font-weight: 500;">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No customers found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
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
// Live search and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const customerTypeFilter = document.getElementById('customerTypeFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    const searchInput = document.getElementById('customerSearch');
    
    // Get table rows
    const rows = document.querySelectorAll('#customerTableBody tr');

    function applyFilters() {
        const customerType = customerTypeFilter.value.toLowerCase();
        const status = statusFilter.value.toLowerCase();
        
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

        rows.forEach(row => {
            let showRow = true;
            const text = row.textContent.toLowerCase();

            // Apply search filter
            if (searchTerm && !text.includes(searchTerm)) {
                showRow = false;
            }

            // Apply customer type filter
            if (customerType && showRow) {
                if (customerType === 'individual' && !text.includes('individual')) {
                    showRow = false;
                } else if (customerType === 'corporate' && !text.includes('corporate')) {
                    showRow = false;
                }
            }

            // Apply status filter
            if (status && showRow) {
                if (status === 'active' && !text.includes('active')) {
                    showRow = false;
                } else if (status === 'inactive' && !text.includes('inactive')) {
                    showRow = false;
                }
            }

            

            if (showRow) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Live filters - apply automatically on change
    customerTypeFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    
    
    // Add search input listener
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
});

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
