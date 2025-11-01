@extends('business.layouts.app')

@section('title', 'Customer Details - ' . $customer->display_name)
@section('page-title', 'Customer Details')

@section('content')
<div class="container-fluid">
    <!-- Customer Header Section -->
    <div class="customer-header-card white-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-4">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <div class="d-flex align-items-center">
                    <div class="status-dot bg-{{ $customer->status === 'active' ? 'success' : 'secondary' }} me-3"></div>
                    <div>
                        <span class="text-muted small">{{ $customer->customer_type === 'individual' ? 'Individual Customer' : 'Corporate Customer' }}</span>
                        <h2 class="mb-0">{{ $customer->display_name }}</h2>
                    </div>
                </div>
                
                <div class="vendor-details-group-horizontal">
                    <div class="vendor-detail-item">
                        <div class="vendor-detail-label">Customer Type</div>
                        <div class="vendor-detail-value">
                            @if($customer->customer_type === 'individual')
                                Individual
                            @else
                                Corporate
                            @endif
                        </div>
                    </div>
                    @if($customer->customer_type === 'corporate' && $customer->gstin)
                    <div class="vendor-detail-item">
                        <div class="vendor-detail-label">GSTIN</div>
                        <div class="vendor-detail-value">{{ $customer->masked_gstin }}</div>
                    </div>
                    @endif
                    @if($customer->customer_type === 'corporate' && $customer->contact_person_name)
                    <div class="vendor-detail-item">
                        <div class="vendor-detail-label">Contact Person</div>
                        <div class="vendor-detail-value">{{ $customer->contact_person_name }}</div>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="d-flex gap-3 align-items-center">
                <span class="badge bg-{{ $customer->status === 'active' ? 'success' : ($customer->status === 'inactive' ? 'danger' : 'warning') }} fs-6">
                    {{ ucfirst($customer->status) }}
                </span>
                <a href="{{ route('business.customers.edit', $customer) }}" class="btn btn-link text-decoration-none">
                    Edit
                </a>
                <button type="button" class="btn btn-link text-danger text-decoration-none" onclick="confirmDelete({{ $customer->id }})">
                    Delete
                </button>
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
                    <div class="single-field">
                        <small class="text-muted">Primary Contact:</small>
                        <strong>{{ $customer->primary_contact }}</strong>
                    </div>
                    @if($customer->primary_email)
                    <div class="single-field">
                        <small class="text-muted">Email Address:</small>
                        <div class="email-field">
                            <strong>{{ $customer->primary_email }}</strong>
                            <button class="btn btn-sm btn-link p-0" onclick="copyToClipboard('{{ $customer->primary_email }}')" title="Copy email">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                    @if($customer->emergency_contact_name)
                    <div class="single-field">
                        <small class="text-muted">Emergency Contact:</small>
                        <strong>{{ $customer->emergency_contact_name }}@if($customer->emergency_contact_number) - {{ $customer->emergency_contact_number }}@endif</strong>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Address Information -->
            <div class="card mb-4 vendor-details-dark-container">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>Address Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="single-field">
                        <small class="text-muted">Permanent Address:</small>
                        <strong>{{ $customer->permanent_address }}</strong>
                    </div>
                    @if($customer->current_address)
                    <div class="single-field">
                        <small class="text-muted">Current Address:</small>
                        <strong>
                            {{ $customer->current_address }}
                            @if($customer->same_as_permanent)
                                <span class="text-muted small">(Same as Permanent Address)</span>
                            @endif
                        </strong>
                    </div>
                    @endif
                    @if($customer->customer_type === 'corporate' && $customer->company_address)
                    <div class="single-field">
                        <small class="text-muted">Company Address:</small>
                        <strong>{{ $customer->company_address }}</strong>
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
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($customer->driving_license_path)
                        <div class="col-md-6 mb-3">
                            <div class="document-card text-center p-3 border rounded">
                                <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i>
                                <div class="small">Driving License</div>
                                <a href="{{ route('business.customers.view-document', ['customer' => $customer, 'type' => 'driving_license']) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if(!$customer->driving_license_path)
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
            <!-- Basic Information -->
            <div class="card mb-4 vendor-details-dark-container">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Basic Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="single-field">
                        <small class="text-muted">Name/Company:</small>
                        <strong>{{ $customer->display_name }}</strong>
                    </div>
                    @if($customer->customer_type === 'individual' && $customer->date_of_birth)
                    <div class="single-field">
                        <small class="text-muted">Date of Birth:</small>
                        <strong>{{ $customer->date_of_birth->format('M d, Y') }}</strong>
                    </div>
                    @endif
                    <div class="single-field">
                        <small class="text-muted">Registration Date:</small>
                        <strong>{{ $customer->created_at->format('M d, Y') }}</strong>
                    </div>
                    <div class="single-field">
                        <small class="text-muted">Status:</small>
                        <strong>
                            @switch($customer->status)
                                @case('active')
                                    <span class="badge bg-success">Active</span>
                                    @break
                                @case('inactive')
                                    <span class="badge bg-danger">Inactive</span>
                                    @break
                                @case('pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @break
                            @endswitch
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Identity Information -->
            <div class="card mb-4 vendor-details-dark-container">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2"></i>Identity Information
                    </h5>
                </div>
                <div class="card-body">
                    @if($customer->customer_type === 'individual')
                        @if($customer->government_id_type)
                        <div class="single-field">
                            <small class="text-muted">Government ID:</small>
                            <strong>{{ ucwords(str_replace('_', ' ', $customer->government_id_type)) }}: {{ $customer->masked_government_id }}</strong>
                        </div>
                        @endif
                        
                        @if($customer->driving_license_number)
                        <div class="single-field">
                            <small class="text-muted">Driving License:</small>
                            <strong>
                                {{ $customer->driving_license_number }}
                                @if($customer->license_expiry_date)
                                    <span class="text-muted small">(Expires: {{ $customer->license_expiry_date->format('M d, Y') }})</span>
                                @endif
                            </strong>
                        </div>
                        @endif
                    @else
                        @if($customer->gstin)
                        <div class="single-field">
                            <small class="text-muted">GSTIN:</small>
                            <strong>{{ $customer->masked_gstin }}</strong>
                        </div>
                        @endif
                        
                        @if($customer->pan_number)
                        <div class="single-field">
                            <small class="text-muted">PAN Number:</small>
                            <strong>{{ $customer->masked_pan }}</strong>
                        </div>
                        @endif
                        
                        @if($customer->company_type)
                        <div class="single-field">
                            <small class="text-muted">Company Type:</small>
                            <strong>{{ ucwords(str_replace('_', ' ', $customer->company_type)) }}</strong>
                        </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Additional Contact Information -->
            @if(($customer->customer_type === 'corporate' && ($customer->designation || $customer->preferred_payment_method || $customer->invoice_frequency)) || ($customer->emergency_contact_name && $customer->customer_type === 'individual'))
            <div class="card mb-4 vendor-details-dark-container">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-phone me-2"></i>Additional Information
                    </h5>
                </div>
                <div class="card-body">
                    @if($customer->customer_type === 'corporate')
                        @if($customer->designation)
                        <div class="single-field">
                            <small class="text-muted">Designation:</small>
                            <strong>{{ $customer->designation }}</strong>
                        </div>
                        @endif
                        
                        @if($customer->preferred_payment_method)
                        <div class="single-field">
                            <small class="text-muted">Payment Method:</small>
                            <strong>{{ ucwords(str_replace('_', ' ', $customer->preferred_payment_method)) }}</strong>
                        </div>
                        @endif
                        
                        @if($customer->invoice_frequency)
                        <div class="single-field">
                            <small class="text-muted">Invoice Frequency:</small>
                            <strong>{{ ucwords($customer->invoice_frequency) }}</strong>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>


    <!-- Authorized Drivers Section -->
    @if($customer->customer_type === 'corporate' && $customer->corporateDrivers->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3 text-primary">
                <i class="fas fa-car me-2"></i>Authorized Drivers
            </h5>
            <div class="filter-section">
                <div class="table-responsive">
                    <table id="driversTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Driver Name</th>
                                <th>License Number</th>
                                <th>Expiry Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->corporateDrivers as $driver)
                            <tr>
                                <td>{{ $driver->driver_name }}</td>
                                <td>{{ $driver->driving_license_number }}</td>
                                <td>{{ $driver->license_expiry_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $driver->license_status === 'valid' ? 'success' : ($driver->license_status === 'near_expiry' ? 'warning' : 'danger') }}">
                                        {{ ucwords(str_replace('_', ' ', $driver->license_status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($driver->driving_license_path)
                                    <a href="{{ route('business.customers.download-driver-document', ['customer' => $customer, 'driver' => $driver, 'type' => 'driving_license']) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Download License">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Additional Information -->
    @if($customer->additional_information)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card vendor-details-dark-container">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-sticky-note me-2"></i>Additional Information
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $customer->additional_information }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this customer? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This will also delete all associated corporate drivers and documents.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Customer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(customerId) {
    const form = document.getElementById('deleteForm');
    form.action = `/business/customers/${customerId}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
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
