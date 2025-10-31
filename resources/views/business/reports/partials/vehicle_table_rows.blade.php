@forelse($vehicleData as $vehicle)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td><strong>{{ $vehicle['name'] }}</strong></td>
    <td><span class="badge bg-secondary">{{ $vehicle['vehicle_number'] }}</span></td>
    <td>
        <span class="badge {{ $vehicle['ownership'] === 'Owned' ? 'bg-success' : 'bg-warning' }}">{{ $vehicle['ownership'] }}</span>
        @if($vehicle['ownership'] === 'Vendor Provided')
            <br><small class="text-muted">{{ $vehicle['vendor_name'] }}</small>
        @endif
    </td>
    <td>{{ $vehicle['registered_on'] }}</td>
    <td>{{ $vehicle['insurance_expiry'] }}</td>
    <td><span class="badge bg-primary">{{ $vehicle['total_rentals'] }}</span></td>
    <td>{{ $vehicle['total_kilometers'] }}</td>
    <td><strong class="text-success">₹{{ number_format($vehicle['net_revenue'], 2) }}</strong></td>
    <td>{{ $vehicle['last_rental_date'] }}</td>
</tr>
@empty
<tr>
    <td colspan="10" class="text-center py-4">
        <div class="text-muted">
            <i class="fas fa-car fa-3x mb-3"></i>
            <h5>No vehicles found</h5>
            <p>No vehicles match the selected filters.</p>
        </div>
    </td>
    </tr>
@endforelse


