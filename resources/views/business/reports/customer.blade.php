@extends('business.layouts.app')

@section('title', 'Customer Data Report')
@section('page-title', 'Customer Data Report')

@section('content')
<div class="row">
    <div class="col-12">
     
          
                
                   
                    
                        <a href="{{ route('business.reports.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Reports
                        </a>
                   
             
            </div>
            <div class="card-body">
                <!-- Top toolbar: records count, type dropdown, filters button, export -->
                <div class="row align-items-center mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">
                            {{ count($customerData) }} Records Found
                        </small>
                    </div>
                    <div class="col-md-6">
                        <form id="customerFiltersInline" method="GET" action="{{ route('business.reports.customer') }}" class="d-flex justify-content-end align-items-center gap-2 flex-nowrap">
                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                            <select class="form-select" name="customer_type">
                                <option value="both" {{ request('customer_type') == 'both' ? 'selected' : '' }}>Both</option>
                                <option value="individual" {{ request('customer_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                                <option value="corporate" {{ request('customer_type') == 'corporate' ? 'selected' : '' }}>Corporate</option>
                            </select>
                            <button id="exportFilteredBtn" type="submit" name="export" value="1" class="btn btn-primary btn-pill" style="display: none;">
                                <i class="fas fa-download me-2"></i>Export Filtered Data
                            </button>
                            <a id="exportAllBtn" href="{{ route('business.reports.customer', ['export' => 1]) }}" class="btn btn-outline-secondary btn-pill">
                                Export All
                            </a>
                        </form>
                    </div>
                </div>
                </div>

                <!-- Customer Data Table -->
                <div class="filter-section">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
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
                            <tbody id="customerReportTbody">
                                @include('business.reports.partials.customer_table_rows', ['customerData' => $customerData])
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="text-muted">
                            Showing <span id="customerCount">{{ count($customerData) }}</span> customer(s)
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="text-muted">
                            Total Revenue: <strong class="text-success">₹<span id="customerTotalRevenue">{{ number_format($customerData->sum('net_bill'), 2) }}</span></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('customerFiltersInline');
    const tbody = document.getElementById('customerReportTbody');
    const countEl = document.getElementById('customerCount');
    const totalEl = document.getElementById('customerTotalRevenue');
    const exportFilteredBtn = document.getElementById('exportFilteredBtn');
    const exportAllBtn = document.getElementById('exportAllBtn');

    function anyFilterActive() {
        const params = new URLSearchParams(new FormData(form));
        const dateFrom = params.get('date_from');
        const dateTo = params.get('date_to');
        const type = params.get('customer_type');
        return (dateFrom && dateFrom.length) || (dateTo && dateTo.length) || (type && type !== 'both');
    }

    function toggleExportFiltered() {
        if (anyFilterActive()) {
            exportFilteredBtn.style.display = '';
        } else {
            exportFilteredBtn.style.display = 'none';
        }
    }

    async function refreshData() {
        const url = new URL(form.action);
        const params = new URLSearchParams(new FormData(form));
        url.search = params.toString();
        try {
            const res = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const data = await res.json();
            tbody.innerHTML = data.tbody;
            countEl.textContent = data.count;
            totalEl.textContent = data.totalRevenue;
        } catch (e) {
            // fail silently
        }
        toggleExportFiltered();
        // Ensure export filtered submits with current filters
        const currentParams = new URLSearchParams(new FormData(form));
        currentParams.set('export', '1');
        exportFilteredBtn.formAction = form.action + '?' + currentParams.toString();
        // Export All should export without filters
        exportAllBtn.href = form.action + '?export=1';
    }

    form.querySelectorAll('input[name="date_from"], input[name="date_to"], select[name="customer_type"]').forEach(el => {
        el.addEventListener('change', refreshData);
    });

    // Initial state
    toggleExportFiltered();
});
</script>
@endpush
