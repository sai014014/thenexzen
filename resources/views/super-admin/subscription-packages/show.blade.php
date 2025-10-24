@extends('super-admin.layouts.app')

@section('title', 'Subscription Package Details')
@section('page-title', 'Package Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Package Overview -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Package Overview</h5>
                        <div class="btn-group">
                            <a href="{{ route('super-admin.subscription-packages.edit', $subscriptionPackage) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit Package
                            </a>
                            <a href="{{ route('super-admin.subscription-packages.index') }}" class="btn btn-outline-secondary btn-sm">
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
                            <h2 class="text-center mb-3">{{ $subscriptionPackage->package_name }}</h2>
                            <div class="text-center mb-3">
                                <h1 class="text-primary">{{ $subscriptionPackage->formatted_price }}</h1>
                                <small class="text-muted">per month</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="package-details">
                                <div class="detail-item mb-3">
                                    <strong>Trial Period:</strong> {{ $subscriptionPackage->trial_period_days }} days
                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Onboarding Fee:</strong> {{ $subscriptionPackage->formatted_onboarding_fee }}
                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Vehicle Capacity:</strong> 
                                    <span class="badge bg-info">{{ $subscriptionPackage->vehicle_capacity_display }}</span>
                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Status:</strong> 
                                    <span class="badge bg-{{ $subscriptionPackage->status === 'active' ? 'success' : ($subscriptionPackage->status === 'inactive' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($subscriptionPackage->status) }}
                                    </span>
                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Support Type:</strong> {{ $subscriptionPackage->support_type_display }}
                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Renewal Type:</strong> {{ $subscriptionPackage->renewal_type_display }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($subscriptionPackage->description)
                        <div class="mt-4">
                            <h6>Description:</h6>
                            <p class="text-muted">{{ $subscriptionPackage->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Features & Functionalities -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Features & Functionalities</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Core Features:</h6>
                            <ul class="list-unstyled">
                                @if($subscriptionPackage->booking_management)
                                    <li><i class="fas fa-check text-success me-2"></i>Booking Management</li>
                                @endif
                                @if($subscriptionPackage->customer_management)
                                    <li><i class="fas fa-check text-success me-2"></i>Customer Management</li>
                                @endif
                                @if($subscriptionPackage->vehicle_management)
                                    <li><i class="fas fa-check text-success me-2"></i>Vehicle Management</li>
                                @endif
                                @if($subscriptionPackage->basic_reporting)
                                    <li><i class="fas fa-check text-success me-2"></i>Basic Reporting Features</li>
                                @endif
                                @if($subscriptionPackage->maintenance_reminders)
                                    <li><i class="fas fa-check text-success me-2"></i>Vehicle Maintenance Reminders</li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Advanced Features:</h6>
                            <ul class="list-unstyled">
                                @if($subscriptionPackage->advanced_reporting)
                                    <li><i class="fas fa-check text-success me-2"></i>Advanced Reporting & Analytics</li>
                                @endif
                                @if($subscriptionPackage->vendor_management)
                                    <li><i class="fas fa-check text-success me-2"></i>Vendor Management</li>
                                @endif
                                @if($subscriptionPackage->customization_options)
                                    <li><i class="fas fa-check text-success me-2"></i>Customization Options</li>
                                @endif
                                @if($subscriptionPackage->multi_user_access)
                                    <li><i class="fas fa-check text-success me-2"></i>Multi-User Access & Role-Based Permissions</li>
                                @endif
                                @if($subscriptionPackage->dedicated_account_manager)
                                    <li><i class="fas fa-check text-success me-2"></i>Dedicated Account Manager</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Subscription Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Billing Cycles:</h6>
                            <div class="billing-cycles">
                                @foreach($subscriptionPackage->billing_cycles as $cycle)
                                    <span class="badge bg-primary me-2">{{ ucfirst($cycle) }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Methods:</h6>
                            <div class="payment-methods">
                                @foreach($subscriptionPackage->payment_methods as $method)
                                    <span class="badge bg-success me-2">{{ ucwords(str_replace('_', ' ', $method)) }}</span>
                                @endforeach
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="availability-item mb-3">
                                <strong>Show on Website:</strong> 
                                <span class="badge bg-{{ $subscriptionPackage->show_on_website ? 'success' : 'secondary' }}">
                                    {{ $subscriptionPackage->show_on_website ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="availability-item mb-3">
                                <strong>Internal Use Only:</strong> 
                                <span class="badge bg-{{ $subscriptionPackage->internal_use_only ? 'warning' : 'secondary' }}">
                                    {{ $subscriptionPackage->internal_use_only ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    @if($subscriptionPackage->features_summary)
                        <div class="mt-3">
                            <h6>Features Summary:</h6>
                            <p class="text-muted">{{ $subscriptionPackage->features_summary }}</p>
                        </div>
                    @endif
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
                        <a href="{{ route('super-admin.subscription-packages.edit', $subscriptionPackage) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Package
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Delete Package
                        </button>
                        <a href="{{ route('super-admin.subscription-packages.index') }}" class="btn btn-outline-secondary">
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
                            <strong>{{ $subscriptionPackage->created_at->format('M d, Y') }}</strong>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Last Updated:</span>
                            <strong>{{ $subscriptionPackage->updated_at->format('M d, Y') }}</strong>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total Features:</span>
                            <strong>{{ count($subscriptionPackage->getFeaturesList()) }}</strong>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Billing Options:</span>
                            <strong>{{ count($subscriptionPackage->billing_cycles) }}</strong>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Payment Methods:</span>
                            <strong>{{ count($subscriptionPackage->payment_methods) }}</strong>
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
                        @php
                            $features = $subscriptionPackage->getFeaturesList();
                            $totalFeatures = 11; // Total possible features
                            $percentage = count($features) / $totalFeatures * 100;
                        @endphp
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%">
                                {{ round($percentage) }}%
                            </div>
                        </div>
                        <p class="text-muted small">
                            {{ count($features) }} of {{ $totalFeatures }} features enabled
                        </p>
                    </div>
                </div>
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
                <p>Are you sure you want to delete the subscription package <strong>{{ $subscriptionPackage->package_name }}</strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('super-admin.subscription-packages.destroy', $subscriptionPackage) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
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
@endsection
