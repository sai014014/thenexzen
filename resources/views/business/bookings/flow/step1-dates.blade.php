@extends('business.layouts.booking-flow')

@section('title', 'New Booking - Select Dates')
@section('page-title', 'New Booking')

@push('styles')
    <link href="{{ asset('css/booking-flow.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="booking-flow-container">
    <!-- Header -->
    <div class="booking-flow-header">
        <div class="container">
            <a href="{{ route('business.bookings.index') }}" class="back-link">
                <i class="fas fa-arrow-left me-2"></i>Back to Bookings
            </a>
            <h1>New Booking</h1>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="booking-progress">
        <div class="container">
            <div class="progress-steps">
                <div class="progress-step active">
                    <div class="step-circle">1</div>
                    <div class="step-label">Dates</div>
                    <div class="step-connector"></div>
                </div>
                <div class="progress-step pending">
                    <div class="step-circle">2</div>
                    <div class="step-label">Vehicles</div>
                    <div class="step-connector"></div>
                </div>
                <div class="progress-step pending">
                    <div class="step-circle">3</div>
                    <div class="step-label">Customer</div>
                    <div class="step-connector"></div>
                </div>
                <div class="progress-step pending">
                    <div class="step-circle">4</div>
                    <div class="step-label">Billing Info</div>
                    <div class="step-connector"></div>
                </div>
                <div class="progress-step pending">
                    <div class="step-circle">5</div>
                    <div class="step-label">Confirm</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="booking-flow-content">
        <div class="container">
            <div class="row">
                <!-- Left Column - Form -->
                <div class="col-lg-8">
                    <div class="booking-flow-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar me-2"></i>Select Booking Dates
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('business.bookings.flow.process-step1') }}" id="datesForm">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="start_date_time" class="form-label">
                                            Pick-up date & time <span class="text-danger">*</span>
                                        </label>
                                        <input type="datetime-local" 
                                               class="form-control @error('start_date_time') is-invalid @enderror" 
                                               id="start_date_time" 
                                               name="start_date_time" 
                                               value="{{ old('start_date_time') }}" 
                                               required>
                                        @error('start_date_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-4">
                                        <label for="end_date_time" class="form-label">
                                            Drop-off date & time <span class="text-danger">*</span>
                                        </label>
                                        <input type="datetime-local" 
                                               class="form-control @error('end_date_time') is-invalid @enderror" 
                                               id="end_date_time" 
                                               name="end_date_time" 
                                               value="{{ old('end_date_time') }}" 
                                               required>
                                        @error('end_date_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="pickup_location" class="form-label">
                                            Pick-up location <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('pickup_location') is-invalid @enderror" 
                                                id="pickup_location" 
                                                name="pickup_location" 
                                                required>
                                            <option value="">Select pickup location</option>
                                            <option value="garage" {{ old('pickup_location') == 'garage' ? 'selected' : '' }}>Garage</option>
                                            <option value="office" {{ old('pickup_location') == 'office' ? 'selected' : '' }}>Office</option>
                                            <option value="airport" {{ old('pickup_location') == 'airport' ? 'selected' : '' }}>Airport</option>
                                            <option value="station" {{ old('pickup_location') == 'station' ? 'selected' : '' }}>Station</option>
                                            <option value="other" {{ old('pickup_location') == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('pickup_location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-4">
                                        <label for="dropoff_location" class="form-label">
                                            Drop-off location <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('dropoff_location') is-invalid @enderror" 
                                                id="dropoff_location" 
                                                name="dropoff_location" 
                                                required>
                                            <option value="">Select drop-off location</option>
                                            <option value="garage" {{ old('dropoff_location') == 'garage' ? 'selected' : '' }}>Garage</option>
                                            <option value="office" {{ old('dropoff_location') == 'office' ? 'selected' : '' }}>Office</option>
                                            <option value="airport" {{ old('dropoff_location') == 'airport' ? 'selected' : '' }}>Airport</option>
                                            <option value="station" {{ old('dropoff_location') == 'station' ? 'selected' : '' }}>Station</option>
                                            <option value="other" {{ old('dropoff_location') == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('dropoff_location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="booking-flow-actions">
                                    <a href="{{ route('business.bookings.index') }}" class="btn-back">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn-next">
                                        Proceed <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Booking Summary -->
                <div class="col-lg-4">
                    <div class="booking-summary-panel">
                        <h5 class="summary-title">Booking Summary</h5>
                        
                        <div class="summary-section">
                            <div class="section-label">Pickup</div>
                            <div class="section-value" id="summary-pickup">
                                <i class="fas fa-calendar me-2"></i>
                                <span id="pickup-date">Select dates</span>
                            </div>
                        </div>
                        
                        <div class="summary-section">
                            <div class="section-label">Drop</div>
                            <div class="section-value" id="summary-drop">
                                <i class="fas fa-calendar me-2"></i>
                                <span id="drop-date">Select dates</span>
                            </div>
                        </div>
                        
                        <div class="summary-section">
                            <div class="section-label">Duration</div>
                            <div class="section-value" id="summary-duration">
                                <i class="fas fa-clock me-2"></i>
                                <span id="duration-text">-</span>
                            </div>
                        </div>
                        
                        <div class="summary-section">
                            <div class="section-label">Vehicle</div>
                            <div class="section-value">
                                <i class="fas fa-car me-2"></i>
                                <span>Not selected</span>
                            </div>
                        </div>
                        
                        <div class="summary-section">
                            <div class="section-label">Customer</div>
                            <div class="section-value">
                                <i class="fas fa-user me-2"></i>
                                <span>Not selected</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateTimeInput = document.getElementById('start_date_time');
    const endDateTimeInput = document.getElementById('end_date_time');
    const pickupLocationSelect = document.getElementById('pickup_location');
    const dropoffLocationSelect = document.getElementById('dropoff_location');
    
    // Set minimum datetime to current time
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    
    const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
    startDateTimeInput.min = minDateTime;
    endDateTimeInput.min = minDateTime;
    
    // Update end datetime minimum when start datetime changes
    startDateTimeInput.addEventListener('change', function() {
        if (this.value) {
            endDateTimeInput.min = this.value;
            updateSummary();
        }
    });
    
    // Update summary when inputs change
    endDateTimeInput.addEventListener('change', updateSummary);
    pickupLocationSelect.addEventListener('change', updateSummary);
    dropoffLocationSelect.addEventListener('change', updateSummary);
    
    function updateSummary() {
        const startDateTime = startDateTimeInput.value;
        const endDateTime = endDateTimeInput.value;
        const pickupLocation = pickupLocationSelect.value;
        const dropoffLocation = dropoffLocationSelect.value;
        
        // Update pickup date
        if (startDateTime) {
            const startDate = new Date(startDateTime);
            const pickupDate = startDate.toLocaleDateString('en-GB', {
                weekday: 'long',
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
            const pickupTime = startDate.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
            
            document.getElementById('pickup-date').innerHTML = `
                ${pickupDate}<br>
                <small>Time: ${pickupTime}</small><br>
                <small>Location: ${getLocationName(pickupLocation)}</small>
            `;
        } else {
            document.getElementById('pickup-date').textContent = 'Select dates';
        }
        
        // Update drop date
        if (endDateTime) {
            const endDate = new Date(endDateTime);
            const dropDate = endDate.toLocaleDateString('en-GB', {
                weekday: 'long',
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
            const dropTime = endDate.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
            
            document.getElementById('drop-date').innerHTML = `
                ${dropDate}<br>
                <small>Time: ${dropTime}</small><br>
                <small>Location: ${getLocationName(dropoffLocation)}</small>
            `;
        } else {
            document.getElementById('drop-date').textContent = 'Select dates';
        }
        
        // Update duration
        if (startDateTime && endDateTime) {
            const start = new Date(startDateTime);
            const end = new Date(endDateTime);
            const diffMs = end - start;
            const diffHours = Math.ceil(diffMs / (1000 * 60 * 60));
            const diffDays = Math.ceil(diffHours / 24);
            
            document.getElementById('duration-text').textContent = `${diffDays} day(s) (${diffHours} hours)`;
        } else {
            document.getElementById('duration-text').textContent = '-';
        }
    }
    
    function getLocationName(value) {
        const locations = {
            'garage': 'Garage',
            'office': 'Office',
            'airport': 'Airport',
            'station': 'Station',
            'other': 'Other'
        };
        return locations[value] || 'Not selected';
    }
    
    // Form validation
    document.getElementById('datesForm').addEventListener('submit', function(e) {
        const startDateTime = startDateTimeInput.value;
        const endDateTime = endDateTimeInput.value;
        
        if (!startDateTime || !endDateTime) {
            e.preventDefault();
            alert('Please select both start and end dates.');
            return;
        }
        
        const start = new Date(startDateTime);
        const end = new Date(endDateTime);
        
        if (end <= start) {
            e.preventDefault();
            alert('End date must be after start date.');
            return;
        }
        
        // Check minimum booking duration (e.g., 1 hour)
        const diffMs = end - start;
        const diffHours = diffMs / (1000 * 60 * 60);
        
        if (diffHours < 1) {
            e.preventDefault();
            alert('Minimum booking duration is 1 hour.');
            return;
        }
    });
});
</script>
@endpush
