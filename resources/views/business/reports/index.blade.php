@extends('business.layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div class="reports-page">
    <div class="row g-4">
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('business.reports.booking') }}" class="report-card d-block text-decoration-none">
                <div class="content">
                    <div class="header-row">
                        <div class="icon-wrap bg-indigo-subtle text-indigo">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h6 class="title mb-0">Booking Data Report</h6>
                    </div>
                    <p class="desc mb-0">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6">
            <a href="{{ route('business.reports.vehicle') }}" class="report-card d-block text-decoration-none">
                <div class="content">
                    <div class="header-row">
                        <div class="icon-wrap bg-blue-subtle text-indigo">
                            <i class="fas fa-car"></i>
                        </div>
                        <h6 class="title mb-0">Vehicle Data Report</h6>
                    </div>
                    <p class="desc mb-0">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6">
            <a href="{{ route('business.reports.vendor') }}" class="report-card d-block text-decoration-none">
                <div class="content">
                    <div class="header-row">
                        <div class="icon-wrap bg-indigo-subtle text-indigo">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h6 class="title mb-0">Vendor Data Report</h6>
                    </div>
                    <p class="desc mb-0">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6">
            <a href="{{ route('business.reports.customer') }}" class="report-card d-block text-decoration-none">
                <div class="content">
                    <div class="header-row">
                        <div class="icon-wrap bg-indigo-subtle text-indigo">
                            <i class="fas fa-user"></i>
                        </div>
                        <h6 class="title mb-0">Customer Data Report</h6>
                    </div>
                    <p class="desc mb-0">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.reports-page { padding: 12px 16px; }
.report-card { background: #FFFFFF; border: 1px solid rgba(17, 24, 39, 0.06); border-radius: 16px; padding: 18px 20px; box-shadow: 0 2px 10px rgba(16,24,40,0.05); transition: transform .18s ease, box-shadow .18s ease; }
.report-card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(16,24,40,0.10); border-color: #635BFF; }
.report-card .header-row { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
.report-card .icon-wrap { width: 40px; height: 40px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 18px; }
.report-card .content .title { color: #0F172A; font-weight: 600; }
.report-card .content .desc { color: #6B7280; font-size: 13px; line-height: 1.5; padding-left: 52px; }
.text-indigo { color: #635BFF !important; }
.bg-indigo-subtle { background: #F0EEFF !important; }
.text-blue { color: #2563EB !important; }
.bg-blue-subtle { background: #E6F0FF !important; }
</style>
@endpush
