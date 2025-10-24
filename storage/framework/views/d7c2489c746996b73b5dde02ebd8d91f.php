<?php $__env->startSection('title', 'Edit Bug'); ?>

<?php $__env->startPush('styles'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/super-admin-bugs.css']); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="header-content">
            <h1 class="page-title">Edit Bug #<?php echo e($bug->id); ?></h1>
            <p class="page-subtitle"><?php echo e($bug->title); ?></p>
        </div>
        <div class="header-actions">
            <a href="<?php echo e(route('super-admin.bugs.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Bugs
            </a>
            <a href="<?php echo e(route('super-admin.bugs.show', $bug)); ?>" class="btn btn-info">
                <i class="fas fa-eye me-2"></i>
                View Bug
            </a>
        </div>
    </div>

    <div class="form-card">
        <form method="POST" action="<?php echo e(route('super-admin.bugs.update', $bug)); ?>" class="bug-form" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label for="title" class="form-label required">Title</label>
                    <input type="text" 
                           class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           id="title" 
                           name="title" 
                           value="<?php echo e(old('title', $bug->title)); ?>" 
                           placeholder="Brief description of the issue"
                           required>
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <div class="form-group col-md-4">
                    <label for="type" class="form-label required">Type</label>
                    <select class="form-select <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                            id="type" 
                            name="type" 
                            required>
                        <option value="">Select Type</option>
                        <option value="bug" <?php echo e(old('type', $bug->type) == 'bug' ? 'selected' : ''); ?>>Bug</option>
                        <option value="feature_request" <?php echo e(old('type', $bug->type) == 'feature_request' ? 'selected' : ''); ?>>Feature Request</option>
                        <option value="improvement" <?php echo e(old('type', $bug->type) == 'improvement' ? 'selected' : ''); ?>>Improvement</option>
                        <option value="task" <?php echo e(old('type', $bug->type) == 'task' ? 'selected' : ''); ?>>Task</option>
                    </select>
                    <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="priority" class="form-label required">Priority</label>
                    <select class="form-select <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                            id="priority" 
                            name="priority" 
                            required>
                        <option value="">Select Priority</option>
                        <option value="low" <?php echo e(old('priority', $bug->priority) == 'low' ? 'selected' : ''); ?>>Low</option>
                        <option value="medium" <?php echo e(old('priority', $bug->priority) == 'medium' ? 'selected' : ''); ?>>Medium</option>
                        <option value="high" <?php echo e(old('priority', $bug->priority) == 'high' ? 'selected' : ''); ?>>High</option>
                        <option value="critical" <?php echo e(old('priority', $bug->priority) == 'critical' ? 'selected' : ''); ?>>Critical</option>
                    </select>
                    <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="status" class="form-label required">Status</label>
                    <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                            id="status" 
                            name="status" 
                            required>
                        <option value="open" <?php echo e(old('status', $bug->status) == 'open' ? 'selected' : ''); ?>>Open</option>
                        <option value="in_progress" <?php echo e(old('status', $bug->status) == 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                        <option value="testing" <?php echo e(old('status', $bug->status) == 'testing' ? 'selected' : ''); ?>>Testing</option>
                        <option value="resolved" <?php echo e(old('status', $bug->status) == 'resolved' ? 'selected' : ''); ?>>Resolved</option>
                        <option value="closed" <?php echo e(old('status', $bug->status) == 'closed' ? 'selected' : ''); ?>>Closed</option>
                    </select>
                    <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="reported_by" class="form-label">Reported By</label>
                    <input type="text" 
                           class="form-control <?php $__errorArgs = ['reported_by'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           id="reported_by" 
                           name="reported_by" 
                           value="<?php echo e(old('reported_by', $bug->reported_by)); ?>" 
                           placeholder="Name of the person reporting">
                    <?php $__errorArgs = ['reported_by'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="assigned_to" class="form-label">Assigned To</label>
                    <input type="text" 
                           class="form-control <?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                           id="assigned_to" 
                           name="assigned_to" 
                           value="<?php echo e(old('assigned_to', $bug->assigned_to)); ?>" 
                           placeholder="Name of the person assigned">
                    <?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label required">Description</label>
                <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                          id="description" 
                          name="description" 
                          rows="4" 
                          placeholder="Detailed description of the issue"
                          required><?php echo e(old('description', $bug->description)); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
                <label for="steps_to_reproduce" class="form-label">Steps to Reproduce</label>
                <textarea class="form-control <?php $__errorArgs = ['steps_to_reproduce'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                          id="steps_to_reproduce" 
                          name="steps_to_reproduce" 
                          rows="3" 
                          placeholder="1. Step one&#10;2. Step two&#10;3. Step three"><?php echo e(old('steps_to_reproduce', $bug->steps_to_reproduce)); ?></textarea>
                <?php $__errorArgs = ['steps_to_reproduce'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="expected_behavior" class="form-label">Expected Behavior</label>
                    <textarea class="form-control <?php $__errorArgs = ['expected_behavior'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                              id="expected_behavior" 
                              name="expected_behavior" 
                              rows="3" 
                              placeholder="What should happen"><?php echo e(old('expected_behavior', $bug->expected_behavior)); ?></textarea>
                    <?php $__errorArgs = ['expected_behavior'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <div class="form-group col-md-6">
                    <label for="actual_behavior" class="form-label">Actual Behavior</label>
                    <textarea class="form-control <?php $__errorArgs = ['actual_behavior'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                              id="actual_behavior" 
                              name="actual_behavior" 
                              rows="3" 
                              placeholder="What actually happens"><?php echo e(old('actual_behavior', $bug->actual_behavior)); ?></textarea>
                    <?php $__errorArgs = ['actual_behavior'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="form-group">
                <label for="resolution_notes" class="form-label">Resolution Notes</label>
                <textarea class="form-control <?php $__errorArgs = ['resolution_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                          id="resolution_notes" 
                          name="resolution_notes" 
                          rows="3" 
                          placeholder="Notes about how the issue was resolved"><?php echo e(old('resolution_notes', $bug->resolution_notes)); ?></textarea>
                <?php $__errorArgs = ['resolution_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Existing Attachments -->
            <?php if($bug->attachments->count() > 0): ?>
                <div class="form-group">
                    <label class="form-label">Current Attachments</label>
                    <div class="existing-attachments">
                        <?php $__currentLoopData = $bug->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="attachment-item" data-attachment-id="<?php echo e($attachment->id); ?>">
                                <div class="attachment-preview">
                                    <?php if($attachment->isImage()): ?>
                                        <img src="<?php echo e($attachment->thumbnail_url); ?>" alt="<?php echo e($attachment->original_name); ?>" class="attachment-thumbnail">
                                    <?php elseif($attachment->isVideo()): ?>
                                        <video src="<?php echo e($attachment->file_url); ?>" class="attachment-thumbnail" muted></video>
                                    <?php else: ?>
                                        <div class="attachment-icon">
                                            <i class="<?php echo e($attachment->file_icon); ?>"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="attachment-info">
                                    <div class="attachment-name"><?php echo e($attachment->original_name); ?></div>
                                    <div class="attachment-size"><?php echo e($attachment->file_size_formatted); ?></div>
                                </div>
                                <button type="button" class="attachment-delete" onclick="deleteAttachment(<?php echo e($attachment->id); ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

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
                           class="file-input <?php $__errorArgs = ['attachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                </div>
                <div id="filePreview" class="file-preview-container"></div>
                <?php $__errorArgs = ['attachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    Update Bug
                </button>
                <a href="<?php echo e(route('super-admin.bugs.show', $bug)); ?>" class="btn btn-info">
                    <i class="fas fa-eye me-2"></i>
                    View Bug
                </a>
                <a href="<?php echo e(route('super-admin.bugs.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
            
            fetch(`<?php echo e(route('super-admin.bug-attachments.destroy', '')); ?>/${attachmentId}`, {
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('super-admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/super-admin/bugs/edit.blade.php ENDPATH**/ ?>