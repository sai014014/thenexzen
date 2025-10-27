@extends('business.layouts.app')

@section('title', 'Add New Customer - ' . $business->business_name)
@section('page-title', 'Add New Customer')

@push('styles')
<style>
/* Customer Add Page Specific Styles - Match Vehicle Create */
.customer-add-container {
    background-color: #f8f9fa;
    min-height: 100vh;
    padding: 20px 0;
}

.form-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 24px;
    padding: 24px;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 20px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e9ecef;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 6px;
    font-size: 14px;
}

.form-label.required::after {
    content: " *";
    color: #dc3545;
}

.form-control, .form-select {
    border: 1px solid #ced4da;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #6B6ADE;
    box-shadow: 0 0 0 0.2rem rgba(107, 106, 222, 0.25);
}

.save-button {
    background: linear-gradient(135deg, #6B6ADE 0%, #3C3CE1 100%);
    border: none;
    color: white;
    padding: 12px 32px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
}

.save-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(107, 106, 222, 0.4);
}

.back-button {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 14px;
}
</style>
@endpush

@section('content')
<div class="customer-add-container">
    <div class="container-fluid">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="{{ route('business.customers.index') }}" class="btn btn-outline-secondary back-button">
                <i class="fas fa-arrow-left me-2"></i>Back to Customers
            </a>
        </div>
                <form method="POST" action="{{ route('business.customers.store') }}" enctype="multipart/form-data" id="customerForm">
                    @csrf
                    <input type="hidden" name="customer_type" value="{{ $customerType }}">
                    
                    @if($customerType === 'individual')
                        @include('business.customers.forms.individual', ['customer' => null])
                    @else
                        @include('business.customers.forms.corporate', ['customer' => null])
                    @endif

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('business.customers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn save-button">
                            <i class="fas fa-save me-2"></i>Register Customer
                        </button>
                    </div>
                </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerType = '{{ $customerType }}';
    
    // Handle same as permanent address checkbox
    const sameAsPermanentCheckbox = document.getElementById('same_as_permanent');
    const currentAddressField = document.getElementById('current_address');
    const permanentAddressField = document.getElementById('permanent_address');
    
    if (sameAsPermanentCheckbox) {
        sameAsPermanentCheckbox.addEventListener('change', function() {
            if (this.checked) {
                currentAddressField.value = permanentAddressField.value;
                currentAddressField.disabled = true;
            } else {
                currentAddressField.disabled = false;
            }
        });
    }
    
    // Handle corporate drivers
    if (customerType === 'corporate') {
        let driverCount = 1;
        
        // Add driver functionality
        window.addDriver = function() {
            driverCount++;
            const driversContainer = document.getElementById('driversContainer');
            const driverHtml = `
                <div class="driver-row" data-driver-index="${driverCount}">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Driver ${driverCount}</h6>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDriver(${driverCount})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Driver's Name *</label>
                                    <input type="text" class="form-control" name="drivers[${driverCount}][driver_name]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Driving License Number *</label>
                                    <input type="text" class="form-control" name="drivers[${driverCount}][driving_license_number]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">License Expiry Date *</label>
                                    <input type="date" class="form-control" name="drivers[${driverCount}][license_expiry_date]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Upload Driving License</label>
                                    <input type="file" class="form-control" name="drivers[${driverCount}][driving_license_file]" accept=".pdf,.jpg,.jpeg,.png">
                                    <div class="form-text">PDF, JPG, PNG files only. Max size: 10MB</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            driversContainer.insertAdjacentHTML('beforeend', driverHtml);
        };
        
        // Remove driver functionality
        window.removeDriver = function(index) {
            const driverRow = document.querySelector(`[data-driver-index="${index}"]`);
            if (driverRow) {
                driverRow.remove();
            }
        };
    }
    
    // Form submission with validation
    document.getElementById('customerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Clear previous alerts
        clearAlerts();
        
        // Validate form
        if (!validateForm()) {
            return;
        }
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registering...';
        submitBtn.disabled = true;
        
        // Submit form via AJAX
        const formData = new FormData(this);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                return response.text().then(text => {
                    throw new Error('Server error: ' + response.status + ' - ' + text);
                });
            }
        })
        .then(data => {
            if (data.success) {
                showSuccessAlert('Customer registered successfully! Redirecting...');
                setTimeout(() => {
                    window.location.href = data.redirect_url || '{{ route("business.customers.index") }}';
                }, 2000);
            } else {
                let errorMessage = data.message || 'Registration failed. Please try again.';
                
                // Handle validation errors
                if (data.errors) {
                    const errorList = Object.values(data.errors).flat().join('<br>');
                    errorMessage = errorList;
                }
                
                showErrorAlert(errorMessage);
                resetSubmitButton(submitBtn, originalText);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorAlert('An error occurred while registering the customer: ' + error.message);
            resetSubmitButton(submitBtn, originalText);
        });
    });
    
    // Form validation
    function validateForm() {
        let isValid = true;
        const errors = [];
        const errorFields = [];
        
        // Clear previous error styling
        clearFieldErrors();
        
        // Basic validation based on customer type
        if (customerType === 'individual') {
            // Individual customer validation
            const requiredFields = [
                'full_name', 'mobile_number', 'date_of_birth', 'permanent_address',
                'government_id_type', 'government_id_number', 'driving_license_number'
            ];
            
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field && !field.value.trim()) {
                    errors.push(`${field.previousElementSibling.textContent.replace('*', '').trim()} is required`);
                    errorFields.push(fieldId);
                    isValid = false;
                }
            });
        } else {
            // Corporate customer validation
            const requiredFields = [
                'company_name', 'company_type', 'contact_person_name', 'designation',
                'official_email', 'contact_person_mobile', 'billing_name', 'billing_email', 'billing_address',
                'preferred_payment_method', 'invoice_frequency'
            ];
            
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field && !field.value.trim()) {
                    errors.push(`${field.previousElementSibling.textContent.replace('*', '').trim()} is required`);
                    errorFields.push(fieldId);
                    isValid = false;
                }
            });
        }
        
        // Highlight error fields
        errorFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.classList.add('is-invalid');
                field.style.borderColor = '#dc3545';
            }
        });
        
        if (!isValid) {
            showErrorAlert(errors.join('<br>'));
        }
        
        return isValid;
    }
    
    // Clear field error styling
    function clearFieldErrors() {
        const fields = document.querySelectorAll('.form-control, .form-select');
        fields.forEach(field => {
            field.classList.remove('is-invalid');
            field.style.borderColor = '';
        });
    }
    
    // Reset submit button
    function resetSubmitButton(btn, originalText) {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
    
    // Clear all alerts
    function clearAlerts() {
        $('.alert').remove();
    }
    
    // Show success alert
    function showSuccessAlert(message) {
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('.card-body').prepend(alertHtml);
    }
    
    // Show error alert
    function showErrorAlert(message) {
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><strong>Please fix the following errors:</strong><br>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('.card-body').prepend(alertHtml);
        
        // Scroll to top to show the error
        $('html, body').animate({
            scrollTop: 0
        }, 500);
    }
    
    // Add event listeners to clear error styling
    const formFields = document.querySelectorAll('.form-control, .form-select');
    formFields.forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            this.style.borderColor = '';
        });
        
        field.addEventListener('change', function() {
            this.classList.remove('is-invalid');
            this.style.borderColor = '';
        });
    });
});
</script>
@endpush
