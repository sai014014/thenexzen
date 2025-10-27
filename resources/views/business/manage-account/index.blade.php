@extends('business.layouts.app')

@section('title', 'Manage Account - The NexZen Business Portal')
@section('page-title', 'Manage Account')

@section('content')
<div class="manage-account-content">
    <!-- Account Overview Section -->
    <div class="account-section">
        <div class="section-header">
            <h3>Account Overview</h3>
            <p>Key account information and settings</p>
        </div>
        
        <div class="account-overview-card">
            <div class="account-info">
                <div class="account-logo">
                    <img src="{{ $business->logo ? asset('storage/' . $business->logo) : asset('images/default-business-logo.png') }}" 
                         alt="Business Logo" id="businessLogo">
                    <button class="change-logo-btn" id="logoUploadBtn" onclick="document.getElementById('logoInput').click()">
                        <span class="btn-text">
                            <i class="fas fa-camera"></i> Change Logo
                        </span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Uploading...
                        </span>
                    </button>
                    <input type="file" id="logoInput" accept="image/*" style="display: none;" onchange="updateLogo(this)">
                </div>
                
                <div class="account-details">
                    <div class="detail-row">
                        <label>Business Name:</label>
                        <span>{{ $business->business_name }}</span>
                    </div>
                    <div class="detail-row">
                        <label>Registered Email:</label>
                        <span>{{ $business->email }}</span>
                    </div>
                    <div class="detail-row">
                        <label>Phone Number:</label>
                        <span>{{ $business->phone ?? 'Not provided' }}</span>
                    </div>
                    <div class="detail-row">
                        <label>Alternate Number:</label>
                        <span>{{ $business->contact_number ?? 'Not provided' }}</span>
                    </div>
                    <div class="detail-row">
                        <label>Account ID:</label>
                        <span class="account-id">{{ $business->client_id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Details Section -->
    <div class="account-section">
        <div class="section-header">
            <h3>Company / Business Details</h3>
            <p>Update your business information</p>
        </div>
        
        <div class="company-details-card">
            <form id="businessDetailsForm">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label for="business_name">Business Name *</label>
                        <input type="text" id="business_name" name="business_name" 
                               value="{{ $business->business_name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="business_type">Business Type *</label>
                        <select id="business_type" name="business_type" required>
                            <option value="individual" {{ $business->business_type == 'individual' ? 'selected' : '' }}>Individual</option>
                            <option value="company" {{ $business->business_type == 'company' ? 'selected' : '' }}>Company</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3" placeholder="Street address">{{ $business->address }}</textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="{{ $business->city }}">
                    </div>
                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" id="state" name="state" value="{{ $business->state }}">
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country" value="{{ $business->country }}">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="gst_number">GST / Tax ID</label>
                        <input type="text" id="gst_number" name="gst_number" value="{{ $business->gst_number }}">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="{{ $business->phone }}" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_number">Alternate Number</label>
                        <input type="text" id="contact_number" name="contact_number" value="{{ $business->contact_number }}">
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="resetForm()">Reset</button>
                    <button type="submit" class="btn btn-primary" id="saveBusinessBtn">
                        <span class="btn-text">Save Changes</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users & Access Management Section -->
    <div class="account-section">
        <div class="section-header">
            <h3>Users & Access Management</h3>
            <p>Manage team members who have access to this account</p>
        </div>
        
        <div class="users-management-card">
            <div class="users-header">
                <button class="btn btn-primary" onclick="openAddUserModal()">
                    <i class="fas fa-plus"></i> Add New User
                </button>
            </div>
            
            <div class="users-table">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($businessAdmins as $admin)
                        <tr>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>
                                <span class="role-badge role-{{ $admin->role }}">
                                    {{ ucfirst($admin->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $admin->is_active ? 'active' : 'inactive' }}">
                                    {{ $admin->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $admin->last_login_at ? $admin->last_login_at->format('M d, Y H:i') : 'Never' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline" onclick="editUser({{ $admin->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($admin->id !== $businessAdmin->id)
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser({{ $admin->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Billing & Subscription Section -->
    <div class="account-section">
        <div class="section-header">
            <h3>Billing & Subscription</h3>
            <p>Your current subscription and payment information</p>
        </div>
        
        <div class="billing-card">
            @if($subscription)
            <div class="subscription-info">
                <div class="subscription-details">
                    <div class="detail-row">
                        <label>Current Plan:</label>
                        <span class="plan-name">{{ $subscription->subscriptionPackage->name ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <label>Status:</label>
                        <span class="status-badge status-{{ $subscription->status }}">
                            {{ ucfirst($subscription->status) }}
                        </span>
                    </div>
                    <div class="detail-row">
                        <label>Started:</label>
                        <span>{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <label>Expires:</label>
                        <span>{{ $subscription->expires_at ? $subscription->expires_at->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <label>Monthly Price:</label>
                        <span>₹{{ number_format($subscription->subscriptionPackage->price ?? 0) }}</span>
                    </div>
                </div>
                
                <div class="subscription-actions">
                    <div style="display: flex; gap: 10px;">
                        <a href="{{ route('business.subscription.show', $subscription->id) }}" class="btn btn-outline">
                            View Full Details
                        </a>
                        @if($subscription->is_active && !$subscription->is_paused)
                            <button class="btn btn-warning" onclick="pauseSubscription()">
                                <i class="fas fa-pause"></i> Pause Subscription
                            </button>
                        @elseif($subscription->is_paused)
                            <button class="btn btn-success" onclick="resumeSubscription()">
                                <i class="fas fa-play"></i> Resume Subscription
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @else
            <div class="no-subscription">
                <div class="no-subscription-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h4>No Active Subscription</h4>
                <p>You don't have an active subscription. Please subscribe to a plan to access all features.</p>
                <div class="subscription-actions">
                    <a href="{{ route('business.subscription.index') }}" class="btn btn-primary">
                        View Subscription Options
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal" id="addUserModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New User</h3>
            <button class="close-btn" onclick="closeAddUserModal()">&times;</button>
        </div>
        <form id="addUserForm">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="user_name">Full Name *</label>
                    <input type="text" id="user_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="user_email">Email Address *</label>
                    <input type="email" id="user_email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="user_role">Role *</label>
                    <select id="user_role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                        <option value="employee">Employee</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="user_password">Password *</label>
                    <input type="password" id="user_password" name="password" required minlength="8">
                </div>
                <div class="form-group">
                    <label for="user_password_confirmation">Confirm Password *</label>
                    <input type="password" id="user_password_confirmation" name="password_confirmation" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAddUserModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="addUserBtn">
                    <span class="btn-text">Add User</span>
                    <span class="btn-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Adding...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal" id="editUserModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit User</h3>
            <button class="close-btn" onclick="closeEditUserModal()">&times;</button>
        </div>
        <form id="editUserForm">
            @csrf
            <input type="hidden" id="edit_user_id" name="user_id">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_user_name">Full Name *</label>
                    <input type="text" id="edit_user_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="edit_user_email">Email Address *</label>
                    <input type="email" id="edit_user_email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="edit_user_role">Role *</label>
                    <select id="edit_user_role" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                        <option value="employee">Employee</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_user_status">Status</label>
                    <select id="edit_user_status" name="is_active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditUserModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="editUserBtn">
                    <span class="btn-text">Update User</span>
                    <span class="btn-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Updating...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
.no-subscription {
    text-align: center;
    padding: 40px 20px;
}

.no-subscription-icon {
    font-size: 48px;
    color: #ffc107;
    margin-bottom: 20px;
}

.no-subscription h4 {
    color: #333;
    margin-bottom: 10px;
}

.no-subscription p {
    color: #666;
    margin-bottom: 30px;
}

.btn-loading {
    display: none;
}

.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.change-logo-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.manage-account-content {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
    zoom: 0.9;
}

.account-section {
    margin-bottom: 40px;
}

.section-header {
    margin-bottom: 20px;
}

.section-header h3 {
    color: #333;
    margin-bottom: 5px;
    font-size: 1.5rem;
}

.section-header p {
    color: #666;
    margin: 0;
}

.account-overview-card,
.company-details-card,
.users-management-card,
.billing-card {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.account-info {
    display: flex;
    gap: 30px;
    align-items: flex-start;
}

.account-logo {
    position: relative;
    text-align: center;
}

.account-logo img {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
    border: 3px solid #f0f0f0;
}

.change-logo-btn {
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: #6B6ADE;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
}

.account-details {
    flex: 1;
}

.detail-row {
    display: flex;
    margin-bottom: 15px;
    align-items: center;
}

.detail-row label {
    font-weight: 600;
    color: #333;
    min-width: 150px;
    margin-right: 15px;
}

.detail-row span {
    color: #666;
}

.account-id {
    font-family: monospace;
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 600;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #6B6ADE;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 30px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-primary {
    background: #6B6ADE;
    color: white;
}

.btn-primary:hover {
    background: #5a5ac7;
}

.btn-secondary {
    background: #f8f9fa;
    color: #666;
    border: 2px solid #e0e0e0;
}

.btn-outline {
    background: transparent;
    color: #6B6ADE;
    border: 2px solid #6B6ADE;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-warning {
    background: #ffc107;
    color: #333;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-sm {
    padding: 8px 12px;
    font-size: 12px;
}

.users-header {
    margin-bottom: 20px;
    display: flex;
    justify-content: flex-end;
}

.users-table table {
    width: 100%;
    border-collapse: collapse;
}

.users-table th,
.users-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

.users-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.role-badge,
.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.role-admin { background: #dc3545; color: white; }
.role-manager { background: #ffc107; color: #333; }
.role-employee { background: #17a2b8; color: white; }

.status-active { background: #d4edda; color: #155724; }
.status-inactive { background: #f8d7da; color: #721c24; }

.action-buttons {
    display: flex;
    gap: 8px;
}

.subscription-info {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.subscription-details {
    flex: 1;
}

.subscription-actions {
    margin-left: 30px;
}

.no-subscription {
    text-align: center;
    padding: 40px;
    color: #666;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}

.modal-content {
    background: white;
    margin: 5% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 20px 30px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #333;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

.modal-body {
    padding: 30px;
}

.modal-footer {
    padding: 20px 30px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    gap: 15px;
    justify-content: flex-end;
}

@media (max-width: 768px) {
    .account-info {
        flex-direction: column;
        text-align: center;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .subscription-info {
        flex-direction: column;
    }
    
    .subscription-actions {
        margin-left: 0;
        margin-top: 20px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Business Details Form
document.getElementById('businessDetailsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const saveBtn = document.getElementById('saveBusinessBtn');
    const btnText = saveBtn.querySelector('.btn-text');
    const btnLoading = saveBtn.querySelector('.btn-loading');
    
    // Show loading state
    saveBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline-block';
    
    const formData = new FormData(this);
    
    fetch('{{ route("business.manage-account.update-business") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            // Update business name in sidebar if it exists
            const sidebarTitle = document.querySelector('.sidebar .logo h3');
            if (sidebarTitle) {
                sidebarTitle.textContent = document.getElementById('business_name').value;
            }
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred while updating business details');
    })
    .finally(() => {
        // Hide loading state
        saveBtn.disabled = false;
        btnText.style.display = 'inline-block';
        btnLoading.style.display = 'none';
    });
});

// Logo Update
function updateLogo(input) {
    if (input.files && input.files[0]) {
        const logoBtn = document.getElementById('logoUploadBtn');
        const btnText = logoBtn.querySelector('.btn-text');
        const btnLoading = logoBtn.querySelector('.btn-loading');
        
        // Show loading state
        logoBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline-block';
        
        const formData = new FormData();
        formData.append('logo', input.files[0]);
        
        fetch('{{ route("business.manage-account.update-logo") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('businessLogo').src = data.logo_url;
                // Update sidebar logo if it exists
                const sidebarLogo = document.getElementById('sidebarLogo');
                if (sidebarLogo) {
                    sidebarLogo.src = data.logo_url;
                }
                showAlert('success', data.message);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            showAlert('error', 'An error occurred while updating logo');
        })
        .finally(() => {
            // Hide loading state
            logoBtn.disabled = false;
            btnText.style.display = 'inline-block';
            btnLoading.style.display = 'none';
        });
    }
}

// Add User Modal
function openAddUserModal() {
    document.getElementById('addUserModal').style.display = 'block';
}

function closeAddUserModal() {
    document.getElementById('addUserModal').style.display = 'none';
    document.getElementById('addUserForm').reset();
}

document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const addBtn = document.getElementById('addUserBtn');
    const btnText = addBtn.querySelector('.btn-text');
    const btnLoading = addBtn.querySelector('.btn-loading');
    
    // Show loading state
    addBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline-block';
    
    const formData = new FormData(this);
    
    // Debug: Log form data
    console.log('Add User Form Data:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
    fetch('{{ route("business.manage-account.add-user") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            closeAddUserModal();
            location.reload();
        } else {
            if (data.errors) {
                // Show validation errors
                let errorMessage = 'Validation failed:\n';
                for (const field in data.errors) {
                    errorMessage += `• ${data.errors[field].join(', ')}\n`;
                }
                showAlert('error', errorMessage);
            } else {
                showAlert('error', data.message);
            }
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred while adding user');
    })
    .finally(() => {
        // Hide loading state
        addBtn.disabled = false;
        btnText.style.display = 'inline-block';
        btnLoading.style.display = 'none';
    });
});

// Edit User Modal
function editUser(userId) {
    // Get user data and populate form
    const row = event.target.closest('tr');
    const name = row.cells[0].textContent;
    const email = row.cells[1].textContent;
    const roleText = row.cells[2].textContent.trim();
    const statusText = row.cells[3].textContent.trim();
    
    // Convert role text to lowercase
    const role = roleText.toLowerCase();
    
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_user_name').value = name;
    document.getElementById('edit_user_email').value = email;
    document.getElementById('edit_user_role').value = role;
    document.getElementById('edit_user_status').value = statusText === 'Active' ? '1' : '0';
    
    document.getElementById('editUserModal').style.display = 'block';
}

function closeEditUserModal() {
    document.getElementById('editUserModal').style.display = 'none';
}

document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const editBtn = document.getElementById('editUserBtn');
    const btnText = editBtn.querySelector('.btn-text');
    const btnLoading = editBtn.querySelector('.btn-loading');
    
    // Show loading state
    editBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline-block';
    
    const userId = document.getElementById('edit_user_id').value;
    const formData = new FormData(this);
    
    // Debug: Log form data
    console.log('Edit User Form Data:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
        fetch(`{{ url('/business/manage-account/update-user') }}/${userId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-HTTP-Method-Override': 'PUT'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            closeEditUserModal();
            location.reload();
        } else {
            if (data.errors) {
                // Show validation errors
                let errorMessage = 'Validation failed:\n';
                for (const field in data.errors) {
                    errorMessage += `• ${data.errors[field].join(', ')}\n`;
                }
                showAlert('error', errorMessage);
            } else {
                showAlert('error', data.message);
            }
        }
    })
    .catch(error => {
        showAlert('error', 'An error occurred while updating user');
    })
    .finally(() => {
        // Hide loading state
        editBtn.disabled = false;
        btnText.style.display = 'inline-block';
        btnLoading.style.display = 'none';
    });
});

// Delete User
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch(`{{ url('/business/manage-account/delete-user') }}/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                location.reload();
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            showAlert('error', 'An error occurred while deleting user');
        });
    }
}

// Reset Form
function resetForm() {
    document.getElementById('businessDetailsForm').reset();
}

// Alert Function
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.insertBefore(alertDiv, document.body.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Pause Subscription
function pauseSubscription() {
    const reason = prompt('Please provide a reason for pausing your subscription (optional):');
    
    if (reason === null) {
        return; // User cancelled
    }

    fetch('{{ route("business.subscription.pause") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while pausing the subscription');
    });
}

// Resume Subscription
function resumeSubscription() {
    if (confirm('Are you sure you want to resume your subscription?')) {
        fetch('{{ route("business.subscription.resume") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'An error occurred while resuming the subscription');
        });
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    const addModal = document.getElementById('addUserModal');
    const editModal = document.getElementById('editUserModal');
    
    if (event.target === addModal) {
        closeAddUserModal();
    }
    if (event.target === editModal) {
        closeEditUserModal();
    }
}
</script>
@endpush
