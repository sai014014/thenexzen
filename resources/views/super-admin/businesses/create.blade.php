@extends('super-admin.layouts.app')

@section('title', 'Create Business - The NexZen Super Admin')
@section('page-title', 'Create New Business')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Add New Business
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('super-admin.businesses.store') }}" id="businessForm">
                    @csrf
                    
                    <!-- Business Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-building me-2"></i>Business Information
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="business_name" class="form-label">Business Name *</label>
                            <input type="text" 
                                   class="form-control @error('business_name') is-invalid @enderror" 
                                   id="business_name" 
                                   name="business_name" 
                                   value="{{ old('business_name') }}" 
                                   required>
                            @error('business_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="business_type" class="form-label">Business Type *</label>
                            <select class="form-select @error('business_type') is-invalid @enderror" 
                                    id="business_type" 
                                    name="business_type" 
                                    required>
                                <option value="">Select Type</option>
                                <option value="car_dealership" {{ old('business_type') == 'car_dealership' ? 'selected' : '' }}>Car Dealership</option>
                                <option value="car_rental" {{ old('business_type') == 'car_rental' ? 'selected' : '' }}>Car Rental</option>
                                <option value="car_service" {{ old('business_type') == 'car_service' ? 'selected' : '' }}>Car Service</option>
                                <option value="car_insurance" {{ old('business_type') == 'car_insurance' ? 'selected' : '' }}>Car Insurance</option>
                            </select>
                            @error('business_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Business Email *</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="tel" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}" 
                                   required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="website" class="form-label">Website</label>
                        <input type="url" 
                               class="form-control @error('website') is-invalid @enderror" 
                               id="website" 
                               name="website" 
                               value="{{ old('website') }}" 
                               placeholder="https://example.com">
                        @error('website')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Address Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-map-marker-alt me-2"></i>Address Information
                            </h6>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Street Address *</label>
                        <input type="text" 
                               class="form-control @error('address') is-invalid @enderror" 
                               id="address" 
                               name="address" 
                               value="{{ old('address') }}" 
                               required>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label">City *</label>
                            <input type="text" 
                                   class="form-control @error('city') is-invalid @enderror" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city') }}" 
                                   required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="state" class="form-label">State *</label>
                            <input type="text" 
                                   class="form-control @error('state') is-invalid @enderror" 
                                   id="state" 
                                   name="state" 
                                   value="{{ old('state') }}" 
                                   required>
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="postal_code" class="form-label">Postal Code *</label>
                            <input type="text" 
                                   class="form-control @error('postal_code') is-invalid @enderror" 
                                   id="postal_code" 
                                   name="postal_code" 
                                   value="{{ old('postal_code') }}" 
                                   required>
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="country" class="form-label">Country *</label>
                        <input type="text" 
                               class="form-control @error('country') is-invalid @enderror" 
                               id="country" 
                               name="country" 
                               value="{{ old('country', 'United States') }}" 
                               required>
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Admin Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-user-shield me-2"></i>Business Admin Information
                            </h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="admin_name" class="form-label">Admin Name *</label>
                            <input type="text" 
                                   class="form-control @error('admin_name') is-invalid @enderror" 
                                   id="admin_name" 
                                   name="admin_name" 
                                   value="{{ old('admin_name') }}" 
                                   required>
                            @error('admin_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="admin_email" class="form-label">Admin Email *</label>
                            <input type="email" 
                                   class="form-control @error('admin_email') is-invalid @enderror" 
                                   id="admin_email" 
                                   name="admin_email" 
                                   value="{{ old('admin_email') }}" 
                                   required>
                            @error('admin_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="admin_password" class="form-label">Admin Password *</label>
                            <input type="password" 
                                   class="form-control @error('admin_password') is-invalid @enderror" 
                                   id="admin_password" 
                                   name="admin_password" 
                                   required>
                            @error('admin_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Minimum 8 characters</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="admin_phone" class="form-label">Admin Phone</label>
                            <input type="tel" 
                                   class="form-control @error('admin_phone') is-invalid @enderror" 
                                   id="admin_phone" 
                                   name="admin_phone" 
                                   value="{{ old('admin_phone') }}">
                            @error('admin_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('super-admin.businesses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Business
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('businessForm').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
    submitBtn.disabled = true;
});
</script>
@endsection
