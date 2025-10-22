@extends('business.layouts.app')

@section('title', 'Booking Data Report')
@section('page-title', 'Booking Data Report')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-chart-line me-2"></i>Booking trends and revenue analysis
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
                                <form method="GET" action="{{ route('business.reports.booking') }}" id="filterForm">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="date_from" class="form-label">Date From</label>
                                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                                   value="{{ request('date_from') }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="date_to" class="form-label">Date To</label>
                                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                                   value="{{ request('date_to') }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary me-2">
                                                <i class="fas fa-search me-2"></i>Apply Filters
                                            </button>
                                            <a href="{{ route('business.reports.booking') }}" class="btn btn-outline-secondary me-2">
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

                <!-- Booking Data Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>S No</th>
                                <th>Date</th>
                                <th>Completed Bookings</th>
                                <th>Vehicles Booked</th>
                                <th>Unique Customers</th>
                                <th>Returning Customers</th>
                                <th>Cancelled Bookings</th>
                                <th>Cancellation Rate</th>
                                <th>Total Revenue</th>
                                <th>Average Booking Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookingData as $date => $booking)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $booking['date'] }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $booking['completed_bookings'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $booking['vehicles_booked'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $booking['unique_customers'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">{{ $booking['returning_customers'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-danger">{{ $booking['cancelled_bookings'] }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $booking['cancellation_rate'] > 10 ? 'bg-danger' : ($booking['cancellation_rate'] > 5 ? 'bg-warning' : 'bg-success') }}">
                                        {{ $booking['cancellation_rate'] }}%
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-success">₹{{ number_format($booking['total_revenue'], 2) }}</strong>
                                </td>
                                <td>
                                    <strong class="text-primary">₹{{ number_format($booking['average_booking_value'], 2) }}</strong>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                                        <h5>No booking data found</h5>
                                        <p>No bookings match the selected date range.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if(count($bookingData) > 0)
                        <tfoot class="table-light">
                            <tr>
                                <td><strong>Total</strong></td>
                                <td></td>
                                <td>
                                    <span class="badge bg-success">{{ $totals['completed_bookings'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $totals['vehicles_booked'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $totals['unique_customers'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">{{ $totals['returning_customers'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-danger">{{ $totals['cancelled_bookings'] }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $totals['cancellation_rate'] > 10 ? 'bg-danger' : ($totals['cancellation_rate'] > 5 ? 'bg-warning' : 'bg-success') }}">
                                        {{ $totals['cancellation_rate'] }}%
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-success">₹{{ number_format($totals['total_revenue'], 2) }}</strong>
                                </td>
                                <td>
                                    <strong class="text-primary">₹{{ number_format($totals['average_booking_value'], 2) }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>

                @if(count($bookingData) > 0)
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="text-muted">
                            Showing {{ count($bookingData) }} day(s) of data
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="text-muted">
                            Total Revenue: <strong class="text-success">₹{{ number_format($totals['total_revenue'], 2) }}</strong>
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
