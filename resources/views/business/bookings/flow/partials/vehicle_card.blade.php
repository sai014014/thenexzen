@props(['vehicle'])
<div class="card vehicle-card" data-vehicle-id="{{ $vehicle['id'] }}">
    <div class="card-body d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ $vehicle['image'] ?? asset('images/vehicle-brands/default.svg') }}" alt="{{ $vehicle['name'] }}" style="width:72px;height:48px;object-fit:contain;">
            <div>
                <div class="fw-semibold vehicle-name">{{ $vehicle['name'] }}</div>
                <div class="text-muted small">{{ $vehicle['transmission'] }} • {{ $vehicle['seats'] }} Seats • {{ $vehicle['fuel'] }}</div>
            </div>
        </div>
        <div class="text-end">
            <div class="fs-5 fw-bold vehicle-price">₹{{ number_format($vehicle['price_per_day'] ?? 0) }}<span class="text-muted fs-6">/day</span></div>
            <button class="btn btn-outline-primary btn-pill select-vehicle book-now-btn">Book Now</button>
        </div>
    </div>
</div>

