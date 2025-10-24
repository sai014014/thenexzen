@extends('business.layouts.app')

@section('title', 'Subscription Details')

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1">Subscription Details</h2>
                        <p class="text-muted mb-0">View your current subscription package and billing information</p>
                    </div>
                    <div>
                        <a href="{{ route('business.subscription.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Subscription
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Overview -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Subscription Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Package Information</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Package Name:</strong> {{ $subscription->subscriptionPackage->package_name ?? 'N/A' }}</li>
                                    <li><strong>Status:</strong> 
                                        <span class="badge bg-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'trial' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($subscription->status) }}
                                        </span>
                                    </li>
                                    <li><strong>Started:</strong> {{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : 'N/A' }}</li>
                                    <li><strong>Expires:</strong> {{ $subscription->expires_at ? $subscription->expires_at->format('M d, Y') : 'N/A' }}</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Billing Information</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Amount Paid:</strong> {{ $subscription->subscriptionPackage->formatted_price ?? 'N/A' }}</li>
                                    <li><strong>Currency:</strong> {{ $subscription->subscriptionPackage->currency ?? 'INR' }}</li>
                                    <li><strong>Auto Renew:</strong> 
                                        <span class="badge bg-{{ $subscription->auto_renew ? 'success' : 'secondary' }}">
                                            {{ $subscription->auto_renew ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </li>
                                    <li><strong>Trial Ends:</strong> {{ $subscription->trial_ends_at ? $subscription->trial_ends_at->format('M d, Y') : 'N/A' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Package Features -->
        @if($subscription->subscriptionPackage)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Package Features</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Core Features:</h6>
                                <ul class="list-unstyled">
                                    @if(in_array('bookings', $subscription->subscriptionPackage->enabled_modules ?? []))
                                        <li><i class="fas fa-check text-success me-2"></i>Booking Management</li>
                                    @endif
                                    @if(in_array('customers', $subscription->subscriptionPackage->enabled_modules ?? []))
                                        <li><i class="fas fa-check text-success me-2"></i>Customer Management</li>
                                    @endif
                                    @if(in_array('vehicles', $subscription->subscriptionPackage->enabled_modules ?? []))
                                        <li><i class="fas fa-check text-success me-2"></i>Vehicle Management</li>
                                    @endif
                                    @if(in_array('reports', $subscription->subscriptionPackage->enabled_modules ?? []))
                                        <li><i class="fas fa-check text-success me-2"></i>Basic Reporting</li>
                                    @endif
                                    @if(in_array('vendors', $subscription->subscriptionPackage->enabled_modules ?? []))
                                        <li><i class="fas fa-check text-success me-2"></i>Vendor Management</li>
                                    @endif
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Additional Features:</h6>
                                <ul class="list-unstyled">
                                    @if($subscription->subscriptionPackage->maintenance_reminders)
                                        <li><i class="fas fa-check text-success me-2"></i>Maintenance Reminders</li>
                                    @endif
                                    @if(in_array('notifications', $subscription->subscriptionPackage->enabled_modules ?? []))
                                        <li><i class="fas fa-check text-success me-2"></i>Notifications System</li>
                                    @endif
                                    @if(in_array('subscription', $subscription->subscriptionPackage->enabled_modules ?? []))
                                        <li><i class="fas fa-check text-success me-2"></i>Subscription Management</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Module Access -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Module Access</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $availableModules = ['vehicles', 'bookings', 'customers', 'vendors', 'reports', 'notifications', 'subscription'];
                                $enabledModules = $subscription->subscriptionPackage->enabled_modules ?? [];
                            @endphp
                            
                            @foreach($availableModules as $moduleKey => $moduleName)
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-center">
                                        @if(in_array($moduleKey, $enabledModules))
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <span class="text-success">{{ $moduleName }}</span>
                                        @else
                                            <i class="fas fa-times-circle text-muted me-2"></i>
                                            <span class="text-muted">{{ $moduleName }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Subscription Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                @if($subscription->status === 'active')
                                    <button class="btn btn-warning w-100" onclick="cancelSubscription()">
                                        <i class="fas fa-times me-2"></i>Cancel Subscription
                                    </button>
                                @endif
                            </div>
                            <div class="col-md-4">
                                @if($subscription->status === 'active')
                                    <button class="btn btn-primary w-100" onclick="renewSubscription()">
                                        <i class="fas fa-sync me-2"></i>Renew Subscription
                                    </button>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('business.subscription.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-arrow-up me-2"></i>Upgrade Package
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Subscription Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Subscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="cancelForm">
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Reason for Cancellation</label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick="confirmCancel()">Cancel Subscription</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function cancelSubscription() {
    $('#cancelModal').modal('show');
}

function confirmCancel() {
    const reason = document.getElementById('cancellation_reason').value;
    
    if (!reason.trim()) {
        alert('Please provide a reason for cancellation.');
        return;
    }

    fetch('{{ route("business.subscription.cancel", $subscription->id) }}', {
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
        console.error('Error:', error);
        alert('An error occurred while cancelling the subscription.');
    });
}

function renewSubscription() {
    if (confirm('Are you sure you want to renew your subscription?')) {
        fetch('{{ route("business.subscription.renew", $subscription->id) }}', {
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
            console.error('Error:', error);
            alert('An error occurred while renewing the subscription.');
        });
    }
}
</script>
@endpush
