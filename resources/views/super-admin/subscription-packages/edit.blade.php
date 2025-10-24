@extends('super-admin.layouts.app')

@section('title', 'Edit Subscription Package')
@section('page-title', 'Edit Package')

@section('content')
<div class="container-fluid">
    @if($subscriptionPackage->active_business_subscriptions_count > 0)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Warning:</strong> This package is currently being used by <strong>{{ $subscriptionPackage->active_business_subscriptions_count }}</strong> active business(es). 
        Changes to this package will affect all subscribed businesses immediately.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('super-admin.subscription-packages.update', $subscriptionPackage) }}">
                @csrf
                @method('PUT')

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
                                           value="{{ old('package_name', $subscriptionPackage->package_name) }}" 
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
                                               value="{{ old('subscription_fee', $subscriptionPackage->subscription_fee) }}" 
                                               step="0.01" min="0" required>
                                        <select class="form-select @error('currency') is-invalid @enderror" name="currency" style="max-width: 100px;">
                                            <option value="INR" {{ old('currency', $subscriptionPackage->currency) == 'INR' ? 'selected' : '' }}>₹</option>
                                            <option value="USD" {{ old('currency', $subscriptionPackage->currency) == 'USD' ? 'selected' : '' }}>$</option>
                                            <option value="EUR" {{ old('currency', $subscriptionPackage->currency) == 'EUR' ? 'selected' : '' }}>€</option>
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
                                           value="{{ old('trial_period_days', $subscriptionPackage->trial_period_days) }}" 
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
                                           value="{{ old('onboarding_fee', $subscriptionPackage->onboarding_fee) }}" 
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
                                               value="{{ old('vehicle_capacity', $subscriptionPackage->vehicle_capacity) }}" 
                                               min="1" {{ $subscriptionPackage->is_unlimited_vehicles ? 'disabled' : '' }}>
                                        <div class="form-check form-switch ms-3">
                                            <input class="form-check-input" type="checkbox" id="is_unlimited_vehicles" 
                                                   name="is_unlimited_vehicles" value="1" 
                                                   {{ $subscriptionPackage->is_unlimited_vehicles ? 'checked' : '' }}>
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

                <!-- Module Management Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">B. Module Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h6>Enable Modules for this Package</h6>
                                <p class="text-muted">Select which modules businesses can access with this subscription package.</p>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="module_vehicles" 
                                                   name="enabled_modules[]" value="vehicles" 
                                                   {{ in_array('vehicles', old('enabled_modules', $subscriptionPackage->enabled_modules ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="module_vehicles">
                                                🚗 Vehicle Management
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="module_bookings" 
                                                   name="enabled_modules[]" value="bookings" 
                                                   {{ in_array('bookings', old('enabled_modules', $subscriptionPackage->enabled_modules ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="module_bookings">
                                                📅 Booking Management
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="module_customers" 
                                                   name="enabled_modules[]" value="customers" 
                                                   {{ in_array('customers', old('enabled_modules', $subscriptionPackage->enabled_modules ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="module_customers">
                                                👥 Customer Management
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="module_reports" 
                                                   name="enabled_modules[]" value="reports" 
                                                   {{ in_array('reports', old('enabled_modules', $subscriptionPackage->enabled_modules ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="module_reports">
                                                📊 Reports & Analytics
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="module_notifications" 
                                                   name="enabled_modules[]" value="notifications" 
                                                   {{ in_array('notifications', old('enabled_modules', $subscriptionPackage->enabled_modules ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="module_notifications">
                                                🔔 Notifications
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="module_vendors" 
                                                   name="enabled_modules[]" value="vendors" 
                                                   {{ in_array('vendors', old('enabled_modules', $subscriptionPackage->enabled_modules ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="module_vendors">
                                                🏢 Vendor Management
                                            </label>
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="module_subscription" 
                                                   name="enabled_modules[]" value="subscription" 
                                                   {{ in_array('subscription', old('enabled_modules', $subscriptionPackage->enabled_modules ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="module_subscription">
                                                💳 Subscription Management
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Package Availability Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">C. Package Availability</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="draft" {{ $subscriptionPackage->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="active" {{ $subscriptionPackage->status == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $subscriptionPackage->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="Brief description of the package...">{{ old('description', $subscriptionPackage->description) }}</textarea>
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
                                              placeholder="Summary of key features...">{{ old('features_summary', $subscriptionPackage->features_summary) }}</textarea>
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
                            <i class="fas fa-save"></i> Update Package
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
                    <h4 id="preview-name" class="text-center mb-3">{{ $subscriptionPackage->package_name }}</h4>
                    <div class="text-center mb-3">
                        <h3 id="preview-price" class="text-primary">{{ $subscriptionPackage->formatted_price }}</h3>
                        <small class="text-muted">per month</small>
                    </div>
                    <div class="mb-3">
                        <h6>Features:</h6>
                        <ul id="preview-features" class="list-unstyled">
                            @foreach($subscriptionPackage->enabled_modules ?? [] as $module)
                                <li><i class="fas fa-check text-success me-2"></i>{{ ucfirst(str_replace('_', ' ', $module)) }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mb-3">
                        <h6>Vehicle Capacity:</h6>
                        <span id="preview-capacity" class="badge bg-info">{{ $subscriptionPackage->vehicle_capacity_display }}</span>
                    </div>
                    <div class="mb-3">
                        <h6>Status:</h6>
                        <span id="preview-status" class="badge bg-{{ $subscriptionPackage->status === 'active' ? 'success' : ($subscriptionPackage->status === 'inactive' ? 'danger' : 'secondary') }}">{{ ucfirst($subscriptionPackage->status) }}</span>
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
