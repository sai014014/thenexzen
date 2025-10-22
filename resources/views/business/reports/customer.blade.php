@extends('business.layouts.app')

@section('title', 'Customer Data Report')
@section('page-title', 'Customer Data Report')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-chart-line me-2"></i>Comprehensive customer analysis and booking data
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
                                <form method="GET" action="{{ route('business.reports.customer') }}" id="filterForm">
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
                                            <label for="customer_type" class="form-label">Customer Type</label>
                                            <select class="form-select" id="customer_type" name="customer_type">
                                                <option value="both" {{ request('customer_type') == 'both' ? 'selected' : '' }}>Both</option>
                                                <option value="individual" {{ request('customer_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                                                <option value="corporate" {{ request('customer_type') == 'corporate' ? 'selected' : '' }}>Corporate</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="sort_field" class="form-label">Sort By</label>
                                            <select class="form-select" id="sort_field" name="sort_field">
                                                <option value="created_at" {{ request('sort_field') == 'created_at' ? 'selected' : '' }}>Registration Date</option>
                                                <option value="full_name" {{ request('sort_field') == 'full_name' ? 'selected' : '' }}>Customer Name</option>
                                                <option value="mobile_number" {{ request('sort_field') == 'mobile_number' ? 'selected' : '' }}>Contact Number</option>
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
                                            <a href="{{ route('business.reports.customer') }}" class="btn btn-outline-secondary me-2">
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

                <!-- Customer Data Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>S No</th>
                                <th>Customer Name</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Contact Number</th>
                                <th>Registered On</th>
                                <th>License Expiry</th>
                                <th>Total Bookings</th>
                                <th>Net Bill</th>
                                <th>Last Booking Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customerData as $customer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $customer['name'] }}</strong>
                                </td>
                                <td>
                                    <span class="badge {{ $customer['type'] === 'individual' ? 'bg-info' : 'bg-warning' }}">
                                        {{ ucfirst($customer['type']) }}
                                    </span>
                                </td>
                                <td>{{ $customer['location'] }}</td>
                                <td>{{ $customer['contact_number'] }}</td>
                                <td>{{ $customer['registered_on'] }}</td>
                                <td>{{ $customer['license_expiry'] }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $customer['total_bookings'] }}</span>
                                </td>
                                <td>
                                    <strong class="text-success">₹{{ number_format($customer['net_bill'], 2) }}</strong>
                                </td>
                                <td>{{ $customer['last_booking_date'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <h5>No customers found</h5>
                                        <p>No customers match the selected filters.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(count($customerData) > 0)
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="text-muted">
                            Showing {{ count($customerData) }} customer(s)
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="text-muted">
                            Total Revenue: <strong class="text-success">₹{{ number_format($customerData->sum('net_bill'), 2) }}</strong>
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
