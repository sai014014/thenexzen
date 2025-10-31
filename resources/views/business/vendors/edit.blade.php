@extends('business.layouts.app')

@section('title', 'Edit Vendor - ' . $vendor->vendor_name)
@section('page-title', 'Edit Vendor')

@push('styles')
<style>
.form-section { background: #fff; border: 1px solid #e9ecef; border-radius: 12px; padding: 18px 18px 8px; margin-bottom: 18px; }
.section-title { font-size: 16px; font-weight: 600; color: #2d3748; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.form-actions { padding: 12px 0; }
.form-actions .btn { min-width: 160px; }
.back-button { padding: 12px 24px; border-radius: 8px; font-weight: 500; font-size: 14px; }
.file-help { font-size: 12px; color: #6c757d; margin-top: 8px; }
</style>
@endpush

@section('content')
<div class="vendor-add-container">
    <div class="container-fluid">
        <div class="mb-3">
            <a href="{{ route('business.vendors.index') }}" class="btn btn-outline-secondary back-button">
                <i class="fas fa-arrow-left me-2"></i>Back to Vendors
            </a>
        </div>

        <form id="vendorForm" method="POST" action="{{ route('business.vendors.update', $vendor) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Vendor Information -->
            <div class="form-section">
                <h3 class="section-title">Vendor Information</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="vendor_name" class="form-label">Vendor Name *</label>
                        <input type="text" class="form-control" id="vendor_name" name="vendor_name" value="{{ old('vendor_name', $vendor->vendor_name) }}" required>
                        @error('vendor_name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="vendor_type" class="form-label">Vendor Type *</label>
                        <select class="form-select" id="vendor_type" name="vendor_type" required>
                            <option value="">Select Vendor Type</option>
                            <option value="vehicle_provider" {{ old('vendor_type', $vendor->vendor_type) == 'vehicle_provider' ? 'selected' : '' }}>Vehicle Provider</option>
                            <option value="service_partner" {{ old('vendor_type', $vendor->vendor_type) == 'service_partner' ? 'selected' : '' }}>Service Partner</option>
                            <option value="other" {{ old('vendor_type', $vendor->vendor_type) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('vendor_type')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="gstin" class="form-label">GSTIN</label>
                        <input type="text" class="form-control" id="gstin" name="gstin" value="{{ old('gstin', $vendor->gstin) }}" maxlength="15">
                        @error('gstin')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pan_number" class="form-label">PAN Number</label>
                        <input type="text" class="form-control" id="pan_number" name="pan_number" value="{{ old('pan_number', $vendor->pan_number) }}" maxlength="10" style="text-transform: uppercase;">
                        @error('pan_number')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="form-section">
                <h3 class="section-title">Contact Information</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="mobile_number" class="form-label">Mobile Number *</label>
                        <input type="tel" class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $vendor->mobile_number) }}" required>
                        @error('mobile_number')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="alternate_contact_number" class="form-label">Alternate Contact Number</label>
                        <input type="tel" class="form-control" id="alternate_contact_number" name="alternate_contact_number" value="{{ old('alternate_contact_number', $vendor->alternate_contact_number) }}">
                        @error('alternate_contact_number')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email_address" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="email_address" name="email_address" value="{{ old('email_address', $vendor->email_address) }}" required>
                        @error('email_address')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="primary_contact_person" class="form-label">Primary Contact Person *</label>
                        <input type="text" class="form-control" id="primary_contact_person" name="primary_contact_person" value="{{ old('primary_contact_person', $vendor->primary_contact_person) }}" required>
                        @error('primary_contact_person')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 mb-3">
                        <label for="office_address" class="form-label">Office Address *</label>
                        <textarea class="form-control" id="office_address" name="office_address" rows="3" required>{{ old('office_address', $vendor->office_address) }}</textarea>
                        @error('office_address')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 mb-3">
                        <label for="additional_branches" class="form-label">Additional Branches</label>
                        <div id="additionalBranchesContainer">
                            @if($vendor->additional_branches && is_array($vendor->additional_branches) && count($vendor->additional_branches) > 0)
                                @foreach($vendor->additional_branches as $branch)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="additional_branches[]" value="{{ $branch }}" placeholder="Enter branch address">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeBranch(this)"><i class="fas fa-trash"></i></button>
                                </div>
                                @endforeach
                            @else
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="additional_branches[]" placeholder="Enter branch address">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeBranch(this)"><i class="fas fa-trash"></i></button>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addBranch()"><i class="fas fa-plus me-1"></i>Add Branch</button>
                    </div>
                </div>
            </div>

            <!-- Vendor Payout Settings -->
            <div class="form-section">
                <h3 class="section-title">Vendor Payout Settings</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="payout_method" class="form-label">Payout Method *</label>
                        <select class="form-select" id="payout_method" name="payout_method" required onchange="togglePayoutFields()">
                            <option value="">Select Payout Method</option>
                            <option value="bank_transfer" {{ old('payout_method', $vendor->payout_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer (NEFT/RTGS)</option>
                            <option value="upi_payment" {{ old('payout_method', $vendor->payout_method) == 'upi_payment' ? 'selected' : '' }}>UPI Payment</option>
                            <option value="cheque" {{ old('payout_method', $vendor->payout_method) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="other" {{ old('payout_method', $vendor->payout_method) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('payout_method')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3" id="otherPayoutMethodDiv" style="display: none;">
                        <label for="other_payout_method" class="form-label">Specify Other Method</label>
                        <input type="text" class="form-control" id="other_payout_method" name="other_payout_method" value="{{ old('other_payout_method', $vendor->other_payout_method) }}">
                        @error('other_payout_method')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Inline Bank Details -->
                <div id="bankDetailsDiv" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bank_name" class="form-label">Bank Name *</label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name', $vendor->bank_name) }}">
                            @error('bank_name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="account_holder_name" class="form-label">Account Holder Name *</label>
                            <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" value="{{ old('account_holder_name', $vendor->account_holder_name) }}">
                            @error('account_holder_name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="account_number" class="form-label">Account Number *</label>
                            <input type="text" class="form-control" id="account_number" name="account_number" value="{{ old('account_number', $vendor->account_number) }}">
                            @error('account_number')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ifsc_code" class="form-label">IFSC Code *</label>
                            <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="{{ old('ifsc_code', $vendor->ifsc_code) }}" maxlength="11">
                            @error('ifsc_code')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bank_branch_name" class="form-label">Bank Branch Name</label>
                            <input type="text" class="form-control" id="bank_branch_name" name="bank_branch_name" value="{{ old('bank_branch_name', $vendor->bank_branch_name) }}">
                            @error('bank_branch_name')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <!-- Inline UPI Details -->
                <div id="upiDetailsDiv" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="upi_id" class="form-label">UPI ID *</label>
                            <input type="text" class="form-control" id="upi_id" name="upi_id" value="{{ old('upi_id', $vendor->upi_id) }}" placeholder="example@upi">
                            @error('upi_id')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payout Frequency -->
            <div class="form-section">
                <h3 class="section-title">Payout Frequency</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="payout_frequency" class="form-label">Payout Frequency *</label>
                        <select class="form-select" id="payout_frequency" name="payout_frequency" required onchange="togglePayoutSchedule()">
                            <option value="">Select Frequency</option>
                            <option value="weekly" {{ old('payout_frequency', $vendor->payout_frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="bi_weekly" {{ old('payout_frequency', $vendor->payout_frequency) == 'bi_weekly' ? 'selected' : '' }}>Bi-Weekly</option>
                            <option value="monthly" {{ old('payout_frequency', $vendor->payout_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="quarterly" {{ old('payout_frequency', $vendor->payout_frequency) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="after_every_booking" {{ old('payout_frequency', $vendor->payout_frequency) == 'after_every_booking' ? 'selected' : '' }}>After Every Booking</option>
                        </select>
                        @error('payout_frequency')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3" id="payoutDayOfWeekDiv" style="display: none;">
                        <label for="payout_day_of_week" class="form-label">Day of Week *</label>
                        <select class="form-select" id="payout_day_of_week" name="payout_day_of_week">
                            <option value="">Select Day</option>
                            @foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $d)
                                <option value="{{ $d }}" {{ old('payout_day_of_week', $vendor->payout_day_of_week) == $d ? 'selected' : '' }}>{{ ucfirst($d) }}</option>
                            @endforeach
                        </select>
                        @error('payout_day_of_week')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3" id="payoutDayOfMonthDiv" style="display: none;">
                        <label for="payout_day_of_month" class="form-label">Day of Month *</label>
                        <input type="number" class="form-control" id="payout_day_of_month" name="payout_day_of_month" value="{{ old('payout_day_of_month', $vendor->payout_day_of_month) }}" min="1" max="31">
                        @error('payout_day_of_month')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 mb-3">
                        <label for="payout_terms" class="form-label">Payout Terms</label>
                        <textarea class="form-control" id="payout_terms" name="payout_terms" rows="3" placeholder="Any special payout terms, thresholds, or conditions">{{ old('payout_terms', $vendor->payout_terms) }}</textarea>
                        @error('payout_terms')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <!-- Commission Settings -->
            <div class="form-section">
                <h3 class="section-title">Commission Settings</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="commission_type" class="form-label">Commission Type *</label>
                        <select class="form-select" id="commission_type" name="commission_type" required onchange="toggleCommissionFields()">
                            <option value="">Select Commission Type</option>
                            <option value="fixed_amount" {{ old('commission_type', $vendor->commission_type) == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount Per Month</option>
                            <option value="percentage_of_revenue" {{ old('commission_type', $vendor->commission_type) == 'percentage_of_revenue' ? 'selected' : '' }}>Percentage of Revenue</option>
                            <option value="per_booking_per_day" {{ old('commission_type', $vendor->commission_type) == 'per_booking_per_day' ? 'selected' : '' }}>Per Booking Per Day</option>
                            <option value="lease_to_rent" {{ old('commission_type', $vendor->commission_type) == 'lease_to_rent' ? 'selected' : '' }}>Lease-to-Rent</option>
                        </select>
                        @error('commission_type')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="commission_rate" class="form-label">Commission Rate *</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="commission_rate" name="commission_rate" value="{{ old('commission_rate', $vendor->commission_rate) }}" step="0.01" min="0" required>
                            <span class="input-group-text" id="commissionUnit">₹</span>
                        </div>
                        @error('commission_rate')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row" id="leaseCommitmentSection" style="display: none;">
                    <div class="col-md-6 mb-3">
                        <label for="lease_commitment_months" class="form-label">Lease Commitment (Months) *</label>
                        <select class="form-select" id="lease_commitment_months" name="lease_commitment_months">
                            <option value="">Select Commitment Period</option>
                            @foreach([3,6,12,24] as $m)
                                <option value="{{ $m }}" {{ old('lease_commitment_months', $vendor->lease_commitment_months) == $m ? 'selected' : '' }}>{{ $m }} Months</option>
                            @endforeach
                        </select>
                        @error('lease_commitment_months')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <!-- Document Upload -->
            <div class="form-section">
                <h3 class="section-title">Document Upload</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="vendor_agreement" class="form-label">Vendor Agreement Document</label>
                            <div class="file-upload-area" onclick="document.getElementById('vendor_agreement').click()">
                                <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <p class="upload-text">Click to upload or drag and drop the file here. Supported format PDF, JPG, PNG</p>
                            </div>
                            <input type="file" class="form-control @error('vendor_agreement') is-invalid @enderror" id="vendor_agreement" name="vendor_agreement" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" onchange="updateFileLabel('vendor_agreement', this)">
                            <div class="helper-text">PDF, JPG, PNG files only (Max 10MB)</div>
                            @error('vendor_agreement')<div class="text-danger small">{{ $message }}</div>@enderror
                            <div id="vendor_agreement_label" class="mt-2 small text-muted" style="display: none;"></div>
                            @if($vendor->vendor_agreement_path)
                                <div class="mt-2"><small class="text-muted">Current:</small>
                                    <a href="{{ route('business.vendors.download-document', [$vendor, 'vendor_agreement']) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i>Download Current</a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="gstin_certificate" class="form-label">GSTIN Certificate</label>
                            <div class="file-upload-area" onclick="document.getElementById('gstin_certificate').click()">
                                <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <p class="upload-text">Click to upload or drag and drop the file here. Supported format PDF, JPG, PNG</p>
                            </div>
                            <input type="file" class="form-control @error('gstin_certificate') is-invalid @enderror" id="gstin_certificate" name="gstin_certificate" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" onchange="updateFileLabel('gstin_certificate', this)">
                            <div class="helper-text">PDF, JPG, PNG files only (Max 10MB)</div>
                            @error('gstin_certificate')<div class="text-danger small">{{ $message }}</div>@enderror
                            <div id="gstin_certificate_label" class="mt-2 small text-muted" style="display: none;"></div>
                            @if($vendor->gstin_certificate_path)
                                <div class="mt-2"><small class="text-muted">Current:</small>
                                    <a href="{{ route('business.vendors.download-document', [$vendor, 'gstin_certificate']) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i>Download Current</a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="pan_card" class="form-label">PAN Card</label>
                            <div class="file-upload-area" onclick="document.getElementById('pan_card').click()">
                                <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <p class="upload-text">Click to upload or drag and drop the file here. Supported format PDF, JPG, PNG</p>
                            </div>
                            <input type="file" class="form-control @error('pan_card') is-invalid @enderror" id="pan_card" name="pan_card" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" onchange="updateFileLabel('pan_card', this)">
                            <div class="helper-text">PDF, JPG, PNG files only (Max 10MB)</div>
                            @error('pan_card')<div class="text-danger small">{{ $message }}</div>@enderror
                            <div id="pan_card_label" class="mt-2 small text-muted" style="display: none;"></div>
                            @if($vendor->pan_card_path)
                                <div class="mt-2"><small class="text-muted">Current:</small>
                                    <a href="{{ route('business.vendors.download-document', [$vendor, 'pan_card']) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i>Download Current</a>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label for="additional_certificates" class="form-label">Additional Certificates</label>
                            <div class="file-upload-area" onclick="document.getElementById('additional_certificates').click()">
                                <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <p class="upload-text">Click to upload or drag and drop files here. Supported format PDF, JPG, PNG (Multiple files allowed)</p>
                            </div>
                            <input type="file" class="form-control @error('additional_certificates.*') is-invalid @enderror" id="additional_certificates" name="additional_certificates[]" accept=".pdf,.jpg,.jpeg,.png" multiple style="display: none;" onchange="updateFileLabel('additional_certificates', this)">
                            <div class="helper-text">PDF, JPG, PNG files only (Max 10MB each)</div>
                            @error('additional_certificates.*')<div class="text-danger small">{{ $message }}</div>@enderror
                            <div id="additional_certificates_label" class="mt-2 small text-muted" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions d-flex justify-content-end gap-2 nxz-action-buttons">
                <a href="{{ route('business.vendors.show', $vendor) }}" class="btn nxz-btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" class="btn nxz-btn-primary">
                    <i class="fas fa-save me-2"></i>Update Vendor
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePayoutFields() {
    const payoutMethod = document.getElementById('payout_method').value;
    const bankDetailsDiv = document.getElementById('bankDetailsDiv');
    const upiDetailsDiv = document.getElementById('upiDetailsDiv');
    const otherPayoutMethodDiv = document.getElementById('otherPayoutMethodDiv');
    bankDetailsDiv.style.display = 'none';
    upiDetailsDiv.style.display = 'none';
    otherPayoutMethodDiv.style.display = 'none';
    if (payoutMethod === 'bank_transfer') {
        bankDetailsDiv.style.display = 'block';
    } else if (payoutMethod === 'upi_payment') {
        upiDetailsDiv.style.display = 'block';
    } else if (payoutMethod === 'other') {
        otherPayoutMethodDiv.style.display = 'block';
    }
}

function togglePayoutSchedule() {
    const payoutFrequency = document.getElementById('payout_frequency').value;
    const payoutDayOfWeekDiv = document.getElementById('payoutDayOfWeekDiv');
    const payoutDayOfMonthDiv = document.getElementById('payoutDayOfMonthDiv');
    payoutDayOfWeekDiv.style.display = 'none';
    payoutDayOfMonthDiv.style.display = 'none';
    if (payoutFrequency === 'weekly' || payoutFrequency === 'bi_weekly') {
        payoutDayOfWeekDiv.style.display = 'block';
    } else if (payoutFrequency === 'monthly' || payoutFrequency === 'quarterly') {
        payoutDayOfMonthDiv.style.display = 'block';
    }
}

function toggleCommissionFields() {
    const commissionType = document.getElementById('commission_type').value;
    const commissionUnit = document.getElementById('commissionUnit');
    const leaseCommitmentSection = document.getElementById('leaseCommitmentSection');
    const leaseCommitmentMonths = document.getElementById('lease_commitment_months');
    if (commissionType === 'percentage_of_revenue') {
        commissionUnit.textContent = '%';
        leaseCommitmentSection.style.display = 'none';
        leaseCommitmentMonths.required = false;
    } else if (commissionType === 'per_booking_per_day') {
        commissionUnit.textContent = '₹/day';
        leaseCommitmentSection.style.display = 'none';
        leaseCommitmentMonths.required = false;
    } else if (commissionType === 'lease_to_rent') {
        commissionUnit.textContent = '₹';
        leaseCommitmentSection.style.display = 'block';
        leaseCommitmentMonths.required = true;
    } else {
        commissionUnit.textContent = '₹';
        leaseCommitmentSection.style.display = 'none';
        leaseCommitmentMonths.required = false;
    }
}

function addBranch() {
    const container = document.getElementById('additionalBranchesContainer');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" class="form-control" name="additional_branches[]" placeholder="Enter branch address">
        <button type="button" class="btn btn-outline-danger" onclick="removeBranch(this)"><i class=\"fas fa-trash\"></i></button>
    `;
    container.appendChild(div);
}
function removeBranch(button) { button.parentElement.remove(); }

document.addEventListener('DOMContentLoaded', function() {
    togglePayoutFields();
    togglePayoutSchedule();
    toggleCommissionFields();
});

// Update file label after selection (parity with create page)
function updateFileLabel(inputId, input) {
    const labelDiv = document.getElementById(inputId + '_label');
    if (!labelDiv) return;
    if (input.files && input.files.length > 0) {
        let labelText = '';
        if (input.files.length === 1) {
            labelText = `Selected: ${input.files[0].name}`;
        } else {
            labelText = `Selected: ${input.files.length} files`;
        }
        labelDiv.textContent = labelText;
        labelDiv.style.display = 'block';
    } else {
        labelDiv.style.display = 'none';
    }
}
</script>
@endpush

