@extends('business.layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<style>
    .notif-row{display:flex;align-items:center;gap:16px;padding:14px 16px;border-bottom:1px solid #eee;background:#fff}
    .notif-row:hover{background:#fafafa}
    .notif-icon{width:40px;height:40px;border-radius:6px;background:#f3f4f6;display:flex;align-items:center;justify-content:center}
    .notif-body{flex:1;min-width:0}
    .notif-title{font-weight:600;color:#212529;margin:0}
    .notif-text{color:#6c757d;margin:2px 0 0 0}
    .notif-time{color:#9aa0a6;font-size:12px;margin-top:4px}
    .notif-actions{display:flex;gap:8px}
    .notif-view{background:#6B6ADE;color:#fff;border:none;border-radius:8px;padding:8px 16px;text-decoration:none}
    .notif-view.muted{background:#6c757d}
    .notif-snooze{background:#fff;color:#495057;border:1px solid #dee2e6;border-radius:8px;padding:8px 16px}
    .notif-list{border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.06)}
    .bg-cat-booking{background:#e9e5ff}
    .bg-cat-insurance{background:#ffe9e9}
    .bg-cat-service{background:#e9f7ff}
    .bg-cat-maintenance{background:#fff4e5}
    .bg-cat-inspection{background:#eaf7f0}
    .filter-container{background:#f8f9fa;padding:1.5rem;border-radius:8px;border:1px solid #e9ecef;margin-bottom:2rem}
    .stats-container{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:2rem}
    .stat-card{background:#fff;padding:1.5rem;border-radius:8px;text-align:center;box-shadow:0 2px 4px rgba(0,0,0,.1)}
    .stat-number{font-size:2rem;font-weight:700;color:#2d3748;margin-bottom:.5rem}
    .stat-label{color:#718096;font-size:.875rem;text-transform:uppercase;letter-spacing:.5px}
    .empty-state{text-align:center;padding:3rem;color:#718096}
    .empty-state i{font-size:4rem;margin-bottom:1rem;color:#cbd5e0}
    .snooze-modal .modal-body{padding:1.5rem}
    .snooze-options{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;margin-bottom:1rem}
    .snooze-option{padding:1rem;border:2px solid #e2e8f0;border-radius:8px;text-align:center;cursor:pointer;transition:all .3s ease}
    .snooze-option:hover{border-color:#6B6ADE;background-color:#f7fafc}
    .snooze-option.selected{border-color:#6B6ADE;background-color:#e6f3ff}
    .custom-date-input{margin-top:1rem}
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
    <div class="notif-list">
    @foreach($notifications as $notification)
        @php
            $cat = $notification->category ?? 'booking_reminder';
            $icon = $notification->category_icon ?? match($cat){
                'booking_reminder' => 'fas fa-bell',
                'insurance_renewal' => 'fas fa-shield-alt',
                'service_reminder' => 'fas fa-tools',
                'maintenance' => 'fas fa-wrench',
                'inspection' => 'fas fa-search',
                default => 'fas fa-bell',
            };
            $bgClass = match($cat){
                'booking_reminder' => 'bg-cat-booking',
                'insurance_renewal' => 'bg-cat-insurance',
                'service_reminder' => 'bg-cat-service',
                'maintenance' => 'bg-cat-maintenance',
                'inspection' => 'bg-cat-inspection',
                default => 'bg-cat-booking',
            };
            $muted = ($notification->is_snoozed || $notification->is_completed);
        @endphp
        <div class="notif-row" id="notification-{{ $notification->id }}">
            <div class="notif-icon {{ $bgClass }}">
                <i class="{{ $icon }} text-muted"></i>
            </div>
            <div class="notif-body">
                <p class="notif-title mb-1">{{ $notification->title }}</p>
                <p class="notif-text mb-0">{{ $notification->description }}</p>
                <div class="notif-time">{{ $notification->created_at->diffForHumans() }}</div>
            </div>
            <div class="notif-actions">
                @php
                    $viewUrl = $notification->vehicle ? route('business.vehicles.show', $notification->vehicle) : route('business.notifications.show', $notification);
                @endphp
                <a href="{{ $viewUrl }}" class="notif-view {{ $muted ? 'muted' : '' }}">View</a>
                @if(!$notification->is_completed && !$notification->is_snoozed)
                    <button type="button" class="notif-snooze" data-bs-toggle="modal" data-bs-target="#snoozeModal" data-notification-id="{{ $notification->id }}">Snooze</button>
                @endif
            </div>
        </div>
    @endforeach
    </div>

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
                // Show success message
                alert('Notification deleted successfully');
                // Remove the notification card from the DOM
                document.getElementById('notification-' + notificationId).remove();
                // If no more notifications, reload to show empty state
                if (document.querySelectorAll('.notification-card').length === 0) {
                    location.reload();
                }
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
