@extends('business.layouts.app')

@section('title', 'Vehicle Management')
@section('page-title', 'Vehicle Management')

@section('content')
<!-- Main Content -->
<link rel="stylesheet" href="{{ asset('dist/css/VehicleManagement/vehicleManagement_view.css') }}">

<!-- Vehicle Statistics Cards -->
<div class="vehicle-stats-container">
    <div class="vehicle-stat-card">
        <div class="stat-icon">
            <i class="fas fa-car"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $vehicles->total() }}</div>
            <div class="stat-label">All Vehicles</div>
        </div>
    </div>
    <div class="vehicle-stat-card">
        <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $vehicles->where('vehicle_status', 'active')->count() }}</div>
            <div class="stat-label">Available Vehicles</div>
        </div>
    </div>
    <div class="vehicle-stat-card">
        <div class="stat-icon">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $vehicles->where('vehicle_status', 'booked')->count() }}</div>
            <div class="stat-label">Booked Vehicles</div>
        </div>
    </div>
</div>
<style>
    /* Vehicle Statistics Cards */
    .vehicle-stats-container {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        padding: 0 1rem;
    }
    
    .vehicle-stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .vehicle-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .vehicle-stat-card:nth-child(1) .stat-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .vehicle-stat-card:nth-child(2) .stat-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .vehicle-stat-card:nth-child(3) .stat-icon {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .stat-content {
        flex: 1;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        line-height: 1;
        margin-bottom: 0.25rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #718096;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    @media (max-width: 768px) {
        .vehicle-stats-container {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .vehicle-stat-card {
            padding: 1rem;
        }
        
        .stat-number {
            font-size: 1.5rem;
        }
    }

    .price-tooltip {
        position: absolute;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        z-index: 100;
        width: 200px;
        display: none;
        font-size: 13px;
    }

    .price-tooltip h4 {
        margin: 0 0 8px;
        padding-bottom: 5px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
    }

    .price-tooltip p {
        margin: 5px 0;
        display: flex;
        justify-content: space-between;
    }

    .fas.fa-info-circle {
        cursor: pointer;
        position: relative;
    }

    /* Add different colors for each row to differentiate */
    tr:nth-child(4n+1) .price-tooltip {
        border-left: 4px solid #4285f4;
    }

    tr:nth-child(4n+2) .price-tooltip {
        border-left: 4px solid #ea4335;
    }

    tr:nth-child(4n+3) .price-tooltip {
        border-left: 4px solid #fbbc05;
    }

    tr:nth-child(4n+4) .price-tooltip {
        border-left: 4px solid #34a853;
    }
</style>
<div class="main-content">
    <div class="record-count">{{ $vehicles->total() }} Records Found, Page {{ $vehicles->currentPage() }} of {{ $vehicles->lastPage() }}</div>
    <div class="filter-section">
        <div class="table-responsive">
            <table id="vehicleTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="vechicle_title">Vehicle</th>
                        <th>Unit Type</th>
                        <th>Fuel</th>
                        <th>Capacity</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="vehicleTableBody">
                    @foreach($vehicles as $vehicle)
                    <tr>
                        <td class="vechicle_title">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('images/vehicle-brands/suzuki.svg') }}" alt="{{ $vehicle->vehicle_make }}" class="me-2" style="width: 30px; height: 30px;">
                                <div>
                                    <div class="fw-bold">{{ $vehicle->vehicle_make }} {{ $vehicle->vehicle_model }}</div>
                                    <small class="text-muted">{{ $vehicle->registration_number }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $unitType = '';
                                if($vehicle->vehicle_type === 'car') {
                                    $unitType = 'Car - ' . ucfirst($vehicle->transmission_type);
                                } elseif($vehicle->vehicle_type === 'bike') {
                                    $unitType = 'Bike - ' . ucfirst($vehicle->transmission_type);
                                } elseif($vehicle->vehicle_type === 'truck') {
                                    $unitType = 'Truck - ' . ucfirst($vehicle->transmission_type);
                                }
                            @endphp
                            {{ $unitType }}
                        </td>
                        <td>{{ ucfirst($vehicle->fuel_type) }}</td>
                        <td>
                            @if($vehicle->vehicle_type === 'car' || $vehicle->vehicle_type === 'bike')
                                {{ $vehicle->seating_capacity }} Seats
                            @elseif($vehicle->vehicle_type === 'truck')
                                {{ $vehicle->payload_capacity_tons }} Tons
                            @endif
                        </td>
                        <td>
                            ₹ {{ number_format($vehicle->rental_price_24h, 2) }}
                            <i class="fas fa-info-circle text-muted ms-1" style="cursor: pointer;"></i>
                        </td>
                        <td>
                            @php
                                $displayStatus = 'Available';
                                $badgeClass = 'success';

                                if ($vehicle->vehicle_status === 'under_maintenance') {
                                    $displayStatus = 'Maintenance';
                                    $badgeClass = 'danger';
                                } elseif ($vehicle->bookings()->whereIn('status', ['ongoing', 'upcoming'])->where('end_date_time', '>=', \Carbon\Carbon::now())->exists()) {
                                    $displayStatus = 'Booked';
                                    $badgeClass = 'warning';
                                }
                            @endphp
                            <span class="badge rounded-pill bg-{{ $badgeClass }} text-white fw-bold px-2 py-1">
                                {{ $displayStatus }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('business.vehicles.show', $vehicle) }}" class="text-primary me-2 text-decoration-none">View</a>
                            <a href="#" onclick="confirmDelete({{ $vehicle->id }}, '{{ $vehicle->vehicle_make }} {{ $vehicle->vehicle_model }}')" class="text-danger text-decoration-none">Delete</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Pagination controls -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="pagination-info">
                <span class="text-muted">Rows per page: 10</span>
                <span class="text-muted ms-3">{{ $vehicles->firstItem() }}-{{ $vehicles->lastItem() }} of {{ $vehicles->total() }}</span>
            </div>
            <nav aria-label="Vehicle pagination">
                <ul class="pagination mb-0">
                    @if ($vehicles->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $vehicles->previousPageUrl() }}">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    @endif

                    @if ($vehicles->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $vehicles->nextPageUrl() }}">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>

<script>
    const RECORDS_PER_PAGE = 10;
    const RECORDS_PER_PAGE_OPTIONS = [10, 25, 50, 100];
    const VEHICLE_STATUS_ARRAY = ['Active', 'Inactive', 'Under Maintenance'];

    function confirmDelete(vehicleId, vehicleName) {
        if (confirm(`Are you sure you want to delete "${vehicleName}"? This action cannot be undone.`)) {
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('business/vehicles') }}/${vehicleId}`;
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add method override
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endsection