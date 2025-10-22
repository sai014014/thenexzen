@php
    $customer = $customer ?? null;
@endphp

<!-- Company Information -->
<div class="row mb-4">
    <div class="col-12">
        <h6 class="text-primary border-bottom pb-2 mb-3">
            <i class="fas fa-building me-2"></i>Company Information
        </h6>
    </div>
    <div class="col-md-6 mb-3">
        <label for="company_name" class="form-label">Company Name *</label>
        <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $customer && $customer->company_name ? $customer->company_name : '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="company_type" class="form-label">Company Type *</label>
        <select class="form-select" id="company_type" name="company_type" required>
            <option value="">Select Company Type</option>
            <option value="private_limited" {{ old('company_type', $customer && $customer->company_type ? $customer->company_type : '') == 'private_limited' ? 'selected' : '' }}>Private Limited</option>
            <option value="public_limited" {{ old('company_type', $customer && $customer->company_type ? $customer->company_type : '') == 'public_limited' ? 'selected' : '' }}>Public Limited</option>
            <option value="partnership" {{ old('company_type', $customer && $customer->company_type ? $customer->company_type : '') == 'partnership' ? 'selected' : '' }}>Partnership</option>
            <option value="proprietorship" {{ old('company_type', $customer && $customer->company_type ? $customer->company_type : '') == 'proprietorship' ? 'selected' : '' }}>Proprietorship</option>
            <option value="llp" {{ old('company_type', $customer && $customer->company_type ? $customer->company_type : '') == 'llp' ? 'selected' : '' }}>LLP</option>
            <option value="ngo" {{ old('company_type', $customer && $customer->company_type ? $customer->company_type : '') == 'ngo' ? 'selected' : '' }}>NGO</option>
            <option value="other" {{ old('company_type', $customer && $customer->company_type ? $customer->company_type : '') == 'other' ? 'selected' : '' }}>Other</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="gstin" class="form-label">GSTIN (GST Identification Number)</label>
        <input type="text" class="form-control" id="gstin" name="gstin" value="{{ old('gstin', $customer && $customer->gstin ? $customer->gstin : '') }}" maxlength="15">
        <div class="form-text">15-digit GSTIN (if applicable)</div>
    </div>
    <div class="col-md-6 mb-3">
        <label for="pan_number" class="form-label">PAN Number</label>
        <input type="text" class="form-control" id="pan_number" name="pan_number" value="{{ old('pan_number', $customer && $customer->pan_number ? $customer->pan_number : '') }}" maxlength="10">
        <div class="form-text">10-digit PAN number</div>
    </div>
    <div class="col-12 mb-3">
        <label for="company_address" class="form-label">Company Address *</label>
        <textarea class="form-control" id="company_address" name="company_address" rows="3" required>{{ old('company_address', $customer && $customer->company_address ? $customer->company_address : '') }}</textarea>
    </div>
</div>

<!-- Primary Contact Person -->
<div class="row mb-4">
    <div class="col-12">
        <h6 class="text-primary border-bottom pb-2 mb-3">
            <i class="fas fa-user-tie me-2"></i>Primary Contact Person
        </h6>
    </div>
    <div class="col-md-6 mb-3">
        <label for="contact_person_name" class="form-label">Contact Person Name *</label>
        <input type="text" class="form-control" id="contact_person_name" name="contact_person_name" value="{{ old('contact_person_name', $customer && $customer->contact_person_name ? $customer->contact_person_name : '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="designation" class="form-label">Designation *</label>
        <input type="text" class="form-control" id="designation" name="designation" value="{{ old('designation', $customer && $customer->designation ? $customer->designation : '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="official_email" class="form-label">Official Email Address *</label>
        <input type="email" class="form-control" id="official_email" name="official_email" value="{{ old('official_email', $customer && $customer->official_email ? $customer->official_email : '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="contact_person_mobile" class="form-label">Mobile Number *</label>
        <input type="tel" class="form-control" id="contact_person_mobile" name="contact_person_mobile" value="{{ old('contact_person_mobile', $customer && $customer->contact_person_mobile ? $customer->contact_person_mobile : '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="contact_person_alternate" class="form-label">Alternate Contact Number</label>
        <input type="tel" class="form-control" id="contact_person_alternate" name="contact_person_alternate" value="{{ old('contact_person_alternate', $customer && $customer->contact_person_alternate ? $customer->contact_person_alternate : '') }}">
    </div>
</div>

<!-- Authorized Driver(s) Information -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="text-primary border-bottom pb-2 mb-0">
                <i class="fas fa-car me-2"></i>Authorized Driver(s) Information
            </h6>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addDriver()">
                <i class="fas fa-plus me-2"></i>Add Driver
            </button>
        </div>
    </div>
    <div class="col-12" id="driversContainer">
        @if(isset($customer) && $customer && $customer->corporateDrivers->count() > 0)
            @foreach($customer->corporateDrivers as $index => $driver)
            <div class="driver-row" data-driver-index="{{ $index + 1 }}">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Driver {{ $index + 1 }}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDriver({{ $index + 1 }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Driver's Name *</label>
                                <input type="text" class="form-control" name="drivers[{{ $index + 1 }}][driver_name]" value="{{ $driver->driver_name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Driving License Number *</label>
                                <input type="text" class="form-control" name="drivers[{{ $index + 1 }}][driving_license_number]" value="{{ $driver->driving_license_number }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">License Expiry Date *</label>
                                <input type="date" class="form-control" name="drivers[{{ $index + 1 }}][license_expiry_date]" value="{{ $driver->license_expiry_date->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Driving License</label>
                                <input type="file" class="form-control" name="drivers[{{ $index + 1 }}][driving_license_file]" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text">PDF, JPG, PNG files only. Max size: 10MB</div>
                                @if($driver->driving_license_path)
                                <div class="mt-2">
                                    <small class="text-muted">Current file: </small>
                                    <a href="{{ route('business.customers.download-driver-document', ['customer' => $customer, 'driver' => $driver, 'type' => 'driving_license']) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>Download Current
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <!-- Driver 1 (Default) -->
            <div class="driver-row" data-driver-index="1">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Driver 1</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDriver(1)" style="display: none;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Driver's Name *</label>
                                <input type="text" class="form-control" name="drivers[1][driver_name]" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Driving License Number *</label>
                                <input type="text" class="form-control" name="drivers[1][driving_license_number]" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">License Expiry Date *</label>
                                <input type="date" class="form-control" name="drivers[1][license_expiry_date]" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Driving License</label>
                                <input type="file" class="form-control" name="drivers[1][driving_license_file]" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text">PDF, JPG, PNG files only. Max size: 10MB</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Invoicing and Payment Preferences -->
<div class="row mb-4">
    <div class="col-12">
        <h6 class="text-primary border-bottom pb-2 mb-3">
            <i class="fas fa-file-invoice me-2"></i>Invoicing and Payment Preferences
        </h6>
    </div>
    <div class="col-md-6 mb-3">
        <label for="billing_name" class="form-label">Billing Name *</label>
        <input type="text" class="form-control" id="billing_name" name="billing_name" value="{{ old('billing_name', $customer && $customer->billing_name ? $customer->billing_name : '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label for="billing_email" class="form-label">Billing Email *</label>
        <input type="email" class="form-control" id="billing_email" name="billing_email" value="{{ old('billing_email', $customer && $customer->billing_email ? $customer->billing_email : '') }}" required>
    </div>
    <div class="col-12 mb-3">
        <label for="billing_address" class="form-label">Billing Address *</label>
        <textarea class="form-control" id="billing_address" name="billing_address" rows="3" required>{{ old('billing_address', $customer && $customer->billing_address ? $customer->billing_address : '') }}</textarea>
    </div>
    <div class="col-md-6 mb-3">
        <label for="preferred_payment_method" class="form-label">Preferred Payment Method *</label>
        <select class="form-select" id="preferred_payment_method" name="preferred_payment_method" required>
            <option value="">Select Payment Method</option>
            <option value="bank_transfer" {{ old('preferred_payment_method', $customer && $customer->preferred_payment_method ? $customer->preferred_payment_method : '') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer (NEFT/RTGS)</option>
            <option value="upi" {{ old('preferred_payment_method', $customer && $customer->preferred_payment_method ? $customer->preferred_payment_method : '') == 'upi' ? 'selected' : '' }}>UPI</option>
            <option value="corporate_credit_card" {{ old('preferred_payment_method', $customer && $customer->preferred_payment_method ? $customer->preferred_payment_method : '') == 'corporate_credit_card' ? 'selected' : '' }}>Corporate Credit Card</option>
            <option value="cheque_payment" {{ old('preferred_payment_method', $customer && $customer->preferred_payment_method ? $customer->preferred_payment_method : '') == 'cheque_payment' ? 'selected' : '' }}>Cheque Payment</option>
            <option value="cash" {{ old('preferred_payment_method', $customer && $customer->preferred_payment_method ? $customer->preferred_payment_method : '') == 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="card" {{ old('preferred_payment_method', $customer && $customer->preferred_payment_method ? $customer->preferred_payment_method : '') == 'card' ? 'selected' : '' }}>Card</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="invoice_frequency" class="form-label">Invoice Frequency *</label>
        <select class="form-select" id="invoice_frequency" name="invoice_frequency" required>
            <option value="">Select Frequency</option>
            <option value="weekly" {{ old('invoice_frequency', $customer && $customer->invoice_frequency ? $customer->invoice_frequency : '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
            <option value="monthly" {{ old('invoice_frequency', $customer && $customer->invoice_frequency ? $customer->invoice_frequency : '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
        </select>
    </div>
</div>

<!-- Additional Information -->
<div class="row mb-4">
    <div class="col-12">
        <h6 class="text-primary border-bottom pb-2 mb-3">
            <i class="fas fa-info-circle me-2"></i>Additional Information
        </h6>
    </div>
    <div class="col-12 mb-3">
        <label for="additional_information" class="form-label">Remarks/Additional Notes</label>
        <textarea class="form-control" id="additional_information" name="additional_information" rows="3" placeholder="Any other requests or requirements...">{{ old('additional_information', $customer && $customer->additional_information ? $customer->additional_information : '') }}</textarea>
    </div>
</div>
