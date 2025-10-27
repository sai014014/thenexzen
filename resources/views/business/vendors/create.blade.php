@extends('business.layouts.app')

@section('title', 'Add New Vendor')
@section('page-title', 'Add New Vendor')

@push('styles')
<style>
/* Vendor Add Page Specific Styles - Match Vehicle Create */
.vendor-add-container {
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
<div class="vendor-add-container">
    <div class="container-fluid">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="{{ route('business.vendors.index') }}" class="btn btn-outline-secondary back-button">
                <i class="fas fa-arrow-left me-2"></i>Back to Vendors
            </a>
        </div>
                <form id="vendorForm" method="POST" action="{{ route('business.vendors.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Vendor Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-building me-2"></i>Vendor Information
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vendor_name" class="form-label">Vendor Name *</label>
                            <input type="text" class="form-control" id="vendor_name" name="vendor_name" value="{{ old('vendor_name') }}" required>
                            @error('vendor_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vendor_type" class="form-label">Vendor Type *</label>
                            <select class="form-select" id="vendor_type" name="vendor_type" required>
                                <option value="">Select Vendor Type</option>
                                <option value="vehicle_provider" {{ old('vendor_type') == 'vehicle_provider' ? 'selected' : '' }}>Vehicle Provider</option>
                                <option value="service_partner" {{ old('vendor_type') == 'service_partner' ? 'selected' : '' }}>Service Partner</option>
                                <option value="other" {{ old('vendor_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('vendor_type')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gstin" class="form-label">GSTIN</label>
                            <input type="text" class="form-control" id="gstin" name="gstin" value="{{ old('gstin') }}" maxlength="15">
                            <div class="form-text">15-digit GST Identification Number</div>
                            @error('gstin')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pan_number" class="form-label">PAN Number *</label>
                            <input type="text" class="form-control" id="pan_number" name="pan_number" value="{{ old('pan_number') }}" maxlength="10" required>
                            <div class="form-text">10-digit PAN number</div>
                            @error('pan_number')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="primary_contact_person" class="form-label">Primary Contact Person *</label>
                            <input type="text" class="form-control" id="primary_contact_person" name="primary_contact_person" value="{{ old('primary_contact_person') }}" required>
                            @error('primary_contact_person')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-phone me-2"></i>Contact Information
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mobile_number" class="form-label">Mobile Number *</label>
                            <input type="tel" class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" required>
                            @error('mobile_number')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="alternate_contact_number" class="form-label">Alternate Contact Number</label>
                            <input type="tel" class="form-control" id="alternate_contact_number" name="alternate_contact_number" value="{{ old('alternate_contact_number') }}">
                            @error('alternate_contact_number')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email_address" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email_address" name="email_address" value="{{ old('email_address') }}" required>
                            @error('email_address')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-map-marker-alt me-2"></i>Address Information
                            </h6>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="office_address" class="form-label">Office Address *</label>
                            <textarea class="form-control" id="office_address" name="office_address" rows="3" required>{{ old('office_address') }}</textarea>
                            @error('office_address')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label for="additional_branches" class="form-label">Additional Branches</label>
                            <div id="additionalBranchesContainer">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="additional_branches[]" placeholder="Enter branch address">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeBranch(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addBranch()">
                                <i class="fas fa-plus me-1"></i>Add Branch
                            </button>
                        </div>
                    </div>

                    <!-- Vendor Payout Settings -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-credit-card me-2"></i>Vendor Payout Settings
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="payout_method" class="form-label">Payout Method *</label>
                            <select class="form-select" id="payout_method" name="payout_method" required onchange="togglePayoutFields()">
                                <option value="">Select Payout Method</option>
                                <option value="bank_transfer" {{ old('payout_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer (NEFT/RTGS)</option>
                                <option value="upi_payment" {{ old('payout_method') == 'upi_payment' ? 'selected' : '' }}>UPI Payment</option>
                                <option value="cheque" {{ old('payout_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="other" {{ old('payout_method') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('payout_method')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3" id="otherPayoutMethodDiv" style="display: none;">
                            <label for="other_payout_method" class="form-label">Specify Other Method</label>
                            <input type="text" class="form-control" id="other_payout_method" name="other_payout_method" value="{{ old('other_payout_method') }}">
                            @error('other_payout_method')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Bank Details (if Bank Transfer selected) -->
                    <div id="bankDetailsDiv" style="display: none;">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-university me-2"></i>Bank Details
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bank_name" class="form-label">Bank Name *</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name') }}">
                                @error('bank_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="account_holder_name" class="form-label">Account Holder Name *</label>
                                <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" value="{{ old('account_holder_name') }}">
                                @error('account_holder_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="account_number" class="form-label">Account Number *</label>
                                <input type="text" class="form-control" id="account_number" name="account_number" value="{{ old('account_number') }}">
                                @error('account_number')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ifsc_code" class="form-label">IFSC Code *</label>
                                <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="{{ old('ifsc_code') }}" maxlength="11">
                                @error('ifsc_code')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bank_branch_name" class="form-label">Bank Branch Name</label>
                                <input type="text" class="form-control" id="bank_branch_name" name="bank_branch_name" value="{{ old('bank_branch_name') }}">
                                @error('bank_branch_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- UPI Payment Details (if UPI Payment selected) -->
                    <div id="upiDetailsDiv" style="display: none;">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-mobile-alt me-2"></i>UPI Payment Details
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="upi_id" class="form-label">UPI ID *</label>
                                <input type="text" class="form-control" id="upi_id" name="upi_id" value="{{ old('upi_id') }}" placeholder="example@upi">
                                @error('upi_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Payout Frequency -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-calendar-alt me-2"></i>Payout Frequency
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="payout_frequency" class="form-label">Payout Frequency *</label>
                            <select class="form-select" id="payout_frequency" name="payout_frequency" required onchange="togglePayoutSchedule()">
                                <option value="">Select Frequency</option>
                                <option value="weekly" {{ old('payout_frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="bi_weekly" {{ old('payout_frequency') == 'bi_weekly' ? 'selected' : '' }}>Bi-Weekly</option>
                                <option value="monthly" {{ old('payout_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ old('payout_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="after_every_booking" {{ old('payout_frequency') == 'after_every_booking' ? 'selected' : '' }}>After Every Booking</option>
                            </select>
                            @error('payout_frequency')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3" id="payoutDayOfWeekDiv" style="display: none;">
                            <label for="payout_day_of_week" class="form-label">Day of Week *</label>
                            <select class="form-select" id="payout_day_of_week" name="payout_day_of_week">
                                <option value="">Select Day</option>
                                <option value="monday" {{ old('payout_day_of_week') == 'monday' ? 'selected' : '' }}>Monday</option>
                                <option value="tuesday" {{ old('payout_day_of_week') == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                                <option value="wednesday" {{ old('payout_day_of_week') == 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                                <option value="thursday" {{ old('payout_day_of_week') == 'thursday' ? 'selected' : '' }}>Thursday</option>
                                <option value="friday" {{ old('payout_day_of_week') == 'friday' ? 'selected' : '' }}>Friday</option>
                                <option value="saturday" {{ old('payout_day_of_week') == 'saturday' ? 'selected' : '' }}>Saturday</option>
                                <option value="sunday" {{ old('payout_day_of_week') == 'sunday' ? 'selected' : '' }}>Sunday</option>
                            </select>
                            @error('payout_day_of_week')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3" id="payoutDayOfMonthDiv" style="display: none;">
                            <label for="payout_day_of_month" class="form-label">Day of Month *</label>
                            <input type="number" class="form-control" id="payout_day_of_month" name="payout_day_of_month" value="{{ old('payout_day_of_month') }}" min="1" max="31">
                            @error('payout_day_of_month')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label for="payout_terms" class="form-label">Payout Terms</label>
                            <textarea class="form-control" id="payout_terms" name="payout_terms" rows="3" placeholder="Any special payout terms, thresholds, or conditions">{{ old('payout_terms') }}</textarea>
                            @error('payout_terms')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Commission Settings -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-percentage me-2"></i>Commission Settings
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="commission_type" class="form-label">Commission Type *</label>
                            <select class="form-select" id="commission_type" name="commission_type" required onchange="toggleCommissionFields()">
                                <option value="">Select Commission Type</option>
                                <option value="fixed_amount" {{ old('commission_type') == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                                <option value="percentage_of_revenue" {{ old('commission_type') == 'percentage_of_revenue' ? 'selected' : '' }}>Percentage of Revenue</option>
                            </select>
                            @error('commission_type')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="commission_rate" class="form-label">Commission Rate *</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="commission_rate" name="commission_rate" value="{{ old('commission_rate') }}" step="0.01" min="0" required>
                                <span class="input-group-text" id="commissionUnit">₹</span>
                            </div>
                            @error('commission_rate')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Document Upload -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-file-upload me-2"></i>Document Upload
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vendor_agreement" class="form-label">Vendor Agreement Document</label>
                            <input type="file" class="form-control" id="vendor_agreement" name="vendor_agreement" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF, JPG, PNG files only (Max 10MB)</div>
                            @error('vendor_agreement')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gstin_certificate" class="form-label">GSTIN Certificate</label>
                            <input type="file" class="form-control" id="gstin_certificate" name="gstin_certificate" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">PDF, JPG, PNG files only (Max 10MB)</div>
                            @error('gstin_certificate')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pan_card" class="form-label">PAN Card *</label>
                            <input type="file" class="form-control" id="pan_card" name="pan_card" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="form-text">PDF, JPG, PNG files only (Max 10MB)</div>
                            @error('pan_card')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="additional_certificates" class="form-label">Additional Certificates</label>
                            <input type="file" class="form-control" id="additional_certificates" name="additional_certificates[]" accept=".pdf,.jpg,.jpeg,.png" multiple>
                            <div class="form-text">PDF, JPG, PNG files only (Max 10MB each)</div>
                            @error('additional_certificates.*')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('business.vendors.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Register Vendor
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle payout method fields
function togglePayoutFields() {
    const payoutMethod = document.getElementById('payout_method').value;
    const bankDetailsDiv = document.getElementById('bankDetailsDiv');
    const upiDetailsDiv = document.getElementById('upiDetailsDiv');
    const otherPayoutMethodDiv = document.getElementById('otherPayoutMethodDiv');
    
    // Hide all sections
    bankDetailsDiv.style.display = 'none';
    upiDetailsDiv.style.display = 'none';
    otherPayoutMethodDiv.style.display = 'none';
    
    // Show relevant section
    if (payoutMethod === 'bank_transfer') {
        bankDetailsDiv.style.display = 'block';
    } else if (payoutMethod === 'upi_payment') {
        upiDetailsDiv.style.display = 'block';
    } else if (payoutMethod === 'other') {
        otherPayoutMethodDiv.style.display = 'block';
    }
}

// Toggle payout schedule fields
function togglePayoutSchedule() {
    const payoutFrequency = document.getElementById('payout_frequency').value;
    const payoutDayOfWeekDiv = document.getElementById('payoutDayOfWeekDiv');
    const payoutDayOfMonthDiv = document.getElementById('payoutDayOfMonthDiv');
    
    // Hide all sections
    payoutDayOfWeekDiv.style.display = 'none';
    payoutDayOfMonthDiv.style.display = 'none';
    
    // Show relevant section
    if (payoutFrequency === 'weekly' || payoutFrequency === 'bi_weekly') {
        payoutDayOfWeekDiv.style.display = 'block';
    } else if (payoutFrequency === 'monthly' || payoutFrequency === 'quarterly') {
        payoutDayOfMonthDiv.style.display = 'block';
    }
}

// Toggle commission fields
function toggleCommissionFields() {
    const commissionType = document.getElementById('commission_type').value;
    const commissionUnit = document.getElementById('commissionUnit');
    
    if (commissionType === 'percentage_of_revenue') {
        commissionUnit.textContent = '%';
    } else {
        commissionUnit.textContent = '₹';
    }
}

// Add additional branch
function addBranch() {
    const container = document.getElementById('additionalBranchesContainer');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" class="form-control" name="additional_branches[]" placeholder="Enter branch address">
        <button type="button" class="btn btn-outline-danger" onclick="removeBranch(this)">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

// Remove additional branch
function removeBranch(button) {
    button.parentElement.remove();
}

// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
    togglePayoutFields();
    togglePayoutSchedule();
    toggleCommissionFields();
});
</script>
@endpush
