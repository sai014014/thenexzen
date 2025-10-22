@extends('business.layouts.app')

@section('title', 'Vehicle Data Report')
@section('page-title', 'Vehicle Data Report')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-chart-line me-2"></i>Vehicle performance and revenue analysis
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('business.reports.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Reports
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-filter me-2"></i>Filters
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('business.reports.vehicle') }}" id="filterForm">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="date_from" class="form-label">Date From</label>
                                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                                   value="{{ request('date_from') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="date_to" class="form-label">Date To</label>
                                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                                   value="{{ request('date_to') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="ownership_type" class="form-label">Ownership Type</label>
                                            <select class="form-select" id="ownership_type" name="ownership_type">
                                                <option value="both" {{ request('ownership_type') == 'both' ? 'selected' : '' }}>Both</option>
                                                <option value="owned" {{ request('ownership_type') == 'owned' ? 'selected' : '' }}>Owned</option>
                                                <option value="vendor_provided" {{ request('ownership_type') == 'vendor_provided' ? 'selected' : '' }}>Vendor Provided</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="sort_field" class="form-label">Sort By</label>
                                            <select class="form-select" id="sort_field" name="sort_field">
                                                <option value="created_at" {{ request('sort_field') == 'created_at' ? 'selected' : '' }}>Registration Date</option>
                                                <option value="vehicle_make" {{ request('sort_field') == 'vehicle_make' ? 'selected' : '' }}>Vehicle Name</option>
                                                <option value="vehicle_number" {{ request('sort_field') == 'vehicle_number' ? 'selected' : '' }}>Vehicle Number</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="sort_direction" class="form-label">Sort Direction</label>
                                            <select class="form-select" id="sort_direction" name="sort_direction">
                                                <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                                                <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary me-2">
                                                <i class="fas fa-search me-2"></i>Apply Filters
                                            </button>
                                            <a href="{{ route('business.reports.vehicle') }}" class="btn btn-outline-secondary me-2">
                                                <i class="fas fa-times me-2"></i>Clear Filters
                                            </a>
                                            <button type="submit" name="export" value="1" class="btn btn-success">
                                                <i class="fas fa-download me-2"></i>Export CSV
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Data Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>S No</th>
                                <th>Vehicle Name</th>
                                <th>Vehicle Number</th>
                                <th>Owned/Vendor</th>
                                <th>Registered On</th>
                                <th>Insurance Expiry</th>
                                <th>Total Rentals</th>
                                <th>Total Kilometers</th>
                                <th>Net Revenue</th>
                                <th>Last Rental Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicleData as $vehicle)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $vehicle['name'] }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $vehicle['vehicle_number'] }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $vehicle['ownership'] === 'Owned' ? 'bg-success' : 'bg-warning' }}">
                                        {{ $vehicle['ownership'] }}
                                    </span>
                                    @if($vehicle['ownership'] === 'Vendor Provided')
                                        <br><small class="text-muted">{{ $vehicle['vendor_name'] }}</small>
                                    @endif
                                </td>
                                <td>{{ $vehicle['registered_on'] }}</td>
                                <td>{{ $vehicle['insurance_expiry'] }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $vehicle['total_rentals'] }}</span>
                                </td>
                                <td>{{ $vehicle['total_kilometers'] }}</td>
                                <td>
                                    <strong class="text-success">₹{{ number_format($vehicle['net_revenue'], 2) }}</strong>
                                </td>
                                <td>{{ $vehicle['last_rental_date'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-car fa-3x mb-3"></i>
                                        <h5>No vehicles found</h5>
                                        <p>No vehicles match the selected filters.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(count($vehicleData) > 0)
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="text-muted">
                            Showing {{ count($vehicleData) }} vehicle(s)
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="text-muted">
                            Total Revenue: <strong class="text-success">₹{{ number_format($vehicleData->sum('net_revenue'), 2) }}</strong>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const filterForm = document.getElementById('filterForm');
    const filterInputs = filterForm.querySelectorAll('select, input[type="date"]');
    
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Don't auto-submit for export button
            if (this.name !== 'export') {
                filterForm.submit();
            }
        });
    });
});
</script>
@endpush
