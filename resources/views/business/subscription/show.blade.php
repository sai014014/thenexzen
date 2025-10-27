@extends('business.layouts.app')

@section('title', 'Subscription Details')

@section('content')
<div class="main-contents">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header-subscription">
                    <div class="header-content">
                        <h2 class="page-title">My Subscription</h2>
                        <p class="page-subtitle">View your current subscription details and package information</p>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('business.subscription.index') }}" class="btn btn-upgrade">
                            <i class="fas fa-arrow-up me-2"></i>Upgrade Package
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Overview Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="subscription-card">
                    <div class="card-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h6 class="card-label">Subscription Period</h6>
                    <div class="card-details">
                        <div class="detail-item">
                            <span class="label">Started</span>
                            <span class="value">{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Expires</span>
                            <span class="value">{{ $subscription->expires_at ? $subscription->expires_at->format('M d, Y') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="subscription-card">
                    <div class="card-icon text-success">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h6 class="card-label">Billing Information</h6>
                    <div class="card-details">
                        <div class="detail-item">
                            <span class="label">Monthly Price</span>
                            <span class="value">₹{{ number_format($subscription->subscriptionPackage->price ?? 0) }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Amount Paid</span>
                            <span class="value">₹{{ number_format($subscription->amount_paid ?? 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="subscription-card">
                    <div class="card-icon text-info">
                        <i class="fas fa-tag"></i>
                    </div>
                    <h6 class="card-label">Package Status</h6>
                    <div class="card-details">
                        <div class="detail-item">
                            <span class="label">Package Name</span>
                            <span class="value">{{ $subscription->subscriptionPackage->name ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Status</span>
                            <span class="badge-status status-{{ $subscription->status }}">{{ ucfirst($subscription->status) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Package Features -->
        @if($subscription->subscriptionPackage)
        <div class="row mb-4">
            <div class="col-12">
                <div class="features-section">
                    <h4 class="section-title">Package Features & Access</h4>
                    <div class="features-grid">
                        <div class="features-column">
                            <h6 class="column-title">
                                <i class="fas fa-check-circle me-2"></i>Module Access
                            </h6>
                            <div class="feature-list">
                                @foreach(['vehicles', 'bookings', 'customers', 'vendors', 'reports', 'notifications'] as $module)
                                <div class="feature-item">
                                    <i class="fas fa-{{ $subscription->canAccessModule($module) ? 'check-circle text-success' : 'times-circle text-danger' }}"></i>
                                    <span>{{ ucfirst($module) }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="features-column">
                            <h6 class="column-title">
                                <i class="fas fa-sliders-h me-2"></i>Package Limits
                            </h6>
                            <div class="feature-list">
                                <div class="feature-item">
                                    <i class="fas fa-car text-info"></i>
                                    <div class="feature-detail">
                                        <span>Vehicle Limit</span>
                                        <strong>{{ $subscription->getVehicleLimit() }}</strong>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-calendar text-info"></i>
                                    <div class="feature-detail">
                                        <span>Booking Limit</span>
                                        <strong>{{ $subscription->getBookingLimit() }}</strong>
                                    </div>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-users text-info"></i>
                                    <div class="feature-detail">
                                        <span>User Limit</span>
                                        <strong>{{ $subscription->getUserLimit() }}</strong>
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
                <div class="action-buttons-section">
                    <div class="action-buttons">
                        <a href="{{ route('business.subscription.index') }}" class="btn btn-upgrade">
                            <i class="fas fa-arrow-up me-2"></i>Upgrade Package
                        </a>
                        @if($subscription->is_active && !$subscription->is_paused)
                            <button class="btn btn-warning" onclick="pauseSubscription()">
                                <i class="fas fa-pause me-2"></i>Pause Subscription
                            </button>
                        @elseif($subscription->is_paused)
                            <button class="btn btn-success" onclick="resumeSubscription()">
                                <i class="fas fa-play me-2"></i>Resume Subscription
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription History Section -->
        @if(isset($subscriptionHistory) && $subscriptionHistory->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="subscription-history-section">
                    <h4 class="section-title">
                        <i class="fas fa-history me-2"></i>Subscription History
                    </h4>
                    <div class="history-table-wrapper">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Status</th>
                                    <th>Started</th>
                                    <th>Expires</th>
                                    <th>Amount Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptionHistory as $histSubscription)
                                <tr>
                                    <td>{{ $histSubscription->subscriptionPackage->package_name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge-status status-{{ $histSubscription->status }}">
                                            {{ ucfirst($histSubscription->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $histSubscription->starts_at ? $histSubscription->starts_at->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $histSubscription->expires_at ? $histSubscription->expires_at->format('M d, Y') : 'N/A' }}</td>
                                    <td>₹{{ number_format($histSubscription->amount_paid ?? 0, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.page-header-subscription {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
}

.header-content .page-title {
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
}

.header-content .page-subtitle {
    font-size: 14px;
    color: #666;
    margin: 0;
}

.btn-upgrade {
    background: linear-gradient(135deg, #6B6ADE 0%, #3C3CE1 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-upgrade:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(107, 106, 222, 0.3);
    color: white;
}

.subscription-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
}

.subscription-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

.card-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #6B6ADE 0%, #3C3CE1 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
}

.card-icon i {
    font-size: 24px;
    color: white;
}

.card-icon.text-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.card-icon.text-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
}

.card-label {
    font-size: 14px;
    font-weight: 600;
    color: #666;
    margin-bottom: 16px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.card-details {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 12px;
    border-bottom: 1px solid #f0f0f0;
}

.detail-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.detail-item .label {
    font-size: 13px;
    color: #999;
}

.detail-item .value {
    font-size: 15px;
    font-weight: 600;
    color: #333;
}

.badge-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-active { background: #d4edda; color: #155724; }
.status-trial { background: #fff3cd; color: #856404; }
.status-paused { background: #ffeaa7; color: #998900; }
.status-cancelled { background: #f8d7da; color: #721c24; }

.features-section {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #333;
    margin-bottom: 24px;
}

.features-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
}

.column-title {
    font-size: 16px;
    font-weight: 600;
    color: #6B6ADE;
    margin-bottom: 20px;
}

.column-title i {
    font-size: 18px;
}

.feature-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.feature-item:hover {
    background: #f0f0f0;
}

.feature-detail {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.feature-detail span {
    font-size: 14px;
    color: #666;
}

.feature-detail strong {
    font-size: 14px;
    color: #333;
    font-weight: 600;
}

.action-buttons-section {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.action-buttons {
    display: flex;
    gap: 16px;
    justify-content: center;
}

.btn-warning {
    background: #ffc107;
    color: #333;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    transition: all 0.3s ease;
}

.btn-warning:hover {
    background: #ffca2c;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
}

.btn-success {
    background: #28a745;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background: #34ce57;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.subscription-history-section {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.history-table-wrapper {
    overflow-x: auto;
}

.history-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.history-table thead {
    background: #f8f9fa;
}

.history-table th {
    padding: 12px 15px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.history-table td {
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
    font-size: 14px;
    color: #333;
}

.history-table tbody tr:last-child td {
    border-bottom: none;
}

.history-table tbody tr:hover {
    background: #f8f9fa;
}

@media (max-width: 768px) {
    .page-header-subscription {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>
@endpush

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