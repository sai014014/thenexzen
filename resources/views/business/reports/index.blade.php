@extends('business.layouts.app')

@section('title', 'Reports Dashboard')
@section('page-title', 'Reports Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Reports Dashboard
                </h5>
                <small class="text-muted">Generate and view comprehensive business reports</small>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Customer Data Report -->
                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="fas fa-users fa-3x text-primary"></i>
                                </div>
                                <h5 class="card-title">Customer Data Report</h5>
                                <p class="card-text text-muted">
                                    View customer information, booking history, and revenue data with date and type filters.
                                </p>
                                <a href="{{ route('business.reports.customer') }}" class="btn btn-primary">
                                    <i class="fas fa-eye me-2"></i>View Report
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Data Report -->
                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="fas fa-car fa-3x text-success"></i>
                                </div>
                                <h5 class="card-title">Vehicle Data Report</h5>
                                <p class="card-text text-muted">
                                    Analyze vehicle performance, revenue, and rental statistics with ownership filters.
                                </p>
                                <a href="{{ route('business.reports.vehicle') }}" class="btn btn-success">
                                    <i class="fas fa-eye me-2"></i>View Report
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Vendor Data Report -->
                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="fas fa-truck fa-3x text-warning"></i>
                                </div>
                                <h5 class="card-title">Vendor Data Report</h5>
                                <p class="card-text text-muted">
                                    Track vendor performance, commission calculations, and revenue contributions.
                                </p>
                                <a href="{{ route('business.reports.vendor') }}" class="btn btn-warning">
                                    <i class="fas fa-eye me-2"></i>View Report
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Data Report -->
                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="fas fa-calendar-alt fa-3x text-info"></i>
                                </div>
                                <h5 class="card-title">Booking Data Report</h5>
                                <p class="card-text text-muted">
                                    Analyze booking trends, revenue patterns, and customer behavior over time.
                                </p>
                                <a href="{{ route('business.reports.booking') }}" class="btn btn-info">
                                    <i class="fas fa-eye me-2"></i>View Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-tachometer-alt me-2"></i>Quick Statistics
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3 mb-3">
                                        <div class="border rounded p-3">
                                            <h4 class="text-primary mb-1">{{ $business->customers()->count() }}</h4>
                                            <small class="text-muted">Total Customers</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="border rounded p-3">
                                            <h4 class="text-success mb-1">{{ $business->vehicles()->count() }}</h4>
                                            <small class="text-muted">Total Vehicles</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="border rounded p-3">
                                            <h4 class="text-warning mb-1">{{ $business->vendors()->count() }}</h4>
                                            <small class="text-muted">Total Vendors</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="border rounded p-3">
                                            <h4 class="text-info mb-1">{{ $business->bookings()->count() }}</h4>
                                            <small class="text-muted">Total Bookings</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
