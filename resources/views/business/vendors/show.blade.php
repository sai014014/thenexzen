@extends('business.layouts.app')

@section('title', 'Vendor Details - ' . $vendor->vendor_name)
@section('page-title', 'Vendor Details')

@section('content')
<div class="container-fluid">
    <!-- Vendor Header Section -->
    <div class="vendor-header-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center">
                <div class="status-dot bg-success me-3"></div>
                <div>
                    <span class="text-muted small">Vendor Partner</span>
                    <h2 class="mb-0 fw-bold">{{ $vendor->vendor_name }}</h2>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-4 flex-wrap mt-2 mt-md-0">
                <div class="text-end">
                    <div class="small text-muted">Vendor Type: {{ $vendor->vendor_type_label }}</div>
                    @if($vendor->gstin || $vendor->pan_number)
                        <div class="small text-muted">GSTIN/PAN: {{ $vendor->gstin ?: $vendor->masked_pan_number }}</div>
                    @endif
                    <div class="small text-muted">Primary Contact Person: {{ $vendor->primary_contact_person }}</div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <a href="{{ route('business.vendors.edit', $vendor) }}" class="btn btn-link text-primary text-decoration-none px-2">
                        Edit
                    </a>
                    <form id="delete-vendor-form" action="{{ route('business.vendors.destroy', $vendor) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-link text-danger text-decoration-none px-2" onclick="confirmDelete()">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-6">
            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-address-book me-2"></i>Contact Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><small class="text-muted">Mobile Number:</small></div>
                        <div class="col-sm-8"><strong>+91 {{ $vendor->mobile_number }}</strong></div>
                    </div>
                    @if($vendor->alternate_contact_number)
                    <div class="row mb-3">
                        <div class="col-sm-4"><small class="text-muted">Alternate Mobile Number:</small></div>
                        <div class="col-sm-8"><strong>+91 {{ $vendor->alternate_contact_number }}</strong></div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-sm-4"><small class="text-muted">Email Address:</small></div>
                        <div class="col-sm-8">
                            <strong>{{ $vendor->email_address }}</strong>
                            <button class="btn btn-sm btn-link text-muted p-0 ms-2" onclick="copyToClipboard('{{ $vendor->email_address }}')" title="Copy email">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><small class="text-muted">Office Address:</small></div>
                        <div class="col-sm-8"><strong>{{ $vendor->office_address }}</strong></div>
                    </div>
                    @if($vendor->additional_branches && count($vendor->additional_branches) > 0)
                    <div class="row">
                        <div class="col-sm-4"><small class="text-muted">Branches:</small></div>
                        <div class="col-sm-8">
                            <ol class="mb-0 ps-3">
                                @foreach($vendor->additional_branches as $branch)
                                    <li><strong>{{ $branch }}</strong></li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-file-alt me-2"></i>Documents
                    </h5>
                    <button class="btn btn-sm btn-link text-primary p-0" title="Download all">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($vendor->vendor_agreement_path)
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="document-card text-center p-3 border rounded">
                                <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                <div class="small text-truncate" title="Vendor Agreement">Vendor Agreeme...</div>
                                <a href="{{ route('business.vendors.download-document', [$vendor, 'vendor_agreement']) }}" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if($vendor->pan_card_path)
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="document-card text-center p-3 border rounded">
                                @php
                                    $panExtension = pathinfo($vendor->pan_card_path, PATHINFO_EXTENSION);
                                    $isPdf = strtolower($panExtension) === 'pdf';
                                @endphp
                                <i class="fas fa-file-{{ $isPdf ? 'pdf' : 'image' }} fa-3x text-{{ $isPdf ? 'danger' : 'success' }} mb-2"></i>
                                <div class="small">PAN Card</div>
                                <a href="{{ route('business.vendors.download-document', [$vendor, 'pan_card']) }}" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if($vendor->gstin_certificate_path)
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="document-card text-center p-3 border rounded">
                                @php
                                    $gstExtension = pathinfo($vendor->gstin_certificate_path, PATHINFO_EXTENSION);
                                    $isPdf = strtolower($gstExtension) === 'pdf';
                                @endphp
                                <i class="fas fa-file-{{ $isPdf ? 'pdf' : 'image' }} fa-3x text-{{ $isPdf ? 'danger' : 'success' }} mb-2"></i>
                                <div class="small">GSTIN Certificate</div>
                                <a href="{{ route('business.vendors.download-document', [$vendor, 'gstin_certificate']) }}" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if($vendor->additional_certificates && count($vendor->additional_certificates) > 0)
                            @foreach($vendor->additional_certificates as $index => $certificate)
                            <div class="col-md-4 col-sm-6 mb-3">
                                <div class="document-card text-center p-3 border rounded">
                                    @php
                                        $certExtension = pathinfo($certificate, PATHINFO_EXTENSION);
                                        $isPdf = strtolower($certExtension) === 'pdf';
                                    @endphp
                                    <i class="fas fa-file-{{ $isPdf ? 'pdf' : 'image' }} fa-3x text-{{ $isPdf ? 'danger' : 'success' }} mb-2"></i>
                                    <div class="small text-truncate">Certificate {{ $index + 1 }}</div>
                                    <a href="{{ Storage::disk('public')->url($certificate) }}" class="btn btn-sm btn-outline-primary mt-2" download>
                                        <i class="fas fa-download me-1"></i>Download
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
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-money-bill-wave me-2"></i>Vendor Payout Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-5"><small class="text-muted">Payout Method:</small></div>
                        <div class="col-sm-7"><strong>{{ $vendor->payout_method_label }}</strong></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5"><small class="text-muted">Payout Frequency:</small></div>
                        <div class="col-sm-7"><strong>{{ $vendor->payout_schedule }}</strong></div>
                    </div>
                    @if($vendor->payout_terms)
                    <div class="row">
                        <div class="col-sm-5"><small class="text-muted">Payout Terms:</small></div>
                        <div class="col-sm-7"><strong>{{ $vendor->payout_terms }}</strong></div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Vendor Bank Details -->
            @if($vendor->hasBankDetails())
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-university me-2"></i>Vendor Bank Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-5"><small class="text-muted">Account Holder Name:</small></div>
                        <div class="col-sm-7"><strong>{{ $vendor->account_holder_name }}</strong></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5"><small class="text-muted">Bank Name:</small></div>
                        <div class="col-sm-7"><strong>{{ $vendor->bank_name }}</strong></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5"><small class="text-muted">IFSC Code:</small></div>
                        <div class="col-sm-7"><strong>{{ $vendor->ifsc_code }}</strong></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5"><small class="text-muted">Account Number:</small></div>
                        <div class="col-sm-7"><strong>{{ $vendor->masked_account_number }}</strong></div>
                    </div>
                    @if($vendor->bank_branch_name)
                    <div class="row">
                        <div class="col-sm-5"><small class="text-muted">Bank Branch Name:</small></div>
                        <div class="col-sm-7"><strong>{{ $vendor->bank_branch_name }}</strong></div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Commission Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 text-primary">
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
                    <div class="row mb-3">
                        <div class="col-sm-5"><small class="text-muted">Commission Type:</small></div>
                        <div class="col-sm-7"><strong>{{ $vendorCommissionLabel }}</strong></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5"><small class="text-muted">Commission Rate:</small></div>
                        <div class="col-sm-7"><strong>{{ $vendorRateDisplay }}</strong></div>
                    </div>
                    @if($vendor->commission_type === 'lease_to_rent' && $vendor->lease_commitment_months)
                    <div class="row">
                        <div class="col-sm-5"><small class="text-muted">Lease Commitment:</small></div>
                        <div class="col-sm-7"><strong>{{ $vendor->lease_commitment_months }} months</strong></div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- List of Vehicles -->
    @if($vehicles && $vehicles->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-car me-2"></i>List of Vehicles
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
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
</script>
@endpush
@endsection