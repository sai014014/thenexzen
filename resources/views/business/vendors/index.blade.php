@extends('business.layouts.app')

@section('title', 'Vendor Management')
@section('page-title', 'Vendor Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-truck me-2"></i>Vendor Management
                        </h5>
                        <small class="text-muted">Manage your vendors and service partners</small>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('business.vendors.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Vendor
                        </a>
                    </div>
                </div>

                <!-- Search and Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Search by name, mobile, or GSTIN..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="vendorTypeFilter">
                            <option value="">All Types</option>
                            <option value="vehicle_provider" {{ request('vendor_type') == 'vehicle_provider' ? 'selected' : '' }}>Vehicle Provider</option>
                            <option value="service_partner" {{ request('vendor_type') == 'service_partner' ? 'selected' : '' }}>Service Partner</option>
                            <option value="other" {{ request('vendor_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="sortByFilter">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Registration Date</option>
                            <option value="vendor_name" {{ request('sort_by') == 'vendor_name' ? 'selected' : '' }}>Name</option>
                            <option value="gstin" {{ request('sort_by') == 'gstin' ? 'selected' : '' }}>GSTIN</option>
                            <option value="pan_number" {{ request('sort_by') == 'pan_number' ? 'selected' : '' }}>PAN Number</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="sortDirectionFilter">
                            <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                            <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                            <i class="fas fa-times me-1"></i>Clear
                        </button>
                    </div>
                </div>

                <!-- Vendor List Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Vendor Name</th>
                                <th>Type</th>
                                <th>Mobile Number</th>
                                <th>GSTIN/PAN</th>
                                <th>Registration Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendors as $vendor)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $vendor->vendor_name }}</strong>
                                        @if($vendor->primary_contact_person)
                                            <br><small class="text-muted">{{ $vendor->primary_contact_person }}</small>
                                        @endif
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
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('business.vendors.show', $vendor) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('business.vendors.edit', $vendor) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $vendor->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-truck fa-3x mb-3"></i>
                                        <h5>No vendors found</h5>
                                        <p>Start by adding your first vendor to the system.</p>
                                        <a href="{{ route('business.vendors.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Add First Vendor
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($vendors->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $vendors->firstItem() }} to {{ $vendors->lastItem() }} of {{ $vendors->total() }} results
                    </div>
                    <div>
                        {{ $vendors->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
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
// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value;
    if (searchTerm.length >= 3 || searchTerm.length === 0) {
        applyFilters();
    }
});

// Filter functionality
document.getElementById('vendorTypeFilter').addEventListener('change', applyFilters);
document.getElementById('sortByFilter').addEventListener('change', applyFilters);
document.getElementById('sortDirectionFilter').addEventListener('change', applyFilters);

function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const vendorType = document.getElementById('vendorTypeFilter').value;
    const sortBy = document.getElementById('sortByFilter').value;
    const sortDirection = document.getElementById('sortDirectionFilter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (vendorType) params.append('vendor_type', vendorType);
    if (sortBy) params.append('sort_by', sortBy);
    if (sortDirection) params.append('sort_direction', sortDirection);
    
    window.location.href = '{{ route("business.vendors.index") }}?' + params.toString();
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('vendorTypeFilter').value = '';
    document.getElementById('sortByFilter').value = 'created_at';
    document.getElementById('sortDirectionFilter').value = 'desc';
    applyFilters();
}

function confirmDelete(vendorId) {
    const form = document.getElementById('deleteForm');
    form.action = `/business/vendors/${vendorId}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush
