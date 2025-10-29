@extends('business.layouts.app')

@section('title', 'New Booking - Select Customer')
@section('page-title', 'New Booking')

@push('styles')
    <link href="{{ asset('css/booking-flow.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="booking-flow-container">
    <!-- Header -->
    <div class="booking-flow-header">
        <div class="container">
            <a href="{{ route('business.bookings.flow.step2') }}" class="back-link">
                <i class="fas fa-arrow-left me-2"></i>Back to Vehicles
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
                <div class="progress-step active">
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
                <!-- Left Column - Customer Selection -->
                <div class="col-lg-8">
                    <div class="booking-flow-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>Select Customer
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('business.bookings.flow.process-step3') }}" id="customerForm">
                                @csrf
                                <input type="hidden" name="customer_id" id="selected_customer_id" value="">
                                
                                <!-- Customer Search -->
                                <div class="customer-search-container">
                                    <label for="customer_search" class="form-label">Customer Information</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="customer_search" 
                                           placeholder="Search by name, phone, or customer ID..."
                                           autocomplete="off">
                                    <div class="customer-search-results" id="customerSearchResults"></div>
                                </div>
                                
                                <div class="text-center my-3">
                                    <span class="text-muted">or</span>
                                </div>
                                
                                <div class="text-center mb-4">
                                    <a href="{{ route('business.customers.create', ['type' => 'individual']) }}" 
                                       class="btn btn-outline-primary me-2" 
                                       target="_blank">
                                        <i class="fas fa-user-plus me-2"></i>Create New Individual Customer
                                    </a>
                                    <a href="{{ route('business.customers.create', ['type' => 'corporate']) }}" 
                                       class="btn btn-outline-warning" 
                                       target="_blank">
                                        <i class="fas fa-building me-2"></i>Create New Corporate Customer
                                    </a>
                                </div>
                                
                                <!-- Customer Type Selection -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <label class="form-label">Customer Type</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="customer_type" id="individual" value="individual" checked>
                                                <label class="form-check-label" for="individual">
                                                    Individual
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="customer_type" id="corporate" value="corporate">
                                                <label class="form-check-label" for="corporate">
                                                    Corporate
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Selected Customer Details -->
                                <div id="selectedCustomerDetails" class="customer-details-card" style="display: none;">
                                    <div class="customer-header">
                                        <h6 class="customer-name" id="selectedCustomerName"></h6>
                                        <span class="customer-type" id="selectedCustomerType"></span>
                                    </div>
                                    <div class="customer-details-grid" id="selectedCustomerGrid">
                                        <!-- Customer details will be populated here -->
                                    </div>
                                </div>
                                
                                <!-- Booking History -->
                                <div id="bookingHistory" class="booking-history-table" style="display: none;">
                                    <h6 class="mb-3">
                                        <i class="fas fa-history me-2"></i>Booking History
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Vehicle Details</th>
                                                    <th>Pickup Date</th>
                                                    <th>Return Date</th>
                                                    <th>Status</th>
                                                    <th>Amount Due</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="bookingHistoryBody">
                                                <!-- Booking history will be populated here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="booking-flow-actions">
                                    <a href="{{ route('business.bookings.flow.step2') }}" class="btn-back">
                                        <i class="fas fa-arrow-left me-2"></i>Back
                                    </a>
                                    <button type="submit" class="btn-next" id="proceedBtn" disabled>
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
                                {{ session('booking_flow.start_date_time') ? \Carbon\Carbon::parse(session('booking_flow.start_date_time'))->format('l, M d, Y') : 'Not set' }}<br>
                                <small>Time: {{ session('booking_flow.start_date_time') ? \Carbon\Carbon::parse(session('booking_flow.start_date_time'))->format('h:i A') : 'Not set' }}</small><br>
                                <small>Location: Garage</small>
                            </div>
                        </div>
                        
                        <div class="summary-section">
                            <div class="section-label">Drop</div>
                            <div class="section-value">
                                <i class="fas fa-calendar me-2"></i>
                                {{ session('booking_flow.end_date_time') ? \Carbon\Carbon::parse(session('booking_flow.end_date_time'))->format('l, M d, Y') : 'Not set' }}<br>
                                <small>Time: {{ session('booking_flow.end_date_time') ? \Carbon\Carbon::parse(session('booking_flow.end_date_time'))->format('h:i A') : 'Not set' }}</small><br>
                                <small>Location: Garage</small>
                            </div>
                        </div>
                        
                        <div class="summary-section">
                            <div class="section-label">Duration</div>
                            <div class="section-value">
                                <i class="fas fa-clock me-2"></i>
                                @if(session('booking_flow.start_date_time') && session('booking_flow.end_date_time'))
                                    @php
                                        $start = \Carbon\Carbon::parse(session('booking_flow.start_date_time'));
                                        $end = \Carbon\Carbon::parse(session('booking_flow.end_date_time'));
                                        $hours = $start->diffInHours($end);
                                        $days = ceil($hours / 24);
                                    @endphp
                                    {{ $days }} day(s) ({{ $hours }} hours)
                                @else
                                    Not set
                                @endif
                            </div>
                        </div>
                        
                        <div class="summary-section">
                            <div class="section-label">Vehicle</div>
                            <div class="section-value">
                                <i class="fas fa-car me-2"></i>
                                @if($vehicle)
                                    {{ $vehicle->vehicle_make }} {{ $vehicle->vehicle_model }}<br>
                                    <small>({{ $vehicle->vehicle_number }})</small><br>
                                    <small class="text-primary">₹{{ number_format($vehicle->rental_price_24h ?? 1000) }}/day</small>
                                @else
                                    Not selected
                                @endif
                            </div>
                        </div>
                        
                        <div class="summary-section">
                            <div class="section-label">Customer</div>
                            <div class="section-value" id="summary-customer">
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
let selectedCustomerId = null;
let customers = @json($customers);

// Customer search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('customer_search');
    const searchResults = document.getElementById('customerSearchResults');
    const selectedCustomerDetails = document.getElementById('selectedCustomerDetails');
    const bookingHistory = document.getElementById('bookingHistory');
    const proceedBtn = document.getElementById('proceedBtn');
    
    // Search customers
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }
        
        const filteredCustomers = customers.filter(customer => {
            const name = (customer.full_name || customer.company_name || '').toLowerCase();
            const phone = (customer.mobile_number || '').toLowerCase();
            const email = (customer.email || '').toLowerCase();
            
            return name.includes(query) || phone.includes(query) || email.includes(query);
        });
        
        displaySearchResults(filteredCustomers);
    });
    
    function displaySearchResults(filteredCustomers) {
        if (filteredCustomers.length === 0) {
            searchResults.innerHTML = '<div class="customer-search-item">No customers found</div>';
        } else {
            searchResults.innerHTML = filteredCustomers.map(customer => `
                <div class="customer-search-item" onclick="selectCustomer(${customer.id})">
                    <div class="customer-name">${customer.full_name || customer.company_name}</div>
                    <div class="customer-details">
                        ${customer.mobile_number} • ${customer.email || 'No email'} • 
                        ${customer.customer_type === 'corporate' ? 'Corporate' : 'Individual'}
                    </div>
                </div>
            `).join('');
        }
        
        searchResults.style.display = 'block';
    }
    
    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
    
    // Form validation
    document.getElementById('customerForm').addEventListener('submit', function(e) {
        if (!selectedCustomerId) {
            e.preventDefault();
            alert('Please select a customer to continue.');
            return;
        }
    });
});

function selectCustomer(customerId) {
    const customer = customers.find(c => c.id === customerId);
    if (!customer) return;
    
    selectedCustomerId = customerId;
    document.getElementById('selected_customer_id').value = customerId;
    
    // Hide search results
    document.getElementById('customerSearchResults').style.display = 'none';
    
    // Clear search input
    document.getElementById('customer_search').value = '';
    
    // Show customer details
    displayCustomerDetails(customer);
    
    // Update customer type radio
    document.getElementById(customer.customer_type).checked = true;
    
    // Update summary
    updateCustomerSummary(customer);
    
    // Enable proceed button
    document.getElementById('proceedBtn').disabled = false;
}

function displayCustomerDetails(customer) {
    const selectedCustomerDetails = document.getElementById('selectedCustomerDetails');
    const selectedCustomerName = document.getElementById('selectedCustomerName');
    const selectedCustomerType = document.getElementById('selectedCustomerType');
    const selectedCustomerGrid = document.getElementById('selectedCustomerGrid');
    
    // Update header
    selectedCustomerName.textContent = customer.full_name || customer.company_name;
    selectedCustomerType.textContent = customer.customer_type === 'corporate' ? 'Corporate' : 'Individual';
    
    // Update details grid
    const details = [];
    
    if (customer.customer_type === 'individual') {
        details.push(
            { label: 'First Name', value: customer.first_name || '-' },
            { label: 'Last Name', value: customer.last_name || '-' },
            { label: 'Email Address', value: customer.email || '-' },
            { label: 'Phone Number', value: customer.mobile_number || '-' },
            { label: 'Date of Birth', value: customer.date_of_birth ? new Date(customer.date_of_birth).toLocaleDateString() : '-' },
            { label: 'Address 1', value: customer.address_line_1 || '-' },
            { label: 'Address 2', value: customer.address_line_2 || '-' },
            { label: 'City', value: customer.city || '-' },
            { label: 'State', value: customer.state || '-' },
            { label: 'Country', value: customer.country || '-' },
            { label: 'Pincode', value: customer.pincode || '-' }
        );
    } else {
        details.push(
            { label: 'Company Name', value: customer.company_name || '-' },
            { label: 'Contact Person', value: customer.contact_person_name || '-' },
            { label: 'Email Address', value: customer.email || '-' },
            { label: 'Phone Number', value: customer.mobile_number || '-' },
            { label: 'GST Number', value: customer.gst_number || '-' },
            { label: 'Address 1', value: customer.address_line_1 || '-' },
            { label: 'Address 2', value: customer.address_line_2 || '-' },
            { label: 'City', value: customer.city || '-' },
            { label: 'State', value: customer.state || '-' },
            { label: 'Country', value: customer.country || '-' },
            { label: 'Pincode', value: customer.pincode || '-' }
        );
    }
    
    selectedCustomerGrid.innerHTML = details.map(detail => `
        <div class="detail-item">
            <div class="detail-label">${detail.label}</div>
            <div class="detail-value">${detail.value}</div>
        </div>
    `).join('');
    
    // Show customer details
    selectedCustomerDetails.style.display = 'block';
    
    // Show booking history (mock data for now)
    displayBookingHistory();
}

function displayBookingHistory() {
    const bookingHistory = document.getElementById('bookingHistory');
    const bookingHistoryBody = document.getElementById('bookingHistoryBody');
    
    // Mock booking history data
    const mockBookings = [
        {
            id: 'BK-1023',
            vehicle: 'Suzuki Swift Dezire',
            pickup: 'Dec 30, 2019 05:18',
            return: 'Jan 05, 2020 05:18',
            status: 'Completed',
            amountDue: '₹0.00',
            amount: '₹3,500.00'
        },
        {
            id: 'BK-1092',
            vehicle: 'Hyundai Grand I10',
            pickup: 'Dec 30, 2019 05:18',
            return: '-',
            status: 'In route',
            amountDue: '₹0.00',
            amount: '-'
        }
    ];
    
    bookingHistoryBody.innerHTML = mockBookings.map(booking => `
        <tr>
            <td>${booking.id}</td>
            <td>${booking.vehicle}</td>
            <td>${booking.pickup}</td>
            <td>${booking.return}</td>
            <td><span class="status-badge status-${booking.status.toLowerCase().replace(' ', '-')}">${booking.status}</span></td>
            <td>${booking.amountDue}</td>
            <td>${booking.amount}</td>
        </tr>
    `).join('');
    
    bookingHistory.style.display = 'block';
}

function updateCustomerSummary(customer) {
    const summaryCustomer = document.getElementById('summary-customer');
    summaryCustomer.innerHTML = `
        <i class="fas fa-user me-2"></i>
        ${customer.full_name || customer.company_name}<br>
        <small>${customer.mobile_number}</small><br>
        <small>${customer.customer_type === 'corporate' ? 'Corporate' : 'Individual'}</small>
    `;
}
</script>
@endpush
