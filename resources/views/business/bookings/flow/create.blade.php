@extends('business.layouts.booking-flow')

@section('page-title', 'New Booking')

@section('content')
<div class="booking-flow-container">
    <!-- Header -->
    <div class="booking-flow-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <a href="{{ route('business.bookings.index') }}" class="back-link">
                        <i class="fas fa-arrow-left me-2"></i>Back to Bookings
                    </a>
                    <h1>New Booking</h1>
                </div>
                <div class="col-md-6 text-end">
                    <div class="booking-summary-panel">
                        <h6><i class="fas fa-info-circle me-2"></i>Booking Summary</h6>
                        <div id="booking-summary-content">
                            <small class="text-muted">Complete the steps to see booking details</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="booking-progress">
        <div class="container">
            <div class="progress-steps">
                <div class="progress-step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-title">Dates</div>
                </div>
                <div class="progress-step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-title">Vehicle</div>
                </div>
                <div class="progress-step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-title">Customer</div>
                </div>
                <div class="progress-step" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-title">Billing</div>
                </div>
                <div class="progress-step" data-step="5">
                    <div class="step-number">5</div>
                    <div class="step-title">Confirm</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="booking-flow-content">
        <div class="container">
            <form id="bookingForm" method="POST" action="{{ route('business.bookings.store') }}">
                @csrf
                
                <!-- Step 1: Dates -->
                <div class="step-content active" id="step1">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title text-center mb-4">
                                        <i class="fas fa-calendar-alt me-2"></i>Select Dates & Time
                                    </h4>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="start_date_time" class="form-label">Pickup Date & Time</label>
                                            <input type="datetime-local" 
                                                   class="form-control" 
                                                   id="start_date_time" 
                                                   name="start_date_time" 
                                                   required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="end_date_time" class="form-label">Drop-off Date & Time</label>
                                            <input type="datetime-local" 
                                                   class="form-control" 
                                                   id="end_date_time" 
                                                   name="end_date_time" 
                                                   required>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center mt-4">
                                        <button type="button" class="btn btn-primary btn-lg" onclick="nextStep(2)">
                                            Next: Select Vehicle <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Vehicle Selection -->
                <div class="step-content" id="step2">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">
                                        <i class="fas fa-car me-2"></i>Select Vehicle
                                    </h4>
                                    
                                    <!-- Filters -->
                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <label for="vehicle_type_filter" class="form-label">Vehicle Type</label>
                                            <select class="form-select" id="vehicle_type_filter">
                                                <option value="">All Types</option>
                                                <option value="car">Car</option>
                                                <option value="bike_scooter">Bike/Scooter</option>
                                                <option value="truck">Truck</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="fuel_type_filter" class="form-label">Fuel Type</label>
                                            <select class="form-select" id="fuel_type_filter">
                                                <option value="">All Fuel Types</option>
                                                <option value="petrol">Petrol</option>
                                                <option value="diesel">Diesel</option>
                                                <option value="electric">Electric</option>
                                                <option value="hybrid">Hybrid</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="sort_by" class="form-label">Sort By</label>
                                            <select class="form-select" id="sort_by">
                                                <option value="price_low">Price: Low to High</option>
                                                <option value="price_high">Price: High to Low</option>
                                                <option value="name">Name: A to Z</option>
                                                <option value="year">Year: Newest First</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Vehicle Grid -->
                                    <div class="vehicle-grid" id="vehicleGrid">
                                        <div class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading vehicles...</span>
                                            </div>
                                            <p class="mt-3 text-muted">Loading available vehicles...</p>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center mt-4">
                                        <button type="button" class="btn btn-secondary me-3" onclick="prevStep(1)">
                                            <i class="fas fa-arrow-left me-2"></i>Back to Dates
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="nextStep(3)" id="vehicleNextBtn" disabled>
                                            Next: Select Customer <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Selected Vehicle Summary -->
                        <div class="col-lg-4">
                            <div class="card" id="selectedVehicleCard" style="display: none;">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Selected Vehicle</h6>
                                </div>
                                <div class="card-body" id="selectedVehicleContent">
                                    <!-- Vehicle details will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Customer Selection -->
                <div class="step-content" id="step3">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">
                                        <i class="fas fa-user me-2"></i>Select Customer
                                    </h4>
                                    
                                    <!-- Customer Search -->
                                    <div class="mb-4">
                                        <label for="customer_search" class="form-label">Search Customer</label>
                                        <div class="position-relative">
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="customer_search" 
                                                   placeholder="Search by name, phone, or email...">
                                            <div class="customer-search-results" id="customerSearchResults"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Customer Selection -->
                                    <div id="customerSelection">
                                        <div class="text-center py-4">
                                            <p class="text-muted">Search for a customer or create a new one</p>
                                            <a href="{{ route('business.customers.create') }}" class="btn btn-outline-primary" target="_blank">
                                                <i class="fas fa-plus me-2"></i>Create New Customer
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center mt-4">
                                        <button type="button" class="btn btn-secondary me-3" onclick="prevStep(2)">
                                            <i class="fas fa-arrow-left me-2"></i>Back to Vehicle
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="nextStep(4)" id="customerNextBtn" disabled>
                                            Next: Billing <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Selected Customer Summary -->
                        <div class="col-lg-4">
                            <div class="card" id="selectedCustomerCard" style="display: none;">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Selected Customer</h6>
                                </div>
                                <div class="card-body" id="selectedCustomerContent">
                                    <!-- Customer details will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Billing -->
                <div class="step-content" id="step4">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">
                                        <i class="fas fa-calculator me-2"></i>Billing Information
                                    </h4>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="rental_charges" class="form-label">Rental Charges</label>
                                            <div class="billing-input-group">
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="rental_charges" 
                                                       name="rental_charges" 
                                                       step="0.01" 
                                                       readonly>
                                                <div class="input-group-text">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="rental_currency" value="INR" checked>
                                                        <label class="form-check-label">INR</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="rental_currency" value="USD">
                                                        <label class="form-check-label">USD</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="additional_charges" class="form-label">Additional Charges</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="additional_charges" 
                                                   name="additional_charges" 
                                                   step="0.01" 
                                                   value="0">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="discount_amount" class="form-label">Discount Amount</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="discount_amount" 
                                                   name="discount_amount" 
                                                   step="0.01" 
                                                   value="0">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="advance_payment" class="form-label">Advance Payment</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="advance_payment" 
                                                   name="advance_payment" 
                                                   step="0.01" 
                                                   value="0">
                                        </div>
                                    </div>
                                    
                                    <div class="text-center mt-4">
                                        <button type="button" class="btn btn-secondary me-3" onclick="prevStep(3)">
                                            <i class="fas fa-arrow-left me-2"></i>Back to Customer
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="nextStep(5)">
                                            Next: Confirm Booking <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Billing Summary -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-receipt me-2"></i>Billing Summary</h6>
                                </div>
                                <div class="card-body" id="billingSummary">
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
                        </div>
                    </div>
                </div>

                <!-- Step 5: Confirmation -->
                <div class="step-content" id="step5">
                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title text-center mb-4">
                                        <i class="fas fa-check-circle me-2"></i>Confirm Booking
                                    </h4>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Booking Details</h6>
                                            <div id="confirmBookingDetails">
                                                <!-- Booking details will be populated here -->
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Customer Details</h6>
                                            <div id="confirmCustomerDetails">
                                                <!-- Customer details will be populated here -->
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <h6>Vehicle Details</h6>
                                            <div id="confirmVehicleDetails">
                                                <!-- Vehicle details will be populated here -->
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Billing Summary</h6>
                                            <div id="confirmBillingDetails">
                                                <!-- Billing details will be populated here -->
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center mt-4">
                                        <button type="button" class="btn btn-secondary me-3" onclick="prevStep(4)">
                                            <i class="fas fa-arrow-left me-2"></i>Back to Billing
                                        </button>
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-check me-2"></i>Create Booking
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden inputs for form submission -->
<input type="hidden" id="selected_vehicle_id" name="vehicle_id">
<input type="hidden" id="selected_customer_id" name="customer_id">

<script>
let currentStep = 1;
let selectedVehicle = null;
let selectedCustomer = null;
let vehicles = [];

// Initialize the booking flow
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to today
    const today = new Date().toISOString().slice(0, 16);
    document.getElementById('start_date_time').min = today;
    document.getElementById('end_date_time').min = today;
    
    // Load vehicles when step 2 is reached
    document.getElementById('start_date_time').addEventListener('change', loadVehicles);
    document.getElementById('end_date_time').addEventListener('change', loadVehicles);
    
    // Initialize billing calculations
    initializeBillingCalculations();
    
    // Initialize customer search
    initializeCustomerSearch();
});

// Step navigation functions
function nextStep(step) {
    if (validateCurrentStep()) {
        hideAllSteps();
        document.getElementById('step' + step).classList.add('active');
        updateProgressBar(step);
        currentStep = step;
        
        // Load data for specific steps
        if (step === 2) {
            loadVehicles();
        } else if (step === 5) {
            populateConfirmationStep();
        }
        
        updateBookingSummary();
    }
}

function prevStep(step) {
    hideAllSteps();
    document.getElementById('step' + step).classList.add('active');
    updateProgressBar(step);
    currentStep = step;
    updateBookingSummary();
}

function hideAllSteps() {
    for (let i = 1; i <= 5; i++) {
        document.getElementById('step' + i).classList.remove('active');
    }
}

function updateProgressBar(activeStep) {
    document.querySelectorAll('.progress-step').forEach((step, index) => {
        step.classList.remove('active', 'completed');
        if (index + 1 < activeStep) {
            step.classList.add('completed');
        } else if (index + 1 === activeStep) {
            step.classList.add('active');
        }
    });
}

function validateCurrentStep() {
    switch (currentStep) {
        case 1:
            const startDate = document.getElementById('start_date_time').value;
            const endDate = document.getElementById('end_date_time').value;
            if (!startDate || !endDate) {
                alert('Please select both pickup and drop-off dates.');
                return false;
            }
            if (new Date(startDate) >= new Date(endDate)) {
                alert('Drop-off date must be after pickup date.');
                return false;
            }
            return true;
        case 2:
            if (!selectedVehicle) {
                alert('Please select a vehicle.');
                return false;
            }
            return true;
        case 3:
            if (!selectedCustomer) {
                alert('Please select a customer.');
                return false;
            }
            return true;
        default:
            return true;
    }
}

// Vehicle loading and selection
function loadVehicles() {
    const startDateTime = document.getElementById('start_date_time').value;
    const endDateTime = document.getElementById('end_date_time').value;
    
    if (!startDateTime || !endDateTime) {
        return;
    }
    
    // Send full datetime information to API
    const startDate = startDateTime.split('T')[0];
    const startTime = startDateTime.split('T')[1];
    const endDate = endDateTime.split('T')[0];
    const endTime = endDateTime.split('T')[1];
    
    const vehicleGrid = document.getElementById('vehicleGrid');
    vehicleGrid.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted">Loading available vehicles...</p></div>';
    
    const apiUrl = `{{ url('business/api/vehicles/available') }}?start_date=${startDate}&start_time=${startTime}&end_date=${endDate}&end_time=${endTime}`;
    
    fetch(apiUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        vehicles = data.vehicles || [];
        displayVehicles(vehicles);
    })
    .catch(error => {
        console.error('Error loading vehicles:', error);
        vehicleGrid.innerHTML = '<div class="text-center py-5"><p class="text-danger">Error loading vehicles. Please try again.</p></div>';
    });
}

function displayVehicles(vehiclesToShow) {
    const vehicleGrid = document.getElementById('vehicleGrid');
    
    if (vehiclesToShow.length === 0) {
        vehicleGrid.innerHTML = '<div class="text-center py-5"><p class="text-muted">No vehicles available for the selected dates.</p></div>';
        return;
    }
    
    vehicleGrid.innerHTML = vehiclesToShow.map(vehicle => `
        <div class="vehicle-card" onclick="selectVehicle(${vehicle.id})">
            <div class="vehicle-image">
                <i class="fas fa-${vehicle.vehicle_type === 'car' ? 'car' : (vehicle.vehicle_type === 'bike_scooter' ? 'motorcycle' : 'truck')} fa-3x"></i>
            </div>
            <div class="vehicle-details">
                <h6>${vehicle.vehicle_make} ${vehicle.vehicle_model}</h6>
                <p class="text-muted">${vehicle.vehicle_number}</p>
                <div class="vehicle-features">
                    <span class="badge bg-secondary">${vehicle.fuel_type}</span>
                    <span class="badge bg-info">${vehicle.transmission_type}</span>
                </div>
                <div class="vehicle-price">
                    <strong>₹${vehicle.rental_price_24h}/day</strong>
                </div>
            </div>
        </div>
    `).join('');
}

function selectVehicle(vehicleId) {
    selectedVehicle = vehicles.find(v => v.id === vehicleId);
    document.getElementById('selected_vehicle_id').value = vehicleId;
    
    // Update UI
    document.querySelectorAll('.vehicle-card').forEach(card => card.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    
    // Show selected vehicle card
    const selectedCard = document.getElementById('selectedVehicleCard');
    const selectedContent = document.getElementById('selectedVehicleContent');
    
    selectedContent.innerHTML = `
        <h6>${selectedVehicle.vehicle_make} ${selectedVehicle.vehicle_model}</h6>
        <p class="text-muted">${selectedVehicle.vehicle_number}</p>
        <p><strong>₹${selectedVehicle.rental_price_24h}/day</strong></p>
    `;
    
    selectedCard.style.display = 'block';
    document.getElementById('vehicleNextBtn').disabled = false;
    
    updateBookingSummary();
}

// Customer search and selection
function initializeCustomerSearch() {
    const searchInput = document.getElementById('customer_search');
    const searchResults = document.getElementById('customerSearchResults');
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length < 2) {
            searchResults.innerHTML = '';
            return;
        }
        
        fetch(`{{ url('business/api/customers/search') }}?q=${encodeURIComponent(query)}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.customers && data.customers.length > 0) {
                searchResults.innerHTML = data.customers.map(customer => `
                    <div class="customer-result-item" onclick="selectCustomer(${customer.id}, '${customer.name}', '${customer.phone}', '${customer.email}')">
                        <div class="customer-name">${customer.name}</div>
                        <div class="customer-details">${customer.phone} • ${customer.email}</div>
                    </div>
                `).join('');
            } else {
                searchResults.innerHTML = '<div class="customer-result-item text-muted">No customers found</div>';
            }
        })
        .catch(error => {
            console.error('Error searching customers:', error);
            searchResults.innerHTML = '<div class="customer-result-item text-danger">Error searching customers</div>';
        });
    });
}

function selectCustomer(customerId, name, phone, email) {
    selectedCustomer = { id: customerId, name, phone, email };
    document.getElementById('selected_customer_id').value = customerId;
    
    // Update UI
    const customerSelection = document.getElementById('customerSelection');
    customerSelection.innerHTML = `
        <div class="alert alert-success">
            <h6><i class="fas fa-check-circle me-2"></i>Selected Customer</h6>
            <p class="mb-1"><strong>${name}</strong></p>
            <p class="mb-0 text-muted">${phone} • ${email}</p>
        </div>
    `;
    
    // Show selected customer card
    const selectedCard = document.getElementById('selectedCustomerCard');
    const selectedContent = document.getElementById('selectedCustomerContent');
    
    selectedContent.innerHTML = `
        <h6>${name}</h6>
        <p class="text-muted">${phone}</p>
        <p class="text-muted">${email}</p>
    `;
    
    selectedCard.style.display = 'block';
    document.getElementById('customerNextBtn').disabled = false;
    
    // Clear search
    document.getElementById('customer_search').value = '';
    document.getElementById('customerSearchResults').innerHTML = '';
    
    updateBookingSummary();
}

// Billing calculations
function initializeBillingCalculations() {
    const inputs = ['rental_charges', 'additional_charges', 'discount_amount', 'advance_payment'];
    inputs.forEach(inputId => {
        document.getElementById(inputId).addEventListener('input', updateBillingSummary);
    });
}

function updateBillingSummary() {
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

// Confirmation step
function populateConfirmationStep() {
    if (!selectedVehicle || !selectedCustomer) return;
    
    const startDate = document.getElementById('start_date_time').value;
    const endDate = document.getElementById('end_date_time').value;
    const rentalCharges = document.getElementById('rental_charges').value;
    const additionalCharges = document.getElementById('additional_charges').value;
    const discountAmount = document.getElementById('discount_amount').value;
    const advancePayment = document.getElementById('advance_payment').value;
    
    // Calculate total
    const subtotal = parseFloat(rentalCharges) + parseFloat(additionalCharges);
    const total = subtotal - parseFloat(discountAmount);
    
    document.getElementById('confirmBookingDetails').innerHTML = `
        <p><strong>Pickup:</strong> ${new Date(startDate).toLocaleString()}</p>
        <p><strong>Drop-off:</strong> ${new Date(endDate).toLocaleString()}</p>
    `;
    
    document.getElementById('confirmCustomerDetails').innerHTML = `
        <p><strong>Name:</strong> ${selectedCustomer.name}</p>
        <p><strong>Phone:</strong> ${selectedCustomer.phone}</p>
        <p><strong>Email:</strong> ${selectedCustomer.email}</p>
    `;
    
    document.getElementById('confirmVehicleDetails').innerHTML = `
        <p><strong>Vehicle:</strong> ${selectedVehicle.vehicle_make} ${selectedVehicle.vehicle_model}</p>
        <p><strong>Number:</strong> ${selectedVehicle.vehicle_number}</p>
        <p><strong>Price:</strong> ₹${selectedVehicle.rental_price_24h}/day</p>
    `;
    
    document.getElementById('confirmBillingDetails').innerHTML = `
        <p><strong>Rental Charges:</strong> ₹${rentalCharges}</p>
        <p><strong>Additional Charges:</strong> ₹${additionalCharges}</p>
        <p><strong>Discount:</strong> -₹${discountAmount}</p>
        <p><strong>Advance Payment:</strong> ₹${advancePayment}</p>
        <hr>
        <p><strong>Total Amount:</strong> ₹${total.toFixed(2)}</p>
    `;
}

// Update booking summary in header
function updateBookingSummary() {
    const summaryContent = document.getElementById('booking-summary-content');
    
    if (currentStep === 1) {
        const startDate = document.getElementById('start_date_time').value;
        const endDate = document.getElementById('end_date_time').value;
        if (startDate && endDate) {
            summaryContent.innerHTML = `
                <small><strong>Dates:</strong> ${new Date(startDate).toLocaleDateString()} - ${new Date(endDate).toLocaleDateString()}</small>
            `;
        }
    } else if (currentStep >= 2 && selectedVehicle) {
        summaryContent.innerHTML = `
            <small><strong>Vehicle:</strong> ${selectedVehicle.vehicle_make} ${selectedVehicle.vehicle_model}</small><br>
            <small><strong>Price:</strong> ₹${selectedVehicle.rental_price_24h}/day</small>
        `;
    } else if (currentStep >= 3 && selectedCustomer) {
        summaryContent.innerHTML = `
            <small><strong>Customer:</strong> ${selectedCustomer.name}</small><br>
            <small><strong>Vehicle:</strong> ${selectedVehicle.vehicle_make} ${selectedVehicle.vehicle_model}</small>
        `;
    }
}

// Filter and sort vehicles
document.getElementById('vehicle_type_filter').addEventListener('change', filterVehicles);
document.getElementById('fuel_type_filter').addEventListener('change', filterVehicles);
document.getElementById('sort_by').addEventListener('change', filterVehicles);

function filterVehicles() {
    const typeFilter = document.getElementById('vehicle_type_filter').value;
    const fuelFilter = document.getElementById('fuel_type_filter').value;
    const sortBy = document.getElementById('sort_by').value;
    
    let filteredVehicles = [...vehicles];
    
    if (typeFilter) {
        filteredVehicles = filteredVehicles.filter(v => v.vehicle_type === typeFilter);
    }
    
    if (fuelFilter) {
        filteredVehicles = filteredVehicles.filter(v => v.fuel_type === fuelFilter);
    }
    
    // Sort vehicles
    switch (sortBy) {
        case 'price_low':
            filteredVehicles.sort((a, b) => a.rental_price_24h - b.rental_price_24h);
            break;
        case 'price_high':
            filteredVehicles.sort((a, b) => b.rental_price_24h - a.rental_price_24h);
            break;
        case 'name':
            filteredVehicles.sort((a, b) => (a.vehicle_make + ' ' + a.vehicle_model).localeCompare(b.vehicle_make + ' ' + b.vehicle_model));
            break;
        case 'year':
            filteredVehicles.sort((a, b) => b.vehicle_year - a.vehicle_year);
            break;
    }
    
    displayVehicles(filteredVehicles);
}
</script>
@endsection
