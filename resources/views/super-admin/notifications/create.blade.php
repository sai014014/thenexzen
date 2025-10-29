@extends('super-admin.layouts.app')

@section('title', 'Send Notification to Businesses')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title">Send Notification to Businesses</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="notificationForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="businesses" class="form-label">Select Businesses <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2 mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllBusinesses()">
                                    <i class="fas fa-check-double"></i> Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearAllBusinesses()">
                                    <i class="fas fa-times"></i> Clear All
                                </button>
                            </div>
                            <select id="businesses" multiple class="form-select" size="10" required>
                                @foreach($businesses as $business)
                                    <option value="{{ $business->id }}">
                                        {{ $business->business_name }} ({{ $business->client_id }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Hold Ctrl (Cmd on Mac) to select multiple businesses</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Notification Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required placeholder="e.g., Vehicle Service Reminder">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Notification Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Enter notification details..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="service_reminder">Service Reminder</option>
                                <option value="insurance_renewal">Insurance Renewal</option>
                                <option value="booking_reminder">Booking Reminder</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="inspection">Inspection</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="">Select Priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="due_date" name="due_date" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('super-admin.notifications.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Send Notification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('notificationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const businesses = Array.from(document.getElementById('businesses').selectedOptions).map(option => option.value);
    const title = document.getElementById('title').value;
    const description = document.getElementById('description').value;
    const category = document.getElementById('category').value;
    const priority = document.getElementById('priority').value;
    const dueDate = document.getElementById('due_date').value;
    
    if (businesses.length === 0) {
        alert('Please select at least one business.');
        return;
    }
    
    fetch('{{ route("super-admin.notifications.bulk-send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            business_ids: businesses,
            title: title,
            description: description,
            category: category,
            priority: priority,
            due_date: dueDate
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Failed to send notification');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            let message = data.message;
            if (data.failed_count > 0 && data.errors && data.errors.length > 0) {
                message += '\n\nErrors:\n' + data.errors.slice(0, 5).join('\n');
                if (data.errors.length > 5) {
                    message += '\n... and ' + (data.errors.length - 5) + ' more errors.';
                }
            }
            alert(message);
            window.location.href = '{{ route("super-admin.notifications.index") }}';
        } else {
            let errorMessage = data.message || 'Failed to send notification';
            if (data.errors && Array.isArray(data.errors)) {
                errorMessage += '\n\n' + data.errors.join('\n');
            } else if (data.errors && typeof data.errors === 'object') {
                // Handle validation errors object
                const errorList = Object.values(data.errors).flat().join('\n');
                errorMessage += '\n\n' + errorList;
            }
            alert('Error: ' + errorMessage);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        let errorMessage = error.message || 'An error occurred while sending the notification.';
        
        // Try to parse if it's a JSON error
        try {
            if (error.response) {
                error.response.json().then(data => {
                    alert('Error: ' + (data.message || errorMessage));
                }).catch(() => {
                    alert('Error: ' + errorMessage);
                });
            } else {
                alert('Error: ' + errorMessage);
            }
        } catch (e) {
            alert('Error: ' + errorMessage);
        }
    });
});

function selectAllBusinesses() {
    const select = document.getElementById('businesses');
    for (let i = 0; i < select.options.length; i++) {
        select.options[i].selected = true;
    }
}

function clearAllBusinesses() {
    const select = document.getElementById('businesses');
    for (let i = 0; i < select.options.length; i++) {
        select.options[i].selected = false;
    }
}

// Set default due date to tomorrow
const tomorrow = new Date();
tomorrow.setDate(tomorrow.getDate() + 1);
document.getElementById('due_date').value = tomorrow.toISOString().slice(0, 16);
</script>
@endsection

