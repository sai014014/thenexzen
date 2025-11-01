@php
    $customer = $customer ?? null;
@endphp

<!-- Basic Customer Information -->
<div class="form-section">
    <h3 class="section-title">Basic Customer Information</h3>
    <div class="row">
    <div class="col-md-6 mb-3">
        <label for="full_name" class="form-label">Full Name *</label>
        <input type="text" class="form-control" id="full_name" name="full_name" value="{{ old('full_name', $customer && $customer->full_name ? $customer->full_name : '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="mobile_number" class="form-label">Mobile Number *</label>
        <input type="tel" class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $customer && $customer->mobile_number ? $customer->mobile_number : '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="alternate_contact_number" class="form-label">Alternate Contact Number</label>
        <input type="tel" class="form-control" id="alternate_contact_number" name="alternate_contact_number" value="{{ old('alternate_contact_number', $customer && $customer->alternate_contact_number ? $customer->alternate_contact_number : '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="email_address" class="form-label">Email Address</label>
        <input type="email" class="form-control" id="email_address" name="email_address" value="{{ old('email_address', $customer && $customer->email_address ? $customer->email_address : '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="date_of_birth" class="form-label">Date of Birth *</label>
        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $customer && $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '') }}" required>
        <div class="form-text">Must be 18 years or older</div>
    </div>
    </div>
</div>

<!-- Address Information -->
<div class="form-section">
    <h3 class="section-title">Address Information</h3>
    <div class="row">
    <div class="col-12 mb-3">
        <label for="permanent_address" class="form-label">Permanent Address *</label>
        <textarea class="form-control" id="permanent_address" name="permanent_address" rows="3" required>{{ old('permanent_address', $customer && $customer->permanent_address ? $customer->permanent_address : '') }}</textarea>
    </div>
    <div class="col-12 mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="same_as_permanent" name="same_as_permanent" value="1" {{ old('same_as_permanent', $customer && $customer->same_as_permanent ? $customer->same_as_permanent : false) ? 'checked' : '' }}>
            <label class="form-check-label" for="same_as_permanent">
                Same as Permanent Address
            </label>
        </div>
    </div>
    <div class="col-12 mb-3">
        <label for="current_address" class="form-label">Current Address</label>
        <textarea class="form-control" id="current_address" name="current_address" rows="3">{{ old('current_address', $customer && $customer->current_address ? $customer->current_address : '') }}        </textarea>
    </div>
    </div>
</div>

<!-- Identity Information -->
<div class="form-section">
    <h3 class="section-title">Identity Information</h3>
    <div class="row">
    <div class="col-md-6 mb-3">
        <label for="government_id_type" class="form-label">Government ID Type *</label>
        <select class="form-select" id="government_id_type" name="government_id_type" required>
            <option value="">Select ID Type</option>
            <option value="aadhar_card" {{ old('government_id_type', $customer && $customer->government_id_type ? $customer->government_id_type : '') == 'aadhar_card' ? 'selected' : '' }}>Aadhar Card</option>
            <option value="passport" {{ old('government_id_type', $customer && $customer->government_id_type ? $customer->government_id_type : '') == 'passport' ? 'selected' : '' }}>Passport</option>
            <option value="pan_card" {{ old('government_id_type', $customer && $customer->government_id_type ? $customer->government_id_type : '') == 'pan_card' ? 'selected' : '' }}>PAN Card</option>
            <option value="voter_id" {{ old('government_id_type', $customer && $customer->government_id_type ? $customer->government_id_type : '') == 'voter_id' ? 'selected' : '' }}>Voter ID</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="government_id_number" class="form-label">Government ID Number *</label>
        <input type="text" class="form-control" id="government_id_number" name="government_id_number" value="{{ old('government_id_number', $customer && $customer->government_id_number ? $customer->government_id_number : '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="driving_license_number" class="form-label">Driving License Number *</label>
        <input type="text" class="form-control" id="driving_license_number" name="driving_license_number" value="{{ old('driving_license_number', $customer && $customer->driving_license_number ? $customer->driving_license_number : '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="license_expiry_date" class="form-label">License Expiry Date</label>
        <input type="date" class="form-control" id="license_expiry_date" name="license_expiry_date" value="{{ old('license_expiry_date', $customer && $customer->license_expiry_date ? $customer->license_expiry_date->format('Y-m-d') : '') }}">
    </div>
    <div class="col-12 mb-3">
        <div class="form-group">
            <label for="driving_license" class="form-label">Upload Driving License</label>
            <div class="file-upload-area" onclick="document.getElementById('driving_license').click()">
                <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <p class="upload-text">Click to upload or drag and drop the file here. Supported format PDF, JPG, PNG</p>
            </div>
            <input type="file" class="form-control @error('driving_license') is-invalid @enderror" id="driving_license" name="driving_license" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" onchange="updateFileLabel('driving_license', this)">
            <div class="helper-text">PDF, JPG, PNG files only. Max size: 10MB</div>
            @error('driving_license')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
            <div id="driving_license_label" class="mt-2 small text-muted" style="display: none;"></div>
            @if(isset($customer) && $customer && $customer->driving_license_path)
            <div class="mt-2">
                <small class="text-muted">Current file: </small>
                <a href="{{ route('business.customers.view-document', ['customer' => $customer, 'type' => 'driving_license']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye me-1"></i>View Current
                </a>
            </div>
            @endif
        </div>
    </div>
    </div>
</div>

<!-- Additional Contact Information -->
<div class="form-section">
    <h3 class="section-title">Additional Contact Information</h3>
    <div class="row">
    <div class="col-md-6 mb-3">
        <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
        <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $customer && $customer->emergency_contact_name ? $customer->emergency_contact_name : '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label for="emergency_contact_number" class="form-label">Emergency Contact Number</label>
        <input type="tel" class="form-control" id="emergency_contact_number" name="emergency_contact_number" value="{{ old('emergency_contact_number', $customer && $customer->emergency_contact_number ? $customer->emergency_contact_number : '') }}">
    </div>
    <div class="col-12 mb-3">
        <label for="additional_information" class="form-label">Additional Information</label>
            <textarea class="form-control" id="additional_information" name="additional_information" rows="3" placeholder="Any additional comments, notes, or instructions...">{{ old('additional_information', $customer && $customer->additional_information ? $customer->additional_information : '') }}</textarea>
    </div>
    </div>
</div>

<script>
function updateFileLabel(inputId, input) {
    const labelDiv = document.getElementById(inputId + '_label');
    if (input.files && input.files.length > 0) {
        let labelText = `Selected: ${input.files[0].name}`;
        if (labelDiv) {
            labelDiv.textContent = labelText;
            labelDiv.style.display = 'block';
        }
    } else {
        if (labelDiv) {
            labelDiv.style.display = 'none';
        }
    }
}
</script>
