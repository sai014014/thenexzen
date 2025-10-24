@extends('business.layouts.app')

@section('title', 'Subscription Management')
@section('page-title', 'My Subscription')

@section('content')
<div class="container-fluid">
    <!-- Current Subscription Card -->
    @if($currentSubscription)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Current Subscription</h5>
                    <span class="badge bg-light text-primary fs-6">{{ $currentSubscription->status_display }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="text-primary">{{ $currentSubscription->subscriptionPackage->package_name }}</h4>
                        <p class="text-muted mb-3">{{ $currentSubscription->subscriptionPackage->description }}</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="subscription-detail">
                                    <strong>Price:</strong> {{ $currentSubscription->subscriptionPackage->formatted_price }}/month
                                </div>
                                <div class="subscription-detail">
                                    <strong>Vehicle Capacity:</strong> {{ $currentSubscription->subscriptionPackage->vehicle_capacity_display }}
                                </div>
                                <div class="subscription-detail">
                                    <strong>Support:</strong> {{ ucfirst($currentSubscription->subscriptionPackage->status) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="subscription-detail">
                                    <strong>Status:</strong> 
                                    <span class="badge bg-{{ $currentSubscription->is_active ? 'success' : ($currentSubscription->is_trial ? 'warning' : 'secondary') }}">
                                        {{ $currentSubscription->status_display }}
                                    </span>
                                </div>
                                <div class="subscription-detail">
                                    <strong>Expires:</strong> {{ $currentSubscription->expires_at->format('M d, Y') }}
                                </div>
                                <div class="subscription-detail">
                                    <strong>Days Remaining:</strong> {{ $currentSubscription->days_remaining }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="subscription-actions">
                            @if($currentSubscription->is_active)
                                <button class="btn btn-outline-danger me-2" onclick="cancelSubscription({{ $currentSubscription->id }})">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button class="btn btn-success" onclick="renewSubscription({{ $currentSubscription->id }})">
                                    <i class="fas fa-sync"></i> Renew
                                </button>
                            @elseif($currentSubscription->is_trial)
                                <button class="btn btn-primary" onclick="upgradeSubscription()">
                                    <i class="fas fa-arrow-up"></i> Upgrade Now
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
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
    @endif

    <!-- Available Packages -->
    @if($availablePackages->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Available Packages</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($availablePackages as $package)
                        <div class="col-md-4 mb-3">
                            <div class="package-card h-100">
                                <div class="card h-100">
                                    <div class="card-header text-center">
                                        <h6 class="mb-0">{{ $package->package_name }}</h6>
                                        <h4 class="text-primary mt-2">{{ $package->formatted_price }}/month</h4>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted small">{{ $package->description }}</p>
                                        <ul class="list-unstyled small">
                                            <li><i class="fas fa-check text-success me-2"></i>{{ $package->vehicle_capacity_display }} vehicles</li>
                                            <li><i class="fas fa-check text-success me-2"></i>{{ ucfirst($package->status) }} support</li>
                                            <li><i class="fas fa-check text-success me-2"></i>{{ $package->trial_period_days }} days trial</li>
                                        </ul>
                                    </div>
                                    <div class="card-footer text-center">
                                        <button class="btn btn-outline-primary btn-sm" onclick="upgradeToPackage({{ $package->id }})">
                                            <i class="fas fa-arrow-up"></i> Upgrade
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Subscription History -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Subscription History</h5>
        </div>
        <div class="card-body">
            @if($subscriptionHistory->count() > 0)
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
                            @foreach($subscriptionHistory as $subscription)
                                <tr>
                                    <td>{{ $subscription->subscriptionPackage->package_name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'trial' ? 'warning' : 'secondary') }}">
                                            {{ $subscription->status_display }}
                                        </span>
                                    </td>
                                    <td>{{ $subscription->starts_at->format('M d, Y') }}</td>
                                    <td>{{ $subscription->expires_at->format('M d, Y') }}</td>
                                    <td>{{ $subscription->subscriptionPackage->currency }} {{ number_format($subscription->amount_paid, 2) }}</td>
                                    <td>
                                        <a href="{{ route('business.subscription.show', $subscription) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $subscriptionHistory->links() }}
            @else
                <div class="text-center py-4">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No subscription history found.</p>
                </div>
            @endif
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
    
    fetch(`{{ url('/business/subscription') }}/${subscriptionId}/cancel`, {
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
        fetch(`{{ url('/business/subscription') }}/${subscriptionId}/renew`, {
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
        fetch('{{ url("/business/subscription/upgrade") }}', {
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
@endsection
