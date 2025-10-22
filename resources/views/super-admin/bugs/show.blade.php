@extends('super-admin.layouts.app')

@section('title', 'Bug Details')

@push('styles')
    @vite(['resources/css/super-admin-bugs.css'])
@endpush

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="header-content">
            <h1 class="page-title">Bug #{{ $bug->id }}</h1>
            <p class="page-subtitle">{{ $bug->title }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('super-admin.bugs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Bugs
            </a>
            <a href="{{ route('super-admin.bugs.edit', $bug) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>
                Edit Bug
            </a>
        </div>
    </div>

    <div class="bug-details-grid">
        <!-- Main Content -->
        <div class="bug-main-content">
            <!-- Bug Information Card -->
            <div class="card">
                <div class="card-header">
                    <h3>Bug Information</h3>
                    <div class="bug-meta">
                        <span class="bug-id">#{{ $bug->id }}</span>
                        <span class="bug-created">Created {{ $bug->created_at->format('M d, Y \a\t g:i A') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="bug-header">
                        <h2 class="bug-title">{{ $bug->title }}</h2>
                        <div class="bug-badges">
                            {!! $bug->type_badge !!}
                            {!! $bug->priority_badge !!}
                            {!! $bug->status_badge !!}
                        </div>
                    </div>
                    
                    <div class="bug-description">
                        <h4>Description</h4>
                        <p>{{ $bug->description }}</p>
                    </div>

                    @if($bug->steps_to_reproduce)
                        <div class="bug-section">
                            <h4>Steps to Reproduce</h4>
                            <div class="steps-content">
                                {!! nl2br(e($bug->steps_to_reproduce)) !!}
                            </div>
                        </div>
                    @endif

                    @if($bug->expected_behavior)
                        <div class="bug-section">
                            <h4>Expected Behavior</h4>
                            <p>{{ $bug->expected_behavior }}</p>
                        </div>
                    @endif

                    @if($bug->actual_behavior)
                        <div class="bug-section">
                            <h4>Actual Behavior</h4>
                            <p>{{ $bug->actual_behavior }}</p>
                        </div>
                    @endif

                    @if($bug->resolution_notes)
                        <div class="bug-section">
                            <h4>Resolution Notes</h4>
                            <div class="resolution-content">
                                {!! nl2br(e($bug->resolution_notes)) !!}
                            </div>
                        </div>
                    @endif

                    @if($bug->attachments->count() > 0)
                        <div class="bug-section">
                            <h4>Attachments ({{ $bug->attachments->count() }})</h4>
                            <div class="attachments-grid">
                                @foreach($bug->attachments as $attachment)
                                    <div class="attachment-card" data-attachment-id="{{ $attachment->id }}">
                                        <div class="attachment-preview">
                                            @if($attachment->isImage())
                                                <img src="{{ $attachment->thumbnail_url }}" 
                                                     alt="{{ $attachment->original_name }}" 
                                                     class="attachment-thumbnail"
                                                     onclick="openAttachmentModal('{{ $attachment->file_url }}', '{{ $attachment->original_name }}', 'image')">
                                            @elseif($attachment->isVideo())
                                                <video src="{{ $attachment->file_url }}" 
                                                       class="attachment-thumbnail" 
                                                       muted
                                                       onclick="openAttachmentModal('{{ $attachment->file_url }}', '{{ $attachment->original_name }}', 'video')">
                                                </video>
                                            @else
                                                <div class="attachment-icon" 
                                                     onclick="openAttachmentModal('{{ $attachment->file_url }}', '{{ $attachment->original_name }}', 'document')">
                                                    <i class="{{ $attachment->file_icon }}"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="attachment-info">
                                            <div class="attachment-name" title="{{ $attachment->original_name }}">
                                                {{ Str::limit($attachment->original_name, 20) }}
                                            </div>
                                            <div class="attachment-size">{{ $attachment->file_size_formatted }}</div>
                                        </div>
                                        <div class="attachment-actions">
                                            <a href="{{ $attachment->file_url }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteAttachment({{ $attachment->id }})"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="bug-sidebar">
            <!-- Status Update Card -->
            <div class="card">
                <div class="card-header">
                    <h3>Update Status</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('super-admin.bugs.update-status', $bug) }}" class="status-update-form">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" onchange="updateBugStatus(this)">
                                <option value="open" {{ $bug->status == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $bug->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="testing" {{ $bug->status == 'testing' ? 'selected' : '' }}>Testing</option>
                                <option value="resolved" {{ $bug->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $bug->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bug Details Card -->
            <div class="card">
                <div class="card-header">
                    <h3>Bug Details</h3>
                </div>
                <div class="card-body">
                    <div class="detail-item">
                        <label>Type</label>
                        <span>{!! $bug->type_badge !!}</span>
                    </div>
                    <div class="detail-item">
                        <label>Priority</label>
                        <span>{!! $bug->priority_badge !!}</span>
                    </div>
                    <div class="detail-item">
                        <label>Status</label>
                        <span>{!! $bug->status_badge !!}</span>
                    </div>
                    <div class="detail-item">
                        <label>Reported By</label>
                        <span>{{ $bug->reported_by ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Assigned To</label>
                        <span>{{ $bug->assigned_to ?? 'Unassigned' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Created</label>
                        <span>{{ $bug->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <span>{{ $bug->updated_at->format('M d, Y') }}</span>
                    </div>
                    @if($bug->resolved_at)
                        <div class="detail-item">
                            <label>Resolved</label>
                            <span>{{ $bug->resolved_at->format('M d, Y') }}</span>
                        </div>
                    @endif
                    <div class="detail-item">
                        <label>Days Open</label>
                        <span>{{ $bug->getDaysOpen() }} days</span>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h3>Actions</h3>
                </div>
                <div class="card-body">
                    <div class="action-buttons">
                        <a href="{{ route('super-admin.bugs.edit', $bug) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-edit me-2"></i>
                            Edit Bug
                        </a>
                        <form method="POST" action="{{ route('super-admin.bugs.destroy', $bug) }}" 
                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this bug?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash me-2"></i>
                                Delete Bug
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attachment Modal -->
<div class="modal fade" id="attachmentModal" tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attachmentModalLabel">Attachment Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="attachmentContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <a id="downloadAttachment" href="#" class="btn btn-primary" target="_blank">
                    <i class="fas fa-download me-2"></i>
                    Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
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
        method: 'PATCH',
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
            
            // Reload page to update all status badges
            setTimeout(() => {
                window.location.reload();
            }, 1000);
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

// Set original value on page load
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    if (statusSelect) {
        statusSelect.dataset.originalValue = statusSelect.value;
    }
});

// Attachment modal functions
window.openAttachmentModal = function(fileUrl, fileName, fileType) {
    const modal = new bootstrap.Modal(document.getElementById('attachmentModal'));
    const modalTitle = document.getElementById('attachmentModalLabel');
    const attachmentContent = document.getElementById('attachmentContent');
    const downloadLink = document.getElementById('downloadAttachment');
    
    modalTitle.textContent = fileName;
    downloadLink.href = fileUrl;
    
    if (fileType === 'image') {
        attachmentContent.innerHTML = `<img src="${fileUrl}" class="img-fluid" alt="${fileName}">`;
    } else if (fileType === 'video') {
        attachmentContent.innerHTML = `<video src="${fileUrl}" class="img-fluid" controls autoplay></video>`;
    } else {
        attachmentContent.innerHTML = `
            <div class="text-center">
                <i class="fas fa-file fa-5x text-muted mb-3"></i>
                <p class="text-muted">Preview not available for this file type</p>
                <p class="text-muted">Click download to view the file</p>
            </div>
        `;
    }
    
    modal.show();
};

window.deleteAttachment = function(attachmentId) {
    if (confirm('Are you sure you want to delete this attachment?')) {
        console.log('Deleting attachment with ID:', attachmentId);
        
        fetch(`{{ route('super-admin.bug-attachments.destroy', '') }}/${attachmentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                const attachmentCard = document.querySelector(`[data-attachment-id="${attachmentId}"]`);
                if (attachmentCard) {
                    attachmentCard.remove();
                }
                showAlert('success', data.message);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred while deleting the attachment.');
        });
    }
};
</script>
@endpush
