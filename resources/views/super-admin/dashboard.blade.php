@extends('super-admin.layouts.app')

@section('title', 'Dashboard - The NexZen Super Admin')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Businesses</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_businesses'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Active Businesses</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_businesses'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Bugs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_bugs'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bug fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Critical Bugs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['critical_bugs'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Subscriptions -->
@if($pending_subscriptions->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-clock me-2"></i>Pending Subscription Approvals
                </h6>
                <span class="badge bg-warning">{{ $pending_subscriptions->count() }} Pending</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Business</th>
                                <th>Package</th>
                                <th>Requested</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pending_subscriptions as $subscription)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-building"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <strong>{{ $subscription->business->business_name }}</strong><br>
                                            <small class="text-muted">{{ $subscription->business->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $subscription->subscriptionPackage->package_name }}</span><br>
                                    <small class="text-muted">{{ $subscription->subscriptionPackage->subscription_fee }} {{ $subscription->subscriptionPackage->currency }}</small>
                                </td>
                                <td>
                                    {{ $subscription->created_at->format('M d, Y') }}<br>
                                    <small class="text-muted">{{ $subscription->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <strong>{{ $subscription->subscriptionPackage->subscription_fee }} {{ $subscription->subscriptionPackage->currency }}</strong>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-success btn-sm" onclick="approveSubscription({{ $subscription->id }})">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="rejectSubscription({{ $subscription->id }})">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                        <a href="{{ route('super-admin.businesses.show', $subscription->business) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Recent Activities -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Business Activities</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Business Name</th>
                                <th>Client ID</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($recent_businesses) && $recent_businesses->count() > 0)
                                @foreach($recent_businesses as $business)
                                <tr>
                                    <td>{{ $business->business_name }}</td>
                                    <td><span class="badge bg-secondary font-monospace">{{ $business->client_id }}</span></td>
                                    <td>{{ ucfirst($business->business_type) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $business->status == 'active' ? 'success' : ($business->status == 'inactive' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($business->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $business->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-building fa-2x mb-2"></i>
                                        <p class="mb-0">No businesses found</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('super-admin.businesses.index') }}" class="btn btn-primary">
                        <i class="fas fa-building me-2"></i>
                        Manage Businesses
                    </a>
                    <a href="{{ route('super-admin.bugs.index') }}" class="btn btn-warning">
                        <i class="fas fa-bug me-2"></i>
                        View Bug Reports
                    </a>
                    <button class="btn btn-info" onclick="exportData()">
                        <i class="fas fa-download me-2"></i>
                        Export Data
                    </button>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">System Status</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Database</span>
                        <span class="badge badge-success">Online</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>API Services</span>
                        <span class="badge badge-success">Online</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Email Service</span>
                        <span class="badge badge-success">Online</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportData() {
    // Simple export functionality
    alert('Export functionality will be implemented here');
}

// Subscription approval functions
function approveSubscription(subscriptionId) {
    if (confirm('Are you sure you want to approve this subscription?')) {
        fetch(`{{ route('super-admin.subscription-packages.approve-subscription') }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                subscription_id: subscriptionId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Subscription approved successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while approving the subscription');
        });
    }
}

function rejectSubscription(subscriptionId) {
    const reason = prompt('Please provide a reason for rejection:');
    if (reason && reason.trim()) {
        fetch(`{{ route('super-admin.subscription-packages.reject-subscription') }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                subscription_id: subscriptionId,
                reason: reason
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Subscription rejected successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while rejecting the subscription');
        });
    }
}
</script>
@endpush