@extends('business.layouts.booking-flow')

@section('title', 'New Booking - Confirm Booking')
@section('page-title', 'New Booking')

@push('styles')
    <link href="{{ asset('css/booking-flow.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="booking-flow-container">
    <!-- Header -->
    <div class="booking-flow-header">
        <div class="container">
            <a href="{{ route('business.bookings.flow.step4') }}" class="back-link">
                <i class="fas fa-arrow-left me-2"></i>Back to Billing
            </a>
            <h1>New Booking</h1>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="booking-progress">
        <div class="container">
            <div class="progress-steps">
                <div class="progress-step completed">
                    <div class="step-circle"><i class="fas fa-check"></i></div>
                    <div class="step-label">Dates</div>
                    <div class="step-connector"></div>
                </div>
                <div class="progress-step completed">
                    <div class="step-circle"><i class="fas fa-check"></i></div>
                    <div class="step-label">Vehicles</div>
                    <div class="step-connector"></div>
                </div>
                <div class="progress-step completed">
                    <div class="step-circle"><i class="fas fa-check"></i></div>
                    <div class="step-label">Customer</div>
                    <div class="step-connector"></div>
                </div>
                <div class="progress-step completed">
                    <div class="step-circle"><i class="fas fa-check"></i></div>
                    <div class="step-label">Billing Info</div>
                    <div class="step-connector"></div>
                </div>
                <div class="progress-step active">
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
                <!-- Left Column - Preview Booking Details -->
                <div class="col-lg-8">
                    <div class="booking-flow-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-eye me-2"></i>Preview Booking Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('business.bookings.flow.process-step5') }}" id="confirmForm">
                                @csrf
                                
                                <!-- Date & Time -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary border-bottom pb-2 mb-3">
                                            <i class="fas fa-calendar me-2"></i>Date & Time
                                        </h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Pickup Location</label>
                                            <p class="mb-0">Hyderabad</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Pickup date</label>
                                            <p class="mb-0">{{ $startDateTime->format('d/m/Y') }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Time</label>
                                            <p class="mb-0">{{ $startDateTime->format('h:i A') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Drop Location</label>
                                            <p class="mb-0">Hyderabad</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Drop-off date</label>
                                            <p class="mb-0">{{ $endDateTime->format('d/m/Y') }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Time</label>
                                            <p class="mb-0">{{ $endDateTime->format('h:i A') }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Vehicle -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary border-bottom pb-2 mb-3">
                                            <i class="fas fa-car me-2"></i>Vehicle
                                        </h6>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="vehicle-image mb-3">
                                            <i class="fas fa-car fa-3x text-muted"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <h5 class="mb-2">{{ $vehicle->vehicle_make }} {{ $vehicle->vehicle_model }}</h5>
                                        <p class="text-muted mb-2">
                                            {{ ucfirst($vehicle->transmission_type) }} Transmission · 
                                            {{ $vehicle->seating_capacity }} Seats · 
                                            {{ ucfirst($vehicle->fuel_type) }} · 
                                            {{ $vehicle->mileage ?? '14' }}KM/Ltr
                                        </p>
                                        <div class="d-flex align-items-center">
                                            <span class="h5 text-primary me-2">₹{{ number_format($baseRentalPrice) }}/day</span>
                                            <i class="fas fa-info-circle text-muted" title="Base rental price"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Customer Details -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary border-bottom pb-2 mb-3">
                                            <i class="fas fa-user me-2"></i>Customer Details
                                        </h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">CustomerType</label>
                                            <p class="mb-0">{{ ucfirst($customer->customer_type) }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">First Name</label>
                                            <p class="mb-0">{{ $customer->first_name ?? $customer->contact_person_name ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Last Name</label>
                                            <p class="mb-0">{{ $customer->last_name ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Email Address</label>
                                            <p class="mb-0">{{ $customer->email ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Phone Number</label>
                                            <p class="mb-0">{{ $customer->mobile_number ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Date of Birth</label>
                                            <p class="mb-0">{{ $customer->date_of_birth ? \Carbon\Carbon::parse($customer->date_of_birth)->format('d/m/Y') : '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Address 1</label>
                                            <p class="mb-0">{{ $customer->address_line_1 ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Address 2</label>
                                            <p class="mb-0">{{ $customer->address_line_2 ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">City</label>
                                            <p class="mb-0">{{ $customer->city ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">State</label>
                                            <p class="mb-0">{{ $customer->state ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Country</label>
                                            <p class="mb-0">{{ $customer->country ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Pincode</label>
                                            <p class="mb-0">{{ $customer->pincode ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Licence Details -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary border-bottom pb-2 mb-3">
                                            <i class="fas fa-id-card me-2"></i>Licence Details
                                        </h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Driving Licence Number</label>
                                            <p class="mb-0">{{ $customer->driving_license_number ?? '-' }}</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Expiry Date</label>
                                            <p class="mb-0">{{ $customer->driving_license_expiry ? \Carbon\Carbon::parse($customer->driving_license_expiry)->format('d/m/Y') : '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Document</label>
                                            <p class="mb-0">
                                                @if($customer->driving_license_document)
                                                    <a href="#" class="btn btn-sm btn-outline-primary">View</a>
                                                @else
                                                    <span class="text-muted">No document uploaded</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Additional Comments -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="text-primary border-bottom pb-2 mb-3">
                                            <i class="fas fa-comment me-2"></i>Additional Comments
                                        </h6>
                                        <textarea class="form-control" rows="3" placeholder="Input Text"></textarea>
                                    </div>
                                </div>

                                <div class="booking-flow-actions">
                                    <a href="{{ route('business.bookings.flow.step4') }}" class="btn-back">
                                        <i class="fas fa-arrow-left me-2"></i>Back
                                    </a>
                                    <button type="submit" class="btn-confirm">
                                        <i class="fas fa-check me-2"></i>Confirm Booking
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
                            <div class="section-value">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $startDateTime->format('l, M d, Y') }}<br>
                                <small>Time: {{ $startDateTime->format('h:i A') }}</small><br>
                                <small>Location: Hyderabad</small>
                            </div>
                        </div>
                        
                        <div class="summary-section">
                            <div class="section-label">Drop</div>
                            <div class="section-value">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $endDateTime->format('l, M d, Y') }}<br>
                                <small>Time: {{ $endDateTime->format('h:i A') }}</small><br>
                                <small>Location: Hyderabad</small>
                            </div>
                        </div>
                        
                        <div class="summary-section">
                            <div class="section-label">Vehicle</div>
                            <div class="section-value">
                                <i class="fas fa-car me-2"></i>
                                {{ $vehicle->vehicle_make }} {{ $vehicle->vehicle_model }}<br>
                                <small>({{ $vehicle->vehicle_number }})</small><br>
                                <small class="text-primary">{{ $days }}x Days</small><br>
                                <small class="text-primary">₹{{ number_format($baseRentalPrice) }}/day</small>
                            </div>
                        </div>
                        
                        <div class="summary-section">
                            <div class="section-label">Customer</div>
                            <div class="section-value">
                                <i class="fas fa-user me-2"></i>
                                {{ $customer->display_name }}<br>
                                <small>{{ $customer->mobile_number }}</small><br>
                                <small>{{ ucfirst($customer->customer_type) }}</small>
                            </div>
                        </div>
                        
                        <!-- Pricing Breakdown -->
                        <div class="pricing-breakdown">
                            <div class="pricing-item">
                                <span class="item-label">{{ $vehicle->vehicle_make }} {{ $vehicle->vehicle_model }}</span>
                                <span class="item-value">₹{{ number_format($baseRentalPrice) }}</span>
                            </div>
                            <div class="pricing-item">
                                <span class="item-label">Additional Charges</span>
                                <span class="item-value">₹{{ number_format($extraCharges) }}</span>
                            </div>
                            <div class="pricing-item">
                                <span class="item-label">Tax 10%</span>
                                <span class="item-value">₹{{ number_format(($totalAmount) * 0.10) }}</span>
                            </div>
                            <hr>
                            <div class="pricing-item total">
                                <span class="item-label">Sub Total</span>
                                <span class="item-value">₹{{ number_format($totalAmount + ($totalAmount * 0.10)) }}</span>
                            </div>
                            <div class="pricing-item">
                                <span class="item-label">Discount</span>
                                <span class="item-value">₹{{ number_format($discountAmount) }}</span>
                            </div>
                            <div class="pricing-item">
                                <span class="item-label">Advance Payment: {{ ucfirst(session('booking_flow.payment_method', 'Cash')) }}</span>
                                <span class="item-value">₹{{ number_format($advanceAmount) }}</span>
                            </div>
                            <hr>
                            <div class="pricing-item total">
                                <span class="item-label">Amount Due</span>
                                <span class="item-value">₹{{ number_format($amountDue) }}</span>
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
    // Form validation
    document.getElementById('confirmForm').addEventListener('submit', function(e) {
        if (!confirm('Are you sure you want to confirm this booking?')) {
            e.preventDefault();
            return;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Booking...';
        submitBtn.disabled = true;
        
        // Re-enable button after 5 seconds as fallback
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 5000);
    });
});
</script>
@endpush
