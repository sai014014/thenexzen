@extends('business.layouts.app')

@section('title', 'Dashboard - The NexZen Business Portal')
@section('page-title', 'Dashboard')

@section('content')
<div class="dashboard-content">
<!-- Stats Cards Row -->
<div class="stats-row">
    <div class="stats-card">
        <div class="stats-content">
            <div class="stats-info">
                <h3 class="stats-title">Net Earnings</h3>
                <div class="stats-value">₹{{ number_format($totalEarnings ?? 0) }}</div>
                <div class="stats-change {{ ($earningsChange ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas fa-arrow-{{ ($earningsChange ?? 0) >= 0 ? 'up' : 'down' }}"></i>
                    <span>{{ abs($earningsChange ?? 0) }}% {{ ($earningsChange ?? 0) >= 0 ? 'Up' : 'Down' }} from yesterday</span>
                </div>
            </div>
            <div class="stats-icon earnings">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="40" rx="8" fill="#6B6ADE"/>
                    <path d="M20 8C20.5523 8 21 8.44772 21 9V11H23C23.5523 11 24 11.4477 24 12C24 12.5523 23.5523 13 23 13H21V15C21 15.5523 20.5523 16 20 16C19.4477 16 19 15.5523 19 15V13H17C16.4477 13 16 12.5523 16 12C16 11.4477 16.4477 11 17 11H19V9C19 8.44772 19.4477 8 20 8Z" fill="white"/>
                    <path d="M12 20C12 18.8954 12.8954 18 14 18H26C27.1046 18 28 18.8954 28 20V28C28 29.1046 27.1046 30 26 30H14C12.8954 30 12 29.1046 12 28V20Z" fill="white"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="stats-card">
        <div class="stats-content">
            <div class="stats-info">
                <h3 class="stats-title">Bookings</h3>
                <div class="stats-value">{{ $totalBookings ?? 0 }}</div>
                <div class="stats-change {{ ($bookingsChange ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas fa-arrow-{{ ($bookingsChange ?? 0) >= 0 ? 'up' : 'down' }}"></i>
                    <span>{{ abs($bookingsChange ?? 0) }}% {{ ($bookingsChange ?? 0) >= 0 ? 'Up' : 'Down' }} from yesterday</span>
                </div>
            </div>
            <div class="stats-icon bookings">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="40" rx="8" fill="#FFD700"/>
                    <path d="M12 10C10.8954 10 10 10.8954 10 12V28C10 29.1046 10.8954 30 12 30H28C29.1046 30 30 29.1046 30 28V12C30 10.8954 29.1046 10 28 10H12ZM12 12H28V28H12V12Z" fill="white"/>
                    <path d="M14 14C13.4477 14 13 14.4477 13 15C13 15.5523 13.4477 16 14 16H26C26.5523 16 27 15.5523 27 15C27 14.4477 26.5523 14 26 14H14Z" fill="white"/>
                    <path d="M14 18C13.4477 18 13 18.4477 13 19C13 19.5523 13.4477 20 14 20H26C26.5523 20 27 19.5523 27 19C27 18.4477 26.5523 18 26 18H14Z" fill="white"/>
                    <path d="M14 22C13.4477 22 13 22.4477 13 23C13 23.5523 13.4477 24 14 24H26C26.5523 24 27 23.5523 27 23C27 22.4477 26.5523 22 26 22H14Z" fill="white"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="stats-card">
        <div class="stats-content">
            <div class="stats-info">
                <h3 class="stats-title">Vendors</h3>
                <div class="stats-value">{{ $totalVendors ?? 0 }}</div>
                <div class="stats-change {{ ($vendorsChange ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas fa-arrow-{{ ($vendorsChange ?? 0) >= 0 ? 'up' : 'down' }}"></i>
                    <span>{{ abs($vendorsChange ?? 0) }}% {{ ($vendorsChange ?? 0) >= 0 ? 'Up' : 'Down' }} from yesterday</span>
                </div>
            </div>
            <div class="stats-icon vendors">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="40" rx="8" fill="#32CD32"/>
                    <path d="M12 8C10.8954 8 10 8.89543 10 10V30C10 31.1046 10.8954 32 12 32H28C29.1046 32 30 31.1046 30 30V10C30 8.89543 29.1046 8 28 8H12ZM12 10H28V30H12V10Z" fill="white"/>
                    <path d="M14 12C13.4477 12 13 12.4477 13 13C13 13.5523 13.4477 14 14 14H26C26.5523 14 27 13.5523 27 13C27 12.4477 26.5523 12 26 12H14Z" fill="white"/>
                    <path d="M14 16C13.4477 16 13 16.4477 13 17C13 17.5523 13.4477 18 14 18H26C26.5523 18 27 17.5523 27 17C27 16.4477 26.5523 16 26 16H14Z" fill="white"/>
                    <path d="M14 20C13.4477 20 13 20.4477 13 21C13 21.5523 13.4477 22 14 22H26C26.5523 22 27 21.5523 27 21C27 20.4477 26.5523 20 26 20H14Z" fill="white"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="stats-card">
        <div class="stats-content">
            <div class="stats-info">
                <h3 class="stats-title">Vehicles</h3>
                <div class="stats-value">{{ $totalVehicles ?? 0 }}</div>
                <div class="stats-change {{ ($vehiclesChange ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas fa-arrow-{{ ($vehiclesChange ?? 0) >= 0 ? 'up' : 'down' }}"></i>
                    <span>{{ abs($vehiclesChange ?? 0) }}% {{ ($vehiclesChange ?? 0) >= 0 ? 'Up' : 'Down' }} from yesterday</span>
                </div>
            </div>
            <div class="stats-icon vehicles">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="40" rx="8" fill="#FF8C00"/>
                    <path d="M8 20C8 18.8954 8.89543 18 10 18H30C31.1046 18 32 18.8954 32 20V24C32 25.1046 31.1046 26 30 26H10C8.89543 26 8 25.1046 8 24V20Z" fill="white"/>
                    <circle cx="12" cy="22" r="2" fill="white"/>
                    <circle cx="28" cy="22" r="2" fill="white"/>
                    <path d="M14 16C14 14.8954 14.8954 14 16 14H24C25.1046 14 26 14.8954 26 16V18H14V16Z" fill="white"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="stats-card">
        <div class="stats-content">
            <div class="stats-info">
                <h3 class="stats-title">Customers</h3>
                <div class="stats-value">{{ $totalCustomers ?? 0 }}</div>
                <div class="stats-change {{ ($customersChange ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas fa-arrow-{{ ($customersChange ?? 0) >= 0 ? 'up' : 'down' }}"></i>
                    <span>{{ abs($customersChange ?? 0) }}% {{ ($customersChange ?? 0) >= 0 ? 'Up' : 'Down' }} from yesterday</span>
                </div>
            </div>
            <div class="stats-icon customers">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="40" rx="8" fill="#4169E1"/>
                    <circle cx="20" cy="14" r="4" fill="white"/>
                    <path d="M12 28C12 24.6863 14.6863 22 18 22H22C25.3137 22 28 24.6863 28 28V30H12V28Z" fill="white"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="charts-row">
    <!-- Net Earnings Chart -->
    <div class="chart-card earnings-chart earnings-large">
        <div class="chart-header">
            <h3>Net Earnings</h3>
        </div>
        <div class="chart-container earnings-container">
            <canvas id="netEarningsChart" width="800" height="320"></canvas>
        </div>
    </div>

    <!-- Vehicle Status Chart -->
    <div class="chart-card vehicle-chart vehicle-small">
        <div class="chart-header">
            <h3>Vehicle Status</h3>
        </div>
        <div class="donut-chart-container donut-small">
            <canvas id="vehicleStatusChart" width="140" height="140"></canvas>
            <div class="donut-center">
                <span class="center-value">{{ $totalVehicles ?? 0 }}</span>
            </div>
        </div>
        <div class="chart-legend">
            <div class="legend-item">
                <span class="legend-color available"></span>
                <span>AVAILABLE ({{ $availableVehicles ?? 0 }})</span>
            </div>
            <div class="legend-item">
                <span class="legend-color booked"></span>
                <span>BOOKED ({{ $bookedVehicles ?? 0 }})</span>
            </div>
            <div class="legend-item">
                <span class="legend-color maintenance"></span>
                <span>MAINTENANCE ({{ $maintenanceVehicles ?? 0 }})</span>
            </div>
        </div>
    </div>
</div>

<!-- Current Bookings Table -->
<div class="bookings-table-card">
    <div class="table-header">
        <h3>Current Bookings</h3>
    </div>
    <div class="table-container">
        @if(isset($recentBookings) && $recentBookings->count() > 0)
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Driver</th>
                        <th>Date of Booking</th>
                        <th>Earnings</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentBookings as $booking)
                        <tr>
                            <td>
                                <div class="vehicle-info">
                                    <div class="vehicle-logo">
                                        @if($booking->vehicle && $booking->vehicle->vehicle_make)
                                            <div class="vehicle-placeholder">{{ substr($booking->vehicle->vehicle_make, 0, 1) }}</div>
                                        @else
                                            <div class="vehicle-placeholder">🚗</div>
                                        @endif
                                    </div>
                                    <div class="vehicle-details">
                                        <div class="vehicle-name">{{ $booking->vehicle->vehicle_make ?? 'N/A' }} {{ $booking->vehicle->vehicle_model ?? '' }}</div>
                                        <div class="vehicle-number">{{ $booking->vehicle->vehicle_number ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $booking->customer->name ?? 'N/A' }}</td>
                            <td>{{ $booking->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                @if($booking->status === 'completed')
                                    ₹{{ number_format($booking->total_amount ?? 0, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="status-badge {{ strtolower(str_replace(' ', '-', $booking->status ?? 'pending')) }}">
                                    <span class="status-dot"></span>
                                    {{ ucfirst($booking->status ?? 'Pending') }}
                                </span>
                            </td>
                            <td>
                                <button class="view-details-btn">VIEW DETAILS</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data-message">
                <div class="no-data-icon">📋</div>
                <h4>No Bookings Found</h4>
                <p>Start by creating your first booking to see data here.</p>
                <a href="{{ route('business.bookings.create') }}" class="btn btn-primary">Create First Booking</a>
            </div>
        @endif
    </div>
</div>
</div>
@endsection

@push('styles')
<style>
/* Dashboard Content */
.dashboard-content {
  padding: 20px;
}

/* Stats Cards */
.stats-row {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.stats-card {
    flex: 1;
    min-width: 200px;
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
}

.stats-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stats-info {
    flex: 1;
}

.stats-title {
    font-size: 14px;
    color: #666;
    margin: 0 0 8px 0;
    font-weight: 500;
}

.stats-value {
    font-size: 24px;
    font-weight: 700;
    color: #333;
    margin: 0 0 8px 0;
}

.stats-change {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    font-weight: 500;
}

.stats-change.positive {
    color: #10B981;
}

.stats-change.negative {
    color: #EF4444;
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Charts Row */
.charts-row {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
}

.chart-card {
    flex: 1;
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.chart-header h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.chart-container {
    position: relative;
    height: 200px;
}
.earnings-container{ height: 340px; }
.earnings-large{ flex: 2; }

/* Net Earnings Chart */
.earnings-chart {
    background: linear-gradient(135deg, #6B6ADE 0%, #3C3CE1 100%);
    color: white;
}

.earnings-chart .chart-header h3 {
    color: white;
}

/* Vehicle Status Chart */
.vehicle-chart {
    text-align: center;
}

.donut-chart-container {
    position: relative;
    display: inline-block;
    margin-bottom: 20px;
}
.donut-small .donut-center{ font-size: 18px; }
.vehicle-small{ max-width: 420px; }

.donut-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 24px;
    font-weight: 700;
    color: #333;
}

.chart-legend {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #666;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.legend-color.available {
    background-color: #10B981;
}

.legend-color.booked {
    background-color: #F59E0B;
}

.legend-color.maintenance {
    background-color: #EF4444;
}

/* Bookings Table */
.bookings-table-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.table-header h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.table-container {
    overflow-x: auto;
}

.bookings-table {
    width: 100%;
    border-collapse: collapse;
}

.bookings-table th {
    text-align: left;
    padding: 12px 0;
    font-size: 14px;
    font-weight: 600;
    color: #666;
    border-bottom: 1px solid #E5E7EB;
}

.bookings-table td {
    padding: 16px 0;
    font-size: 14px;
    color: #333;
    border-bottom: 1px solid #F3F4F6;
}

.vehicle-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.vehicle-logo img {
    border-radius: 4px;
}

.vehicle-placeholder {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #6B6ADE;
    color: white;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.vehicle-details {
    display: flex;
    flex-direction: column;
}

.vehicle-name {
    font-weight: 600;
    color: #333;
}

.vehicle-number {
    font-size: 12px;
    color: #666;
}

.status-badge {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 500;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-badge.in-route .status-dot,
.status-badge.pending .status-dot {
    background-color: #3B82F6;
}

.status-badge.completed .status-dot {
    background-color: #10B981;
}

.status-badge.cancelled .status-dot {
    background-color: #EF4444;
}

.view-details-btn {
    background: #6B6ADE;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.view-details-btn:hover {
    background-color: #5A5ACF;
}

/* No Data Message */
.no-data-message {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.no-data-icon {
    font-size: 48px;
    margin-bottom: 20px;
}

.no-data-message h4 {
    margin: 0 0 10px 0;
    color: #333;
}

.no-data-message p {
    margin: 0 0 20px 0;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-row {
        flex-direction: column;
    }
    
    .charts-row {
        flex-direction: column;
    }
    
    .chart-legend {
        flex-direction: column;
        align-items: center;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Net Earnings Chart
    const earningsCtx = document.getElementById('netEarningsChart').getContext('2d');
    new Chart(earningsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!},
            datasets: [{
                label: 'Net Earnings',
                data: {!! json_encode($chartData ?? [0, 0, 0, 0, 0, 0, 0]) !!},
                borderColor: 'rgba(255, 255, 255, 0.8)',
                backgroundColor: 'rgba(255, 255, 255, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'white',
                pointBorderColor: 'white',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'white'
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'white',
                        callback: function(value) {
                            return '₹' + (value / 1000) + 'K';
                        }
                    }
                }
            }
        }
    });

    // Vehicle Status Chart
    const vehicleCtx = document.getElementById('vehicleStatusChart').getContext('2d');
    new Chart(vehicleCtx, {
        type: 'doughnut',
        data: {
            labels: ['Available', 'Booked', 'Maintenance'],
            datasets: [{
                data: [
                    {{ $availableVehicles ?? 0 }}, 
                    {{ $bookedVehicles ?? 0 }}, 
                    {{ $maintenanceVehicles ?? 0 }}
                ],
                backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush