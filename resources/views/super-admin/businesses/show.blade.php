@extends('super-admin.layouts.app')

@section('title', 'View Business - The NexZen Super Admin')
@section('page-title', 'View Business')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Business Information Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-building me-2"></i>Business Information
                </h5>
                <div>
                    <a href="{{ route('super-admin.businesses.edit', $business) }}" class="btn btn-warning btn-sm me-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('super-admin.businesses.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        @if($business->logo)
                            <img src="{{ $business->logo }}" alt="{{ $business->business_name }}" class="img-fluid rounded" style="max-height: 200px;">
                        @else
                            <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-building fa-4x"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <h4 class="mb-3">{{ $business->business_name }}</h4>
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <strong>Business Type:</strong><br>
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $business->business_type)) }}</span>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <strong>Status:</strong><br>
                                @if($business->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($business->status === 'inactive')
                                    <span class="badge bg-secondary">Inactive</span>
                                @else
                                    <span class="badge bg-danger">Suspended</span>
                                @endif
                                @if($business->is_verified)
                                    <span class="badge bg-primary ms-2">Verified</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($business->description)
                        <div class="mb-3">
                            <strong>Description:</strong><br>
                            <p class="text-muted">{{ $business->description }}</p>
                        </div>
                        @endif
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
                            <a href="mailto:{{ $business->email }}" class="text-decoration-none">{{ $business->email }}</a>
                        </div>
                        <div class="mb-3">
                            <strong>Phone:</strong><br>
                            <a href="tel:{{ $business->phone }}" class="text-decoration-none">{{ $business->phone }}</a>
                        </div>
                        @if($business->website)
                        <div class="mb-3">
                            <strong>Website:</strong><br>
                            <a href="{{ $business->website }}" target="_blank" class="text-decoration-none">{{ $business->website }}</a>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Address:</strong><br>
                            <span class="text-muted">
                                {{ $business->address }}<br>
                                {{ $business->city }}, {{ $business->state }} {{ $business->postal_code }}<br>
                                {{ $business->country }}
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
                            <span class="badge bg-primary">{{ ucfirst($business->subscription_plan) }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Expires:</strong><br>
                            @if($business->subscription_expires_at)
                                <span class="text-muted">{{ $business->subscription_expires_at->format('M d, Y') }}</span>
                            @else
                                <span class="text-muted">No expiration date</span>
                            @endif
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
                    <i class="fas fa-users me-2"></i>Business Admins ({{ $business->businessAdmins->count() }})
                </h5>
            </div>
            <div class="card-body">
                @if($business->businessAdmins->count() > 0)
                    @foreach($business->businessAdmins as $admin)
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $admin->name }}</h6>
                            <small class="text-muted">{{ $admin->email }}</small>
                        </div>
                        <div>
                            <span class="badge bg-{{ $admin->role === 'admin' ? 'success' : ($admin->role === 'manager' ? 'warning' : 'info') }}">
                                {{ ucfirst($admin->role) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-users fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No admins found</p>
                    </div>
                @endif
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
                    <a href="{{ route('super-admin.businesses.edit', $business) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Business
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $business->id }})">
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
</script>
@endsection
