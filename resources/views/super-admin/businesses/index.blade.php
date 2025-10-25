@extends('super-admin.layouts.app')

@section('title', 'Manage Businesses - The NexZen Super Admin')
@section('page-title', 'Manage Businesses')

@section('styles')
@vite(['resources/css/super-admin-businesses.css'])
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Business Management</h4>
        <p class="text-muted mb-0">Manage all businesses in the system</p>
    </div>
    <a href="{{ route('super-admin.businesses.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Business
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($businesses->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Business</th>
                            <th>Client ID</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Admins</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($businesses as $business)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($business->logo)
                                            <img src="{{ asset('storage/' . $business->logo) }}" alt="{{ $business->business_name }}" class="rounded-circle" width="50" height="50">
                                        @else
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fas fa-building"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $business->business_name }}</h6>
                                        <small class="text-muted">{{ $business->email }}</small>
                                        @if($business->website)
                                            <br><small><a href="{{ $business->website }}" target="_blank" class="text-decoration-none">{{ $business->website }}</a></small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary font-monospace">
                                    {{ $business->client_id }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ ucfirst(str_replace('_', ' ', $business->business_type)) }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <small class="text-muted">{{ $business->city }}, {{ $business->state }}</small>
                                    <br><small class="text-muted">{{ $business->country }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <form class="status-form" data-business-id="{{ $business->id }}" style="display: inline;">
                                        @csrf
                                        <select class="form-select form-select-sm status-select" name="status" style="width: auto; min-width: 100px;">
                                            <option value="active" {{ $business->status === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $business->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="suspended" {{ $business->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary ms-1" style="display: none;" id="status-submit-{{ $business->id }}">Update</button>
                                    </form>
                                    @if($business->is_verified)
                                        <br><small class="text-success ms-2"><i class="fas fa-check-circle"></i> Verified</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $business->businessAdmins->count() }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $business->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('super-admin.businesses.show', $business) }}" class="btn btn-outline-primary btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('super-admin.businesses.edit', $business) }}" class="btn btn-outline-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm" title="Delete" onclick="confirmDelete({{ $business->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $businesses->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-building fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">No businesses found</h4>
                <p class="text-muted">Get started by creating your first business.</p>
                <a href="{{ route('super-admin.businesses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create First Business
                </a>
            </div>
        @endif
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
                    @csrf
                    @method('DELETE')
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

// Status change functionality
document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.status-select');
    
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const form = this.closest('.status-form');
            const businessId = form.dataset.businessId;
            const newStatus = this.value;
            const originalValue = this.dataset.originalValue || this.querySelector('option[selected]').value;
            const csrfToken = form.querySelector('input[name="_token"]').value;
            
            console.log('Status change initiated:', {
                businessId: businessId,
                newStatus: newStatus,
                originalValue: originalValue,
                csrfToken: csrfToken,
                url: `/super-admin/businesses/${businessId}/status`
            });
            
            // Show loading state
            this.disabled = true;
            this.style.opacity = '0.6';
            
            // Make AJAX request
            fetch(`/super-admin/businesses/${businessId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Show success message
                    showAlert('success', data.message);
                    
                    // Update the select with new data
                    this.dataset.originalValue = newStatus;
                } else {
                    // Revert to original value on error
                    this.value = originalValue;
                    showAlert('error', data.message || 'Failed to update status. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error details:', error);
                // Revert to original value on error
                this.value = originalValue;
                showAlert('error', 'An error occurred: ' + error.message);
                
                // Show the submit button as fallback
                const submitBtn = document.getElementById(`status-submit-${businessId}`);
                if (submitBtn) {
                    submitBtn.style.display = 'inline-block';
                }
            })
            .finally(() => {
                // Remove loading state
                this.disabled = false;
                this.style.opacity = '1';
            });
        });
        
        // Store original value
        select.dataset.originalValue = select.value;
    });
    
    // Handle form submission as fallback
    const statusForms = document.querySelectorAll('.status-form');
    statusForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const businessId = this.dataset.businessId;
            const formData = new FormData(this);
            
            // Show loading state
            const select = this.querySelector('.status-select');
            select.disabled = true;
            select.style.opacity = '0.6';
            
            // Submit form via AJAX
            fetch(`/super-admin/businesses/${businessId}/status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    select.dataset.originalValue = formData.get('status');
                } else {
                    showAlert('error', data.message || 'Failed to update status.');
                }
            })
            .catch(error => {
                console.error('Form submission error:', error);
                showAlert('error', 'An error occurred: ' + error.message);
            })
            .finally(() => {
                select.disabled = false;
                select.style.opacity = '1';
            });
        });
    });
});

function showAlert(type, message) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add to page
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endsection
