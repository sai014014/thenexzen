@extends('business.layouts.app')

@section('title', 'Vendor Details - ' . $vendor->vendor_name)
@section('page-title', 'Vendor Details')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Section -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-truck me-2"></i>{{ $vendor->vendor_name }}
                        </h5>
                        <small class="text-muted">Vendor ID: #{{ str_pad($vendor->id, 6, '0', STR_PAD_LEFT) }}</small>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="badge bg-{{ $vendor->vendor_type === 'vehicle_provider' ? 'primary' : ($vendor->vendor_type === 'service_partner' ? 'success' : 'secondary') }} fs-6">
                            {{ $vendor->vendor_type_label }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Vendor Information -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-building me-2"></i>Vendor Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><strong>Vendor Name:</strong></div>
                            <div class="col-sm-8">{{ $vendor->vendor_name }}</div>
                            
                            <div class="col-sm-4"><strong>Type:</strong></div>
                            <div class="col-sm-8">{{ $vendor->vendor_type_label }}</div>
                            
                            <div class="col-sm-4"><strong>Contact Person:</strong></div>
                            <div class="col-sm-8">{{ $vendor->primary_contact_person }}</div>
                            
                            <div class="col-sm-4"><strong>Mobile Number:</strong></div>
                            <div class="col-sm-8">
                                <i class="fas fa-phone me-1"></i>{{ $vendor->mobile_number }}
                                @if($vendor->alternate_contact_number)
                                    <br><small class="text-muted">{{ $vendor->alternate_contact_number }}</small>
                                @endif
                            </div>
                            
                            <div class="col-sm-4"><strong>Email Address:</strong></div>
                            <div class="col-sm-8">
                                <i class="fas fa-envelope me-1"></i>{{ $vendor->email_address }}
                            </div>
                            
                            @if($vendor->gstin)
                            <div class="col-sm-4"><strong>GSTIN:</strong></div>
                            <div class="col-sm-8">{{ $vendor->masked_gstin }}</div>
                            @endif
                            
                            <div class="col-sm-4"><strong>PAN Number:</strong></div>
                            <div class="col-sm-8">{{ $vendor->masked_pan_number }}</div>
                            
                            <div class="col-sm-4"><strong>Registration Date:</strong></div>
                            <div class="col-sm-8">{{ $vendor->created_at->format('M d, Y') }}</div>
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
                            <div class="col-sm-4"><strong>Office Address:</strong></div>
                            <div class="col-sm-8">{{ $vendor->office_address }}</div>
                            
                            @if($vendor->additional_branches && count($vendor->additional_branches) > 0)
                            <div class="col-sm-4"><strong>Additional Branches:</strong></div>
                            <div class="col-sm-8">
                                @foreach($vendor->additional_branches as $index => $branch)
                                    <div class="mb-2">
                                        <strong>Branch {{ $index + 1 }}:</strong><br>
                                        {{ $branch }}
                                    </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payout Settings -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Payout Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><strong>Payout Method:</strong></div>
                            <div class="col-sm-8">{{ $vendor->payout_method_label }}</div>
                            
                            @if($vendor->hasBankDetails())
                            <div class="col-sm-4"><strong>Bank Details:</strong></div>
                            <div class="col-sm-8">
                                <strong>Bank:</strong> {{ $vendor->bank_name }}<br>
                                <strong>Account Holder:</strong> {{ $vendor->account_holder_name }}<br>
                                <strong>Account Number:</strong> {{ $vendor->masked_account_number }}<br>
                                <strong>IFSC Code:</strong> {{ $vendor->ifsc_code }}<br>
                                @if($vendor->bank_branch_name)
                                    <strong>Branch:</strong> {{ $vendor->bank_branch_name }}
                                @endif
                            </div>
                            @endif
                            
                            @if($vendor->hasUpiDetails())
                            <div class="col-sm-4"><strong>UPI ID:</strong></div>
                            <div class="col-sm-8">{{ $vendor->upi_id }}</div>
                            @endif
                            
                            <div class="col-sm-4"><strong>Payout Schedule:</strong></div>
                            <div class="col-sm-8">{{ $vendor->payout_schedule }}</div>
                            
                            @if($vendor->payout_terms)
                            <div class="col-sm-4"><strong>Payout Terms:</strong></div>
                            <div class="col-sm-8">{{ $vendor->payout_terms }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commission Settings -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-percentage me-2"></i>Commission Settings
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4"><strong>Commission Type:</strong></div>
                            <div class="col-sm-8">{{ $vendor->commission_type_label }}</div>
                            
                            <div class="col-sm-4"><strong>Commission Rate:</strong></div>
                            <div class="col-sm-8">{{ $vendor->formatted_commission_rate }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uploaded Documents -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-file-upload me-2"></i>Uploaded Documents
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($vendor->vendor_agreement_path)
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <div>
                                        <strong>Vendor Agreement</strong><br>
                                        <a href="{{ route('business.vendors.download-document', [$vendor, 'vendor_agreement']) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($vendor->gstin_certificate_path)
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <div>
                                        <strong>GSTIN Certificate</strong><br>
                                        <a href="{{ route('business.vendors.download-document', [$vendor, 'gstin_certificate']) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($vendor->pan_card_path)
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <div>
                                        <strong>PAN Card</strong><br>
                                        <a href="{{ route('business.vendors.download-document', [$vendor, 'pan_card']) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($vendor->additional_certificates && count($vendor->additional_certificates) > 0)
                                @foreach($vendor->additional_certificates as $index => $certificate)
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                        <div>
                                            <strong>Certificate {{ $index + 1 }}</strong><br>
                                            <a href="{{ Storage::disk('public')->url($certificate) }}" class="btn btn-sm btn-outline-primary" download>
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                            
                            @if(!$vendor->vendor_agreement_path && !$vendor->gstin_certificate_path && !$vendor->pan_card_path && (!$vendor->additional_certificates || count($vendor->additional_certificates) == 0))
                            <div class="col-12 text-center text-muted">
                                <i class="fas fa-file-upload fa-3x mb-3"></i>
                                <p>No documents uploaded</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('business.vendors.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Vendor List
                            </a>
                            <div>
                                <a href="{{ route('business.vendors.edit', $vendor) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Edit Vendor
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
