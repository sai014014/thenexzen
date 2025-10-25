@extends('business.layouts.app')

@section('title', 'Subscription Details')

@section('content')
<div class="main-contents">
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
                        <a href="{{ route('business.subscription.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-up me-2"></i>Upgrade Package
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Overview -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>
                                {{ $subscription->subscriptionPackage->name ?? 'Subscription Details' }}
                            </h5>
                            <span class="badge bg-light text-primary fs-6">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="subscription-info-card text-center p-3 border rounded">
                                    <i class="fas fa-calendar-alt fa-2x text-primary mb-2"></i>
                                    <h6>Subscription Period</h6>
                                    <p class="mb-1"><strong>Started:</strong><br>{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : 'N/A' }}</p>
                                    <p class="mb-0"><strong>Expires:</strong><br>{{ $subscription->expires_at ? $subscription->expires_at->format('M d, Y') : 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="subscription-info-card text-center p-3 border rounded">
                                    <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                                    <h6>Billing</h6>
                                    <p class="mb-1"><strong>Monthly Price:</strong><br>₹{{ number_format($subscription->subscriptionPackage->price ?? 0) }}</p>
                                    <p class="mb-0"><strong>Amount Paid:</strong><br>₹{{ number_format($subscription->amount_paid ?? 0) }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="subscription-info-card text-center p-3 border rounded">
                                    <i class="fas fa-cogs fa-2x text-info mb-2"></i>
                                    <h6>Package Features</h6>
                                    <p class="mb-1"><strong>Vehicle Limit:</strong><br>{{ $subscription->getVehicleLimit() }}</p>
                                    <p class="mb-0"><strong>Support:</strong><br>{{ ucfirst($subscription->subscriptionPackage->support_type ?? 'Standard') }}</p>
                                </div>
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
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-star me-2"></i>Package Features
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Module Access</h6>
                                <div class="row">
                                    @foreach(['vehicles', 'bookings', 'customers', 'vendors', 'reports', 'notifications'] as $module)
                                    <div class="col-6 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-{{ $subscription->canAccessModule($module) ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                                            <span>{{ ucfirst($module) }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Package Limits</h6>
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span><i class="fas fa-car me-2 text-info"></i>Vehicle Limit:</span>
                                            <strong>{{ $subscription->getVehicleLimit() }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span><i class="fas fa-calendar me-2 text-info"></i>Booking Limit:</span>
                                            <strong>{{ $subscription->getBookingLimit() }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span><i class="fas fa-users me-2 text-info"></i>User Limit:</span>
                                            <strong>{{ $subscription->getUserLimit() }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="{{ route('business.subscription.index') }}" class="btn btn-primary me-md-2">
                                <i class="fas fa-arrow-up me-2"></i>Upgrade Package
                            </a>
                            @if($subscription->is_active && !$subscription->is_paused)
                                <button class="btn btn-warning me-md-2" onclick="pauseSubscription()">
                                    <i class="fas fa-pause me-2"></i>Pause Subscription
                                </button>
                            @elseif($subscription->is_paused)
                                <button class="btn btn-success me-md-2" onclick="resumeSubscription()">
                                    <i class="fas fa-play me-2"></i>Resume Subscription
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function pauseSubscription() {
    if (confirm('Are you sure you want to pause your subscription?')) {
        fetch('{{ route("business.subscription.pause", $subscription->id) }}', {
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
            alert('An error occurred while pausing the subscription.');
        });
    }
}

function resumeSubscription() {
    if (confirm('Are you sure you want to resume your subscription?')) {
        fetch('{{ route("business.subscription.resume", $subscription->id) }}', {
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
            alert('An error occurred while resuming the subscription.');
        });
    }
}
</script>
@endpush