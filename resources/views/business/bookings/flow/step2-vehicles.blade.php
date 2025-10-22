@extends('business.layouts.booking-flow')

@section('title', 'New Booking - Select Vehicle')
@section('page-title', 'New Booking')

@push('styles')
    <link href="{{ asset('css/booking-flow.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="booking-flow-container">
    <!-- Header -->
    <div class="booking-flow-header">
        <div class="container">
            <a href="{{ route('business.bookings.flow.step1') }}" class="back-link">
                <i class="fas fa-arrow-left me-2"></i>Back to Dates
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
                <div class="progress-step active">
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
                <!-- Left Column - Vehicle Selection -->
                <div class="col-lg-8">
                    <div class="booking-flow-card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-0">
                                        <i class="fas fa-car me-2"></i>Select Vehicle
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-6">
                                            <select class="form-select form-select-sm" id="sortBy">
                                                <option value="price-low">Sort by: low to high</option>
                                                <option value="price-high">Sort by: high to low</option>
                                                <option value="name">Sort by: name</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex gap-2">
                                                <select class="form-select form-select-sm" id="transmissionFilter">
                                                    <option value="">Transmission</option>
                                                    <option value="manual">Manual</option>
                                                    <option value="automatic">Automatic</option>
                                                </select>
                                                <select class="form-select form-select-sm" id="seatsFilter">
                                                    <option value="">Seats</option>
                                                    <option value="2">2</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8+</option>
                                                </select>
                                                <select class="form-select form-select-sm" id="fuelFilter">
                                                    <option value="">Fuel</option>
                                                    <option value="petrol">Petrol</option>
                                                    <option value="diesel">Diesel</option>
                                                    <option value="hybrid">Hybrid</option>
                                                    <option value="electric">Electric</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('business.bookings.flow.process-step2') }}" id="vehicleForm">
                                @csrf
                                <input type="hidden" name="vehicle_id" id="selected_vehicle_id" value="">
                                
                                @if($vehicles->count() > 0)
                                    <div class="vehicle-grid" id="vehicleGrid">
                                        @foreach($vehicles as $vehicle)
                                            <div class="vehicle-card" 
                                                 data-vehicle-id="{{ $vehicle->id }}"
                                                 data-type="{{ $vehicle->vehicle_type }}"
                                                 data-seating="{{ $vehicle->seating_capacity }}"
                                                 data-fuel="{{ $vehicle->fuel_type }}"
                                                 data-transmission="{{ $vehicle->transmission_type }}"
                                                 data-price="{{ $vehicle->rental_price_24h ?? 1000 }}">
                                                <div class="vehicle-image">
                                                    <i class="fas fa-car fa-3x text-muted"></i>
                                                </div>
                                                <div class="vehicle-name">{{ $vehicle->vehicle_make }} {{ $vehicle->vehicle_model }}</div>
                                                <div class="vehicle-reg">({{ $vehicle->vehicle_number }})</div>
                                                <div class="vehicle-specs">
                                                    {{ ucfirst($vehicle->transmission_type) }} Transmission · 
                                                    {{ $vehicle->seating_capacity }} Seats · 
                                                    {{ ucfirst($vehicle->fuel_type) }} · 
                                                    {{ $vehicle->mileage ?? 'N/A' }}KM/Ltr
                                                </div>
                                                <div class="vehicle-price">
                                                    ₹{{ number_format($vehicle->rental_price_24h ?? 1000) }}/day
                                                    <i class="fas fa-info-circle ms-1" title="Base rental price"></i>
                                                </div>
                                                <button type="button" class="book-btn" onclick="selectVehicle({{ $vehicle->id }})">
                                                    Book Now
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-car fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No vehicles available</h5>
                                        <p class="text-muted">No vehicles are available for the selected date range.</p>
                                        <a href="{{ route('business.bookings.flow.step1') }}" class="btn btn-primary">
                                            <i class="fas fa-arrow-left me-2"></i>Change Dates
                                        </a>
                                    </div>
                                @endif

                                <div class="booking-flow-actions">
                                    <a href="{{ route('business.bookings.flow.step1') }}" class="btn-back">
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
                                {{ $startDateTime->format('l, M d, Y') }}<br>
                                <small>Time: {{ $startDateTime->format('h:i A') }}</small><br>
                                <small>Location: Garage</small>
                            </div>
                        </div>
                        
                        <div class="summary-section">
                            <div class="section-label">Drop</div>
                            <div class="section-value">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $endDateTime->format('l, M d, Y') }}<br>
                                <small>Time: {{ $endDateTime->format('h:i A') }}</small><br>
                                <small>Location: Garage</small>
                            </div>
                        </div>
                        
                        <div class="summary-section">
                            <div class="section-label">Duration</div>
                            <div class="section-value">
                                <i class="fas fa-clock me-2"></i>
                                @php
                                    $hours = $startDateTime->diffInHours($endDateTime);
                                    $days = ceil($hours / 24);
                                @endphp
                                {{ $days }} day(s) ({{ $hours }} hours)
                            </div>
                        </div>
                        
                        <div class="summary-section">
                            <div class="section-label">Vehicle</div>
                            <div class="section-value" id="summary-vehicle">
                                <i class="fas fa-car me-2"></i>
                                <span id="vehicle-name">Not selected</span>
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
let selectedVehicleId = null;

function selectVehicle(vehicleId) {
    // Remove previous selection
    document.querySelectorAll('.vehicle-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selection to clicked card
    const vehicleCard = document.querySelector(`[data-vehicle-id="${vehicleId}"]`);
    vehicleCard.classList.add('selected');
    
    // Update form
    selectedVehicleId = vehicleId;
    document.getElementById('selected_vehicle_id').value = vehicleId;
    
    // Update summary
    updateVehicleSummary(vehicleCard);
    
    // Enable proceed button
    document.getElementById('proceedBtn').disabled = false;
}

function updateVehicleSummary(vehicleCard) {
    const vehicleName = vehicleCard.querySelector('.vehicle-name').textContent;
    const vehicleReg = vehicleCard.querySelector('.vehicle-reg').textContent;
    const vehiclePrice = vehicleCard.querySelector('.vehicle-price').textContent;
    
    document.getElementById('vehicle-name').innerHTML = `
        ${vehicleName}<br>
        <small>${vehicleReg}</small><br>
        <small class="text-primary">${vehiclePrice}</small>
    `;
}

// Filter and sort functionality
document.addEventListener('DOMContentLoaded', function() {
    const sortSelect = document.getElementById('sortBy');
    const transmissionFilter = document.getElementById('transmissionFilter');
    const seatsFilter = document.getElementById('seatsFilter');
    const fuelFilter = document.getElementById('fuelFilter');
    
    function filterAndSortVehicles() {
        const vehicleCards = document.querySelectorAll('.vehicle-card');
        const sortValue = sortSelect.value;
        const transmissionValue = transmissionFilter.value;
        const seatsValue = seatsFilter.value;
        const fuelValue = fuelFilter.value;
        
        // Filter vehicles
        vehicleCards.forEach(card => {
            let show = true;
            
            if (transmissionValue && card.dataset.transmission !== transmissionValue) {
                show = false;
            }
            
            if (seatsValue && card.dataset.seating !== seatsValue) {
                show = false;
            }
            
            if (fuelValue && card.dataset.fuel !== fuelValue) {
                show = false;
            }
            
            card.style.display = show ? 'block' : 'none';
        });
        
        // Sort vehicles
        const visibleCards = Array.from(vehicleCards).filter(card => card.style.display !== 'none');
        
        visibleCards.sort((a, b) => {
            switch (sortValue) {
                case 'price-low':
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                case 'price-high':
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                case 'name':
                    const nameA = a.querySelector('.vehicle-name').textContent;
                    const nameB = b.querySelector('.vehicle-name').textContent;
                    return nameA.localeCompare(nameB);
                default:
                    return 0;
            }
        });
        
        // Reorder in DOM
        const vehicleGrid = document.getElementById('vehicleGrid');
        visibleCards.forEach(card => {
            vehicleGrid.appendChild(card);
        });
    }
    
    // Add event listeners
    sortSelect.addEventListener('change', filterAndSortVehicles);
    transmissionFilter.addEventListener('change', filterAndSortVehicles);
    seatsFilter.addEventListener('change', filterAndSortVehicles);
    fuelFilter.addEventListener('change', filterAndSortVehicles);
    
    // Form validation
    document.getElementById('vehicleForm').addEventListener('submit', function(e) {
        if (!selectedVehicleId) {
            e.preventDefault();
            alert('Please select a vehicle to continue.');
            return;
        }
    });
});
</script>
@endpush
