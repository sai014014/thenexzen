@extends('business.layouts.app')

@section('title', 'Vendor Details - ' . $vendor->vendor_name)
@section('page-title', 'Vendor Details')

@section('content')
<div class="container-fluid">
    <!-- Vendor Header Section -->
    <div class="vendor-header-card white-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-4">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <div class="d-flex align-items-center">
                    <div class="status-dot bg-success me-3"></div>
                    <div>
                        <span class="text-muted small">Vendor Partner</span>
                        <h2 class="mb-0">{{ $vendor->vendor_name }}</h2>
                    </div>
                </div>
                
                <div class="vendor-details-group-horizontal">
                    <div class="vendor-detail-item">
                        <div class="vendor-detail-label">Vendor Type</div>
                        <div class="vendor-detail-value">{{ $vendor->vendor_type_label }}</div>
                    </div>
                    @if($vendor->gstin || $vendor->pan_number)
                    <div class="vendor-detail-item">
                        <div class="vendor-detail-label">GSTIN/PAN</div>
                        <div class="vendor-detail-value">{{ $vendor->gstin ?: $vendor->masked_pan_number }}</div>
                    </div>
                    @endif
                    <div class="vendor-detail-item">
                        <div class="vendor-detail-label">Primary Contact Person</div>
                        <div class="vendor-detail-value">{{ $vendor->primary_contact_person }}</div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-3 align-items-center">
                <span class="badge bg-{{ $vendor->status === 'active' ? 'success' : ($vendor->status === 'inactive' ? 'danger' : 'warning') }} fs-6">
                    {{ ucfirst($vendor->status) }}
                </span>
                <a href="{{ route('business.vendors.edit', $vendor) }}" class="btn btn-link text-decoration-none">
                    Edit
                </a>
                <form id="delete-vendor-form" action="{{ route('business.vendors.destroy', $vendor) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-link text-danger text-decoration-none " onclick="confirmDelete()">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-6">
            <!-- Contact Information -->
            <div class="card mb-4 vendor-details-dark-container">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-address-book me-2"></i>Contact Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mobile-numbers-row">
                        <div>
                            <small class="text-muted">Mobile Number:</small>
                            <strong>+91 {{ $vendor->mobile_number }}</strong>
                        </div>
                        @if($vendor->alternate_contact_number)
                        <div>
                            <small class="text-muted">Alternate Mobile Number:</small>
                            <strong>+91 {{ $vendor->alternate_contact_number }}</strong>
                        </div>
                        @endif
                    </div>
                    <div class="single-field">
                        <small class="text-muted">Email Address:</small>
                        <div class="email-field">
                            <strong>{{ $vendor->email_address }}</strong>
                            <button class="btn btn-sm btn-link p-0" onclick="copyToClipboard('{{ $vendor->email_address }}')" title="Copy email">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="single-field">
                        <small class="text-muted">Office Address:</small>
                        <strong>{{ $vendor->office_address }}</strong>
                    </div>
                    @if($vendor->additional_branches && count($vendor->additional_branches) > 0)
                    <div class="single-field branches-section">
                        <small class="text-muted">Branches:</small>
                        <ol class="mb-0">
                            @foreach($vendor->additional_branches as $branch)
                                <li><strong>{{ $branch }}</strong></li>
                            @endforeach
                        </ol>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            <div class="card mb-4 vendor-details-dark-container">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Documents
                    </h5>
                    <button class="btn btn-sm btn-link p-0" title="Download all" style="color: #2563eb;">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <div class="card-body">
                    @php
                        $totalDocuments = 0;
                        if($vendor->vendor_agreement_path) $totalDocuments++;
                        if($vendor->pan_card_path) $totalDocuments++;
                        if($vendor->gstin_certificate_path) $totalDocuments++;
                        if($vendor->additional_certificates) $totalDocuments += count($vendor->additional_certificates);
                        // Use col-md-3 for 4 per row, or col-md-4 for 3 per row if more than 4
                        $colClass = ($totalDocuments <= 4) ? 'col-md-3 col-sm-6' : 'col-md-4 col-sm-6';
                    @endphp
                    <div class="row">
                        @if($vendor->vendor_agreement_path)
                        <div class="{{ $colClass }} mb-3">
                            <div class="document-card text-center p-3 border rounded">
                                <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                <div class="small text-truncate" title="Vendor Agreement">Vendor Agreeme...</div>
                                <a href="{{ route('business.vendors.view-document', [$vendor, 'vendor_agreement']) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if($vendor->pan_card_path)
                        <div class="{{ $colClass }} mb-3">
                            <div class="document-card text-center p-3 border rounded">
                                @php
                                    $panExtension = pathinfo($vendor->pan_card_path, PATHINFO_EXTENSION);
                                    $isPdf = strtolower($panExtension) === 'pdf';
                                @endphp
                                <i class="fas fa-file-{{ $isPdf ? 'pdf' : 'image' }} fa-3x text-{{ $isPdf ? 'danger' : 'success' }} mb-2"></i>
                                <div class="small">PAN Card</div>
                                <a href="{{ route('business.vendors.view-document', [$vendor, 'pan_card']) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if($vendor->gstin_certificate_path)
                        <div class="{{ $colClass }} mb-3">
                            <div class="document-card text-center p-3 border rounded">
                                @php
                                    $gstExtension = pathinfo($vendor->gstin_certificate_path, PATHINFO_EXTENSION);
                                    $isPdf = strtolower($gstExtension) === 'pdf';
                                @endphp
                                <i class="fas fa-file-{{ $isPdf ? 'pdf' : 'image' }} fa-3x text-{{ $isPdf ? 'danger' : 'success' }} mb-2"></i>
                                <div class="small">GSTIN Certificate</div>
                                <a href="{{ route('business.vendors.view-document', [$vendor, 'gstin_certificate']) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if($vendor->additional_certificates && count($vendor->additional_certificates) > 0)
                            @foreach($vendor->additional_certificates as $index => $certificate)
                            <div class="{{ $colClass }} mb-3">
                                <div class="document-card text-center p-3 border rounded">
                                    @php
                                        $certExtension = pathinfo($certificate, PATHINFO_EXTENSION);
                                        $isPdf = strtolower($certExtension) === 'pdf';
                                    @endphp
                                    <i class="fas fa-file-{{ $isPdf ? 'pdf' : 'image' }} fa-3x text-{{ $isPdf ? 'danger' : 'success' }} mb-2"></i>
                                    <div class="small text-truncate">Certificate {{ $index + 1 }}</div>
                                    <a href="{{ Storage::disk('public')->url($certificate) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        @endif
                        
                        @if(!$vendor->vendor_agreement_path && !$vendor->gstin_certificate_path && !$vendor->pan_card_path && (!$vendor->additional_certificates || count($vendor->additional_certificates) == 0))
                        <div class="col-12 text-center text-muted py-4">
                            <i class="fas fa-file-upload fa-3x mb-3"></i>
                            <p class="mb-0">No documents uploaded</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-6">
            <!-- Vendor Payout Details -->
            <div class="card mb-4 vendor-details-dark-container">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Vendor Payout Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="single-field">
                        <small class="text-muted">Payout Method:</small>
                        <strong>{{ $vendor->payout_method_label }}</strong>
                    </div>
                    <div class="single-field">
                        <small class="text-muted">Payout Frequency:</small>
                        <strong>{{ $vendor->payout_schedule }}</strong>
                    </div>
                    @if($vendor->payout_terms)
                    <div class="single-field">
                        <small class="text-muted">Payout Terms:</small>
                        <strong>{{ $vendor->payout_terms }}</strong>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Vendor Bank Details -->
            @if($vendor->hasBankDetails())
            <div class="card mb-4 vendor-details-dark-container">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-university me-2"></i>Vendor Bank Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="single-field">
                        <small class="text-muted">Account Holder Name:</small>
                        <strong>{{ $vendor->account_holder_name }}</strong>
                    </div>
                    <div class="single-field">
                        <small class="text-muted">Bank Name:</small>
                        <strong>{{ $vendor->bank_name }}</strong>
                    </div>
                    <div class="single-field">
                        <small class="text-muted">IFSC Code:</small>
                        <strong>{{ $vendor->ifsc_code }}</strong>
                    </div>
                    <div class="single-field">
                        <small class="text-muted">Account Number:</small>
                        <strong>{{ $vendor->masked_account_number }}</strong>
                    </div>
                    @if($vendor->bank_branch_name)
                    <div class="single-field">
                        <small class="text-muted">Bank Branch Name:</small>
                        <strong>{{ $vendor->bank_branch_name }}</strong>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Commission Details -->
            <div class="card mb-4 vendor-details-dark-container">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-percentage me-2"></i>Commission Details
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $vendorCommissionLabel = 'Fixed Amount';
                        if (in_array($vendor->commission_type, ['percentage_of_revenue','percentage'])) {
                            $vendorCommissionLabel = 'Percentage of Revenue';
                        } elseif ($vendor->commission_type === 'per_booking_per_day') {
                            $vendorCommissionLabel = 'Per Booking Per Day';
                        } elseif ($vendor->commission_type === 'lease_to_rent') {
                            $vendorCommissionLabel = 'Lease-to-Rent';
                        }
                        $vendorRateDisplay = '';
                        if (in_array($vendor->commission_type, ['percentage_of_revenue','percentage'])) {
                            $vendorRateDisplay = ($vendor->commission_rate !== null ? rtrim(rtrim(number_format((float)$vendor->commission_rate, 2, '.', ''), '0'), '.') : '0') . '%';
                        } elseif ($vendor->commission_type === 'per_booking_per_day') {
                            $vendorRateDisplay = '₹' . number_format((float)$vendor->commission_rate, 2) . ' / day';
                        } else { // fixed_amount or lease_to_rent
                            $vendorRateDisplay = '₹' . number_format((float)$vendor->commission_rate, 2);
                        }
                    @endphp
                    <div class="single-field">
                        <small class="text-muted">Commission Type:</small>
                        <strong>{{ $vendorCommissionLabel }}</strong>
                    </div>
                    <div class="single-field">
                        <small class="text-muted">Commission Rate:</small>
                        <strong>{{ $vendorRateDisplay }}</strong>
                    </div>
                    @if($vendor->commission_type === 'lease_to_rent' && $vendor->lease_commitment_months)
                    <div class="single-field">
                        <small class="text-muted">Lease Commitment:</small>
                        <strong>{{ $vendor->lease_commitment_months }} months</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly History Table -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3 text-primary">
                <i class="fas fa-calendar-alt me-2"></i>Monthly Earnings History
            </h5>
            <div class="filter-section">
                <div class="table-responsive">
                    <table id="monthlyEarningsTable" class="table table-striped table-bordered">
                        <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Total Bookings</th>
                                    <th>Business Revenue</th>
                                    <th>Vendor Earnings</th>
                                    <th>Business Profit</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monthlyEarnings ?? [] as $index => $monthData)
                                <tr class="monthly-earnings-row" @if($index >= 4) style="display: none;" @endif>
                                    <td>
                                        <strong>{{ $monthData['month'] ?? 'N/A' }}</strong>
                                        @if($monthData['month_key'] === \Carbon\Carbon::now()->format('Y-m'))
                                            <span class="badge bg-success ms-2">Current</span>
                                        @endif
                                    </td>
                                    <td>{{ $monthData['total_bookings'] ?? 0 }}</td>
                                    <td>₹{{ number_format($monthData['total_business_revenue'] ?? 0, 2) }}</td>
                                    <td>
                                        <strong class="text-success">₹{{ number_format($monthData['total_earnings'] ?? 0, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-muted">₹{{ number_format($monthData['net_business_profit'] ?? 0, 2) }}</span>
                                    </td>
                                    <td>
                                        @if(($monthData['total_bookings'] ?? 0) > 0)
                                            <button class="btn btn-sm btn-outline-primary view-month-details" 
                                                    data-month="{{ $monthData['month'] ?? '' }}"
                                                    data-bookings='@json($monthData['bookings'] ?? [])'>
                                                View Details
                                            </button>
                                        @else
                                            <span class="text-muted small">No bookings</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                                        <p class="mb-0">No earnings history available</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(!empty($monthlyEarnings) && count($monthlyEarnings) > 0)
                            <tfoot>
                                <tr>
                                    <th>Total (Last 12 Months)</th>
                                    <th>{{ collect($monthlyEarnings)->sum('total_bookings') }}</th>
                                    <th>₹{{ number_format(collect($monthlyEarnings)->sum('total_business_revenue'), 2) }}</th>
                                    <th class="text-success">₹{{ number_format(collect($monthlyEarnings)->sum('total_earnings'), 2) }}</th>
                                    <th>₹{{ number_format(collect($monthlyEarnings)->sum('net_business_profit'), 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                    @if(!empty($monthlyEarnings) && count($monthlyEarnings) > 4)
                    <div class="text-center mt-3 pb-3">
                        <button id="showMoreMonthsBtn" class="btn btn-link text-primary text-decoration-none" onclick="toggleMoreMonths()">
                            <i class="fas fa-chevron-down me-1"></i>Show More ({{ count($monthlyEarnings) - 4 }} more months)
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- List of Vehicles -->
    @if($vehicles && $vehicles->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3 text-primary">
                <i class="fas fa-car me-2"></i>List of Vehicles
            </h5>
            <div class="filter-section">
                <div class="table-responsive">
                    <table id="vehiclesTable" class="table table-striped table-bordered">
                        <thead>
                                <tr>
                                    <th>Vehicle</th>
                                    <th>Unit Type</th>
                                    <th>Fuel</th>
                                    <th>Capacity</th>
                                    <th>Commission</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vehicles as $vehicle)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="vehicle-brand-logo me-2">
                                                @php
                                                    $brandInitial = strtoupper(substr($vehicle->vehicle_make, 0, 1));
                                                    $brandColors = [
                                                        'S' => 'text-danger',
                                                        'T' => 'text-primary',
                                                        'H' => 'text-info',
                                                        'M' => 'text-danger',
                                                        'K' => 'text-danger',
                                                        'Y' => 'text-dark',
                                                        'V' => 'text-primary',
                                                        'B' => 'text-primary',
                                                        'I' => 'text-danger'
                                                    ];
                                                    $logoColor = $brandColors[$brandInitial] ?? 'text-secondary';
                                                @endphp
                                                <span class="fw-bold {{ $logoColor }}" style="font-size: 1.2rem;">{{ $brandInitial }}</span>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $vehicle->vehicle_make }} {{ $vehicle->vehicle_model }}</div>
                                                <div class="small text-muted">{{ $vehicle->vehicle_number }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ ucfirst(str_replace('_', ' ', $vehicle->vehicle_type)) }} - {{ ucfirst($vehicle->transmission_type) }}
                                    </td>
                                    <td>{{ ucfirst($vehicle->fuel_type) }}</td>
                                    <td>
                                        @if($vehicle->seating_capacity)
                                            {{ $vehicle->seating_capacity }} Seats
                                        @elseif($vehicle->engine_capacity_cc)
                                            {{ $vehicle->engine_capacity_cc }}cc
                                        @elseif($vehicle->payload_capacity_tons)
                                            {{ $vehicle->payload_capacity_tons }} Tons
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $vType = $vehicle->commission_type;
                                            // Normalize legacy values if any
                                            if ($vType === 'fixed_amount') { $vType = 'fixed'; }
                                            if ($vType === 'percentage_of_revenue') { $vType = 'percentage'; }
                                            $rate = $vehicle->commission_rate ?? $vehicle->commission_value ?? 0;
                                        @endphp
                                        @if($vType === 'percentage')
                                            Percentage - {{ rtrim(rtrim(number_format((float)$rate, 2, '.', ''), '0'), '.') }}%
                                        @elseif($vType === 'per_booking_per_day')
                                            Per Booking/Day - ₹{{ number_format((float)$rate, 2) }}
                                        @elseif($vType === 'lease_to_rent')
                                            Lease-to-Rent - ₹{{ number_format((float)$rate, 2) }} @if(!empty($vehicle->lease_commitment_months)) ({{ $vehicle->lease_commitment_months }} months) @endif
                                        @else
                                            Fixed Amount - ₹{{ number_format((float)$rate, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($vehicle->vehicle_status === 'under_maintenance')
                                            <span class="badge bg-danger">Maintenance</span>
                                        @elseif($vehicle->is_available && $vehicle->vehicle_status === 'active')
                                            <span class="badge bg-success">Available</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Booked</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('business.vehicles.show', $vehicle) }}" class="btn btn-link text-primary text-decoration-none p-0 me-2">
                                            View
                                        </a>
                                        <button class="btn btn-link text-danger text-decoration-none p-0" onclick="confirmVehicleDelete({{ $vehicle->id }})">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Month Details Modal -->
<div class="modal fade" id="monthDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="monthDetailsModalLabel">Monthly Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="monthDetailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<style>

.gap-4 {gap: 120px !important;}
</style>

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this vendor? This action cannot be undone.')) {
        document.getElementById('delete-vendor-form').submit();
    }
}

function confirmVehicleDelete(vehicleId) {
    if (confirm('Are you sure you want to delete this vehicle? This action cannot be undone.')) {
        // You can implement vehicle deletion here or redirect to delete route
        window.location.href = '{{ route("business.vehicles.index") }}?delete=' + vehicleId;
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show a temporary toast/alert
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check text-success"></i>';
        setTimeout(() => {
            btn.innerHTML = originalHTML;
        }, 2000);
    });
}

// Month details modal
document.querySelectorAll('.view-month-details').forEach(btn => {
    btn.addEventListener('click', function() {
        const month = this.getAttribute('data-month');
        const bookings = JSON.parse(this.getAttribute('data-bookings') || '[]');
        
        const modal = new bootstrap.Modal(document.getElementById('monthDetailsModal'));
        const modalLabel = document.getElementById('monthDetailsModalLabel');
        const modalContent = document.getElementById('monthDetailsContent');
        
        modalLabel.textContent = `Booking Details - ${month}`;
        
        if (bookings.length === 0) {
            modalContent.innerHTML = '<div class="text-center py-4 text-muted">No bookings for this month</div>';
        } else {
            let html = '<div class="table-responsive"><table class="table table-sm">';
            html += '<thead><tr><th>Booking ID</th><th>Vehicle</th><th>Completed Date</th><th>Business Revenue</th><th>Vendor Earning</th></tr></thead>';
            html += '<tbody>';
            
            bookings.forEach(booking => {
                html += `<tr>
                    <td>${booking.booking_id || 'N/A'}</td>
                    <td>${booking.vehicle || 'N/A'}</td>
                    <td>${booking.completed_at || 'N/A'}</td>
                    <td>₹${parseFloat(booking.business_revenue || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td><strong class="text-success">₹${parseFloat(booking.vendor_earning || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong></td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';
            
            // Add summary
            const totalRevenue = bookings.reduce((sum, b) => sum + parseFloat(b.business_revenue || 0), 0);
            const totalEarnings = bookings.reduce((sum, b) => sum + parseFloat(b.vendor_earning || 0), 0);
            html += `<div class="mt-3 p-3 bg-light rounded">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="small text-muted">Total Bookings</div>
                        <strong>${bookings.length}</strong>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Total Revenue</div>
                        <strong>₹${totalRevenue.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>
                    </div>
                    <div class="col-4">
                        <div class="small text-muted">Total Earnings</div>
                        <strong class="text-success">₹${totalEarnings.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>
                    </div>
                </div>
            </div>`;
            
            modalContent.innerHTML = html;
        }
        
        modal.show();
    });
});

// Show More/Less Months functionality
function toggleMoreMonths() {
    const rows = document.querySelectorAll('.monthly-earnings-row');
    const btn = document.getElementById('showMoreMonthsBtn');
    let allVisible = true;
    
    // Check if any rows are hidden
    rows.forEach((row, index) => {
        if (index >= 4 && row.style.display === 'none') {
            allVisible = false;
        }
    });
    
    // Toggle visibility
    rows.forEach((row, index) => {
        if (index >= 4) {
            if (allVisible) {
                row.style.display = 'none';
            } else {
                row.style.display = '';
            }
        }
    });
    
    // Update button text
    if (allVisible) {
        const hiddenCount = rows.length - 4;
        btn.innerHTML = `<i class="fas fa-chevron-down me-1"></i>Show More (${hiddenCount} more months)`;
    } else {
        btn.innerHTML = `<i class="fas fa-chevron-up me-1"></i>Show Less`;
    }
}
</script>
@endpush
@endsection