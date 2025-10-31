@extends('business.layouts.app')

@section('title', 'Vendor Data Report')
@section('page-title', 'Vendor Data Report')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Top toolbar -->
                <div class="row align-items-center mb-3">
                    <div class="col-md-6">
                        <small class="text-muted"><span id="vendorCount">{{ count($vendorData) }}</span> Records Found</small>
                    </div>
                    <div class="col-md-6">
                        <form id="vendorFiltersInline" method="GET" action="{{ route('business.reports.vendor') }}" class="d-flex justify-content-end align-items-center gap-2 flex-nowrap">
                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                            <select class="form-select" name="vendor_status">
                                <option value="all" {{ request('vendor_status') == 'all' ? 'selected' : '' }}>All</option>
                                <option value="active" {{ request('vendor_status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('vendor_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <button id="exportVendorFilteredBtn" type="submit" name="export" value="1" class="btn btn-primary btn-pill" style="display: none;">
                                <i class="fas fa-download me-2"></i>Export Filtered Data
                            </button>
                            <a id="exportVendorAllBtn" href="{{ route('business.reports.vendor', ['export' => 1]) }}" class="btn btn-outline-secondary btn-pill">Export All</a>
                        </form>
                    </div>
                </div>
                </div>

                <!-- Vendor Data Table -->
                <div class="filter-section">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>S No</th>
                                <th>Vendor Name</th>
                                <th>Contact Number</th>
                                <th>Active Status</th>
                                <th>Registered On</th>
                                <th>Total Vehicles</th>
                                <th>Total Bookings</th>
                                <th>Commission Type</th>
                                <th>Net Revenue</th>
                                <th>Total Commission</th>
                            </tr>
                            </thead>
                            <tbody id="vendorReportTbody">
                                @include('business.reports.partials.vendor_table_rows', ['vendorData' => $vendorData])
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="text-muted">
                            Showing <span id="vendorCountBottom">{{ count($vendorData) }}</span> vendor(s)
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="text-muted">
                            Total Revenue: <strong class="text-success">₹<span id="vendorTotalRevenue">{{ number_format($vendorData->sum('net_revenue'), 2) }}</span></strong>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="text-muted">
                            Total Commission: <strong class="text-primary">₹<span id="vendorTotalCommission">{{ number_format($vendorData->sum('total_commission'), 2) }}</span></strong>
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
    const form = document.getElementById('vendorFiltersInline');
    const tbody = document.getElementById('vendorReportTbody');
    const topCount = document.getElementById('vendorCount');
    const bottomCount = document.getElementById('vendorCountBottom');
    const totalRevenue = document.getElementById('vendorTotalRevenue');
    const totalCommission = document.getElementById('vendorTotalCommission');
    const exportFilteredBtn = document.getElementById('exportVendorFilteredBtn');
    const exportAllBtn = document.getElementById('exportVendorAllBtn');

    function anyFilterActive() {
        const params = new URLSearchParams(new FormData(form));
        const dateFrom = params.get('date_from');
        const dateTo = params.get('date_to');
        const status = params.get('vendor_status');
        return (dateFrom && dateFrom.length) || (dateTo && dateTo.length) || (status && status !== 'all');
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
            totalCommission.textContent = data.totalCommission;
        } catch (_) {}

        toggleExportFiltered();
        const currentParams = new URLSearchParams(new FormData(form));
        currentParams.set('export', '1');
        exportFilteredBtn.formAction = form.action + '?' + currentParams.toString();
        exportAllBtn.href = form.action + '?export=1';
    }

    form.querySelectorAll('input[name="date_from"], input[name="date_to"], select[name="vendor_status"]').forEach(el => el.addEventListener('change', refreshData));
    toggleExportFiltered();
});
</script>
@endpush

@push('styles')
@endpush
