@extends('business.layouts.app')

@section('title', 'Edit Booking - ' . $booking->booking_number)
@section('page-title', 'Edit Booking')

@push('styles')
    @vite(['resources/css/bookings.css'])
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Booking #{{ $booking->booking_number }}
                </h5>
                <small class="text-muted">Update booking details</small>
            </div>
            <div class="card-body">
                <form id="bookingForm" method="POST" action="{{ route('business.bookings.update', $booking) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Left Column - Booking Form -->
                        <div class="col-lg-8">
                            <!-- Booking Details -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="fas fa-calendar me-2"></i>Booking Details
                                    </h6>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="start_date_time" class="form-label">Start Date & Time *</label>
                                    <input type="datetime-local" class="form-control" id="start_date_time" name="start_date_time" 
                                           value="{{ old('start_date_time', $booking->start_date_time->format('Y-m-d\TH:i')) }}" required>
                                    @error('start_date_time')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_date_time" class="form-label">End Date & Time *</label>
                                    <input type="datetime-local" class="form-control" id="end_date_time" name="end_date_time" 
                                           value="{{ old('end_date_time', $booking->end_date_time->format('Y-m-d\TH:i')) }}" required>
                                    @error('end_date_time')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Vehicle Selection -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="fas fa-car me-2"></i>Select Vehicle
                                    </h6>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="vehicle_type_filter" class="form-label">Vehicle Type</label>
                                    <select class="form-select" id="vehicle_type_filter" onchange="filterVehicles()">
                                        <option value="">All Types</option>
                                        <option value="car">Car</option>
                                        <option value="bike">Bike</option>
                                        <option value="heavy_vehicle">Heavy Vehicle</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="seating_capacity_filter" class="form-label">Seating Capacity</label>
                                    <select class="form-select" id="seating_capacity_filter" onchange="filterVehicles()">
                                        <option value="">All Capacities</option>
                                        <option value="2">2 Seater</option>
                                        <option value="4">4 Seater</option>
                                        <option value="5">5 Seater</option>
                                        <option value="7">7 Seater</option>
                                        <option value="8">8+ Seater</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="vehicle_id" class="form-label">Select Vehicle *</label>
                                    <select class="form-select" id="vehicle_id" name="vehicle_id" required onchange="calculatePricing()">
                                        <option value="">Choose a vehicle...</option>
                                        @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" 
                                                data-type="{{ $vehicle->vehicle_type }}"
                                                data-seating="{{ $vehicle->seating_capacity }}"
                                                data-fuel="{{ $vehicle->fuel_type }}"
                                                data-transmission="{{ $vehicle->transmission_type }}"
                                                data-daily-rate="{{ $vehicle->rental_price_24h ?? 1000 }}"
                                                {{ old('vehicle_id', $booking->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->vehicle_make }} {{ $vehicle->vehicle_model }} ({{ $vehicle->vehicle_number }}) - ₹{{ number_format($vehicle->rental_price_24h ?? 1000) }}/day
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Customer Selection -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="fas fa-user me-2"></i>Select Customer
                                    </h6>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label for="customer_id" class="form-label">Select Customer *</label>
                                    <select class="form-select" id="customer_id" name="customer_id" required>
                                        <option value="">Choose a customer...</option>
                                        @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id', $booking->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->display_name }} - {{ $customer->mobile_number }}
                                            @if($customer->customer_type === 'corporate')
                                                (Corporate)
                                            @endif
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <a href="{{ route('business.customers.create', ['type' => 'individual']) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                            <i class="fas fa-user-plus me-1"></i>Add Individual
                                        </a>
                                        <a href="{{ route('business.customers.create', ['type' => 'corporate']) }}" class="btn btn-outline-warning btn-sm" target="_blank">
                                            <i class="fas fa-building me-1"></i>Add Corporate
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="fas fa-credit-card me-2"></i>Payment Information
                                    </h6>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="advance_amount" class="form-label">Advance Amount</label>
                                    <input type="number" class="form-control" id="advance_amount" name="advance_amount" 
                                           value="{{ old('advance_amount', $booking->advance_amount) }}" min="0" step="0.01" onchange="calculatePricing()">
                                    @error('advance_amount')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="advance_payment_method" class="form-label">Advance Payment Method</label>
                                    <select class="form-select" id="advance_payment_method" name="advance_payment_method">
                                        <option value="">Select Method</option>
                                        <option value="cash" {{ old('advance_payment_method', $booking->advance_payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="credit_card" {{ old('advance_payment_method', $booking->advance_payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                        <option value="debit_card" {{ old('advance_payment_method', $booking->advance_payment_method) == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                        <option value="upi" {{ old('advance_payment_method', $booking->advance_payment_method) == 'upi' ? 'selected' : '' }}>UPI</option>
                                        <option value="bank_transfer" {{ old('advance_payment_method', $booking->advance_payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="cheque" {{ old('advance_payment_method', $booking->advance_payment_method) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                    </select>
                                    @error('advance_payment_method')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="payment_method" class="form-label">Final Payment Method</label>
                                    <select class="form-select" id="payment_method" name="payment_method">
                                        <option value="">Select Method</option>
                                        <option value="cash" {{ old('payment_method', $booking->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="credit_card" {{ old('payment_method', $booking->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                        <option value="debit_card" {{ old('payment_method', $booking->payment_method) == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                        <option value="upi" {{ old('payment_method', $booking->payment_method) == 'upi' ? 'selected' : '' }}>UPI</option>
                                        <option value="bank_transfer" {{ old('payment_method', $booking->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="cheque" {{ old('payment_method', $booking->payment_method) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="fas fa-sticky-note me-2"></i>Additional Information
                                    </h6>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="customer_notes" class="form-label">Customer Notes</label>
                                    <textarea class="form-control" id="customer_notes" name="customer_notes" rows="3" 
                                              placeholder="Any special requests or notes from the customer">{{ old('customer_notes', $booking->customer_notes) }}</textarea>
                                    @error('customer_notes')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="notes" class="form-label">Internal Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                                              placeholder="Internal notes about this booking">{{ old('notes', $booking->notes) }}</textarea>
                                    @error('notes')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Order Summary -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-receipt me-2"></i>Order Summary
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="orderSummary">
                                        <div class="text-muted text-center py-4">
                                            <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                            <p>Select a vehicle and dates to see pricing</p>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2 mt-4">
                                        <button type="submit" class="btn btn-primary" id="updateBookingBtn">
                                            <i class="fas fa-save me-2"></i>Update Booking
                                        </button>
                                        <a href="{{ route('business.bookings.show', $booking) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Filter vehicles based on selected criteria
function filterVehicles() {
    const vehicleType = document.getElementById('vehicle_type_filter').value;
    const seatingCapacity = document.getElementById('seating_capacity_filter').value;
    const vehicleSelect = document.getElementById('vehicle_id');
    
    // Show/hide options based on filters
    Array.from(vehicleSelect.options).forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }
        
        const type = option.getAttribute('data-type');
        const seating = option.getAttribute('data-seating');
        
        let show = true;
        
        if (vehicleType && type !== vehicleType) {
            show = false;
        }
        
        if (seatingCapacity && seating !== seatingCapacity) {
            show = false;
        }
        
        option.style.display = show ? 'block' : 'none';
    });
    
    // Reset selection if current selection is hidden
    if (vehicleSelect.value && vehicleSelect.selectedOptions[0].style.display === 'none') {
        vehicleSelect.value = '';
        calculatePricing();
    }
}

// Calculate pricing and update order summary
function calculatePricing() {
    const vehicleSelect = document.getElementById('vehicle_id');
    const startDateTime = document.getElementById('start_date_time').value;
    const endDateTime = document.getElementById('end_date_time').value;
    const advanceAmount = parseFloat(document.getElementById('advance_amount').value) || 0;
    
    if (!vehicleSelect.value || !startDateTime || !endDateTime) {
        updateOrderSummary(null);
        return;
    }
    
    const selectedOption = vehicleSelect.selectedOptions[0];
    const dailyRate = parseFloat(selectedOption.getAttribute('data-daily-rate'));
    
    // Calculate duration
    const start = new Date(startDateTime);
    const end = new Date(endDateTime);
    const hours = (end - start) / (1000 * 60 * 60);
    const days = Math.ceil(hours / 24);
    
    const basePrice = dailyRate * days;
    const totalAmount = basePrice;
    const amountDue = totalAmount - advanceAmount;
    
    updateOrderSummary({
        vehicle: selectedOption.textContent,
        dailyRate: dailyRate,
        days: days,
        hours: hours,
        basePrice: basePrice,
        totalAmount: totalAmount,
        advanceAmount: advanceAmount,
        amountDue: amountDue
    });
}

// Update order summary display
function updateOrderSummary(data) {
    const summaryDiv = document.getElementById('orderSummary');
    const updateBtn = document.getElementById('updateBookingBtn');
    
    if (!data) {
        summaryDiv.innerHTML = `
            <div class="text-muted text-center py-4">
                <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                <p>Select a vehicle and dates to see pricing</p>
            </div>
        `;
        updateBtn.disabled = true;
        return;
    }
    
    summaryDiv.innerHTML = `
        <div class="mb-3">
            <strong>Vehicle:</strong><br>
            <small class="text-muted">${data.vehicle}</small>
        </div>
        <div class="mb-3">
            <strong>Duration:</strong><br>
            <small class="text-muted">${data.days} day(s) (${Math.round(data.hours)} hours)</small>
        </div>
        <div class="mb-3">
            <strong>Base Rental Price:</strong><br>
            <span class="text-primary">₹${data.dailyRate.toLocaleString()}/day × ${data.days} = ₹${data.basePrice.toLocaleString()}</span>
        </div>
        <div class="mb-3">
            <strong>Extra Charges:</strong><br>
            <span class="text-muted">₹0</span>
        </div>
        <hr>
        <div class="mb-3">
            <strong>Total Amount:</strong><br>
            <span class="h5 text-primary">₹${data.totalAmount.toLocaleString()}</span>
        </div>
        <div class="mb-3">
            <strong>Advance Amount:</strong><br>
            <span class="text-success">₹${data.advanceAmount.toLocaleString()}</span>
        </div>
        <hr>
        <div class="mb-0">
            <strong>Amount Due:</strong><br>
            <span class="h6 ${data.amountDue > 0 ? 'text-warning' : 'text-success'}">₹${data.amountDue.toLocaleString()}</span>
        </div>
    `;
    
    updateBtn.disabled = false;
}

// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum datetime to current time
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    
    const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
    
    document.getElementById('start_date_time').min = minDateTime;
    document.getElementById('end_date_time').min = minDateTime;
    
    // Update end datetime when start datetime changes
    document.getElementById('start_date_time').addEventListener('change', function() {
        const startDateTime = this.value;
        if (startDateTime) {
            document.getElementById('end_date_time').min = startDateTime;
            calculatePricing();
        }
    });
    
    // Recalculate when end datetime changes
    document.getElementById('end_date_time').addEventListener('change', calculatePricing);
    
    // Recalculate when advance amount changes
    document.getElementById('advance_amount').addEventListener('input', calculatePricing);
    
    // Initial calculation
    calculatePricing();
    
    // Initialize vehicle display
    filterVehicles();
});
</script>
@endpush
