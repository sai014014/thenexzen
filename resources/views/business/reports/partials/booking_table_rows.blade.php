@forelse($bookingData as $date => $booking)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td><strong>{{ $booking['date'] }}</strong></td>
    <td><span class="badge bg-success">{{ $booking['completed_bookings'] }}</span></td>
    <td><span class="badge bg-primary">{{ $booking['vehicles_booked'] }}</span></td>
    <td><span class="badge bg-info">{{ $booking['unique_customers'] }}</span></td>
    <td><span class="badge bg-warning">{{ $booking['returning_customers'] }}</span></td>
    <td><span class="badge bg-danger">{{ $booking['cancelled_bookings'] }}</span></td>
    <td>
        <span class="badge {{ $booking['cancellation_rate'] > 10 ? 'bg-danger' : ($booking['cancellation_rate'] > 5 ? 'bg-warning' : 'bg-success') }}">
            {{ $booking['cancellation_rate'] }}%
        </span>
    </td>
    <td><strong class="text-success">₹{{ number_format($booking['total_revenue'], 2) }}</strong></td>
    <td><strong class="text-primary">₹{{ number_format($booking['average_booking_value'], 2) }}</strong></td>
</tr>
@empty
<tr>
    <td colspan="10" class="text-center py-4">
        <div class="text-muted">
            <i class="fas fa-calendar-alt fa-3x mb-3"></i>
            <h5>No booking data found</h5>
            <p>No bookings match the selected date range.</p>
        </div>
    </td>
</tr>
@endforelse


