@forelse($vendorData as $vendor)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td><strong>{{ $vendor['name'] }}</strong></td>
    <td>{{ $vendor['contact_number'] }}</td>
    <td><span class="badge bg-success">{{ $vendor['active_status'] }}</span></td>
    <td>{{ $vendor['registered_on'] }}</td>
    <td><span class="badge bg-primary">{{ $vendor['total_vehicles'] }}</span></td>
    <td><span class="badge bg-info">{{ $vendor['total_bookings'] }}</span></td>
    <td>
        <span class="badge {{ $vendor['commission_type'] === 'Fixed Amount' ? 'bg-warning' : 'bg-info' }}">
            {{ $vendor['commission_type'] }}
        </span>
    </td>
    <td><strong class="text-success">₹{{ number_format($vendor['net_revenue'], 2) }}</strong></td>
    <td><strong class="text-primary">₹{{ number_format($vendor['total_commission'], 2) }}</strong></td>
</tr>
@empty
<tr>
    <td colspan="10" class="text-center py-4">
        <div class="text-muted">
            <i class="fas fa-truck fa-3x mb-3"></i>
            <h5>No vendors found</h5>
            <p>No vendors match the selected filters.</p>
        </div>
    </td>
</tr>
@endforelse


