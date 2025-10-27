@extends('super-admin.layouts.app')

@section('title', 'Send Notifications to Businesses')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title">Notifications Management</h2>
                <a href="{{ route('super-admin.notifications.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Send New Notification
                </a>
            </div>
        </div>
    </div>

    <!-- Notification History -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Sent Notifications History</h5>
        </div>
        <div class="card-body">
            @if($notifications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Business</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Sent Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifications as $notification)
                                <tr>
                                    <td>
                                        <strong>{{ $notification->business->business_name }}</strong>
                                        @if($notification->vehicle)
                                            <br><small class="text-muted">{{ $notification->vehicle->vehicle_make }} {{ $notification->vehicle->vehicle_model }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $notification->title }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ ucfirst(str_replace('_', ' ', $notification->category)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $notification->priority === 'high' ? 'danger' : ($notification->priority === 'medium' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($notification->priority) }}
                                        </span>
                                    </td>
                                    <td>{{ $notification->due_date->format('M d, Y H:i') }}</td>
                                    <td>
                                        @if($notification->is_completed)
                                            <span class="badge bg-success">Completed</span>
                                        @else
                                            <span class="badge bg-{{ $notification->is_active ? 'primary' : 'secondary' }}">
                                                {{ $notification->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $notification->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" onclick="deleteNotification({{ $notification->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $notifications->links() }}
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No notifications sent yet</h5>
                    <p class="text-muted">Start sending notifications to businesses using the button above.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteNotification(id) {
    if (confirm('Are you sure you want to delete this notification?')) {
        const form = document.getElementById('deleteForm');
        form.action = `{{ url('/super-admin/notifications') }}/${id}`;
        form.submit();
    }
}
</script>
@endsection

