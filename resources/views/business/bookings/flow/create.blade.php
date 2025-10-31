@extends('business.layouts.app')

@section('title', 'New Booking')
@section('page-title', 'New Booking')

@section('content')
<div class="booking-flow">
    @include('business.bookings.flow.partials.progress')

    <div class="row">
        <div class="col-lg-8">
            <!-- Step 1: Dates -->
            <section id="step-1" data-step="1" class="flow-step">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Pick-up date & time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="pickup_datetime" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pick-up location <span class="text-danger">*</span></label>
                                <select id="pickup_location" class="form-select" data-title="Select">
                                    <option value="">Select</option>
                                    <option value="Garage">Garage</option>
                                    <option value="Near Bus Stop">Near Bus Stop</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Drop-off date & time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="dropoff_datetime" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Drop-off location <span class="text-danger">*</span></label>
                                <select id="dropoff_location" class="form-select" data-title="Select">
                                    <option value="">Select</option>
                                    <option value="Garage">Garage</option>
                                    <option value="Near Bus Stop">Near Bus Stop</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button class="btn btn-outline-secondary btn-pill save-draft-btn">Save Draft</button>
                            <button class="btn btn-primary btn-pill" id="step1Next">Proceed</button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Step 2: Vehicles -->
            <section id="step-2" data-step="2" class="flow-step d-none">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>Sort by: <a href="#" class="nxz-link" id="sortPrice">low to high</a></div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted">Filters:</span>
                        <select id="filterTransmission" class="form-select" data-title="Transmission Type">
                            <option value="">Transmission Type</option>
                            <option value="manual">Manual</option>
                            <option value="automatic">Automatic</option>
                            <option value="hybrid">Hybrid</option>
                            <option value="gear">Gear</option>
                            <option value="gearless">Gearless</option>
                        </select>
                        <select id="filterSeats" class="form-select" data-title="Seats">
                            <option value="">Seats</option>
                            @for($i = 1; $i <= 50; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        <select id="filterFuel" class="form-select" data-title="Fuel Type">
                            <option value="">Fuel Type</option>
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="cng">CNG</option>
                            <option value="electric">Electric</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                </div>
                <div id="vehicleList" class="vstack gap-3"></div>
                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-outline-secondary btn-pill" data-prev>Back</button>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-pill save-draft-btn">Save Draft</button>
                        <button class="btn btn-primary btn-pill" data-next disabled>Book Now</button>
                    </div>
                </div>
            </section>

            <!-- Step 3: Customer -->
            <section id="step-3" data-step="3" class="flow-step d-none">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Select Customer <span class="text-danger">*</span></label>
                            <div class="customer-dropdown-container position-relative">
                                <input type="text" 
                                       id="booking_customer_select" 
                                       class="form-control" 
                                       placeholder="Search customer by name, phone, or email..."
                                       autocomplete="off" />
                                <div class="customer-dropdown-options position-absolute w-100 bg-white border rounded shadow-sm d-none" style="z-index: 1000; max-height: 300px; overflow-y: auto;">
                                    <div class="p-2 border-bottom">
                                        <input type="text" 
                                               class="form-control form-control-sm customer-search-filter" 
                                               placeholder="Type to search..." 
                                               autocomplete="off" />
                                    </div>
                                    <div class="customer-options-list"></div>
                                </div>
                            </div>
                            <a href="#" class="d-inline-block mt-2 nxz-link" id="createNewCustomer">+ Create a New Customer</a>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <button class="btn btn-outline-secondary btn-pill" data-prev>Back</button>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary btn-pill save-draft-btn">Save Draft</button>
                                <button class="btn btn-primary btn-pill" data-next id="step3Next" disabled>Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Step 4: Billing Info -->
            <section id="step-4" data-step="4" class="flow-step d-none">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3">Vehicle Rental Charges</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Vehicle Rent for 24 hrs <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" id="rent_24h" class="form-control" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kilometer Limit per Booking</label>
                                <input type="number" id="km_limit" class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Extra Price per Hour</label>
                                <input type="number" step="0.01" id="extra_per_hour" class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Extra Price per Kilometre</label>
                                <input type="number" step="0.01" id="extra_per_km" class="form-control" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Additional Charges</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary btn-pill" id="addAdditionalCharge">
                                    + Add Additional Charges
                                </button>
                            </div>
                            <div id="additionalChargesList" class="vstack gap-2">
                                <!-- Additional charges rows will be added here -->
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Discount</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" id="discount_amount" class="form-control" placeholder="Amount" />
                                    <select id="discount_type" class="form-select" style="max-width: 120px;">
                                        <option value="amount">Amount</option>
                                        <option value="percentage">Percentage</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Discount Value</label>
                                <input type="text" id="booking_discount_display" class="form-control" readonly placeholder="0.00" />
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="mb-3">Advance Payment</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Advance Payment Amount</label>
                                <input type="number" step="0.01" id="advance_payment" class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Method</label>
                                <select id="payment_method" class="form-select" data-title="Select">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="upi">UPI</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button class="btn btn-outline-secondary btn-pill" data-prev>Back</button>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary btn-pill" id="saveDraftBtn">Save as Draft</button>
                                <button class="btn btn-primary btn-pill" data-next id="step4Next">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Step 5: Confirm -->
            <section id="step-5" data-step="5" class="flow-step d-none">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3">Preview Booking Details</h6>
                        <div id="confirmPreview"></div>
                        <div class="d-flex justify-content-between mt-3">
                            <button class="btn btn-outline-secondary btn-pill" data-prev>Back</button>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary btn-pill save-draft-btn">Save Draft</button>
                                <button class="btn btn-primary btn-pill" id="confirmBookingBtn">Confirm Booking</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
        <div class="col-lg-4">
            @include('business.bookings.flow.partials.summary')
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.booking-flow .flow-step.d-none{display:none!important}
.booking-flow .progress-steps{display:flex;gap:24px;align-items:center;margin-bottom:10px}
.booking-flow .progress-steps .step{display:flex;align-items:center;gap:8px;color:#6c757d}
.booking-flow .progress-steps .step.active{color:#6B6ADE;font-weight:600}
.booking-flow .progress-steps .dot{width:24px;height:24px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:#e9e9ff;color:#6B6ADE;font-weight:700}
</style>
@endpush

@push('scripts')
<script>
window.bookingFlow = {
    vehiclesUrl: '{{ route("business.bookings.flow.vehicles") }}',
    saveStepUrl: '{{ route("business.bookings.flow.save_step") }}',
    clearDraftUrl: '{{ route("business.bookings.flow.clear_draft") }}',
    billingSummaryUrl: '{{ route("business.bookings.flow.billing_summary") }}',
    vehicleBillingUrl: '{{ route("business.bookings.flow.vehicle.billing", ["vehicleId" => ":vehicleId"]) }}',
    storeUrl: '{{ route("business.bookings.flow.store") }}',
    customersSearchUrl: '{{ route("business.api.customers.search") }}',
    quickCustomerUrl: '{{ route("business.customers.quick-create") }}',
    successRedirect: '{{ route("business.bookings.index") }}',
    draft: {
        exists: {{ !empty($draft) && (isset($draft['step_1']) || isset($draft['step_2']) || isset($draft['step_3']) || isset($draft['step_4']) || isset($draft['step_5'])) ? 'true' : 'false' }},
        step: {{ isset($draftStep) && $draftStep ? (int)$draftStep : 1 }},
        data: @json($draft ?? [])
    }
};
</script>
<script src="{{ asset('dist/js/BookingFlow/flow.js') }}" defer></script>
@endpush

<!-- Quick Customer Create Modal -->
<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quickCustomerForm">
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="quick_cust_name" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" id="quick_cust_mobile" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" id="quick_cust_email" class="form-control" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Customer Type <span class="text-danger">*</span></label>
                        <select id="quick_cust_type" class="form-select" required>
                            <option value="individual">Individual</option>
                            <option value="corporate">Corporate</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-pill" id="saveQuickCustomer">Create Customer</button>
            </div>
        </div>
    </div>
</div>

<!-- Resume Draft Modal -->
<div class="modal fade" id="resumeDraftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Continue your draft?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>You have an auto-saved booking draft. Would you like to continue where you left off or start a new booking?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="newBookingBtn">Create New Booking</button>
                <button type="button" class="btn btn-primary" id="resumeDraftBtn">Continue Draft</button>
            </div>
        </div>
    </div>
</div>
