@forelse($customerData as $customer)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>
        <strong>{{ $customer['name'] }}</strong>
    </td>
    <td>
        <span class="badge {{ $customer['type'] === 'individual' ? 'bg-info' : 'bg-warning' }}">
            {{ ucfirst($customer['type']) }}
        </span>
    </td>
    <td>{{ $customer['location'] }}</td>
    <td>{{ $customer['contact_number'] }}</td>
    <td>{{ $customer['registered_on'] }}</td>
    <td>{{ $customer['license_expiry'] }}</td>
    <td>
        <span class="badge bg-primary">{{ $customer['total_bookings'] }}</span>
    </td>
    <td>
        <strong class="text-success">₹{{ number_format($customer['net_bill'], 2) }}</strong>
    </td>
    <td>{{ $customer['last_booking_date'] }}</td>
</tr>
@empty
<tr>
    <td colspan="10" class="text-center py-4">
        <div class="text-muted">
            <i class="fas fa-users fa-3x mb-3"></i>
            <h5>No customers found</h5>
            <p>No customers match the selected filters.</p>
        </div>
    </td>
</tr>
@endforelse


