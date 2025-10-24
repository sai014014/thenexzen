@extends('business.layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<style>
    .notification-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-left: 4px solid #6B6ADE;
        transition: all 0.3s ease;
    }

    .notification-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }

    .notification-card.overdue {
        border-left-color: #dc3545;
    }

    .notification-card.snoozed {
        border-left-color: #ffc107;
        opacity: 0.8;
    }

    .notification-card.completed {
        border-left-color: #28a745;
        opacity: 0.7;
    }

    .notification-header {
        display: flex;
        justify-content: between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .notification-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
        flex: 1;
    }

    .notification-meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-left: 1rem;
    }

    .priority-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .priority-high {
        background-color: #fed7d7;
        color: #c53030;
    }

    .priority-medium {
        background-color: #fef5e7;
        color: #d69e2e;
    }

    .priority-low {
        background-color: #bee3f8;
        color: #2b6cb0;
    }

    .notification-description {
        color: #4a5568;
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .notification-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        color: #718096;
    }

    .notification-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-snooze {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }

    .btn-snooze:hover {
        background-color: #e0a800;
        border-color: #d39e00;
        color: #212529;
    }

    .btn-complete {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }

    .btn-complete:hover {
        background-color: #218838;
        border-color: #1e7e34;
        color: white;
    }

    .btn-delete {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background-color: #c82333;
        border-color: #bd2130;
        color: white;
    }

    .filter-container {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        margin-bottom: 2rem;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #718096;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #718096;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: #cbd5e0;
    }

    .snooze-modal .modal-body {
        padding: 1.5rem;
    }

    .snooze-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .snooze-option {
        padding: 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .snooze-option:hover {
        border-color: #6B6ADE;
        background-color: #f7fafc;
    }

    .snooze-option.selected {
        border-color: #6B6ADE;
        background-color: #e6f3ff;
    }

    .custom-date-input {
        margin-top: 1rem;
    }

    @media (max-width: 768px) {
        .notification-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .notification-meta {
            margin-left: 0;
            margin-top: 0.5rem;
        }

        .notification-details {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .notification-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .stats-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Statistics Cards -->
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-number">{{ $notifications->where('status', '!=', 'completed')->count() }}</div>
        <div class="stat-label">Active Notifications</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">{{ $notifications->where('status', 'overdue')->count() }}</div>
        <div class="stat-label">Overdue</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">{{ $notifications->where('status', 'snoozed')->count() }}</div>
        <div class="stat-label">Snoozed</div>
    </div>
    <div class="stat-card">
        <div class="stat-number">{{ $notifications->where('status', 'completed')->count() }}</div>
        <div class="stat-label">Completed</div>
    </div>
</div>

<!-- Filter Options -->
<div class="filter-container">
    <form method="GET" action="{{ route('business.notifications.index') }}" id="filterForm">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="category" class="form-label">Category</label>
                <select name="category" id="category" class="form-select">
                    <option value="">All Categories</option>
                    <option value="service_reminder" {{ request('category') == 'service_reminder' ? 'selected' : '' }}>Service Reminder</option>
                    <option value="insurance_renewal" {{ request('category') == 'insurance_renewal' ? 'selected' : '' }}>Insurance Renewal</option>
                    <option value="booking_reminder" {{ request('category') == 'booking_reminder' ? 'selected' : '' }}>Booking Reminder</option>
                    <option value="maintenance" {{ request('category') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="inspection" {{ request('category') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="priority" class="form-label">Priority</label>
                <select name="priority" id="priority" class="form-select">
                    <option value="">All Priorities</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="due_date" class="form-label">Due Date</label>
                <select name="due_date" id="due_date" class="form-select">
                    <option value="">All Dates</option>
                    <option value="overdue" {{ request('due_date') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="today" {{ request('due_date') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="tomorrow" {{ request('due_date') == 'tomorrow' ? 'selected' : '' }}>Tomorrow</option>
                    <option value="this_week" {{ request('due_date') == 'this_week' ? 'selected' : '' }}>This Week</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="snoozed" {{ request('status') == 'snoozed' ? 'selected' : '' }}>Snoozed</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <a href="{{ route('business.notifications.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
            </div>
        </div>
    </form>
</div>

<!-- Notifications List -->
@if($notifications->count() > 0)
    @foreach($notifications as $notification)
        <div class="notification-card {{ $notification->status }}">
            <div class="notification-header">
                <h5 class="notification-title">
                    <i class="{{ $notification->category_icon }} me-2"></i>
                    {{ $notification->title }}
                </h5>
                <div class="notification-meta">
                    <span class="priority-badge priority-{{ $notification->priority }}">
                        <i class="{{ $notification->priority_icon }} me-1"></i>
                        {{ ucfirst($notification->priority) }}
                    </span>
                </div>
            </div>

            <div class="notification-description">
                {{ $notification->description }}
            </div>

            <div class="notification-details">
                <div>
                    <strong>Due:</strong> {{ $notification->due_date->format('M d, Y H:i') }}
                    @if($notification->is_overdue)
                        <span class="text-danger ms-2">
                            <i class="fas fa-exclamation-triangle"></i> Overdue
                        </span>
                    @endif
                </div>
                <div>
                    @if($notification->vehicle)
                        <strong>Vehicle:</strong> {{ $notification->vehicle->vehicle_name }}
                    @endif
                </div>
            </div>

            <div class="notification-actions">
                @if($notification->vehicle)
                    <a href="{{ route('business.vehicles.show', $notification->vehicle) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye me-1"></i> View Details
                    </a>
                @endif

                @if(!$notification->is_completed)
                    <button type="button" class="btn btn-snooze btn-sm" data-bs-toggle="modal" data-bs-target="#snoozeModal" data-notification-id="{{ $notification->id }}">
                        <i class="fas fa-clock me-1"></i> Snooze
                    </button>

                    <button type="button" class="btn btn-complete btn-sm" onclick="markCompleted({{ $notification->id }})">
                        <i class="fas fa-check me-1"></i> Mark Complete
                    </button>
                @endif

                <button type="button" class="btn btn-delete btn-sm" onclick="deleteNotification({{ $notification->id }})">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    @endforeach

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $notifications->appends(request()->query())->links() }}
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-bell-slash"></i>
        <h4>No notifications found</h4>
        <p>There are no notifications matching your current filters.</p>
    </div>
@endif

<!-- Snooze Modal -->
<div class="modal fade snooze-modal" id="snoozeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Snooze Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="snoozeForm">
                    <input type="hidden" id="notificationId" name="notification_id">
                    
                    <div class="snooze-options">
                        <div class="snooze-option" data-period="1_hour">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <div><strong>1 Hour</strong></div>
                        </div>
                        <div class="snooze-option" data-period="1_day">
                            <i class="fas fa-calendar-day fa-2x mb-2"></i>
                            <div><strong>1 Day</strong></div>
                        </div>
                        <div class="snooze-option" data-period="1_week">
                            <i class="fas fa-calendar-week fa-2x mb-2"></i>
                            <div><strong>1 Week</strong></div>
                        </div>
                        <div class="snooze-option" data-period="custom">
                            <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                            <div><strong>Custom</strong></div>
                        </div>
                    </div>

                    <input type="hidden" id="snoozePeriod" name="snooze_period" value="1_hour">

                    <div class="custom-date-input" id="customDateInput" style="display: none;">
                        <label for="customDate" class="form-label">Custom Date & Time</label>
                        <input type="datetime-local" id="customDate" name="custom_date" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="submitSnooze()">Snooze</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit filter form on change
    const filterForm = document.getElementById('filterForm');
    const filterSelects = filterForm.querySelectorAll('select');
    
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            filterForm.submit();
        });
    });

    // Snooze option selection
    const snoozeOptions = document.querySelectorAll('.snooze-option');
    const customDateInput = document.getElementById('customDateInput');
    const snoozePeriodInput = document.getElementById('snoozePeriod');

    snoozeOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            snoozeOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Add selected class to clicked option
            this.classList.add('selected');
            
            // Set the period value
            const period = this.dataset.period;
            snoozePeriodInput.value = period;
            
            // Show/hide custom date input
            if (period === 'custom') {
                customDateInput.style.display = 'block';
            } else {
                customDateInput.style.display = 'none';
            }
        });
    });

    // Set default selection
    snoozeOptions[0].classList.add('selected');
});

function markCompleted(notificationId) {
    if (confirm('Are you sure you want to mark this notification as completed?')) {
        fetch(`{{ url('/business/notifications') }}/${notificationId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                completion_notes: ''
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while marking the notification as completed.');
        });
    }
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification? This action cannot be undone.')) {
        fetch(`{{ url('/business/notifications') }}/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the notification.');
        });
    }
}

function submitSnooze() {
    const form = document.getElementById('snoozeForm');
    const formData = new FormData(form);
    const notificationId = document.getElementById('notificationId').value;
    
    const data = {
        snooze_period: formData.get('snooze_period'),
        custom_date: formData.get('custom_date')
    };

    // Debug: Log the data being sent
    console.log('Snooze data:', data);
    console.log('Notification ID:', notificationId);

    fetch(`{{ url('/business/notifications') }}/${notificationId}/snooze`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            alert('Notification snoozed successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while snoozing the notification. Check console for details.');
    });
}

// Set notification ID when snooze modal is opened
document.getElementById('snoozeModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const notificationId = button.getAttribute('data-notification-id');
    document.getElementById('notificationId').value = notificationId;
});
</script>
@endsection
