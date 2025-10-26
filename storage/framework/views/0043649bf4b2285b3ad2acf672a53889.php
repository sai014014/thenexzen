<?php $__env->startSection('title', 'Dashboard - The NexZen Business Portal'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="dashboard-content dashboard-page">
<!-- Stats Cards Row -->
<div class="stats-row">
    <div class="stats-card">
        <div class="stats-content">
            <div class="stats-info">
                <div class="stats-title">
                    <h3>Total Revenue</h3>
                    <div class="stats-icon earnings">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="40" height="40" rx="8" fill="#6B6ADE"/>
                            <path d="M20 8C20.5523 8 21 8.44772 21 9V11H23C23.5523 11 24 11.4477 24 12C24 12.5523 23.5523 13 23 13H21V15C21 15.5523 20.5523 16 20 16C19.4477 16 19 15.5523 19 15V13H17C16.4477 13 16 12.5523 16 12C16 11.4477 16.4477 11 17 11H19V9C19 8.44772 19.4477 8 20 8Z" fill="white"/>
                            <path d="M12 20C12 18.8954 12.8954 18 14 18H26C27.1046 18 28 18.8954 28 20V28C28 29.1046 27.1046 30 26 30H14C12.8954 30 12 29.1046 12 28V20Z" fill="white"/>
                        </svg>
                    </div>
                </div>
                <div class="stats-value">₹<?php echo e(number_format($totalRevenue ?? 0)); ?></div>
                <div class="stats-change <?php echo e(($revenueChange ?? 0) >= 0 ? 'positive' : 'negative'); ?>">
                    <i class="fas fa-arrow-<?php echo e(($revenueChange ?? 0) >= 0 ? 'up' : 'down'); ?>"></i>
                    <span><?php echo e(abs($revenueChange ?? 0)); ?>% <?php echo e(($revenueChange ?? 0) >= 0 ? 'Up' : 'Down'); ?> from yesterday</span>
                </div>
            </div>
        </div>
    </div>

    <div class="stats-card">
        <div class="stats-content">
            <div class="stats-info">
                <div class="stats-title">
                    <h3>Completed Bookings</h3>
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
                <div class="stats-value"><?php echo e($completedBookings ?? 0); ?></div>
                <div class="stats-change <?php echo e(($completedBookingsChange ?? 0) >= 0 ? 'positive' : 'negative'); ?>">
                    <i class="fas fa-arrow-<?php echo e(($completedBookingsChange ?? 0) >= 0 ? 'up' : 'down'); ?>"></i>
                    <span><?php echo e(abs($completedBookingsChange ?? 0)); ?>% <?php echo e(($completedBookingsChange ?? 0) >= 0 ? 'Up' : 'Down'); ?> from yesterday</span>
                </div>
            </div>
        </div>
    </div>

    <div class="stats-card">
        <div class="stats-content">
            <div class="stats-info">
                <div class="stats-title">
                    <h3>Ongoing <br> Bookings</h3>
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
                <div class="stats-value"><?php echo e($ongoingBookings ?? 0); ?></div>
                <div class="stats-change <?php echo e(($ongoingBookingsChange ?? 0) >= 0 ? 'positive' : 'negative'); ?>">
                    <i class="fas fa-arrow-<?php echo e(($ongoingBookingsChange ?? 0) >= 0 ? 'up' : 'down'); ?>"></i>
                    <span><?php echo e(abs($ongoingBookingsChange ?? 0)); ?>% <?php echo e(($ongoingBookingsChange ?? 0) >= 0 ? 'Up' : 'Down'); ?> from yesterday</span>
                </div>
            </div>
        </div>
    </div>

    <div class="stats-card">
        <div class="stats-content">
            <div class="stats-info">
                <div class="stats-title">
                    <h3>Fleet Utilization</h3>
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
                <div class="stats-value"><?php echo e($fleetUtilization ?? 0); ?>%</div>
                <div class="stats-change <?php echo e(($fleetUtilizationChange ?? 0) >= 0 ? 'positive' : 'negative'); ?>">
                    <i class="fas fa-arrow-<?php echo e(($fleetUtilizationChange ?? 0) >= 0 ? 'up' : 'down'); ?>"></i>
                    <span><?php echo e(abs($fleetUtilizationChange ?? 0)); ?>% <?php echo e(($fleetUtilizationChange ?? 0) >= 0 ? 'Up' : 'Down'); ?> from yesterday</span>
                </div>
            </div>
        </div>
    </div>

    <div class="stats-card">
        <div class="stats-content">
            <div class="stats-info">
                <div class="stats-title">
                    <h3>Outstanding Payments</h3>
                    <div class="stats-icon customers">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="40" height="40" rx="8" fill="#4169E1"/>
                            <circle cx="20" cy="14" r="4" fill="white"/>
                            <path d="M12 28C12 24.6863 14.6863 22 18 22H22C25.3137 22 28 24.6863 28 28V30H12V28Z" fill="white"/>
                        </svg>
                    </div>
                </div>
                <div class="stats-value">₹<?php echo e(number_format($outstandingPayments ?? 0)); ?></div>
                <div class="stats-change <?php echo e(($outstandingPaymentsChange ?? 0) >= 0 ? 'positive' : 'negative'); ?>">
                    <i class="fas fa-arrow-<?php echo e(($outstandingPaymentsChange ?? 0) >= 0 ? 'up' : 'down'); ?>"></i>
                    <span><?php echo e(abs($outstandingPaymentsChange ?? 0)); ?>% <?php echo e(($outstandingPaymentsChange ?? 0) >= 0 ? 'Up' : 'Down'); ?> from yesterday</span>
                </div>
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
            <canvas id="vehicleStatusChart" width="280" height="280"></canvas>
            <div class="donut-center">
                <span class="center-value"><?php echo e($totalVehicles ?? 0); ?></span>
            </div>
        </div>
        <div class="chart-legend">
            <div class="legend-item">
                <span class="legend-color available"></span>
                <span>AVAILABLE (<?php echo e($availableVehicles ?? 0); ?>)</span>
            </div>
            <div class="legend-item">
                <span class="legend-color booked"></span>
                <span>BOOKED (<?php echo e($bookedVehicles ?? 0); ?>)</span>
            </div>
            <div class="legend-item">
                <span class="legend-color maintenance"></span>
                <span>MAINTENANCE (<?php echo e($maintenanceVehicles ?? 0); ?>)</span>
            </div>
        </div>
    </div>
</div>

<!-- Current Bookings Table -->
<div class="bookings-table-card">
    <div class="table-header">
        <h3>Ongoing Bookings</h3>
    </div>
    <div class="table-container">
        <?php if(isset($recentBookings) && $recentBookings->count() > 0): ?>
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer</th>
                        <th>Vehicle Details</th>
                        <th>Start Date & Time</th>
                        <th>End Date & Time</th>
                        <th>Status</th>
                        <th>Amount Due</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $recentBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="booking-id">
                                    #<?php echo e($booking->booking_number ?? $booking->id ?? 'N/A'); ?>

                                </div>
                            </td>
                            <td>
                                <div class="customer-name">ravella 
                                    <?php if($booking->customer): ?>
                                        <?php echo e($booking->customer->full_name ?? 'N/A'); ?>

                                    <?php else: ?>
                                        Customer #<?php echo e($booking->customer_id ?? 'N/A'); ?>

                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="vehicle-info">
                                    <div class="vehicle-logo">
                                        <?php if($booking->vehicle && $booking->vehicle->vehicle_make): ?>
                                            <div class="vehicle-placeholder"><?php echo e(substr($booking->vehicle->vehicle_make, 0, 1)); ?></div>
                                        <?php else: ?>
                                            <div class="vehicle-placeholder">🚗</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="vehicle-details">
                                        <div class="vehicle-make"><?php echo e($booking->vehicle->vehicle_make ?? 'N/A'); ?></div>
                                        <div class="vehicle-model"><?php echo e($booking->vehicle->vehicle_model ?? 'N/A'); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="date-time">
                                    <?php if($booking->pickup_date): ?>
                                        <?php echo e(\Carbon\Carbon::parse($booking->pickup_date)->format('M d, Y')); ?>

                                    <?php elseif($booking->start_date): ?>
                                        <?php echo e(\Carbon\Carbon::parse($booking->start_date)->format('M d, Y')); ?>

                                    <?php elseif($booking->created_at): ?>
                                        <?php echo e($booking->created_at->format('M d, Y')); ?>

                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                    <br>
                                    <small>
                                        <?php if($booking->pickup_time): ?>
                                            <?php echo e(\Carbon\Carbon::parse($booking->pickup_time)->format('g:i A')); ?>

                                        <?php elseif($booking->start_time): ?>
                                            <?php echo e(\Carbon\Carbon::parse($booking->start_time)->format('g:i A')); ?>

                                        <?php elseif($booking->created_at): ?>
                                            <?php echo e($booking->created_at->format('g:i A')); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </td>
                            <td>
                                <div class="date-time">
                                    <?php if($booking->return_date): ?>
                                        <?php echo e(\Carbon\Carbon::parse($booking->return_date)->format('M d, Y')); ?>

                                    <?php elseif($booking->end_date): ?>
                                        <?php echo e(\Carbon\Carbon::parse($booking->end_date)->format('M d, Y')); ?>

                                    <?php elseif($booking->created_at): ?>
                                        <?php echo e($booking->created_at->addDays(7)->format('M d, Y')); ?>

                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                    <br>
                                    <small>
                                        <?php if($booking->return_time): ?>
                                            <?php echo e(\Carbon\Carbon::parse($booking->return_time)->format('g:i A')); ?>

                                        <?php elseif($booking->end_time): ?>
                                            <?php echo e(\Carbon\Carbon::parse($booking->end_time)->format('g:i A')); ?>

                                        <?php elseif($booking->created_at): ?>
                                            <?php echo e($booking->created_at->addDays(7)->format('g:i A')); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge <?php echo e(strtolower(str_replace(' ', '-', $booking->status ?? 'pending'))); ?>">
                                    <span class="status-dot"></span>
                                    <?php echo e(ucfirst($booking->status ?? 'Pending')); ?>

                                </span>
                            </td>
                            <td>
                                <div class="amount-due">
                                    ₹<?php echo e(number_format($booking->total_amount ?? 0, 2)); ?>

                                </div>
                            </td>
                            <td>
                                <a href="<?php echo e(route('business.bookings.show', $booking->id)); ?>" class="view-details-btn">View</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data-message">
                <div class="no-data-icon">📋</div>
                <h4>No Ongoing Bookings</h4>
                <p>There are currently no ongoing bookings. Create a new booking to get started.</p>
                <a href="<?php echo e(route('business.bookings.create')); ?>" class="btn btn-primary">Create New Booking</a>
            </div>
        <?php endif; ?>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Dashboard-specific styles (header styles now in common.css) */

/* Dashboard Stats Text Wrapping */
body.business-dashboard-page .stats-title {
  word-wrap: break-word;
  overflow-wrap: break-word;
  white-space: normal;
  line-height: 1.3;
}

body.business-dashboard-page .stats-value {
  word-wrap: break-word;
  overflow-wrap: break-word;
  white-space: normal;
  line-height: 1.2;
}

body.business-dashboard-page .stats-change {
  word-wrap: break-word;
  overflow-wrap: break-word;
  white-space: normal;
  flex-wrap: wrap;
  line-height: 1.2;
}

body.business-dashboard-page .stats-title h3 {
  font-size: 16px;
  margin: 0;
  font-weight: 600;
  color: #666;
}

body.business-dashboard-page .stats-icon {
  width: 32px;
  height: 32px;
}

body.business-dashboard-page .stats-icon svg {
  width: 32px;
  height: 32px;
}

body.business-dashboard-page .stats-value {
  font-size: 20px;
  font-weight: 700;
  margin: 8px 0 6px 0;
  color: #333;
}

body.business-dashboard-page .stats-change {
  font-size: 11px;
  gap: 3px;
}

body.business-dashboard-page .stats-change i {
  font-size: 10px;
}

body.business-dashboard-page .stats-row {
  gap: 15px;
  margin-bottom: 25px;
}

/* Vehicle Status Chart - 70% of section size */
body.business-dashboard-page .donut-chart-container {
  width: 70%;
  max-width: 280px;
  height: 280px;
  margin: 0 auto 20px auto;
}

body.business-dashboard-page .donut-chart-container canvas {
  width: 100% !important;
  height: 100% !important;
  max-width: 280px !important;
  max-height: 280px !important;
}

body.business-dashboard-page .donut-center {
  font-size: 36px;
  font-weight: 700;
}

body.business-dashboard-page .chart-legend {
  justify-content: center;
  gap: 15px;
  flex-wrap: wrap;
}

body.business-dashboard-page .legend-item {
  font-size: 13px;
  gap: 6px;
}

body.business-dashboard-page .legend-color {
  width: 14px;
  height: 14px;
}

body.business-dashboard-page .stats-content {
  flex-direction: column;
  align-items: flex-start;
  gap: 10px;
}

body.business-dashboard-page .stats-info {
  width: 100%;
  flex: 1;
}

body.business-dashboard-page .stats-title {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  margin-bottom: 8px;
}

body.business-dashboard-page .stats-icon {
  margin-left: auto;
  flex-shrink: 0;
  align-self: center;
  margin-top: 0;
  margin-bottom: 0;
}

body.business-dashboard-page .stats-change {
  margin-top: 12px;
}

/* Header Icons Spacing */
body.business-dashboard-page .head_right {
  display: flex;
  align-items: center;
  gap: 15px;
}

body.business-dashboard-page .profile {
  display: flex;
  align-items: center;
  gap: 15px;
}

body.business-dashboard-page .scan,
body.business-dashboard-page .notify,
body.business-dashboard-page .profile_menu {
  margin: 0 5px;
}

/* Bookings Table Styling */
body.business-dashboard-page .bookings-table thead tr {
  background-color: #f1f4f9 !important;
}

body.business-dashboard-page .bookings-table thead th {
  background-color: #f1f4f9 !important;
  color: #333;
  font-weight: 600;
  padding: 15px 12px;
  border-bottom: 1px solid #e1e5e9;
  text-align: left;
  vertical-align: top;
}

body.business-dashboard-page .bookings-table tbody td {
  padding: 15px 12px;
  vertical-align: top;
  text-align: left;
}

body.business-dashboard-page .booking-id {
  font-weight: 600;
  color: #6B6ADE;
  font-family: 'Courier New', monospace;
}

body.business-dashboard-page .customer-name {
  font-weight: 500;
  color: #333;
}

body.business-dashboard-page .vehicle-info {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin: 0;
  padding: 0;
}

body.business-dashboard-page .vehicle-logo {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

body.business-dashboard-page .vehicle-placeholder {
  font-size: 14px;
  font-weight: 600;
  color: #6B6ADE;
}

body.business-dashboard-page .vehicle-details {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

body.business-dashboard-page .vehicle-make {
  font-weight: 600;
  color: #333;
  font-size: 13px;
}

body.business-dashboard-page .vehicle-model {
  color: #666;
  font-size: 12px;
}

body.business-dashboard-page .date-time {
  font-size: 13px;
  color: #333;
  line-height: 1.4;
  margin: 0;
  padding: 0;
}

body.business-dashboard-page .date-time small {
  color: #666;
  font-size: 11px;
}

body.business-dashboard-page .amount-due {
  font-weight: 600;
  color: #059669;
  font-size: 14px;
  margin: 0;
  padding: 0;
}

body.business-dashboard-page .view-details-btn {
  background: #6B6ADE;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
  text-decoration: none;
  display: inline-block;
}

body.business-dashboard-page .view-details-btn:hover {
  background: #5a5acf;
  color: white;
  text-decoration: none;
}

/* Status Column Alignment */
body.business-dashboard-page .status-badge {
  margin: 0;
  padding: 0;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  text-align: left;
}

body.business-dashboard-page .status-dot {
  margin: 0;
  padding: 0;
  flex-shrink: 0;
}

/* Ensure status column content aligns with header */
body.business-dashboard-page .bookings-table tbody td:nth-child(6) {
  text-align: left;
  padding-left: 12px;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Net Earnings Chart
    const earningsCtx = document.getElementById('netEarningsChart').getContext('2d');
    
    // Debug: Log chart data
    const chartLabels = <?php echo json_encode($chartLabels ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']); ?>;
    const chartData = <?php echo json_encode($chartData ?? [0, 0, 0, 0, 0, 0, 0]); ?>;
    console.log('Chart Labels:', chartLabels);
    console.log('Chart Data:', chartData);
    
    new Chart(earningsCtx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Net Earnings',
                data: chartData,
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
                        color: 'white',
                        maxTicksLimit: function(context) {
                            // For hourly data (24 hours), show every 2 hours
                            const labels = context.chart.data.labels;
                            if (labels && labels.length === 24) {
                                return 12; // Show every 2 hours (00:00, 02:00, 04:00, etc.)
                            }
                            return 7; // Default for daily data
                        },
                        callback: function(value, index, ticks) {
                            const labels = this.chart.data.labels;
                            if (labels && labels.length === 24) {
                                // For hourly data, show every 2 hours
                                if (index % 2 === 0) {
                                    return labels[index];
                                }
                                return '';
                            }
                            // For daily data, show all labels
                            return labels[index];
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    min: 0,
                    max: function(context) {
                        const data = context.chart.data.datasets[0].data;
                        const maxValue = Math.max(...data);
                        
                        // If no data or all values are 0, show scale up to 5K
                        if (maxValue === 0 || data.every(val => val === 0)) {
                            return 5000;
                        }
                        
                        // If data exists, set max to 20% above the highest value
                        return Math.ceil(maxValue * 1.2);
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: 'white',
                        stepSize: function(context) {
                            const data = context.chart.data.datasets[0].data;
                            const maxValue = Math.max(...data);
                            
                            // If no data or all values are 0, show steps of 1K
                            if (maxValue === 0 || data.every(val => val === 0)) {
                                return 1000;
                            }
                            
                            // If data exists, calculate appropriate step size
                            return Math.ceil(maxValue / 5);
                        },
                        callback: function(value) {
                            if (value >= 1000) {
                                return '₹' + (value / 1000) + 'K';
                            } else {
                                return '₹' + value;
                            }
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
                    <?php echo e($availableVehicles ?? 0); ?>, 
                    <?php echo e($bookedVehicles ?? 0); ?>, 
                    <?php echo e($maintenanceVehicles ?? 0); ?>

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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('business.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp 8.2\htdocs\nexzen\resources\views/business/dashboard.blade.php ENDPATH**/ ?>