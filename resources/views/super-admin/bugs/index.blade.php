@extends('super-admin.layouts.app')

@section('title', 'Bug Tracking')

@push('styles')
    @vite(['resources/css/super-admin-bugs.css'])
@endpush

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="header-content">
            <h1 class="page-title">Bug Tracking</h1>
            <p class="page-subtitle">Manage and track bugs, feature requests, and improvements</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('super-admin.bugs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Add New Bug
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-bug"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Bugs</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon open">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['open'] }}</h3>
                <p>Open</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon in-progress">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['in_progress'] }}</h3>
                <p>In Progress</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon resolved">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['resolved'] }}</h3>
                <p>Resolved</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <form method="GET" action="{{ route('super-admin.bugs.index') }}" class="filters-form">
            <div class="filter-group">
                <label for="search">Search</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" 
                       placeholder="Search by title, description, reporter...">
            </div>
            <div class="filter-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="testing" {{ request('status') == 'testing' ? 'selected' : '' }}>Testing</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    <option value="">All Priorities</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="type">Type</label>
                <select id="type" name="type">
                    <option value="">All Types</option>
                    <option value="bug" {{ request('type') == 'bug' ? 'selected' : '' }}>Bug</option>
                    <option value="feature_request" {{ request('type') == 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                    <option value="improvement" {{ request('type') == 'improvement' ? 'selected' : '' }}>Improvement</option>
                    <option value="task" {{ request('type') == 'task' ? 'selected' : '' }}>Task</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>
                    Filter
                </button>
                <a href="{{ route('super-admin.bugs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Bugs Table -->
    <div class="table-card">
        <div class="table-header">
            <h3>Bugs List</h3>
            <div class="table-actions">
                <span class="results-count">{{ $bugs->total() }} results found</span>
            </div>
        </div>
        
        @if($bugs->count() > 0)
            <div class="table-responsive">
                <table class="bugs-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Reporter</th>
                            <th>Assigned To</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bugs as $bug)
                            <tr>
                                <td class="bug-id">#{{ $bug->id }}</td>
                                <td class="bug-title">
                                    <a href="{{ route('super-admin.bugs.show', $bug) }}" class="title-link">
                                        {{ Str::limit($bug->title, 50) }}
                                    </a>
                                </td>
                                <td>{!! $bug->type_badge !!}</td>
                                <td>{!! $bug->priority_badge !!}</td>
                                <td>
                                    <form method="POST" action="{{ route('super-admin.bugs.update-status', $bug) }}" class="status-form">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="status-select" onchange="updateBugStatus(this)">
                                            <option value="open" {{ $bug->status == 'open' ? 'selected' : '' }}>Open</option>
                                            <option value="in_progress" {{ $bug->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="testing" {{ $bug->status == 'testing' ? 'selected' : '' }}>Testing</option>
                                            <option value="resolved" {{ $bug->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                            <option value="closed" {{ $bug->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                        </select>
                                    </form>
                                </td>
                                <td>{{ $bug->reported_by ?? 'N/A' }}</td>
                                <td>{{ $bug->assigned_to ?? 'Unassigned' }}</td>
                                <td>{{ $bug->created_at->format('M d, Y') }}</td>
                                <td class="actions">
                                    <div class="action-buttons">
                                        <a href="{{ route('super-admin.bugs.show', $bug) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('super-admin.bugs.edit', $bug) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('super-admin.bugs.destroy', $bug) }}" 
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this bug?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $bugs->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-bug"></i>
                </div>
                <h3>No bugs found</h3>
                <p>There are no bugs matching your current filters.</p>
                <a href="{{ route('super-admin.bugs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Add First Bug
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Updating status...</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateBugStatus(selectElement) {
    const form = selectElement.closest('form');
    const originalValue = selectElement.dataset.originalValue || selectElement.value;
    
    // Show loading overlay
    document.getElementById('loadingOverlay').style.display = 'flex';
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: selectElement.value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('success', data.message);
            
            // Update the select element's original value
            selectElement.dataset.originalValue = selectElement.value;
        } else {
            // Revert to original value
            selectElement.value = originalValue;
            showAlert('error', data.message || 'An error occurred while updating status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert to original value
        selectElement.value = originalValue;
        showAlert('error', 'An error occurred while updating status');
    })
    .finally(() => {
        // Hide loading overlay
        document.getElementById('loadingOverlay').style.display = 'none';
    });
}

function showAlert(type, message) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of content
    const content = document.querySelector('.content-wrapper');
    content.insertBefore(alertDiv, content.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Set original values on page load
document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach(select => {
        select.dataset.originalValue = select.value;
    });
});
</script>
@endpush
