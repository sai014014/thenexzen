@extends('business.layouts.app')

@section('title', 'Vehicle Data Report')
@section('page-title', 'Vehicle Data Report')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Top toolbar -->
                <div class="row align-items-center mb-3">
                    <div class="col-md-6">
                        <small class="text-muted"><span id="vehicleCount">{{ count($vehicleData) }}</span> Records Found</small>
                    </div>
                    <div class="col-md-6">
                        <form id="vehicleFiltersInline" method="GET" action="{{ route('business.reports.vehicle') }}" class="d-flex justify-content-end align-items-center gap-2 flex-nowrap">
                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                            <select class="form-select" name="ownership_type">
                                <option value="both" {{ request('ownership_type') == 'both' ? 'selected' : '' }}>Both</option>
                                <option value="owned" {{ request('ownership_type') == 'owned' ? 'selected' : '' }}>Owned</option>
                                <option value="vendor_provided" {{ request('ownership_type') == 'vendor_provided' ? 'selected' : '' }}>Vendor Provided</option>
                            </select>
                            <button id="exportVehicleFilteredBtn" type="submit" name="export" value="1" class="btn btn-primary btn-pill" style="display: none;">
                                <i class="fas fa-download me-2"></i>Export Filtered Data
                            </button>
                            <a id="exportVehicleAllBtn" href="{{ route('business.reports.vehicle', ['export' => 1]) }}" class="btn btn-outline-secondary btn-pill">Export All</a>
                        </form>
                    </div>
                </div>
                </div>

                <!-- Vehicle Data Table -->
                <div class="filter-section">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
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
                            <tbody id="vehicleReportTbody">
                                @include('business.reports.partials.vehicle_table_rows', ['vehicleData' => $vehicleData])
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="text-muted">
                            Showing <span id="vehicleCountBottom">{{ count($vehicleData) }}</span> vehicle(s)
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="text-muted">
                            Total Revenue: <strong class="text-success">₹<span id="vehicleTotalRevenue">{{ number_format($vehicleData->sum('net_revenue'), 2) }}</span></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('vehicleFiltersInline');
    const tbody = document.getElementById('vehicleReportTbody');
    const topCount = document.getElementById('vehicleCount');
    const bottomCount = document.getElementById('vehicleCountBottom');
    const totalRevenue = document.getElementById('vehicleTotalRevenue');
    const exportFilteredBtn = document.getElementById('exportVehicleFilteredBtn');
    const exportAllBtn = document.getElementById('exportVehicleAllBtn');

    function anyFilterActive() {
        const params = new URLSearchParams(new FormData(form));
        const df = params.get('date_from');
        const dt = params.get('date_to');
        const owner = params.get('ownership_type');
        return (df && df.length) || (dt && dt.length) || (owner && owner !== 'both');
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
            topCount.textContent = data.count;
            bottomCount.textContent = data.count;
            totalRevenue.textContent = data.totalRevenue;
        } catch (_) {}
        toggleExportFiltered();
        const currentParams = new URLSearchParams(new FormData(form));
        currentParams.set('export', '1');
        exportFilteredBtn.formAction = form.action + '?' + currentParams.toString();
        exportAllBtn.href = form.action + '?export=1';
    }

    form.querySelectorAll('input[name="date_from"], input[name="date_to"], select[name="ownership_type"]').forEach(el => el.addEventListener('change', refreshData));
    toggleExportFiltered();
});
</script>
@endpush

@push('styles')
@endpush
