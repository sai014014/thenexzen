@extends('business.layouts.app')

@section('title', 'Booking Management')
@section('page-title', 'Booking Management')

@push('styles')
    @vite(['resources/css/bookings.css'])
@endpush

@section('content')
<div class="content-wrapper">
    <!-- Draft Alert Banner -->
    @if(isset($hasDraft) && $hasDraft)
    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center mb-3" role="alert">
        <i class="fas fa-save me-2 fs-5"></i>
        <div class="flex-grow-1">
            <strong>You have a saved booking draft!</strong> Continue where you left off or start a new booking.
        </div>
        <div class="d-flex gap-2 ms-3">
            <a href="{{ route('business.bookings.flow.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit me-1"></i>Continue Draft
            </a>
            <form action="{{ route('business.bookings.flow.clear_draft') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete your saved draft?')">
                    <i class="fas fa-trash me-1"></i>Delete Draft
                </button>
            </form>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Booking Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="{{ route('business.bookings.flow.create') }}" class="btn btn-primary">
                <i class="fas fa-magic me-2"></i>Advance Booking
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <ul class="nav nav-tabs mb-3" id="bookingTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $status === 'all' ? 'active' : '' }}" 
                    id="all-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#all" 
                    type="button" 
                    role="tab"
                    onclick="switchTab('all')">
                <i class="fas fa-list me-1"></i>All Bookings
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $status === 'upcoming' ? 'active' : '' }}" 
                    id="upcoming-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#upcoming" 
                    type="button" 
                    role="tab"
                    onclick="switchTab('upcoming')">
                <i class="fas fa-clock me-1"></i>Upcoming
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $status === 'ongoing' ? 'active' : '' }}" 
                    id="ongoing-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#ongoing" 
                    type="button" 
                    role="tab"
                    onclick="switchTab('ongoing')">
                <i class="fas fa-play-circle me-1"></i>Ongoing
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $status === 'completed' ? 'active' : '' }}" 
                    id="completed-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#completed" 
                    type="button" 
                    role="tab"
                    onclick="switchTab('completed')">
                <i class="fas fa-check-circle me-1"></i>Completed
            </button>
        </li>
    </ul>

    <!-- Search Bar -->
    <div class="row mb-3 align-items-end filter-row">
        <div class="col-md-12">
            <div class="vehicle-search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="bookingSearch" class="form-control search-input" placeholder="Search by booking ID, customer, or vehicle..." value="{{ request('search') }}">
            </div>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="filter-section">
        <div class="table-responsive">
            <table id="bookingTable" class="table table-striped table-bordered">
            <thead>
                    @php
                        $currentSort = request('sort');
                        $currentDir = strtolower(request('dir', 'desc')) === 'desc' ? 'desc' : 'asc';
                        $nextDir = $currentDir === 'asc' ? 'desc' : 'asc';
                        $sortLink = function ($key, $label) use ($currentSort, $currentDir, $nextDir) {
                            $params = array_merge(request()->query(), [
                                'sort' => $key,
                                'dir' => $currentSort === $key ? $nextDir : 'asc',
                            ]);
                            // Always show a sort icon; neutral when not active
                            $icon = '↕';
                            if ($currentSort === $key) {
                                $icon = $currentDir === 'asc' ? '▲' : '▼';
                            }
                            $url = request()->url() . '?' . http_build_query($params);
                            $activeClass = $currentSort === $key ? 'text-primary fw-semibold' : 'text-muted';
                            $iconStyle = 'font-size:11px;';
                            if ($currentSort !== $key) { $iconStyle .= 'opacity:0.6;'; }
                            return '<a href="' . e($url) . '" class="text-decoration-none">' . e($label) . ' <span class="ms-1 ' . $activeClass . '" style="' . $iconStyle . '">' . e($icon) . '</span></a>';
                        };
                    @endphp
                    <tr>
                        <th>{!! $sortLink('booking_id', 'Booking ID') !!}</th>
                        <th>{!! $sortLink('customer', 'Customer Name') !!}</th>
                        <th>{!! $sortLink('vehicle', 'Vehicle Details') !!}</th>
                        <th>{!! $sortLink('start_date', 'Start Date & Time') !!}</th>
                        @if($status !== 'upcoming' && $status !== 'all')
                        <th>{!! $sortLink('end_date', 'End Date & Time') !!}</th>
                        @elseif($status === 'all')
                        <th>{!! $sortLink('end_date', 'End Date & Time') !!}</th>
                        @endif
                        <th>{!! $sortLink('status', 'Status') !!}</th>
                        @if($status === 'ongoing' || $status === 'all')
                        <th>{!! $sortLink('amount_due', 'Amount Due') !!}</th>
                        @endif
                        <th>Actions</th>
                    </tr>
                </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $booking->booking_number }}</span>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $booking->customer->display_name }}</strong>
                                        <br><small class="text-muted">{{ $booking->customer->mobile_number }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $booking->vehicle->vehicle_make }} {{ $booking->vehicle->vehicle_model }}</strong>
                                        <br><small class="text-muted">{{ $booking->vehicle->vehicle_number }}</small>
                                    </div>
                                </td>
                                <td>{{ $booking->start_date_time->format('M d, Y H:i') }}</td>
                                @if($status !== 'upcoming' && $status !== 'all')
                                <td>{{ $booking->end_date_time->format('M d, Y H:i') }}</td>
                                @elseif($status === 'all')
                                <td>{{ $booking->end_date_time->format('M d, Y H:i') }}</td>
                                @endif
                                <td>
                                    <span class="badge {{ $booking->status_badge_class }}">
                                        {{ $booking->status_label }}
                                    </span>
                                </td>
                                @if($status === 'ongoing' || $status === 'all')
                                <td>
                                    <strong>₹{{ number_format($booking->amount_due, 2) }}</strong>
                                    @if($booking->amount_paid > 0)
                                        <br><small class="text-success">Paid: ₹{{ number_format($booking->amount_paid, 2) }}</small>
                                    @endif
                                </td>
                                @endif
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('business.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($booking->status === 'upcoming')
                                        <a href="{{ route('business.bookings.edit', $booking) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $status === 'upcoming' ? '7' : ($status === 'all' ? '8' : '8') }}" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                                        <h5>No {{ $status === 'all' ? '' : $status . ' ' }}bookings found</h5>
                                        <p>
                                            @if($status === 'all')
                                                No bookings found in the system.
                                            @elseif($status === 'ongoing')
                                                No ongoing bookings at the moment.
                                            @elseif($status === 'upcoming')
                                                No upcoming bookings scheduled.
                                            @else
                                                No completed bookings found.
                                            @endif
                                        </p>
                                        @if($status === 'upcoming' || $status === 'all')
                                        <a href="{{ route('business.bookings.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Create First Booking
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
        </div>
    </div>

                @if($bookings->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} results
                    </div>
                    <div>
                        {{ $bookings->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Search functionality
document.getElementById('bookingSearch').addEventListener('input', function() {
    const searchTerm = this.value;
    if (searchTerm.length >= 3 || searchTerm.length === 0) {
        applyFilters();
    }
});

// Switch between tabs
function switchTab(status) {
    const url = new URL(window.location);
    url.searchParams.set('status', status);
    window.location.href = url.toString();
}

// Apply filters
function applyFilters() {
    const search = document.getElementById('bookingSearch').value;
    const currentStatus = '{{ $status }}' || 'all';
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    params.append('status', currentStatus);
    
    window.location.href = '{{ route("business.bookings.index") }}?' + params.toString();
}

// Clear filters
function clearFilters() {
    document.getElementById('bookingSearch').value = '';
    const currentStatus = '{{ $status }}' || 'all';
    window.location.href = '{{ route("business.bookings.index") }}?status=' + currentStatus;
}
</script>
@endpush
