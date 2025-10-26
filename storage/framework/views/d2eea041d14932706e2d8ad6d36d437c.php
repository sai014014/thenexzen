

<?php $__env->startSection('title', 'Quick Create Booking - The NexZen'); ?>
<?php $__env->startSection('page-title', 'Quick Create Booking'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-plus me-2"></i>Quick Create Booking
                </h5>
            </div>
            <div class="card-body">
                <form id="quickBookingForm" method="POST" action="<?php echo e(route('business.bookings.store')); ?>">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Date & Time Selection Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-calendar-alt me-2"></i>Select Dates & Time
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="start_date_time" class="form-label">Pickup Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control" 
                                   id="start_date_time" 
                                   name="start_date_time" 
                                   required
                                   onchange="validateDates(); checkAvailabilityAfterDateChange();">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date_time" class="form-label">Drop-off Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control" 
                                   id="end_date_time" 
                                   name="end_date_time" 
                                   required
                                   onchange="validateDates(); checkAvailabilityAfterDateChange();">
                            <div class="invalid-feedback"></div>
                        </div>
                        <!-- Date Validation Message -->
                        <div class="col-12" id="dateValidationMessage" style="display: none;"></div>
                    </div>

                    <!-- Customer Selection Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-users me-2"></i>Select Customer
                            </h6>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                            <select class="form-select" id="customer_id" name="customer_id" required onchange="loadCustomerDetails()">
                                <option value="">Search and select customer...</option>
                                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($customer->id); ?>" 
                                            data-name="<?php echo e($customer->full_name ?? $customer->company_name); ?>"
                                            data-mobile="<?php echo e($customer->mobile_number); ?>"
                                            data-email="<?php echo e($customer->email); ?>"
                                            data-address="<?php echo e($customer->address); ?>">
                                        <?php echo e($customer->full_name ?? $customer->company_name); ?> 
                                        (<?php echo e($customer->mobile_number); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-outline-primary w-100" onclick="openNewCustomerModal()">
                                <i class="fas fa-user-plus me-2"></i>Add New Customer
                            </button>
                        </div>
                        <!-- Customer Details Display -->
                        <div class="col-12" id="customerDetails" style="display: none;">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <small class="text-muted">Name:</small>
                                            <div id="customerName" class="fw-bold"></div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Mobile:</small>
                                            <div id="customerMobile"></div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Email:</small>
                                            <div id="customerEmail"></div>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Address:</small>
                                            <div id="customerAddress"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Selection Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-car me-2"></i>Select Vehicle
                            </h6>
                        </div>
                        
                        <!-- Vehicle Filters -->
                        <div class="col-md-3 mb-3">
                            <label for="vehicle_type_filter" class="form-label">Vehicle Type</label>
                            <select class="form-select" id="vehicle_type_filter" onchange="filterVehicles()">
                                <option value="">All Types</option>
                                <option value="car">Car</option>
                                <option value="bike">Bike</option>
                                <option value="heavy_vehicle">Heavy Vehicle</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
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
                        <div class="col-md-3 mb-3">
                            <label for="fuel_type_filter" class="form-label">Fuel Type</label>
                            <select class="form-select" id="fuel_type_filter" onchange="filterVehicles()">
                                <option value="">All Fuel Types</option>
                                <option value="petrol">Petrol</option>
                                <option value="diesel">Diesel</option>
                                <option value="electric">Electric</option>
                                <option value="hybrid">Hybrid</option>
                                <option value="cng">CNG</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="transmission_filter" class="form-label">Transmission</label>
                            <select class="form-select" id="transmission_filter" onchange="filterVehicles()">
                                <option value="">All Transmissions</option>
                                <option value="manual">Manual</option>
                                <option value="automatic">Automatic</option>
                                <option value="semi_automatic">Semi-Automatic</option>
                            </select>
                        </div>
                        
                        <!-- Vehicle Selection -->
                        <div class="col-12 mb-3">
                            <label for="vehicle_id" class="form-label">Select Vehicle <span class="text-danger">*</span></label>
                            <select class="form-select" id="vehicle_id" name="vehicle_id" required onchange="calculatePricing()">
                                <option value="">Choose a vehicle...</option>
                                <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($vehicle->id); ?>" 
                                            data-type="<?php echo e($vehicle->vehicle_type); ?>"
                                            data-seating="<?php echo e($vehicle->seating_capacity); ?>"
                                            data-fuel="<?php echo e($vehicle->fuel_type); ?>"
                                            data-transmission="<?php echo e($vehicle->transmission_type); ?>"
                                            data-daily-rate="<?php echo e($vehicle->rental_price_24h ?? 1000); ?>"
                                            data-available="<?php echo e($vehicle->is_available ? 'true' : 'false'); ?>"
                                            data-make="<?php echo e($vehicle->vehicle_make); ?>"
                                            data-model="<?php echo e($vehicle->vehicle_model); ?>"
                                            data-number="<?php echo e($vehicle->vehicle_number); ?>">
                                        <?php echo e($vehicle->vehicle_make); ?> <?php echo e($vehicle->vehicle_model); ?> 
                                        (<?php echo e($vehicle->vehicle_number); ?>) - ₹<?php echo e(number_format($vehicle->rental_price_24h ?? 1000)); ?>/day
                                        <?php if(!$vehicle->is_available): ?>
                                            - [UNAVAILABLE]
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <!-- Vehicle Availability Check -->
                        <div class="col-12 mb-3">
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="checkVehicleAvailability()">
                                <i class="fas fa-calendar-check me-1"></i>Check Vehicle Availability
                            </button>
                            <div id="availabilityStatus" class="mt-2" style="display: none;"></div>
                        </div>
                    </div>


                    <!-- Billing Details Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-receipt me-2"></i>Billing Details
                            </h6>
                        </div>
                        
                        <!-- Base Rental Price -->
                        <div class="col-md-6 mb-3">
                            <label for="rental_charges" class="form-label">Base Rental Price (24h) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="rental_charges" 
                                       name="rental_charges" 
                                       step="0.01" 
                                       required
                                       readonly>
                            </div>
                            <small class="text-muted">Auto-calculated based on vehicle selection</small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Extra Charges -->
                        <div class="col-md-6 mb-3">
                            <label for="additional_charges" class="form-label">Extra Charges</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="additional_charges" 
                                       name="additional_charges" 
                                       step="0.01" 
                                       value="0"
                                       onchange="updatePricingSummary()">
                            </div>
                            <small class="text-muted">Additional fees, tolls, etc.</small>
                        </div>

                        <!-- Discount Amount -->
                        <div class="col-md-6 mb-3">
                            <label for="discount_amount" class="form-label">Discount Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="discount_amount" 
                                       name="discount_amount" 
                                       step="0.01" 
                                       value="0"
                                       onchange="updatePricingSummary()">
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="upi">UPI</option>
                                <option value="net_banking">Net Banking</option>
                                <option value="wallet">Digital Wallet</option>
                                <option value="cheque">Cheque</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Advance Payment -->
                        <div class="col-md-6 mb-3">
                            <label for="advance_payment" class="form-label">Advance Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="advance_payment" 
                                       name="advance_payment" 
                                       step="0.01" 
                                       value="0"
                                       onchange="updatePricingSummary()">
                            </div>
                            <small class="text-muted">Amount paid in advance</small>
                        </div>

                        <!-- Total Amount Due (Read-only) -->
                        <div class="col-md-6 mb-3">
                            <label for="total_amount" class="form-label">Total Amount Due</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="total_amount" 
                                       name="total_amount" 
                                       step="0.01" 
                                       readonly
                                       style="background-color: #f8f9fa;">
                            </div>
                            <small class="text-muted">Auto-calculated total</small>
                        </div>
                    </div>

                    <!-- Special Instructions -->
                    <div class="mb-3">
                        <label for="special_instructions" class="form-label">Special Instructions</label>
                        <textarea class="form-control" 
                                  id="special_instructions" 
                                  name="special_instructions" 
                                  rows="3" 
                                  placeholder="Any special instructions for this booking..."></textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="<?php echo e(route('business.bookings.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Bookings
                        </a>
                        <div>
                            <button type="button" class="btn btn-outline-primary me-2" onclick="calculatePricing()">
                                <i class="fas fa-calculator me-2"></i>Calculate Pricing
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>Create Booking
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pricing Summary -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-receipt me-2"></i>Pricing Summary</h6>
            </div>
            <div class="card-body" id="pricingSummary">
                <div class="d-flex justify-content-between mb-2">
                    <span>Rental Charges:</span>
                    <span id="summaryRentalCharges">₹0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Additional Charges:</span>
                    <span id="summaryAdditionalCharges">₹0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Discount:</span>
                    <span id="summaryDiscount">-₹0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span>Advance Payment:</span>
                    <span id="summaryAdvance">₹0.00</span>
                </div>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Total Amount:</span>
                    <span id="summaryTotal">₹0.00</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <a href="<?php echo e(route('business.bookings.create')); ?>" class="btn btn-primary w-100 mb-2">
                    <i class="fas fa-magic me-2"></i>5-Step Booking Flow
                </a>
                <a href="<?php echo e(route('business.customers.create')); ?>" class="btn btn-outline-primary w-100 mb-2" target="_blank">
                    <i class="fas fa-user-plus me-2"></i>Add New Customer
                </a>
                <a href="<?php echo e(route('business.vehicles.create')); ?>" class="btn btn-outline-success w-100" target="_blank">
                    <i class="fas fa-car me-2"></i>Add New Vehicle
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to today
    const today = new Date().toISOString().slice(0, 16);
    document.getElementById('start_date_time').min = today;
    document.getElementById('end_date_time').min = today;
    
    // Auto-calculate pricing when vehicle is selected
    document.getElementById('vehicle_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const dailyRate = selectedOption.getAttribute('data-daily-rate');
            document.getElementById('rental_charges').value = dailyRate;
            updatePricingSummary();
        }
    });
    
    // Update pricing when dates change
    document.getElementById('start_date_time').addEventListener('change', calculatePricing);
    document.getElementById('end_date_time').addEventListener('change', calculatePricing);
    
    // Update pricing when amounts change
    ['rental_charges', 'additional_charges', 'discount_amount', 'advance_payment'].forEach(id => {
        document.getElementById(id).addEventListener('input', updatePricingSummary);
    });
    
    // Form submission with validation
    document.getElementById('quickBookingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            submitForm();
        }
    });
});

function validateForm() {
    let isValid = true;
    
    // Clear previous errors
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    
    // Validate required fields
    const requiredFields = ['customer_id', 'vehicle_id', 'start_date_time', 'end_date_time', 'rental_charges', 'payment_method'];
    
    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            field.nextElementSibling.textContent = 'This field is required.';
            isValid = false;
        }
    });
    
    // Validate dates
    const startDate = new Date(document.getElementById('start_date_time').value);
    const endDate = new Date(document.getElementById('end_date_time').value);
    
    if (startDate >= endDate) {
        document.getElementById('end_date_time').classList.add('is-invalid');
        document.getElementById('end_date_time').nextElementSibling.textContent = 'Drop-off date must be after pickup date.';
        isValid = false;
    }
    
    return isValid;
}

function calculatePricing() {
    const vehicleId = document.getElementById('vehicle_id').value;
    const startDate = document.getElementById('start_date_time').value;
    const endDate = document.getElementById('end_date_time').value;
    
    if (!vehicleId || !startDate || !endDate) {
        return;
    }
    
    // Show loading state
    const calculateBtn = document.querySelector('button[onclick="calculatePricing()"]');
    const originalText = calculateBtn.innerHTML;
    calculateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Calculating...';
    calculateBtn.disabled = true;
    
    fetch(`<?php echo e(url('business/bookings/calculate-pricing')); ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            vehicle_id: vehicleId,
            start_date_time: startDate,
            end_date_time: endDate
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('rental_charges').value = data.total_amount;
            updatePricingSummary();
            showAlert('Pricing calculated successfully!', 'success');
        } else {
            showAlert(data.message || 'Failed to calculate pricing', 'danger');
        }
    })
    .catch(error => {
        console.error('Error calculating pricing:', error);
        showAlert('Error calculating pricing. Please try again.', 'danger');
    })
    .finally(() => {
        calculateBtn.innerHTML = originalText;
        calculateBtn.disabled = false;
    });
}

function updatePricingSummary() {
    const rentalCharges = parseFloat(document.getElementById('rental_charges').value) || 0;
    const additionalCharges = parseFloat(document.getElementById('additional_charges').value) || 0;
    const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const advancePayment = parseFloat(document.getElementById('advance_payment').value) || 0;
    
    const subtotal = rentalCharges + additionalCharges;
    const total = subtotal - discountAmount;
    
    document.getElementById('summaryRentalCharges').textContent = `₹${rentalCharges.toFixed(2)}`;
    document.getElementById('summaryAdditionalCharges').textContent = `₹${additionalCharges.toFixed(2)}`;
    document.getElementById('summaryDiscount').textContent = `-₹${discountAmount.toFixed(2)}`;
    document.getElementById('summaryAdvance').textContent = `₹${advancePayment.toFixed(2)}`;
    document.getElementById('summaryTotal').textContent = `₹${total.toFixed(2)}`;
}

function submitForm() {
    const form = document.getElementById('quickBookingForm');
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
    submitBtn.disabled = true;
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Booking created successfully!', 'success');
            setTimeout(() => {
                window.location.href = '<?php echo e(route("business.bookings.index")); ?>';
            }, 1500);
        } else {
            showAlert(data.message || 'Failed to create booking', 'danger');
            
            // Show validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const fieldElement = document.getElementById(field);
                    if (fieldElement) {
                        fieldElement.classList.add('is-invalid');
                        fieldElement.nextElementSibling.textContent = data.errors[field][0];
                    }
                });
            }
        }
    })
    .catch(error => {
        console.error('Error creating booking:', error);
        showAlert('Error creating booking. Please try again.', 'danger');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.card-body');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Load customer details when selected
function loadCustomerDetails() {
    const customerSelect = document.getElementById('customer_id');
    const customerDetails = document.getElementById('customerDetails');
    const selectedOption = customerSelect.options[customerSelect.selectedIndex];
    
    if (selectedOption.value) {
        document.getElementById('customerName').textContent = selectedOption.getAttribute('data-name');
        document.getElementById('customerMobile').textContent = selectedOption.getAttribute('data-mobile');
        document.getElementById('customerEmail').textContent = selectedOption.getAttribute('data-email') || 'N/A';
        document.getElementById('customerAddress').textContent = selectedOption.getAttribute('data-address') || 'N/A';
        customerDetails.style.display = 'block';
    } else {
        customerDetails.style.display = 'none';
    }
}

// Filter vehicles based on selected criteria
function filterVehicles() {
    const vehicleSelect = document.getElementById('vehicle_id');
    const typeFilter = document.getElementById('vehicle_type_filter').value;
    const seatingFilter = document.getElementById('seating_capacity_filter').value;
    const fuelFilter = document.getElementById('fuel_type_filter').value;
    const transmissionFilter = document.getElementById('transmission_filter').value;
    
    const options = vehicleSelect.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
            return;
        }
        
        const vehicleType = option.getAttribute('data-type');
        const seating = option.getAttribute('data-seating');
        const fuel = option.getAttribute('data-fuel');
        const transmission = option.getAttribute('data-transmission');
        
        let showOption = true;
        
        if (typeFilter && vehicleType !== typeFilter) showOption = false;
        if (seatingFilter && seating !== seatingFilter) showOption = false;
        if (fuelFilter && fuel !== fuelFilter) showOption = false;
        if (transmissionFilter && transmission !== transmissionFilter) showOption = false;
        
        option.style.display = showOption ? 'block' : 'none';
    });
    
    // Reset selection if current selection is hidden
    if (vehicleSelect.value && vehicleSelect.options[vehicleSelect.selectedIndex].style.display === 'none') {
        vehicleSelect.value = '';
        updatePricingSummary();
    }
}

// Check vehicle availability
function checkVehicleAvailability() {
    const vehicleSelect = document.getElementById('vehicle_id');
    const startDate = document.getElementById('start_date_time').value;
    const endDate = document.getElementById('end_date_time').value;
    const statusDiv = document.getElementById('availabilityStatus');
    
    if (!vehicleSelect.value || !startDate || !endDate) {
        showAlert('Please select a vehicle and dates first', 'warning');
        return;
    }
    
    statusDiv.innerHTML = '<div class="spinner-border spinner-border-sm me-2" role="status"></div>Checking availability...';
    statusDiv.style.display = 'block';
    
    // Simulate availability check (replace with actual API call)
    setTimeout(() => {
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        const isAvailable = selectedOption.getAttribute('data-available') === 'true';
        
        if (isAvailable) {
            statusDiv.innerHTML = '<div class="alert alert-success mb-0"><i class="fas fa-check-circle me-2"></i>Vehicle is available for the selected dates</div>';
        } else {
            statusDiv.innerHTML = '<div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Vehicle is currently unavailable</div>';
        }
    }, 1500);
}

// Open new customer modal (placeholder function)
function openNewCustomerModal() {
    // This would open a modal to add a new customer
    // For now, redirect to customer creation page
    window.open('<?php echo e(route("business.customers.create")); ?>', '_blank');
}

// Enhanced pricing calculation
function updatePricingSummary() {
    const rentalCharges = parseFloat(document.getElementById('rental_charges').value) || 0;
    const additionalCharges = parseFloat(document.getElementById('additional_charges').value) || 0;
    const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const advancePayment = parseFloat(document.getElementById('advance_payment').value) || 0;
    
    const subtotal = rentalCharges + additionalCharges;
    const total = subtotal - discountAmount;
    
    // Update the total amount field
    document.getElementById('total_amount').value = total.toFixed(2);
    
    // Update pricing summary in sidebar
    document.getElementById('summaryRentalCharges').textContent = `₹${rentalCharges.toFixed(2)}`;
    document.getElementById('summaryAdditionalCharges').textContent = `₹${additionalCharges.toFixed(2)}`;
    document.getElementById('summaryDiscount').textContent = `-₹${discountAmount.toFixed(2)}`;
    document.getElementById('summaryAdvance').textContent = `₹${advancePayment.toFixed(2)}`;
    document.getElementById('summaryTotal').textContent = `₹${total.toFixed(2)}`;
}

// Validate dates and prevent past date selection
function validateDates() {
    const startDateInput = document.getElementById('start_date_time');
    const endDateInput = document.getElementById('end_date_time');
    const validationMessage = document.getElementById('dateValidationMessage');
    
    const now = new Date();
    const startDate = new Date(startDateInput.value);
    const endDate = new Date(endDateInput.value);
    
    // Clear previous validation messages
    validationMessage.style.display = 'none';
    startDateInput.classList.remove('is-invalid');
    endDateInput.classList.remove('is-invalid');
    
    let isValid = true;
    let errorMessage = '';
    
    // Check if start date is in the past
    if (startDateInput.value && startDate < now) {
        startDateInput.classList.add('is-invalid');
        errorMessage = 'Pickup date and time cannot be in the past.';
        isValid = false;
    }
    
    // Check if end date is before start date
    if (startDateInput.value && endDateInput.value && endDate <= startDate) {
        endDateInput.classList.add('is-invalid');
        errorMessage = 'Drop-off date must be after pickup date.';
        isValid = false;
    }
    
    // Check if end date is in the past
    if (endDateInput.value && endDate < now) {
        endDateInput.classList.add('is-invalid');
        errorMessage = 'Drop-off date and time cannot be in the past.';
        isValid = false;
    }
    
    if (!isValid) {
        validationMessage.innerHTML = `
            <div class="alert alert-danger mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>${errorMessage}
            </div>
        `;
        validationMessage.style.display = 'block';
    }
    
    return isValid;
}

// Check availability after date change
function checkAvailabilityAfterDateChange() {
    const vehicleSelect = document.getElementById('vehicle_id');
    const startDate = document.getElementById('start_date_time').value;
    const endDate = document.getElementById('end_date_time').value;
    const statusDiv = document.getElementById('availabilityStatus');
    
    // Only check if both dates are selected and a vehicle is selected
    if (startDate && endDate && vehicleSelect.value) {
        checkVehicleAvailability();
    } else if (startDate && endDate) {
        // Show message that vehicle needs to be selected
        statusDiv.innerHTML = '<div class="alert alert-info mb-0"><i class="fas fa-info-circle me-2"></i>Please select a vehicle to check availability</div>';
        statusDiv.style.display = 'block';
    }
}

// Enhanced vehicle availability check with date validation
function checkVehicleAvailability() {
    const vehicleSelect = document.getElementById('vehicle_id');
    const startDate = document.getElementById('start_date_time').value;
    const endDate = document.getElementById('end_date_time').value;
    const statusDiv = document.getElementById('availabilityStatus');
    
    if (!vehicleSelect.value || !startDate || !endDate) {
        showAlert('Please select a vehicle and dates first', 'warning');
        return;
    }
    
    // Validate dates first
    if (!validateDates()) {
        statusDiv.innerHTML = '<div class="alert alert-danger mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Please correct the date selection first</div>';
        statusDiv.style.display = 'block';
        return;
    }
    
    statusDiv.innerHTML = '<div class="spinner-border spinner-border-sm me-2" role="status"></div>Checking availability...';
    statusDiv.style.display = 'block';
    
    // Simulate availability check (replace with actual API call)
    setTimeout(() => {
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        const isAvailable = selectedOption.getAttribute('data-available') === 'true';
        
        if (isAvailable) {
            statusDiv.innerHTML = '<div class="alert alert-success mb-0"><i class="fas fa-check-circle me-2"></i>Vehicle is available for the selected dates</div>';
        } else {
            statusDiv.innerHTML = '<div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Vehicle is currently unavailable</div>';
        }
    }, 1500);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('business.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/business/bookings/quick-create.blade.php ENDPATH**/ ?>