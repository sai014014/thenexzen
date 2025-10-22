@extends('business.layouts.app')

@section('title', 'Customer Details - ' . $customer->display_name)
@section('page-title', 'Customer Details')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Section -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>{{ $customer->display_name }}
                        </h5>
                        <small class="text-muted">Customer ID: #{{ str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</small>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="badge bg-{{ $customer->customer_type === 'individual' ? 'info' : 'warning' }} fs-6">
                            @if($customer->customer_type === 'individual')
                                <i class="fas fa-user me-1"></i>Individual
                            @else
                                <i class="fas fa-building me-1"></i>Corporate
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Basic Information -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Basic Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><strong>Name/Company:</strong></div>
                            <div class="col-sm-8">{{ $customer->display_name }}</div>
                            
                            @if($customer->customer_type === 'corporate' && $customer->contact_person_name)
                            <div class="col-sm-4"><strong>Contact Person:</strong></div>
                            <div class="col-sm-8">{{ $customer->contact_person_name }}</div>
                            @endif
                            
                            <div class="col-sm-4"><strong>Primary Contact:</strong></div>
                            <div class="col-sm-8">
                                <i class="fas fa-phone me-1"></i>{{ $customer->primary_contact }}
                            </div>
                            
                            @if($customer->primary_email)
                            <div class="col-sm-4"><strong>Email:</strong></div>
                            <div class="col-sm-8">
                                <i class="fas fa-envelope me-1"></i>{{ $customer->primary_email }}
                            </div>
                            @endif
                            
                            @if($customer->customer_type === 'individual' && $customer->date_of_birth)
                            <div class="col-sm-4"><strong>Date of Birth:</strong></div>
                            <div class="col-sm-8">{{ $customer->date_of_birth->format('M d, Y') }}</div>
                            @endif
                            
                            <div class="col-sm-4"><strong>Registration Date:</strong></div>
                            <div class="col-sm-8">{{ $customer->created_at->format('M d, Y') }}</div>
                            
                            <div class="col-sm-4"><strong>Status:</strong></div>
                            <div class="col-sm-8">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Management -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-cog me-2"></i>Status Management
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><strong>Current Status:</strong></div>
                            <div class="col-sm-8">
                                @switch($customer->status)
                                    @case('active')
                                        <span class="badge bg-success fs-6">Active</span>
                                        @break
                                    @case('inactive')
                                        <span class="badge bg-danger fs-6">Inactive</span>
                                        @break
                                    @case('pending')
                                        <span class="badge bg-warning fs-6">Pending</span>
                                        @break
                                @endswitch
                            </div>
                            
                            <div class="col-12 mt-3">
                                <form method="POST" action="{{ route('business.customers.update-status', $customer) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <div class="btn-group" role="group">
                                        <button type="submit" name="status" value="active" 
                                                class="btn btn-sm {{ $customer->status === 'active' ? 'btn-success' : 'btn-outline-success' }}"
                                                {{ $customer->status === 'active' ? 'disabled' : '' }}>
                                            <i class="fas fa-check me-1"></i>Activate
                                        </button>
                                        <button type="submit" name="status" value="inactive" 
                                                class="btn btn-sm {{ $customer->status === 'inactive' ? 'btn-danger' : 'btn-outline-danger' }}"
                                                {{ $customer->status === 'inactive' ? 'disabled' : '' }}>
                                            <i class="fas fa-times me-1"></i>Deactivate
                                        </button>
                                        <button type="submit" name="status" value="pending" 
                                                class="btn btn-sm {{ $customer->status === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}"
                                                {{ $customer->status === 'pending' ? 'disabled' : '' }}>
                                            <i class="fas fa-clock me-1"></i>Set Pending
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="col-12 mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Status Guide:</strong><br>
                                    • <strong>Active:</strong> Customer can make bookings and use services<br>
                                    • <strong>Inactive:</strong> Customer account is disabled<br>
                                    • <strong>Pending:</strong> Customer registration is incomplete or under review
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>Address Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><strong>Permanent Address:</strong></div>
                            <div class="col-sm-8">{{ $customer->permanent_address }}</div>
                            
                            @if($customer->current_address)
                            <div class="col-sm-4"><strong>Current Address:</strong></div>
                            <div class="col-sm-8">
                                {{ $customer->current_address }}
                                @if($customer->same_as_permanent)
                                    <br><small class="text-muted"><i class="fas fa-info-circle me-1"></i>Same as Permanent Address</small>
                                @endif
                            </div>
                            @endif
                            
                            @if($customer->customer_type === 'corporate' && $customer->company_address)
                            <div class="col-sm-4"><strong>Company Address:</strong></div>
                            <div class="col-sm-8">{{ $customer->company_address }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Identity Information -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-id-card me-2"></i>Identity Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($customer->customer_type === 'individual')
                                @if($customer->government_id_type)
                                <div class="col-sm-4"><strong>Government ID:</strong></div>
                                <div class="col-sm-8">
                                    {{ ucwords(str_replace('_', ' ', $customer->government_id_type)) }}: {{ $customer->masked_government_id }}
                                </div>
                                @endif
                                
                                @if($customer->driving_license_number)
                                <div class="col-sm-4"><strong>Driving License:</strong></div>
                                <div class="col-sm-8">
                                    {{ $customer->driving_license_number }}
                                    @if($customer->license_expiry_date)
                                        <br><small class="text-muted">Expires: {{ $customer->license_expiry_date->format('M d, Y') }}</small>
                                        <span class="badge bg-{{ $customer->license_status === 'valid' ? 'success' : ($customer->license_status === 'near_expiry' ? 'warning' : 'danger') }}">
                                            {{ ucwords(str_replace('_', ' ', $customer->license_status)) }}
                                        </span>
                                    @endif
                                </div>
                                @endif
                            @else
                                @if($customer->gstin)
                                <div class="col-sm-4"><strong>GSTIN:</strong></div>
                                <div class="col-sm-8">{{ $customer->masked_gstin }}</div>
                                @endif
                                
                                @if($customer->pan_number)
                                <div class="col-sm-4"><strong>PAN Number:</strong></div>
                                <div class="col-sm-8">{{ $customer->masked_pan }}</div>
                                @endif
                                
                                @if($customer->company_type)
                                <div class="col-sm-4"><strong>Company Type:</strong></div>
                                <div class="col-sm-8">{{ ucwords(str_replace('_', ' ', $customer->company_type)) }}</div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Contact Information -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-phone me-2"></i>Additional Contact Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($customer->emergency_contact_name)
                            <div class="col-sm-4"><strong>Emergency Contact:</strong></div>
                            <div class="col-sm-8">
                                {{ $customer->emergency_contact_name }}
                                @if($customer->emergency_contact_number)
                                    <br><small class="text-muted">{{ $customer->emergency_contact_number }}</small>
                                @endif
                            </div>
                            @endif
                            
                            @if($customer->customer_type === 'corporate')
                                @if($customer->designation)
                                <div class="col-sm-4"><strong>Designation:</strong></div>
                                <div class="col-sm-8">{{ $customer->designation }}</div>
                                @endif
                                
                                @if($customer->preferred_payment_method)
                                <div class="col-sm-4"><strong>Payment Method:</strong></div>
                                <div class="col-sm-8">{{ ucwords(str_replace('_', ' ', $customer->preferred_payment_method)) }}</div>
                                @endif
                                
                                @if($customer->invoice_frequency)
                                <div class="col-sm-4"><strong>Invoice Frequency:</strong></div>
                                <div class="col-sm-8">{{ ucwords($customer->invoice_frequency) }}</div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Corporate Drivers Section -->
        @if($customer->customer_type === 'corporate' && $customer->corporateDrivers->count() > 0)
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-car me-2"></i>Authorized Drivers
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
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
        </div>
        @endif

        <!-- Uploaded Documents -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>Uploaded Documents
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($customer->driving_license_path)
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center p-3 border rounded">
                                    <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Driving License</h6>
                                        <small class="text-muted">PDF Document</small>
                                    </div>
                                    <a href="{{ route('business.customers.download-document', ['customer' => $customer, 'type' => 'driving_license']) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>
                            @endif
                            
                            @if(!$customer->driving_license_path)
                            <div class="col-12">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-file-alt fa-3x mb-3"></i>
                                    <p>No documents uploaded</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        @if($customer->additional_information)
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Additional Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $customer->additional_information }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Quick Actions Card -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2 d-md-flex">
                    <a href="{{ route('business.customers.edit', $customer) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Customer
                    </a>
                    <a href="{{ route('business.customers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Customers
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $customer->id }})">
                        <i class="fas fa-trash me-2"></i>Delete Customer
                    </button>
                </div>
            </div>
        </div>
    </div>
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
@endsection

@push('scripts')
<script>
function confirmDelete(customerId) {
    const form = document.getElementById('deleteForm');
    form.action = `/business/customers/${customerId}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
