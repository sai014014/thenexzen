@extends('business.layouts.app')

@section('title', 'Booking Data Report')
@section('page-title', 'Booking Data Report')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Top toolbar -->
                <div class="row align-items-center mb-3">
                    <div class="col-md-6">
                        <small class="text-muted"><span id="bookingCount">{{ count($bookingData) }}</span> Records Found</small>
                    </div>
                    <div class="col-md-6">
                        <form id="bookingFiltersInline" method="GET" action="{{ route('business.reports.booking') }}" class="d-flex justify-content-end align-items-center gap-2 flex-nowrap">
                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                            <button id="exportBookingFilteredBtn" type="submit" name="export" value="1" class="btn btn-primary btn-pill" style="display: none;">
                                <i class="fas fa-download me-2"></i>Export Filtered Data
                            </button>
                            <a id="exportBookingAllBtn" href="{{ route('business.reports.booking', ['export' => 1]) }}" class="btn btn-outline-secondary btn-pill">Export All</a>
                        </form>
                    </div>
                </div>
                </div>

                <!-- Booking Data Table -->
                <div class="filter-section">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
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
                            <tbody id="bookingReportTbody">
                                @include('business.reports.partials.booking_table_rows', ['bookingData' => $bookingData])
                            </tbody>
                            @if(count($bookingData) > 0)
                            <tfoot>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td></td>
                                <td>
                                    <span class="badge bg-success" id="bookingTotalCompleted">{{ $totals['completed_bookings'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary" id="bookingTotalVehicles">{{ $totals['vehicles_booked'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info" id="bookingTotalUnique">{{ $totals['unique_customers'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning" id="bookingTotalReturning">{{ $totals['returning_customers'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-danger" id="bookingTotalCancelled">{{ $totals['cancelled_bookings'] }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $totals['cancellation_rate'] > 10 ? 'bg-danger' : ($totals['cancellation_rate'] > 5 ? 'bg-warning' : 'bg-success') }}" id="bookingTotalCancelRate">
                                        {{ $totals['cancellation_rate'] }}%
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-success">₹<span id="bookingTotalRevenue">{{ number_format($totals['total_revenue'], 2) }}</span></strong>
                                </td>
                                <td>
                                    <strong class="text-primary">₹<span id="bookingAvgValue">{{ number_format($totals['average_booking_value'], 2) }}</span></strong>
                                </td>
                            </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
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
    const form = document.getElementById('bookingFiltersInline');
    const tbody = document.getElementById('bookingReportTbody');
    const count = document.getElementById('bookingCount');
    const exportFilteredBtn = document.getElementById('exportBookingFilteredBtn');
    const exportAllBtn = document.getElementById('exportBookingAllBtn');

    const totalCompleted = document.getElementById('bookingTotalCompleted');
    const totalVehicles = document.getElementById('bookingTotalVehicles');
    const totalUnique = document.getElementById('bookingTotalUnique');
    const totalReturning = document.getElementById('bookingTotalReturning');
    const totalCancelled = document.getElementById('bookingTotalCancelled');
    const totalCancelRate = document.getElementById('bookingTotalCancelRate');
    const totalRevenue = document.getElementById('bookingTotalRevenue');
    const avgValue = document.getElementById('bookingAvgValue');

    function anyFilterActive() {
        const params = new URLSearchParams(new FormData(form));
        const df = params.get('date_from');
        const dt = params.get('date_to');
        return (df && df.length) || (dt && dt.length);
    }

    function toggleExportFiltered() {
        exportFilteredBtn.style.display = anyFilterActive() ? '' : 'none';
    }

    async function refreshData() {
        const url = new URL(form.action);
        const params = new URLSearchParams(new FormData(form));
        url.search = params.toString();
        try {
            const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) return;
            const data = await res.json();
            tbody.innerHTML = data.tbody;
            count.textContent = data.count;
            totalCompleted.textContent = data.totals.completed_bookings;
            totalVehicles.textContent = data.totals.vehicles_booked;
            totalUnique.textContent = data.totals.unique_customers;
            totalReturning.textContent = data.totals.returning_customers;
            totalCancelled.textContent = data.totals.cancelled_bookings;
            totalCancelRate.textContent = data.totals.cancellation_rate + '%';
            totalRevenue.textContent = data.totals.total_revenue;
            avgValue.textContent = data.totals.average_booking_value;
        } catch (_) {}

        toggleExportFiltered();
        const currentParams = new URLSearchParams(new FormData(form));
        currentParams.set('export', '1');
        exportFilteredBtn.formAction = form.action + '?' + currentParams.toString();
        exportAllBtn.href = form.action + '?export=1';
    }

    form.querySelectorAll('input[name="date_from"], input[name="date_to"]').forEach(el => el.addEventListener('change', refreshData));
    toggleExportFiltered();
});
</script>
@endpush

@push('styles')
@endpush
