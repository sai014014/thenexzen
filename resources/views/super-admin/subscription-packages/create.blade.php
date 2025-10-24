@extends('super-admin.layouts.app')

@section('title', $subscriptionPackage ? 'Edit Subscription Package' : 'Create Subscription Package')
@section('page-title', $subscriptionPackage ? 'Edit Package' : 'Create New Package')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ $subscriptionPackage ? route('super-admin.subscription-packages.update', $subscriptionPackage) : route('super-admin.subscription-packages.store') }}">
                @csrf
                @if($subscriptionPackage)
                    @method('PUT')
                @endif

                <!-- Package Details Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">A. Package Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="package_name" class="form-label">1️⃣ Package Name</label>
                                    <input type="text" class="form-control @error('package_name') is-invalid @enderror" 
                                           id="package_name" name="package_name" 
                                           value="{{ old('package_name', $subscriptionPackage->package_name ?? '') }}" 
                                           placeholder="e.g., Starter, Pro, Max" required>
                                    @error('package_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subscription_fee" class="form-label">2️⃣ Package Price</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('subscription_fee') is-invalid @enderror" 
                                               id="subscription_fee" name="subscription_fee" 
                                               value="{{ old('subscription_fee', $subscriptionPackage->subscription_fee ?? '') }}" 
                                               step="0.01" min="0" required>
                                        <select class="form-select @error('currency') is-invalid @enderror" name="currency" style="max-width: 100px;">
                                            <option value="INR" {{ old('currency', $subscriptionPackage->currency ?? 'INR') == 'INR' ? 'selected' : '' }}>₹</option>
                                            <option value="USD" {{ old('currency', $subscriptionPackage->currency ?? '') == 'USD' ? 'selected' : '' }}>$</option>
                                            <option value="EUR" {{ old('currency', $subscriptionPackage->currency ?? '') == 'EUR' ? 'selected' : '' }}>€</option>
                                        </select>
                                    </div>
                                    <small class="form-text text-muted">per month / year</small>
                                    @error('subscription_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="trial_period_days" class="form-label">3️⃣ Trial Period (Days)</label>
                                    <input type="number" class="form-control @error('trial_period_days') is-invalid @enderror" 
                                           id="trial_period_days" name="trial_period_days" 
                                           value="{{ old('trial_period_days', $subscriptionPackage->trial_period_days ?? 14) }}" 
                                           min="0" max="365" required>
                                    <small class="form-text text-muted">Default: 14 days</small>
                                    @error('trial_period_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="onboarding_fee" class="form-label">4️⃣ Onboarding Fee</label>
                                    <input type="number" class="form-control @error('onboarding_fee') is-invalid @enderror" 
                                           id="onboarding_fee" name="onboarding_fee" 
                                           value="{{ old('onboarding_fee', $subscriptionPackage->onboarding_fee ?? 6000) }}" 
                                           step="0.01" min="0" required>
                                    <small class="form-text text-muted">Default: 6000</small>
                                    @error('onboarding_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="vehicle_capacity" class="form-label">5️⃣ Vehicle Capacity</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('vehicle_capacity') is-invalid @enderror" 
                                               id="vehicle_capacity" name="vehicle_capacity" 
                                               value="{{ old('vehicle_capacity', $subscriptionPackage->vehicle_capacity ?? '') }}" 
                                               min="1" {{ old('is_unlimited_vehicles', $subscriptionPackage->is_unlimited_vehicles ?? false) ? 'disabled' : '' }}>
                                        <div class="form-check form-switch ms-3">
                                            <input class="form-check-input" type="checkbox" id="is_unlimited_vehicles" 
                                                   name="is_unlimited_vehicles" value="1" 
                                                   {{ old('is_unlimited_vehicles', $subscriptionPackage->is_unlimited_vehicles ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_unlimited_vehicles">
                                                Unlimited
                                            </label>
                                        </div>
                                    </div>
                                    @error('vehicle_capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features & Functionalities Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">B. Features & Functionalities</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="booking_management" 
                                           name="booking_management" value="1" 
                                           {{ old('booking_management', $subscriptionPackage->booking_management ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="booking_management">
                                        ✅ Booking Management
                                    </label>
                                    <small class="form-text text-muted d-block">Default: Yes</small>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="customer_management" 
                                           name="customer_management" value="1" 
                                           {{ old('customer_management', $subscriptionPackage->customer_management ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="customer_management">
                                        ✅ Customer Management
                                    </label>
                                    <small class="form-text text-muted d-block">Default: Yes</small>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="vehicle_management" 
                                           name="vehicle_management" value="1" 
                                           {{ old('vehicle_management', $subscriptionPackage->vehicle_management ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="vehicle_management">
                                        ✅ Vehicle Management
                                    </label>
                                    <small class="form-text text-muted d-block">Default: Yes</small>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="basic_reporting" 
                                           name="basic_reporting" value="1" 
                                           {{ old('basic_reporting', $subscriptionPackage->basic_reporting ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="basic_reporting">
                                        ✅ Basic Reporting Features
                                    </label>
                                    <small class="form-text text-muted d-block">Default: Yes</small>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="advanced_reporting" 
                                           name="advanced_reporting" value="1" 
                                           {{ old('advanced_reporting', $subscriptionPackage->advanced_reporting ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="advanced_reporting">
                                        ✅ Advanced Reporting & Analytics
                                    </label>
                                    <small class="form-text text-muted d-block">Available in Pro & Max only</small>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="vendor_management" 
                                           name="vendor_management" value="1" 
                                           {{ old('vendor_management', $subscriptionPackage->vendor_management ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="vendor_management">
                                        ✅ Vendor Management
                                    </label>
                                    <small class="form-text text-muted d-block">Available in Pro & Max only</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="maintenance_reminders" 
                                           name="maintenance_reminders" value="1" 
                                           {{ old('maintenance_reminders', $subscriptionPackage->maintenance_reminders ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maintenance_reminders">
                                        ✅ Vehicle Maintenance Reminders
                                    </label>
                                    <small class="form-text text-muted d-block">Default: Yes</small>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="customization_options" 
                                           name="customization_options" value="1" 
                                           {{ old('customization_options', $subscriptionPackage->customization_options ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="customization_options">
                                        ✅ Customization Options
                                    </label>
                                    <small class="form-text text-muted d-block">Available in Max only</small>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="multi_user_access" 
                                           name="multi_user_access" value="1" 
                                           {{ old('multi_user_access', $subscriptionPackage->multi_user_access ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="multi_user_access">
                                        ✅ Multi-User Access & Role-Based Permissions
                                    </label>
                                    <small class="form-text text-muted d-block">Available in Max only</small>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="dedicated_account_manager" 
                                           name="dedicated_account_manager" value="1" 
                                           {{ old('dedicated_account_manager', $subscriptionPackage->dedicated_account_manager ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="dedicated_account_manager">
                                        ✅ Dedicated Account Manager
                                    </label>
                                    <small class="form-text text-muted d-block">Available in Max only</small>
                                </div>

                                <div class="mb-3">
                                    <label for="support_type" class="form-label">✅ 24/7 Support Type</label>
                                    <select class="form-select @error('support_type') is-invalid @enderror" id="support_type" name="support_type" required>
                                        <option value="standard" {{ old('support_type', $subscriptionPackage->support_type ?? 'standard') == 'standard' ? 'selected' : '' }}>Standard</option>
                                        <option value="chat_only" {{ old('support_type', $subscriptionPackage->support_type ?? '') == 'chat_only' ? 'selected' : '' }}>Chat Only</option>
                                        <option value="full_support" {{ old('support_type', $subscriptionPackage->support_type ?? '') == 'full_support' ? 'selected' : '' }}>Full Support</option>
                                        <option value="enterprise_level" {{ old('support_type', $subscriptionPackage->support_type ?? '') == 'enterprise_level' ? 'selected' : '' }}>Enterprise-Level</option>
                                    </select>
                                    @error('support_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subscription Settings Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">C. Subscription Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Billing Cycle Options</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="billing_monthly" 
                                               name="billing_cycles[]" value="monthly" 
                                               {{ in_array('monthly', old('billing_cycles', $subscriptionPackage->billing_cycles ?? ['monthly'])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="billing_monthly">Monthly</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="billing_quarterly" 
                                               name="billing_cycles[]" value="quarterly" 
                                               {{ in_array('quarterly', old('billing_cycles', $subscriptionPackage->billing_cycles ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="billing_quarterly">Quarterly</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="billing_yearly" 
                                               name="billing_cycles[]" value="yearly" 
                                               {{ in_array('yearly', old('billing_cycles', $subscriptionPackage->billing_cycles ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="billing_yearly">Yearly</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="billing_custom" 
                                               name="billing_cycles[]" value="custom" 
                                               {{ in_array('custom', old('billing_cycles', $subscriptionPackage->billing_cycles ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="billing_custom">Custom</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Payment Methods</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="payment_direct_debit" 
                                               name="payment_methods[]" value="direct_debit" 
                                               {{ in_array('direct_debit', old('payment_methods', $subscriptionPackage->payment_methods ?? ['direct_debit'])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="payment_direct_debit">Direct Debit</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="payment_credit_card" 
                                               name="payment_methods[]" value="credit_card" 
                                               {{ in_array('credit_card', old('payment_methods', $subscriptionPackage->payment_methods ?? ['credit_card'])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="payment_credit_card">Credit Card</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="payment_bank_transfer" 
                                               name="payment_methods[]" value="bank_transfer" 
                                               {{ in_array('bank_transfer', old('payment_methods', $subscriptionPackage->payment_methods ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="payment_bank_transfer">Bank Transfer</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="payment_cash" 
                                               name="payment_methods[]" value="cash" 
                                               {{ in_array('cash', old('payment_methods', $subscriptionPackage->payment_methods ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="payment_cash">Cash</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Renewal Type</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="renewal_auto" 
                                               name="renewal_type" value="auto_renew" 
                                               {{ old('renewal_type', $subscriptionPackage->renewal_type ?? 'auto_renew') == 'auto_renew' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="renewal_auto">Auto-Renew</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="renewal_manual" 
                                               name="renewal_type" value="manual_renewal" 
                                               {{ old('renewal_type', $subscriptionPackage->renewal_type ?? '') == 'manual_renewal' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="renewal_manual">Manual Renewal</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Package Availability Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">D. Package Availability</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="draft" {{ old('status', $subscriptionPackage->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="active" {{ old('status', $subscriptionPackage->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $subscriptionPackage->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Visibility</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="show_on_website" 
                                               name="show_on_website" value="1" 
                                               {{ old('show_on_website', $subscriptionPackage->show_on_website ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_on_website">Show on Website</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="internal_use_only" 
                                               name="internal_use_only" value="1" 
                                               {{ old('internal_use_only', $subscriptionPackage->internal_use_only ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="internal_use_only">Internal Use Only</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Brief description of the package...">{{ old('description', $subscriptionPackage->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="features_summary" class="form-label">Features Summary</label>
                                    <textarea class="form-control @error('features_summary') is-invalid @enderror" 
                                              id="features_summary" name="features_summary" rows="3" 
                                              placeholder="Summary of key features...">{{ old('features_summary', $subscriptionPackage->features_summary ?? '') }}</textarea>
                                    @error('features_summary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="card">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-save"></i> {{ $subscriptionPackage ? 'Update Package' : 'Save Package' }}
                        </button>
                        <a href="{{ route('super-admin.subscription-packages.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Preview Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Package Preview</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="package-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                    <h4 id="preview-name" class="text-center mb-3">Package Name</h4>
                    <div class="text-center mb-3">
                        <h3 id="preview-price" class="text-primary">₹ 0.00</h3>
                        <small class="text-muted">per month</small>
                    </div>
                    <div class="mb-3">
                        <h6>Features:</h6>
                        <ul id="preview-features" class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Basic Features</li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <h6>Vehicle Capacity:</h6>
                        <span id="preview-capacity" class="badge bg-info">Unlimited</span>
                    </div>
                    <div class="mb-3">
                        <h6>Status:</h6>
                        <span id="preview-status" class="badge bg-secondary">Draft</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle unlimited vehicles checkbox
    const unlimitedCheckbox = document.getElementById('is_unlimited_vehicles');
    const vehicleCapacityInput = document.getElementById('vehicle_capacity');
    
    unlimitedCheckbox.addEventListener('change', function() {
        if (this.checked) {
            vehicleCapacityInput.disabled = true;
            vehicleCapacityInput.value = '';
        } else {
            vehicleCapacityInput.disabled = false;
        }
        updatePreview();
    });

    // Update preview on form changes
    const formInputs = document.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        input.addEventListener('change', updatePreview);
        input.addEventListener('input', updatePreview);
    });

    function updatePreview() {
        // Update package name
        const packageName = document.getElementById('package_name').value || 'Package Name';
        document.getElementById('preview-name').textContent = packageName;

        // Update price
        const price = document.getElementById('subscription_fee').value || '0';
        const currency = document.querySelector('select[name="currency"]').value;
        const currencySymbol = currency === 'INR' ? '₹' : currency === 'USD' ? '$' : '€';
        document.getElementById('preview-price').textContent = currencySymbol + ' ' + parseFloat(price).toFixed(2);

        // Update vehicle capacity
        const isUnlimited = document.getElementById('is_unlimited_vehicles').checked;
        const capacity = document.getElementById('vehicle_capacity').value;
        const capacityText = isUnlimited ? 'Unlimited' : (capacity || '0');
        document.getElementById('preview-capacity').textContent = capacityText;

        // Update status
        const status = document.getElementById('status').value;
        const statusElement = document.getElementById('preview-status');
        statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        statusElement.className = `badge bg-${status === 'active' ? 'success' : status === 'inactive' ? 'danger' : 'secondary'}`;

        // Update features
        const features = [];
        const featureCheckboxes = document.querySelectorAll('input[type="checkbox"][name$="_management"], input[type="checkbox"][name$="_reporting"], input[type="checkbox"][name$="_reminders"], input[type="checkbox"][name$="_options"], input[type="checkbox"][name$="_access"], input[type="checkbox"][name$="_manager"]');
        
        featureCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const label = checkbox.nextElementSibling.textContent.replace('✅ ', '');
                features.push(label);
            }
        });

        const featuresList = document.getElementById('preview-features');
        featuresList.innerHTML = '';
        if (features.length > 0) {
            features.forEach(feature => {
                const li = document.createElement('li');
                li.innerHTML = `<i class="fas fa-check text-success me-2"></i>${feature}`;
                featuresList.appendChild(li);
            });
        } else {
            featuresList.innerHTML = '<li class="text-muted">No features selected</li>';
        }
    }

    // Initial preview update
    updatePreview();
});
</script>

<style>
.package-icon {
    font-size: 24px;
}

.form-check-label {
    font-weight: 500;
}

.card-header h5 {
    color: #495057;
    font-weight: 600;
}

.form-label {
    font-weight: 500;
    color: #495057;
}

.btn-lg {
    padding: 12px 30px;
    font-size: 16px;
}
</style>
@endsection
