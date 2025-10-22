@extends('business.layouts.app')

@section('title', 'Booking Management')
@section('page-title', 'Booking Management')

@push('styles')
    @vite(['resources/css/bookings.css'])
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>Booking Management
                        </h5>
                        <small class="text-muted">Manage your vehicle bookings efficiently</small>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group">
                            <a href="{{ route('business.bookings.create') }}" class="btn btn-primary">
                                <i class="fas fa-magic me-2"></i>5-Step Flow
                            </a>
                            <a href="{{ route('business.bookings.quick-create') }}" class="btn btn-outline-primary">
                                <i class="fas fa-bolt me-2"></i>Quick Create
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Status Tabs -->
                <ul class="nav nav-tabs mt-3" id="bookingTabs" role="tablist">
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
            </div>

            <div class="card-body">
                <!-- Search and Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Search by booking ID, customer, or vehicle..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                            <i class="fas fa-times me-1"></i>Clear Filters
                        </button>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Booking ID</th>
                                <th>Customer Name</th>
                                <th>Vehicle Details</th>
                                <th>Start Date & Time</th>
                                @if($status !== 'upcoming' && $status !== 'all')
                                <th>End Date & Time</th>
                                @elseif($status === 'all')
                                <th>End Date & Time</th>
                                @endif
                                <th>Status</th>
                                @if($status === 'ongoing' || $status === 'all')
                                <th>Amount Due</th>
                                @endif
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#{{ $booking->booking_number }}</span>
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
    </div>
</div>
@endsection

@push('scripts')
<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
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
    const search = document.getElementById('searchInput').value;
    const currentStatus = '{{ $status }}' || 'all';
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    params.append('status', currentStatus);
    
    window.location.href = '{{ route("business.bookings.index") }}?' + params.toString();
}

// Clear filters
function clearFilters() {
    document.getElementById('searchInput').value = '';
    const currentStatus = '{{ $status }}' || 'all';
    window.location.href = '{{ route("business.bookings.index") }}?status=' + currentStatus;
}
</script>
@endpush
