@extends('business.layouts.app')

@section('title', 'Subscription Management')
@section('page-title', 'My Subscription')

@section('content')
<div class="container-fluid">
    <!-- No Subscription / Trial Ended Message -->
    @if(!$currentSubscription)
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            @if(!$hasSubscriptionHistory)
                                Welcome! Start Your Free Trial
                            @else
                                Subscription Required
                            @endif
                        </h5>
                    </div>
                    <div class="card-body text-center py-5">
                        @if(!$hasSubscriptionHistory)
                            <i class="fas fa-rocket fa-4x text-primary mb-4"></i>
                            <h4 class="text-primary mb-3">Get Started with Your Free Trial</h4>
                            <p class="text-muted mb-4">
                                Experience the full power of our vehicle management system with a free trial. 
                                No credit card required!
                            </p>
                        @else
                            <i class="fas fa-lock fa-4x text-warning mb-4"></i>
                            <h4 class="text-warning mb-3">Subscription Required</h4>
                            <p class="text-muted mb-4">
                                Your trial period has ended. Please choose a subscription plan to continue using our services.
                            </p>
                        @endif
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            @if(!$hasSubscriptionHistory)
                                <button class="btn btn-primary btn-lg me-md-2" onclick="startTrial()">
                                    <i class="fas fa-play me-2"></i>Start Free Trial
                                </button>
                            @endif
                            <button class="btn btn-outline-primary btn-lg" onclick="showPackages()">
                                <i class="fas fa-list me-2"></i>View All Packages
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Available Packages -->
    <div class="pricing-section" id="available-packages">
        <div class="section-title text-center mb-5">
            <h2 class="pricing-title">Available Packages</h2>
            <span class="pricing-subtitle">Simple, scalable pricing for your business needs.</span>
        </div>

        @if($availablePackages->count() > 0)
            <div class="row advanced-pricing-table">
                @foreach($availablePackages as $index => $package)
                    @php
                        $isCurrentPackage = $currentSubscription && $currentSubscription->subscription_package_id == $package->id;
                    @endphp
                    <div class="col-lg-4 mb-4">
                        <div class="pricing-table style-two {{ $index == 1 ? 'featured' : '' }} price-two {{ $isCurrentPackage ? 'current-package' : '' }}" data-package-id="{{ $package->id }}">
                            @if($index == 1 && !$isCurrentPackage)
                                <span class="offer-tag">
                                    <span class="tag">Popular</span>
                                </span>
                            @endif

                            <div class="pricing-header pricing-amount">
                                <h3 class="price-title">
                                    {{ $package->package_name }}
                                    @if($isCurrentPackage)
                                        <span class="current-text">(Current)</span>
                                    @endif
                                </h3>
                                <p>{{ $package->description ?? 'For creating impressive tools that generate results.' }}</p>

                                <div class="monthly_price">
                                    <h2 class="price">{{ $package->formatted_price }}</h2>
                                    <span class="monthly">Per month</span>
                                </div>
                            </div>

                            <div class="trail_btn">
                                @if($isCurrentPackage)
                                    <a href="#" class="pix-btn btn-outline-two" style="opacity: 0.5; cursor: not-allowed; pointer-events: none;">
                                        Current Plan
                                    </a>
                                @elseif(!$hasSubscriptionHistory)
                                    <a href="#" class="pix-btn btn-outline-two" onclick="startTrialForPackage({{ $package->id }}); return false;">
                                        Start a free trial
                                    </a>
                                @else
                                    <a href="#" class="pix-btn btn-outline-two" onclick="upgradeToPackage({{ $package->id }}); return false;">
                                        Upgrade Now
                                    </a>
                                @endif
                                @if(!$isCurrentPackage)
                                    <span>No credit card required</span>
                                @endif
                            </div>

                            <p class="key-features">Key features:</p>
                            <ul class="price-feture">
                                <li class="have">{{ $package->vehicle_capacity_display }} vehicles</li>
                                <li class="have">{{ ucfirst($package->support_type) }} support</li>
                                <li class="have">{{ $package->trial_period_days }} days trial</li>
                                @if($package->booking_limit)
                                    <li class="have">{{ $package->booking_limit }} bookings/month</li>
                                @endif
                                @if($package->user_limit)
                                    <li class="have">{{ $package->user_limit }} team members</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">No Packages Available</h5>
                <p class="text-muted">Please contact support for available subscription options.</p>
            </div>
        @endif
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

<!-- Trial Package Selection Modal -->
<div class="modal fade" id="trialPackageModal" tabindex="-1" aria-labelledby="trialPackageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="trialPackageModalLabel">
                    <i class="fas fa-rocket me-2"></i>Start Your Free Trial
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-gift fa-3x text-success mb-3"></i>
                    <h4 class="text-success">Choose Your Trial Package</h4>
                    <p class="text-muted">Select a package to start your free trial. No credit card required!</p>
                </div>
                
                @if($availablePackages->count() > 0)
                    <div class="row">
                        @foreach($availablePackages as $package)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-success">
                                    <div class="card-header bg-light text-center">
                                        <h6 class="mb-0 text-success">{{ $package->package_name }}</h6>
                                        <h4 class="text-primary mt-2">{{ $package->formatted_price }}/month</h4>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted small">{{ $package->description }}</p>
                                        <ul class="list-unstyled small">
                                            <li><i class="fas fa-check text-success me-2"></i>{{ $package->vehicle_capacity_display }} vehicles</li>
                                            <li><i class="fas fa-check text-success me-2"></i>{{ ucfirst($package->support_type) }} support</li>
                                            <li><i class="fas fa-check text-success me-2"></i>{{ $package->trial_period_days }} days trial</li>
                                        </ul>
                                    </div>
                                    <div class="card-footer text-center">
                                        <button class="btn btn-success w-100" onclick="startTrialForPackage({{ $package->id }})">
                                            <i class="fas fa-play me-2"></i>Start {{ $package->trial_period_days }}-Day Trial
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-3"></i>
                        <h5 class="text-warning">No Packages Available</h5>
                        <p class="text-muted">Please contact support for available trial options.</p>
                    </div>
                @endif
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

function startTrial() {
    // Show package selection modal
    const modal = new bootstrap.Modal(document.getElementById('trialPackageModal'));
    modal.show();
}

function startTrialForPackage(packageId) {
    if (confirm('Are you sure you want to start a free trial for this package?')) {
        fetch('{{ route("business.subscription.start-trial") }}', {
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
                alert('🎉 ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ An error occurred while starting the trial');
        });
    }
}

function showPackages() {
    // Scroll to available packages section
    const packagesSection = document.querySelector('#available-packages');
    if (packagesSection) {
        packagesSection.scrollIntoView({ behavior: 'smooth' });
    } else {
        // If packages section doesn't exist, show it
        document.querySelector('.card:has(.card-header h5:contains("Available Packages"))')?.scrollIntoView({ behavior: 'smooth' });
    }
}

function pauseSubscription() {
    const reason = prompt('Please provide a reason for pausing your subscription (optional):');
    
    if (reason === null) {
        return; // User cancelled
    }

    fetch('{{ route("business.subscription.pause") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ An error occurred while pausing the subscription');
    });
}

function resumeSubscription() {
    if (confirm('Are you sure you want to resume your subscription?')) {
        fetch('{{ route("business.subscription.resume") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ An error occurred while resuming the subscription');
        });
    }
}
</script>

@push('styles')
<link rel="stylesheet" href="{{ asset('homePage/assets/css/style.css') }}">
@endpush

<style>
.subscription-detail {
    margin-bottom: 8px;
}

.subscription-actions .btn {
    margin-bottom: 5px;
}

.pricing-section {
    padding: 60px 0;
}

.pricing-title {
    font-size: 36px;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
}

.pricing-subtitle {
    font-size: 18px;
    color: #666;
}

.advanced-pricing-table .pricing-table {
    position: relative;
    background: #fff;
    border-radius: 15px;
    padding: 40px 30px;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
}

.advanced-pricing-table .pricing-table:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 40px rgba(107, 106, 222, 0.15);
}

.advanced-pricing-table .pricing-table.featured {
    background: linear-gradient(135deg, #6B6ADE 0%, #3C3CE1 100%);
    color: #fff;
}

.advanced-pricing-table .pricing-table.featured .price-title,
.advanced-pricing-table .pricing-table.featured p,
.advanced-pricing-table .pricing-table.featured .monthly {
    color: #fff;
}

.price-title {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.current-text {
    font-size: 16px;
    font-weight: 600;
    color: #28a745;
}

.pricing-header p {
    font-size: 16px;
    color: #666;
    margin-bottom: 20px;
}

.monthly_price .price {
    font-size: 42px;
    font-weight: 700;
    color: #6B6ADE;
    margin-bottom: 5px;
}

.monthly_price .monthly {
    font-size: 14px;
    color: #999;
    display: block;
}

.trail_btn {
    text-align: center;
    margin: 30px 0;
}

.pix-btn {
    display: inline-block;
    padding: 12px 30px;
    background: #fff;
    color: #6B6ADE;
    border: 2px solid #6B6ADE;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.pix-btn:hover {
    background: #6B6ADE;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(107, 106, 222, 0.3);
}

.trail_btn span {
    display: block;
    font-size: 12px;
    color: #999;
    margin-top: 8px;
}

.key-features {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
}

.price-feture {
    list-style: none;
    padding: 0;
    margin: 0;
    text-align: left;
}

.price-feture li {
    padding: 10px 0;
    font-size: 15px;
    color: #333;
    position: relative;
    padding-left: 30px;
}

.price-feture li.have::before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #22c55e;
    font-weight: bold;
}

.price-feture li.not::before {
    content: "✗";
    position: absolute;
    left: 0;
    color: #ef4444;
    font-weight: bold;
}

.offer-tag {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 120px;
    height: 120px;
    overflow: hidden;
    z-index: 10;
}

.offer-tag::before {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 0 120px 120px 0;
    border-color: transparent #13244D transparent transparent;
}

.offer-tag .tag {
    position: absolute;
    top: 25px;
    right: -35px;
    transform: rotate(45deg);
    background: transparent;
    color: #fff;
    padding: 0;
    border-radius: 0;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
    z-index: 11;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.offer-tag .tag-current {
    /* Green ribbon for current package */
    /* Same positioning, color will be set via parent */
}

.current-package .offer-tag::before {
    border-color: transparent #28a745 transparent transparent;
}

.current-package {
    border: 3px solid #28a745;
    box-shadow: 0 4px 20px rgba(40, 167, 69, 0.15);
}

.current-package:hover {
    transform: translateY(-10px);
    box-shadow: 0 6px 30px rgba(40, 167, 69, 0.25);
}
</style>
@endsection
