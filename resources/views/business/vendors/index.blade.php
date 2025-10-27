@extends('business.layouts.app')

@section('title', 'Vendor Management')
@section('page-title', 'Vendor Management')

@section('content')
<div class="content-wrapper">
                <!-- Search, Filters and Add Vendor Section -->
                <div class="row mb-3 align-items-end filter-row">
                    <div class="col-md-4">
                        <div class="vendor-search-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="vendorSearch" class="form-control search-input" placeholder="Search vendors...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select id="vendorTypeFilter" class="form-select" data-title="Vendor Type">
                            <option value="">Vendor Type</option>
                            <option value="service_provider">Service Provider</option>
                            <option value="equipment_supplier">Equipment Supplier</option>
                            <option value="consultant">Consultant</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="statusFilter" class="form-select" data-title="Status">
                            <option value="">Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="gstFilter" class="form-select" data-title="GST">
                            <option value="">GST Status</option>
                            <option value="with_gst">With GST</option>
                            <option value="without_gst">Without GST</option>
                        </select>
                    </div>
                    <div class="col-md-2 text-end">
                        <a href="{{ route('business.vendors.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>ADD NEW VENDOR
                        </a>
                    </div>
                </div>

<!-- Vendor List Table -->
<div class="table-responsive">
    <table id="vendorTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th class="vendor_title">Vendor</th>
                <th>Type</th>
                <th>Contact</th>
                <th>GSTIN/PAN</th>
                <th>Registration Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="vendorTableBody">
            @forelse($vendors as $vendor)
            <tr>
                <td class="vendor_title">
                    <div class="d-flex align-items-center">
                        @php
                            $colors = ['#6B6ADE', '#FF6B6B', '#4ECDC4', '#FFE66D', '#FF8C94', '#95E1D3', '#F38181', '#AA96DA', '#FCBAD3', '#A8E6CF'];
                            $colorIndex = $vendor->id % count($colors);
                            $vendorColor = $colors[$colorIndex];
                        @endphp
                        <div class="brand-icon-placeholder me-2 d-flex align-items-center justify-content-center vendor-color-badge" 
                             style="width: 30px; height: 30px; background: {{ $vendorColor }}; color: white; border-radius: 4px; font-size: 12px; font-weight: bold;">
                            {{ strtoupper(substr($vendor->vendor_name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="fw-bold">{{ $vendor->vendor_name }}</div>
                            <small class="text-muted">{{ $vendor->mobile_number }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-{{ $vendor->vendor_type === 'vehicle_provider' ? 'primary' : ($vendor->vendor_type === 'service_partner' ? 'success' : 'secondary') }}">
                        {{ $vendor->vendor_type_label }}
                    </span>
                </td>
                <td>
                    <div>
                        <i class="fas fa-phone me-1"></i>{{ $vendor->mobile_number }}
                        @if($vendor->alternate_contact_number)
                            <br><small class="text-muted">{{ $vendor->alternate_contact_number }}</small>
                        @endif
                    </div>
                </td>
                <td>
                    @if($vendor->gstin)
                        <div><strong>GSTIN:</strong> {{ $vendor->masked_gstin }}</div>
                    @endif
                    <div><strong>PAN:</strong> {{ $vendor->masked_pan_number }}</div>
                </td>
                <td>{{ $vendor->created_at->format('M d, Y') }}</td>
                <td>
                    <a href="{{ route('business.vendors.show', $vendor) }}" class="text-primary text-decoration-none" style="font-weight: 500;">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No vendors found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
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
                <p>Are you sure you want to delete this vendor? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This will also delete all associated documents and files.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Vendor</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Live search and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const vendorTypeFilter = document.getElementById('vendorTypeFilter');
    const statusFilter = document.getElementById('statusFilter');
    const gstFilter = document.getElementById('gstFilter');
    const searchInput = document.getElementById('vendorSearch');
    
    // Get table rows
    const rows = document.querySelectorAll('#vendorTableBody tr');

    function applyFilters() {
        const vendorType = vendorTypeFilter ? vendorTypeFilter.value.toLowerCase() : '';
        const status = statusFilter ? statusFilter.value.toLowerCase() : '';
        const gst = gstFilter ? gstFilter.value.toLowerCase() : '';
        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

        rows.forEach(row => {
            let showRow = true;
            const text = row.textContent.toLowerCase();

            // Apply search filter
            if (searchTerm && !text.includes(searchTerm)) {
                showRow = false;
            }

            // Apply vendor type filter
            if (vendorType && showRow) {
                const vendorTypeLabel = text.includes('service provider') ? 'service provider' : 
                                      text.includes('equipment supplier') ? 'equipment supplier' : 
                                      text.includes('consultant') ? 'consultant' : '';
                if (vendorTypeLabel && !vendorTypeLabel.includes(vendorType)) {
                    showRow = false;
                }
            }

            // Apply status filter
            if (status && showRow) {
                if (status === 'active' && !text.includes('active')) {
                    showRow = false;
                } else if (status === 'inactive' && !text.includes('inactive')) {
                    showRow = false;
                }
            }

            // Apply GST filter
            if (gst && showRow) {
                if (gst === 'with_gst' && !text.includes('gst')) {
                    showRow = false;
                } else if (gst === 'without_gst' && text.includes('gst')) {
                    showRow = false;
                }
            }

            if (showRow) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Live filters - apply automatically on change
    if (vendorTypeFilter) vendorTypeFilter.addEventListener('change', applyFilters);
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    if (gstFilter) gstFilter.addEventListener('change', applyFilters);
    
    // Add search input listener
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
});

function confirmDelete(vendorId) {
    const form = document.getElementById('deleteForm');
    form.action = `/business/vendors/${vendorId}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
