@extends('business.layouts.booking-flow')

@section('title', 'New Booking - Billing Information')
@section('page-title', 'New Booking')

@push('styles')
    <link href="{{ asset('css/booking-flow.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="booking-flow-container">
    <!-- Header -->
    <div class="booking-flow-header">
        <div class="container">
            <a href="{{ route('business.bookings.flow.step3') }}" class="back-link">
                <i class="fas fa-arrow-left me-2"></i>Back to Customer
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
                <div class="progress-step active">
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
                <!-- Left Column - Billing Information -->
                <div class="col-lg-8">
                    <div class="booking-flow-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>Billing Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('business.bookings.flow.process-step4') }}" id="billingForm">
                                @csrf
                                
                                <!-- Vehicle Rental Charges -->
                                <div class="billing-section">
                                    <h6 class="section-title">
                                        <i class="fas fa-car me-2"></i>Vehicle Rental Charges
                                    </h6>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="base_rental_price" class="form-label">Vehicle Rent for {{ $days }} day(s)</label>
                                            <div class="billing-input-group">
                                                <input type="number" 
                                                       class="form-control @error('base_rental_price') is-invalid @enderror" 
                                                       id="base_rental_price" 
                                                       name="base_rental_price" 
                                                       value="{{ old('base_rental_price', $baseRentalPrice) }}" 
                                                       min="0" 
                                                       step="0.01" 
                                                       required>
                                                <div class="btn-group">
                                                    <button type="button" class="btn active" data-currency="₹">₹</button>
                                                    <button type="button" class="btn" data-currency="%">%</button>
                                                </div>
                                            </div>
                                            @error('base_rental_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="kilometer_limit" class="form-label">Kilometer Limit per Booking</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="kilometer_limit" 
                                                   name="kilometer_limit" 
                                                   value="{{ old('kilometer_limit', 110) }}" 
                                                   min="0">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="extra_price_per_hour" class="form-label">Extra Price per Hour</label>
                                            <div class="billing-input-group">
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="extra_price_per_hour" 
                                                       name="extra_price_per_hour" 
                                                       value="{{ old('extra_price_per_hour', 350) }}" 
                                                       min="0" 
                                                       step="0.01">
                                                <div class="btn-group">
                                                    <button type="button" class="btn active" data-currency="₹">₹</button>
                                                    <button type="button" class="btn" data-currency="%">%</button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="extra_price_per_km" class="form-label">Extra Price per Kilometre</label>
                                            <div class="billing-input-group">
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="extra_price_per_km" 
                                                       name="extra_price_per_km" 
                                                       value="{{ old('extra_price_per_km', 35) }}" 
                                                       min="0" 
                                                       step="0.01">
                                                <div class="btn-group">
                                                    <button type="button" class="btn active" data-currency="₹">₹</button>
                                                    <button type="button" class="btn" data-currency="%">%</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Additional Charges -->
                                <div class="billing-section">
                                    <h6 class="section-title">
                                        <i class="fas fa-plus me-2"></i>Additional Charges
                                    </h6>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="extra_charges" class="form-label">Amount</label>
                                            <div class="billing-input-group">
                                                <input type="number" 
                                                       class="form-control @error('extra_charges') is-invalid @enderror" 
                                                       id="extra_charges" 
                                                       name="extra_charges" 
                                                       value="{{ old('extra_charges', 0) }}" 
                                                       min="0" 
                                                       step="0.01">
                                                <div class="btn-group">
                                                    <button type="button" class="btn active" data-currency="₹">₹</button>
                                                    <button type="button" class="btn" data-currency="%">%</button>
                                                </div>
                                            </div>
                                            @error('extra_charges')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="extra_charges_description" class="form-label">Description</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="extra_charges_description" 
                                                   name="extra_charges_description" 
                                                   value="{{ old('extra_charges_description') }}" 
                                                   placeholder="Input Text">
                                        </div>
                                    </div>
                                    
                                    <div class="text-end">
                                        <a href="#" class="btn btn-outline-primary btn-sm" onclick="addAdditionalCharge()">
                                            <i class="fas fa-plus me-1"></i>Add New Charge
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Discount Details -->
                                <div class="billing-section">
                                    <h6 class="section-title">
                                        <i class="fas fa-percentage me-2"></i>Discount Details
                                    </h6>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="discount_amount" class="form-label">Amount</label>
                                            <div class="billing-input-group">
                                                <input type="number" 
                                                       class="form-control @error('discount_amount') is-invalid @enderror" 
                                                       id="discount_amount" 
                                                       name="discount_amount" 
                                                       value="{{ old('discount_amount', 0) }}" 
                                                       min="0" 
                                                       step="0.01">
                                                <div class="btn-group">
                                                    <button type="button" class="btn active" data-currency="₹">₹</button>
                                                    <button type="button" class="btn" data-currency="%">%</button>
                                                </div>
                                            </div>
                                            @error('discount_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="discount_description" class="form-label">Description</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="discount_description" 
                                                   name="discount_description" 
                                                   value="{{ old('discount_description') }}" 
                                                   placeholder="Input Text">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Advance Payment -->
                                <div class="billing-section">
                                    <h6 class="section-title">
                                        <i class="fas fa-money-bill-wave me-2"></i>Advance Payment
                                    </h6>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="advance_amount" class="form-label">Amount</label>
                                            <input type="number" 
                                                   class="form-control @error('advance_amount') is-invalid @enderror" 
                                                   id="advance_amount" 
                                                   name="advance_amount" 
                                                   value="{{ old('advance_amount', 0) }}" 
                                                   min="0" 
                                                   step="0.01">
                                            @error('advance_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="payment_method" class="form-label">Payment Method</label>
                                            <select class="form-select @error('payment_method') is-invalid @enderror" 
                                                    id="payment_method" 
                                                    name="payment_method">
                                                <option value="">Dropdown (Cash, Card, UPI)</option>
                                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                                <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                                <option value="upi" {{ old('payment_method') == 'upi' ? 'selected' : '' }}>UPI</option>
                                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                            </select>
                                            @error('payment_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="booking-flow-actions">
                                    <a href="{{ route('business.bookings.flow.step3') }}" class="btn-back">
                                        <i class="fas fa-arrow-left me-2"></i>Back
                                    </a>
                                    <button type="submit" class="btn-next">
                                        Next <i class="fas fa-arrow-right ms-2"></i>
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
                                <span class="item-value" id="summary-extra-charges">₹0</span>
                            </div>
                            <div class="pricing-item">
                                <span class="item-label">Tax 10%</span>
                                <span class="item-value" id="summary-tax">₹0</span>
                            </div>
                            <hr>
                            <div class="pricing-item total">
                                <span class="item-label">Sub Total</span>
                                <span class="item-value" id="summary-subtotal">₹{{ number_format($totalAmount) }}</span>
                            </div>
                            <div class="pricing-item">
                                <span class="item-label">Discount</span>
                                <span class="item-value" id="summary-discount">₹0</span>
                            </div>
                            <div class="pricing-item">
                                <span class="item-label">Advance Payment: Cash</span>
                                <span class="item-value" id="summary-advance">₹0</span>
                            </div>
                            <hr>
                            <div class="pricing-item total">
                                <span class="item-label">Amount Due</span>
                                <span class="item-value" id="summary-amount-due">₹{{ number_format($totalAmount) }}</span>
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
    const baseRentalPrice = {{ $baseRentalPrice }};
    const days = {{ $days }};
    
    // Currency toggle functionality
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const group = this.closest('.btn-group');
            group.querySelectorAll('.btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Real-time calculation
    function calculatePricing() {
        const basePrice = parseFloat(document.getElementById('base_rental_price').value) || 0;
        const extraCharges = parseFloat(document.getElementById('extra_charges').value) || 0;
        const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
        const advanceAmount = parseFloat(document.getElementById('advance_amount').value) || 0;
        
        const subtotal = basePrice + extraCharges;
        const tax = subtotal * 0.10; // 10% tax
        const total = subtotal + tax;
        const amountAfterDiscount = total - discountAmount;
        const amountDue = amountAfterDiscount - advanceAmount;
        
        // Update summary
        document.getElementById('summary-extra-charges').textContent = `₹${extraCharges.toLocaleString()}`;
        document.getElementById('summary-tax').textContent = `₹${tax.toLocaleString()}`;
        document.getElementById('summary-subtotal').textContent = `₹${total.toLocaleString()}`;
        document.getElementById('summary-discount').textContent = `₹${discountAmount.toLocaleString()}`;
        document.getElementById('summary-advance').textContent = `₹${advanceAmount.toLocaleString()}`;
        document.getElementById('summary-amount-due').textContent = `₹${amountDue.toLocaleString()}`;
    }
    
    // Add event listeners for real-time calculation
    document.getElementById('base_rental_price').addEventListener('input', calculatePricing);
    document.getElementById('extra_charges').addEventListener('input', calculatePricing);
    document.getElementById('discount_amount').addEventListener('input', calculatePricing);
    document.getElementById('advance_amount').addEventListener('input', calculatePricing);
    
    // Initialize calculation
    calculatePricing();
});

function addAdditionalCharge() {
    // This would open a modal or add a new row for additional charges
    alert('Additional charge functionality would be implemented here.');
}
</script>
@endpush
