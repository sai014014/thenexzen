@props(['vehicle'])
<div class="card vehicle-card" data-vehicle-id="{{ $vehicle['id'] }}">
    <div class="card-body d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3 flex-grow-1">
            <img src="{{ $vehicle['image'] ?? asset('images/vehicle-brands/default.svg') }}" alt="{{ $vehicle['name'] }}" style="width:72px;height:48px;object-fit:contain;">
            <div class="flex-grow-1">
                <div class="fw-semibold vehicle-name">{{ $vehicle['name'] }}</div>
                <div class="text-muted small mb-1">{{ $vehicle['transmission'] }} • {{ $vehicle['seats'] }} Seats • {{ $vehicle['fuel'] }}</div>
                @if(!empty($vehicle['return_info']))
                <div class="small text-danger d-flex align-items-center gap-1">
                    <i class="fas fa-info-circle"></i>
                    <span>Returns on {{ $vehicle['return_info']['datetime'] }}</span>
                </div>
                @endif
            </div>
        </div>
        <div class="text-end">
            <div class="fs-5 fw-bold vehicle-price">₹{{ number_format($vehicle['price_per_day'] ?? 0) }}<span class="text-muted fs-6">/day</span></div>
            <button class="btn btn-outline-primary btn-pill select-vehicle book-now-btn">Book Now</button>
        </div>
    </div>
</div>

