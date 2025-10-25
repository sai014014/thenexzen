@extends('business.layouts.app')

@section('title', 'Activity Log')

@section('content')
<div class="main-content1">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1">Activity Log</h2>
                        <p class="text-muted mb-0">Track all user activities and system changes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="{{ route('business.activity-log.index') }}" id="filterForm">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="Search activities...">
                                </div>
                                <div class="col-md-2">
                                    <label for="action" class="form-label">Action</label>
                                    <select class="form-select" id="action" name="action">
                                        <option value="">All Actions</option>
                                        @foreach($actions as $action)
                                            <option value="{{ $action['value'] }}" {{ request('action') == $action['value'] ? 'selected' : '' }}>
                                                {{ $action['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="user_id" class="form-label">User</label>
                                    <select class="form-select" id="user_id" name="user_id">
                                        <option value="">All Users</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="date_from" class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" 
                                           value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="date_to" class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" 
                                           value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Log Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Activity Log
                            <span class="badge bg-primary ms-2">{{ $activityLogs->total() }} records</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if($activityLogs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Action</th>
                                            <th>Description</th>
                                            <th>User</th>
                                            <th>Date & Time</th>
                                            <th>IP Address</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activityLogs as $log)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="{{ $log->action_icon }} me-2"></i>
                                                    <span class="fw-medium">{{ $log->formatted_action }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $log->description }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $log->user_name }}</div>
                                                        <small class="text-muted">{{ $log->user->email ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-medium">{{ $log->created_at->format('M d, Y') }}</div>
                                                    <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $log->ip_address ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('business.activity-log.show', $log->id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                                <div class="text-muted">
                                    Showing {{ $activityLogs->firstItem() }} to {{ $activityLogs->lastItem() }} 
                                    of {{ $activityLogs->total() }} results
                                </div>
                                <div>
                                    {{ $activityLogs->appends(request()->query())->links() }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Activity Found</h5>
                                <p class="text-muted">No activities match your current filters.</p>
                                <a href="{{ route('business.activity-log.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-refresh me-2"></i>Clear Filters
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const filterForm = document.getElementById('filterForm');
    const filterInputs = filterForm.querySelectorAll('select, input[type="date"]');
    
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            filterForm.submit();
        });
    });
    
    // Debounced search
    const searchInput = document.getElementById('search');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterForm.submit();
        }, 500);
    });
});
</script>
@endpush
