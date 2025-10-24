@extends('super-admin.layouts.app')

@section('title', 'Edit Bug')

@push('styles')
    @vite(['resources/css/super-admin-bugs.css'])
@endpush

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="header-content">
            <h1 class="page-title">Edit Bug #{{ $bug->id }}</h1>
            <p class="page-subtitle">{{ $bug->title }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('super-admin.bugs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Bugs
            </a>
            <a href="{{ route('super-admin.bugs.show', $bug) }}" class="btn btn-info">
                <i class="fas fa-eye me-2"></i>
                View Bug
            </a>
        </div>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('super-admin.bugs.update', $bug) }}" class="bug-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="title" class="form-label required">Title</label>
                    <input type="text" 
                           class="form-control @error('title') is-invalid @enderror" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $bug->title) }}" 
                           placeholder="Brief description of the issue"
                           required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group col-md-4">
                    <label for="type" class="form-label required">Type</label>
                    <select class="form-select @error('type') is-invalid @enderror" 
                            id="type" 
                            name="type" 
                            required>
                        <option value="">Select Type</option>
                        <option value="bug" {{ old('type', $bug->type) == 'bug' ? 'selected' : '' }}>Bug</option>
                        <option value="feature_request" {{ old('type', $bug->type) == 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                        <option value="improvement" {{ old('type', $bug->type) == 'improvement' ? 'selected' : '' }}>Improvement</option>
                        <option value="task" {{ old('type', $bug->type) == 'task' ? 'selected' : '' }}>Task</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="priority" class="form-label required">Priority</label>
                    <select class="form-select @error('priority') is-invalid @enderror" 
                            id="priority" 
                            name="priority" 
                            required>
                        <option value="">Select Priority</option>
                        <option value="low" {{ old('priority', $bug->priority) == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $bug->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $bug->priority) == 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('priority', $bug->priority) == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group col-md-6">
                    <label for="status" class="form-label required">Status</label>
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" 
                            name="status" 
                            required>
                        <option value="open" {{ old('status', $bug->status) == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ old('status', $bug->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="testing" {{ old('status', $bug->status) == 'testing' ? 'selected' : '' }}>Testing</option>
                        <option value="resolved" {{ old('status', $bug->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ old('status', $bug->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="reported_by" class="form-label">Reported By</label>
                    <input type="text" 
                           class="form-control @error('reported_by') is-invalid @enderror" 
                           id="reported_by" 
                           name="reported_by" 
                           value="{{ old('reported_by', $bug->reported_by) }}" 
                           placeholder="Name of the person reporting">
                    @error('reported_by')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group col-md-6">
                    <label for="assigned_to" class="form-label">Assigned To</label>
                    <input type="text" 
                           class="form-control @error('assigned_to') is-invalid @enderror" 
                           id="assigned_to" 
                           name="assigned_to" 
                           value="{{ old('assigned_to', $bug->assigned_to) }}" 
                           placeholder="Name of the person assigned">
                    @error('assigned_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label required">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" 
                          name="description" 
                          rows="4" 
                          placeholder="Detailed description of the issue"
                          required>{{ old('description', $bug->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="steps_to_reproduce" class="form-label">Steps to Reproduce</label>
                <textarea class="form-control @error('steps_to_reproduce') is-invalid @enderror" 
                          id="steps_to_reproduce" 
                          name="steps_to_reproduce" 
                          rows="3" 
                          placeholder="1. Step one&#10;2. Step two&#10;3. Step three">{{ old('steps_to_reproduce', $bug->steps_to_reproduce) }}</textarea>
                @error('steps_to_reproduce')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="expected_behavior" class="form-label">Expected Behavior</label>
                    <textarea class="form-control @error('expected_behavior') is-invalid @enderror" 
                              id="expected_behavior" 
                              name="expected_behavior" 
                              rows="3" 
                              placeholder="What should happen">{{ old('expected_behavior', $bug->expected_behavior) }}</textarea>
                    @error('expected_behavior')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group col-md-6">
                    <label for="actual_behavior" class="form-label">Actual Behavior</label>
                    <textarea class="form-control @error('actual_behavior') is-invalid @enderror" 
                              id="actual_behavior" 
                              name="actual_behavior" 
                              rows="3" 
                              placeholder="What actually happens">{{ old('actual_behavior', $bug->actual_behavior) }}</textarea>
                    @error('actual_behavior')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="resolution_notes" class="form-label">Resolution Notes</label>
                <textarea class="form-control @error('resolution_notes') is-invalid @enderror" 
                          id="resolution_notes" 
                          name="resolution_notes" 
                          rows="3" 
                          placeholder="Notes about how the issue was resolved">{{ old('resolution_notes', $bug->resolution_notes) }}</textarea>
                @error('resolution_notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Existing Attachments -->
            @if($bug->attachments->count() > 0)
                <div class="form-group">
                    <label class="form-label">Current Attachments</label>
                    <div class="existing-attachments">
                        @foreach($bug->attachments as $attachment)
                            <div class="attachment-item" data-attachment-id="{{ $attachment->id }}">
                                <div class="attachment-preview">
                                    @if($attachment->isImage())
                                        <img src="{{ $attachment->thumbnail_url }}" alt="{{ $attachment->original_name }}" class="attachment-thumbnail">
                                    @elseif($attachment->isVideo())
                                        <video src="{{ $attachment->file_url }}" class="attachment-thumbnail" muted></video>
                                    @else
                                        <div class="attachment-icon">
                                            <i class="{{ $attachment->file_icon }}"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="attachment-info">
                                    <div class="attachment-name">{{ $attachment->original_name }}</div>
                                    <div class="attachment-size">{{ $attachment->file_size_formatted }}</div>
                                </div>
                                <button type="button" class="attachment-delete" onclick="deleteAttachment({{ $attachment->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- File Attachments Section -->
            <div class="form-group">
                <label for="attachments" class="form-label">Add New Attachments</label>
                <div class="file-upload-area" id="fileUploadArea">
                    <div class="file-upload-content">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Drag and drop files here or click to browse</p>
                        <p class="file-types">Supports: Images, Videos, PDFs, Documents (Max 10MB each)</p>
                    </div>
                    <input type="file" 
                           id="attachments" 
                           name="attachments[]" 
                           multiple 
                           accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                           class="file-input @error('attachments.*') is-invalid @enderror">
                </div>
                <div id="filePreview" class="file-preview-container"></div>
                @error('attachments.*')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    Update Bug
                </button>
                <a href="{{ route('super-admin.bugs.show', $bug) }}" class="btn btn-info">
                    <i class="fas fa-eye me-2"></i>
                    View Bug
                </a>
                <a href="{{ route('super-admin.bugs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('attachments');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const filePreview = document.getElementById('filePreview');
    const maxFileSize = 10 * 1024 * 1024; // 10MB
    const allowedTypes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/webm',
        'application/pdf',
        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
    ];

    // File upload area click handler
    fileUploadArea.addEventListener('click', () => fileInput.click());

    // Drag and drop handlers
    fileUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUploadArea.classList.add('dragover');
    });

    fileUploadArea.addEventListener('dragleave', () => {
        fileUploadArea.classList.remove('dragover');
    });

    fileUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUploadArea.classList.remove('dragover');
        const files = Array.from(e.dataTransfer.files);
        handleFiles(files);
    });

    // File input change handler
    fileInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        handleFiles(files);
    });

    function handleFiles(files) {
        files.forEach(file => {
            if (validateFile(file)) {
                addFilePreview(file);
            }
        });
    }

    function validateFile(file) {
        if (file.size > maxFileSize) {
            alert(`File "${file.name}" is too large. Maximum size is 10MB.`);
            return false;
        }

        if (!allowedTypes.includes(file.type)) {
            alert(`File type "${file.type}" is not supported.`);
            return false;
        }

        return true;
    }

    function addFilePreview(file) {
        const previewItem = document.createElement('div');
        previewItem.className = 'file-preview-item';
        previewItem.dataset.fileName = file.name;

        const fileIcon = getFileIcon(file.type);
        const isImage = file.type.startsWith('image/');
        const isVideo = file.type.startsWith('video/');

        previewItem.innerHTML = `
            <div class="file-preview-content">
                <div class="file-icon">
                    <i class="${fileIcon}"></i>
                </div>
                <div class="file-info">
                    <div class="file-name">${file.name}</div>
                    <div class="file-size">${formatFileSize(file.size)}</div>
                </div>
                <button type="button" class="file-remove" onclick="removeFilePreview(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            ${isImage ? `<div class="file-thumbnail"><img src="${URL.createObjectURL(file)}" alt="${file.name}"></div>` : ''}
            ${isVideo ? `<div class="file-thumbnail"><video src="${URL.createObjectURL(file)}" muted></video></div>` : ''}
        `;

        filePreview.appendChild(previewItem);
    }

    function getFileIcon(mimeType) {
        if (mimeType.startsWith('image/')) return 'fas fa-image';
        if (mimeType.startsWith('video/')) return 'fas fa-video';
        if (mimeType === 'application/pdf') return 'fas fa-file-pdf';
        if (mimeType.includes('word')) return 'fas fa-file-word';
        if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'fas fa-file-excel';
        if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) return 'fas fa-file-powerpoint';
        return 'fas fa-file';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    window.removeFilePreview = function(button) {
        const previewItem = button.closest('.file-preview-item');
        previewItem.remove();
    };

    window.deleteAttachment = function(attachmentId) {
        if (confirm('Are you sure you want to delete this attachment?')) {
            console.log('Deleting attachment with ID:', attachmentId);
            
            fetch(`{{ route('super-admin.bug-attachments.destroy', ':attachment') }}`.replace(':attachment', attachmentId), {
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
                    const attachmentItem = document.querySelector(`[data-attachment-id="${attachmentId}"]`);
                    if (attachmentItem) {
                        attachmentItem.remove();
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

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const content = document.querySelector('.content-wrapper');
        content.insertBefore(alertDiv, content.firstChild);
        
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>
@endpush
