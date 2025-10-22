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
                                    <td colspan="4" class="text-center text-muted py-4">
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
</script>
@endpush